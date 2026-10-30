<?php
require_once 'includes/session.php';
require_once 'includes/employee_input.php';
requireCsrf();
try {
    $data = employeeInput($conn, 600);
    $employeeCode = mb_strtoupper(mb_substr($data['firstname'], 0, 1) . mb_substr($data['lastname'], 0, 1) . $data['identity_card']);
    $stmt = $conn->prepare('SELECT id FROM employees WHERE employee_id = ? OR identity_card = ? LIMIT 1');
    $stmt->bind_param('ss', $employeeCode, $data['identity_card']);
    $stmt->execute();
    $exists = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if ($exists) { throw new InvalidArgumentException('Ya existe un pasante con ese código o carnet.'); }
    $photo = savePhoto($_FILES['photo'] ?? array());
    $created = date('Y-m-d');
    $values = array($employeeCode, $data['firstname'], $data['lastname'], $data['identity_card'], $data['email'], $data['address'], $data['birthdate'], $data['contact'], $data['gender'], $data['position'], $data['schedule'], $data['career'], $data['area'], $data['institution'], $data['modality'], $photo, $created, $data['add_hr']);
    $stmt = $conn->prepare('INSERT INTO employees (employee_id, firstname, lastname, identity_card, email, address, birthdate, contact_info, gender, position_id, schedule_id, career_id, area_id, institution_id, modality_id, photo, created_on, add_hr) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
    $stmt->bind_param(str_repeat('s', count($values)), ...$values);
    $stmt->execute();
    $newEmployeeId = (int) $stmt->insert_id;
    $stmt->close();
    require_once __DIR__ . '/includes/notifications.php';
    createAdminNotification($conn, 'change', 'Nuevo pasante registrado', trim($data['firstname'] . ' ' . $data['lastname']) . ' fue registrado en el sistema.', $newEmployeeId);
    $_SESSION['success'] = 'Pasante registrado correctamente.';
} catch (InvalidArgumentException $exception) {
    $_SESSION['error'] = $exception->getMessage();
} catch (mysqli_sql_exception | RuntimeException $exception) {
    $_SESSION['error'] = 'No se pudo registrar el pasante. Revise los datos e intente nuevamente.';
}
header('Location: employee.php');
exit;
