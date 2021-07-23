<?php

    require_once('../model/Movie_Generos.php');

    class Movie_generoDB{
        private $database;
        public function __construct($database){
            $this->database = $database;
        }

        public function insertar($movies_generos){
            $query = $this->database->prepare('INSERT INTO MOVIE_GENEROS(GENERO_ID, MOVIE_ID) VALUES (?, ?)');
            $query->bindParam(1, $movies_generos->getGeneroId(), PDO::PARAM_STR);  
            $query->bindParam(2, $movies_generos->getMovieId(), PDO::PARAM_STR);      
            $query->execute();

            $rowCount = $query->rowCount();

            return $rowCount;
        }
         /*OBTENER LOS DATOS DE GENERO DE UNA PELICULA EN ESPECIFICO TOMANDO COMO DATO DE REFERENCIA EL TITULO Y EL DATE ---> WHERE M.MOVIE_TITLE = ? AND M.MOVIE_DATE = ?*/

    }