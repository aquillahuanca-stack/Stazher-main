<?php
require_once 'includes/session.php';
header('Content-Type: application/json; charset=utf-8');
$id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT, array('options' => array('min_range' => 1)));
if (!$id) { http_response_code(400); echo json_encode(null); exit; }
$stmt = $conn->prepare('SELECT employees.*, employees.id AS empid, position.description, schedules.time_in, schedules.time_out, modality.type_modality, institution.name_institution, career.name_career, area.name_area FROM employees LEFT JOIN position ON position.id = employees.position_id LEFT JOIN schedules ON schedules.id = employees.schedule_id LEFT JOIN modality ON modality.id = employees.modality_id LEFT JOIN institution ON institution.id = employees.institution_id LEFT JOIN career ON career.id = employees.career_id LEFT JOIN area ON area.id = employees.area_id WHERE employees.id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
$stmt->close();
echo json_encode($row, JSON_INVALID_UTF8_SUBSTITUTE);
