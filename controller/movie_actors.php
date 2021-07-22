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

            if( !isset($jsonData->MOVIE_ID) || !isset($jsonData->ACTOR_ID)){
                $messages = array();

                (!isset($jsonData->MOVIE_ID) ? $messages[] = 'MOVIE_ID no ingresado': false);
                (!isset($jsonData->ACTOR_ID) ? $messages[] = 'Campo ACTOR_ID no ingresado': false);

                $response->sendParams(false,400, $messages);

            }

            $MOVIE_ID = trim($jsonData->MOVIE_ID);
            $ACTOR_ID = trim($jsonData->ACTOR_ID);
        
            try{
                
                $movie_actor = new Movie_actorsDB($MOVIE_ID, $ACTOR_ID);
                $rowCount = $movie_actorDB->insertar($movie_actorDB);

                if($rowCount === 0){
                    $response->sendParams(false, 404, 'Hubo un error al recuperar el MOVIE_ACTOR creado');
                }

                $returnData = array();
                $returnData['rows_returned'] = $rowCount;
                $returnData['fav_movies'] = $lastfav_movies;

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

    }

