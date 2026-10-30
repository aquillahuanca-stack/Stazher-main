<?php
require_once 'includes/session.php';
require_once 'includes/notifications.php';
require_once 'includes/notifications.php';
requireCsrf();
$id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT, array('options' => array('min_range' => 1)));
$transaction = false;
try {
    if (!$id) { throw new InvalidArgumentException('Seleccione un pasante válido.'); }
    $conn->begin_transaction();
    $transaction = true;
    $stmt = $conn->prepare('SELECT id, firstname, lastname FROM employees WHERE id = ? FOR UPDATE');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $employee = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if (!$employee) { throw new InvalidArgumentException('El pasante ya no está disponible.'); }
    $stmt = $conn->prepare('SELECT id FROM attendance WHERE employee_id = ? LIMIT 1 FOR UPDATE');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $history = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if ($history) { throw new InvalidArgumentException('El pasante tiene asistencias registradas y no puede eliminarse. Conserve su historial.'); }
    $stmt = $conn->prepare('DELETE FROM employees WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
    $conn->commit();
    $transaction = false;
    createAdminNotification($conn, 'change', 'Pasante eliminado', trim($employee['firstname'] . ' ' . $employee['lastname']) . ' fue eliminado del sistema.', (int) $id);
    $_SESSION['success'] = 'Pasante eliminado correctamente.';
} catch (InvalidArgumentException $exception) {
    if ($transaction) { $conn->rollback(); }
    $_SESSION['error'] = $exception->getMessage();
} catch (mysqli_sql_exception $exception) {
    if ($transaction) { $conn->rollback(); }
    $_SESSION['error'] = 'No se pudo eliminar el pasante.';
}
header('Location: employee.php');
exit;
