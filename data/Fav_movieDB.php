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
            $query->bindParam(1, $fav_movies->getMovieId(), PDO::PARAM_STR);  
            $query->bindParam(2, $fav_movies->getUseID(), PDO::PARAM_STR);      
            $query->execute();

            $rowCount = $query->rowCount();

            return $rowCount;
        }

        public function obtenerPorUSERNAME($USE_USERNAME){
            $query = $this->database->prepare('SELECT U.USE_ID, FM.MOVIE_ID, M.MOVIE_TITLE, M.MOVIE_DATE, M.MOVIE_TIME, M.MOVIE_SINOPSIS, 
            M.MOVIE_CALIFICATION, G.GENERO_ID, G.GENERO_NAME FROM USERS U INNER JOIN  FAV_MOVIES FM ON FM.USE_ID = U.USE_ID 
            INNER JOIN MOVIES M ON M.MOVIE_ID = FM.MOVIE_ID INNER JOIN MOVIE_GENEROS MG ON MG.MOVIE_ID = M.MOVIE_ID 
            INNER JOIN GENEROS G ON G.GENERO_ID = MG.GENERO_ID WHERE U.USE_USERNAME = ? ');
            
            $query ->bindParam(1, $USE_USERNAME, PDO:: PARAM_STR);
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