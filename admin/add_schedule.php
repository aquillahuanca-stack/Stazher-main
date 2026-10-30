<?php
include 'includes/session.php';
if (!isset($_SESSION['admin']) || trim($_SESSION['admin']) === '') { exit; }
require_once 'includes/schedule_validation.php';
require_once 'includes/notifications.php';
requireCsrf();

$returnEmployeeId = isset($_POST['return_employee_id']) && is_string($_POST['return_employee_id']) && ctype_digit($_POST['return_employee_id']) ? (int) $_POST['return_employee_id'] : 0;
$transactionStarted = false;
try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new InvalidArgumentException('Complete el formulario para registrar un horario.');
    }
    $date = $_POST['date'] ?? '';
    $timeIn = $_POST['time_in'] ?? '';
    $timeOut = $_POST['time_out'] ?? '';
    $hours = validateScheduleEntry($date, $timeIn, $timeOut);
    $empid = $_POST['empid'] ?? '';
    if (!is_string($empid) || trim($empid) === '') {
        throw new InvalidArgumentException('Seleccione un pasante válido.');
    }

    $conn->begin_transaction();
    $transactionStarted = true;
    // Serializar registros del mismo pasante para evitar envíos duplicados.
    $statement = $conn->prepare('SELECT id, schedule_id FROM employees WHERE employee_id = ? FOR UPDATE');
    $statement->bind_param('s', $empid);
    $statement->execute();
    $employee = $statement->get_result()->fetch_assoc();
    $statement->close();
    if (!$employee) {
        throw new InvalidArgumentException('No se encontró el pasante.');
    }
    $employeeId = (int) $employee['id'];
    $statement = $conn->prepare('SELECT id FROM attendance WHERE employee_id = ? AND date = ? LIMIT 1 FOR UPDATE');
    $statement->bind_param('is', $employeeId, $date);
    $statement->execute();
    $exists = $statement->get_result()->fetch_assoc();
    $statement->close();
    if ($exists) {
        throw new InvalidArgumentException('Ya existe una marcación para esa fecha. Edítela desde Asistencia.');
    }

    $statement = $conn->prepare('SELECT time_in, time_out FROM schedules WHERE id = ?');
    $statement->bind_param('i', $employee['schedule_id']);
    $statement->execute();
    $schedule = $statement->get_result()->fetch_assoc();
    $statement->close();
    $timeIn .= ':00';
    $timeOut .= ':00';
    $status = $schedule && $schedule['time_in'] && $timeIn > $schedule['time_in'] ? 0 : 1;
    $status2 = 0;
    if ($schedule && $schedule['time_out']) {
        $status2 = $timeOut < $schedule['time_out'] ? 1 : ($timeOut > $schedule['time_out'] ? 2 : 0);
    }
    $statement = $conn->prepare('INSERT INTO attendance (employee_id, date, time_in, time_out, status, status2, num_hr) VALUES (?, ?, ?, ?, ?, ?, ?)');
    $statement->bind_param('isssiid', $employeeId, $date, $timeIn, $timeOut, $status, $status2, $hours);
    $statement->execute();
    $statement->close();
    $conn->commit();
    $transactionStarted = false;
    createAdminNotification($conn, 'change', 'Asistencia agregada', 'Se añadió una marcación de horario para el pasante ' . $empid . '.', (int) $employeeId);
    syncWorkloadCompletionNotifications($conn);
    $_SESSION['success'] = 'Horario registrado. La marcación y sus horas ya están incluidas en el acumulado.';
} catch (InvalidArgumentException $exception) {
    if ($transactionStarted) { $conn->rollback(); }
    $_SESSION['error'] = $exception->getMessage();
} catch (mysqli_sql_exception $exception) {
    if ($transactionStarted) { $conn->rollback(); }
    $_SESSION['error'] = 'No se pudo registrar el horario. Intente nuevamente.';
}
header('Location: ' . ($returnEmployeeId > 0 ? 'employee_detail.php?id=' . $returnEmployeeId : 'hours.php'));
exit;
