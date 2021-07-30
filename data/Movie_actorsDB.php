<?php

    require_once('../model/Movies_Actors.php');
    require_once('../model/Actors.php');
    class Movie_actorsDB{
        private $database;
        public function __construct($database){
            $this->database = $database;
        }

        public function insertar($movie_actors){
            $MOVIE_ID = $movie_actors->getMovieId();
            $ACTOR_ID = $movie_actors->getActorId();
            $query = $this->database->prepare('INSERT INTO MOVIES_ACTORS(MOVIE_ID, ACTOR_ID) VALUES (?, ?)');
            $query->bindParam(1, $MOVIE_ID, PDO::PARAM_STR);  
            $query->bindParam(2, $ACTOR_ID, PDO::PARAM_STR);      
            $query->execute();

            $rowCount = $query->rowCount();

            return $rowCount;
        }

        public function obtenerMovieActors($movie_actors){
            $MOVIE_ID = $movie_actors->getMovieId();
            $ACTOR_ID = $movie_actors->getActorId();
            $query = $this->database->prepare('SELECT * FROM movies_actors WHERE MOVIE_ID = ? AND ACTOR_ID = ? ');
            $query->bindParam(1, $MOVIE_ID, PDO::PARAM_STR);  
            $query->bindParam(2, $ACTOR_ID, PDO::PARAM_STR);      
            $query ->execute();
            $actorsArray = array();

            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $actor = new Movies_Actors($row['MOVIE_ID'], $row['ACTOR_ID']);
                $actorsArray[] = $actor->returnMovies_ActorsAsArray();
            }
            return $actorsArray;
        }

        public function obtenerPorTitleAndDate($MOVIE_TITLE, $MOVIE_DATE){
            $query = $this->database->prepare('SELECT A.ACTOR_ID, A.ACTOR_FULLNAME, A.ACTOR_BIRTHDAY 
            FROM MOVIES_ACTORS MA INNER JOIN MOVIES M ON M.MOVIE_ID = MA.MOVIE_ID INNER JOIN ACTORS A ON A.ACTOR_ID = MA.ACTOR_ID
            WHERE M.MOVIE_TITLE = ?  AND M.MOVIE_DATE = ?');
            
            $query ->bindParam(1, $MOVIE_TITLE, PDO:: PARAM_STR);
            $query ->bindParam(2, $MOVIE_DATE, PDO:: PARAM_STR);
            $query ->execute();
            $actorsArray = array();

            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $actor = new Actors($row['ACTOR_ID'],$row['ACTOR_FULLNAME'], $row['ACTOR_BIRTHDAY']);
                $actorsArray[] = $actor->returnActorsAsArray();
            }
            return $actorsArray;
        }

        public function eliminar($ACTOR_ID,$MOVIE_ID){
            $query = $this->database->prepare('DELETE FROM MOVIES_ACTORS WHERE MOVIE_ID = ? AND ACTOR_ID = ?');
            $query->bindParam(1, $MOVIE_ID, PDO::PARAM_INT);
            $query->bindParam(2, $ACTOR_ID, PDO::PARAM_INT);
            $query->execute();
            
            $rowCount = $query->rowCount();

            return $rowCount;
        }
        
    }