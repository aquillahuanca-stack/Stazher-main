<?php
function attendanceExitStatus(array $record, DateTimeImmutable $now)
{
    if (!empty($record['time_out']) && $record['time_out'] !== '00:00:00') {
        return (int) ($record['status2'] ?? 0) === 3 ? 'scheduled' : 'recorded';
    }
    $localNow = $now->setTimezone(new DateTimeZone('America/La_Paz'));
    $exit = $record['scheduled_exit'] ?? '';
    if ($record['date'] === $localNow->format('Y-m-d') && $exit !== '' && $localNow->format('H:i:s') < $exit) {
        return 'in_progress';
    }
    return 'missing';
}
