<?php
require_once __DIR__ . '/session.php';
if (!function_exists('certificateNumberWords')) {
    function certificateNumberWords(int $number): string
    {
        $number = max(0, $number);
        $units = array('cero','uno','dos','tres','cuatro','cinco','seis','siete','ocho','nueve','diez','once','doce','trece','catorce','quince','dieciséis','diecisiete','dieciocho','diecinueve','veinte','veintiuno','veintidós','veintitrés','veinticuatro','veinticinco','veintiséis','veintisiete','veintiocho','veintinueve');
        if ($number < 30) { $words = $units[$number]; }
        elseif ($number < 100) {
            $tens = array(3=>'treinta',4=>'cuarenta',5=>'cincuenta',6=>'sesenta',7=>'setenta',8=>'ochenta',9=>'noventa');
            $words = $tens[intdiv($number, 10)] . ($number % 10 ? ' y ' . $units[$number % 10] : '');
        } elseif ($number < 1000) {
            $hundreds = array(1=>'ciento',2=>'doscientos',3=>'trescientos',4=>'cuatrocientos',5=>'quinientos',6=>'seiscientos',7=>'setecientos',8=>'ochocientos',9=>'novecientos');
            $words = $number === 100 ? 'cien' : $hundreds[intdiv($number, 100)] . ($number % 100 ? ' ' . certificateNumberWords($number % 100) : '');
        } elseif ($number < 1000000) {
            $thousands = intdiv($number, 1000);
            $thousandWords = $thousands === 1 ? '' : certificateNumberWords($thousands);
            $thousandWords = preg_replace('/veintiuno$/u', 'veintiún', $thousandWords);
            $thousandWords = preg_replace('/ uno$/u', ' un', $thousandWords);
            $words = trim($thousandWords . ' mil ' . ($number % 1000 ? certificateNumberWords($number % 1000) : ''));
        } else { $words = (string) $number; }
        $words = preg_replace('/veintiuno$/u', 'veintiún', $words);
        if ($words === 'uno') { $words = 'un'; }
        return preg_replace('/ uno$/u', ' un', $words);
    }
}
$employeeCode = $_GET['empid'] ?? null;
if (!is_string($employeeCode) || trim($employeeCode) === '' || mb_strlen($employeeCode) > 15) {
    http_response_code(400);
    exit('Seleccione un pasante válido.');
}
$employeeCode = trim($employeeCode);
$sql = "SELECT e.*, m.type_modality, a.name_area AS description,
        COALESCE((SELECT SUM(" . attendanceSecondsSql('att') . ") FROM attendance att WHERE att.employee_id = e.id), 0) + e.accumulated_extra_seconds AS totalSeconds,
        e.id AS empid
        FROM employees e
        LEFT JOIN modality m ON m.id = e.modality_id
        LEFT JOIN area a ON a.id = e.area_id
        WHERE e.employee_id = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $employeeCode);
$stmt->execute();
$employee = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$employee || !$employee['type_modality'] || !$employee['description']) {
    http_response_code(404);
    exit('No se encontró el pasante o faltan su modalidad y área.');
}
$employee['totalSeconds'] = (float) $employee['totalSeconds'];
$employee['totalHours'] = $employee['totalSeconds'] / 3600;
$areaName = trim((string) $employee['description']);
$areaName = preg_replace('/^(?:Área|Area)\s+de\s+/iu', '', $areaName);
$employee['certificate_area'] = 'Dirección Administrativa Financiera – Área de ' . $areaName;
