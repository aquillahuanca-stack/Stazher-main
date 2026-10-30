<?php
// La regla predeterminada acredita tiempo real, sin descuentos automáticos.
function attendanceSecondsSql(string $alias = ''): string
{
    if ($alias !== '' && !preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/D', $alias)) { throw new InvalidArgumentException('Alias inválido.'); }
    $prefix = $alias === '' ? '' : $alias . '.';
    return "CASE WHEN {$prefix}time_in IS NULL OR {$prefix}time_out IS NULL OR {$prefix}time_in = '00:00:00' OR {$prefix}time_out = '00:00:00' OR {$prefix}time_out <= {$prefix}time_in THEN 0 ELSE TIME_TO_SEC(TIMEDIFF({$prefix}time_out, {$prefix}time_in)) END";
}
function attendanceHours(string $timeIn, string $timeOut): float
{
    $pattern = '/^(?:[01][0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$/D';
    if (!preg_match($pattern, $timeIn) || !preg_match($pattern, $timeOut) || $timeIn === '00:00:00' || $timeOut <= $timeIn) {
        throw new InvalidArgumentException('La salida debe ser posterior a una entrada válida del mismo día.');
    }
    $seconds = static function ($time) { return (int) substr($time, 0, 2) * 3600 + (int) substr($time, 3, 2) * 60 + (int) substr($time, 6, 2); };
    return ($seconds($timeOut) - $seconds($timeIn)) / 3600;
}
