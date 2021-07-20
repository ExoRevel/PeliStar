<?php

    class Movies_ActorsException extends Exception{}

    class Movies_Actors{
        private $MOVIE_ID;
        private $ACTOR_ID;

        public function __construct($MOVIE_ID, $ACTOR_ID){
            $this->setMovieid($MOVIE_ID);
            $this->setActorid($ACTOR_ID);   
        }

        public function getMovieid(){
            return $this->MOVIE_ID;
        }

        public function getActorid(){
            return $this->ACTOR_ID;
        }


        public function setMovieid($MOVIE_ID){
            if(($MOVIE_ID!==null) && (!is_numeric($MOVIE_ID) || $MOVIE_ID<=0 || $MOVIE_ID > 9223372036854775807)){
                throw new Movies_ActorsException('Error en MOVIE_ID');
            }

            $this->MOVIE_ID = $MOVIE_ID;
        }

        public function setActorid($ACTOR_ID){           
            if(($ACTOR_ID!==null) && (!is_numeric($ACTOR_ID) || $ACTOR_ID<=0 || $ACTOR_ID > 9223372036854775807)){
                throw new Movies_ActorsException('Error en ACTOR_ID');
            }

            $this->ACTOR_ID = $ACTOR_ID;
        }

        public function returnMovies_ActorsAsArray(){
            $Movies_Actors = array();
            $Movies_Actors['MOVIE_ID'] = $this->getMovieid();
            $Movies_Actors['ACTOR_ID'] = $this->getActorid();        

            return $Movies_Actors;
        }
    }