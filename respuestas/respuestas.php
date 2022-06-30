<?php            
    // json falla consulta
    $queryFailedJson =  json_encode(['type' => 'error' ,'code' => '500', 'message' => 'falla al consultar']);
    // json falla al insertar
    $insertFailedJSON =  json_encode(['type' => 'error' ,'code' => '500', 'message' => 'falla al insertar']);
    // json respuesta consulta vacia
    $noUserJson = json_encode(['type' => 'error' ,'code' => '401', 'message' => 'Credenciales Incorrectas']);
    // json respuesta token expirado
    $tokenExpiredJson = json_encode(['type' => 'error' ,'code' => '401', 'message' => 'Token Expirado']);
    ?>