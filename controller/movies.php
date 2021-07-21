<?php

    require_once('../config/database.php');   
    require_once('../data/MovieDB.php');
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

    switch($_SERVER['REQUEST_METHOD']){
    
    
    
    
        case 'GET':
            if(isset($_GET['MOVIE_ID'])) {
                try {
                    $movieDB = new MovieDB($database);
                    $data = $movieDB->obtenerPorId($_GET['MOVIE_ID']);
                    $rowCount = count($data);
        
                    if($rowCount === 0){
                        $response->sendParams(false, 404, 'Hubo un error al recuperar The Movie ');
                    }
                    $returnData = array();
                    $returnData['movies'] = $data;
                    $response->sendParams(true, 201,null,$returnData); //201->Recurso creado
                }
                catch(MoviesException $ex){
                    $response->sendParams(false, 400, $ex->getMessage());
                }
                catch(PDOException $ex){
                    error_log("Database query error - {$ex}", 0);
                    $response->sendParams(false, 500);
                }
            }

            else{
                try{
                    $movieDB = new MovieDB($database);
                    $data = $movieDB->obtenerTodosMovies();
                    $rowCount = count($data);
        
                    if($rowCount === 0){
                        $response->sendParams(false, 404, 'Hubo un error al recuperar The Movies');
                    }
                    $returnData = array();
                    $returnData['movies'] = $data;
                    $response->sendParams(true, 201,null, $returnData); //201->Recurso creado
                }
                catch(MoviesException $ex){
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
