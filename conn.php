<?php
require_once __DIR__ . '/timezone.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
try {
    $conn = new mysqli(getenv('STAZHER_DB_HOST') ?: 'localhost', getenv('STAZHER_DB_USER') ?: 'root', getenv('STAZHER_DB_PASSWORD') ?: '', getenv('STAZHER_DB_NAME') ?: 'stazher');
    $conn->set_charset('utf8mb4');
    $conn->query("SET time_zone = '-04:00'");
} catch (mysqli_sql_exception $exception) {
    error_log('STAZHER: error de conexión a la base de datos.');
    http_response_code(503);
    exit('La base de datos no está disponible. Intente nuevamente.');
}
