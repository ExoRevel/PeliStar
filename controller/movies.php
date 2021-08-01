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
        header('Access-Control-Allow-Methods: POST, OPTIONS, GET, PATCH, DELETE');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        header('Access-Control-Max-Age: 86400');
        $response->sendParams(true, 200);
    }

    switch($_SERVER['REQUEST_METHOD']){
        
        case 'PATCH':
            if($_SERVER['CONTENT_TYPE']!== 'application/json'){
                $response->sendParams(false, 400, 'Content Type header no Válido');
            }

            $rawPostData = file_get_contents('php://input');

            if(!$jsonData = json_decode($rawPostData)){
                $response->sendParams(false, 400, 'Body no es Válido (JSON)');
            }
    
            $MOVIE_TITLE = trim($jsonData->MOVIE_TITLE);
            $MOVIE_DATE = trim($jsonData->MOVIE_DATE);
            $MOVIE_TIME = trim($jsonData->MOVIE_TIME);
            $MOVIE_SINOPSIS = trim($jsonData->MOVIE_SINOPSIS);
            $MOVIE_CALIFICATION = trim($jsonData->MOVIE_CALIFICATION);
            $MOVIE_ID = $jsonData->MOVIE_ID;
            try{
                $movieDB = new MovieDB($database);
                $existingmovie = $movieDB->obtenerPorId($MOVIE_ID);
                $rowCount = count($existingmovie);

                if($rowCount === 0){
                    $response->sendParams(false, 409, 'Esta pelicula no se encuentra registrada');
                }
                
                $movie = new Movies($MOVIE_ID, $MOVIE_TITLE, $MOVIE_DATE, $MOVIE_TIME , $MOVIE_SINOPSIS , $MOVIE_CALIFICATION);
                
                if($MOVIE_TITLE!=null)
                {
                    $rowCount = $movieDB->actualizarTituloPorId($MOVIE_TITLE, $MOVIE_ID);
                }
                if($rowCount === 0){
                    $response->sendParams(false, 404, 'ERROR! Ingrese un nombre diferente al registrado, o deje la casilla en blanco');
                }

                if($MOVIE_DATE!=null)
                {
                    $rowCount = $movieDB->actualizarFechaEstrenoPorId($MOVIE_DATE, $MOVIE_ID);
                }
                if($rowCount === 0){
                    $response->sendParams(false, 404, 'ERROR! Ingrese una fecha diferente a la registrada, o deje la casilla en blanco');
                }

                if($MOVIE_TIME!=null)
                {
                    $rowCount = $movieDB->actualizarDuracionPorId($MOVIE_TIME, $MOVIE_ID);
                }
                if($rowCount === 0){
                    $response->sendParams(false, 404, 'ERROR! Ingrese una duración diferente a la registrada, o deje la casilla en blanco');
                }

                if($MOVIE_SINOPSIS!=null)
                {
                    $rowCount = $movieDB->actualizarSinopsisPorId($MOVIE_SINOPSIS, $MOVIE_ID);
                }
                if($rowCount === 0){
                    $response->sendParams(false, 404, 'ERROR! Ingrese una Sinopsis diferente a la registrada, o deje la casilla en blanco');
                }

                if($MOVIE_CALIFICATION!=null)
                {
                    $rowCount = $movieDB->actualizarCalifacionPorId($MOVIE_CALIFICATION, $MOVIE_ID);
                }
                if($rowCount === 0){
                    $response->sendParams(false, 404, 'ERROR! Ingrese una Calificación diferente a la registrada, o deje la casilla en blanco');
                }

                if( !($jsonData->MOVIE_TITLE) && !($jsonData->MOVIE_DATE) && !($jsonData->MOVIE_TIME) && !($jsonData->MOVIE_SINOPSIS) && !($jsonData->MOVIE_CALIFICATION)){
                    $messages = array();
    
                    (!($jsonData->MOVIE_TITLE) ? $messages[] = 'EL TITULO NO FUE INGRESADO': false);
                    (!($jsonData->MOVIE_DATE) ? $messages[] = 'LA FECHA DE ESTRENO NO FUE INGRESADA': false);
                    (!($jsonData->MOVIE_TIME) ? $messages[] = 'LA DURACION DE LA PELICULA NO FUE INGRESADA': false);
                    (!($jsonData->MOVIE_SINOPSIS) ? $messages[] = 'LA SINOPSIS DE LA PELICULA NO FUE INGRESADA': false);
                    (!($jsonData->MOVIE_CALIFICATION) ? $messages[] = 'LA CALIFICACIÓN DE LA PELICULA NO FUE INGRESADA': false);
                    $response->sendParams(false,400, $messages);
                }

                $returnData = array();

                $response->sendParams(true, 201, 'Los datos fueron modificados correctamente', $returnData);
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
        case 'POST':
            if($_SERVER['CONTENT_TYPE']!== 'application/json'){
                $response->sendParams(false, 400, 'Content Type header no Válido');
            }

            $rawPostData = file_get_contents('php://input');

            if(!$jsonData = json_decode($rawPostData)){
                $response->sendParams(false, 400, 'Body no es Válido (JSON)');
            }

            if( !($jsonData->MOVIE_TITLE) || !($jsonData->MOVIE_DATE) || !($jsonData->MOVIE_TIME) || !($jsonData->MOVIE_SINOPSIS) || !($jsonData->MOVIE_CALIFICATION)){
                $messages = array();

                (!($jsonData->MOVIE_TITLE) ? $messages[] = 'EL TITULO NO FUE INGRESADO': false);
                (!($jsonData->MOVIE_DATE) ? $messages[] = 'LA FECHA DE ESTRENO NO FUE INGRESADA': false);
                (!($jsonData->MOVIE_TIME) ? $messages[] = 'LA DURACION DE LA PELICULA NO FUE INGRESADA': false);
                (!($jsonData->MOVIE_SINOPSIS) ? $messages[] = 'LA SINOPSIS DE LA PELICULA NO FUE INGRESADA': false);
                (!($jsonData->MOVIE_CALIFICATION) ? $messages[] = 'LA CALIFICACIÓN DE LA PELICULA NO FUE INGRESADA': false);

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
        default: 
            $response->sendParams(false, 405, 'Tipo de petición no permitida');
            break;
        case 'DELETE':
            try{
                $MOVIE_ID = $_GET['MOVIE_ID'];
                $movieDB = new MovieDB($database);
                $existingmovie = $movieDB->obtenerPorId($MOVIE_ID);
                $rowCount = count($existingmovie);

                if($rowCount ===0){
                    $response->sendParams(false, 409, 'Esta pelicula no se encuentra registrada');
                }
                foreach(glob('../images/movies/'.$MOVIE_ID.'/*') as $archivo)
                {
                    unlink($archivo);
                }

                $returnData = array();
                $returnData['rows_returned'] = $rowCount;

                $response->sendParams(true, 201, 'La pelicula fue eliminada correctamente', $returnData);
            }

            catch(MoviesException $ex){
                $response->sendParams(false, 400, $ex->getMessage());
            }
            catch(PDOException $ex){
                error_log("Database query error - {$ex}", 0);
                $response->sendParams(false, 500, 'Error al eliminar la pelicula');
            }
        break;
    
    }
