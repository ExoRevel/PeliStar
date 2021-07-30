<?php

    require_once('../model/Directors.php');
    require_once('../model/Movies_Directors.php');
    class Movie_directorDB{
        private $database;
        public function __construct($database){
            $this->database = $database;
        }

        public function insertar($movies_directors){
            $DIRECTOR_ID = $movies_directors->getDirectorId();
            $MOVIE_ID = $movies_directors->getMovieId();
            $query = $this->database->prepare('INSERT INTO MOVIES_DIRECTORS(DIRECTOR_ID, MOVIE_ID) VALUES (?, ?)');
            $query->bindParam(1, $DIRECTOR_ID, PDO::PARAM_STR);  
            $query->bindParam(2, $MOVIE_ID, PDO::PARAM_STR);      
            $query->execute();

            $rowCount = $query->rowCount();

            return $rowCount;
        }

        public function obtenerMovieDirectors($movies_directors){
            $DIRECTOR_ID = $movies_directors->getDirectorId();
            $MOVIE_ID = $movies_directors->getMovieId();
            $query = $this->database->prepare('SELECT * FROM movies_directors WHERE DIRECTOR_ID = ? AND MOVIE_ID = ?');
            $query->bindParam(1, $DIRECTOR_ID, PDO::PARAM_STR);  
            $query->bindParam(2, $MOVIE_ID, PDO::PARAM_STR);      
            $query->execute();
            $MovieDirectorArray = array();

            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $movieD = new Movies_Directors($row['DIRECTOR_ID'],$row['MOVIE_ID']);
                $MovieDirectorArray[] = $movieD->returnMovies_DirectorsAsArray();
            }
            return $MovieDirectorArray;
        }
        
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