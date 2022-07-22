<?php
    header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, X-Auth-Token, Authorization, Accept,charset,boundary,Content-Length');
    header('Access-Control-Allow-Origin: *');
    $ruta = implode("/", array_slice(explode("/", $_SERVER["REQUEST_URI"]), 1));
    $datos_put = json_decode(file_get_contents("php://input"), true);
    require ('conexion.php');
    date_default_timezone_set('America/Santiago');    
    $metodo = $_SERVER["REQUEST_METHOD"];
    


if($metodo === 'POST'){
    $tipo = $_POST['tipo'];
    switch ($tipo) {
        case 'guardarInstruccionJuegoSemanal':
            $target_path = "imagenes/instrucciones-juego-semanal/";
            $target_path2 = $target_path . basename( $_FILES['archivo']['name']); 
            $ruta_imagen = 'http://localhost/api-administracion/' . $target_path2;
            $resultadoMover = move_uploaded_file($_FILES['archivo']['tmp_name'], $target_path2);
            if($resultadoMover){
                $idJuegoSemanal = $_POST['juego'];
                $instruccionesJuegoSemanal = $_POST['descripcion'];          
                $errorInsertar = ['exito' => 'no', 'mensaje' => 'Error al insertar'];
                $mensajeErrorInsertar = json_encode($errorInsertar);
                $estado = "INACTIVO";
                $insertar = "INSERT INTO `instrucciones-juego-semanal`(`id-juego-semanal`, ImagenJuegoSemanal, instruccionesJuegoSemanal) VALUES('$idJuegoSemanal', '$ruta_imagen', '$instruccionesJuegoSemanal')";
                $guardar = mysqli_query($conexion,$insertar)
                or die($mensajeErrorInsertar);
                $mensaje = ['exito'=> 'si','mensaje' => 'Istrucción del Juego registrada con éxito'];
                $respuesta = json_encode($mensaje);
                echo $respuesta;
  
            }else{
                $errorImagen = ['exito' => 'no', 'mensaje' => 'error al cargar imagen', 'temporal' => $_FILES['archivo']['tmp_name'], 'ruta' => $target_path2, 'nombre' => $_FILES['archivo']['name'], 'error' => $_FILES['archivo']['error'], 'resultado' => $resultadoMover, 'isWritable' => is_writable($target_path)];
                $mensajeErrorIimagen = json_encode($errorImagen);
                echo $mensajeErrorIimagen;
            }
            break;

        case 'guardarVideoSemanal':
            $nombreVideoSemanal = $_POST['nombreVideo'];
            $descripcionVideoSemanal = $_POST['descripcionVideo']; 
            $target_path = "videos/video-semana/";
            $target_path = $target_path . basename( $_FILES['archivo']['name']); 
            $ruta_imagen = 'http://localhost/api-administracion/' . $target_path;
            $errorConsulta = ['exito' => 'no', 'mensaje' => 'Error al verificar'];
            $mensajeErrorConsulta = json_encode($errorConsulta);    
            $consulta = "SELECT * FROM `video-semanal` WHERE nombreVideoSemanal = '$nombreVideoSemanal'";
            $mostrar = mysqli_query($conexion,$consulta)
            or die($mensajeErrorConsulta);
            $row_cnt = mysqli_num_rows($mostrar); 
            if ($row_cnt > 0) {
                $mensaje = ['exito'=> 'no','mensaje' => 'Ya existe este video'];
                $respuesta = json_encode($mensaje);
                echo $respuesta;
            }else{
                if(move_uploaded_file($_FILES['archivo']['tmp_name'], $target_path)) {      
                    $errorInsertar = ['exito' => 'no', 'mensaje' => 'Error al insertar'];
                    $mensajeErrorInsertar = json_encode($errorInsertar);
                    $insertar = "INSERT INTO `video-semanal`(nombreVideoSemanal, descripcionVideoSemanal, linkVideoSemanal) VALUES('$nombreVideoSemanal', '$descripcionVideoSemanal', '$ruta_imagen')";
                    $guardar = mysqli_query($conexion,$insertar)
                    or die($mensajeErrorInsertar);
                    $mensaje = ['exito' => 'si', 'mensaje' => 'Video agregado en el servidor'];
                    $respuesta = json_encode($mensaje);
                    echo $respuesta;
        
                }else{
                    $errorImagen = ['exito' => 'no', 'mensaje' => 'Error al Cargar Video en el servidor'];
                    $mensajeErrorImagen = json_encode($errorImagen);
                    echo $mensajeErrorImagen;
                }
            }
            break;

        case 'guardarInstruccionSemana':
            $target_path = "imagenes/instrucciones-semana/";
            $target_path = $target_path . basename( $_FILES['archivo']['name']); 
            $ruta_imagen = 'http://localhost/api-administracion/' . $target_path;
            if(move_uploaded_file($_FILES['archivo']['tmp_name'], $target_path)) {
                $idSemana = $_POST['semana'];
                $instruccionesSemana = $_POST['descripcion'];            
                $errorInsertar = ['exito' => 'no', 'mensaje' => 'Error al insertar'];
                $mensajeErrorInsertar = json_encode($errorInsertar);
                $insertar = "INSERT INTO instrucciones(instruccionesSemana, `id-semana`, imagenSemana) VALUES('$instruccionesSemana', '$idSemana', '$ruta_imagen')";
                $guardar = mysqli_query($conexion,$insertar)
                or die($mensajeErrorInsertar);
                $mensaje = ['exito'=> 'si','mensaje' => 'Instrucción Semanal registrada con éxito'];
                $respuesta = json_encode($mensaje);
                echo $respuesta;
    
            }else{
                $errorImagen = ['exito' => 'no', 'mensaje' => 'Error al Cargar Imagen en el servidor'];
                $mensajeErrorIimagen = json_encode($errorImagen);
                echo $errorImagen;
            }
            break;
        
        case 'guardarMedalla':
            $nombreMedalla = $_POST['nombreMedalla'];  
            $target_path = "imagenes/medallas/";
            $target_path = $target_path . basename( $_FILES['archivo']['name']); 
            $ruta_imagen = 'http://localhost/api-administracion/' . $target_path;
            $errorConsulta = ['exito' => 'no', 'mensaje' => 'Error al verificar'];
            $mensajeErrorConsulta = json_encode($errorConsulta);    
            $consulta = "SELECT * FROM medallas WHERE nombreMedalla = '$nombreMedalla'";
            $mostrar = mysqli_query($conexion,$consulta)
            or die($mensajeErrorConsulta);
            $row_cnt = mysqli_num_rows($mostrar); 
            if ($row_cnt > 0) {
                $mensaje = ['exito'=> 'no','mensaje' => 'Ya existe esta Medalla'];
                $respuesta = json_encode($mensaje);
                echo $respuesta;
            }else{
                if(move_uploaded_file($_FILES['archivo']['tmp_name'], $target_path)) {     
                    $errorInsertar = ['exito' => 'no', 'mensaje' => 'Error al insertar'];
                    $mensajeErrorInsertar = json_encode($errorInsertar);
                    $insertar = "INSERT INTO medallas(nombreMedalla, linkImagenMedalla) VALUES('$nombreMedalla', '$ruta_imagen')";
                    $guardar = mysqli_query($conexion,$insertar)
                    or die($mensajeErrorInsertar);
                    $mensaje = ['exito' => 'si', 'mensaje' => 'Medalla agregada en el servidor'];
                    $respuesta = json_encode($mensaje);
                    echo $respuesta;
        
                }else{
                    $errorImagen = ['exito' => 'no', 'mensaje' => 'Error al Cargar la imagen de la medalla en el servidor'];
                    $mensajeErrorImagen = json_encode($errorImagen);
                    echo $mensajeErrorImagen;
                }
            }
            break;

        case 'editarMedalla':
            $id = $_POST['id'];
            $nombreMedalla = $_POST['nombreMedalla'];  
            $target_path = "imagenes/medallas/";
            $target_path = $target_path . basename( $_FILES['archivo']['name']); 
            $ruta_imagen = 'http://localhost/api-administracion/' . $target_path;
            $errorConsulta = ['exito' => 'no', 'mensaje' => 'Error al verificar'];
            $mensajeErrorConsulta = json_encode($errorConsulta);    
            $consulta = "SELECT * FROM medallas WHERE nombreMedalla = '$nombreMedalla' AND `id-medalla` != '$id'";
            $mostrar = mysqli_query($conexion,$consulta)
            or die($mensajeErrorConsulta);
            $row_cnt = mysqli_num_rows($mostrar); 
            if ($row_cnt > 0) {
                $mensaje = ['exito'=> 'no','mensaje' => 'Ya existe otra Medalla con este Nombre'];
                $respuesta = json_encode($mensaje);
                echo $respuesta;
            }else{
                if(move_uploaded_file($_FILES['archivo']['tmp_name'], $target_path)) {     
                    $errorInsertar = ['exito' => 'no', 'mensaje' => 'Error al Actualizar'];
                    $mensajeErrorInsertar = json_encode($errorInsertar);
                    $insertar = "UPDATE medallas SET nombreMedalla = '$nombreMedalla', linkImagenMedalla = '$ruta_imagen' WHERE `id-medalla` = '$id'";
                    $guardar = mysqli_query($conexion,$insertar)
                    or die($mensajeErrorInsertar);
                    $mensaje = ['exito' => 'si', 'mensaje' => 'Medalla actualizada en el servidor'];
                    $respuesta = json_encode($mensaje);
                    echo $respuesta;
        
                }else{
                    $errorImagen = ['exito' => 'no', 'mensaje' => 'Error al Cargar la imagen de la medalla en el servidor'];
                    $mensajeErrorImagen = json_encode($errorImagen);
                    echo $mensajeErrorImagen;
                }
            }
            break;

        case 'editarMedalla':
            $id = $_POST['id'];
            $nombreMedalla = $_POST['nombreMedalla'];  
            $target_path = "imagenes/medallas/";
            $target_path = $target_path . basename( $_FILES['archivo']['name']); 
            $ruta_imagen = 'http://localhost/api-administracion/' . $target_path;
            $errorConsulta = ['exito' => 'no', 'mensaje' => 'Error al verificar'];
            $mensajeErrorConsulta = json_encode($errorConsulta);    
            $consulta = "SELECT * FROM medallas WHERE nombreMedalla = '$nombreMedalla' AND `id-medalla` != '$id'";
            $mostrar = mysqli_query($conexion,$consulta)
            or die($mensajeErrorConsulta);
            $row_cnt = mysqli_num_rows($mostrar); 
            if ($row_cnt > 0) {
                $mensaje = ['exito'=> 'no','mensaje' => 'Ya existe otra Medalla con este Nombre'];
                $respuesta = json_encode($mensaje);
                echo $respuesta;
            }else{
                if(move_uploaded_file($_FILES['archivo']['tmp_name'], $target_path)) {     
                    $errorInsertar = ['exito' => 'no', 'mensaje' => 'Error al Actualizar'];
                    $mensajeErrorInsertar = json_encode($errorInsertar);
                    $insertar = "UPDATE medallas SET nombreMedalla = '$nombreMedalla', linkImagenMedalla = '$ruta_imagen' WHERE `id-medalla` = '$id'";
                    $guardar = mysqli_query($conexion,$insertar)
                    or die($mensajeErrorInsertar);
                    $mensaje = ['exito' => 'si', 'mensaje' => 'Medalla actualizada en el servidor'];
                    $respuesta = json_encode($mensaje);
                    echo $respuesta;
                }else{
                    $errorImagen = ['exito' => 'no', 'mensaje' => 'Error al Cargar la imagen de la medalla en el servidor'];
                    $mensajeErrorImagen = json_encode($errorImagen);
                    echo $mensajeErrorImagen;
                }
            }
            break;

        
        default:
            
            break;
    }
}else if($metodo === 'PUT'){

    switch ($tipo) {
        
        

        
        default:
            echo 'no llega';
            break;

    }

}




?>