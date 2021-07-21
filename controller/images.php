<?php

    require_once('../config/database.php');   
    require_once('../data/ActorDB.php');
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

            if( !isset($jsonData->IMG_TITLE) || !isset($jsonData->IMG_FILENAME) || !isset($jsonData->IMG_MIMETYPE) || !isset($jsonData->MOVIE_ID)){
                $messages = array();

                (!isset($jsonData->IMG_TITLE) ? $messages[] = 'IMG_TITLE FULLNAME NO INGRESADO': false);
                (!isset($jsonData->IMG_FILENAME) ? $messages[] = 'Campo IMG_FILENAME no ingresado': false);
                (!isset($jsonData->IMG_MIMETYPE) ? $messages[] = 'Campo IMG_MIMETYPE no ingresado': false);
                (!isset($jsonData->MOVIE_ID) ? $messages[] = 'Campo MOVIE_ID no ingresado': false);

                $response->sendParams(false,400, $messages);

            }

            $IMG_TITLE = trim($jsonData->IMG_TITLE);
            $IMG_FILENAME = trim($jsonData->IMG_FILENAME);
            $IMG_MIMETYPE = trim($jsonData->IMG_MIMETYPE);
            $MOVIE_ID = trim($jsonData->MOVIE_ID);

            try{
                $imageDB = new ImageDB($database);
                $existingimagen = $imageDB ->obtenerPorTitle($IMG_TITLE);
                $rowCount = count($existingimagen);

                if($rowCount !==0){
                    $response->sendParams(false, 409, 'Imagen ya existe en la base de data');
                }
                
                $image = new Images(null, $IMG_TITLE, $IMG_FILENAME,$IMG_MIMETYPE, $MOVIE_ID);
                $rowCount = $imageDB->insertar($image);

                if($rowCount === 0){
                    $response->sendParams(false, 404, 'Hubo un error al recuperar la Iamgen');
                }

                $returnData = array();
                $returnData['rows_returned'] = $rowCount;
                $returnData['actors'] = $lastactors;

                $response->sendParams(true, 201, 'Imagen insertada correctamente', $returnData);
            }


            catch(imagesException $ex){
                $response->sendParams(false, 400, $ex->getMessage());
            }
            catch(PDOException $ex){
                error_log("Database query error - {$ex}", 0);
                $response->sendParams(false, 500, 'Error al insertar Nueva Imagen');
            }
            break;


    }





































