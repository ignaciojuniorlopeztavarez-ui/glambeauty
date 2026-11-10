<?php
	require 'conexion.php';
	
	$nombre = $_POST['nombre'];
	$email = $_POST['email'];
	$telefono = $_POST['telefono'];
	$estado_civil = $_POST['estado_civil'];
	$hijos = isset($_POST['hijos']) ? $_POST['hijos'] : 0;
	$intereses = isset($_POST['intereses']) ? $_POST['intereses'] : null;
	$id_estado = isset($_POST['id_estado']) ? $_POST['id_estado'] : 0;
	
	$arrayIntereses = null;
	$num_array = is_array($intereses) ? count($intereses) : 0;
	$contador = 0;
	
	if($num_array > 0){
		foreach ($intereses as $key => $value) {
			if ($contador != $num_array - 1)
				$arrayIntereses .= $value.' ';
			else
				$arrayIntereses .= $value;
			$contador++;
		}
	}
	
	$sql = "INSERT INTO personas (nombre, correo, telefono, estado_civil, hijos, intereses, id_estado) VALUES ('$nombre', '$email', '$telefono', '$estado_civil', '$hijos', '$arrayIntereses', '$id_estado')";
	$resultado = $mysqli->query($sql);
	$id_insert = $mysqli->insert_id;

	if(isset($_FILES["Archivo"]) && $_FILES["Archivo"]["name"] != "") {

		if($_FILES["Archivo"]["error"] > 0) {
			echo "Error al cargar el archivo.";
		} else {
			$permitidos = array("image/gif", "image/png", "image/jpg", "image/jpeg", "application/pdf");
			$Limite_kb = 400;

			if(in_array($_FILES["Archivo"]["type"], $permitidos) && $_FILES["Archivo"]["size"] <= $Limite_kb * 1024) {

				$ruta = "files/" . $id_insert . '/';
				$archivo = $ruta . $_FILES["Archivo"]["name"];

				if(!file_exists($ruta)){
					mkdir($ruta, 0777, true);
				}

				if(!file_exists($archivo)){
					$resultado_archivo = @move_uploaded_file($_FILES["Archivo"]["tmp_name"], $archivo);

					if($resultado_archivo) {
						$nombre_imagen = $_FILES["Archivo"]["name"];
						$sql_img = "UPDATE personas SET imagen = '$nombre_imagen' WHERE id = '$id_insert'";
						$mysqli->query($sql_img);
					} 
				} 
			} 
		} 
	}
?>
<html lang="es">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>Guardar Registro</title>
		<link href="css/bootstrap.min.css" rel="stylesheet">
		<link href="css/bootstrap-theme.css" rel="stylesheet">
		<script src="js/jquery-3.1.1.min.js"></script>
		<script src="js/bootstrap.min.js"></script>	
	</head>
	
	<body>
		<div class="container" style="margin-top: 50px;">
			<div class="row">
				<div class="row" style="text-align:center">
					<?php if($resultado) { ?>
						<h3 class="text-success">REGISTRO GUARDADO</h3>
					<?php } else { ?>
						<h3 class="text-danger">ERROR AL GUARDAR</h3>
					<?php } ?>
					<br>
					<a href="index.php" class="btn btn-primary">Regresar</a>
				</div>
			</div>
		</div>
	</body>
</html>