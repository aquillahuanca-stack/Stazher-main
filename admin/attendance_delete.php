<?php
require_once 'includes/session.php';
require_once 'includes/notifications.php';
requireCsrf();
$id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT, array('options' => array('min_range' => 1)));
try {
    if (!$id) { throw new InvalidArgumentException('Seleccione una marcación válida.'); }
    $lookup = $conn->prepare('SELECT e.id AS employee_id, e.firstname, e.lastname, a.date FROM attendance a JOIN employees e ON e.id = a.employee_id WHERE a.id = ? LIMIT 1');
    $lookup->bind_param('i', $id);
    $lookup->execute();
    $attendanceRecord = $lookup->get_result()->fetch_assoc();
    $lookup->close();
    $stmt = $conn->prepare('DELETE FROM attendance WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    if ($stmt->affected_rows && $attendanceRecord) {
        createAdminNotification($conn, 'change', 'Marcación eliminada', trim($attendanceRecord['firstname'] . ' ' . $attendanceRecord['lastname']) . ': se eliminó la asistencia del ' . date('d/m/Y', strtotime($attendanceRecord['date'])) . '.', (int) $attendanceRecord['employee_id']);
    }
    $_SESSION[$stmt->affected_rows ? 'success' : 'error'] = $stmt->affected_rows ? 'Marcación eliminada correctamente.' : 'La marcación ya no está disponible.';
    $stmt->close();
} catch (InvalidArgumentException $exception) { $_SESSION['error'] = $exception->getMessage(); }
catch (mysqli_sql_exception $exception) { $_SESSION['error'] = 'No se pudo eliminar la marcación.'; }
header('Location: attendance.php');
exit;
