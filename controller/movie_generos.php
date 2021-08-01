<?php

    require_once('../config/database.php');   
    require_once('../data/movie_generoDB.php');
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

            if( !($jsonData->GENERO_ID) || !($jsonData->MOVIE_ID)){
                $messages = array();

                (!($jsonData->GENERO_ID) ? $messages[] = 'GENERO_ID no ingresado': false);
                (!($jsonData->MOVIE_ID) ? $messages[] = 'Campo MOVIE_ID no ingresado': false);

                $response->sendParams(false,400, $messages);

            }

            $GENERO_ID = trim($jsonData->GENERO_ID);
            $MOVIE_ID = trim($jsonData->MOVIE_ID);
        
            try{

                $movie_generoDB = new Movie_generoDB($database);
                $movie_genero = new Movies_Generos($GENERO_ID,$MOVIE_ID);

                $rowCount = $movie_generoDB->obtenerMovieGenero($movie_genero);
                $rowCount = count($rowCount);
                if($rowCount !==0){
                    $response->sendParams(false, 409, 'ESTE GENERO YA SE ENCUENTRA REGISTRADO A LA PELICULA SELECCIONADA');
                }
                $rowCount = $movie_generoDB->insertar($movie_genero);

                if($rowCount === 0){
                    $response->sendParams(false, 404, 'ERROR AL INGRESAR EL GENERO A LA PELICULA SELECCIONADA');
                }

                $returnData = array();
                $returnData['rows_returned'] = $rowCount;
                //$returnData['movie_genero'] = $lastfav_movies;

                $response->sendParams(true, 201, 'GENERO AGREGADO EXITOSAMENTE A LA PELICULA', $returnData);

            }

            catch(Movies_GenerosException $ex){
                $response->sendParams(false, 400, $ex->getMessage());
            }
            catch(PDOException $ex){
                error_log("Database query error - {$ex}", 0);
                $response->sendParams(false, 500, 'Error al registrar el este genero en la pelicula seleccionada');
            }
            break;

            case 'GET':
                if(isset($_GET['MOVIE_TITLE']) &&  isset($_GET['MOVIE_DATE'])) {
                    try {
                        $movie_Generos = new Movie_generoDB($database);
                        $data = $movie_Generos->obtenerPorTitleAndDate($_GET['MOVIE_TITLE'],$_GET['MOVIE_DATE']);
                        $rowCount = count($data);
                    
                        if($rowCount === 0){
                            $response->sendParams(false, 404, 'Hubo un error al recuperar los Generos que participan en una pelicula');
                        }
                        $returnData = array();
                        $returnData['Generos'] = $data;
                        $response->sendParams(true, 201,null,$returnData); //201->Recurso creado
                    }
                    catch(Movies_GenerosException $ex){
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
                        $movie_Generos = new Movie_generoDB($database);
                        $rowCount = $movie_Generos->eliminar($_GET['GENERO_ID'],$_GET['MOVIE_ID']);
                        
                        if($rowCount === 0){
                            $response->sendParams(false, 400, 'ESTE GENERO NO SE ENCUENTRA REGISTRADO EN LA PELICULA');
                        }
            
                        $response->sendParams(true, 200, 'Genero Eliminado correctamente de la pelicula seleccionada', null);
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