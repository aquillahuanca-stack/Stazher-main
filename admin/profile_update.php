<?php
require_once 'includes/session.php';
requireCsrf();
$return = $_GET['return'] ?? 'home.php';
if (!is_string($return) || !preg_match('/^[a-z_]+\.php$/D', $return) || !is_file(__DIR__ . '/' . $return)) { $return = 'home.php'; }
try {
    $current = $_POST['curr_password'] ?? '';
    if (!is_string($current) || !password_verify($current, $user['password'])) { throw new InvalidArgumentException('Contraseña actual incorrecta.'); }
    foreach (array('username', 'firstname', 'lastname') as $key) {
        if (!is_string($_POST[$key] ?? null) || trim($_POST[$key]) === '' || mb_strlen($_POST[$key]) > 50) { throw new InvalidArgumentException('Revise el usuario, nombre y apellido.'); }
    }
    $username = trim($_POST['username']);
    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);
    $newPassword = $_POST['password'] ?? '';
    if (!is_string($newPassword)) { throw new InvalidArgumentException('Contraseña no válida.'); }
    if ($newPassword !== '' && strlen($newPassword) < 8) { throw new InvalidArgumentException('La nueva contraseña debe tener al menos 8 caracteres.'); }
    $password = $newPassword === '' ? $user['password'] : password_hash($newPassword, PASSWORD_DEFAULT);
    $photo = savePhoto($_FILES['photo'] ?? array()) ?: $user['photo'];
    $id = (int) $user['id'];
    $stmt = $conn->prepare('UPDATE admin SET username = ?, password = ?, firstname = ?, lastname = ?, photo = ? WHERE id = ?');
    $stmt->bind_param('sssssi', $username, $password, $firstname, $lastname, $photo, $id);
    $stmt->execute();
    $stmt->close();
    session_regenerate_id(true);
    $_SESSION['success'] = 'Perfil actualizado correctamente.';
} catch (InvalidArgumentException $exception) { $_SESSION['error'] = $exception->getMessage(); }
catch (mysqli_sql_exception | RuntimeException $exception) { $_SESSION['error'] = 'No se pudo actualizar el perfil.'; }
header('Location: ' . $return);
exit;
