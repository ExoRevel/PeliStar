<?php
 
    require_once('../data/SessionDB.php');    
    require_once('response.php');

    function checkAuthStatusAndReturnUser($database){

        //Respuesta que se enviará al cliente    
        $response = new Response();
                
        if(!isset($_SERVER['HTTP_AUTHORIZATION']) || strlen($_SERVER['HTTP_AUTHORIZATION']) < 1 ){
            $response->sendParams(false, 401, 'El access token debe estar presente y no debe estar en blanco');
        }

        $SES_ACCTOK = $_SERVER['HTTP_AUTHORIZATION'];

        try{
            $sessionDB = new SessionDB($database);
            $returnData = $sessionDB->obtenerUserPorAccessToken($SES_ACCTOK);

            $rowCount = count($returnData['SESSION']);
                    
            if($rowCount === 0){
                $response->sendParams(false, 401, 'El access token es incorrecto');
            }

            $session = $returnData['SESSION'][0];
            $user = $returnData['USER'][0];            

            if($user['USE_ACTIVE'] == 0){
                $response->sendParams(false, 401, 'La cuenta no se encuentra activa');
            }

            if($user['USE_LOGAT'] >= 3){
                $response->sendParams(false, 401, 'La cuenta ha sido bloqueada');
            }

            $token_expiry_time = DateTime::createFromFormat('d/m/Y H:i', $session['SES_ACCTOKEXP'])->getTimestamp();

            if($token_expiry_time < time()){
                $response->sendParams(false, 401, 'El access token ha expirado. Inicia sesión nuevamente');
            }

            return $user;
        }
        catch(SesssionException $ex){
            $response->sendParams(false, 400, $ex->getMessage());
        }
        catch(UserException $ex){
            $response->sendParams(false, 400, $ex->getMessage());
        }
        catch(PDOException $ex){
            error_log("Database query error - {$ex}", 0);
            $response->sendParams(false, 500, 'Hubo un error al intentar autenticar al usuario');
        }  
    }