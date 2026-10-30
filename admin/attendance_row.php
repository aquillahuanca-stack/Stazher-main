<?php
require_once 'includes/session.php';
header('Content-Type: application/json; charset=utf-8');
$id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT, array('options' => array('min_range' => 1)));
if (!$id) { http_response_code(400); echo json_encode(null); exit; }
$stmt = $conn->prepare('SELECT attendance.*, attendance.id AS attid, employees.employee_id AS employee_code, employees.firstname, employees.lastname FROM attendance LEFT JOIN employees ON employees.id = attendance.employee_id WHERE attendance.id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
$stmt->close();
echo json_encode($row, JSON_INVALID_UTF8_SUBSTITUTE);
