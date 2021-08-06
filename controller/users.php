<?php

require_once('../config/database.php');
require_once('../data/UserDB.php');
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
header('Access-Control-Allow-Origin: *');
//Opciones de preflight (CORS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Methods: POST, OPTIONS, GET, PATCH');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Max-Age: 86400');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    $response->sendParams(true, 200);
}

switch ($_SERVER['REQUEST_METHOD']) {
    case 'POST':

        if ($_SERVER['CONTENT_TYPE'] !== 'application/json') {
            $response->sendParams(false, 400, 'Content type header no válido');
        }

        $rawPostData = file_get_contents('php://input');

        if (!$jsonData = json_decode($rawPostData)) { //Si no se pudo decodificar el body a un objeto JavaScript
            $response->sendParams(false, 400, 'Body no es válido (JSON)');
        }


        if ($jsonData->USE_ROL != null) {
            //Authorization
            $verificarToken = checkAuthStatusAndReturnUser($database);
        }

        if (!($jsonData->USE_FULLNAME) || !($jsonData->USE_USERNAME) || !($jsonData->USE_PASSWORD)) {
            $messages = array();

            (!($jsonData->USE_FULLNAME) ? $messages[] = 'Nombre no ingresado ' : false);
            (!($jsonData->USE_USERNAME) ? $messages[] = 'Username no ingresado ' : false);
            (!($jsonData->USE_PASSWORD) ? $messages[] = 'Contraseña no ingresada ' : false);

            $response->sendParams(false, 400, $messages);
        }

        $USE_FULLNAME = trim($jsonData->USE_FULLNAME);
        $USE_USERNAME = trim($jsonData->USE_USERNAME);
        $USE_PASSWORD = $jsonData->USE_PASSWORD;
        $USE_ROL = $jsonData->USE_ROL;
        try {
            $userDB = new UserDB($database);
            $existingUser = $userDB->obtenerPorUsername($USE_USERNAME);
            $rowCount = count($existingUser);

            if ($rowCount !== 0) {
                $response->sendParams(false, 409, 'Usuario ya se encuentra registrado, use otro username'); //409: Conflicto
            }

            $USE_PASSWORD = password_hash($USE_PASSWORD, PASSWORD_DEFAULT);
            $user = new User(null, $USE_FULLNAME, $USE_USERNAME, $USE_PASSWORD, null, null);
            if ($USE_ROL === 'admin') {
                //Authorization
                $verificarToken = checkAuthStatusAndReturnUser($database);
                $user->constructAdmin(null, $USE_FULLNAME, $USE_USERNAME, $USE_PASSWORD, null, null);
            }

            $rowCount = $userDB->insertar($user);

            if ($rowCount === 0) {
                $response->sendParams(false, 500, 'Hubo un error al intentar registrar el usuario');
            }

            $lastUser = $userDB->obtenerPorId($database->lastInsertId());
            $rowCount = count($lastUser);

            if ($rowCount === 0) {
                $response->sendParams(false, 404, 'Hubo un error al recuperar el user creado');
            }

            $returnData = array();
            $returnData['rows_returned'] = $rowCount;
            //$returnData['users'] = $lastUser;

            $response->sendParams(true, 201, 'User insertado correctamente', $returnData); //201->Recurso creado
        } catch (UserException $ex) {
            $response->sendParams(false, 400, $ex->getMessage());
        } catch (PDOException $ex) {
            error_log("Database query error - {$ex}", 0);
            $response->sendParams(false, 500, 'Error al intentar crear la cuenta de usuario');
        }
        break;

    case 'GET':
        if (isset($_GET['USE_USERNAME'])) {
            try {
                $userDB = new UserDB($database);
                $data = $userDB->obtenerPorUsername($_GET['USE_USERNAME']);
                $rowCount = count($data);

                if ($rowCount === 0) {
                    $response->sendParams(false, 404, 'USERNAME INCORRECTO');
                }
                $returnData = array();
                $returnData['users'] = $data;
                $response->sendParams(true, 201, null, $returnData); //201->Recurso creado
            } catch (UserException $ex) {
                $response->sendParams(false, 400, $ex->getMessage());
            } catch (PDOException $ex) {
                error_log("Database query error - {$ex}", 0);
                $response->sendParams(false, 500);
            }
        } else {
            try {
                $userDB = new UserDB($database);
                $data = $userDB->obtenerTodosUsuarios();
                $rowCount = count($data);

                if ($rowCount === 0) {
                    $response->sendParams(false, 404, 'Hubo un error al recuperar los usuarios');
                }
                $returnData = array();
                $returnData['users'] = $data;
                $response->sendParams(true, 201, null, $returnData); //201->Recurso creado
            } catch (UserException $ex) {
                $response->sendParams(false, 400, $ex->getMessage());
            } catch (PDOException $ex) {
                error_log("Database query error - {$ex}", 0);
                $response->sendParams(false, 500);
            }
        }
        break;
    case 'PATCH':
        //Authorization
        $user = checkAuthStatusAndReturnUser($database);
        if ($_SERVER['CONTENT_TYPE'] !== 'application/json') {
            $response->sendParams(false, 400, 'Content type header no válido');
        }

        $rawPostData = file_get_contents('php://input');

        if (!$jsonData = json_decode($rawPostData)) { //Si no se pudo decodificar el body a un objeto JavaScript
            $response->sendParams(false, 400, 'Body no es válido (JSON)');
        }


        $USE_FULLNAME = trim($jsonData->USE_FULLNAME);
        $USE_USERNAME = trim($jsonData->USE_USERNAME);
        $USE_PASSWORD = $jsonData->USE_PASSWORD;
        $USE_ID = $jsonData->USE_ID;
        try {
            $userDB = new UserDB($database);
            $existingUser = $userDB->obtenerPorId($USE_ID);
            $rowCount = count($existingUser);

            if ($rowCount === 0) {
                $response->sendParams(false, 409, 'VUELVA A INICIAR SESION'); //409: Conflicto
            }

            if ($USE_FULLNAME != null) {
                $rowCount = $userDB->actualizarFullNamePorId($USE_ID, $USE_FULLNAME);
                if ($rowCount === 0) {
                    $response->sendParams(false, 404, 'ERROR! Ingrese un nombre diferente al registrado, o deje la casilla en blanco');
                } else {
                    $response->sendParams(true, 201, 'Nombre modificado correctamente');
                }
            }

            if ($USE_PASSWORD != null) {
                $USE_PASSWORD = password_hash($USE_PASSWORD, PASSWORD_DEFAULT);
                $rowCount = $userDB->actualizarPasswordPorId($USE_ID, $USE_PASSWORD);
                if ($rowCount === 0) {
                    $response->sendParams(false, 404, 'ERROR! Ingrese una contraseña diferente a la registrada, o deje la casilla en blanco');
                } else {
                    $response->sendParams(true, 201, 'Contraseña modificada correctamente');
                }
            }

            if ($USE_USERNAME != null) {
                $rowCount = $userDB->actualizarUserNamePorId($USE_ID, $USE_USERNAME);
                if ($rowCount === 0) {
                    $response->sendParams(false, 404, 'ERROR! Ingrese un USERNAME diferente al registrado, o deje la casilla en blanco');
                } else {
                    $response->sendParams(true, 201, 'Username modificado correctamente, necesitará volver a iniciar sesión');
                }
            }

            if (!($jsonData->USE_FULLNAME) && !($jsonData->USE_USERNAME) && !($jsonData->USE_PASSWORD)) {
                $messages = array();

                (!($jsonData->USE_FULLNAME) ? $messages[] = 'Nombre no ingresado ' : false);
                (!($jsonData->USE_USERNAME) ? $messages[] = 'Username no ingresado ' : false);
                (!($jsonData->USE_PASSWORD) ? $messages[] = 'Contraseña no ingresada ' : false);
                $response->sendParams(false, 400, $messages);
            }

            $returnData = array();
            $response->sendParams(true, 201, 'User MODIFICADO correctamente', $returnData); //201->Recurso creado
        } catch (UserException $ex) {
            $response->sendParams(false, 400, $ex->getMessage());
        } catch (PDOException $ex) {
            error_log("Database query error - {$ex}", 0);
            $response->sendParams(false, 500, 'Error al intentar crear la cuenta de usuario');
        }
        break;
    default:
        $response->sendParams(false, 405, 'Tipo de petición no permitida');
        break;
}
