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
        header('Access-Control-Allow-Methods: POST, OPTIONS, GET');
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

            if( !isset($jsonData->DIRECTOR_NAME) || !isset($jsonData->DIRECTOR_BIRTHDAY)){
                $messages = array();

                (!isset($jsonData->DIRECTOR_NAME) ? $messages[] = 'Director_Name no ingresado': false);
                (!isset($jsonData->DIRECTOR_BIRTHDAY) ? $messages[] = 'Campo DIRECTOR_BIRTHDAY no ingresado': false);

                $response->sendParams(false,400, $messages);

            }

            $DIRECTOR_NAME = trim($jsonData->DIRECTOR_NAME);
            $DIRECTOR_BIRTHDAY = trim($jsonData->DIRECTOR_BIRTHDAY);

            try{
                $directorDB = new DirectorDB($database);
                $existingdirector = $directorDB ->obtenerPorName($DIRECTOR_NAME);
                $rowCount = count($existingdirector);

                if($rowCount !==0){
                    $response->sendParams(false, 409, 'DIRECTOR ya existe en la base de data');
                }
                
                $director = new Directors(null, $DIRECTOR_NAME, $DIRECTOR_BIRTHDAY);
                $rowCount = $directorDB->insertar($director);

                if($rowCount === 0){
                    $response->sendParams(false, 404, 'Hubo un error al recuperar el Director creado');
                }

                $returnData = array();
                $returnData['rows_returned'] = $rowCount;
                $returnData['directors'] = $lastdirectors;

                $response->sendParams(true, 201, 'DIRECTOR insertado correctamente', $returnData);

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
                            $response->sendParams(false, 404, 'Hubo un error al recuperar el Actor');
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
            default: 
                $response->sendParams(false, 405, 'Tipo de petición no permitida');
                break;




    }

















