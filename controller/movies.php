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

                (!isset($jsonData->MOVIE_TITLE) ? $messages[] = 'EL TITULO NO FUE INGRESADO': false);
                (!isset($jsonData->MOVIE_DATE) ? $messages[] = 'LA FECHA DE ESTRENO NO FUE INGRESADA': false);
                (!isset($jsonData->MOVIE_TIME) ? $messages[] = 'LA DURACION DE LA PELICULA NO FUE INGRESADA': false);
                (!isset($jsonData->MOVIE_SINOPSIS) ? $messages[] = 'LA SINOPSIS DE LA PELICULA NO FUE INGRESADA': false);
                (!isset($jsonData->MOVIE_CALIFICATION) ? $messages[] = 'LA CALIFICACIÓN DE LA PELICULA NO FUE INGRESADA': false);

                $response->sendParams(false,400, $messages);

            }
    
            $MOVIE_TITLE = trim($jsonData->MOVIE_TITLE);
            $MOVIE_DATE = trim($jsonData->MOVIE_DATE);
            $MOVIE_TIME = trim($jsonData->MOVIE_TIME);
            $MOVIE_SINOPSIS = trim($jsonData->MOVIE_SINOPSIS);
            $MOVIE_CALIFICATION = trim($jsonData->MOVIE_CALIFICATION);
    
            try{
                $movieDB = new MovieDB($database);
                $existingmovie = $movieDB->obtenerPorTitleAndDate($MOVIE_TITLE, $MOVIE_DATE);
                $rowCount = count($existingmovie);

                if($rowCount !==0){
                    $response->sendParams(false, 409, 'Esta pelicula ya se encuentra registrada');
                }
                
                $movie = new Movies(null, $MOVIE_TITLE, $MOVIE_DATE, $MOVIE_TIME , $MOVIE_SINOPSIS , $MOVIE_CALIFICATION);
                $rowCount = $movieDB->insertar($movie);

                if($rowCount === 0){
                    $response->sendParams(false, 404, 'Hubo un error al recuperar la pelicula creada');
                }

                $returnData = array();
                $returnData['rows_returned'] = $rowCount;
                //$returnData['movie'] = $lastmovies;

                $response->sendParams(true, 201, 'La pelicula fue insertada correctamente', $returnData);
            }

            catch(MoviesException $ex){
                $response->sendParams(false, 400, $ex->getMessage());
            }
            catch(PDOException $ex){
                error_log("Database query error - {$ex}", 0);
                $response->sendParams(false, 500, 'Error al insertar la pelicula');
            }
            break;

    
        case 'GET':
            if(isset($_GET['MOVIE_ID'])) {
                try {
                    $movieDB = new MovieDB($database);
                    $data = $movieDB->obtenerPorId($_GET['MOVIE_ID']);
                    $rowCount = count($data);
        
                    if($rowCount === 0){
                        $response->sendParams(false, 404, 'Hubo un error al recuperar la pelicula ');
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
                        $response->sendParams(false, 404, 'Hubo un error al recuperar la pelicula');
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
