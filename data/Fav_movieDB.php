<?php

    require_once('../model/Fav_Movies.php');

    class Fav_movieDB{
        private $database;
        public function __construct($database){
            $this->database = $database;
        }

        public function insertar($fav_movies){
            $query = $this->database->prepare('INSERT INTO FAV_MOVIES(MOVIE_ID, USE_ID) VALUES (?, ?)');
            $query->bindParam(1, $fav_movies->MOVIE_ID(), PDO::PARAM_STR);  
            $query->bindParam(2, $fav_movies->USE_ID(), PDO::PARAM_STR);      
            $query->execute();

            $rowCount = $query->rowCount();

            return $rowCount;
        }

    }