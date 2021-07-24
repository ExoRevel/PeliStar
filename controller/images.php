<?php

    require_once('../config/database.php');   
    require_once('../data/ImageDB.php');   
    require_once('../data/MovieDB.php'); 
    require_once('../util/response.php'); //Auth trae response

    //Respuesta que se enviará al cliente    
    $response = new Response();

    //Conexión a la base de datos
    try{
        $database = Database::connectDB();      
    }
    catch(PDOException $ex){
        error_log("Connection error - {$ex}", 0);       
        $response->sendParams(false, 500, 'Error de conexión a la base de datos');
    }

    //Authorization
    //$user = checkAuthStatusAndReturnUser($database);   

    if(array_key_exists('MOVIE_ID', $_GET) && array_key_exists('IMG_ID', $_GET)){       
        $IMG_ID = $_GET['IMG_ID'];
        $MOVIE_ID = $_GET['MOVIE_ID'];

         //Validaciones de los query parameters
         if($IMG_ID == '' || !is_numeric($IMG_ID) || $MOVIE_ID == '' || !is_numeric($MOVIE_ID)){
            $messages = array();

            (($IMG_ID == '' || !is_numeric($IMG_ID))  ? $messages[] = 'El campo Image id no puede estar en blanco y debe ser numérico': false);
            (($MOVIE_ID == '' || !is_numeric($MOVIE_ID)) ? $messages[] = 'El campo MOVIE id no puede estar en blanco y debe ser numérico': false);

            $response->sendParams(false, 400, $messages);
        }

        if(array_key_exists('IMG_ATTR', $_GET)){
           
            $IMG_ATTR = $_GET['IMG_ATTR'];
        
            if($_SERVER['REQUEST_METHOD'] === 'GET'){

                try{
                    $imageDB = new ImageDB($database);           
                    $image = $imageDB->obtenerPorId($IMG_ID, $MOVIE_ID);
                    $rowCount = count($image);
    
                    if($rowCount === 0){
                        $response->sendParams(false, 404, 'Imagen no encontrada');
                    }

                    $returnData = array();
                    $returnData['rows_returned'] = $rowCount;
                    $returnData['images'] = $image;

                    $response->sendParams(true, 200, 'Atributos de imagen recuperados correctamente', $returnData, true);
                }
                catch(ImageException $ex){
                    $response->sendParams(false, 400, $ex->getMessage());
                }
                catch(PDOException $ex){
                    error_log("Database query error - {$ex}", 0);
                    $response->sendParams(false, 500, 'Error al intentar obtener las peliculas');
                }
            }
            else if($_SERVER['REQUEST_METHOD'] === 'PATCH'){    

                try{

                    if($_SERVER['CONTENT_TYPE'] !== 'application/json'){
                        $response->sendParams(false, 400, 'Content type header no válido');
                    }

                    $rawPatchData = file_get_contents('php://input');

                    if(!$jsonData = json_decode($rawPatchData)){ //Si no se pudo decodificar el body a un objeto JavaScript
                        $response->sendParams(false, 400, 'Body no es válido (JSON)');
                    }

                    $IMG_TITLE_UPD = isset($jsonData->IMG_TITLE);

                    if(isset($jsonData->IMG_FILENAME) && strpos($jsonData->IMG_FILENAME, '.') !== false){
                        $response->sendParams(false, 400, 'El nombre del archivo no debe tener extensión');
                    }

                    $IMG_FILENAME_UPD = isset($jsonData->IMG_FILENAME);

                    if(!$IMG_TITLE_UPD && !$IMG_FILENAME_UPD){
                        $response->sendParams(false, 400, 'No se ingresó ningún atributo de la imagen');
                    }

                    $imageDB = new ImageDB($database);  
                    $originalImage = $imageDB->obtenerPorId($IMG_ID, $MOVIE_ID);
                    $rowCount = count($originalImage);

                    if($rowCount === 0){
                        $response->sendParams(false, 404, 'Imagen no encontrada');
                    }

                    $originalImage = $originalImage[0]; //Se devuelve un array

                    $database->beginTransaction();
               
                    $image = new Images($IMG_ID, $IMG_TITLE_UPD ? $jsonData->IMG_TITLE: null, $IMG_FILENAME_UPD ? $jsonData->IMG_FILENAME.'.'.$originalImage['IMG_FILEEXTENSION']: null, $originalImage['IMG_MIMETYPE'],$MOVIE_ID);
                    $rowCount = $imageDB->actualizar($image);
    
                    if($rowCount == 0){
                        if($database->inTransaction())
                            $database->rollBack();

                        $response->sendParams(false, 500, 'Hubo un error al intentar actualizar los atributos de la imagen o no se identificó ningún cambio');
                    }               
                    
                    $updatedImage = $imageDB->obtenerPorId($IMG_ID, $MOVIE_ID);
                    $rowCount = count($updatedImage);

                    if($rowCount === 0){
                        if($database->inTransaction())
                            $database->rollBack();

                        $response->sendParams(false, 404, 'Imagen actualizada no se pudo recuperar');
                    }

                    $updatedImage = $updatedImage[0]; //Se devuelve un array

                    if($IMG_FILENAME_UPD){//Actualizar el nombre de la imagen en la carpeta
                        $image->renameImageFile($originalImage['IMG_FILENAME'],  $updatedImage['IMG_FILENAME']);
                    }

                    $database->commit();

                    $returnData = array();
                    $returnData['rows_returned'] = $rowCount;
                    $returnData['images'] = $updatedImage;

                    $response->sendParams(true, 200, 'Imagen actualizada correctamente', $returnData, true);                                        
                }
                catch(ImageException $ex){
                    if($database->inTransaction())
                        $database->rollBack();

                    $response->sendParams(false, 400, $ex->getMessage());
                }
                catch(PDOException $ex){
                    error_log("Database query error - {$ex}", 0);

                    if($database->inTransaction())
                        $database->rollBack();

                    $response->sendParams(false, 500, 'Error al intentar actualizar los atributos de la imagen');
                }
            }
            else{
                $response->sendParams(false, 405, 'Tipo de petición no permitida');
            }     

        }
        else{
            if($_SERVER['REQUEST_METHOD'] === 'GET'){

                try{
                    $imageDB = new ImageDB($database);           
                    $image = $imageDB->obtenerPorId($IMG_ID, $MOVIE_ID);
                    $rowCount = count($image);
    
                    if($rowCount === 0){
                        $response->sendParams(false, 404, 'Imagen no encontrada');
                    }

                    $image = $image[0]; // Se guarda como array

                    $filePath = $image['IMG_FOLDER_LOCATION'] .$MOVIE_ID.'/'.$image['IMG_FILENAME'];

                    if(!file_exists($filePath)){
                        http_response_code(404);
                    }

                    header('Content-Type:'.$image['IMG_MIMETYPE']);
                    header('Content-Disposition: inline; filename="'.$image['IMG_FILENAME'].'"');
                    
                    if(!readfile($filePath)){
                        http_response_code(404);
                    }

                    exit;
                }
                catch(ImageException $ex){
                    $response->sendParams(false, 400, $ex->getMessage());
                }
                catch(PDOException $ex){
                    error_log("Database query error - {$ex}", 0);
                    $response->sendParams(false, 500, 'Error al intentar obtener la imagen ');
                }
            }
            else if($_SERVER['REQUEST_METHOD'] === 'DELETE'){

                try{

                    $database->beginTransaction();

                    $imageDB = new ImageDB($database);
                    $originalImage = $imageDB->obtenerPorId($IMG_ID, $MOVIE_ID);
                    $rowCount = count($originalImage);

                    if($rowCount === 0){
                        $response->sendParams(false, 404, 'Imagen no encontrada');
                    }

                    $originalImage = $originalImage[0];

                    $image = new Images($originalImage['IMG_ID'], $originalImage['IMG_TITLE'], $originalImage['IMG_FILENAME'], $originalImage['IMG_MIMETYPE'], $originalImage['MOVIE_ID']);

                    $rowCount = $imageDB->eliminarPorId($IMG_ID, $MOVIE_ID);
                
                    if($rowCount === 0){
                        if($database->inTransaction())
                            $database->rollBack();

                        $response->sendParams(false, 404, 'Imagen no encontrada');
                    }

                    $image->deleteImageFile();

                    $database->commit();
    
                    $response->sendParams(true, 200, 'Imagen eliminada correctamente');
                }
                catch(PDOException $ex){
                    error_log("Database query error - {$ex}", 0);

                    if($database->inTransaction())
                        $database->rollBack();

                    $response->sendParams(false, 500, 'Error al intentar eliminar la imagen');
                }
            }
            else{
                $response->sendParams(false, 405, 'Tipo de petición no permitida');
            } 
        }      
    }
    else if(array_key_exists('MOVIE_ID', $_GET) && !array_key_exists('IMG_ID', $_GET)){
        $MOVIE_ID = $_GET['MOVIE_ID'];

        //Validaciones de los parámetros de la consulta
        if($MOVIE_ID == '' || !is_numeric($MOVIE_ID)){
            $response->sendParams(false, 400, 'El campo MOVIE id no puede estar en blanco y debe ser numérico');
        }
        
        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            try{
                if(!isset($_SERVER['CONTENT_TYPE']) || strpos($_SERVER['CONTENT_TYPE'], "multipart/form-data; boundary=") === false){
                    $response->sendParams(false, 400, 'Content type header no establecido en  multipart/form-data con boundary');
                }
    
                $MovieDB = new MovieDB($database);
                $movie = $MovieDB->obtenerPorId($_GET['MOVIE_ID']);
                $rowCount = count($movie);
    
                if($rowCount === 0){
                    $response->sendParams(false, 404, 'movie no encontrado');
                }
    
                if(!isset($_POST['IMG_ATTR'])){
                    $response->sendParams(false, 400, 'Atributos no han sido establecidos en la petición');
                }

                if(!$jsonImageAttributes = json_decode($_POST['IMG_ATTR'])){
                    $response->sendParams(false, 400, 'El campo IMG_ATTR no es json válido');
                }

                if(!isset($jsonImageAttributes->IMG_TITLE) || !isset($jsonImageAttributes->IMG_FILENAME) || $jsonImageAttributes->IMG_TITLE ==='' || $jsonImageAttributes->IMG_FILENAME === ''){
                    $response->sendParams(false, 400, 'Los campos IMG_TITLE e IMG_FILENAME son obligatorios');
                }

                if(strpos($jsonImageAttributes->IMG_FILENAME, '.') > 0 ){
                    $response->sendParams(false, 400, 'El campo IMG_FILENAME no debe tener extensión');
                }

                if(!isset($_FILES['IMG_FILE']) || $_FILES['IMG_FILE']['error'] !== 0){
                    $response->sendParams(false, 500, 'No se ha seleccionado ninguna imagen');
                }

                $imageFileDetails = getimagesize($_FILES['IMG_FILE']['tmp_name']);

                if(isset($_FILES['IMG_FILE']['size']) && $_FILES['IMG_FILE']['size']>5242880){ //bytes (5MB)
                    $response->sendParams(false, 400, 'El archivo debe pesar menos de 5MB');
                }

                $allowedImageFileTypes = array('image/jpeg', 'image/gif', 'image/png');

                if(!in_array($imageFileDetails['mime'], $allowedImageFileTypes)){
                    $response->sendParams(false, 400, 'Tipo de archivo no soportado');
                }

                $fileExtension = '';

                switch($imageFileDetails['mime']){
                    case 'image/jpeg':
                        $fileExtension = '.jpg';
                        break;
                    case 'image/gif':
                        $fileExtension = '.gif';
                        break;
                    case 'image/png':
                        $fileExtension = '.png';
                        break;
                    default:
                        break;
                }

                if($fileExtension == ''){
                    $response->sendParams(false, 400, 'No se encontró una extensión válida para el mimetype');
                }

                $image = new Images(null, $jsonImageAttributes->IMG_TITLE, $jsonImageAttributes->IMG_FILENAME.$fileExtension, $imageFileDetails['mime'],$MOVIE_ID);

                $IMG_TITLE = $image->getTitle();
                $IMG_FILENAME = $image->getFileName();
                $IMG_MIMETYPE = $image->getMimeType();

                $imageDB = new ImageDB($database);               
                $rowCount = count($imageDB->buscar($MOVIE_ID, $IMG_FILENAME));
            
                if($rowCount !== 0){
                    $response->sendParams(false, 409, 'Ya existe un archivo con el mismo nombre. Intenta nuevamente');
                }

                //Transacción para guardar la imagen en directorio y en BD
                $database->beginTransaction();

                $image = new Images(null, $IMG_TITLE, $IMG_FILENAME, $IMG_MIMETYPE,$_GET['MOVIE_ID']);
                $rowCount = $imageDB->insertar($image);
            
                if($rowCount === 0){                   
                    $response->sendParams(false, 500, 'Hubo un error al intentar registrar la imagen');
                }

                $lastImageId = $database->lastInsertId();

                $lastImage = $imageDB->obtenerPorId($lastImageId, $_GET['MOVIE_ID']);
                $rowCount = count($lastImage);

                if($rowCount === 0){
                    if($database->inTransaction())
                        $database->rollBack();
                    $response->sendParams(false, 404, 'Hubo un error al recuperar la imagen creada');
                }

                $image->saveImageFile($_FILES['IMG_FILE']['tmp_name']);

                $database->commit();       
                
                $response->sendParams(true, 201, 'Imagen guardada correctamente', $lastImage);
            }
            catch(ImageException $ex){
                if($database->inTransaction())
                    $database->rollBack();

                $response->sendParams(false, 400, $ex->getMessage());
            }
            catch(PDOException $ex){
                error_log("Database query error - {$ex}", 0);

                if($database->inTransaction())
                    $database->rollBack();

                $response->sendParams(false, 500, 'Error al intentar subir la imagen');
            }
        }
        else{
            $response->sendParams(false, 405, 'Tipo de petición no permitida');
        } 
    }
    else{
        $response->sendParams(false, 404, 'Endpoint no encontrado');
    }