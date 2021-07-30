<?php

    class GenerosException extends Exception{}

    class Generos{
        private $GENERO_ID;
        private $GENERO_NAME;

        public function __construct($GENERO_ID, $GENERO_NAME){
            $this->setGeneroId($GENERO_ID);
            $this->setGeneroName($GENERO_NAME);
        }

        public function getGeneroId(){
            return $this->GENERO_ID;
        }

        public function getGeneroName(){
            return $this->GENERO_NAME;
        }


        public function setGeneroId($GENERO_ID){
            if(($GENERO_ID!==null) && (!is_numeric($GENERO_ID) || $GENERO_ID<=0 || $GENERO_ID > 9223372036854775807)){
                throw new GenerosException('Error en GENERO_ID');
            }

            $this->GENERO_ID = $GENERO_ID;
        }

        public function setGeneroName($GENERO_NAME){           
            if(($GENERO_NAME!==null) && (strlen($GENERO_NAME) < 0 || strlen($GENERO_NAME) > 50)){
                throw new GenerosException('Error en el nombre del Genero');
            }

            $this->GENERO_NAME = $GENERO_NAME;
        }


        public function returnGenerosAsArray(){
            $Generos = array();
            $Generos['GENERO_ID'] = $this->getGeneroId();
            $Generos['GENERO_NAME'] = $this->getGeneroName();         

            return $Generos;
        }
    }