<?php

    require_once('../config/database.php');   
    require_once('../data/ActorDB.php');
    require_once('../util/response.php');
    require_once('../util/auth.php'); 
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
    //header('Access-Control-Allow-Origin: *');
    //Opciones de preflight (CORS)
    if($_SERVER['REQUEST_METHOD'] === 'OPTIONS'){
        header('Access-Control-Allow-Methods: POST, OPTIONS, GET, PATCH');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Max-Age: 86400');
        $response->sendParams(true, 200);
    }
    //Authorization
    $user = checkAuthStatusAndReturnUser($database);  

    switch($_SERVER['REQUEST_METHOD']){
        case 'POST':
            if($_SERVER['CONTENT_TYPE']!== 'application/json'){
                $response->sendParams(false, 400, 'Content Type header no Válido');
            }

            $rawPostData = file_get_contents('php://input');

            if(!$jsonData = json_decode($rawPostData)){
                $response->sendParams(false, 400, 'Body no es Válido (JSON)');
            }

            if( !($jsonData->ACTOR_FULLNAME) || !($jsonData->ACTOR_BIRTHDAY)){
                $messages = array();

                (!($jsonData->ACTOR_FULLNAME) ? $messages[] = 'EL NOMBRE DEL ACTOR NO FUE INGRESADO': false);
                (!($jsonData->ACTOR_BIRTHDAY) ? $messages[] = 'LA FECHA DE NACIMIENTO DEL ACTOR NO FUE INGRESADA': false);

                $response->sendParams(false,400, $messages);

            }

            $ACTOR_FULLNAME = trim($jsonData->ACTOR_FULLNAME);
            $ACTOR_BIRTHDAY = trim($jsonData->ACTOR_BIRTHDAY);

            try{
                $actorDB = new ActorDB($database);
                $existingactor = $actorDB ->obtenerPorFullName($ACTOR_FULLNAME,$ACTOR_BIRTHDAY);
                $rowCount = count($existingactor);

                if($rowCount !==0){
                    $response->sendParams(false, 409, 'Este actor ya se encuentra registrado');
                }
                
                $actor = new Actors(null, $ACTOR_FULLNAME, $ACTOR_BIRTHDAY);
                $rowCount = $actorDB->insertar($actor);

                if($rowCount === 0){
                    $response->sendParams(false, 404, 'Hubo un error al recuperar el Actor creado');
                }

                $returnData = array();
                $returnData['rows_returned'] = $rowCount;
                //$returnData['actors'] = $lastactors;

                $response->sendParams(true, 201, 'El actor fue insertado correctamente', $returnData);
            }

            catch(ActorsException $ex){
                $response->sendParams(false, 400, $ex->getMessage());
            }
            catch(PDOException $ex){
                error_log("Database query error - {$ex}", 0);
                $response->sendParams(false, 500, 'Error al crear el Actor');
            }
            break;

            case 'GET':
                if(isset($_GET['ACTOR_ID'])) {
                    try {
                        $actorDB = new ActorDB($database);
                        $data = $actorDB->obtenerPorId($_GET['ACTOR_ID']);
                        $rowCount = count($data);
            
                        if($rowCount === 0){
                            $response->sendParams(false, 404, 'Hubo un error al recuperar el Actor');
                        }
                        $returnData = array();
                        $returnData['actors'] = $data;
                        $response->sendParams(true, 201,null,$returnData); //201->Recurso creado
                    }
                    catch(ActorsException $ex){
                        $response->sendParams(false, 400, $ex->getMessage());
                    }
                    catch(PDOException $ex){
                        error_log("Database query error - {$ex}", 0);
                        $response->sendParams(false, 500,"ERROR DE BASE DE DATOS");
                    }
                }

                else{
                    try{
                        $actorDB = new ActorDB($database);
                        $data = $actorDB->obtenerTodosActores();
                        $rowCount = count($data);
            
                        if($rowCount === 0){
                            $response->sendParams(false, 404, 'Hubo un error al recuperar los Actores');
                        }
                        $returnData = array();
                        $returnData['actors'] = $data;
                        $response->sendParams(true, 201,null, $returnData); //201->Recurso creado
                    }
                    catch(ActorsException $ex){
                        $response->sendParams(false, 400, $ex->getMessage());
                    }
                    catch(PDOException $ex){
                        error_log("Database query error - {$ex}", 0);
                        $response->sendParams(false, 500,"ERROR DE BASE DE DATOS");
                    }
                }
                break;
            case 'PATCH':
                if($_SERVER['CONTENT_TYPE']!== 'application/json'){
                    $response->sendParams(false, 400, 'Content Type header no Válido');
                }
    
                $rawPostData = file_get_contents('php://input');
    
                if(!$jsonData = json_decode($rawPostData)){
                    $response->sendParams(false, 400, 'Body no es Válido (JSON)');
                }
    
                $ACTOR_FULLNAME = trim($jsonData->ACTOR_FULLNAME);
                $ACTOR_BIRTHDAY = trim($jsonData->ACTOR_BIRTHDAY);
                $ACTOR_ID = $jsonData->ACTOR_ID;
                try{
                    $actorDB = new ActorDB($database);
                    $existingactor = $actorDB ->obtenerPorId($ACTOR_ID);
                    $rowCount = count($existingactor);
                    if($rowCount === 0){
                        $response->sendParams(false, 409, 'Este ACTOR no existe en la base de data');
                    }
                    $actor = new Actors($ACTOR_ID, $ACTOR_FULLNAME, $ACTOR_BIRTHDAY);
                    if($ACTOR_FULLNAME!=null)
                    {
                        $rowCount = $actorDB->actualizarFullnamePorId($ACTOR_FULLNAME, $ACTOR_ID);
                    }

                    if($rowCount === 0){
                        $response->sendParams(false, 404, 'ERROR! Ingrese un nombre diferente al registrado, o deje la casilla en blanco');
                    }

                    if($ACTOR_BIRTHDAY!=null)
                    {
                        $rowCount = $actorDB->actualizarBirthdayPorId($actor);
                    }
                    
                    if($rowCount === 0){
                        $response->sendParams(false, 404, 'ERROR! Ingrese un fecha diferente a la registrada, o deje la casilla en blanco');
                    }
                    if( !($jsonData->ACTOR_FULLNAME) && !($jsonData->ACTOR_BIRTHDAY)){
                        $messages = array();
                        (!($jsonData->ACTOR_FULLNAME) ? $messages[] = 'EL NOMBRE DEL ACTOR NO FUE INGRESADO': false);
                        (!($jsonData->ACTOR_BIRTHDAY) ? $messages[] = 'LA FECHA DE NACIMIENTO DEL ACTOR NO FUE INGRESADA': false);
                        $response->sendParams(false,400, $messages);
                    }

                    $returnData = array();
                    $response->sendParams(true, 201, 'Los datos fueron modificados correctamente', $returnData);
                }
                catch(ActorsException $ex){
                    $response->sendParams(false, 400, $ex->getMessage());
                }
                catch(PDOException $ex){
                    error_log("Database query error - {$ex}", 0);
                    $response->sendParams(false, 500, "ERROR DE BASE DE DATOS");
                }
                break;
            default: 
                $response->sendParams(false, 405, 'Tipo de petición no permitida');
                break;
          
}



