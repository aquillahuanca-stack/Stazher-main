<?php
require_once __DIR__ . '/includes/session.php';
requireCsrf();
require_once __DIR__ . '/includes/notifications.php';

try {
    ensureAdminNotificationsTable($conn);
    $conn->query('UPDATE stazher_notifications SET read_at = NOW() WHERE read_at IS NULL');
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(array('ok' => true));
} catch (Throwable $error) {
    http_response_code(500);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(array('ok' => false));
}
