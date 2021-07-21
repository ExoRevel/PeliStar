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
        
        case 'POST':
            if($_SERVER['CONTENT_TYPE']!== 'application/json'){
                $response->sendParams(false, 400, 'Content Type header no Válido');
            }

            $rawPostData = file_get_contents('php://input');

            if(!$jsonData = json_decode($rawPostData)){
                $response->sendParams(false, 400, 'Body no es Válido (JSON)');
            }

            if( !isset($jsonData->MOVIE_TITLE) || !isset($jsonData->MOVIE_DATE) || !isset($jsonData->MOVIE_TIME) || !isset($jsonData->MOVIE_SINOPSIS) || !isset($jsonData->MOVIE_CALIFICATION)){
                $messages = array();

                (!isset($jsonData->MOVIE_TITLE) ? $messages[] = 'MOVIE_TITLE NO INGRESADO': false);
                (!isset($jsonData->MOVIE_DATE) ? $messages[] = 'Campo MOVIE_DATE no ingresado': false);
                (!isset($jsonData->MOVIE_TIME) ? $messages[] = 'Campo MOVIE_TIME no ingresado': false);
                (!isset($jsonData->MOVIE_SINOPSIS) ? $messages[] = 'Campo MOVIE_SINOPSIS no ingresado': false);
                (!isset($jsonData->MOVIE_CALIFICATION) ? $messages[] = 'Campo MOVIE_CALIFICATION no ingresado': false);

                $response->sendParams(false,400, $messages);

            }
    
            $MOVIE_TITLE = trim($jsonData->MOVIE_TITLE);
            $MOVIE_DATE = trim($jsonData->MOVIE_DATE);
            $MOVIE_TIME = trim($jsonData->MOVIE_TIME);
            $MOVIE_SINOPSIS = trim($jsonData->MOVIE_SINOPSIS);
            $MOVIE_CALIFICATION = trim($jsonData->MOVIE_CALIFICATION);
    
            try{
                $movieDB = new MovieDB($database);
                $existingmovie = $movieDB->obtenerPorFullName($MOVIE_TITLE);
                $rowCount = count($existingmovie);

                if($rowCount !==0){
                    $response->sendParams(false, 409, 'MOVIE ya existe en la base de data');
                }
                
                $movie = new Movies(null, $MOVIE_TITLE, $MOVIE_DATE, $MOVIE_TIME , $MOVIE_SINOPSIS , $MOVIE_CALIFICATION);
                $rowCount = $movieDB->insertar($movie);

                if($rowCount === 0){
                    $response->sendParams(false, 404, 'Hubo un error al recuperar The Movie creado');
                }

                $returnData = array();
                $returnData['rows_returned'] = $rowCount;
                $returnData['movie'] = $lastmovies;

                $response->sendParams(true, 201, 'Movie insertado correctamente', $returnData);
            }

            catch(MoviesException $ex){
                $response->sendParams(false, 400, $ex->getMessage());
            }
            catch(PDOException $ex){
                error_log("Database query error - {$ex}", 0);
                $response->sendParams(false, 500, 'Error al insertar o craer new Movie');
            }
            break;

    
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
