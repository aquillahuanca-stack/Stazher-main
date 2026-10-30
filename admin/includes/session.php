<?php
if (session_status() !== PHP_SESSION_ACTIVE) { session_start(); }
if (empty($_SESSION['admin']) || !ctype_digit((string) $_SESSION['admin'])) {
    header('Location: index.php');
    exit;
}
require_once __DIR__ . '/conn.php';
require_once __DIR__ . '/security.php';
require_once __DIR__ . '/attendance_hours.php';
$adminId = (int) $_SESSION['admin'];
$statement = $conn->prepare('SELECT * FROM admin WHERE id = ?');
$statement->bind_param('i', $adminId);
$statement->execute();
$user = $statement->get_result()->fetch_assoc();
$statement->close();
if (!$user) {
    unset($_SESSION['admin']);
    header('Location: index.php');
    exit;
}
csrfField();
