<?php

    class Fav_MoviesException extends Exception{}

    class Fav_Movies{
        private $USE_ID;
        private $MOVIE_ID;

        public function __construct($MOVIE_ID, $USE_ID){
            $this->setMovieId($MOVIE_ID);
            $this->setUseId($USE_ID);   
        }

        public function getMovieId(){
            return $this->MOVIE_ID;
        }

        public function getUseID(){
            return $this->USE_ID;
        }


        public function setMovieId($MOVIE_ID){
            if(($MOVIE_ID!==null) && (!is_numeric($MOVIE_ID) || $MOVIE_ID<=0 || $MOVIE_ID > 9223372036854775807)){
                throw new Fav_MoviesException('Error en MOVIE_ID');
            }

            $this->MOVIE_ID = $MOVIE_ID;
        }

        public function setUseId($USE_ID){           
            if(($USE_ID!==null) || (!is_numeric($USE_ID) || $USE_ID<=0 || $USE_ID > 9223372036854775807)){
                throw new Fav_MoviesException('Error en USE_ID');
            }

            $this->USE_ID = $USE_ID;
        }

        public function returnFav_MoviesAsArray(){
            $Fav_Movies = array();
            $Fav_Movies['MOVIE_ID'] = $this->getMovieId();
            $Fav_Movies['USE_ID'] = $this->getUseID();        
            return $Fav_Movies;
        }
    }