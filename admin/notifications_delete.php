<?php
require_once __DIR__ . '/includes/session.php';
requireCsrf();
require_once __DIR__ . '/includes/notifications.php';

header('Content-Type: application/json; charset=utf-8');

$notificationId = filter_input(INPUT_POST, 'notification_id', FILTER_VALIDATE_INT, array('options' => array('min_range' => 1)));
if (!$notificationId) {
    http_response_code(400);
    echo json_encode(array('ok' => false));
    exit;
}

try {
    ensureAdminNotificationsTable($conn);
    $statement = $conn->prepare('DELETE FROM stazher_notifications WHERE id = ?');
    $statement->bind_param('i', $notificationId);
    $statement->execute();
    $statement->close();

    $result = $conn->query('SELECT COUNT(*) AS total FROM stazher_notifications WHERE read_at IS NULL');
    $unread = (int) $result->fetch_assoc()['total'];
    echo json_encode(array('ok' => true, 'unread' => $unread));
} catch (Throwable $error) {
    http_response_code(500);
    echo json_encode(array('ok' => false));
}
