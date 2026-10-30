<?php

function ensureAdminNotificationsTable(mysqli $conn): void
{
    static $ready = false;
    if ($ready) { return; }
    $conn->query("CREATE TABLE IF NOT EXISTS stazher_notifications (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        type VARCHAR(24) NOT NULL,
        title VARCHAR(120) NOT NULL,
        message VARCHAR(500) NOT NULL,
        employee_id INT NULL,
        dedupe_key VARCHAR(191) NULL,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        read_at DATETIME NULL,
        PRIMARY KEY (id),
        UNIQUE KEY uq_stazher_notifications_dedupe (dedupe_key),
        KEY idx_stazher_notifications_read (read_at, created_at)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    $ready = true;
}

function createAdminNotification(mysqli $conn, string $type, string $title, string $message, ?int $employeeId = null, ?string $dedupeKey = null): void
{
    try {
        ensureAdminNotificationsTable($conn);
        $stmt = $conn->prepare('INSERT IGNORE INTO stazher_notifications (type, title, message, employee_id, dedupe_key) VALUES (?, ?, ?, ?, ?)');
        $stmt->bind_param('sssis', $type, $title, $message, $employeeId, $dedupeKey);
        $stmt->execute();
        $stmt->close();
    } catch (Throwable $error) {
        // Notification failures must never undo a successful attendance or employee change.
    }
}

function syncWorkloadCompletionNotifications(mysqli $conn): void
{
    try {
        ensureAdminNotificationsTable($conn);
        $sql = "SELECT e.id, e.employee_id, e.firstname, e.lastname,
                       TIME_TO_SEC(e.add_hr) AS required_seconds,
                       COALESCE(SUM(" . attendanceSecondsSql('att') . "), 0) + e.accumulated_extra_seconds AS total_seconds
                FROM employees e
                LEFT JOIN attendance att ON att.employee_id = e.id
                GROUP BY e.id, e.employee_id, e.firstname, e.lastname, e.add_hr, e.accumulated_extra_seconds
                HAVING required_seconds > 0 AND total_seconds >= required_seconds";
        $result = $conn->query($sql);
        while ($employee = $result->fetch_assoc()) {
            $id = (int) $employee['id'];
            $name = trim($employee['firstname'] . ' ' . $employee['lastname']);
            $required = (int) $employee['required_seconds'];
            $key = 'workload-complete:' . $id . ':' . $required;
            createAdminNotification(
                $conn,
                'completion',
                'Carga horaria completada',
                $name . ' (' . $employee['employee_id'] . ') ha completado su carga horaria requerida.',
                $id,
                $key
            );
        }
        $result->free();
    } catch (Throwable $error) {
        error_log('STAZHER: no se pudo sincronizar la alerta de carga horaria.');
    }
}

