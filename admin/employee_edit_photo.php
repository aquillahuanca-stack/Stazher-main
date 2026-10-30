<?php
require_once 'includes/session.php';
requireCsrf();
try {
    $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT, array('options' => array('min_range' => 1)));
    if (!$id) { throw new InvalidArgumentException('Seleccione un pasante válido.'); }
    $stmt = $conn->prepare('SELECT id FROM employees WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $employee = $stmt->get_result()->fetch_assoc();
    $stmt->close();
    if (!$employee) { throw new InvalidArgumentException('No se encontró el pasante.'); }
    $photo = savePhoto($_FILES['photo'] ?? array());
    if ($photo === '') { throw new InvalidArgumentException('Seleccione una fotografía.'); }
    $stmt = $conn->prepare('UPDATE employees SET photo = ? WHERE id = ?');
    $stmt->bind_param('si', $photo, $id);
    $stmt->execute();
    $stmt->close();
    $_SESSION['success'] = 'Fotografía actualizada correctamente.';
} catch (InvalidArgumentException $exception) { $_SESSION['error'] = $exception->getMessage(); }
catch (mysqli_sql_exception | RuntimeException $exception) { $_SESSION['error'] = 'No se pudo actualizar la fotografía.'; }
header('Location: employee.php');
exit;
