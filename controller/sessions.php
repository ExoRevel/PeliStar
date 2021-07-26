<?php

    require_once('../config/database.php');   
    require_once('../data/SessionDB.php');
    require_once('../data/UserDB.php');
    require_once('../util/response.php');

    function returnSessionToShow($session){
        //se elimina las claves SES_ACCTOKEXP y SES_REFTOKEXP porque como guardan la fecha y hora no se enviará esos datos al cliente ya que la fecha del servidor puede diferir al del cliente
        unset($session['SES_ACCTOKEXP']);
        unset($session['SES_REFTOKEXP']);
        //se crea nuevas claves y se le asigna la duración de SES_ACCTOKEXPT y SES_REFTOKEXPT
        $session['SES_ACCTOKEXPT'] = 1200;
        $session['SES_REFTOKEXPT'] = 1209600;

        return $session;
    }

    //Respuesta que se enviará al cliente    
    $response = new Response();

    //Conexión a la base de datos
    try{
        $database = Database::connectDB();      
    }
    catch(PDOException $ex){
        error_log("Connection error - {$ex}", 0);       
        $response->sendParams(false, 500, 'Error de conexión a la base de datos');
    }

    //Opciones de preflight (CORS)
    if($_SERVER['REQUEST_METHOD'] === 'OPTIONS'){
        header('Access-Control-Allow-Methods: POST, GET, PATCH, DELETE, OPTIONS');
        header('Access-Control-Allow-Origin "*"');
        header('Access-Control-Max-Age: 86400');
        $response->sendParams(true, 200);
    }

    //Manejo de la petición HTTP
    if(array_key_exists('SES_ID', $_GET)){
        $SES_ID = $_GET['SES_ID'];

        //Validaciones de los query parameters
        if($SES_ID == '' || !is_numeric($SES_ID)){
            $response->sendParams(false, 400, 'El campo SES_ID no puede estar en blanco y debe ser numérico');
        }

        if(!isset($_SERVER['HTTP_AUTHORIZATION']) || strlen($_SERVER['HTTP_AUTHORIZATION']) < 1 ){
            $response->sendParams(false, 401, 'El access token debe estar presente y no debe estar en blanco');
        }

        $SES_ACCTOK = $_SERVER['HTTP_AUTHORIZATION'];

        if($_SERVER['REQUEST_METHOD'] === 'DELETE'){
            try{
                $sessionDB = new SessionDB($database);
                $rowCount = $sessionDB->eliminarPorId($SES_ID, $SES_ACCTOK);
            
                if($rowCount === 0){
                    $response->sendParams(false, 400, 'No se pudo cerrar sesión usando el access token provisto');
                }

                $returnData = array();
                $returnData['SES_ID'] = intval($SES_ID);             

                $response->sendParams(true, 200, 'Sesión cerrada correctamente', $returnData);
            }
            catch(PDOException $ex){
                error_log("Database query error - {$ex}", 0);
                $response->sendParams(false, 500, $ex->getMessage());
            }            
        }
        else if($_SERVER['REQUEST_METHOD'] === 'PATCH'){

            if($_SERVER['CONTENT_TYPE'] !== 'application/json'){
                $response->sendParams(false, 400, 'Content type header no válido');
            }
    
            $rawPatchData = file_get_contents('php://input');
    
            if(!$jsonData = json_decode($rawPatchData)){ //Si no se pudo decodificar el body a un objeto JavaScript
                $response->sendParams(false, 400, 'Body no es válido (JSON)');
            }      
            
            if(!isset($jsonData->SES_REFTOK) || strlen($jsonData->SES_REFTOK) < 1){ //Se verifican campos obligatorios
                $response->sendParams(false, 400, 'El refresh token debe estar presente y no debe estar en blanco');
            }

            try{
                $SES_REFTOK = $jsonData->SES_REFTOK;

                $sessionDB = new SessionDB($database);
                $returnData = $sessionDB->obtenerUserPorSession($SES_ID, $SES_ACCTOK, $SES_REFTOK);

                $rowCount = count($returnData['SESSION']);
                    
                if($rowCount === 0){
                    $response->sendParams(false, 401, 'El access token o el refresh token es incorrecto');
                }

                $session = $returnData['SESSION'][0];
                $user = $returnData['USER'][0];            

                if($user['USE_ACTIVE'] == 0){
                    $response->sendParams(false, 401, 'La cuenta no se encuentra activa');
                }

                if($user['USE_LOGAT'] >= 3){
                    $response->sendParams(false, 401, 'La cuenta ha sido bloqueada');
                }

                $token_expiry_time = DateTime::createFromFormat('d/m/Y H:i', $session['SES_REFTOKEXP'])->getTimestamp();

                if($token_expiry_time < time()){
                    $response->sendParams(false, 401, 'El refresh token ha expirado. Inicia sesión nuevamente');
                }

                $SES_ACCTOK = base64_encode(bin2hex(openssl_random_pseudo_bytes(24)).time()); //Genera bytes aleatorios - Se convierte a hexadecimal - Se pasa a caracteres -Time para garantizar unicidad
                $SES_REFTOK = base64_encode(bin2hex(openssl_random_pseudo_bytes(24)).time()); //Genera bytes aleatorios - Se convierte a hexadecimal - Se pasa a caracteres -Time para garantizar unicidad
    
                $SES_ACCTOKEXP = date('d/m/Y H:i', time() + 1200); //20 mins
                $SES_REFTOKEXP = date('d/m/Y H:i', time() + 1209600); //14 dias

                //Se parcha la sesión
                $session = new Session($session['SES_ID'], $user['USE_ID'], $SES_ACCTOK, $SES_ACCTOKEXP, $SES_REFTOK, $SES_REFTOKEXP);
                $rowCount = $sessionDB->reemplazar($session, $user['USE_ID']);

                if($rowCount === 0){
                    $response->sendParams(false, 401, 'El access token no pudo ser actualizado. Inicia sesión nuevamente');
                }   
                
                $lastSession = $sessionDB->obtenerPorId($SES_ID);
                $rowCount = count($lastSession);
    
                if($rowCount === 0){
                    $response->sendParams(false, 404, 'Hubo un error al recuperar la sesión actualizada');
                }
    
                $returnData = array();
                $returnData['rows_returned'] = $rowCount;
                $lastSession[0]=returnSessionToShow($lastSession[0]);        
                $returnData['sessions'] = $lastSession;
    
                $response->sendParams(true, 200, 'Token actualizado correctamente', $returnData); 
            }
            catch(SessionException $ex){
                $response->sendParams(false, 400, $ex->getMessage());
            }
            catch(UserException $ex){
                $response->sendParams(false, 400, $ex->getMessage());
            }
            catch(PDOException $ex){
                error_log("Database query error - {$ex}", 0);
                $response->sendParams(false, 500, 'Hubo un error al intentar actualizar el access token');
            }               
        }
        else{
            $response->sendParams(false, 405, 'Tipo de petición no permitida');
        }
    }
    else if(empty($_GET)){
        if($_SERVER['REQUEST_METHOD'] !== 'POST'){
            $response->sendParams(false, 405, 'Tipo de petición no permitida');
        }

        sleep(1); //Se retrasa la ejecución del programa 1 segundo cada vez que el usuario intenta iniciar sesión

        if($_SERVER['CONTENT_TYPE'] !== 'application/json'){
            $response->sendParams(false, 400, 'Content type header no válido');
        }

        $rawPOSTData = file_get_contents('php://input');

        if(!$jsonData = json_decode($rawPOSTData)){ //Si no se pudo decodificar el body a un objeto JavaScript
            $response->sendParams(false, 400, 'Body no es válido (JSON)');
        }

        if(!isset($jsonData->USE_USERNAME) || !isset($jsonData->USE_PASSWORD)){ //Se verifican campos obligatorios
            $messages = array();

            (!isset($jsonData->USE_USERNAME) ? $messages[] = 'Campo USE_USERNAME no ingresado': false);
            (!isset($jsonData->USE_PASSWORD) ? $messages[] = 'Campo USE_PASSWORD no ingresado': false);

            $response->sendParams(false, 400, $messages);
        }

        $USE_USERNAME = trim($jsonData->USE_USERNAME);
        $USE_PASSWORD = $jsonData->USE_PASSWORD;       

        try{
            $userDB = new UserDB($database);
            $foundUser = $userDB->obtenerPorUsername($USE_USERNAME); 
            $rowCount = count($foundUser);

            if($rowCount === 0){
                $response->sendParams(false, 401, 'Usuario o contraseña incorrectos'); //Debería ser 404 pero se envía 401 para "engañar" a potenciales hackers
            }

            //Las funciones de obtención siempre devuelven un arreglo.

            $foundUser = $foundUser[0]; // Se extrae el primer y único elemento

            if($foundUser['USE_ACTIVE'] == 0){
                $response->sendParams(false, 401, 'La cuenta no está activa');
            }

            if($foundUser['USE_LOGAT'] >= 3){
                $response->sendParams(false, 401, 'La cuenta ha sido bloqueada');
            }

            if(!password_verify($USE_PASSWORD, $foundUser['USE_PASSWORD'])){
                $userDB->incrementarAttemps($foundUser['USE_ID']);
            
                $response->sendParams(false, 401, 'Usuario o contraseña incorrectos');   
            } 

            $SES_ACCTOK = base64_encode(bin2hex(openssl_random_pseudo_bytes(24)).time()); //Genera bytes aleatorios - Se convierte a hexadecimal - Se pasa a caracteres -Time para garantizar unicidad
            $SES_REFTOK = base64_encode(bin2hex(openssl_random_pseudo_bytes(24)).time()); //Genera bytes aleatorios - Se convierte a hexadecimal - Se pasa a caracteres -Time para garantizar unicidad

            $SES_ACCTOKEXP = date('d/m/Y H:i', time() + 1200); //20 mins
            $SES_REFTOKEXP = date('d/m/Y H:i', time() + 1209600); //14 dias
        }
        catch(UserException $ex){
            $response->sendParams(false, 400, $ex->getMessage());
        }
        catch(PDOException $ex){
            error_log("Database query error - {$ex}", 0);
            $response->sendParams(false, 500, 'Error al intentar loguear al usuario');
        }
        
        try{
            $database->beginTransaction();
            $userDB->reiniciarAttemps($foundUser['USE_ID']);
          
            //Se inserta una nueva sesión
            $sessionDB = new SessionDB($database);
            $session = new Session(null, $foundUser['USE_ID'], $SES_ACCTOK, $SES_ACCTOKEXP, $SES_REFTOK, $SES_REFTOKEXP);
            $sessionDB->insertar($session);
            $lastSessionId = $database->lastInsertId();

            $database->commit();

            $lastSession = $sessionDB->obtenerPorId($lastSessionId);
            $rowCount = count($lastSession);

            if($rowCount === 0){
                $response->sendParams(false, 404, 'Hubo un error al recuperar la sesión creada');
            }

            $returnData = array();
            $returnData['rows_returned'] = $rowCount;
            $lastSession[0]=returnSessionToShow($lastSession[0]);            
            $returnData['sessions'] = $lastSession;

            $response->sendParams(true, 201, 'Sesión creada correctamente', $returnData); //201->Recurso creado
        }
        catch(SessionException $ex){
            if($database->inTransaction())
                $database->rollBack(); //Si ocurre alguna excepcion a estas alturas, se regresa todo a como estaba antes

            $response->sendParams(false, 400, $ex->getMessage());
        }
        catch(PDOException $ex){
            error_log("Database query error - {$ex}", 0);
            
            if($database->inTransaction())
                $database->rollBack(); //Si ocurre alguna excepcion a estas alturas, se regresa todo a como estaba antes

            $response->sendParams(false, 500, 'Error al intentar loguear al usuario');
        }
    }
    else{
        $response->sendParams(false, 404, 'Endpoint no encontrado');
    }
