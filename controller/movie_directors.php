<?php

    require_once('../config/database.php');   
    require_once('../data/Movie_directorDB.php');
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
    //header('Access-Control-Allow-Origin: *');
    //Opciones de preflight (CORS)
    if($_SERVER['REQUEST_METHOD'] === 'OPTIONS'){
        header('Access-Control-Allow-Methods: POST, OPTIONS, GET, DELETE');
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

            if( !($jsonData->DIRECTOR_ID) || !($jsonData->MOVIE_ID)){
                $messages = array();

                (!($jsonData->DIRECTOR_ID) ? $messages[] = 'DIRECTOR_ID no ingresado': false);
                (!($jsonData->MOVIE_ID) ? $messages[] = 'Campo MOVIE_ID no ingresado': false);

                $response->sendParams(false,400, $messages);

            }

            $DIRECTOR_ID = trim($jsonData->DIRECTOR_ID);
            $MOVIE_ID = trim($jsonData->MOVIE_ID);
        
            try{
                
                $movie_directorDB = new Movie_directorDB($database);
                $movie_director = new Movies_Directors($DIRECTOR_ID, $MOVIE_ID);

                $rowCount = $movie_directorDB->obtenerMovieDirectors($movie_director);
                $rowCount = count($rowCount);
                if($rowCount !==0){
                    $response->sendParams(false, 409, 'ESTE DIRECTOR YA SE ENCUENTRA REGISTRADO');
                }

                $rowCount = $movie_directorDB->insertar($movie_director);

                if($rowCount === 0){
                    $response->sendParams(false, 404, 'HUBO UN PROBLEMA AL AGREGAR ESTE DIRECTROR A LA PELICULA SELECCIONADA');
                }

                $returnData = array();
                $returnData['rows_returned'] = $rowCount;
                //$returnData['movie_director'] = $lastmovie_directors;

                $response->sendParams(true, 201, 'DIRECTOR AGREGADO CORRECTAMENTE', $returnData);
            }

            catch(Movies_DirectorsException $ex){
                $response->sendParams(false, 400, $ex->getMessage());
            }
            catch(PDOException $ex){
                error_log("Database query error - {$ex}", 0);
                $response->sendParams(false, 500, 'Error al registrar este director a la pelicula seleccionada');
            }
            break;

            case 'GET':
                if(isset($_GET['MOVIE_ID'])) {
                    try {
                        $movie_directors = new Movie_directorDB($database);
                        $data = $movie_directors->obtenerPorMovieId($_GET['MOVIE_ID']);
                        $rowCount = count($data);
                    
                        if($rowCount === 0){
                            $response->sendParams(false, 404, 'Hubo un error al recuperar los Directores que participan en la pelicula');
                        }
                        $returnData = array();
                        $returnData['Directors'] = $data;
                        $response->sendParams(true, 201,null,$returnData); //201->Recurso creado
                    }
                    catch(Movies_DirectorsException $ex){
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
                        $movie_directors = new Movie_directorDB($database);
                        $rowCount = $movie_directors->eliminar($_GET['DIRECTOR_ID'],$_GET['MOVIE_ID']);
                        
                        if($rowCount === 0){
                            $response->sendParams(false, 400, 'ESTE DIRECTOR NO SE ENCUENTRA REGISTRADO EN LA PELICULA');
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