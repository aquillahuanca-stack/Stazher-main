<?php
require_once __DIR__ . '/session.php';
require_once __DIR__ . '/attendance_hours.php';
require_once __DIR__ . '/notifications.php';
requireCsrf();
$transaction = false;
try {
    $editing = basename($_SERVER['SCRIPT_FILENAME']) === 'attendance_edit.php';
    $date = $_POST[$editing ? 'edit_date' : 'date'] ?? null;
    $timeIn = $_POST[$editing ? 'edit_time_in' : 'time_in'] ?? null;
    $timeOut = $_POST[$editing ? 'edit_time_out' : 'time_out'] ?? null;
    $parsed = is_string($date) ? DateTimeImmutable::createFromFormat('!Y-m-d', $date) : false;
    if (!$parsed || $parsed->format('Y-m-d') !== $date || $date < '1000-01-01' || $date > date('Y-m-d')) { throw new InvalidArgumentException('Ingrese una fecha válida que no sea futura.'); }
    foreach (array(&$timeIn, &$timeOut) as &$time) {
        if (!is_string($time) || !preg_match('/^(?:[0-9]|[01][0-9]|2[0-3]):[0-5][0-9](?::[0-5][0-9])?$/D', $time)) { throw new InvalidArgumentException('Ingrese horas válidas.'); }
        $time = str_pad($time, strpos($time, ':') === 1 ? strlen($time) + 1 : strlen($time), '0', STR_PAD_LEFT);
        if (strlen($time) === 5) { $time .= ':00'; }
    }
    unset($time);
    $hours = attendanceHours($timeIn, $timeOut);
    if ($date === date('Y-m-d') && $timeOut > date('H:i:s')) { throw new InvalidArgumentException('No puede registrar una salida futura.'); }
    $conn->begin_transaction();
    $transaction = true;
    if ($editing) {
        $recordId = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT, array('options' => array('min_range' => 1)));
        if (!$recordId) { throw new InvalidArgumentException('Seleccione una marcación válida.'); }
        $stmt = $conn->prepare('SELECT employee_id FROM attendance WHERE id = ?');
        $stmt->bind_param('i', $recordId);
        $stmt->execute();
        $record = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if (!$record) { throw new InvalidArgumentException('No se encontró la marcación.'); }
        $employeeId = (int) $record['employee_id'];
        $stmt = $conn->prepare('SELECT e.id, e.firstname, e.lastname, e.employee_id, s.time_in, s.time_out FROM employees e LEFT JOIN schedules s ON s.id = e.schedule_id WHERE e.id = ? FOR UPDATE');
        $stmt->bind_param('i', $employeeId);
    } else {
        $code = $_POST['employee'] ?? null;
        if (!is_string($code) || trim($code) === '') { throw new InvalidArgumentException('Ingrese el código del pasante.'); }
        $stmt = $conn->prepare('SELECT e.id, e.firstname, e.lastname, e.employee_id, s.time_in, s.time_out FROM employees e LEFT JOIN schedules s ON s.id = e.schedule_id WHERE e.employee_id = ? FOR UPDATE');
        $stmt->bind_param('s', $code);
        $recordId = 0;
    }
    $stmt->execute();
    $employee = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if (!$employee) { throw new InvalidArgumentException('No se encontró el pasante.'); }
    $employeeId = (int) $employee['id'];
    $stmt = $conn->prepare('SELECT id FROM attendance WHERE employee_id = ? AND date = ? AND id <> ? FOR UPDATE');
    $stmt->bind_param('isi', $employeeId, $date, $recordId);
    $stmt->execute();
    $duplicate = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if ($duplicate) { throw new InvalidArgumentException('Ya existe una marcación del pasante para esa fecha.'); }
    $status = $employee['time_in'] && $timeIn > $employee['time_in'] ? 0 : 1;
    $status2 = $employee['time_out'] ? ($timeOut < $employee['time_out'] ? 1 : 2) : 0;
    if ($editing) {
        $stmt = $conn->prepare('UPDATE attendance SET date = ?, time_in = ?, time_out = ?, status = ?, status2 = ?, num_hr = ? WHERE id = ? AND employee_id = ?');
        $stmt->bind_param('sssiidii', $date, $timeIn, $timeOut, $status, $status2, $hours, $recordId, $employeeId);
        $stmt->execute();
        if ($stmt->affected_rows === 0) {
            $check = $conn->prepare('SELECT id FROM attendance WHERE id = ? AND employee_id = ?');
            $check->bind_param('ii', $recordId, $employeeId);
            $check->execute();
            if (!$check->get_result()->fetch_assoc()) { throw new InvalidArgumentException('La marcación ya no está disponible.'); }
            $check->close();
        }
    } else {
        $stmt = $conn->prepare('INSERT INTO attendance (employee_id, date, time_in, time_out, status, status2, num_hr) VALUES (?, ?, ?, ?, ?, ?, ?)');
        $stmt->bind_param('isssiid', $employeeId, $date, $timeIn, $timeOut, $status, $status2, $hours);
        $stmt->execute();
    }
    $stmt->close();
    $conn->commit();
    $transaction = false;
    $changeVerb = $editing ? 'actualizada' : 'registrada';
    createAdminNotification($conn, 'change', 'Asistencia ' . $changeVerb, trim($employee['firstname'] . ' ' . $employee['lastname']) . ' (' . $employee['employee_id'] . '): marcación del ' . date('d/m/Y', strtotime($date)) . '.', $employeeId);
    syncWorkloadCompletionNotifications($conn);
    $_SESSION['success'] = $editing ? 'Marcación actualizada correctamente.' : 'Marcación registrada correctamente.';
} catch (InvalidArgumentException $exception) {
    if ($transaction) { $conn->rollback(); }
    $_SESSION['error'] = $exception->getMessage();
} catch (mysqli_sql_exception $exception) {
    if ($transaction) { $conn->rollback(); }
    $_SESSION['error'] = 'No se pudo guardar la marcación. Intente nuevamente.';
}
header('Location: attendance.php');
exit;
