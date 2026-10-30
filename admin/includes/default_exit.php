<?php
// status2 = 3 identifica una salida completada con el horario asignado.
function assignedExitHours(array $record, DateTimeImmutable $now)
{
    if (!empty($record['time_out']) && $record['time_out'] !== '00:00:00') {
        throw new InvalidArgumentException('Esta marcación ya tiene una salida registrada.');
    }
    $entry = $record['time_in'] ?? '';
    $exit = $record['scheduled_exit'] ?? '';
    $pattern = '/^(?:[01][0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/D';
    if (!preg_match($pattern, $entry) || $entry === '00:00:00') {
        throw new InvalidArgumentException('Se requiere una entrada registrada.');
    }
    if (!preg_match($pattern, $exit)) {
        throw new InvalidArgumentException('El pasante no tiene un horario de salida asignado.');
    }
    if ($exit <= $entry) {
        throw new InvalidArgumentException('La salida asignada debe ser posterior a la entrada. Revise la marcación manualmente.');
    }
    $end = DateTimeImmutable::createFromFormat('!Y-m-d H:i:s', $record['date'] . ' ' . $exit, new DateTimeZone('America/La_Paz'));
    if (!$end || $end->format('Y-m-d H:i:s') !== $record['date'] . ' ' . $exit || $end > $now) {
        throw new InvalidArgumentException('Solo puede completar la salida después de finalizar el horario asignado.');
    }
    require_once __DIR__ . '/attendance_hours.php';
    return attendanceHours($entry, $exit);
}
