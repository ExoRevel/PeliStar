<?php

require_once('../config/database.php');
require_once('../data/Movie_actorsDB.php');
require_once('../util/response.php');
require_once('../util/auth.php');
//Respuesta que se enviará al cliente    
$response = new Response();

//Conexión a la base de data
try {
    $database = Database::connectDB();
} catch (PDOException $ex) {
    error_log("Connection error - {$ex}", 0);
    $response->sendParams(false, 500, 'Error de conexión a la base de data');
}
//header('Access-Control-Allow-Origin: *');
//Opciones de preflight (CORS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Methods: POST, OPTIONS, GET, DELETE');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Max-Age: 86400');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    $response->sendParams(true, 200);
}
//Authorization
$user = checkAuthStatusAndReturnUser($database);
switch ($_SERVER['REQUEST_METHOD']) {

    case 'POST':
        if ($_SERVER['CONTENT_TYPE'] !== 'application/json') {
            $response->sendParams(false, 400, 'Content Type header no Válido');
        }

        $rawPostData = file_get_contents('php://input');

        if (!$jsonData = json_decode($rawPostData)) {
            $response->sendParams(false, 400, 'Body no es Válido (JSON)');
        }

        if (!($jsonData->MOVIE_ID) || !($jsonData->ACTOR_ID)) {
            $messages = array();

            (!($jsonData->MOVIE_ID) ? $messages[] = 'MOVIE_ID no ingresado' : false);
            (!($jsonData->ACTOR_ID) ? $messages[] = 'Campo ACTOR_ID no ingresado' : false);

            $response->sendParams(false, 400, $messages);
        }

        $MOVIE_ID = trim($jsonData->MOVIE_ID);
        $ACTOR_ID = trim($jsonData->ACTOR_ID);

        try {

            $movie_actorsDB = new Movie_actorsDB($database);
            $movie_actor = new Movies_actors($MOVIE_ID, $ACTOR_ID);

            $rowCount = $movie_actorsDB->obtenerMovieActors($movie_actor);
            $rowCount = count($rowCount);
            if ($rowCount !== 0) {
                $response->sendParams(false, 409, 'ESTE ACTOR YA SE ENCUENTRA REGISTRADO EN LA PELICULA SELECCIONADA');
            }

            $rowCount = $movie_actorsDB->insertar($movie_actor);

            if ($rowCount === 0) {
                $response->sendParams(false, 404, 'Hubo un error al recuperar el MOVIE_ACTOR creado');
            }

            $returnData = array();
            $returnData['rows_returned'] = $rowCount;
            //$returnData['movie_actors'] = $lastmovie_actors;

            $response->sendParams(true, 201, 'El actor fue registrado correctamente en la pelicula', $returnData);
        } catch (Movies_ActorsException $ex) {
            $response->sendParams(false, 400, $ex->getMessage());
        } catch (PDOException $ex) {
            error_log("Database query error - {$ex}", 0);
            $response->sendParams(false, 500, 'Error al registrar el actor a la pelicula seleccionada');
        }
        break;
    case 'GET':
        if (isset($_GET['MOVIE_ID'])) {
            if (isset($_GET['MOVIE_ID']) && isset($_GET['ACTOR_ID'])) {
                try {
                    $movie_actorsDB = new Movie_actorsDB($database);
                    $movie_actor = new Movies_actors($_GET['MOVIE_ID'], $_GET['ACTOR_ID']);

                    $rowCount = $movie_actorsDB->obtenerMovieActors($movie_actor);
                    $rowCount = count($rowCount);
                    if ($rowCount !== 0) {
                        $response->sendParams(true, 201, 'ESTE ACTOR YA SE ENCUENTRA REGISTRADO EN LA PELICULA SELECCIONADA');
                    }
                } catch (Fav_MoviesException $ex) {
                    $response->sendParams(false, 400, $ex->getMessage());
                } catch (PDOException $ex) {
                    error_log("Database query error - {$ex}", 0);
                    $response->sendParams(false, 500);
                }
            } else {
                try {
                    $movie_Actors = new Movie_actorsDB($database);
                    $data = $movie_Actors->obtenerPorMovieId($_GET['MOVIE_ID']);
                    $rowCount = count($data);

                    if ($rowCount === 0) {
                        $response->sendParams(false, 404, 'Hubo un error al recuperar los actores que participan en una pelicula');
                    }
                    $returnData = array();
                    $returnData['Actors'] = $data;
                    $response->sendParams(true, 201, null, $returnData); //201->Recurso creado
                } catch (Fav_MoviesException $ex) {
                    $response->sendParams(false, 400, $ex->getMessage());
                } catch (PDOException $ex) {
                    error_log("Database query error - {$ex}", 0);
                    $response->sendParams(false, 500);
                }
            }
        }
        break;
    case 'DELETE':
        try {
            $movie_Actors = new Movie_actorsDB($database);
            $rowCount = $movie_Actors->eliminar($_GET['ACTOR_ID'], $_GET['MOVIE_ID']);

            if ($rowCount === 0) {
                $response->sendParams(false, 400, 'ESTE ACTOR NO SE ENCUENTRA REGISTRADO EN LA PELICULA');
            }

            $response->sendParams(true, 200, 'Actor fue Eliminado correctamente de la pelicula seleccionada', null);
        } catch (PDOException $ex) {
            error_log("Database query error - {$ex}", 0);
            $response->sendParams(false, 500, $ex->getMessage());
        }
        break;

    default:
        $response->sendParams(false, 405, 'Tipo de petición no permitida');
        break;
}
