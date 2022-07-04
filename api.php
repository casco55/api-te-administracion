<?php
    ini_set('display_errors', '1');
    ini_set('error_reporting', E_ALL);
?>
<?php

    header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, X-Auth-Token, Authorization, Accept,charset,boundary,Content-Length');
    header('Access-Control-Allow-Origin: *');
    
    $ruta = implode("/", array_slice(explode("/", $_SERVER["REQUEST_URI"]), 3));
    $datos = json_decode(file_get_contents("php://input"));
    $datos_post = json_decode(file_get_contents("php://input"), true);
    require ('conexion.php');
    include 'login/userLogin.php';
    include 'guardar/guardarUsuario.php';
    date_default_timezone_set('America/Santiago');
    $metodo = $_SERVER["REQUEST_METHOD"];

    if($metodo === 'GET'){
        $tipo = $_GET['type'];        
        switch ($tipo) {
            case 'tokenLogin':
                $token = $_GET['token'];
                $respuesta = tokenLogin($conexion, $token);
                echo $respuesta;
                break;
            case 'login':
                $usuario = $_GET['user'];
                $password = $_GET['pass'];    
                $respuesta = login($conexion, $usuario, $password);
                echo $respuesta;
                break;            
            case 'regiones':
                $lista_regiones = [];
                $consulta = "SELECT * FROM regiones";
                $mostrar = mysqli_query($conexion,$consulta)
                or die("error al traer los datos");
                while ($extraido = mysqli_fetch_array($mostrar)){
                    $linea = ['value' => $extraido['region'], 'label' => $extraido['region']];
                    array_push($lista_regiones, $linea);
                }
                $listado_json = json_encode($lista_regiones);
                echo $listado_json;
                break;
            
            case 'comunas':
                $lista_comunas = [];
                $consulta = "SELECT * FROM comunas ORDER BY comuna asc";
                $mostrar = mysqli_query($conexion,$consulta)
                or die("error al traer los datos");
                while ($extraido = mysqli_fetch_array($mostrar)){
                    $linea = ['value' => $extraido['id'], 'label' => $extraido['comuna']];
                    array_push($lista_comunas, $linea);
                }
                $listado_json = json_encode($lista_comunas);
                echo $listado_json;
                break;

            case 'corporaciones':
                $lista_corporaciones = [];
                $consulta = "SELECT * FROM corporaciones ORDER BY nombreCorporacion asc";
                $mostrar = mysqli_query($conexion,$consulta)
                or die("error al traer los datos");
                while ($extraido = mysqli_fetch_array($mostrar)){
                    $opcion = $extraido['rutCorporacion'] .' - '.$extraido['nombreCorporacion'];
                    $linea = ['value' => $extraido['id-corporacion'], 'label' => $opcion];
                    array_push($lista_corporaciones, $linea);
                }
                $listado_json = json_encode($lista_corporaciones);
                echo $listado_json;
                break;

            case 'corporacion':
                $idCorp = $_GET['id'];
                $consulta = "SELECT * FROM corporaciones WHERE `id-corporacion`  ='$idCorp'";
                $mostrar = mysqli_query($conexion,$consulta)
                or die("error al traer los datos");
                $extraido = mysqli_fetch_array($mostrar);
                $respuesta = ['idCorporacion' => $extraido['id-corporacion'], 'rutCorporacion' => $extraido['rutCorporacion'], 'nombreCorporacion' => $extraido['nombreCorporacion']];
                $respuesta_json = json_encode($respuesta);
                echo $respuesta_json;
                break;
            
            case 'instituciones':
                $lista_instituciones = [];
                $consulta = "SELECT * FROM institucion ORDER BY nombreInstitucion asc";
                $mostrar = mysqli_query($conexion,$consulta)
                or die("error al traer los datos");
                while ($extraido = mysqli_fetch_array($mostrar)){
                    $opcion = $extraido['rbdInstitucion'] .' - '.$extraido['nombreInstitucion'];
                    $linea = ['value' => $extraido['id-institucion'], 'label' => $opcion];
                    array_push($lista_instituciones, $linea);
                }
                $listado_json = json_encode($lista_instituciones);
                echo $listado_json;
                break;

            case 'institucion':
                $idInst = $_GET['id'];
                $consulta = "SELECT * FROM institucion WHERE `id-institucion`  ='$idInst'";
                $mostrar = mysqli_query($conexion,$consulta)
                or die("error al traer los datos");
                $extraido = mysqli_fetch_array($mostrar);
                $idComuna = $extraido['id-comuna'];
                $consultaComuna = "SELECT * from comunas WHERE id = '$idComuna'";
                $mostrarComuna = mysqli_query($conexion,$consultaComuna)
                or die("error al traer la comuna");
                $extraidoComuna = mysqli_fetch_array($mostrarComuna);
                $nombreComuna = $extraidoComuna['comuna'];
                $respuesta = ['idInstitucion' => $extraido['id-institucion'], 'rbdInstitucion' => $extraido['rbdInstitucion'], 'nombreInstitucion' => $extraido['nombreInstitucion'], 'comuna' => $idComuna, 'nombreComuna' => $nombreComuna, 'calleInstitucion' => $extraido['calleInstitucion'], 'numeroInstitucion' => $extraido['numeroInstitucion'], 'fonoInstitucion' => $extraido['fonoInstitucion'], 'mailInstitucion' => $extraido['mailInstitucion'], 'directorInstitucion' => $extraido['nombreDirector'], 'cursosNivelInstitucion' => $extraido['cursosPorNIvel'], 'alumnosNivelInstitucion' => $extraido['alumnosPorNivel'], 'tipoInstitucion' => $extraido['tipoInstitucion'], 'tipoUbicacionInstitucion' => $extraido['tipoUbicacionInstitucion']];
                $respuesta_json = json_encode($respuesta);
                echo $respuesta_json;
                break;

            case 'niveles':
                $lista_niveles = [];
                $consulta = "SELECT * FROM nivel JOIN institucion ON nivel.`id-institucion` = institucion.`id-institucion` ORDER BY nombreInstitucion asc";
                $mostrar = mysqli_query($conexion,$consulta)
                or die("error al traer los datos");                
                while ($extraido = mysqli_fetch_array($mostrar)){
                    $opcion = $extraido['nombreInstitucion'] .' - '.$extraido['nombreNivel'] .' - '.$extraido['temporada'];
                    $linea = ['value' => $extraido['id-nivel'], 'label' => $opcion];
                    array_push($lista_niveles, $linea);
                }
                $listado_json = json_encode($lista_niveles);
                echo $listado_json;
                break;

            case 'nivel':
                $idNivel = $_GET['id'];
                $consulta = "SELECT * FROM nivel JOIN institucion ON nivel.`id-institucion` = institucion.`id-institucion` WHERE nivel.`id-nivel`  ='$idNivel'";
                $mostrar = mysqli_query($conexion,$consulta)
                or die("error al traer los datos");
                $extraido = mysqli_fetch_array($mostrar);
                $respuesta = ['id' => $extraido['id-nivel'], 'nombreNivel' => $extraido['nombreNivel'], 'cantidadAlumnosNivel' => $extraido['cantidadAlumnos'], 'temporadaNivel' => $extraido['temporada'], 'idInstitucion' => $extraido['id-institucion'], 'nombreInstitucion' => $extraido['nombreInstitucion']];
                $respuesta_json = json_encode($respuesta);
                echo $respuesta_json;
                break;
            
            case 'roles':
                $lista_roles = [];
                $consulta = "SELECT * FROM rol ORDER BY `id-rol` asc";
                $mostrar = mysqli_query($conexion,$consulta)
                or die("error al traer los datos");
                while ($extraido = mysqli_fetch_array($mostrar)){
                    $linea = ['value' => $extraido['id-rol'], 'label' => $extraido['nombreRol']];
                    array_push($lista_roles, $linea);
                }
                $listado_json = json_encode($lista_roles);
                echo $listado_json;
                break;

            case 'usuarios':
                $lista_usuarios = [];
                $consulta = "SELECT * FROM usuarios ORDER BY `nombreUsuario` asc";
                $mostrar = mysqli_query($conexion,$consulta)
                or die("error al traer los datos");
                while ($extraido = mysqli_fetch_array($mostrar)){
                    $opcion = $extraido['rutUsuario'] .' - '.$extraido['nombreUsuario'];
                    $linea = ['value' => $extraido['id-usuario'], 'label' => $opcion];
                    array_push($lista_usuarios, $linea);
                }
                $listado_json = json_encode($lista_usuarios);
                echo $listado_json;
                break;

            case 'usuario':
                $idUser = $_GET['id'];
                $consulta = "SELECT * FROM usuarios JOIN rol ON usuarios.`id-rol` = rol.`id-rol` WHERE `id-usuario` = '$idUser'";
                $mostrar = mysqli_query($conexion, $consulta)
                or die("error al traer los datos");
                $extraido = mysqli_fetch_array($mostrar);
                $respuesta = ['idUsuario' => $extraido['id-usuario'], 'rutUsuario' => $extraido['rutUsuario'], 'nombreUsuario' => $extraido['nombreUsuario'], 'apellidoUsuario' => $extraido['apellidoUsuario'], 'mailUsuario' => $extraido['mailUsuario'], 'fonoUsuario' => $extraido['fonoUsuario'], 'userUsuario' => $extraido['userUsuario'], 'claveUsuario' => $extraido['claveUsuario'], 'idRol' => $extraido['id-rol'], 'labelRol' => $extraido['nombreRol'], 'sexoUsuario' => $extraido['sexoUsuario'], 'fechaNacimientoUsuario' => $extraido['fechaNacimientoUsuario']];
                $respuesta_json = json_encode($respuesta);
                echo $respuesta_json;
                break;

            case 'minijuegos':
                $lista_minijuegos = [];
                $consulta = "SELECT * FROM minijuegos ORDER BY nombreMinijuego asc";
                $mostrar = mysqli_query($conexion,$consulta)
                or die("error al traer los datos");
                while ($extraido = mysqli_fetch_array($mostrar)){
                    $linea = ['value' => $extraido['id-minijuego'], 'label' => $extraido['nombreMinijuego']];
                    array_push($lista_minijuegos, $linea);
                }
                $listado_json = json_encode($lista_minijuegos);
                echo $listado_json;
                break;

            case 'minijuego':
                $idMinijuego = $_GET['id'];
                $consulta = "SELECT * FROM minijuegos WHERE `id-minijuego`  ='$idMinijuego'";
                $mostrar = mysqli_query($conexion,$consulta)
                or die("error al traer los datos");
                $extraido = mysqli_fetch_array($mostrar);
                $respuesta = ['id' => $extraido['id-minijuego'], 'codigoMinijuego' => $extraido['codigoMinijuego'], 'nombreMinijuego' => $extraido['nombreMinijuego']];
                $respuesta_json = json_encode($respuesta);
                echo $respuesta_json;
                break;

            case 'palabras':
                $lista_palabras = [];
                $consulta = "SELECT * FROM `palabra-semanal` ORDER BY nombrePalabraSemanal asc";
                $mostrar = mysqli_query($conexion,$consulta)
                or die("error al traer los datos");
                while ($extraido = mysqli_fetch_array($mostrar)){
                    $linea = ['value' => $extraido['id-palabra-semanal'], 'label' => $extraido['nombrePalabraSemanal']];
                    array_push($lista_palabras, $linea);
                }
                $listado_json = json_encode($lista_palabras);
                echo $listado_json;
                break;
            
            case 'palabra':
                $idPalabraSemanal = $_GET['id'];
                $consulta = "SELECT * FROM `palabra-semanal` WHERE `id-palabra-semanal`  ='$idPalabraSemanal'";
                $mostrar = mysqli_query($conexion,$consulta)
                or die("error al traer los datos");
                $extraido = mysqli_fetch_array($mostrar);
                $respuesta = ['id' => $extraido['id-palabra-semanal'], 'nombrePalabraSemanal' => $extraido['nombrePalabraSemanal']];
                $respuesta_json = json_encode($respuesta);
                echo $respuesta_json; 
                break;

            case 'trivias':
                $lista_trivias = [];
                $consulta = "SELECT * FROM trivias ORDER BY nombreTrivia asc";
                $mostrar = mysqli_query($conexion,$consulta)
                or die("error al traer los datos");
                while ($extraido = mysqli_fetch_array($mostrar)){
                    $linea = ['value' => $extraido['id-trivia'], 'label' => $extraido['nombreTrivia']];
                    array_push($lista_trivias, $linea);
                }
                $listado_json = json_encode($lista_trivias);
                echo $listado_json;
                break;

            case 'trivia':
                $idTrivia = $_GET['id'];
                $consulta = "SELECT * FROM trivias WHERE `id-trivia`  ='$idTrivia'";
                $mostrar = mysqli_query($conexion,$consulta)
                or die("error al traer los datos");
                $extraido = mysqli_fetch_array($mostrar);
                $respuesta = ['id' => $extraido['id-trivia'], 'nombreTrivia' => $extraido['nombreTrivia'], 'descripcionTrivia' => $extraido['descripcionTrivia']];
                $respuesta_json = json_encode($respuesta);
                echo $respuesta_json; 
                break;
            
            case 'preguntas':
                $lista_preguntas = [];
                $consulta = "SELECT * FROM preguntas ORDER BY pregunta asc";
                $mostrar = mysqli_query($conexion,$consulta)
                or die("error al traer los datos");
                while ($extraido = mysqli_fetch_array($mostrar)){
                    $linea = ['value' => $extraido['id-pregunta'], 'label' => $extraido['pregunta']];
                    array_push($lista_preguntas, $linea);
                }
                $listado_json = json_encode($lista_preguntas);
                echo $listado_json;
                break;

            case 'pregunta':
                $idPregunta = $_GET['id'];
                $consulta = "SELECT * FROM preguntas WHERE `id-pregunta`  ='$idPregunta'";
                $mostrar = mysqli_query($conexion,$consulta)
                or die("error al traer los datos");
                $extraido = mysqli_fetch_array($mostrar);
                $respuesta = ['id' => $extraido['id-pregunta'], 'codigoPregunta' => $extraido['codigoPregunta'], 'nombrePregunta' => $extraido['pregunta']];
                $respuesta_json = json_encode($respuesta);
                echo $respuesta_json; 
                break;

            case 'asociacionTriviaPreguntas':
                $idTrivia = $_GET['id'];
                $lista_asociacion_trivia = [];
                $consulta = "SELECT * FROM `preguntas-trivias` JOIN preguntas ON `preguntas-trivias`.`id-pregunta` = preguntas.`id-pregunta` JOIN trivias ON `preguntas-trivias`.`id-trivia` = trivias.`id-trivia` WHERE trivias.`id-trivia` = '$idTrivia'";
                $mostrar = mysqli_query($conexion,$consulta)
                or die("error al traer los datos");
                while ($extraido = mysqli_fetch_array($mostrar)){
                    $linea = ['id' => $extraido['id-pregunta-trivia'], 'nombrePregunta' => $extraido['pregunta'], 'nombreTrivia' => $extraido['nombreTrivia']];
                    array_push($lista_asociacion_trivia, $linea);
                }
                $listado_json = json_encode($lista_asociacion_trivia);
                echo $listado_json;
                break;
            
            case 'juegos':
                $lista_juegos = [];
                $consulta = "SELECT * FROM `juego-semanal` ORDER BY nombreJuegoSemanal asc";
                $mostrar = mysqli_query($conexion,$consulta)
                or die("error al traer los datos");
                while ($extraido = mysqli_fetch_array($mostrar)){
                    $linea = ['value' => $extraido['id-juego-semanal'], 'label' => $extraido['nombreJuegoSemanal']];
                    array_push($lista_juegos, $linea);
                }
                $listado_json = json_encode($lista_juegos);
                echo $listado_json;
                break;
            
            case 'juego':
                $idJuegoSemanal = $_GET['id'];
                $consulta = "SELECT * FROM `juego-semanal` JOIN trivias ON `juego-semanal`.`id-trivia` = trivias.`id-trivia` JOIN `palabra-semanal` ON `juego-semanal`.`id-palabra-semanal` = `palabra-semanal`.`id-palabra-semanal` WHERE `id-juego-semanal`  ='$idJuegoSemanal'";
                $mostrar = mysqli_query($conexion,$consulta)
                or die("error al traer los datos");
                $extraido = mysqli_fetch_array($mostrar);
                $respuesta = ['id' => $extraido['id-juego-semanal'], 'nombreJuegoSemanal' => $extraido['nombreJuegoSemanal'], 'metaPuntaje' => $extraido['metaPuntaje'], 'idTrivia' => $extraido['id-trivia'], 'labelTrivia' => $extraido['nombreTrivia'], 'idPalabraSemanal' => $extraido['id-palabra-semanal'], 'labelPalabraSemanal' => $extraido['nombrePalabraSemanal']];
                $respuesta_json = json_encode($respuesta);
                echo $respuesta_json; 
                break;

            case 'asociacionJuegoSemanalMinijuego':
                $idJuegoSemanal = $_GET['id'];
                $lista_asociacion_juegoSemanal = [];
                $consulta = "SELECT * FROM `minijuegos-juego-semanal` JOIN minijuegos ON `minijuegos-juego-semanal`.`id-minijuego` = minijuegos.`id-minijuego` JOIN `juego-semanal` ON `minijuegos-juego-semanal`.`id-juego-semanal` =  `juego-semanal`.`id-juego-semanal` WHERE `juego-semanal`.`id-juego-semanal` = '$idJuegoSemanal'";
                $mostrar = mysqli_query($conexion,$consulta)
                or die("error al traer los datos");
                while ($extraido = mysqli_fetch_array($mostrar)){
                    $linea = ['id' => $extraido['idminijuego-juego-semanal'], 'nombreJuegoSemanal' => $extraido['nombreJuegoSemanal'], 'nombreMinijuego' => $extraido['nombreMinijuego']];
                    array_push($lista_asociacion_juegoSemanal, $linea);
                }
                $listado_json = json_encode($lista_asociacion_juegoSemanal);
                echo $listado_json;
                break;

            case 'instruccionesJuegoSemanal':
                $idJuegoSemanal = $_GET['id'];
                $lista_Instrucciones_Juego_Semanal = [];
                $consulta = "SELECT * FROM `instrucciones-juego-semanal` JOIN `juego-semanal` ON `instrucciones-juego-semanal`.`id-juego-semanal` = `juego-semanal`.`id-juego-semanal` WHERE `juego-semanal`.`id-juego-semanal` = '$idJuegoSemanal'";
                $mostrar = mysqli_query($conexion,$consulta)
                or die("error al traer los datos");
                while ($extraido = mysqli_fetch_array($mostrar)){
                    $linea = ['id' => $extraido['id-instruccion-juego-semanal'], 'nombreJuegoSemanal' => $extraido['nombreJuegoSemanal'], 'instruccionesJuegoSemanal' => $extraido['instruccionesJuegoSemanal'], 'imagenJuegoSemanal' => $extraido['ImagenJuegoSemanal']];
                    array_push($lista_Instrucciones_Juego_Semanal, $linea);
                }
                $listado_json = json_encode($lista_Instrucciones_Juego_Semanal);
                echo $listado_json;
                break;

            case 'videos':
                $lista_videos = [];
                $consulta = "SELECT * FROM `video-semanal` ORDER BY nombreVideoSemanal asc";
                $mostrar = mysqli_query($conexion,$consulta)
                or die("error al traer los datos");
                while ($extraido = mysqli_fetch_array($mostrar)){
                    $linea = ['value' => $extraido['id-video-semanal'], 'label' => $extraido['nombreVideoSemanal']];
                    array_push($lista_videos, $linea);
                }
                $listado_json = json_encode($lista_videos);
                echo $listado_json;
                break;

            case 'medallas':
                $lista_medallas = [];
                $consulta = "SELECT * FROM medallas ORDER BY nombreMedalla asc";
                $mostrar = mysqli_query($conexion,$consulta)
                or die("error al traer los datos");
                while ($extraido = mysqli_fetch_array($mostrar)){
                    $linea = ['value' => $extraido['id-medalla'], 'label' => $extraido['nombreMedalla']];
                    array_push($lista_medallas, $linea);
                }
                $listado_json = json_encode($lista_medallas);
                echo $listado_json;
                break;

            case 'medallaSemanal':
                $idMedallaSemanal = $_GET['id'];
                $consulta = "SELECT * FROM medallas WHERE `id-medalla`  ='$idMedallaSemanal'";
                $mostrar = mysqli_query($conexion,$consulta)
                or die("error al traer los datos");
                $extraido = mysqli_fetch_array($mostrar);
                $respuesta = ['id' => $extraido['id-medalla'], 'nombreMedalla' => $extraido['nombreMedalla'], 'linkImagenMedalla' => $extraido['linkImagenMedalla']];
                $respuesta_json = json_encode($respuesta);
                echo $respuesta_json; 
                break;

                

            case 'linkVideo':
                $idVideoSemanal = $_GET['id'];
                $consulta = "SELECT * FROM `video-semanal` WHERE `id-video-semanal`  ='$idVideoSemanal'";
                $mostrar = mysqli_query($conexion,$consulta)
                or die("error al traer los datos");
                $extraido = mysqli_fetch_array($mostrar);
                $respuesta = ['linkVideoSemanal' => $extraido['linkVideoSemanal']];
                $respuesta_json = json_encode($respuesta);
                echo $respuesta_json; 
                break;
                

            case 'semanas':
                $lista_semanas = [];
                $consulta = "SELECT * FROM semanas ORDER BY tituloSemana asc";
                $mostrar = mysqli_query($conexion,$consulta)
                or die("error al traer los datos");
                while ($extraido = mysqli_fetch_array($mostrar)){
                    $linea = ['value' => $extraido['id-semana'], 'label' => $extraido['tituloSemana']];
                    array_push($lista_semanas, $linea);
                }
                $listado_json = json_encode($lista_semanas);
                echo $listado_json;
                break;

            case 'semana':
                $idSemana = $_GET['id'];
                $consulta = "SELECT * FROM semanas JOIN `juego-semanal` ON semanas.`id-juego-semanal` = `juego-semanal`.`id-juego-semanal` JOIN `video-semanal` ON semanas.`id-video-semanal` = `video-semanal`.`id-video-semanal` JOIN medallas ON semanas.`id-medalla` = medallas.`id-medalla` WHERE semanas.`id-semana` = '$idSemana'";
                $mostrar = mysqli_query($conexion,$consulta)
                or die("error al traer los datos");
                $extraido = mysqli_fetch_array($mostrar);
                
                $respuesta = ['id' => $extraido['id-semana'], 'tituloSemana' => $extraido['tituloSemana'], 'bajadaSemana' => $extraido['bajadaSemana'], 'idMedalla' => $extraido['id-medalla'], 'labelMedalla' => $extraido['nombreMedalla'], 'idJuegoSemanal' => $extraido['id-juego-semanal'], 'labelJuegoSemanal' => $extraido['nombreJuegoSemanal'], 'idVideoSemanal' => $extraido['id-video-semanal'], 'labelVideoSemanal' => $extraido['nombreVideoSemanal'] ];
            
                $respuesta_json = json_encode($respuesta);
                echo $respuesta_json;
                break;
                

            case 'instruccionesSemana':
                $idSemana = $_GET['id'];
                $lista_Instrucciones_Semana = [];
                $consulta = "SELECT * FROM instrucciones JOIN semanas ON instrucciones.`id-semana` = semanas.`id-semana` WHERE semanas.`id-semana` = '$idSemana'";
                $mostrar = mysqli_query($conexion,$consulta)
                or die("error al traer los datos");
                while ($extraido = mysqli_fetch_array($mostrar)){
                    $linea = ['id' => $extraido['id-instruccion'], 'instruccionesSemana' => $extraido['instruccionesSemana'], 'imagenSemana' => $extraido['imagenSemana'], 'tituloSemana' => $extraido['tituloSemana']];
                    array_push($lista_Instrucciones_Semana, $linea);
                }
                $listado_json = json_encode($lista_Instrucciones_Semana);
                echo $listado_json;
                break;

            case 'programas':
                $lista_niveles = [];
                $consulta = "SELECT * FROM `programa-nivel` JOIN nivel ON `programa-nivel`.`id-nivel` = nivel.`id-nivel` JOIN institucion ON nivel.`id-institucion` = institucion.`id-institucion` ORDER BY nombreInstitucion asc";
                $mostrar = mysqli_query($conexion,$consulta)
                or die("error al traer los datos");                
                while ($extraido = mysqli_fetch_array($mostrar)){
                    $opcion = $extraido['nombreProgramaNivel'].' - '.$extraido['nombreInstitucion'] .' - '.$extraido['nombreNivel'];
                    $linea = ['value' => $extraido['id-programa-nivel'], 'label' => $opcion];
                    array_push($lista_niveles, $linea);
                }
                $listado_json = json_encode($lista_niveles);
                echo $listado_json;
                break;
            
            case 'programa':
                $idProgramaNivel = $_GET['id'];
                $consulta = "SELECT * FROM `programa-nivel` JOIN nivel ON `programa-nivel`.`id-nivel` = nivel.`id-nivel` JOIN institucion ON nivel.`id-institucion` = institucion.`id-institucion` WHERE `programa-nivel`.`id-programa-nivel`  ='$idProgramaNivel'";
                $mostrar = mysqli_query($conexion,$consulta)
                or die("error al traer los datos");
                $extraido = mysqli_fetch_array($mostrar);
                $opcion = $extraido['nombreInstitucion'] .' - '.$extraido['nombreNivel'];
                $respuesta = ['id' => $extraido['id-programa-nivel'], 'idNivel' => $extraido['id-nivel'], 'labelNivel' => $opcion, 'nombreProgramaNivel' => $extraido['nombreProgramaNivel'], 'descripcionProgramaNivel' => $extraido['descripcionProgramaNivel']];
                $respuesta_json = json_encode($respuesta);
                echo $respuesta_json;
                break;

            case 'asociacionSemanaProgramaNivel':

                $idProgramaNivel = $_GET['id'];
                $lista_asociacion_programa_nivel = [];
                $consulta = "SELECT * FROM `semana-programa-nivel` JOIN `programa-nivel` ON `semana-programa-nivel`.`id-programa-nivel` = `programa-nivel`.`id-programa-nivel` JOIN semanas ON `semana-programa-nivel`.`id-semana` =  semanas.`id-semana` WHERE `programa-nivel`.`id-programa-nivel` = '$idProgramaNivel' ORDER BY `numeroSemana-programa`";
                $mostrar = mysqli_query($conexion,$consulta)
                or die("error al traer los datos");
                while ($extraido = mysqli_fetch_array($mostrar)){
                    $linea = ['id' => $extraido['id-semana-programa-nivel'], 'numeroSemanaPrograma' => $extraido['numeroSemana-programa'], 'idSemana' => $extraido['id-semana'], 'tituloSemana' => $extraido['tituloSemana'], 'idProgramaNivel' => $extraido['id-programa-nivel'], 'nombreProgramaNivel' => $extraido['nombreProgramaNivel'], 'fechaInicioSemana' => $extraido['fechaInicioSemana'], 'fechaFinSemana' => $extraido['FechaFinSemana']];
                    array_push($lista_asociacion_programa_nivel, $linea);
                }
                $listado_json = json_encode($lista_asociacion_programa_nivel);
                echo $listado_json;

                break;

            case 'semanaProgramaNivel':

                $idSemanaProgramaNivel = $_GET['id'];
                $consulta = "SELECT * FROM `semana-programa-nivel` JOIN `programa-nivel` ON `semana-programa-nivel`.`id-programa-nivel` = `programa-nivel`.`id-programa-nivel` JOIN semanas ON `semana-programa-nivel`.`id-semana` = semanas.`id-semana` WHERE `semana-programa-nivel`.`id-semana-programa-nivel`  ='$idSemanaProgramaNivel'";
                $mostrar = mysqli_query($conexion,$consulta)
                or die("error al traer los datos");
                $extraido = mysqli_fetch_array($mostrar);
                $respuesta = ['id' => $extraido['id-semana-programa-nivel'], 'numeroSemanaPrograma' => $extraido['numeroSemana-programa'], 'idProgramaNivel' => $extraido['id-programa-nivel'], 'labelProgramaNivel' => $extraido['nombreProgramaNivel'], 'idSemana' => $extraido['id-semana'], 'labelSemana' => $extraido['tituloSemana'], 'fechaInicioSemana' => $extraido['fechaInicioSemana'], 'fechaTerminoSemana' => $extraido['FechaFinSemana']];
                $respuesta_json = json_encode($respuesta);
                echo $respuesta_json;
                break;

            case 'mensajesSemanaProgramaNivel':

                $idSemanaProgramaNivel = $_GET['id'];
                $lista_mensajes_semana_programa_nivel = [];
                $consulta = "SELECT * FROM `mensajes-semana-programa-nivel` JOIN `semana-programa-nivel` ON `mensajes-semana-programa-nivel`.`id-semana-programa-nivel` = `semana-programa-nivel`.`id-semana-programa-nivel` WHERE `semana-programa-nivel`.`id-semana-programa-nivel`  ='$idSemanaProgramaNivel'";
                $mostrar = mysqli_query($conexion,$consulta)
                or die("error al traer los datos");
                while ($extraido = mysqli_fetch_array($mostrar)){
                    $linea = ['id' => $extraido['id-mensaje-semana-programa-nivel'], 'tituloMensajeSemanaProgramaNivel' => $extraido['tituloMensajeSemanaProgramaNivel'], 'DescripcionMensajeSemanaProgramaNivel' => $extraido['DescripcionMensajeSemanaProgramaNivel'], 'estadoMensajeProgramaNivel' => $extraido['estadoMensajeProgramaNivel']];
                    array_push($lista_mensajes_semana_programa_nivel, $linea);
                }
                $listado_json = json_encode($lista_mensajes_semana_programa_nivel);
                echo $listado_json;

                break;

            case 'programaNivelInstitucion':
                $idInstitucion = $_GET['id'];
                $lista_niveles = [];
                $consulta = "SELECT * FROM `programa-nivel` JOIN nivel ON `programa-nivel`.`id-nivel` = nivel.`id-nivel` JOIN institucion ON nivel.`id-institucion` = institucion.`id-institucion` WHERE `nivel`.`id-institucion`  = '$idInstitucion' ORDER BY nombreProgramaNivel asc";
                $mostrar = mysqli_query($conexion,$consulta)
                or die("error al traer los datos");                
                while ($extraido = mysqli_fetch_array($mostrar)){
                    $opcion = $extraido['nombreProgramaNivel'].' - '.$extraido['nombreInstitucion'] .' - '.$extraido['nombreNivel'];
                    $linea = ['value' => $extraido['id-programa-nivel'], 'label' => $opcion];
                    array_push($lista_niveles, $linea);
                }
                $listado_json = json_encode($lista_niveles);
                echo $listado_json;
                break;

            case 'profesoresInstitucion':
                $idInstitucion = $_GET['id'];
                $lista_usuarios = [];
                $idRol = 2;
                $consulta = "SELECT * FROM usuarios JOIN nivel ON usuarios.`id-nivel` = nivel.`id-nivel` JOIN institucion ON nivel.`id-institucion` = institucion.`id-institucion` WHERE nivel.`id-institucion` = '$idInstitucion' AND usuarios.`id-rol` = '$idRol' ORDER BY `nombreUsuario` asc";
                $mostrar = mysqli_query($conexion,$consulta)
                or die("error al traer los datos");
                while ($extraido = mysqli_fetch_array($mostrar)){
                    $opcion = $extraido['rutUsuario'] .' - '.$extraido['nombreUsuario'];
                    $linea = ['value' => $extraido['id-usuario'], 'label' => $opcion];
                    array_push($lista_usuarios, $linea);
                }
                $listado_json = json_encode($lista_usuarios);
                echo $listado_json;
                break;

            case 'asociacionProfesorProgramaNivel':

                $idProgramaNivel = $_GET['id'];
                $lista_profesores_programa_nivel = [];
                $consulta = "SELECT * FROM `profesores-programa-nivel` JOIN `programa-nivel` ON `profesores-programa-nivel`.`id-programa-nivel` = `programa-nivel`.`id-programa-nivel` JOIN nivel ON `programa-nivel`.`id-nivel` = nivel.`id-nivel` JOIN usuarios ON `profesores-programa-nivel`.`id-usuario` = usuarios.`id-usuario` WHERE `programa-nivel`.`id-programa-nivel`  ='$idProgramaNivel'";
                $mostrar = mysqli_query($conexion,$consulta)
                or die("error al traer los datos");
                while ($extraido = mysqli_fetch_array($mostrar)){
                    $linea = ['id' => $extraido['id-profesor-programa-nivel'], 'nombreUsuario' => $extraido['nombreUsuario'], 'apellidoUsuario' => $extraido['apellidoUsuario'], 'nombreProgramaNivel' => $extraido['nombreProgramaNivel'], 'nombreNivel' => $extraido['nombreNivel'], 'estadoAsociacionProfesorProgramaNivel' => $extraido['estadoAsociacionProfesorProgramaNivel']];
                    array_push($lista_profesores_programa_nivel, $linea);
                }
                $listado_json = json_encode($lista_profesores_programa_nivel);
                echo $listado_json;

                break;
            
            case 'asociacionCorporacionInstitucion':

                $idCorporacion = $_GET['id'];
                $lista_corporacion_institucion = [];
                $consulta = "SELECT * FROM `corporacion-institucion` JOIN corporaciones ON `corporacion-institucion`.`id-corporacion` = corporaciones.`id-corporacion` JOIN institucion ON `corporacion-institucion`.`id-institucion` = institucion.`id-institucion`  WHERE `corporacion-institucion`.`id-corporacion`  ='$idCorporacion'";
                $mostrar = mysqli_query($conexion,$consulta)
                or die("error al traer los datos");
                while ($extraido = mysqli_fetch_array($mostrar)){
                    $linea = ['id' => $extraido['id-corporacion-institucion'], 'nombreCorporacion' => $extraido['nombreCorporacion'], 'rbdInstitucion' => $extraido['rbdInstitucion'], 'nombreInstitucion' => $extraido['nombreInstitucion']];
                    array_push($lista_corporacion_institucion, $linea);
                }
                $listado_json = json_encode($lista_corporacion_institucion);
                echo $listado_json;

                break;
            
                
            default:
                break;


            
        }
        
        
    }else if($metodo === 'POST'){
            $tipo = $datos_post['tipo'];
            
         switch($tipo){
            case 'guardarCorporacion':
                $rutCorporacion = $datos_post['rutCorporacion'];
                $nombreCorporacion = $datos_post['nombreCorporacion'];
                $errorConsulta = ['exito' => 'no', 'mensaje' => 'Error al verificar'];
                $mensajeErrorConsulta = json_encode($errorConsulta);    
                $consulta = "SELECT * FROM corporaciones WHERE rutCorporacion = '$rutCorporacion'";
                $mostrar = mysqli_query($conexion,$consulta)
                or die($mensajeErrorConsulta);
                $row_cnt = mysqli_num_rows($mostrar);           
                if ($row_cnt > 0) {
                    $mensaje = ['exito'=> 'no','mensaje' => 'Ya existe esta Corporación'];
                    $respuesta = json_encode($mensaje);
                    echo $respuesta;
                }else{
                    $errorInsertar = ['exito' => 'no', 'mensaje' => 'Error al insertar'];
                    $mensajeErrorInsertar = json_encode($errorConsulta);
                    $insertar = "INSERT INTO corporaciones(rutCorporacion, nombrecorporacion) VALUES('$rutCorporacion', '$nombreCorporacion')";
                    $guardar = mysqli_query($conexion,$insertar)
                    or die($mensajeErrorInsertar);
                    $mensaje = ['exito'=> 'si','mensaje' => 'Corporación registrada con éxito'];
                    $respuesta = json_encode($mensaje);
                    echo $respuesta;
                }
                break;
            case 'guardarInstitucion':
                $rbdInstitucion = $datos_post['rbdInstitucion'];
                $nombreInstitucion = $datos_post['nombreInstitucion'];
                $tipoInstitucion = $datos_post['tipoInstitucion'];
                $tipoUbicacionInstitucion = $datos_post['tipoUbicacionInstitucion'];
                $comuna = $datos_post['comuna'];
                $calleInstitucion = $datos_post['calleInstitucion'];
                $numeroInstitucion = $datos_post['numeroInstitucion'];
                $fonoInstitucion = $datos_post['fonoInstitucion'];
                $mailInstitucion = $datos_post['mailInstitucion'];
                $directorInstitucion = $datos_post['directorInstitucion'];
                $cursosNivelInstitucion = $datos_post['cursosNivelInstitucion'];
                $alumnosNivelInstitucion = $datos_post['alumnosNivelInstitucion'];
                $errorConsulta = ['exito' => 'no', 'mensaje' => 'Error al verificar'];
                $mensajeErrorConsulta = json_encode($errorConsulta);    
                $consulta = "SELECT * FROM institucion WHERE rbdInstitucion = '$rbdInstitucion'";
                $mostrar = mysqli_query($conexion,$consulta)
                or die($mensajeErrorConsulta);
                $row_cnt = mysqli_num_rows($mostrar);           
                if ($row_cnt > 0) {
                    $mensaje = ['exito'=> 'no','mensaje' => 'Ya existe esta Institucion'];
                    $respuesta = json_encode($mensaje);
                    echo $respuesta;
                }else{
                    $errorInsertar = ['exito' => 'no', 'mensaje' => 'Error al insertar'];
                    $mensajeErrorInsertar = json_encode($errorConsulta);
                    $insertar = "INSERT INTO institucion(rbdInstitucion, nombreInstitucion, `id-comuna`, calleInstitucion, numeroInstitucion, fonoInstitucion, mailInstitucion, nombreDirector, cursosPorNIvel, alumnosPorNivel, tipoInstitucion, tipoUbicacionInstitucion) VALUES('$rbdInstitucion', '$nombreInstitucion', '$comuna', '$calleInstitucion', '$numeroInstitucion', '$fonoInstitucion', '$mailInstitucion', '$directorInstitucion', '$cursosNivelInstitucion', '$alumnosNivelInstitucion', '$tipoInstitucion', '$tipoUbicacionInstitucion')";
                    $guardar = mysqli_query($conexion,$insertar)
                    or die($mensajeErrorInsertar);
                    $mensaje = ['exito'=> 'si','mensaje' => 'Institución registrada con éxito'];
                    $respuesta = json_encode($mensaje);
                    echo $respuesta;
                }
                break;

            case 'guardarNivel':
                $nombreNivel = $datos_post['nombreNivel'];
                $cantidadAlumnosNivel = $datos_post['cantidadAlumnosNivel'];
                $temporadaNivel = $datos_post['temporadaNivel'];
                $idInstitucion = $datos_post['idInstitucion'];
                $errorConsulta = ['exito' => 'no', 'mensaje' => 'Error al verificar'];
                $mensajeErrorConsulta = json_encode($errorConsulta);    
                $consulta = "SELECT * FROM nivel JOIN institucion ON nivel.`id-institucion` = institucion.`id-institucion` WHERE nivel.nombreNivel = '$nombreNivel' AND institucion.`id-institucion` = '$idInstitucion'";
                $mostrar = mysqli_query($conexion,$consulta)
                or die($mensajeErrorConsulta);
                $row_cnt = mysqli_num_rows($mostrar);           
                if ($row_cnt > 0) {
                    $mensaje = ['exito'=> 'no','mensaje' => 'Ya existe este nivel en esta institución'];
                    $respuesta = json_encode($mensaje);
                    echo $respuesta;
                }else{
                    $errorInsertar = ['exito' => 'no', 'mensaje' => 'Error al insertar'];
                    $mensajeErrorInsertar = json_encode($errorInsertar);
                    $insertar = "INSERT INTO nivel(nombreNivel, `id-institucion`, cantidadAlumnos, temporada) VALUES('$nombreNivel', '$idInstitucion', '$cantidadAlumnosNivel', '$temporadaNivel')";
                    $guardar = mysqli_query($conexion,$insertar)
                    or die($mensajeErrorInsertar);
                    $mensaje = ['exito'=> 'si','mensaje' => 'Nivel Registrado con éxito'];
                    $respuesta = json_encode($mensaje);
                    echo $respuesta;
                }
                break;
            
            case 'guardarUsuario':
                $rutUsuario = $datos_post['rutUsuario'];
                $nombreUsuario = $datos_post['nombreUsuario'];
                $apellidoUsuario  = $datos_post['apellidoUsuario'];
                $mailUsuario = $datos_post['mailUsuario'];
                $fonoUsuario = $datos_post['fonoUsuario'];
                $userUsuario = $datos_post['userUsuario'];
                $claveUsuario = $datos_post['claveUsuario'];
                $idRol = $datos_post['idRol'];
                $sexoUsuario = $datos_post['sexoUsuario'];
                $fechaNacimientoUsuario = $datos_post['fechaNacimientoUsuario'];
                $respuesta = guardarUsuario($conexion, $rutUsuario, $nombreUsuario, $apellidoUsuario, $mailUsuario, $fonoUsuario, $userUsuario, $claveUsuario, $idRol, $sexoUsuario, $fechaNacimientoUsuario);
                echo $respuesta;
                break;

            case 'guardarMinijuego':
                $codigoMinijuego = $datos_post['codigoMinijuego'];
                $nombreMinijuego = $datos_post['nombreMinijuego'];
                $errorConsulta = ['exito' => 'no', 'mensaje' => 'Error al verificar minijuego', 'codigoMinijuego' => $codigoMinijuego, 'nombre' => $nombreMinijuego];
                $mensajeErrorConsulta = json_encode($errorConsulta);    
                $consulta = "SELECT * FROM minijuegos WHERE codigoMinijuego = '$codigoMinijuego' OR nombreMinijuego = '$nombreMinijuego'";
                $mostrar = mysqli_query($conexion,$consulta)
                or die($mensajeErrorConsulta);
                $row_cnt = mysqli_num_rows($mostrar);           
                if ($row_cnt > 0) {
                    $mensaje = ['exito'=> 'no','mensaje' => 'Ya existe este código o nombre, asociado a un minijuego'];
                    $respuesta = json_encode($mensaje);
                    echo $respuesta;
                }else{
                    $errorInsertar = ['exito' => 'no', 'mensaje' => 'Error al insertar'];
                    $mensajeErrorInsertar = json_encode($errorInsertar);
                    $insertar = "INSERT INTO minijuegos(codigoMinijuego, nombreMinijuego) VALUES('$codigoMinijuego', '$nombreMinijuego')";
                    mysqli_query($conexion,$insertar)
                    or die($mensajeErrorInsertar);
                    $mensaje = ['exito'=> 'si','mensaje' => 'minijuego registrado con éxito'];
                    $respuesta = json_encode($mensaje);
                    echo $respuesta;
                }

                
                break;
            
            case 'guardarPalabraSemanal':
                $nombrePalabraSemanal = $datos_post['nombrePalabraSemanal'];
                $errorConsulta = ['exito' => 'no', 'mensaje' => 'Error al verificar'];
                $mensajeErrorConsulta = json_encode($errorConsulta);    
                $consulta = "SELECT * FROM `palabra-semanal` WHERE nombrePalabraSemanal = '$nombrePalabraSemanal'";
                $mostrar = mysqli_query($conexion,$consulta)
                or die($mensajeErrorConsulta);
                $row_cnt = mysqli_num_rows($mostrar);           
                if ($row_cnt > 0) {
                    $mensaje = ['exito'=> 'no','mensaje' => 'Ya existe esta Palabra'];
                    $respuesta = json_encode($mensaje);
                    echo $respuesta;
                }else{
                    $errorInsertar = ['exito' => 'no', 'mensaje' => 'Error al insertar'];
                    $mensajeErrorInsertar = json_encode($errorInsertar);
                    $insertar = "INSERT INTO `palabra-semanal`(nombrePalabraSemanal) VALUES('$nombrePalabraSemanal')";
                    $guardar = mysqli_query($conexion,$insertar)
                    or die($mensajeErrorInsertar);
                    $mensaje = ['exito'=> 'si','mensaje' => 'Palabra Semanal registrada con éxito'];
                    $respuesta = json_encode($mensaje);
                    echo $respuesta;
                }

                
                break;
            
            case 'guardarTrivia':
                $nombreTrivia = $datos_post['nombreTrivia'];
                $descripcionTrivia = $datos_post['descripcionTrivia'];
                $errorConsulta = ['exito' => 'no', 'mensaje' => 'Error al verificar'];
                $mensajeErrorConsulta = json_encode($errorConsulta);    
                $consulta = "SELECT * FROM trivias WHERE nombreTrivia = '$nombreTrivia'";
                $mostrar = mysqli_query($conexion,$consulta)
                or die($mensajeErrorConsulta);
                $row_cnt = mysqli_num_rows($mostrar);           
                if ($row_cnt > 0) {
                    $mensaje = ['exito'=> 'no','mensaje' => 'Ya existe esta trivia'];
                    $respuesta = json_encode($mensaje);
                    echo $respuesta;
                }else{
                    $errorInsertar = ['exito' => 'no', 'mensaje' => 'Error al insertar'];
                    $mensajeErrorInsertar = json_encode($errorInsertar);
                    $insertar = "INSERT INTO trivias (nombreTrivia, descripcionTrivia) VALUES('$nombreTrivia', '$descripcionTrivia')";
                    $guardar = mysqli_query($conexion,$insertar)
                    or die($mensajeErrorInsertar);
                    $mensaje = ['exito'=> 'si','mensaje' => 'Trivia registrada con éxito'];
                    $respuesta = json_encode($mensaje);
                    echo $respuesta;
                }

                
                break;

            case 'guardarPregunta':
                $codigoPregunta = $datos_post['codigoPregunta'];
                $nombrePregunta = $datos_post['nombrePregunta'];
                $errorConsulta = ['exito' => 'no', 'mensaje' => 'Error al verificar'];
                $mensajeErrorConsulta = json_encode($errorConsulta);    
                $consulta = "SELECT * FROM preguntas WHERE codigoPregunta = '$codigoPregunta' OR pregunta = '$nombrePregunta'";
                $mostrar = mysqli_query($conexion,$consulta)
                or die($mensajeErrorConsulta);
                $row_cnt = mysqli_num_rows($mostrar);           
                if ($row_cnt > 0) {
                    $mensaje = ['exito'=> 'no','mensaje' => 'Ya existe este código o nombre, asociado a una Pregunta'];
                    $respuesta = json_encode($mensaje);
                    echo $respuesta;
                }else{
                    $errorInsertar = ['exito' => 'no', 'mensaje' => 'Error al insertar'];
                    $mensajeErrorInsertar = json_encode($errorInsertar);
                    $insertar = "INSERT INTO preguntas (codigoPregunta, pregunta) VALUES('$codigoPregunta','$nombrePregunta')";
                    $guardar = mysqli_query($conexion,$insertar)
                    or die($mensajeErrorInsertar);
                    $mensaje = ['exito'=> 'si','mensaje' => 'Pregunta registrada con éxito'];
                    $respuesta = json_encode($mensaje);
                    echo $respuesta;
                }

                
                break;

            case 'guardarAsociacionTriviaPregunta':
                $idTrivia = $datos_post['idTrivia'];
                $idPregunta = $datos_post['idPregunta'];
                $errorConsulta = ['exito' => 'no', 'mensaje' => 'Error al verificar'];
                $mensajeErrorConsulta = json_encode($errorConsulta);    
                $consulta = "SELECT * FROM `preguntas-trivias` WHERE `id-trivia` = '$idTrivia' AND `id-pregunta`='$idPregunta'";
                $mostrar = mysqli_query($conexion,$consulta)
                or die($mensajeErrorConsulta);
                $row_cnt = mysqli_num_rows($mostrar);           
                if ($row_cnt > 0) {
                    $mensaje = ['exito'=> 'no','mensaje' => 'Ya existe esta Asociacion'];
                    $respuesta = json_encode($mensaje);
                    echo $respuesta;
                }else{
                    $errorInsertar = ['exito' => 'no', 'mensaje' => 'Error al insertar'];
                    $mensajeErrorInsertar = json_encode($errorInsertar);
                    $insertar = "INSERT INTO `preguntas-trivias` (`id-trivia`, `id-pregunta`) VALUES('$idTrivia', '$idPregunta')";
                    $guardar = mysqli_query($conexion,$insertar)
                    or die($mensajeErrorInsertar);
                    $mensaje = ['exito'=> 'si','mensaje' => 'Asociacion Registrada con éxito'];
                    $respuesta = json_encode($mensaje);
                    echo $respuesta;
                }

                
                break;

            
            case 'guardaJuegoSemanal':
                $nombreJuegoSemanal = $datos_post['nombreJuegoSemanal'];
                $metaPuntaje = $datos_post['metaPuntaje'];
                $idTrivia = $datos_post['idTrivia'];
                $idPalabraSemanal = $datos_post['idPalabraSemanal'];
                $errorConsulta = ['exito' => 'no', 'mensaje' => 'Error'];
                $mensajeErrorConsulta = json_encode($errorConsulta);    
                $consulta = "SELECT * FROM `juego-semanal` WHERE nombreJuegoSemanal ='$nombreJuegoSemanal'";
                $mostrar = mysqli_query($conexion,$consulta)
                or die($mensajeErrorConsulta);
                $row_cnt = mysqli_num_rows($mostrar);           
                if ($row_cnt > 0) {
                    $mensaje = ['exito'=> 'no','mensaje' => 'Ya existe este Juego Semanal'];
                    $respuesta = json_encode($mensaje);
                    echo $respuesta;
                }else{
                    $errorInsertar = ['exito' => 'no', 'mensaje' => 'Error al insertar'];
                    $mensajeErrorInsertar = json_encode($errorInsertar);
                    $insertar = "INSERT INTO `juego-semanal` (nombreJuegoSemanal, metaPuntaje, `id-palabra-semanal`, `id-trivia`) VALUES('$nombreJuegoSemanal','$metaPuntaje','$idPalabraSemanal', '$idTrivia')";
                    $guardar = mysqli_query($conexion,$insertar)
                    or die($mensajeErrorInsertar);
                    $mensaje = ['exito'=> 'si','mensaje' => 'Juego Semanal Registrado con éxito'];
                    $respuesta = json_encode($mensaje);
                    echo $respuesta;
                }

                
                break;

            case 'guardarAsociacionJuegoSemanalMinijuego':
                $idJuegoSemanal = $datos_post['idJuegoSemanal'];
                $idMinijuego = $datos_post['idMinijuego'];
                $errorConsulta = ['exito' => 'no', 'mensaje' => 'Error al verificar'];
                $mensajeErrorConsulta = json_encode($errorConsulta);    
                $consulta = "SELECT * FROM `minijuegos-juego-semanal` WHERE `id-juego-semanal` = '$idJuegoSemanal' AND `id-minijuego`='$idMinijuego'";
                $mostrar = mysqli_query($conexion,$consulta)
                or die($mensajeErrorConsulta);
                $row_cnt = mysqli_num_rows($mostrar);           
                if ($row_cnt > 0) {
                    $mensaje = ['exito'=> 'no','mensaje' => 'Ya existe esta Asociacion'];
                    $respuesta = json_encode($mensaje);
                    echo $respuesta;
                }else{
                    $errorInsertar = ['exito' => 'no', 'mensaje' => 'Error al insertar'];
                    $mensajeErrorInsertar = json_encode($errorInsertar);
                    $estado = "INACTIVO";
                    $insertar = "INSERT INTO `minijuegos-juego-semanal` (`id-minijuego`, `id-juego-semanal`) VALUES('$idMinijuego', '$idJuegoSemanal')";
                    $guardar = mysqli_query($conexion,$insertar)
                    or die($mensajeErrorInsertar);
                    $mensaje = ['exito'=> 'si','mensaje' => 'Asociacion Registrada con éxito'];
                    $respuesta = json_encode($mensaje);
                    echo $respuesta;
                }                
                break;

            case 'guardarSemana':
                $tituloSemana = $datos_post['tituloSemana'];
                $bajadaSemana = $datos_post['bajadaSemana'];
                $idMedalla = $datos_post['idMedalla'];                
                $idJuegoSemanal = $datos_post['idJuegoSemanal'];
                $idVideoSemanal = $datos_post['idVideoSemanal'];
                $errorConsulta = ['exito' => 'no', 'mensaje' => 'Error al verificar'];
                $mensajeErrorConsulta = json_encode($errorConsulta);    
                $consulta = "SELECT * FROM semanas WHERE `tituloSemana` = '$tituloSemana'";
                $mostrar = mysqli_query($conexion,$consulta)
                or die($mensajeErrorConsulta);
                $row_cnt = mysqli_num_rows($mostrar);           
                if ($row_cnt > 0) {
                    $mensaje = ['exito'=> 'no','mensaje' => 'Ya existe esta Semana'];
                    $respuesta = json_encode($mensaje);
                    echo $respuesta;
                }else{
                    $errorInsertar = ['exito' => 'no', 'mensaje' => 'Error al insertar'];
                    $mensajeErrorInsertar = json_encode($errorInsertar);
                    $insertar = "INSERT INTO semanas(tituloSemana, bajadaSemana, `id-medalla`, `id-juego-semanal`, `id-video-semanal`) VALUES('$tituloSemana', '$bajadaSemana', '$idMedalla', '$idJuegoSemanal', '$idVideoSemanal')";
                    $guardar = mysqli_query($conexion,$insertar)
                    or die($mensajeErrorInsertar);
                    $mensaje = ['exito'=> 'si','mensaje' => 'Semana Registrada con éxito'];
                    $respuesta = json_encode($mensaje);
                    echo $respuesta;
                }                
                break;



            case 'guardarProgramaNivel':
                $idNivel = $datos_post['idNivel'];
                $nombreProgramaNivel = $datos_post['nombreProgramaNivel'];
                $descripcionProgramaNivel = $datos_post['descripcionProgramaNivel'];                
                $errorConsulta = ['exito' => 'no', 'mensaje' => 'Error al verificar'];
                $mensajeErrorConsulta = json_encode($errorConsulta);    
                $consulta = "SELECT * FROM `programa-nivel` WHERE nombreProgramaNivel= '$nombreProgramaNivel'";
                $mostrar = mysqli_query($conexion,$consulta)
                or die($mensajeErrorConsulta);
                $row_cnt = mysqli_num_rows($mostrar);           
                if ($row_cnt > 0) {
                    $mensaje = ['exito'=> 'no','mensaje' => 'Ya existe este Programa'];
                    $respuesta = json_encode($mensaje);
                    echo $respuesta;
                }else{
                    $errorInsertar = ['exito' => 'no', 'mensaje' => 'Error al insertar'];
                    $mensajeErrorInsertar = json_encode($errorConsulta);
                    $insertar = "INSERT INTO `programa-nivel`(`id-nivel`, nombreProgramaNivel, descripcionProgramaNivel) VALUES('$idNivel', '$nombreProgramaNivel', '$descripcionProgramaNivel')";
                    $guardar = mysqli_query($conexion,$insertar)
                    or die($mensajeErrorInsertar);
                    $mensaje = ['exito'=> 'si','mensaje' => 'Programa Registrado con éxito'];
                    $respuesta = json_encode($mensaje);
                    echo $respuesta;
                }                
                break;

            case 'guardarSemanaProgramaNivel':

                $idProgramaNivel = $datos_post['idProgramaNivel'];
                $idSemana = $datos_post['idSemana'];
                $numeroSemana = $datos_post['numeroSemanaPrograma'];
                $fechaInicioSemana = $datos_post['fechaInicioSemana'];
                $fechaTerminoSemana = $datos_post['fechaTerminoSemana'];                
                $errorConsulta = ['exito' => 'no', 'mensaje' => 'Error al verificar'];
                $mensajeErrorConsulta = json_encode($errorConsulta);    
                $consulta = "SELECT * FROM `semana-programa-nivel` WHERE `id-programa-nivel`= '$idProgramaNivel' AND `id-semana` = '$idSemana'";
                $mostrar = mysqli_query($conexion,$consulta)
                or die($mensajeErrorConsulta);
                $row_cnt = mysqli_num_rows($mostrar);           
                if ($row_cnt > 0) {
                    $mensaje = ['exito'=> 'no','mensaje' => 'Esta semana ya está asociada a este programa'];
                    $respuesta = json_encode($mensaje);
                    echo $respuesta;
                }else{
                    $consultaNumeroSemana = "SELECT * FROM `semana-programa-nivel` WHERE `id-programa-nivel`= '$idProgramaNivel' AND `numeroSemana-programa` = '$numeroSemana'";
                    $mostrar = mysqli_query($conexion,$consultaNumeroSemana)
                    or die($mensajeErrorConsulta);
                    $row_cnt_numero_semana = mysqli_num_rows($mostrar);
                    if ($row_cnt_numero_semana > 0) {
                        $mensaje = ['exito'=> 'no','mensaje' => 'Este numero de semana ya está asociado en este Programa'];
                        $respuesta = json_encode($mensaje);
                        echo $respuesta;
                    }else{
                    $errorInsertar = ['exito' => 'no', 'mensaje' => 'Error al insertar'];
                    $mensajeErrorInsertar = json_encode($errorConsulta);
                    $insertar = "INSERT INTO `semana-programa-nivel`(`id-programa-nivel`, `id-semana`, `numeroSemana-programa`, fechaInicioSemana, FechaFinSemana) VALUES('$idProgramaNivel', '$idSemana', '$numeroSemana', '$fechaInicioSemana', '$fechaTerminoSemana')";
                    $guardar = mysqli_query($conexion,$insertar)
                    or die($mensajeErrorInsertar);
                    $mensaje = ['exito'=> 'si','mensaje' => 'Semana Asociada a Programa con éxito'];
                    $respuesta = json_encode($mensaje);
                    echo $respuesta;
                    }
                }                
                break;

            case 'guardarMensajeSemanaNivel':
                $tituloMensajeSemanaProgramaNivel = $datos_post['tituloMensajeSemanaProgramaNivel'];
                $DescripcionMensajeSemanaProgramaNivel = $datos_post['DescripcionMensajeSemanaProgramaNivel'];
                $idSemanaProgramaNivel = $datos_post['idSemanaProgramaNivel']; 
                $estadoMensajeProgramaNivel = $datos_post['estadoMensajeProgramaNivel'];                
                $errorConsulta = ['exito' => 'no', 'mensaje' => 'Error al verificar'];
                $mensajeErrorConsulta = json_encode($errorConsulta);    
                $consulta = "SELECT * FROM `mensajes-semana-programa-nivel` WHERE `id-semana-programa-nivel`='$idSemanaProgramaNivel' AND tituloMensajeSemanaProgramaNivel='$tituloMensajeSemanaProgramaNivel'";
                $mostrar = mysqli_query($conexion,$consulta)
                or die($mensajeErrorConsulta);
                $row_cnt = mysqli_num_rows($mostrar);           
                if ($row_cnt > 0) {
                    $mensaje = ['exito'=> 'no','mensaje' => 'Ya existe un mensaje con este título, para esta semana'];
                    $respuesta = json_encode($mensaje);
                    echo $respuesta;
                }else{
                    $errorInsertar = ['exito' => 'no', 'mensaje' => 'Error al insertar'];
                    $mensajeErrorInsertar = json_encode($errorInsertar);
                    $insertar = "INSERT INTO `mensajes-semana-programa-nivel`(tituloMensajeSemanaProgramaNivel, DescripcionMensajeSemanaProgramaNivel, `id-semana-programa-nivel`, estadoMensajeProgramaNivel) VALUES('$tituloMensajeSemanaProgramaNivel', '$DescripcionMensajeSemanaProgramaNivel', '$idSemanaProgramaNivel', '$estadoMensajeProgramaNivel')";
                    $guardar = mysqli_query($conexion,$insertar)
                    or die($mensajeErrorInsertar);
                    $mensaje = ['exito'=> 'si','mensaje' => 'Mensaje Registrado con éxito'];
                    $respuesta = json_encode($mensaje);
                    echo $respuesta;
                }                
                break;

            case 'guardarAsociarProfesorProgramaNivel':
                $idProgramaNivel = $datos_post['idProgramaNivel'];
                $idUsuario = $datos_post['idUsuario'];
                $estadoAsociacionProfesorProgramaNivel = "ACTIVO";
                $errorConsulta = ['exito' => 'no', 'mensaje' => 'Error al verificar'];
                $mensajeErrorConsulta = json_encode($errorConsulta);    
                $consulta = "SELECT * FROM `profesores-programa-nivel` WHERE `id-programa-nivel` = '$idProgramaNivel' AND `id-usuario`='$idUsuario'";
                $mostrar = mysqli_query($conexion,$consulta)
                or die($mensajeErrorConsulta);
                $row_cnt = mysqli_num_rows($mostrar);           
                if ($row_cnt > 0) {
                    $mensaje = ['exito'=> 'no','mensaje' => 'Ya existe esta Asociacion'];
                    $respuesta = json_encode($mensaje);
                    echo $respuesta;
                }else{
                    $errorInsertar = ['exito' => 'no', 'mensaje' => 'Error al insertar'];
                    $mensajeErrorInsertar = json_encode($errorInsertar);
                    $insertar = "INSERT INTO `profesores-programa-nivel`(`id-programa-nivel`, `id-usuario`, estadoAsociacionProfesorProgramaNivel) VALUES('$idProgramaNivel', '$idUsuario', '$estadoAsociacionProfesorProgramaNivel')";
                    $guardar = mysqli_query($conexion,$insertar)
                    or die($mensajeErrorInsertar);
                    $mensaje = ['exito'=> 'si','mensaje' => 'Asociacion Registrada con éxito'];
                    $respuesta = json_encode($mensaje);
                    echo $respuesta;
                }                
                break;

            case 'asociarCorporacionInstitucion':
                $idCorporacion = $datos_post['idCorporacion'];
                $idInstitucion = $datos_post['idInstitucion'];
                $errorConsulta = ['exito' => 'no', 'mensaje' => 'Error al verificar'];
                $mensajeErrorConsulta = json_encode($errorConsulta);    
                $consulta = "SELECT * FROM `corporacion-institucion` WHERE `id-corporacion` = '$idCorporacion' AND `id-institucion`='$idInstitucion'";
                $mostrar = mysqli_query($conexion,$consulta)
                or die($mensajeErrorConsulta);
                $row_cnt = mysqli_num_rows($mostrar);           
                if ($row_cnt > 0) {
                    $mensaje = ['exito'=> 'no','mensaje' => 'Ya existe esta Asociacion'];
                    $respuesta = json_encode($mensaje);
                    echo $respuesta;
                }else{
                    $errorInsertar = ['exito' => 'no', 'mensaje' => 'Error al insertar'];
                    $mensajeErrorInsertar = json_encode($errorInsertar);
                    $estado = "INACTIVO";
                    $insertar = "INSERT INTO `corporacion-institucion`(`id-corporacion`, `id-institucion`) VALUES('$idCorporacion', '$idInstitucion')";
                    $guardar = mysqli_query($conexion,$insertar)
                    or die($mensajeErrorInsertar);
                    $mensaje = ['exito'=> 'si','mensaje' => 'Asociacion Registrada con éxito'];
                    $respuesta = json_encode($mensaje);
                    echo $respuesta;
                }                
                break;

            default: 
                break;

        } 
    }else if($metodo === 'PUT'){
        $tipo = $datos_post['tipo'];
        switch ($tipo) {
            case 'editarCorporacion':
                $id = $datos_post['id'];
                $rut = $datos_post['rutCorporacion'];
                $nombre = $datos_post['nombreCorporacion'];
                $errorConsulta = ['exito' => 'no', 'mensaje' => 'Error al verificar'];
                $mensajeErrorConsulta = json_encode($errorConsulta);    
                $consulta = "SELECT * FROM corporaciones WHERE rutCorporacion = '$rut' AND `id-corporacion` != '$id'";
                $mostrar = mysqli_query($conexion,$consulta)
                or die($mensajeErrorConsulta);
                $row_cnt = mysqli_num_rows($mostrar);           
                if ($row_cnt > 0) {
                    $mensaje = ['exito'=> 'no','mensaje' => 'Ya existe esta Corporación'];
                    $respuesta = json_encode($mensaje);
                    echo $respuesta;
                }else{
                    $errorActualizar = ['exito' => 'no', 'mensaje' => 'Error al actualizar'];
                    $mensajeErrorActualizar = json_encode($errorConsulta);                   
                    $consulta = "UPDATE corporaciones SET rutCorporacion='$rut', nombreCorporacion= '$nombre' WHERE `id-corporacion`='$id'";
                    $resultado = mysqli_query($conexion,$consulta)
                    or die($mensajeErrorActualizar);
                    $mensaje = ['exito'=> 'si','mensaje' => 'Corporación actualizada con éxito'];
                    $respuesta = json_encode($mensaje);
                    echo $respuesta;
                }
                break;

            case 'editarInstitucion':
                $errorActualizar = ['exito' => 'no', 'mensaje' => 'Error al actualizar'];
                $mensajeErrorActualizar = json_encode($errorActualizar);
                $id = $datos_post['id'];
                $rbd = $datos_post['rbdInstitucion'];
                $nombre = $datos_post['nombreInstitucion'];
                $comuna = $datos_post['comuna'];
                $calleInstitucion = $datos_post['calleInstitucion'];
                $numeroInstitucion = $datos_post['numeroInstitucion'];
                $fonoInstitucion = $datos_post['fonoInstitucion'];
                $mailInstitucion = $datos_post['mailInstitucion'];
                $directorInstitucion = $datos_post['directorInstitucion'];
                $cursosNivelInstitucion = $datos_post['cursosNivelInstitucion'];
                $alumnosNivelInstitucion = $datos_post['alumnosNivelInstitucion'];
                $tipoInstitucion = $datos_post['tipoInstitucion'];
                $tipoUbicacionInstitucion = $datos_post['tipoUbicacionInstitucion'];
                $consulta = "SELECT * FROM institucion WHERE rbdInstitucion = '$rbd' AND `id-institucion` != '$id'";
                $mostrar = mysqli_query($conexion,$consulta)
                or die($mensajeErrorConsulta);
                $row_cnt = mysqli_num_rows($mostrar);           
                if ($row_cnt > 0) {
                    $mensaje = ['exito'=> 'no','mensaje' => 'Ya existe esta Institucion'];
                    $respuesta = json_encode($mensaje);
                    echo $respuesta;
                }else{
                    $consulta = "UPDATE institucion SET rbdInstitucion='$rbd', nombreInstitucion= '$nombre', `id-comuna` = '$comuna', calleInstitucion = '$calleInstitucion', numeroInstitucion = '$numeroInstitucion', fonoInstitucion = '$fonoInstitucion', mailInstitucion = '$mailInstitucion', nombreDirector = '$directorInstitucion', cursosPorNIvel = '$cursosNivelInstitucion', alumnosPorNivel = '$alumnosNivelInstitucion', tipoInstitucion = '$tipoInstitucion', tipoUbicacionInstitucion = '$tipoUbicacionInstitucion' WHERE `id-institucion`='$id'";
                    $resultado = mysqli_query($conexion,$consulta)
                    or die($mensajeErrorActualizar);
                    $mensaje = ['exito'=> 'si','mensaje' => 'Institución actualizada con éxito'];
                    $respuesta = json_encode($mensaje);
                    echo $respuesta;
                }
                break;

            case 'editarNivel':
                $errorActualizar = ['exito' => 'no', 'mensaje' => 'Error al actualizar'];
                $mensajeErrorActualizar = json_encode($errorActualizar);
                $id = $datos_post['id'];
                $nombre = $datos_post['nombreNivel'];
                $cantidadAlumnos = $datos_post['cantidadAlumnosNivel'];
                $temporadaNivel = $datos_post['temporadaNivel'];
                $idInst = $datos_post['idInstitucion'];
                $consulta = "SELECT * FROM nivel JOIN institucion ON nivel.`id-institucion` = institucion.`id-institucion` WHERE nivel.nombreNivel = '$nombre' AND nivel.`id-nivel` != '$id'";
                $mostrar = mysqli_query($conexion,$consulta)
                or die($mensajeErrorConsulta);
                $row_cnt = mysqli_num_rows($mostrar);           
                if ($row_cnt > 0) {
                    $mensaje = ['exito'=> 'no','mensaje' => 'Ya existe este nivel en esta institución'];
                    $respuesta = json_encode($mensaje);
                    echo $respuesta;
                }else{
                    $consulta = "UPDATE nivel SET nombreNivel='$nombre', `id-institucion`= '$idInst', cantidadAlumnos = '$cantidadAlumnos', temporada = '$temporadaNivel' WHERE `id-nivel`='$id'";
                    $resultado = mysqli_query($conexion,$consulta)
                    or die($mensajeErrorActualizar);
                    $mensaje = ['exito'=> 'si','mensaje' => 'Nivel actualizado con éxito'];
                    $respuesta = json_encode($mensaje);
                    echo $respuesta;
                }
                break;

            case 'editarUsuario':
                
                $errorActualizar = ['exito' => 'no', 'mensaje' => 'Error al actualizar'];
                $mensajeErrorActualizar = json_encode($errorActualizar);
                $id = $datos_post['id'];
                $rutUsuario = $datos_post['rutUsuario'];
                $nombreUsuario = $datos_post['nombreUsuario'];
                $apellidoUsuario = $datos_post['apellidoUsuario'];
                $mailUsuario = $datos_post['mailUsuario'];
                $fonoUsuario = $datos_post['fonoUsuario'];
                $userUsuario = $datos_post['userUsuario'];
                $claveUsuario = $datos_post['claveUsuario'];
                $idRol = $datos_post['idRol'];
                $sexoUsuario = $datos_post['sexoUsuario'];
                $fechaNacimientoUsuario = $datos_post['fechaNacimientoUsuario'];
                $errorConsulta = ['exito' => 'no', 'mensaje' => 'Error al verificar'];
                $mensajeErrorConsulta = json_encode($errorConsulta); 
                $consulta = "SELECT * FROM  usuarios WHERE rutUsuario = '$rutUsuario' AND `id-usuario` != '$id' ";
                $mostrar = mysqli_query($conexion,$consulta)
                or die($mensajeErrorConsulta);
                $row_cnt = mysqli_num_rows($mostrar);           
                if ($row_cnt > 0) {
                    $mensaje = ['exito'=> 'no','mensaje' => 'Ya existe este usuario'];
                    $respuesta = json_encode($mensaje);
                    echo $respuesta;
                }else{
                    if ($fechaNacimientoUsuario == '') {
                        $consulta = "UPDATE usuarios SET rutUsuario='$rutUsuario', nombreUsuario= '$nombreUsuario', apellidoUsuario = '$apellidoUsuario', mailUsuario = '$mailUsuario', fonoUsuario = '$fonoUsuario', userUsuario = '$userUsuario', claveUsuario = '$claveUsuario', `id-rol` = '$idRol', sexoUsuario = '$sexoUsuario'  WHERE `id-usuario`='$id'";
                        $resultado = mysqli_query($conexion,$consulta)
                        or die($mensajeErrorActualizar);
                        $mensaje = ['exito'=> 'si','mensaje' => 'Usuario actualizado con éxito'];
                        $respuesta = json_encode($mensaje);
                        echo $respuesta;
                    }else {
                        $consulta = "UPDATE usuarios SET rutUsuario='$rutUsuario', nombreUsuario= '$nombreUsuario', apellidoUsuario = '$apellidoUsuario', mailUsuario = '$mailUsuario', fonoUsuario = '$fonoUsuario', userUsuario = '$userUsuario', claveUsuario = '$claveUsuario', `id-rol` = '$idRol', sexoUsuario = '$sexoUsuario', fechaNacimientoUsuario = '$fechaNacimientoUsuario' WHERE `id-usuario`='$id'";
                        $resultado = mysqli_query($conexion,$consulta)
                        or die($mensajeErrorActualizar);
                        $mensaje = ['exito'=> 'si','mensaje' => 'Usuario actualizado con éxito'];
                        $respuesta = json_encode($mensaje);
                        echo $respuesta;
                    }
                    
                }
                break;
            
            case 'editarMinijuego':
                $errorVerificar = ['exito' => 'no', 'mensaje' => 'Error al verificar'];
                $mensajeErrorVerificar = json_encode($errorVerificar);
                $errorActualizar = ['exito' => 'no', 'mensaje' => 'Error al actualizar'];
                $mensajeErrorActualizar = json_encode($errorActualizar);
                $id = $datos_post['id'];
                $codigoMinijuego = $datos_post['codigoMinijuego'];
                $nombreMinijuego = $datos_post['nombreMinijuego'];
                $consulta = "SELECT * FROM minijuegos WHERE codigoMinijuego = '$codigoMinijuego' AND `id-minijuego` != '$id'";
                $mostrar = mysqli_query($conexion,$consulta)
                or die($mensajeErrorVerificar);
                $row_cnt = mysqli_num_rows($mostrar);           
                if ($row_cnt > 0) {
                    $mensaje = ['exito'=> 'no','mensaje' => 'Ya existe este código, asociado a un minijuego'];
                    $respuesta = json_encode($mensaje);
                    echo $respuesta;
                }else{
                    $consulta = "UPDATE minijuegos SET codigoMinijuego = '$codigoMinijuego', nombreMinijuego='$nombreMinijuego' WHERE `id-minijuego`='$id'";
                    $resultado = mysqli_query($conexion,$consulta)
                    or die($mensajeErrorActualizar);
                    $mensaje = ['exito'=> 'si','mensaje' => 'Minijuego actualizado con éxito'];
                    $respuesta = json_encode($mensaje);
                    echo $respuesta;
                }
                break;
            
            case 'editarPalabraSemanal':
                $errorVerificar = ['exito' => 'no', 'mensaje' => 'Error al verificar'];
                $mensajeErrorVerificar = json_encode($errorVerificar);
                $errorActualizar = ['exito' => 'no', 'mensaje' => 'Error al actualizar'];
                $mensajeErrorActualizar = json_encode($errorActualizar);
                $id = $datos_post['id'];
                $nombrePalabraSemanal = $datos_post['nombrePalabraSemanal'];
                $consulta = "SELECT * FROM `palabra-semanal` WHERE nombrePalabraSemanal = '$nombrePalabraSemanal' AND `id-palabra-semanal` != '$id'";
                $mostrar = mysqli_query($conexion,$consulta)
                or die($mensajeErrorVerificar);
                $row_cnt = mysqli_num_rows($mostrar);           
                if ($row_cnt > 0) {
                    $mensaje = ['exito'=> 'no','mensaje' => 'Ya existe esta Palabra'];
                    $respuesta = json_encode($mensaje);
                    echo $respuesta;
                }else{
                    $consulta = "UPDATE `palabra-semanal` SET nombrePalabraSemanal='$nombrePalabraSemanal' WHERE `id-palabra-semanal`='$id'";
                    $resultado = mysqli_query($conexion,$consulta)
                    or die($mensajeErrorActualizar);
                    $mensaje = ['exito'=> 'si','mensaje' => 'Palabra Semanal actualizada con éxito'];
                    $respuesta = json_encode($mensaje);
                    echo $respuesta;
                }
                break;
            
            case 'editarTrivia':
                $errorActualizar = ['exito' => 'no', 'mensaje' => 'Error al actualizar'];
                $mensajeErrorActualizar = json_encode($errorActualizar);
                $id = $datos_post['id'];
                $nombreTrivia = $datos_post['nombreTrivia'];
                $descripcionTrivia = $datos_post['descripcionTrivia'];
                $consulta = "SELECT * FROM trivias WHERE nombreTrivia = '$nombreTrivia' AND `id-trivia` != '$id'";
                $mostrar = mysqli_query($conexion,$consulta)
                or die($mensajeErrorConsulta);
                $row_cnt = mysqli_num_rows($mostrar);           
                if ($row_cnt > 0) {
                    $mensaje = ['exito'=> 'no','mensaje' => 'Ya existe esta trivia'];
                    $respuesta = json_encode($mensaje);
                    echo $respuesta;
                }else{
                    $consulta = "UPDATE trivias SET nombreTrivia='$nombreTrivia', descripcionTrivia='$descripcionTrivia' WHERE `id-trivia`='$id'";
                    $resultado = mysqli_query($conexion,$consulta)
                    or die($mensajeErrorActualizar);
                    $mensaje = ['exito'=> 'si','mensaje' => 'Trivia actualizada con éxito'];
                    $respuesta = json_encode($mensaje);
                    echo $respuesta;
                }
                break;

            case 'editarPregunta':
                $errorActualizar = ['exito' => 'no', 'mensaje' => 'Error al actualizar'];
                $mensajeErrorActualizar = json_encode($errorActualizar);
                $id = $datos_post['id'];
                $codigoPregunta = $datos_post['codigoPregunta'];
                $nombrePregunta = $datos_post['nombrePregunta'];
                $consulta = "SELECT * FROM preguntas WHERE codigoPregunta = '$codigoPregunta'  AND `id-pregunta` != '$id'";
                $mostrar = mysqli_query($conexion,$consulta)
                or die($mensajeErrorConsulta);
                $row_cnt = mysqli_num_rows($mostrar);           
                if ($row_cnt > 0) {
                    $mensaje = ['exito'=> 'no','mensaje' => 'Ya existe este código, asociado a una Pregunta'];
                    $respuesta = json_encode($mensaje);
                    echo $respuesta;
                }else{
                    $consulta = "UPDATE preguntas SET codigoPregunta = '$codigoPregunta', pregunta='$nombrePregunta' WHERE `id-pregunta`='$id'";
                    $resultado = mysqli_query($conexion,$consulta)
                    or die($mensajeErrorActualizar);
                    $mensaje = ['exito'=> 'si','mensaje' => 'Pregunta actualizada con éxito'];
                    $respuesta = json_encode($mensaje);
                    echo $respuesta;
                }
                break;
            
            case 'editarJuegoSemanal':
                $errorConsulta = ['exito' => 'no', 'mensaje' => 'Error al consultar'];
                $mensajeErrorConsulta = json_encode($errorConsulta);
                $errorActualizar = ['exito' => 'no', 'mensaje' => 'Error al actualizar'];
                $mensajeErrorActualizar = json_encode($errorActualizar);
                $id = $datos_post['id'];
                $nombreJuegoSemanal = $datos_post['nombreJuegoSemanal'];
                $metaPuntaje = $datos_post['metaPuntaje'];
                $idTrivia = $datos_post['idTrivia'];
                $idPalabraSemanal = $datos_post['idPalabraSemanal'];
                $consulta = "SELECT * FROM `juego-semanal` WHERE nombreJuegoSemanal ='$nombreJuegoSemanal' AND `id-juego-semanal` != '$id'";
                $mostrar = mysqli_query($conexion,$consulta)
                or die($mensajeErrorConsulta);
                $row_cnt = mysqli_num_rows($mostrar);           
                if ($row_cnt > 0) {
                    $mensaje = ['exito'=> 'no','mensaje' => 'Ya existe este Juego Semanal'];
                    $respuesta = json_encode($mensaje);
                    echo $respuesta;
                }else{
                    $consulta = "UPDATE `juego-semanal` SET nombreJuegoSemanal='$nombreJuegoSemanal', metaPuntaje = '$metaPuntaje', `id-trivia`='$idTrivia', `id-palabra-semanal`='$idPalabraSemanal' WHERE `id-juego-semanal`='$id'";
                    $resultado = mysqli_query($conexion,$consulta)
                    or die($mensajeErrorActualizar);
                    $mensaje = ['exito'=> 'si','mensaje' => 'Juego Semanal actualizado con éxito'];
                    $respuesta = json_encode($mensaje);
                    echo $respuesta;
                }
                break;

            case 'editarSemana':
                $errorConsulta = ['exito' => 'no', 'mensaje' => 'Error al consultar'];
                $mensajeErrorConsulta = json_encode($errorConsulta);
                $errorActualizar = ['exito' => 'no', 'mensaje' => 'Error al actualizar'];
                $mensajeErrorActualizar = json_encode($errorActualizar);
                $id = $datos_post['id'];
                $tituloSemana = $datos_post['tituloSemana'];
                $bajadaSemana = $datos_post['bajadaSemana'];
                $idMedalla = $datos_post['idMedalla'];
                $idJuegoSemanal = $datos_post['idJuegoSemanal'];
                $idVideoSemanal = $datos_post['idVideoSemanal'];
                $consulta = "SELECT * FROM semanas WHERE `tituloSemana` = '$tituloSemana' AND `id-semana` != '$id'";
                $mostrar = mysqli_query($conexion,$consulta)
                or die($mensajeErrorConsulta);
                $row_cnt = mysqli_num_rows($mostrar);           
                if ($row_cnt > 0) {
                    $mensaje = ['exito'=> 'no','mensaje' => 'Ya existe esta Semana'];
                    $respuesta = json_encode($mensaje);
                    echo $respuesta;
                }else{
                    $consulta = "UPDATE semanas SET tituloSemana='$tituloSemana', bajadaSemana='$bajadaSemana', `id-medalla`='$idMedalla', `id-juego-semanal`='$idJuegoSemanal',`id-video-semanal` = '$idVideoSemanal'  WHERE `id-semana`='$id'";
                    $resultado = mysqli_query($conexion,$consulta)
                    or die($mensajeErrorActualizar);
                    $mensaje = ['exito'=> 'si','mensaje' => 'Semana actualizada con éxito'];
                    $respuesta = json_encode($mensaje);
                    echo $respuesta;
                }
                break;

            
            case 'editarProgramaNivel':
                $errorConsulta = ['exito' => 'no', 'mensaje' => 'Error al consultar'];
                $mensajeErrorConsulta = json_encode($errorConsulta);
                $errorActualizar = ['exito' => 'no', 'mensaje' => 'Error al actualizar'];
                $mensajeErrorActualizar = json_encode($errorActualizar);
                $id = $datos_post['id'];
                $idNivel = $datos_post['idNivel'];
                $nombreProgramaNivel = $datos_post['nombreProgramaNivel'];
                $descripcionProgramaNivel = $datos_post['descripcionProgramaNivel'];
                $consulta = "SELECT * FROM `programa-nivel` WHERE nombreProgramaNivel= '$nombreProgramaNivel' AND `id-programa-nivel` != '$id'";
                $mostrar = mysqli_query($conexion,$consulta)
                or die($mensajeErrorConsulta);
                $row_cnt = mysqli_num_rows($mostrar);           
                if ($row_cnt > 0) {
                    $mensaje = ['exito'=> 'no','mensaje' => 'Ya existe este Programa'];
                    $respuesta = json_encode($mensaje);
                    echo $respuesta;
                }else{
                    $consulta = "UPDATE `programa-nivel` SET `id-nivel`='$idNivel', nombreProgramaNivel='$nombreProgramaNivel', descripcionProgramaNivel='$descripcionProgramaNivel' WHERE `id-programa-nivel`='$id'";
                    $resultado = mysqli_query($conexion,$consulta)
                    or die($mensajeErrorActualizar);
                    $mensaje = ['exito'=> 'si','mensaje' => 'Programa Nivel actualizado con éxito'];
                    $respuesta = json_encode($mensaje);
                    echo $respuesta;
                }
                break;

            case 'editarSemanaProgramaNivel': 
                         
                $errorActualizar = ['exito' => 'no', 'mensaje' => 'Error al actualizar'];
                $mensajeErrorActualizar = json_encode($errorConsulta);
                $id = $datos_post['id'];
                $numeroSemanaPrograma = $datos_post['numeroSemanaPrograma'];
                $idProgramaNivel = $datos_post['idProgramaNivel'];
                $idSemana = $datos_post['idSemana'];
                $fechaInicioSemana = $datos_post['fechaInicioSemana'];
                $fechaTerminoSemana = $datos_post['fechaTerminoSemana'];
                $consulta = "SELECT * FROM `semana-programa-nivel` WHERE `id-programa-nivel`= '$idProgramaNivel' AND `id-semana` = '$idSemana' AND `id-semana-programa-nivel` != '$id'";
                $mostrar = mysqli_query($conexion,$consulta)
                or die($mensajeErrorConsulta);
                $row_cnt = mysqli_num_rows($mostrar);           
                if ($row_cnt > 0) {
                    $mensaje = ['exito'=> 'no','mensaje' => 'Esta semana ya está asociada a este programa'];
                    $respuesta = json_encode($mensaje);
                    echo $respuesta;
                }else{
                    $consulta = "UPDATE `semana-programa-nivel` SET `numeroSemana-programa`='$numeroSemanaPrograma', `id-semana`='$idSemana', `id-programa-nivel`='$idProgramaNivel', fechaInicioSemana='$fechaInicioSemana', FechaFinSemana='$fechaTerminoSemana' WHERE `id-semana-programa-nivel`='$id'";
                    $resultado = mysqli_query($conexion,$consulta)
                    or die($mensajeErrorActualizar);
                    $mensaje = ['exito'=> 'si','mensaje' => 'Semana Programa Nivel actualizada con éxito'];
                    $respuesta = json_encode($mensaje);
                    echo $respuesta;
                }
                break;

            case 'cambiarEstadoMensaje':
                $errorActualizar = ['exito' => 'no', 'mensaje' => 'Error al actualizar'];
                $mensajeErrorActualizar = json_encode($errorConsulta);
                $id = $datos_post['id'];
                $estadoMensajeProgramaNivel = $datos_post['estadoMensajeProgramaNivel'];
                $consulta = "UPDATE `mensajes-semana-programa-nivel` SET estadoMensajeProgramaNivel='$estadoMensajeProgramaNivel' WHERE `id-mensaje-semana-programa-nivel`='$id'";
                $resultado = mysqli_query($conexion,$consulta)
                or die($mensajeErrorActualizar);
                $mensaje = ['exito'=> 'si','mensaje' => 'Estado del mensaje, actualizado con éxito'];
                $respuesta = json_encode($mensaje);
                echo $respuesta;
                break;

            case 'cambiarEstadoProfesorProgramaNivel':
                $errorActualizar = ['exito' => 'no', 'mensaje' => 'Error al actualizar'];
                $mensajeErrorActualizar = json_encode($errorConsulta);
                $id = $datos_post['id'];
                $estadoAsociacionProfesorProgramaNivel = $datos_post['estadoAsociacionProfesorProgramaNivel'];
                $consulta = "UPDATE `profesores-programa-nivel` SET estadoAsociacionProfesorProgramaNivel='$estadoAsociacionProfesorProgramaNivel' WHERE `id-profesor-programa-nivel`='$id'";
                $resultado = mysqli_query($conexion,$consulta)
                or die($mensajeErrorActualizar);
                $mensaje = ['exito'=> 'si','mensaje' => 'Estado del Profesor, actualizado con éxito'];
                $respuesta = json_encode($mensaje);
                echo $respuesta;
                break;

            case 'cambiarEstadoAsociacionCorporacionInstitucion':
                $errorActualizar = ['exito' => 'no', 'mensaje' => 'Error al actualizar'];
                $mensajeErrorActualizar = json_encode($errorConsulta);
                $id = $datos_post['id'];
                $estadoAsociacion = $datos_post['estadoAsociacion'];
                $consulta = "UPDATE `corporacion-institucion` SET estado='$estadoAsociacion' WHERE `id-corporacion-institucion`='$id'";
                $resultado = mysqli_query($conexion,$consulta)
                or die($mensajeErrorActualizar);
                $mensaje = ['exito'=> 'si','mensaje' => 'Estado de Asociación, actualizado con éxito'];
                $respuesta = json_encode($mensaje);
                echo $respuesta;
                break;
            
            case 'cambiarEstadoAsociacionTriviaPregunta':
                $errorActualizar = ['exito' => 'no', 'mensaje' => 'Error al actualizar'];
                $mensajeErrorActualizar = json_encode($errorConsulta);
                $id = $datos_post['id'];
                $estadoAsociacion = $datos_post['estadoAsociacion'];
                $consulta = "UPDATE `preguntas-trivias` SET estado='$estadoAsociacion' WHERE `id-pregunta-trivia`='$id'";
                $resultado = mysqli_query($conexion,$consulta)
                or die($mensajeErrorActualizar);
                $mensaje = ['exito'=> 'si','mensaje' => 'Estado de Asociación, actualizado con éxito'];
                $respuesta = json_encode($mensaje);
                echo $respuesta;
                break;
            
            case 'cambiarEstadoAsociacionJuegoSemanalMinijuego':
                $errorActualizar = ['exito' => 'no', 'mensaje' => 'Error al actualizar'];
                $mensajeErrorActualizar = json_encode($errorConsulta);
                $id = $datos_post['id'];
                $estadoAsociacion = $datos_post['estadoAsociacion'];
                $consulta = "UPDATE `minijuegos-juego-semanal` SET estado='$estadoAsociacion' WHERE `idminijuego-juego-semanal`='$id'";
                $resultado = mysqli_query($conexion,$consulta)
                or die($mensajeErrorActualizar);
                $mensaje = ['exito'=> 'si','mensaje' => 'Estado de Asociación, actualizado con éxito'];
                $respuesta = json_encode($mensaje);
                echo $respuesta;
                break;

            case 'cambiarEstadoInstruccionJuegoSemanal':
                $errorActualizar = ['exito' => 'no', 'mensaje' => 'Error al actualizar'];
                $mensajeErrorActualizar = json_encode($errorConsulta);
                $id = $datos_post['id'];
                $estadoAsociacion = $datos_post['estadoAsociacion'];
                $consulta = "UPDATE `instrucciones-juego-semanal` SET estado='$estadoAsociacion' WHERE `id-instruccion-juego-semanal`='$id'";
                $resultado = mysqli_query($conexion,$consulta)
                or die($mensajeErrorActualizar);
                $mensaje = ['exito'=> 'si','mensaje' => 'Estado de Asociación, actualizado con éxito'];
                $respuesta = json_encode($mensaje);
                echo $respuesta;
                break;
            
            case 'cambiarEstadoInstruccionSemana':
                $errorActualizar = ['exito' => 'no', 'mensaje' => 'Error al actualizar'];
                $mensajeErrorActualizar = json_encode($errorConsulta);
                $id = $datos_post['id'];
                $estadoAsociacion = $datos_post['estadoAsociacion'];
                $consulta = "UPDATE instrucciones SET estado='$estadoAsociacion' WHERE `id-instruccion`='$id'";
                $resultado = mysqli_query($conexion,$consulta)
                or die($mensajeErrorActualizar);
                $mensaje = ['exito'=> 'si','mensaje' => 'Estado de Asociación, actualizado con éxito'];
                $respuesta = json_encode($mensaje);
                echo $respuesta;
                break;


            default:
                break;
        }
    }

    
    
?>