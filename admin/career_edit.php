<?php
	include 'includes/session.php';

	if(isset($_POST['edit'])){
		$id = $_POST['id'];
		$name_career = $_POST['name_career'];

		$sql = "UPDATE career SET name_career = '$name_career' WHERE id = '$id'";
		if($conn->query($sql)){
			$_SESSION['success'] = 'Carrera Universitaria Actualizada Satisfactoriamente';
		}
		else{
			$_SESSION['error'] = $conn->error;
		}
	}

	header('location:career.php');

?>