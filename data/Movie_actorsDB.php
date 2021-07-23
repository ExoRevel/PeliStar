<?php

    require_once('../model/Movies_Actors.php');

    class Movie_actorsDB{
        private $database;
        public function __construct($database){
            $this->database = $database;
        }

        public function insertar($movie_actors){
            $query = $this->database->prepare('INSERT INTO MOVIES_ACTORS(MOVIE_ID, ACTOR_ID) VALUES (?, ?)');
            $query->bindParam(1, $movie_actors->getMovieId(), PDO::PARAM_STR);  
            $query->bindParam(2, $movie_actors->getActorId(), PDO::PARAM_STR);      
            $query->execute();

            $rowCount = $query->rowCount();

            return $rowCount;
        }

        /*OBTENER LOS DATOS DE LOS ACTORES QUE PARTICIPAN EN UNA PELICULA EN ESPECIFICO TOMANDO COMO DATO DE REFERENCIA EL TITULO Y EL DATE ---> WHERE M.MOVIE_TITLE = ? AND M.MOVIE_DATE = ?*/
        
    }