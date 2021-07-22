<?php

    require_once('../config/database.php');   
    require_once('../data/Fav_moviesDB.php');
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

        case 'POST':
            if($_SERVER['CONTENT_TYPE']!== 'application/json'){
                $response->sendParams(false, 400, 'Content Type header no Válido');
            }
            
            $rawPostData = file_get_contents('php://input');

            if(!$jsonData = json_decode($rawPostData)){
                $response->sendParams(false, 400, 'Body no es Válido (JSON)');
            }

            if( !isset($jsonData->MOVIE_ID) || !isset($jsonData->USE_ID)){
                $messages = array();

                (!isset($jsonData->MOVIE_ID) ? $messages[] = 'MOVIE_ID no ingresado': false);
                (!isset($jsonData->USE_ID) ? $messages[] = 'Campo USE_ID no ingresado': false);

                $response->sendParams(false,400, $messages);

            }

            $MOVIE_ID = trim($jsonData->MOVIE_ID);
            $USE_ID = trim($jsonData->USE_ID);
        
            try{
                
                $fav_movie = new Fav_movieDB($MOVIE_ID, $USE_ID);
                $rowCount = $fav_movieDB->insertar($fav_movieDB);

                if($rowCount === 0){
                    $response->sendParams(false, 404, 'Hubo un error al recuperar el FAV_MOVIE creado');
                }

                $returnData = array();
                $returnData['rows_returned'] = $rowCount;
                $returnData['fav_movies'] = $lastfav_movies;

                $response->sendParams(true, 201, 'FAV_MOVIE insertado correctamente', $returnData);

            }

            catch(Fav_MoviesException $ex){
                $response->sendParams(false, 400, $ex->getMessage());
            }
            catch(PDOException $ex){
                error_log("Database query error - {$ex}", 0);
                $response->sendParams(false, 500, 'Error al insertar FAV_MOVIES');
            }
            break;

        case 'GET':
            if(isset($_GET['USE_ID'])) {
                try {
                    $fav_moviesDB = new Fav_MovieDB($database);
                    $data = $fav_moviesDB->obtenerPorUserId($_GET['USE_ID']);
                    $rowCount = count($data);
            
                    if($rowCount === 0){
                        $response->sendParams(false, 404, 'Hubo un error al recuperar las peliculas favoritas');
                    }
                    $returnData = array();
                    $returnData['Movies'] = $data;
                    $response->sendParams(true, 201,null,$returnData); //201->Recurso creado
                }
                catch(Fav_MoviesException $ex){
                    $response->sendParams(false, 400, $ex->getMessage());
                }
                catch(PDOException $ex){
                    error_log("Database query error - {$ex}", 0);
                    $response->sendParams(false, 500);
                }
            }
            break;
        case 'DELETE':
            try{
                $fav_moviesDB = new Fav_MovieDB($database);
                $rowCount = $fav_moviesDB->eliminar($GET_['USE_ID'],$GET_['MOVIE_ID']);
            
                if($rowCount === 0){
                    $response->sendParams(false, 400, 'No se pudo cerrar sesión usando el access token provisto');
                }

                $response->sendParams(true, 200, 'Sesión cerrada correctamente', null);
            }
            catch(PDOException $ex){
                error_log("Database query error - {$ex}", 0);
                $response->sendParams(false, 500, $ex->getMessage());
            }            
            break;

        default: 
            $response->sendParams(false, 405, 'Tipo de petición no permitida');
            break;
    }











