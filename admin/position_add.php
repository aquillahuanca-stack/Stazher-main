<?php
	include 'includes/session.php';

	if(isset($_POST['add'])){
		$title = $_POST['title'];

		$sql = "INSERT INTO position (description) VALUES ('$title')";
		if($conn->query($sql)){
			$_SESSION['success'] = 'Position added successfully';
		}
		else{
			$_SESSION['error'] = $conn->error;
		}
	}	


	header('location: position.php');

?>