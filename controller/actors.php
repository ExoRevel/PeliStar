<?php

    require_once('../config/database.php');   
    require_once('../data/ActorDB.php');
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
    header('Access-Control-Allow-Origin: *');
    //Opciones de preflight (CORS)
    if($_SERVER['REQUEST_METHOD'] === 'OPTIONS'){
        header('Access-Control-Allow-Methods: POST, OPTIONS, GET');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Max-Age: 86400');
        $response->sendParams(true, 200);
    }

    switch($_SERVER['REQUEST_METHOD']){
        case 'POST':
            /*if($_SERVER['CONTENT_TYPE']!== 'application/json'){
                $response->sendParams(false, 400, 'Content Type header no Válido');
            }*/

            $rawPostData = file_get_contents('php://input');

            if(!$jsonData = json_decode($rawPostData)){
                $response->sendParams(false, 400, 'Body no es Válido (JSON)');
            }

            if( !isset($jsonData->ACTOR_FULLNAME) || !isset($jsonData->ACTOR_BIRTHDAY)){
                $messages = array();

                (!isset($jsonData->ACTOR_FULLNAME) ? $messages[] = 'ACTOR FULLNAME NO INGRESADO': false);
                (!isset($jsonData->ACTOR_BIRTHDAY) ? $messages[] = 'Campo ACTOR_BIRTHDAY no ingresado': false);

                $response->sendParams(false,400, $messages);

            }

            $ACTOR_FULLNAME = trim($jsonData->ACTOR_FULLNAME);
            $ACTOR_BIRTHDAY = trim($jsonData->ACTOR_BIRTHDAY);

            try{
                $actorDB = new ActorDB($database);
                $existingactor = $actorDB ->obtenerPorFullName($ACTOR_FULLNAME);
                $rowCount = count($existingactor);

                if($rowCount !==0){
                    $response->sendParams(false, 409, 'ACTOR ya existe en la base de data');
                }
                
                $actor = new Actors(null, $ACTOR_FULLNAME, $ACTOR_BIRTHDAY);
                $rowCount = $actorDB->insertar($actor);

                if($rowCount === 0){
                    $response->sendParams(false, 404, 'Hubo un error al recuperar el Actor creado');
                }

                $returnData = array();
                $returnData['rows_returned'] = $rowCount;
                $returnData['actors'] = $lastactors;

                $response->sendParams(true, 201, 'ACTOR insertado correctamente', $returnData);
            }

            catch(ActorsException $ex){
                $response->sendParams(false, 400, $ex->getMessage());
            }
            catch(PDOException $ex){
                error_log("Database query error - {$ex}", 0);
                $response->sendParams(false, 500, 'Error al insertar crear la cuenta de Actor');
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
                        $response->sendParams(false, 500);
                    }
                }

                else{
                    try{
                        $actorDB = new ActorDB($database);
                        $data = $actorDB->obtenerTodosActores();
                        $rowCount = count($data);
            
                        if($rowCount === 0){
                            $response->sendParams(false, 404, 'Hubo un error al recuperar los ACTORES');
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
                        $response->sendParams(false, 500);
                    }
                }
                break;
            default: 
                $response->sendParams(false, 405, 'Tipo de petición no permitida');
                break;
          
}



