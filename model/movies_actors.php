<?php

    class Movies_ActorsException extends Exception{}

    class Movies_Actors{
        private $MOVIE_ID;
        private $ACTOR_ID;

        public function __construct($MOVIE_ID, $ACTOR_ID){
            $this->setMovieId($MOVIE_ID);
            $this->setActorId($ACTOR_ID);   
        }

        public function getMovieId(){
            return $this->MOVIE_ID;
        }

        public function getActorId(){
            return $this->ACTOR_ID;
        }


        public function setMovieId($MOVIE_ID){
            if(($MOVIE_ID!==null) && (!is_numeric($MOVIE_ID) || $MOVIE_ID<=0 || $MOVIE_ID > 9223372036854775807)){
                throw new Movies_ActorsException('Error en los datos de la pelicula');
            }

            $this->MOVIE_ID = $MOVIE_ID;
        }

        public function setActorId($ACTOR_ID){           
            if(($ACTOR_ID!==null) && (!is_numeric($ACTOR_ID) || $ACTOR_ID<=0 || $ACTOR_ID > 9223372036854775807)){
                throw new Movies_ActorsException('Error en los datos del actor');
            }

            $this->ACTOR_ID = $ACTOR_ID;
        }

        public function returnMovies_ActorsAsArray(){
            $Movies_Actors = array();
            $Movies_Actors['MOVIE_ID'] = $this->getMovieId();
            $Movies_Actors['ACTOR_ID'] = $this->getActorId();        

            return $Movies_Actors;
        }
    }