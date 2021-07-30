<?php

    class Movies_DirectorsException extends Exception{}

    class Movies_Directors{
        private $DIRECTOR_ID;
        private $MOVIE_ID;

        public function __construct($DIRECTOR_ID, $MOVIE_ID){
            $this->setDirectorId($DIRECTOR_ID);
            $this->setMovieId($MOVIE_ID);   
        }

        public function getDirectorId(){
            return $this->DIRECTOR_ID;
        }

        public function getMovieId(){
            return $this->MOVIE_ID;
        }


        public function setDirectorId($DIRECTOR_ID){
            if(($DIRECTOR_ID!==null) && (!is_numeric($DIRECTOR_ID) || $DIRECTOR_ID<=0 || $DIRECTOR_ID > 9223372036854775807)){
                throw new Movies_DirectorsException('Error en los datos del Director');
            }

            $this->DIRECTOR_ID = $DIRECTOR_ID;
        }

        public function setMovieId($MOVIE_ID){           
            if(($MOVIE_ID!==null) && (!is_numeric($MOVIE_ID) || $MOVIE_ID<=0 || $MOVIE_ID > 9223372036854775807)){
                throw new Movies_DirectorsException('Error en los datos de la pelicula');
            }

            $this->MOVIE_ID = $MOVIE_ID;
        }

        public function returnMovies_DirectorsAsArray(){
            $Movies_Directors = array();
            $Movies_Directors['DIRECTOR_ID'] = $this->getDirectorId();
            $Movies_Directors['MOVIE_ID'] = $this->getMovieId();        

            return $Movies_Directors;
        }
    }