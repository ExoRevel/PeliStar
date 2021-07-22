<?php

    require_once('../model/Movies_Actors.php');

    class Movie_actorsDB{
        private $database;
        public function __construct($database){
            $this->database = $database;
        }

        public function insertar($movie_actors){
            $query = $this->database->prepare('INSERT INTO MOVIE_ACTORS(MOVIE_ID, ACTOR_ID) VALUES (?, ?)');
            $query->bindParam(1, $movie_actors->MOVIE_ID(), PDO::PARAM_STR);  
            $query->bindParam(2, $movie_actors->ACTOR_ID(), PDO::PARAM_STR);      
            $query->execute();

            $rowCount = $query->rowCount();

            return $rowCount;
        }

    }