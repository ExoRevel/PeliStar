<?php

    require_once('../config/database.php');   
    require_once('../data/Fav_movieDB.php');
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

            if( !($jsonData->MOVIE_ID) || !($jsonData->USE_ID)){
                $messages = array();

                (!($jsonData->MOVIE_ID) ? $messages[] = 'MOVIE_ID no ingresado': false);
                (!($jsonData->USE_ID) ? $messages[] = 'Campo USE_ID no ingresado': false);

                $response->sendParams(false,400, $messages);

            }

            $MOVIE_ID = trim($jsonData->MOVIE_ID);
            $USE_ID = trim($jsonData->USE_ID);
        
            try{
                
                $fav_movieDB = new Fav_movieDB($database);
                $fav_Movie = new Fav_movies($MOVIE_ID, $USE_ID);
                $rowCount = $fav_movieDB->obtenerFav_movie($fav_Movie);
                if($rowCount !==0){
                    $response->sendParams(false, 409, 'ESTA PELICULA YA SE ENCUENTRA EN SU LISTA DE FAVORITOS');
                }
                
                $rowCount = $fav_movieDB->insertar($fav_Movie);

                if($rowCount === 0){
                    $response->sendParams(false, 404, 'Hubo un error al agregar la pelicula favorita');
                }

                $returnData = array();
                $returnData['rows_returned'] = $rowCount;
                $returnData['fav_movies'] = $lastfav_movies;

                $response->sendParams(true, 201, 'Pelicula agregada correctamente', $returnData);

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
            if(isset($_GET['USE_USERNAME'])) {
                try {
                    $fav_moviesDB = new Fav_movieDB($database);
                    $data = $fav_moviesDB->obtenerPorUSERNAME($_GET['USE_USERNAME']);
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
                $fav_moviesDB = new Fav_movieDB($database);
                $rowCount = $fav_moviesDB->eliminar($_GET['USE_ID'],$_GET['MOVIE_ID']);
            
                if($rowCount === 0){
                    $response->sendParams(false, 400, 'No se pudo eliminar');
                }

                $response->sendParams(true, 200, 'Eliminado correctamente correctamente', null);
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
