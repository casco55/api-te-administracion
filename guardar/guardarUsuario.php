<?php
    function guardarUsuario($conexion,$rutUsuario, $nombreUsuario, $apellidoUsuario, $mailUsuario, $fonoUsuario, $userUsuario, $claveUsuario, $idRol, $sexoUsuario, $fechaNacimientoUsuario) {

        $errorConsulta = ['exito' => 'no', 'mensaje' => 'Error al verificar'];
        $mensajeErrorConsulta = json_encode($errorConsulta); 
        $consulta = "SELECT * FROM  usuarios WHERE rutUsuario = '$rutUsuario'";
        $mostrar = mysqli_query($conexion,$consulta)
        or die($mensajeErrorConsulta);
        $row_cnt = mysqli_num_rows($mostrar);
        if ($row_cnt > 0) {
            $mensaje = ['exito'=> 'no','mensaje' => 'Ya existe este usuario'];
            $respuesta = json_encode($mensaje);
            return $respuesta;
        } else {
            $errorInsertar = ['exito' => 'no', 'mensaje' => 'Error al insertar a'];
            $mensajeErrorInsertar = json_encode($errorInsertar);
            if ($fechaNacimientoUsuario == '') {
                $insertar = "INSERT INTO usuarios(rutUsuario, nombreUsuario, apellidoUsuario, mailUsuario, fonoUsuario, userUsuario, claveUsuario, `id-rol`, sexoUsuario, fechaCreacionUsuario) VALUES('$rutUsuario', '$nombreUsuario', '$apellidoUsuario', '$mailUsuario', '$fonoUsuario', '$userUsuario', '$claveUsuario', '$idRol', '$sexoUsuario', curdate())";
                $guardar = mysqli_query($conexion,$insertar)
                or die($mensajeErrorInsertar);
                
                $mensaje = ['exito'=> 'si','mensaje' => 'Usuario Registrado con éxito'];
                $respuesta = json_encode($mensaje);
                return $respuesta;
            }else {
                $insertar = "INSERT INTO usuarios(rutUsuario, nombreUsuario, apellidoUsuario, mailUsuario, fonoUsuario, userUsuario, claveUsuario, `id-rol`, sexoUsuario, fechaNacimientoUsuario, fechaCreacionUsuario) VALUES('$rutUsuario', '$nombreUsuario', '$apellidoUsuario', '$mailUsuario', '$fonoUsuario', '$userUsuario', '$claveUsuario', '$idRol', '$sexoUsuario', '$fechaNacimientoUsuario', curdate())";
                $guardar = mysqli_query($conexion,$insertar)
                or die($mensajeErrorInsertar);
                
                $mensaje = ['exito'=> 'si','mensaje' => 'Usuario Registrado con éxito'];
                $respuesta = json_encode($mensaje);
                return $respuesta;
            }
            
        }
    }
?>