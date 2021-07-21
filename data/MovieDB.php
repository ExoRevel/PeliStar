<?php

    require_once('../model/Movies.php');

    class MovieDB{
        private $database;
        public function __construct($database){
            $this->database = $database;
        }

        public function obtenerPorId($MOVIE_ID){
            $query = $this->database->prepare('SELECT MOVIE_ID, MOVIE_TITLE, MOVIE_DATE, MOVIE_TIME, MOVIE_SINOPSIS, MOVIE_CALIFICATION FROM MOVIES WHERE MOVIE_ID = ?');
            $query ->bindParam(1, $MOVIE_ID, PDO:: PARAM_STR);
            $query ->execute();

            $movieArray = array();

            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $movie= new Movies($row['MOVIE_ID'], $row['MOVIE_TITLE'], $row['MOVIE_DATE'], $row['MOVIE_TIME'], $row['MOVIE_SINOPSIS'], $row['MOVIE_CALIFICATION']);

                $movieArray[] = $movie->returnMoviesAsArray();
            }

            return $movieArray;
        }

        public function obtenerTodosMovies(){
            $query = $this->database->prepare('SELECT * FROM MOVIES');
            $query->execute();

            $movieArray = [];

            while($row = $query->fetch(PDO::FETCH_ASSOC)){
                $movie = new Movies($row['MOVIE_ID'], $row['MOVIE_TITLE'], $row['MOVIE_DATE'], $row['MOVIE_TIME'], $row['MOVIE_SINOPSIS'], $row['MOVIE_CALIFICATION']);          
                $movieArray[] = $movie->returnMoviesAsArray();
             } 
            
            return $movieArray;
        } 

        public function obtenerPorFullName($MOVIE_TITLE){
            $query = $this->database->prepare('SELECT MOVIE_ID, MOVIE_TITLE,MOVIE_DATE, MOVIE_TIME, MOVIE_SINOPSIS, MOVIE_CALIFICATION FROM MOVIES WHERE MOVIE_TITLE = ?');
            $query->bindParam(1, $MOVIE_TITLE, PDO::PARAM_STR);
            $query->execute();

            $movieArray = array();
            
            while($row = $query->fetch(PDO::FETCH_ASSOC)){
               $movie = new Movies($row['MOVIE_ID'], $row['MOVIE_TITLE'], $row['MOVIE_DATE'], $row['MOVIE_TIME'], $row['MOVIE_SINOPSIS'], $row['MOVIE_CALIFICATION']);          

               $movieArray[] = $movie->returnMoviesAsArray();
            } 
            
            return $movieArray;
        }

        public function insertar($movie){
            $query = $this->database->prepare('INSERT INTO MOVIES( MOVIE_TITLE,MOVIE_DATE, MOVIE_TIME, MOVIE_SINOPSIS, MOVIE_CALIFICATION) VALUES (?, ?, ?, ?, ?)');
            $query->bindParam(1, $movie->getTitle(), PDO::PARAM_STR);
            $query->bindParam(2, $movie->getDate(), PDO::PARAM_STR);   
            $query->bindParam(3, $movie->getTime(), PDO::PARAM_STR);
            $query->bindParam(4, $movie->getSinopsis(), PDO::PARAM_STR);
            $query->bindParam(5, $movie->getCalification(), PDO::PARAM_STR);       
            $query->execute();

            $rowCount = $query->rowCount();

            return $rowCount;
        }


    
    
    }