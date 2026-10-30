<?php
function buildAttendanceCalendar(array $employees, array $records, DateTimeImmutable $start, DateTimeImmutable $end) {
    $byEmployee = array();
    foreach ($records as $record) {
        $byEmployee[$record['employee_id']][$record['date']][] = $record;
    }
    $rows = array();
    for ($date = $end; $date >= $start; $date = $date->modify('-1 day')) {
        if ((int) $date->format('N') > 5) { continue; }
        $day = $date->format('Y-m-d');
        foreach ($employees as $employee) {
            $begin = $employee['created_on'];
            if (!empty($employee['first_date']) && (!$begin || $begin === '0000-00-00' || $employee['first_date'] < $begin)) {
                $begin = $employee['first_date'];
            }
            if (!$begin || $begin === '0000-00-00' || $day < $begin) { continue; }
            $daily = $byEmployee[$employee['id']][$day] ?? array(array('attid' => null, 'date' => $day, 'time_in' => null, 'time_out' => null, 'status2_label' => ''));
            foreach ($daily as $record) {
                $rows[] = array_merge($employee, $record);
            }
        }
    }
    return $rows;
}