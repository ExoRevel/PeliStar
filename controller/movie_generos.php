<?php

    require_once('../config/database.php');   
    require_once('../data/Movie_generoDB.php');
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

            if( !isset($jsonData->GENERO_ID) || !isset($jsonData->MOVIE_ID)){
                $messages = array();

                (!isset($jsonData->GENERO_ID) ? $messages[] = 'GENERO_ID no ingresado': false);
                (!isset($jsonData->MOVIE_ID) ? $messages[] = 'Campo MOVIE_ID no ingresado': false);

                $response->sendParams(false,400, $messages);

            }

            $GENERO_ID = trim($jsonData->GENERO_ID);
            $MOVIE_ID = trim($jsonData->MOVIE_ID);
        
            try{

                $movie_generoDB = new Movie_generoDB($database);
                $movie_genero = new Movies_Generos($MOVIE_ID, $USE_ID);
                $rowCount = $movie_generoDB->insertar($movie_genero);

                if($rowCount === 0){
                    $response->sendParams(false, 404, 'Hubo un error al recuperar el MOVIE_GENERO creado');
                }

                $returnData = array();
                $returnData['rows_returned'] = $rowCount;
                $returnData['movie_genero'] = $lastfav_movies;

                $response->sendParams(true, 201, 'MOVIE_GENERO insertado correctamente', $returnData);

            }

            catch(Movies_GenerosException $ex){
                $response->sendParams(false, 400, $ex->getMessage());
            }
            catch(PDOException $ex){
                error_log("Database query error - {$ex}", 0);
                $response->sendParams(false, 500, 'Error al insertar MOVIE_GENERO');
            }
            break;

    }