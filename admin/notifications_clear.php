<?php
require_once __DIR__ . '/includes/session.php';
requireCsrf();
require_once __DIR__ . '/includes/notifications.php';

header('Content-Type: application/json; charset=utf-8');

try {
    ensureAdminNotificationsTable($conn);
    $conn->query('DELETE FROM stazher_notifications');
    echo json_encode(array('ok' => true));
} catch (Throwable $error) {
    http_response_code(500);
    echo json_encode(array('ok' => false));
}
