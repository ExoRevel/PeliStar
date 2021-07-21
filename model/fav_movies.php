<?php

    class Fav_MoviesException extends Exception{}

    class Fav_Movies{
        private $USE_ID;
        private $MOVIE_ID;

        public function __construct($MOVIE_ID, $USE_ID){
            $this->setMovieid($MOVIE_ID);
            $this->setUSERid($USE_ID);   
        }

        public function getMovieid(){
            return $this->MOVIE_ID;
        }

        public function getUSerID(){
            return $this->USE_ID;
        }


        public function setMovieid($MOVIE_ID){
            if(($MOVIE_ID!==null) && (!is_numeric($MOVIE_ID) || $MOVIE_ID<=0 || $MOVIE_ID > 9223372036854775807)){
                throw new Fav_MoviesException('Error en MOVIE_ID');
            }

            $this->MOVIE_ID = $MOVIE_ID;
        }

        public function setUSERid($ACTOR_ID){           
            if(($ACTOR_ID!==null) && (!is_numeric($ACTOR_ID) || $ACTOR_ID<=0 || $ACTOR_ID > 9223372036854775807)){
                throw new Fav_MoviesException('Error en ACTOR_ID');
            }

            $this->ACTOR_ID = $ACTOR_ID;
        }

        public function returnFav_MoviesAsArray(){
            $Fav_Movies = array();
            $Fav_Movies['MOVIE_ID'] = $this->getMovieid();
            $Fav_Movies['ACTOR_ID'] = $this->getUSerID();        

            return $Fav_Movies;
        }
    }