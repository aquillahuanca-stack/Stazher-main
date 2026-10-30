<?php
include 'includes/session.php';
if (empty($_SESSION['admin'])) { exit; }

// El PDF siempre corresponde al día seleccionado en "Desde".
$dateValue = $_GET['from'] ?? '';
$parseDate = static function ($value) {
    if (!is_string($value) || !preg_match('/^\d{4}-\d{2}-\d{2}$/D', $value)) { return false; }
    $date = DateTimeImmutable::createFromFormat('!Y-m-d', $value);
    return $date && $date->format('Y-m-d') === $value ? $date : false;
};

$reportDate = $parseDate($dateValue);
if (!$reportDate) {
    $_SESSION['date_notice'] = 'Seleccione la fecha desde para generar el reporte.';
    header('Location: attendance.php');
    exit;
}

session_write_close();
$attendanceReportPeriod = 'Fecha: ' . $reportDate->format('d/m/Y');
$sql = "SELECT e.id AS intern_id, e.employee_id, e.firstname, e.lastname,
        a.date, a.time_in, a.time_out, a.status2,
        " . attendanceSecondsSql('a') . " AS duration_seconds
        FROM attendance a JOIN employees e ON e.id = a.employee_id
        WHERE a.time_in IS NOT NULL AND a.time_in <> '00:00:00'
          AND a.time_out IS NOT NULL AND a.time_out <> '00:00:00'
          AND a.date = ?
        ORDER BY a.date DESC, e.lastname, e.firstname, a.time_in";
$statement = $conn->prepare($sql);
$statement->bind_param('s', $dateValue);
$statement->execute();
$attendanceReportRows = $statement->get_result()->fetch_all(MYSQLI_ASSOC);
$statement->close();
$attendanceReportDestination = 'I';
$attendanceReportFilename = 'Reporte_Asistencia_' . $dateValue . '.pdf';
require __DIR__ . '/includes/attendance_report_pdf.php';
