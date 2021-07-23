<?php

    require_once('../model/Movies_Directors.php');

    class Movie_directorDB{
        private $database;
        public function __construct($database){
            $this->database = $database;
        }

        public function insertar($movies_directors){
            $query = $this->database->prepare('INSERT INTO MOVIES_DIRECTORS(MOVIEDIRECTOR_ID, MOVIE_ID) VALUES (?, ?)');
            $query->bindParam(1, $movies_directors->getMoviedirectorId(), PDO::PARAM_STR);  
            $query->bindParam(2, $movies_directors->getMovieId(), PDO::PARAM_STR);      
            $query->execute();

            $rowCount = $query->rowCount();

            return $rowCount;
        }

    }