<?php

    require_once('../model/Generos.php');
    require_once('../model/Movie_generos.php');

    class Movie_generoDB{
        private $database;
        public function __construct($database){
            $this->database = $database;
        }

        public function insertar($movies_generos){
            $query = $this->database->prepare('INSERT INTO MOVIE_GENEROS(GENERO_ID, MOVIE_ID) VALUES (?, ?)');
            $genero_id = $movies_generos->getGeneroId();
            $movie_id = $movies_generos->getMovieId();
            $query->bindParam(1, $genero_id, PDO::PARAM_STR);  
            $query->bindParam(2, $movie_id, PDO::PARAM_STR);      
            $query->execute();

            $rowCount = $query->rowCount();

            return $rowCount;
        }

        public function obtenerMovieGenero($movies_generos){
            $query = $this->database->prepare('SELECT * FROM movie_generos WHERE GENERO_ID = ? AND MOVIE_ID = ?');
            $genero_id = $movies_generos->getGeneroId();
            $movie_id = $movies_generos->getMovieId();
            $query->bindParam(1, $genero_id, PDO::PARAM_STR);  
            $query->bindParam(2, $movie_id, PDO::PARAM_STR);     
            $query->execute();

            $MovieGeneroArray = array();

            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $movieG = new Movies_Generos($row['GENERO_ID'],$row['MOVIE_ID']);
                $MovieGeneroArray[] = $movieG->returnMovies_GenerosAsArray();
            }
            return $MovieGeneroArray;
        }

         public function obtenerPorMovieId($MOVIE_ID){
            $query = $this->database->prepare('SELECT  G.GENERO_ID, G.GENERO_NAME 
            FROM MOVIE_GENEROS MG INNER JOIN MOVIES M ON M.MOVIE_ID = MG.MOVIE_ID INNER JOIN GENEROS G ON G.GENERO_ID = MG.GENERO_ID 
            WHERE M.MOVIE_ID = ?');
            
            $query ->bindParam(1, $MOVIE_ID, PDO:: PARAM_STR);
            $query ->execute();
            $generoArray = array();

            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $genero = new Generos($row['GENERO_ID'],$row['GENERO_NAME']);
                $generoArray[] = $genero->returnGenerosAsArray();
            }
            return $generoArray;
        }

        public function eliminar($GENERO_ID,$MOVIE_ID){
            $query = $this->database->prepare('DELETE FROM MOVIE_GENEROS WHERE GENERO_ID = ? AND MOVIE_ID = ?');
            $query->bindParam(1, $GENERO_ID, PDO::PARAM_INT);
            $query->bindParam(2, $MOVIE_ID, PDO::PARAM_INT);
            $query->execute();
            
            $rowCount = $query->rowCount();

            return $rowCount;
        }

    }