<?php
require_once 'includes/session.php';
requireCsrf();

$transactionStarted = false;
try {
    $employeeCode = isset($_POST['empid']) && is_string($_POST['empid']) ? trim($_POST['empid']) : '';
    $hours = filter_var($_POST['hours'] ?? null, FILTER_VALIDATE_INT, array('options' => array('min_range' => 0, 'max_range' => 10000)));
    $minutes = filter_var($_POST['minutes'] ?? null, FILTER_VALIDATE_INT, array('options' => array('min_range' => 0, 'max_range' => 59)));
    if ($employeeCode === '' || $hours === false || $minutes === false) {
        throw new InvalidArgumentException('Ingrese una cantidad válida de horas y minutos.');
    }
    $secondsToAdd = ($hours * 3600) + ($minutes * 60);
    if ($secondsToAdd <= 0) {
        throw new InvalidArgumentException('Ingrese al menos un minuto para sumar al total.');
    }

    $conn->begin_transaction();
    $transactionStarted = true;
    $statement = $conn->prepare('SELECT id, firstname, lastname FROM employees WHERE employee_id = ? FOR UPDATE');
    $statement->bind_param('s', $employeeCode);
    $statement->execute();
    $employee = $statement->get_result()->fetch_assoc();
    $statement->close();
    if (!$employee) {
        throw new InvalidArgumentException('No se encontró el pasante seleccionado.');
    }

    $statement = $conn->prepare('UPDATE employees SET accumulated_extra_seconds = COALESCE(accumulated_extra_seconds, 0) + ? WHERE id = ?');
    $employeeId = (int) $employee['id'];
    $statement->bind_param('ii', $secondsToAdd, $employeeId);
    $statement->execute();
    $statement->close();
    $conn->commit();
    $transactionStarted = false;

    require_once __DIR__ . '/includes/notifications.php';
    $amount = $hours . ' h ' . str_pad((string) $minutes, 2, '0', STR_PAD_LEFT) . ' min';
    createAdminNotification($conn, 'change', 'Horas añadidas', 'Se añadieron ' . $amount . ' al total de ' . trim($employee['firstname'] . ' ' . $employee['lastname']) . '.', $employeeId);
    syncWorkloadCompletionNotifications($conn);
    $_SESSION['success'] = 'Se añadieron ' . $amount . ' al total acumulado.';
} catch (InvalidArgumentException $exception) {
    if ($transactionStarted) { $conn->rollback(); }
    $_SESSION['error'] = $exception->getMessage();
} catch (mysqli_sql_exception $exception) {
    if ($transactionStarted) { $conn->rollback(); }
    $_SESSION['error'] = 'No se pudieron sumar las horas. Intente nuevamente.';
}

header('Location: employee.php');
exit;
