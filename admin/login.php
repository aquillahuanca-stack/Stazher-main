<?php
session_start();
require_once 'includes/conn.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = is_string($_POST['username'] ?? null) ? trim($_POST['username']) : '';
    $password = is_string($_POST['password'] ?? null) ? $_POST['password'] : '';
    $statement = $conn->prepare('SELECT id, password FROM admin WHERE username = ? LIMIT 1');
    $statement->bind_param('s', $username);
    $statement->execute();
    $account = $statement->get_result()->fetch_assoc();
    $statement->close();
    if ($account && password_verify($password, $account['password'])) {
        session_regenerate_id(true);
        $_SESSION['admin'] = $account['id'];
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    } else { $_SESSION['error'] = 'Usuario o contraseña incorrectos.'; }
} else { $_SESSION['error'] = 'Ingrese sus credenciales.'; }
header('Location: index.php');
exit;
