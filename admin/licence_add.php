<?php
include 'includes/session.php';

if(isset($_POST['add'])){
    $reason = $_POST['reason'];
    $date_licence = $_POST['date_licence'];
    $id = $_POST['employee_id'];

    $sql = "INSERT INTO licence (reason, date_licence, employee_id)
            VALUES ('$reason', '$date_licence', (SELECT id FROM employees WHERE employee_id = '$id'))";

    if ($conn->query($sql)) {
        $_SESSION['success'] = 'Licencia creada exitosamente';
    } else {
        $_SESSION['error'] = 'Rellene el formulario de edición primero';
    }
}

header('location: licence.php');
?>




