<?php
	$mysqli = new mysqli("localhost", "root", "IJ010910", "personal"); 
	
	if(mysqli_connect_errno()){
		echo 'Conexion Fallida : ', mysqli_connect_error();
		exit();
	}
?>
