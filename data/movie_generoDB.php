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

         public function obtenerPorTitleAndDate($MOVIE_TITLE, $MOVIE_DATE){
            $query = $this->database->prepare('SELECT A.ACTOR_ID, A.ACTOR_FULLNAME, A.ACTOR_BIRTHDAY 
            FROM MOVIES_ACTORS MA INNER JOIN MOVIES M ON M.MOVIE_ID = MA.MOVIE_ID INNER JOIN ACTORS A ON A.ACTOR_ID = MA.ACTOR_ID
            WHERE M.MOVIE_TITLE = ? AND M.MOVIE_DATE = ?');
            
            $query ->bindParam(1, $MOVIE_TITLE, PDO:: PARAM_STR);
            $query ->bindParam(2, $MOVIE_DATE, PDO:: PARAM_STR);
            $query ->execute();
            $generoArray = array();

            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $genero = new Generos($row['GENERO_ID'],$row['GENERO_NAME']);
                $generoArray[] = $genero->returnGenerosAsArray();
            }
            return $generoArray;
        }

        public function eliminar($GENERO_ID,$MOVIE_ID){
            $query = $this->database->prepare('DELETE FROM MOVIES_GENEROS WHERE GENERO_ID = ? AND MOVIE_ID = ?');
            $query->bindParam(1, $GENERO_ID, PDO::PARAM_INT);
            $query->bindParam(2, $MOVIE_ID, PDO::PARAM_INT);
            $query->execute();
            
            $rowCount = $query->rowCount();

            return $rowCount;
        }

    }