<?php
/**
 * Fuente paginada para la tabla de asistencias.
 * Devuelve solamente la página solicitada por DataTables.
 */
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
header('Content-Type: application/json; charset=utf-8');

if (empty($_SESSION['admin']) || !ctype_digit((string) $_SESSION['admin'])) {
    http_response_code(401);
    echo json_encode(array('error' => 'Sesión no válida.'));
    exit;
}

require_once __DIR__ . '/includes/conn.php';

function attendanceDataInt($value, int $default, int $minimum, int $maximum): int
{
    $number = filter_var($value, FILTER_VALIDATE_INT);
    if ($number === false) {
        return $default;
    }
    return max($minimum, min($maximum, (int) $number));
}

function attendanceDataDate($value): string
{
    if (!is_string($value) || !preg_match('/^\d{4}-\d{2}-\d{2}$/D', $value)) {
        return '';
    }
    list($year, $month, $day) = array_map('intval', explode('-', $value));
    return checkdate($month, $day, $year) ? $value : '';
}

function attendanceDataBind(mysqli_stmt $statement, string $types, array &$parameters): void
{
    if ($types === '') {
        return;
    }
    $arguments = array($types);
    foreach ($parameters as $index => &$parameter) {
        $arguments[] = &$parameter;
    }
    call_user_func_array(array($statement, 'bind_param'), $arguments);
}

function attendanceDataEscape($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function attendanceDataTime($value): string
{
    return $value && $value !== '00:00:00' ? date('h:i A', strtotime($value)) : '';
}

$draw = attendanceDataInt($_GET['draw'] ?? null, 0, 0, PHP_INT_MAX);
$start = attendanceDataInt($_GET['start'] ?? null, 0, 0, PHP_INT_MAX);
$length = attendanceDataInt($_GET['length'] ?? null, 25, 10, 100);
$dateFrom = attendanceDataDate($_GET['date_from'] ?? null);
$dateTo = attendanceDataDate($_GET['date_to'] ?? null);
$employee = is_string($_GET['employee'] ?? null) ? trim($_GET['employee']) : '';
if (mb_strlen($employee) > 100 || preg_match('/[\x00-\x1F\x7F]/u', $employee)) {
    $employee = '';
}

$where = array("a.time_in IS NOT NULL", "a.time_in <> '00:00:00'");
$types = '';
$parameters = array();
if ($dateFrom !== '') {
    $where[] = 'a.date >= ?';
    $types .= 's';
    $parameters[] = $dateFrom;
}
if ($dateTo !== '') {
    $where[] = 'a.date <= ?';
    $types .= 's';
    $parameters[] = $dateTo;
}
if ($employee !== '') {
    $where[] = "(e.firstname LIKE ? OR e.lastname LIKE ? OR e.employee_id LIKE ? OR CONCAT(e.firstname, ' ', e.lastname) LIKE ? )";
    $types .= 'ssss';
    $like = '%' . $employee . '%';
    array_push($parameters, $like, $like, $like, $like);
}
$whereSql = implode(' AND ', $where);

$totalResult = $conn->query("SELECT COUNT(*) AS total FROM attendance a WHERE a.time_in IS NOT NULL AND a.time_in <> '00:00:00'");
$recordsTotal = (int) ($totalResult->fetch_assoc()['total'] ?? 0);

$countStatement = $conn->prepare("SELECT COUNT(*) AS total FROM attendance a JOIN employees e ON e.id = a.employee_id WHERE {$whereSql}");
$countParameters = $parameters;
attendanceDataBind($countStatement, $types, $countParameters);
$countStatement->execute();
$recordsFiltered = (int) ($countStatement->get_result()->fetch_assoc()['total'] ?? 0);
$countStatement->close();

$orderColumn = attendanceDataInt($_GET['order'][0]['column'] ?? null, 1, 0, 6);
$orderDirection = (($_GET['order'][0]['dir'] ?? '') === 'asc') ? 'ASC' : 'DESC';
$orders = array(
    0 => "e.lastname {$orderDirection}, e.firstname {$orderDirection}",
    1 => "a.date {$orderDirection}, a.time_in {$orderDirection}",
    2 => "a.time_in {$orderDirection}",
    3 => "a.time_out {$orderDirection}",
);
$orderSql = $orders[$orderColumn] ?? 'a.date DESC, a.time_in DESC';

$query = "SELECT a.id AS attid, a.date, a.time_in, a.time_out, a.status2, e.employee_id, e.firstname, e.lastname
          FROM attendance a
          JOIN employees e ON e.id = a.employee_id
          WHERE {$whereSql}
          ORDER BY {$orderSql}
          LIMIT ? OFFSET ?";
$statement = $conn->prepare($query);
$dataParameters = $parameters;
$dataParameters[] = $length;
$dataParameters[] = $start;
attendanceDataBind($statement, $types . 'ii', $dataParameters);
$statement->execute();
$result = $statement->get_result();
$data = array();
while ($row = $result->fetch_assoc()) {
    $timeIn = (string) $row['time_in'];
    $timeOut = (string) $row['time_out'];
    $hasExit = $timeOut !== '' && $timeOut !== '00:00:00';
    $seconds = 0;
    if ($hasExit && $timeOut > $timeIn) {
        $seconds = strtotime($timeOut) - strtotime($timeIn);
    }
    $hours = intdiv($seconds, 3600);
    $minutes = intdiv($seconds % 3600, 60);
    $name = attendanceDataEscape($row['firstname'] . ' ' . $row['lastname']);
    $timeOutText = attendanceDataTime($timeOut);
    if ($hasExit && (int) $row['status2'] === 3) {
        $timeOutText .= '<br><small class="text-muted">Según horario asignado</small>';
    }
    $status = $hasExit
        ? '<span class="label label-success">Presente</span>'
        : '<span class="label label-warning">Omisión</span>';
    $id = (int) $row['attid'];
    $data[] = array(
        'employee' => $name,
        'date' => attendanceDataEscape(date('d/m/Y', strtotime($row['date']))),
        'time_in' => attendanceDataEscape(attendanceDataTime($timeIn)),
        'time_out' => $timeOutText,
        'hours' => $hours . ' h ' . str_pad((string) $minutes, 2, '0', STR_PAD_LEFT) . ' min',
        'status' => $status,
        'actions' => '<span class="attendance-actions-cell"><button class="attendance-action-btn edit" data-id="' . $id . '" title="Editar asistencia" aria-label="Editar asistencia"><i class="fa fa-pencil"></i></button><button class="attendance-action-btn delete" data-id="' . $id . '" title="Eliminar asistencia" aria-label="Eliminar asistencia"><i class="fa fa-trash"></i></button></span>'
    );
}
$statement->close();

echo json_encode(array(
    'draw' => $draw,
    'recordsTotal' => $recordsTotal,
    'recordsFiltered' => $recordsFiltered,
    'data' => $data
), JSON_UNESCAPED_UNICODE);
