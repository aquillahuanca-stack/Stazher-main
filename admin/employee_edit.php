<?php
require_once 'includes/session.php';
require_once 'includes/employee_input.php';
requireCsrf();
try {
    $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT, array('options' => array('min_range' => 1)));
    if (!$id) { throw new InvalidArgumentException('Seleccione un pasante válido.'); }
    $data = employeeInput($conn, 0);
    $stmt = $conn->prepare('SELECT id FROM employees WHERE identity_card = ? AND id <> ? LIMIT 1');
    $stmt->bind_param('si', $data['identity_card'], $id);
    $stmt->execute();
    $exists = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if ($exists) { throw new InvalidArgumentException('El carnet ya está registrado para otro pasante.'); }
    $stmt = $conn->prepare('SELECT id FROM employees WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $exists = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if (!$exists) { throw new InvalidArgumentException('No se encontró el pasante.'); }
    $values = array($data['firstname'], $data['lastname'], $data['identity_card'], $data['email'], $data['address'], $data['birthdate'], $data['contact'], $data['gender'], $data['position'], $data['schedule'], $data['career'], $data['area'], $data['institution'], $data['modality'], $data['add_hr'], $id);
    $stmt = $conn->prepare('UPDATE employees SET firstname = ?, lastname = ?, identity_card = ?, email = ?, address = ?, birthdate = ?, contact_info = ?, gender = ?, position_id = ?, schedule_id = ?, career_id = ?, area_id = ?, institution_id = ?, modality_id = ?, add_hr = ? WHERE id = ?');
    $stmt->bind_param(str_repeat('s', count($values)), ...$values);
    $stmt->execute();
    $stmt->close();
    require_once __DIR__ . '/includes/notifications.php';
    createAdminNotification($conn, 'change', 'Datos de pasante actualizados', trim($data['firstname'] . ' ' . $data['lastname']) . ': se modificaron sus datos o su carga horaria.', (int) $id);
    syncWorkloadCompletionNotifications($conn);
    $_SESSION['success'] = 'Pasante actualizado correctamente.';
} catch (InvalidArgumentException $exception) { $_SESSION['error'] = $exception->getMessage(); }
catch (mysqli_sql_exception $exception) { $_SESSION['error'] = 'No se pudo actualizar el pasante.'; }
header('Location: employee.php');
exit;
