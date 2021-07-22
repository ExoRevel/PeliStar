<?php

    require_once('../model/Movies_Generos.php');

    class Movie_generoDB{
        private $database;
        public function __construct($database){
            $this->database = $database;
        }

        public function insertar($movies_generos){
            $query = $this->database->prepare('INSERT INTO MOVIE_GENEROS(GENERO_ID, MOVIE_ID) VALUES (?, ?)');
            $query->bindParam(1, $movies_generos->MOVIE_ID(), PDO::PARAM_STR);  
            $query->bindParam(2, $movies_generos->USE_ID(), PDO::PARAM_STR);      
            $query->execute();

            $rowCount = $query->rowCount();

            return $rowCount;
        }

    }