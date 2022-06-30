
<?php
//servidor, usuario de base de datos, contraseña del usuario, nombre de base de datos
	// $conexion = new mysqli("localhost","tiempoex_desafios","Tiempo321extra","tiempoex_TiempoExtra");
	$conexion = new mysqli("localhost","root","Oriwater55.","ellavado_TiempoExtra");
	$conexion->set_charset("utf8");
	if(mysqli_connect_errno()){
		echo 'Conexion Fallida : ', mysqli_connect_error();
		exit();
	}
?>