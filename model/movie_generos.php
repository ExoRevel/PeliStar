<?php

    class Movies_GenerosException extends Exception{}

    class Movies_Generos{
        private $GENERO_ID;
        private $MOVIE_ID;

        public function __construct($GENERO_ID, $MOVIE_ID){
            $this->setGeneroid($GENERO_ID);
            $this->setMovieid($MOVIE_ID);   
        }

        public function getGeneroid(){
            return $this->GENERO_ID;
        }

        public function getMovieid(){
            return $this->MOVIE_ID;
        }


        public function setGeneroid($GENERO_ID){
            if(($GENERO_ID!==null) && (!is_numeric($GENERO_ID) || $GENERO_ID<=0 || $GENERO_ID > 9223372036854775807)){
                throw new Movies_GenerosException('Error en GENERO_ID');
            }

            $this->GENERO_ID = $GENERO_ID;
        }

        public function setMovieid($MOVIE_ID){           
            if(($MOVIE_ID!==null) && (!is_numeric($MOVIE_ID) || $MOVIE_ID<=0 || $MOVIE_ID > 9223372036854775807)){
                throw new Movies_GenerosException('Error en MOVIE_ID');
            }

            $this->MOVIE_ID = $MOVIE_ID;
        }

        public function returnMovies_GenerosAsArray(){
            $Movies_Generos = array();
            $Movies_Generos['GENERO_ID'] = $this->getGeneroid();
            $Movies_Generos['MOVIE_ID'] = $this->getMovieid();        

            return $Movies_Generos;
        }
    }