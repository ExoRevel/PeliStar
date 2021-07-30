<?php

    require_once('../config/database.php');   
    require_once('../data/Movie_actorsDB.php');
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

            if( !isset($jsonData->MOVIE_ID) || !isset($jsonData->ACTOR_ID)){
                $messages = array();

                (!isset($jsonData->MOVIE_ID) ? $messages[] = 'MOVIE_ID no ingresado': false);
                (!isset($jsonData->ACTOR_ID) ? $messages[] = 'Campo ACTOR_ID no ingresado': false);

                $response->sendParams(false,400, $messages);

            }

            $MOVIE_ID = trim($jsonData->MOVIE_ID);
            $ACTOR_ID = trim($jsonData->ACTOR_ID);
        
            try{
                
                $movie_actorsDB = new Movie_actorsDB($database);
                $movie_actor = new Movies_actors($MOVIE_ID, $ACTOR_ID);

                $rowCount = $movie_actorsDB->obtenerMovieActors($movie_actor);
                $rowCount = count($rowCount);
                if($rowCount !==0){
                    $response->sendParams(false, 409, 'ESTE ACTOR YA SE ENCUENTRA REGISTRADO EN LA PELICULA SELECCIONADA PELICULA');
                }
                
                $rowCount = $movie_actorsDB->insertar($movie_actor);

                if($rowCount === 0){
                    $response->sendParams(false, 404, 'Hubo un error al recuperar el MOVIE_ACTOR creado');
                }

                $returnData = array();
                $returnData['rows_returned'] = $rowCount;
                //$returnData['movie_actors'] = $lastmovie_actors;

                $response->sendParams(true, 201, 'MOVIE_ACTOR insertado correctamente', $returnData);

            }

            catch(Movies_ActorsException $ex){
                $response->sendParams(false, 400, $ex->getMessage());
            }
            catch(PDOException $ex){
                error_log("Database query error - {$ex}", 0);
                $response->sendParams(false, 500, 'Error al insertar MOVIE_ACTOR');
            }
            break;
        case 'GET':
            if(isset($_GET['MOVIE_TITLE']) &&  isset($_GET['MOVIE_DATE'])) {
                try {
                    $movie_Actors = new Movie_actorsDB($database);
                    $data = $movie_Actors->obtenerPorTitleAndDate($_GET['MOVIE_TITLE'],$_GET['MOVIE_DATE']);
                    $rowCount = count($data);
                
                    if($rowCount === 0){
                        $response->sendParams(false, 404, 'Hubo un error al recuperar los actores que participan en una pelicula');
                    }
                    $returnData = array();
                    $returnData['Actors'] = $data;
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
                $movie_Actors = new Movie_actorsDB($database);
                $rowCount = $movie_Actors->eliminar($_GET['ACTOR_ID'],$_GET['MOVIE_ID']);
                
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

