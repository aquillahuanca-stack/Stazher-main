<?php
include 'includes/session.php';

$employeeFilter = isset($_GET['employee_id']) ? trim((string) $_GET['employee_id']) : '';
$selectedEmployee = null;
$attendanceRows = array();

if ($employeeFilter !== '') {
    $employeeStmt = $conn->prepare(
        "SELECT e.*, s.time_in, s.time_out, a.name_area, c.name_career, i.name_institution, m.type_modality, p.description AS position_description,
                COALESCE((SELECT SUM(" . attendanceSecondsSql('att') . ") FROM attendance att WHERE att.employee_id = e.id), 0) + e.accumulated_extra_seconds AS total_seconds,
                COALESCE(TIME_TO_SEC(e.add_hr), 0) AS required_seconds
         FROM employees e
         LEFT JOIN schedules s ON s.id = e.schedule_id
         LEFT JOIN area a ON a.id = e.area_id
         LEFT JOIN career c ON c.id = e.career_id
         LEFT JOIN institution i ON i.id = e.institution_id
         LEFT JOIN modality m ON m.id = e.modality_id
         LEFT JOIN position p ON p.id = e.position_id
         WHERE e.id = ? OR e.employee_id = ? OR CONCAT(e.firstname, ' ', e.lastname) LIKE ?
         LIMIT 1"
    );

    if ($employeeStmt) {
        $likeValue = '%' . $employeeFilter . '%';
        $internalId = ctype_digit($employeeFilter) ? (int) $employeeFilter : 0;
        $employeeStmt->bind_param('iss', $internalId, $employeeFilter, $likeValue);
        $employeeStmt->execute();
        $selectedEmployee = $employeeStmt->get_result()->fetch_assoc();
        $employeeStmt->close();
    }
}

if (!$selectedEmployee && $employeeFilter === '') {
    $fallbackStmt = $conn->prepare(
        "SELECT e.*, s.time_in, s.time_out, a.name_area, c.name_career, i.name_institution, m.type_modality, p.description AS position_description,
                COALESCE((SELECT SUM(" . attendanceSecondsSql('att') . ") FROM attendance att WHERE att.employee_id = e.id), 0) + e.accumulated_extra_seconds AS total_seconds,
                COALESCE(TIME_TO_SEC(e.add_hr), 0) AS required_seconds
         FROM employees e
         LEFT JOIN schedules s ON s.id = e.schedule_id
         LEFT JOIN area a ON a.id = e.area_id
         LEFT JOIN career c ON c.id = e.career_id
         LEFT JOIN institution i ON i.id = e.institution_id
         LEFT JOIN modality m ON m.id = e.modality_id
         LEFT JOIN position p ON p.id = e.position_id
         ORDER BY e.id ASC
         LIMIT 1"
    );

    if ($fallbackStmt) {
        $fallbackStmt->execute();
        $selectedEmployee = $fallbackStmt->get_result()->fetch_assoc();
        $fallbackStmt->close();
    }
}

if (!$selectedEmployee) {
    http_response_code(404);
    echo '<html><body><h2>No se encontró el pasante.</h2></body></html>';
    exit;
}

$employeeId = (int) $selectedEmployee['id'];
$parseReportDate = static function ($value) {
    if (!is_string($value) || !preg_match('/^\d{4}-\d{2}-\d{2}$/D', $value)) { return false; }
    $date = DateTimeImmutable::createFromFormat('!Y-m-d', $value);
    return $date && $date->format('Y-m-d') === $value ? $date : false;
};
$fromValue = $_GET['from'] ?? '';
$toValue = $_GET['to'] ?? '';
$reportFrom = $parseReportDate($fromValue);
$reportTo = $parseReportDate($toValue);
if (!$reportFrom || !$reportTo || $reportFrom > $reportTo) {
    $_SESSION['error'] = 'Selecciona fechas válidas. La fecha desde debe ser anterior o igual a la fecha hasta.';
    header('Location: employee_detail.php?id=' . $employeeId);
    exit;
}
$workloadReportPeriod = $reportFrom->format('d/m/Y') . ' al ' . $reportTo->format('d/m/Y');

$attendanceSql = "
    SELECT
        a.id,
        DATE_FORMAT(a.date, '%d/%m/%Y') AS fecha,
        CASE DAYOFWEEK(a.date)
            WHEN 1 THEN 'Domingo'
            WHEN 2 THEN 'Lunes'
            WHEN 3 THEN 'Martes'
            WHEN 4 THEN 'Miércoles'
            WHEN 5 THEN 'Jueves'
            WHEN 6 THEN 'Viernes'
            WHEN 7 THEN 'Sábado'
        END AS dia,
        TIME_FORMAT(a.time_in, '%H:%i') AS entrada,
        COALESCE(TIME_FORMAT(a.time_out, '%H:%i'), 'Sin salida') AS salida,
        COALESCE(" . attendanceSecondsSql('a') . ", 0) AS segundos_trabajados,
        (" . attendanceSecondsSql('a') . ") / 3600 AS num_hr
    FROM attendance a
    WHERE a.employee_id = ? AND a.date BETWEEN ? AND ?
    ORDER BY a.date ASC, a.time_in ASC
";

$attendanceStmt = $conn->prepare($attendanceSql);
if ($attendanceStmt) {
    $attendanceStmt->bind_param('iss', $employeeId, $fromValue, $toValue);
    $attendanceStmt->execute();
    $attendanceResult = $attendanceStmt->get_result();
    while ($row = $attendanceResult->fetch_assoc()) {
        $attendanceRows[] = $row;
    }
    $attendanceStmt->close();
}

$totalSeconds = 0;
foreach ($attendanceRows as $attendanceRow) {
    $totalSeconds += max(0, (int) ($attendanceRow['segundos_trabajados'] ?? 0));
}
$requiredSeconds = isset($selectedEmployee['required_seconds']) ? (int) $selectedEmployee['required_seconds'] : 0;
$remainingSeconds = max(0, $requiredSeconds - $totalSeconds);

function format_hours_display($seconds) {
    $totalMinutes = max(0, (int) floor($seconds / 60));
    $hours = intdiv($totalMinutes, 60);
    $minutes = $totalMinutes % 60;
    return $hours . ' h. ' . str_pad((string) $minutes, 2, '0', STR_PAD_LEFT) . ' min.';
}

function format_decimal_hours($seconds) {
    if ($seconds <= 0) {
        return '0';
    }
    $hours = $seconds / 3600;
    return number_format($hours, 2, '.', '');
}

$emissionDate = date('d/m/Y');
$fullName = trim(($selectedEmployee['firstname'] ?? '') . ' ' . ($selectedEmployee['lastname'] ?? ''));
$documentCode = !empty($selectedEmployee['identity_card'])
    ? $selectedEmployee['identity_card']
    : ($selectedEmployee['employee_id'] ?? 'N/A');
$careerName = $selectedEmployee['name_career'] ?? 'Carrera';
$institutionName = $selectedEmployee['name_institution'] ?? 'Institución';
$modalityName = $selectedEmployee['type_modality'] ?? 'Modalidad';
$areaName = $selectedEmployee['name_area'] ?? 'Área';
$positionName = $selectedEmployee['position_description'] ?? 'Cargo';
$employeeAddress = $selectedEmployee['address'] ?? 'Sin dirección';
$employeeEmail = $selectedEmployee['email'] ?? 'Sin correo';
$scheduleLabel = ($selectedEmployee['time_in'] && $selectedEmployee['time_out'])
    ? date('H:i', strtotime($selectedEmployee['time_in'])) . ' - ' . date('H:i', strtotime($selectedEmployee['time_out']))
    : 'Sin horario';
require __DIR__ . '/includes/workload_pdf.php';
