<?php

    require_once('../model/Fav_movies.php');
    require_once('../model/Generos.php');
    require_once('../model/Movies.php');

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

        public function obtenerPorUserId($USE_ID){
            $query = $this->database->prepare('SELECT FV.USE_ID, FV.MOVIE_ID, M.MOVIE_TITLE, M.MOVIE_DATE, M.MOVIE_TIME, M.MOVIE_SINOPSIS, M.MOVIE_CALIFICATION, G.GENERO_ID, G.GENERO_NAME
            FROM FAV_MOVIES FV INNER JOIN MOVIES M ON M.MOVIE_ID = FV.MOVIE_ID INNER JOIN MOVIE_GENEROS MG ON MG.MOVIE_ID = M.MOVIE_ID
            INNER JOIN GENEROS G ON G.GENERO_ID = MG.GENERO_ID WHERE FV.USE_ID = ?');
            
            $query ->bindParam(1, $USE_ID, PDO:: PARAM_STR);
            $query ->execute();
            $fav_moviesArray = array();

            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $fav_movies = new Fav_Movies($row['MOVIE_ID'], $row['USE_ID']);
                $movies = new Movies($row['MOVIE_ID'],$row['MOVIE_TITLE'],$row['MOVIE_DATE'],$row['MOVIE_TIME'],$row['MOVIE_SINOPSIS'],$row['MOVIE_CALIFICATION']);
                $genero= new Generos($row['GENERO_ID'], $row['GENERO_NAME']);
                $fav_moviesArray[] = $fav_movies->returnFav_MoviesAsArray() + $movies->returnMoviesAsArray() + $genero->returnGenerosAsArray();
            }
            return $fav_moviesArray;
        }

        public function eliminar($USE_ID,$MOVIE_ID){
            $query = $this->database->prepare('DELETE FROM FAV_MOVIES WHERE USE_ID = ? AND MOVIE_ID = ?');
            $query->bindParam(1, $USE_ID, PDO::PARAM_INT);
            $query->bindParam(2, $MOVIE_ID, PDO::PARAM_INT);
            $query->execute();
            
            $rowCount = $query->rowCount();

            return $rowCount;
        }

    }