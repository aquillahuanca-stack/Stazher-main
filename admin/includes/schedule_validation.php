<?php
function validateScheduleEntry($date, $timeIn, $timeOut)
{
    if (!is_string($date) || !is_string($timeIn) || !is_string($timeOut)) {
        throw new InvalidArgumentException('Complete la fecha y las horas de entrada y salida.');
    }
    $parsedDate = DateTimeImmutable::createFromFormat('!Y-m-d', $date);
    if (!$parsedDate || $parsedDate->format('Y-m-d') !== $date || $date < '1000-01-01') {
        throw new InvalidArgumentException('Ingrese una fecha válida.');
    }
    $pattern = '/^([01][0-9]|2[0-3]):[0-5][0-9]$/D';
    if (!preg_match($pattern, $timeIn) || !preg_match($pattern, $timeOut)) {
        throw new InvalidArgumentException('Ingrese horas válidas de entrada y salida.');
    }
    $start = (int) substr($timeIn, 0, 2) * 60 + (int) substr($timeIn, 3, 2);
    $end = (int) substr($timeOut, 0, 2) * 60 + (int) substr($timeOut, 3, 2);
    if ($end <= $start) {
        throw new InvalidArgumentException('La salida debe ser posterior a la entrada del mismo día.');
    }
    if ($date > date('Y-m-d') || ($date === date('Y-m-d') && $timeOut . ':00' > date('H:i:s'))) {
        throw new InvalidArgumentException('No puede registrar una marcación futura.');
    }
    require_once __DIR__ . '/attendance_hours.php';
    return attendanceHours($timeIn . ':00', $timeOut . ':00');
}
