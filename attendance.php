<?php
header('Content-Type: application/json; charset=utf-8');
require_once 'conn.php';
require_once 'admin/includes/attendance_hours.php';
$output = array('error' => true, 'message' => 'Datos incompletos.');
$transaction = false;
try {
    $employeeCode = $_POST['employee'] ?? null;
    $status = $_POST['status'] ?? null;
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !is_string($employeeCode) || trim($employeeCode) === '' || strlen($employeeCode) > 15 || !in_array($status, array('in', 'out'), true)) {
        throw new InvalidArgumentException('Ingrese un código de pasante y una acción válidos.');
    }
    $employeeCode = trim($employeeCode);
    $conn->begin_transaction();
    $transaction = true;
    // El bloqueo del pasante serializa entradas y salidas simultáneas.
    $stmt = $conn->prepare('SELECT e.id, e.firstname, e.lastname, s.time_in AS scheduled_in, s.time_out AS scheduled_out FROM employees e LEFT JOIN schedules s ON s.id = e.schedule_id WHERE e.employee_id = ? FOR UPDATE');
    $stmt->bind_param('s', $employeeCode);
    $stmt->execute();
    $employee = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if (!$employee) { throw new InvalidArgumentException('Código de pasante no encontrado.'); }
    $id = (int) $employee['id'];
    $now = new DateTimeImmutable('now', new DateTimeZone('America/La_Paz'));
    $date = $now->format('Y-m-d');
    $time = $now->format('H:i:s');
    $stmt = $conn->prepare('SELECT id, time_in, time_out FROM attendance WHERE employee_id = ? AND date = ? ORDER BY id FOR UPDATE');
    $stmt->bind_param('is', $id, $date);
    $stmt->execute();
    $records = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    if (count($records) > 1) { throw new InvalidArgumentException('Hay marcaciones duplicadas para hoy. Consulte al administrador.'); }
    $record = $records[0] ?? null;
    if ($status === 'in') {
        if ($record) { throw new InvalidArgumentException('Ya tiene una marcación para hoy. Consulte al administrador si necesita corregirla.'); }
        if ($time === '00:00:00') { throw new InvalidArgumentException('Intente nuevamente en unos segundos.'); }
        $entryStatus = $employee['scheduled_in'] && $time > $employee['scheduled_in'] ? 0 : 1;
        $exitStatus = 0;
        $stmt = $conn->prepare('INSERT INTO attendance (employee_id, date, time_in, status, status2, num_hr) VALUES (?, ?, ?, ?, ?, 0)');
        $stmt->bind_param('issii', $id, $date, $time, $entryStatus, $exitStatus);
        $stmt->execute();
        $stmt->close();
        $message = 'Entrada registrada para: ';
    } else {
        if (!$record) { throw new InvalidArgumentException('Registre su entrada antes de marcar la salida.'); }
        if (!empty($record['time_out']) && $record['time_out'] !== '00:00:00') { throw new InvalidArgumentException('Ya registró su salida por hoy.'); }
        $hours = attendanceHours($record['time_in'], $time);
        $exitStatus = $employee['scheduled_out'] ? ($time < $employee['scheduled_out'] ? 1 : 2) : 0;
        $recordId = (int) $record['id'];
        $stmt = $conn->prepare('UPDATE attendance SET time_out = ?, status2 = ?, num_hr = ? WHERE id = ?');
        $stmt->bind_param('sidi', $time, $exitStatus, $hours, $recordId);
        $stmt->execute();
        $stmt->close();
        $message = 'Salida registrada para: ';
    }
    $conn->commit();
    $transaction = false;
    $output = array('error' => false, 'message' => $message . $employee['firstname'] . ' ' . $employee['lastname']);
} catch (InvalidArgumentException $exception) {
    if ($transaction) { $conn->rollback(); }
    $output['message'] = $exception->getMessage();
} catch (mysqli_sql_exception $exception) {
    if ($transaction) { $conn->rollback(); }
    $output['message'] = 'No se pudo guardar la marcación. Intente nuevamente.';
}
echo json_encode($output, JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
