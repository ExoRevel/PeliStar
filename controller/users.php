<?php

    require_once('../config/database.php');   
    require_once('../data/UserDB.php');
    require_once('../util/response.php');

    //Respuesta que se enviará al cliente    
    $response = new Response();

    //Conexión a la base de data
    try{
        $database = Database::connectDB();      
    }
    catch(PDOException $ex){
        error_log("Connection error - {$ex}", 0);       
        $response->sendParams(false, 500, 'Error de conexión a la base de data');
    }

    //Opciones de preflight (CORS)
    if($_SERVER['REQUEST_METHOD'] === 'OPTIONS'){
        header('Access-Control-Allow-Methods: POST, OPTIONS, GET');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        header('Access-Control-Max-Age: 86400');
        $response->sendParams(true, 200);
    }

    switch($_SERVER['REQUEST_METHOD']) {
        case 'POST':
            if($_SERVER['CONTENT_TYPE'] !== 'application/json'){
                $response->sendParams(false, 400, 'Content type header no válido');
            }
    
            $rawPostData = file_get_contents('php://input');
    
            if(!$jsonData = json_decode($rawPostData)){ //Si no se pudo decodificar el body a un objeto JavaScript
                $response->sendParams(false, 400, 'Body no es válido (JSON)');
            }
    
            if(!isset($jsonData->USE_FULLNAME) || !isset($jsonData->USE_USERNAME) || !isset($jsonData->USE_PASSWORD)){
                $messages = array();
    
                (!isset($jsonData->USE_FULLNAME) ? $messages[] = 'Campo USE_FULLNAME no ingresado': false);
                (!isset($jsonData->USE_USERNAME) ? $messages[] = 'Campo USE_USERNAME no ingresado': false);
                (!isset($jsonData->USE_PASSWORD) ? $messages[] = 'Campo USE_PASSWORD no ingresado': false);
    
                $response->sendParams(false, 400, $messages);
            }
    
            $USE_FULLNAME = trim($jsonData->USE_FULLNAME);
            $USE_USERNAME = trim($jsonData->USE_USERNAME);
            $USE_PASSWORD = $jsonData->USE_PASSWORD;
    
            try{
                $userDB = new UserDB($database);
                $existingUser = $userDB->obtenerPorUsername($USE_USERNAME);
                $rowCount = count($existingUser);
    
                if($rowCount !== 0){
                    $response->sendParams(false, 409, 'Usuario ya existe en la base de data'); //409: Conflicto
                }
    
                $USE_PASSWORD = password_hash($USE_PASSWORD, PASSWORD_DEFAULT);
    
                $user = new User(3, $USE_FULLNAME, $USE_USERNAME, $USE_PASSWORD , null, null);
                $rowCount = $userDB->insertar($user);
    
                if($rowCount === 0){
                    $response->sendParams(false, 500, 'Hubo un error al intentar registrar el usuario');
                }
    
                $lastUser = $userDB->obtenerPorId($database->lastInsertId());
                $rowCount = count($lastUser);
    
                if($rowCount === 0){
                    $response->sendParams(false, 404, 'Hubo un error al recuperar el user creado');
                }
    
                $returnData = array();
                $returnData['rows_returned'] = $rowCount;
                $returnData['users'] = $lastUser;
    
                $response->sendParams(true, 201, 'User insertado correctamente', $returnData); //201->Recurso creado
            }
            catch(UserException $ex){
                $response->sendParams(false, 400, $ex->getMessage());
            }
            catch(PDOException $ex){
                error_log("Database query error - {$ex}", 0);
                $response->sendParams(false, 500, 'Error al intentar crear la cuenta de usuario');
            }
            break;
        
        case 'GET':
            if(isset($_GET['id'])) {
                try {
                    $userDB = new UserDB($database);
                    $lastUser = $userDB->obtenerPorId($_GET['id']);
                    $rowCount = count($lastUser);
        
                    if($rowCount === 0){
                        $response->sendParams(false, 404, 'Hubo un error al recuperar el user');
                    }
        
                    $returnData = array();
                    $returnData['rows_returned'] = $rowCount;
                    $returnData['users'] = $lastUser;
        
                    $response->sendParams(true, 201, 'Usuario', $returnData); //201->Recurso creado
                }
                catch(UserException $ex){
                    $response->sendParams(false, 400, $ex->getMessage());
                }
                catch(PDOException $ex){
                    error_log("Database query error - {$ex}", 0);
                    $response->sendParams(false, 500);
                }
            }
            break;
        default: 
            $response->sendParams(false, 405, 'Tipo de petición no permitida');
            break;
    }