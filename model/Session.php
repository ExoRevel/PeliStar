<?php

    class SessionException extends Exception{}

    class Session{
        private $SES_ID;
        private $USE_ID;
        private $SES_ACCTOK;//access token
        private $SES_ACCTOKEXP;//access token expiration
        private $SES_REFTOK;//refresh token
        private $SES_REFTOKEXP;//refresh token expiration

        public function __construct($SES_ID, $USE_ID, $SES_ACCTOK, $SES_ACCTOKEXP, $SES_REFTOK, $SES_REFTOKEXP){
            $this->setId($SES_ID);
            $this->setUseId($USE_ID);
            $this->setAcctok($SES_ACCTOK);
            $this->setAcctokexp($SES_ACCTOKEXP);            
            $this->setReftok($SES_REFTOK);
            $this->setReftokexp($SES_REFTOKEXP);
        }

        public function getId(){
            return $this->SES_ID;
        }

        public function getUseId(){
            return $this->USE_ID;
        }

        public function getAcctok(){
            return $this->SES_ACCTOK;
        }

        public function getAcctokexp(){
            return $this->SES_ACCTOKEXP;
        }

        public function getReftok(){
            return $this->SES_REFTOK;
        }

        public function getReftokexp(){
            return $this->SES_REFTOKEXP;
        }

        public function setId($SES_ID){
            if(($SES_ID!==null) && (!is_numeric($SES_ID) || $SES_ID<=0 || $SES_ID > 9223372036854775807)){
                throw new SessionException('Error en SES_ID');
            }

            $this->SES_ID = $SES_ID;
        }

        public function setUseId($USE_ID){           
            if(($USE_ID!==null) && (!is_numeric($USE_ID) || $USE_ID<=0 || $USE_ID > 9223372036854775807)){
                throw new SessionException('Error en USE_ID');
            }

            $this->USE_ID = $USE_ID;
        }

        public function setAcctok($SES_ACCTOK){           
            if($SES_ACCTOK!== null && (strlen($SES_ACCTOK) < 0 || strlen($SES_ACCTOK) > 100)){
                throw new SessionException('Error en SES_ACCTOK');
            }

            $this->SES_ACCTOK = $SES_ACCTOK;
        }

        public function setAcctokexp($SES_ACCTOKEXP){           
            if($SES_ACCTOKEXP !== null &&  date_format(date_create_from_format('d/m/Y H:i', $SES_ACCTOKEXP), 'd/m/Y H:i') != $SES_ACCTOKEXP){
                throw new SessionException('Error en SES_ACCTOKEXP');
            }

            $this->SES_ACCTOKEXP = $SES_ACCTOKEXP;
        }          

        public function setReftok($SES_REFTOK){           
            if($SES_REFTOK!== null && (strlen($SES_REFTOK) < 0 || strlen($SES_REFTOK) > 100)){
                throw new SessionException('Error en SES_REFTOK');
            }

            $this->SES_REFTOK = $SES_REFTOK;
        }

        public function setReftokexp($SES_REFTOKEXP){           
            if($SES_REFTOKEXP !== null &&  date_format(date_create_from_format('d/m/Y H:i', $SES_REFTOKEXP), 'd/m/Y H:i') != $SES_REFTOKEXP){
                throw new SessionException('Error en SES_REFTOKEXP');
            }

            $this->SES_REFTOKEXP = $SES_REFTOKEXP;
        }      

        public function returnSessionAsArray(){
            $session = array();
            $session['SES_ID'] = $this->getId();
            $session['USE_ID'] = $this->getUseId();
            $session['SES_ACCTOK'] = $this->getAcctok();
            $session['SES_ACCTOKEXP'] = $this->getAcctokexp();         
            $session['SES_REFTOK'] = $this->getReftok();
            $session['SES_REFTOKEXP'] = $this->getReftokexp();           

            return $session;
        }
    }