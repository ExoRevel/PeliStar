<?php

    class DirectorsException extends Exception{}

    class Directors{
        private $DIRECTOR_ID;
        private $DIRECTOR_NAME;
        private $DIRECTOR_BIRTHDAY;//access token


        public function __construct($DIRECTOR_ID, $DIRECTOR_NAME, $DIRECTOR_BIRTHDAY){
            $this->setDirectorid($DIRECTOR_ID);
            $this->setDirectorName($DIRECTOR_NAME);
            $this->setDirectorBirthday($DIRECTOR_BIRTHDAY);
        }

        public function getDirectorid(){
            return $this->DIRECTOR_ID;
        }

        public function getDirectorName(){
            return $this->DIRECTOR_NAME;
        }

        public function getDirectorBirthday(){
            return $this->DIRECTOR_BIRTHDAY;
        }

        public function setDirectorid($DIRECTOR_ID){
            if(($DIRECTOR_ID!==null) && (!is_numeric($DIRECTOR_ID) || $DIRECTOR_ID<=0 || $DIRECTOR_ID > 9223372036854775807)){
                throw new DirectorsException('Error en DIRECTOR_ID');
            }

            $this->DIRECTOR_ID = $DIRECTOR_ID;
        }

        public function setDirectorName($DIRECTOR_NAME){           
            if(($DIRECTOR_NAME!==null) && (!is_numeric($DIRECTOR_NAME) || $DIRECTOR_NAME<=0 || $DIRECTOR_NAME > 9223372036854775807)){
                throw new DirectorsException('Error en DIRECTOR_NAME');
            }

            $this->DIRECTOR_NAME = $DIRECTOR_NAME;
        }

        public function setDirectorBirthday($DIRECTOR_BIRTHDAY){           
            if($DIRECTOR_BIRTHDAY!== null && (strlen($DIRECTOR_BIRTHDAY) < 0 || strlen($DIRECTOR_BIRTHDAY) > 100)){
                throw new DirectorsException('Error en DIRECTOR_BIRTHDAY');
            }

            $this->DIRECTOR_BIRTHDAY = $DIRECTOR_BIRTHDAY;
        }



        public function returnDirectorAsArray(){
            $Director = array();
            $Director['DIRECTOR_ID'] = $this->getDirectorid();
            $Director['DIRECTOR_NAME'] = $this->getDirectorName();
            $Director['DIRECTOR_BIRTHDAY'] = $this->getDirectorBirthday();          

            return $Director;
        }
    }