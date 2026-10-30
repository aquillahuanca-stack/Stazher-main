<?php
function employeeInput(mysqli $conn, float $defaultHours): array
{
    $keys = array('firstname', 'lastname', 'identity_card', 'email', 'address', 'birthdate', 'contact', 'gender', 'position', 'schedule', 'career', 'area', 'institution', 'modality');
    $data = array();
    foreach ($keys as $key) {
        if (!is_string($_POST[$key] ?? null) || trim($_POST[$key]) === '') { throw new InvalidArgumentException('Complete todos los campos obligatorios del pasante.'); }
        $data[$key] = trim($_POST[$key]);
    }
    if (mb_strlen($data['firstname']) > 50 || mb_strlen($data['lastname']) > 50 || strlen($data['email']) > 50 || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        throw new InvalidArgumentException('Revise el nombre, apellido y correo electrónico.');
    }
    if (!ctype_digit($data['identity_card']) || (float) $data['identity_card'] < 1 || (float) $data['identity_card'] > 2147483647) {
        throw new InvalidArgumentException('Ingrese un carnet numérico válido.');
    }
    $date = DateTimeImmutable::createFromFormat('!Y-m-d', $data['birthdate']);
    if (!$date || $date->format('Y-m-d') !== $data['birthdate'] || $data['birthdate'] < '1000-01-01' || $data['birthdate'] > date('Y-m-d')) {
        throw new InvalidArgumentException('Ingrese una fecha de nacimiento válida.');
    }
    if (!in_array($data['gender'], array('Male', 'Female'), true) || strlen($data['contact']) > 100) { throw new InvalidArgumentException('Revise el género y el teléfono.'); }
    foreach (array('position' => 'position', 'schedule' => 'schedules', 'career' => 'career', 'area' => 'area', 'institution' => 'institution', 'modality' => 'modality') as $key => $table) {
        if (!ctype_digit($data[$key]) || (int) $data[$key] < 1) { throw new InvalidArgumentException('Seleccione un área, horario y catálogos válidos.'); }
        $stmt = $conn->prepare("SELECT id FROM $table WHERE id = ?");
        $stmt->bind_param('s', $data[$key]);
        $stmt->execute();
        $found = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        if (!$found) { throw new InvalidArgumentException('Una de las opciones seleccionadas ya no está disponible. Recargue la página.'); }
    }
    $required = $_POST['required_hours'] ?? $defaultHours;
    // El esquema actual usa TIME: su límite es 838:59:59.
    if (!is_scalar($required) || !is_numeric($required) || (float) $required < 0 || (float) $required > 838 + 59 / 60) {
        throw new InvalidArgumentException('Las horas requeridas deben estar entre 0 y 838,98 con el esquema actual.');
    }
    $minutes = (int) round((float) $required * 60);
    $data['add_hr'] = sprintf('%d:%02d:00', intdiv($minutes, 60), $minutes % 60);
    return $data;
}
