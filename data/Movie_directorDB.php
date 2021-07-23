<?php

    require_once('../model/Movies_Directors.php');

    class Movie_directorDB{
        private $database;
        public function __construct($database){
            $this->database = $database;
        }

        public function insertar($movies_directors){
            $query = $this->database->prepare('INSERT INTO MOVIES_DIRECTORS(DIRECTOR_ID, MOVIE_ID) VALUES (?, ?)');
            $query->bindParam(1, $movies_directors->getDirectorId(), PDO::PARAM_STR);  
            $query->bindParam(2, $movies_directors->getMovieId(), PDO::PARAM_STR);      
            $query->execute();

            $rowCount = $query->rowCount();

            return $rowCount;
        }

         /*OBTENER LOS DATOS DE LOS DIRECTORS QUE PARTICIPAN EN UNA PELICULA EN ESPECIFICO TOMANDO COMO DATO DE REFERENCIA EL TITULO Y EL DATE ---> WHERE M.MOVIE_TITLE = ? AND M.MOVIE_DATE = ?*/

    }