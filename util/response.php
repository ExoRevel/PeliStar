<?php

    class Response{
        private $success;//si se hizo bien la peticion o no
        private $httpStatusCode;//el estado de la respuesta del servidor
        private $messages = array();//mensaje que devolverá el servidor
        private $data;//es la información que se devolverá
        private $toCache = false;//si es que se le almacena en caché para que cuando se ejecuta la misma consulta en un tiempo determinado entonces solo usa el dato almacenado en cache
        private $responseData = array();//es la concatenación de los atributos de arriba y es lo que se le enviará al cliente

        //Se arma la respuesta y se envía al cliente
        public function send(){
            //Se establece el tipo de información que se envía
            header('Content-type: application/json;charset=utf-8');

            //Se establece si se almacenará info en la memoria cache por un minuto
            if($this->toCache){
                header('Cache-control: max-age=60');
            }
            else{
                header('Cache-control: no-cache, no-store');
            }

            //Se verifica si se puede o no generar la respuesta
            if(($this->success!== false && $this->success!== true) || !is_numeric($this->httpStatusCode)){
                http_response_code(500);

                $this->responseData['statusCode'] = 500;
                $this->responseData['success'] = false;
                $this->messages[]= 'Error al crear la respuesta';
                $this->responseData['messages'] = $this->messages;
            }
            else{
                http_response_code($this->httpStatusCode);
                $this->responseData['statusCode'] = $this->httpStatusCode;
                $this->responseData['success'] = $this->success;
                $this->responseData['messages'] = $this->messages;
                $this->responseData['data'] = $this->data;
            }

            echo json_encode($this->responseData);
        }
        //es la función que se va a usar externamente
        public function sendParams($success, $httpStatusCode, $messages = null, $data = null, $toCache = false){
            $this->success = $success;
            $this->httpStatusCode = $httpStatusCode;      
            //si el atributo es un arreglo, será tal cual
            if(is_array($messages))            
                $this->messages = $messages;
            else
                $this->messages[] = $messages;//sino es un arreglo entonces se hará un arreglo al atributo
                
            if($data != null)
                $this->data = $data;     

            $this->toCache = $toCache;

            $this->send();
            exit;
        }
    }