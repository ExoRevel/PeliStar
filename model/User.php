<?php

    class UserException extends Exception{}

    class User{
        private $USE_ID;
        private $USE_FULLNAME;
        private $USE_USERNAME;
        private $USE_PASSWORD;
        private $USE_ACTIVE;
        private $USE_LOGAT;
        private $USE_ROL;

        public function __construct($USE_ID, $USE_FULLNAME, $USE_USERNAME, $USE_PASSWORD, $USE_ACTIVE, $USE_LOGAT){
            $this->setId($USE_ID);
            $this->setFullname($USE_FULLNAME);
            $this->setUsername($USE_USERNAME);
            $this->setPassword($USE_PASSWORD);
            $this->setActive($USE_ACTIVE);
            $this->setLogat($USE_LOGAT);
            $this->setRol('client');
        }

        public function constructAdmin($USE_ID, $USE_FULLNAME, $USE_USERNAME, $USE_PASSWORD, $USE_ACTIVE, $USE_LOGAT){
            $this->setId($USE_ID);
            $this->setFullname($USE_FULLNAME);
            $this->setUsername($USE_USERNAME);
            $this->setPassword($USE_PASSWORD);
            $this->setActive($USE_ACTIVE);
            $this->setLogat($USE_LOGAT);
            $this->setRol('admin');
            return $this;
        }

        public function getId(){
            return $this->USE_ID;
        }

        public function getFullname(){
            return $this->USE_FULLNAME;
        }

        public function getUsername(){
            return $this->USE_USERNAME;
        }

        public function getPassword(){
            return $this->USE_PASSWORD;
        }

        public function getActive(){
            return $this->USE_ACTIVE;
        }

        public function getLogat(){
            return $this->USE_LOGAT;
        }

        public function getRol(){
            return $this->USE_ROL;
        }

        public function setId($USE_ID){
            if(($USE_ID!==null) && (!is_numeric($USE_ID) || $USE_ID<=0 || $USE_ID > 9223372036854775807)){
                throw new UserException('Error en USE_ID');
            }

            $this->USE_ID = $USE_ID;
        }

        public function setRol($USE_ROL){
            if($USE_ROL!== null && (strlen($USE_ROL) <= 0 || strlen($USE_ROL) > 6)){
                throw new UserException('Error en el rol del usuario'); //Mensaje debe cambiar
            }

            $this->USE_ROL = $USE_ROL;
        }

        public function setFullname($USE_FULLNAME){           
            if($USE_FULLNAME!== null && (strlen($USE_FULLNAME) <= 0 || strlen($USE_FULLNAME) > 255)){
                throw new UserException('Error en el nombre del usuario');
            }

            $this->USE_FULLNAME = $USE_FULLNAME;
        }

        public function setUsername($USE_USERNAME){           
            if($USE_USERNAME!== null && (strlen($USE_USERNAME) <= 0 || strlen($USE_USERNAME) > 255)){
                throw new UserException('Error en el username del usuario'); //Mensaje debe cambiar
            }
            $this->USE_USERNAME = $USE_USERNAME;
        }

        public function setPassword($USE_PASSWORD){           
            if($USE_PASSWORD!== null && (strlen($USE_PASSWORD) <= 0 || strlen($USE_PASSWORD) > 255)){
                throw new UserException('Error en la contraseña'); //Mensaje debe cambiar
            }

            $this->USE_PASSWORD = $USE_PASSWORD;
        }

        public function setActive($USE_ACTIVE){           
            if($USE_ACTIVE !== null && (!is_numeric($USE_ACTIVE) || ($USE_ACTIVE != 0 && $USE_ACTIVE != 1))){
                throw new UserException('Error en USE_ACTIVE');
            }

            $this->USE_ACTIVE = $USE_ACTIVE;
        }

        public function setLogat($USE_LOGAT){           
            if($USE_LOGAT !== null && (!is_numeric($USE_LOGAT) || $USE_LOGAT<0 || $USE_LOGAT > 2147483647)){
                throw new UserException('Error en USE_LOGAT');
            }

            $this->USE_LOGAT = $USE_LOGAT;
        }

        public function returnUserAsArray(){
            $user = array();
            $user['USE_ID'] = $this->getId();
            $user['USE_FULLNAME'] = $this->getFullname();
            $user['USE_USERNAME'] = $this->getUsername();
            $user['USE_PASSWORD'] = $this->getPassword();
            $user['USE_ACTIVE'] = $this->getActive();
            $user['USE_LOGAT'] = $this->getLogat();
            $user['USE_ROL'] = $this->getRol();
            return $user;
        }
        
    }