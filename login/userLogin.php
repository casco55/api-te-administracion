<?php
  

    function tokenLogin ($conexion,$token) {
        require('respuestas/respuestas.php');
        $consulta = "SELECT * FROM `sesion-usuario` WHERE codigoSesion = '$token' and limiteSesion > NOW()";
        $mostrar = mysqli_query($conexion,$consulta)
        or die($queryFailedJson);
        $numRows = mysqli_num_rows($mostrar);
        if ($numRows < 1) {
            return  $tokenExpiredJson;
        } else {
            
                $linea = ['type' => 'success' ,'code' => '200', 'message' => 'Login Exitoso con token', 'token' => $token];
                $respuesta = json_encode($linea);
                return $respuesta;
        }
    }


    function login ($conexion, $usuario, $password){
            require('respuestas/respuestas.php');
            $consulta = "SELECT * FROM usuarios WHERE userUsuario = '$usuario' AND claveUsuario = '$password' AND `id-rol` = 1";
            $mostrar = mysqli_query($conexion,$consulta)
            or die($queryFailedJson);
            $numRows = mysqli_num_rows($mostrar);
            if($numRows == 0){
                return $noUserJson;
            }else{
                //extracción de datos
                $extraido = $mostrar->fetch_assoc();
                $idUser = $extraido['id-usuario'];
                $user = $extraido['userUsuario'];
                $pass = $extraido['claveUsuario'];

                // generar uniqid()
                $token = uniqid('user_', true);
                // obtener dateTime
                $dt = date("Y-m-d H:i:s");
                // sumar 2 horas al dateTime
                $dt2 = date("Y-m-d H:i:s", strtotime($dt . " +2 hours"));

                $insertSession = 'INSERT INTO `sesion-usuario` (`id-usuario`, `codigoSesion`, `limiteSesion`, `registroInicioSesion`) VALUES ("'.$idUser.'", "'.$token.'", "'.$dt2.'", "'.$dt.'")';
                $insertSessionQuery = mysqli_query($conexion,$insertSession)
                or die($insertFailedJSON);
                $linea = ['type' => 'success' ,'code' => '200', 'message' => 'Login Exitoso', 'token' => $token];
                $respuesta = json_encode($linea);
                return $respuesta;
            }

            
    }
    
?>