<?php
include 'includes/session.php';
require_once __DIR__ . '/includes/attendance_status.php';

header('Content-Type: application/json; charset=utf-8');

if (!isset($_POST['id']) || !ctype_digit((string) $_POST['id'])) {
    echo json_encode(array('employee' => null, 'attendance' => array(), 'summary' => array()));
    exit;
}

$id = (int) $_POST['id'];
$sql = "SELECT employees.id, employees.employee_id, employees.firstname, employees.lastname,
        employees.identity_card, employees.email, employees.contact_info, employees.address, employees.birthdate, employees.gender,
        employees.photo, employees.created_on, area.name_area, career.name_career,
        institution.name_institution, modality.type_modality, position.description,
        CASE WHEN schedules.time_in IS NOT NULL AND schedules.time_out IS NOT NULL
          THEN CONCAT(TIME_FORMAT(schedules.time_in, '%h:%i %p'), ' - ', TIME_FORMAT(schedules.time_out, '%h:%i %p'))
        ELSE NULL END AS schedule, schedules.time_out AS scheduled_exit
        FROM employees
        LEFT JOIN schedules ON schedules.id = employees.schedule_id
        LEFT JOIN position ON position.id = employees.position_id
        LEFT JOIN modality ON modality.id = employees.modality_id
        LEFT JOIN institution ON institution.id = employees.institution_id
        LEFT JOIN area ON area.id = employees.area_id
        LEFT JOIN career ON career.id = employees.career_id
        WHERE employees.id = ?";
$statement = $conn->prepare($sql);
$statement->bind_param('i', $id);
$statement->execute();
$employee = $statement->get_result()->fetch_assoc();
$statement->close();

if (!$employee) {
    echo json_encode(array('employee' => null, 'attendance' => array(), 'summary' => array()));
    exit;
}

$summary = array('hours' => 0, 'required_hours' => 0, 'remaining_hours' => 0);
$sql = "SELECT COALESCE(SUM(" . attendanceSecondsSql('') . "), 0) AS attendance_seconds,
        MIN(date) AS first_attendance_date,
        MAX(date) AS last_attendance_date,
        COALESCE(TIME_TO_SEC((SELECT add_hr FROM employees WHERE id = ?)), 0) AS required_seconds
        FROM attendance WHERE employee_id = ?";
$statement = $conn->prepare($sql);
$statement->bind_param('ii', $id, $id);
$statement->execute();
$hoursRow = $statement->get_result()->fetch_assoc();
$statement->close();
$extraStatement = $conn->prepare('SELECT accumulated_extra_seconds FROM employees WHERE id = ?');
$extraStatement->bind_param('i', $id);
$extraStatement->execute();
$extraSeconds = (int) $extraStatement->get_result()->fetch_assoc()['accumulated_extra_seconds'];
$extraStatement->close();
$totalSeconds = (int) ($hoursRow['attendance_seconds'] ?? 0) + $extraSeconds;
$summary['hours'] = round($totalSeconds / 3600, 2);
$summary['required_hours'] = round((int) ($hoursRow['required_seconds'] ?? 0) / 3600, 2);
$summary['remaining_hours'] = round(max(0, $summary['required_hours'] - $summary['hours']), 2);
$summary['first_attendance_date'] = $hoursRow['first_attendance_date'] ?? null;
$summary['last_attendance_date'] = $hoursRow['last_attendance_date'] ?? null;

$sql = "SELECT DATE_FORMAT(date, '%d/%m/%Y') AS date,
    attendance.date AS record_date, attendance.time_out AS raw_time_out, status2,
    CASE DAYOFWEEK(date)
      WHEN 1 THEN 'Domingo'
      WHEN 2 THEN 'Lunes'
      WHEN 3 THEN 'Martes'
      WHEN 4 THEN 'Miércoles'
      WHEN 5 THEN 'Jueves'
      WHEN 6 THEN 'Viernes'
      WHEN 7 THEN 'Sábado'
    END AS day,
        TIME_FORMAT(time_in, '%h:%i %p') AS time_in,
        COALESCE(TIME_FORMAT(time_out, '%h:%i %p'), 'Sin salida') AS time_out,
    (" . attendanceSecondsSql() . ") / 3600 AS num_hr
        FROM attendance WHERE employee_id = ?
        ORDER BY attendance.date DESC, attendance.time_in DESC";
$statement = $conn->prepare($sql);
$statement->bind_param('i', $id);
$statement->execute();
$attendance = array();
$result = $statement->get_result();
$statusNow = new DateTimeImmutable('now', new DateTimeZone('America/La_Paz'));
while ($row = $result->fetch_assoc()) {
    $row['exit_status'] = attendanceExitStatus(array(
        'date' => $row['record_date'],
        'time_out' => $row['raw_time_out'],
        'status2' => $row['status2'],
        'scheduled_exit' => $employee['scheduled_exit']
    ), $statusNow);
    unset($row['record_date'], $row['raw_time_out']);
    $attendance[] = $row;
}
$statement->close();

echo json_encode(array('employee' => $employee, 'attendance' => $attendance, 'summary' => $summary));
?>
