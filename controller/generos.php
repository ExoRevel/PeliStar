<?php

    require_once('../config/database.php');   
    require_once('../data/GeneroDB.php');
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
    header('Access-Control-Allow-Origin: *');
    //Opciones de preflight (CORS)
    if($_SERVER['REQUEST_METHOD'] === 'OPTIONS'){
        header('Access-Control-Allow-Methods: POST, OPTIONS, GET, PATCH');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Max-Age: 86400');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
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

            if( !($jsonData->GENERO_NAME)){
                $messages = array();

                (!($jsonData->GENERO_NAME) ? $messages[] = 'Genero_Name no ingresado': false);

                $response->sendParams(false,400, $messages);

            }

            $GENERO_NAME = trim($jsonData->GENERO_NAME);

            try{
                $generoDB = new GeneroDB($database);
                $existinggenero = $generoDB ->obtenerPorName($GENERO_NAME);
                $rowCount = count($existinggenero);

                if($rowCount !==0){
                    $response->sendParams(false, 409, 'Genero ya existe en la base de data');
                }
                
                $genero = new Generos(null, $GENERO_NAME);
                $rowCount = $generoDB->insertar($genero);

                if($rowCount === 0){
                    $response->sendParams(false, 404, 'Hubo un error al recuperar crear el genero');
                }

                $returnData = array();
                $returnData['rows_returned'] = $rowCount;
                $returnData['genero'] = $lastgeneros;

                $response->sendParams(true, 201, 'GENERO insertado correctamente', $returnData);

            }

            catch(GenerosException $ex){
                $response->sendParams(false, 400, $ex->getMessage());
            }
            catch(PDOException $ex){
                error_log("Database query error - {$ex}", 0);
                $response->sendParams(false, 500, 'Error al insertar GENERO ');
            }
            break;


            case 'GET':
                if(isset($_GET['GENERO_ID'])) {
                    try {
                        $generoDB = new GeneroDB($database);
                        $data = $generoDB->obtenerPorId($_GET['GENERO_ID']);
                        $rowCount = count($data);
            
                        if($rowCount === 0){
                            $response->sendParams(false, 404, 'Hubo un error al recuperar el GENERO');
                        }
                        $returnData = array();
                        $returnData['generos'] = $data;
                        $response->sendParams(true, 201,null,$returnData); //201->Recurso creado
                    }
                    catch(GenerosException $ex){
                        $response->sendParams(false, 400, $ex->getMessage());
                    }
                    catch(PDOException $ex){
                        error_log("Database query error - {$ex}", 0);
                        $response->sendParams(false, 500);
                    }
                }

                else{
                    try{
                        $generoDB = new GeneroDB($database);
                        $data = $generoDB->obtenerTodosGeneros();
                        $rowCount = count($data);
            
                        if($rowCount === 0){
                            $response->sendParams(false, 404, 'Hubo un error al recuperar los Directores');
                        }
                        $returnData = array();
                        $returnData['generos'] = $data;
                        $response->sendParams(true, 201,null, $returnData); //201->Recurso creado
                    }
                    catch(GenerosException $ex){
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
    
                $GENERO_NAME = trim($jsonData->GENERO_NAME);
                $GENERO_ID = $jsonData->GENERO_ID;
                try{
                    $generoDB = new GeneroDB($database);
                    $existinggenero = $generoDB ->obtenerPorId($GENERO_ID);
                    $rowCount = count($existinggenero);
    
                    if($rowCount === 0){
                        $response->sendParams(false, 409, 'Este Genero no existe en la base de data');
                    }

                    if($GENERO_NAME!=null)
                    {
                        $rowCount = $generoDB->actualizarNamePorId($GENERO_NAME, $GENERO_ID);
                    }
    
                    if($rowCount === 0){
                        $response->sendParams(false, 404, 'ERROR! Ingrese un nombre diferente al registrado');
                    }

                    if( !($jsonData->GENERO_NAME)){
                        $messages = array();
                        (!($jsonData->GENERO_NAME) ? $messages[] = 'El nuevo nombre no fue ingresado': false);
                        $response->sendParams(false,400, $messages);
                    }
                    $returnData = array();
                    $response->sendParams(true, 201, 'Los datos fueron modificados correctamente', $returnData);
    
                }
    
                catch(GenerosException $ex){
                    $response->sendParams(false, 400, $ex->getMessage());
                }
                catch(PDOException $ex){
                    error_log("Database query error - {$ex}", 0);
                    $response->sendParams(false, 500, 'Este nombre de Genero ya se encuentra registrado ');
                }
                break;
            default: 
                $response->sendParams(false, 405, 'Tipo de petición no permitida');
                break;


    }