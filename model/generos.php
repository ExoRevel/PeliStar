<?php

    class GenerosException extends Exception{}

    class Generos{
        private $GENERO_ID;
        private $GENERO_NAME;

        public function __construct($GENERO_ID, $GENERO_NAME){
            $this->setGeneroid($GENERO_ID);
            $this->setGeneroname($GENERO_NAME);
        }

        public function getGeneroid(){
            return $this->GENERO_ID;
        }

        public function getGeneroname(){
            return $this->GENERO_NAME;
        }


        public function setGeneroid($GENERO_ID){
            if(($GENERO_ID!==null) && (!is_numeric($GENERO_ID) || $GENERO_ID<=0 || $GENERO_ID > 9223372036854775807)){
                throw new GenerosException('Error en GENERO_ID');
            }

            $this->GENERO_ID = $GENERO_ID;
        }

        public function setGeneroname($GENERO_NAME){           
            if(($GENERO_NAME!==null) && (!is_numeric($GENERO_NAME) || $GENERO_NAME<=0 || $GENERO_NAME > 9223372036854775807)){
                throw new GenerosException('Error en GENERO_NAME');
            }

            $this->GENERO_NAME = $GENERO_NAME;
        }


        public function returnGenerosAsArray(){
            $Generos = array();
            $Generos['GENERO_ID'] = $this->getGeneroid();
            $Generos['GENERO_NAME'] = $this->getGeneroname();         

            return $Generos;
        }
    }