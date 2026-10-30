<?php
include 'includes/session.php';
if (empty($_SESSION['admin'])) { exit; }
require_once __DIR__ . '/includes/default_exit.php';
$month = isset($_POST['month']) && is_string($_POST['month']) && preg_match('/^\d{4}-(0[1-9]|1[0-2])$/D', $_POST['month']) ? $_POST['month'] : '';
$transaction = false;
try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['token']) || !is_string($_POST['token']) || !hash_equals($_SESSION['complete_exit_token'] ?? '', $_POST['token']) || empty($_SESSION['complete_exit_token'])) {
        throw new InvalidArgumentException('Solicitud no válida. Recargue la página e intente nuevamente.');
    }
    if (!isset($_POST['id']) || !is_string($_POST['id']) || !ctype_digit($_POST['id'])) {
        throw new InvalidArgumentException('Seleccione una marcación válida.');
    }
    $id = (int) $_POST['id'];
    $conn->begin_transaction();
    $transaction = true;
    $stmt = $conn->prepare('SELECT a.date, a.time_in, a.time_out, s.time_out AS scheduled_exit FROM attendance a JOIN employees e ON e.id = a.employee_id LEFT JOIN schedules s ON s.id = e.schedule_id WHERE a.id = ? FOR UPDATE');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $record = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if (!$record) { throw new InvalidArgumentException('No se encontró la marcación.'); }
    $hours = assignedExitHours($record, new DateTimeImmutable('now', new DateTimeZone('America/La_Paz')));
    $stmt = $conn->prepare("UPDATE attendance SET time_out = ?, num_hr = ?, status2 = 3 WHERE id = ? AND (time_out IS NULL OR time_out = '00:00:00')");
    $stmt->bind_param('sdi', $record['scheduled_exit'], $hours, $id);
    $stmt->execute();
    $stmt->close();
    $conn->commit();
    $transaction = false;
    $_SESSION['success'] = 'Salida completada a las ' . substr($record['scheduled_exit'], 0, 5) . ' según el horario asignado. Horas acumuladas actualizadas.';
} catch (InvalidArgumentException $error) {
    if ($transaction) { $conn->rollback(); }
    $_SESSION['error'] = $error->getMessage();
} catch (mysqli_sql_exception $error) {
    if ($transaction) { $conn->rollback(); }
    $_SESSION['error'] = 'No se pudo completar la salida. Intente nuevamente.';
}
header('Location: attendance.php' . ($month ? '?month=' . urlencode($month) : ''));
exit;
