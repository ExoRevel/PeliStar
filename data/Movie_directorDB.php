<?php

    require_once('../model/Directors.php');
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

         public function obtenerPorTitleAndDate($MOVIE_TITLE, $MOVIE_DATE){
            $query = $this->database->prepare('SELECT D.DIRECTOR_ID, D.DIRECTOR_NAME, D.DIRECTOR_BIRTHDAY
            FROM MOVIES_DIRECTORS MD INNER JOIN MOVIES M ON M.MOVIE_ID = MD.DIRECTOR_ID INNER JOIN DIRECTORS D ON D.DIRECTOR_ID = MD.DIRECTOR_ID
            WHERE M.MOVIE_TITLE = ? AND M.MOVIE_DATE = ?');
            
            $query ->bindParam(1, $MOVIE_TITLE, PDO:: PARAM_STR);
            $query ->bindParam(2, $MOVIE_DATE, PDO:: PARAM_STR);
            $query ->execute();
            $directorArray = array();

            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $directors = new Directors($row['DIRECTOR_ID'],$row['DIRECTOR_NAME'],$row['DIRECTOR_BIRTHDAY']);
                $directorArray[] = $directors->returnDirectorAsArray();
            }
            return $directorArray;
        }

        public function eliminar($DIRECTOR_ID,$MOVIE_ID){
            $query = $this->database->prepare('DELETE FROM MOVIES_DIRECTORS WHERE DIRECTOR_ID = ? AND MOVIE_ID = ?');
            $query->bindParam(1, $DIRECTOR_ID, PDO::PARAM_INT);
            $query->bindParam(2, $MOVIE_ID, PDO::PARAM_INT);
            $query->execute();
            
            $rowCount = $query->rowCount();

            return $rowCount;
        }




    }