<?php

    class Movies_DirectorsException extends Exception{}

    class Movies_Directors{
        private $MOVIEDIRECTOR_ID;
        private $MOVIE_ID;

        public function __construct($MOVIEDIRECTOR_ID, $MOVIE_ID){
            $this->getMoviedirectorId($MOVIEDIRECTOR_ID);
            $this->getMovieId($MOVIE_ID);   
        }

        public function getMoviedirectorId(){
            return $this->MOVIEDIRECTOR_ID;
        }

        public function getMovieId(){
            return $this->MOVIE_ID;
        }


        public function setMoviedirectorId($MOVIEDIRECTOR_ID){
            if(($MOVIEDIRECTOR_ID===null) || (!is_numeric($MOVIEDIRECTOR_ID) || $MOVIEDIRECTOR_ID<=0 || $MOVIEDIRECTOR_ID > 9223372036854775807)){
                throw new Movies_DirectorsException('Error en MOVIEDIRECTOR_ID');
            }

            $this->MOVIEDIRECTOR_ID = $MOVIEDIRECTOR_ID;
        }

        public function setMovieId($MOVIE_ID){           
            if(($MOVIE_ID===null) || (!is_numeric($MOVIE_ID) || $MOVIE_ID<=0 || $MOVIE_ID > 9223372036854775807)){
                throw new Movies_DirectorsException('Error en MOVIE_ID');
            }

            $this->MOVIE_ID = $MOVIE_ID;
        }

        public function returnMovies_DirectorsAsArray(){
            $Movies_Directors = array();
            $Movies_Directors['MOVIEDIRECTOR_ID'] = $this->getMoviedirectorId();
            $Movies_Directors['MOVIE_ID'] = $this->getMovieId();        

            return $Movies_Directors;
        }
    }