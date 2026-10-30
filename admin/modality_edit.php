<?php
	include 'includes/session.php';

	if(isset($_POST['edit'])){
		$id = $_POST['id'];
		$type_modality = $_POST['type_modality'];
		$sql = "UPDATE modality SET type_modality = '$type_modality' WHERE id = '$id'";
		if($conn->query($sql)){
			$_SESSION['success'] = 'Modalidad Actualizada Satisfactoriamente';
		}
		else{
			$_SESSION['error'] = $conn->error;
		}
	}

	header('location:modality.php');

?>