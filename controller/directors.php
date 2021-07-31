<?php

    require_once('../config/database.php');   
    require_once('../data/DirectorDB.php');
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
        header('Access-Control-Allow-Methods: POST, OPTIONS, GET, PATCH');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Max-Age: 86400');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        $response->sendParams(true, 200);
    }

    switch($_SERVER['REQUEST_METHOD']){
        case 'POST':
            if($_SERVER['CONTENT_TYPE']!== 'application/json'){
                $response->sendParams(false, 400, 'Content Type header no Válido');
            }
            
            $rawPostData = file_get_contents('php://input');

            if(!$jsonData = json_decode($rawPostData)){
                $response->sendParams(false, 400, 'Body no es Válido (JSON)');
            }

            if( !($jsonData->DIRECTOR_NAME) || !($jsonData->DIRECTOR_BIRTHDAY)){
                $messages = array();

                (!($jsonData->DIRECTOR_NAME) ? $messages[] = 'EL nombre no fue ingresado': false);
                (!($jsonData->DIRECTOR_BIRTHDAY) ? $messages[] = 'La fecha de nacimiento no fue ingresado': false);

                $response->sendParams(false,400, $messages);

            }

            $DIRECTOR_NAME = trim($jsonData->DIRECTOR_NAME);
            $DIRECTOR_BIRTHDAY = trim($jsonData->DIRECTOR_BIRTHDAY);

            try{
                $directorDB = new DirectorDB($database);
                $existingdirector = $directorDB ->obtenerPorName($DIRECTOR_NAME,$DIRECTOR_BIRTHDAY);
                $rowCount = count($existingdirector);

                if($rowCount !==0){
                    $response->sendParams(false, 409, 'Este Director ya se encuentra registrado');
                }
                
                $director = new Directors(null, $DIRECTOR_NAME, $DIRECTOR_BIRTHDAY);
                $rowCount = $directorDB->insertar($director);

                if($rowCount === 0){
                    $response->sendParams(false, 404, 'Hubo un error al recuperar el Director creado');
                }

                $returnData = array();
                $returnData['rows_returned'] = $rowCount;
                //$returnData['directors'] = $lastdirectors;

                $response->sendParams(true, 201, 'Director insertado correctamente', $returnData);

            }

            catch(DirectorsException $ex){
                $response->sendParams(false, 400, $ex->getMessage());
            }
            catch(PDOException $ex){
                error_log("Database query error - {$ex}", 0);
                $response->sendParams(false, 500, 'Error al insertar Director');
            }
            break;


            case 'GET':
                if(isset($_GET['DIRECTOR_ID'])) {
                    try {
                        $directorDB = new DirectorDB($database);
                        $data = $directorDB->obtenerPorId($_GET['DIRECTOR_ID']);
                        $rowCount = count($data);
            
                        if($rowCount === 0){
                            $response->sendParams(false, 404, 'Hubo un error al recuperar el Director');
                        }
                        $returnData = array();
                        $returnData['directors'] = $data;
                        $response->sendParams(true, 201,null,$returnData); //201->Recurso creado
                    }
                    catch(DirectorsException $ex){
                        $response->sendParams(false, 400, $ex->getMessage());
                    }
                    catch(PDOException $ex){
                        error_log("Database query error - {$ex}", 0);
                        $response->sendParams(false, 500);
                    }
                }

                else{
                    try{
                        $directorDB = new DirectorDB($database);
                        $data = $directorDB->obtenerTodosDirectores();
                        $rowCount = count($data);
            
                        if($rowCount === 0){
                            $response->sendParams(false, 404, 'Hubo un error al recuperar los Directores');
                        }
                        $returnData = array();
                        $returnData['directors'] = $data;
                        $response->sendParams(true, 201,null, $returnData); //201->Recurso creado
                    }
                    catch(DirectorsException $ex){
                        $response->sendParams(false, 400, $ex->getMessage());
                    }
                    catch(PDOException $ex){
                        error_log("Database query error - {$ex}", 0);
                        $response->sendParams(false, 500);
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
    
                $DIRECTOR_NAME = trim($jsonData->DIRECTOR_NAME);
                $DIRECTOR_BIRTHDAY = trim($jsonData->DIRECTOR_BIRTHDAY);
                $DIRECTOR_ID = $jsonData->DIRECTOR_ID;
                try{
                    $directorDB = new DirectorDB($database);
                    $existingdirector = $directorDB ->obtenerPorId($DIRECTOR_ID);
                    $rowCount = count($existingdirector);
    
                    if($rowCount === 0){
                        $response->sendParams(false, 409, 'Este Director no se encuentra registrado');
                    }
                    
                    $director = new Directors($DIRECTOR_ID, $DIRECTOR_NAME, $DIRECTOR_BIRTHDAY);
                    
                    //$rowCount = $directorDB->insertar($director);
                    if($DIRECTOR_NAME!=null)
                    {
                        $rowCount = $directorDB->actualizarFullnamePorId($DIRECTOR_NAME, $DIRECTOR_ID);
                    }
                    if($rowCount === 0){
                        $response->sendParams(false, 404, 'ERROR! Ingrese un nombre diferente al registrado, o deje la casilla en blanco');
                    }

                    if($DIRECTOR_BIRTHDAY!=null)
                    {
                        $rowCount = $directorDB->actualizarBirthdayPorId($director);
                    }

                    if($rowCount === 0){
                        $response->sendParams(false, 404, 'ERROR! Ingrese un fecha diferente a la registrada, o deje la casilla en blanco');
                    }

                    if( !($jsonData->DIRECTOR_NAME) || !($jsonData->DIRECTOR_BIRTHDAY)){
                        $messages = array();
        
                        (!($jsonData->DIRECTOR_NAME) ? $messages[] = 'EL nombre no fue ingresado': false);
                        (!($jsonData->DIRECTOR_BIRTHDAY) ? $messages[] = 'La fecha de nacimiento no fue ingresado': false);
                        $response->sendParams(false,400, $messages);
                    }
                    $returnData = array();
    
                    $response->sendParams(true, 201, 'Los datos fueron modificados correctamente', $returnData);
    
                }
    
                catch(DirectorsException $ex){
                    $response->sendParams(false, 400, $ex->getMessage());
                }
                catch(PDOException $ex){
                    error_log("Database query error - {$ex}", 0);
                    $response->sendParams(false, 500, 'Error al insertar Director');
                }
                break;
            default: 
                $response->sendParams(false, 405, 'Tipo de petición no permitida');
                break;




    }

















