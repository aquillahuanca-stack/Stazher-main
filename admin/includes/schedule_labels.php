<?php
function getScheduleTurnLabel(array $schedule): string
{
    $timeIn = substr((string) ($schedule['time_in'] ?? ''), 0, 5);
    $timeOut = substr((string) ($schedule['time_out'] ?? ''), 0, 5);

    if ($timeIn === '08:30' && in_array($timeOut, array('13:00', '13:30'), true)) {
        return 'Turno Mañana';
    }
    if (in_array($timeIn, array('12:00', '12:30'), true) && $timeOut === '18:30') {
        return 'Turno Tarde';
    }
    if ($timeIn === '08:30' && in_array($timeOut, array('18:30', '20:00'), true)) {
        return 'Tiempo Completo';
    }
    return '';
}

function getScheduleOptionLabel(array $schedule): string
{
    $timeIn = substr((string) ($schedule['time_in'] ?? ''), 0, 5);
    $timeOut = substr((string) ($schedule['time_out'] ?? ''), 0, 5);
    $turnLabel = getScheduleTurnLabel($schedule);
    $label = $timeIn . ' - ' . $timeOut;

    return $turnLabel === '' ? $label : $label . ' (' . $turnLabel . ')';
}