<?php

    require_once('../model/Movies.php');

    class MovieDB{
        private $database;
        public function __construct($database){
            $this->database = $database;
        }

        public function obtenerPorId($MOVIE_ID){
            $query = $this->database->prepare('SELECT MOVIE_ID, MOVIE_TITLE, DATE_FORMAT(MOVIE_DATE,"%d/%m/%Y") AS MOVIE_DATE, MOVIE_TIME, MOVIE_SINOPSIS, MOVIE_CALIFICATION FROM MOVIES WHERE MOVIE_ID = ?');
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
            $query = $this->database->prepare('SELECT MOVIE_ID, MOVIE_TITLE, DATE_FORMAT(MOVIE_DATE,"%d/%m/%Y") AS MOVIE_DATE , MOVIE_TIME, MOVIE_SINOPSIS, MOVIE_CALIFICATION FROM MOVIES');
            $query->execute();

            $movieArray = [];

            while($row = $query->fetch(PDO::FETCH_ASSOC)){
                $movie = new Movies($row['MOVIE_ID'], $row['MOVIE_TITLE'], $row['MOVIE_DATE'], $row['MOVIE_TIME'], $row['MOVIE_SINOPSIS'], $row['MOVIE_CALIFICATION']);          
                $movieArray[] = $movie->returnMoviesAsArray();
             } 
            
            return $movieArray;
        } 

        public function obtenerPorTitleAndDate($MOVIE_TITLE, $MOVIE_DATE){
            $query = $this->database->prepare('SELECT MOVIE_ID, MOVIE_TITLE, DATE_FORMAT(MOVIE_DATE,"%d/%m/%Y") AS MOVIE_DATE , MOVIE_TIME, MOVIE_SINOPSIS, MOVIE_CALIFICATION FROM MOVIES WHERE MOVIE_TITLE = ? AND MOVIE_DATE = ? ');
            $query->bindParam(1, $MOVIE_TITLE, PDO::PARAM_STR);
            $query->bindParam(2, $MOVIE_DATE, PDO::PARAM_STR);
            $query->execute();

            $movieArray = array();
            
            while($row = $query->fetch(PDO::FETCH_ASSOC)){
               $movie = new Movies($row['MOVIE_ID'], $row['MOVIE_TITLE'], $row['MOVIE_DATE'], $row['MOVIE_TIME'], $row['MOVIE_SINOPSIS'], $row['MOVIE_CALIFICATION']);          

               $movieArray[] = $movie->returnMoviesAsArray();
            } 
            
            return $movieArray;
        }

        public function insertar($movie){
            $title = $movie->getTitle();
            $date = $movie->getDate();
            $time = $movie->getTime();
            $sinopsis = $movie->getSinopsis();
            $calification = $movie->getCalification();
            $query = $this->database->prepare('INSERT INTO MOVIES( MOVIE_TITLE,MOVIE_DATE, MOVIE_TIME, MOVIE_SINOPSIS, MOVIE_CALIFICATION) VALUES (?, ?, ?, ?, ?)');
            $query->bindParam(1, $title, PDO::PARAM_STR);
            $query->bindParam(2, $date, PDO::PARAM_STR);   
            $query->bindParam(3, $time, PDO::PARAM_STR);
            $query->bindParam(4, $sinopsis, PDO::PARAM_STR);
            $query->bindParam(5, $calification, PDO::PARAM_STR);       
            $query->execute();

            $rowCount = $query->rowCount();

            return $rowCount;
        }

        public function actualizarTituloPorId($MOVIE_TITLE,$MOVIE_ID)
        {
            $query = $this->database->prepare('UPDATE MOVIES SET  MOVIE_TITLE = ? WHERE MOVIE_ID = ?');
            $query->bindParam(1, $MOVIE_TITLE, PDO::PARAM_STR);
            $query->bindParam(2, $MOVIE_ID, PDO::PARAM_STR);
            $query ->execute();
            $rowCount = $query->rowCount();
            return $rowCount;
        }

        public function actualizarFechaEstrenoPorId($MOVIE_DATE,$MOVIE_ID)
        {
            $query = $this->database->prepare('UPDATE MOVIES SET  MOVIE_DATE = ? WHERE MOVIE_ID = ?');
            $query->bindParam(1, $MOVIE_DATE, PDO::PARAM_STR);
            $query->bindParam(2, $MOVIE_ID, PDO::PARAM_STR);
            $query ->execute();
            $rowCount = $query->rowCount();
            return $rowCount;
        }

        public function actualizarDuracionPorId($MOVIE_TIME,$MOVIE_ID)
        {
            $query = $this->database->prepare('UPDATE MOVIES SET  MOVIE_TIME = ? WHERE MOVIE_ID = ?');
            $query->bindParam(1, $MOVIE_TIME, PDO::PARAM_STR);
            $query->bindParam(2, $MOVIE_ID, PDO::PARAM_STR);
            $query ->execute();
            $rowCount = $query->rowCount();
            return $rowCount;
        }

        public function actualizarSinopsisPorId($MOVIE_SINOPSIS,$MOVIE_ID)
        {
            $query = $this->database->prepare('UPDATE MOVIES SET  MOVIE_SINOPSIS = ? WHERE MOVIE_ID = ?');
            $query->bindParam(1, $MOVIE_SINOPSIS, PDO::PARAM_STR);
            $query->bindParam(2, $MOVIE_ID, PDO::PARAM_STR);
            $query ->execute();
            $rowCount = $query->rowCount();
            return $rowCount;
        }
        
        public function actualizarCalifacionPorId($MOVIE_CALIFICATION,$MOVIE_ID)
        {
            $query = $this->database->prepare('UPDATE MOVIES SET  MOVIE_CALIFICATION = ? WHERE MOVIE_ID = ?');
            $query->bindParam(1, $MOVIE_CALIFICATION, PDO::PARAM_STR);
            $query->bindParam(2, $MOVIE_ID, PDO::PARAM_STR);
            $query ->execute();
            $rowCount = $query->rowCount();
            return $rowCount;
        }

        function eliminarRelacionesFv_movie($MOVIE_ID)
        {
            $query = $this->database->prepare('DELETE FROM FAV_MOVIES WHERE MOVIE_ID = ?');
            $query->bindParam(1, $MOVIE_ID, PDO::PARAM_STR);
            $query ->execute();
            $rowCount = $query->rowCount();
            return $rowCount;
        }

        function eliminarRelacionesMovie_generos($MOVIE_ID)
        {
            $query = $this->database->prepare('DELETE FROM movie_generos WHERE MOVIE_ID = ?');
            $query->bindParam(1, $MOVIE_ID, PDO::PARAM_STR);
            $query ->execute();
            $rowCount = $query->rowCount();
            return $rowCount;
        }

        function eliminarRelacionesMovie_actors($MOVIE_ID)
        {
            $query = $this->database->prepare('DELETE FROM MOVIES_ACTORS WHERE MOVIE_ID = ?');
            $query->bindParam(1, $MOVIE_ID, PDO::PARAM_STR);
            $query ->execute();
            $rowCount = $query->rowCount();
            return $rowCount;
        }

        function eliminarRelacionesMovie_directors($MOVIE_ID)
        {
            $query = $this->database->prepare('DELETE FROM MOVIES_DIRECTORS WHERE MOVIE_ID = ?');
            $query->bindParam(1, $MOVIE_ID, PDO::PARAM_STR);
            $query ->execute();
            $rowCount = $query->rowCount();
            return $rowCount;
        }

        function eliminarRelacionesMovie_images($MOVIE_ID)
        {
            $query = $this->database->prepare('DELETE FROM images WHERE MOVIE_ID = ?');
            $query->bindParam(1, $MOVIE_ID, PDO::PARAM_STR);
            $query ->execute();
            $rowCount = $query->rowCount();
            return $rowCount;
        }

        function eliminarMovie($MOVIE_ID)
        {
            $query = $this->database->prepare('DELETE FROM  MOVIES WHERE MOVIE_ID = ?');
            $query->bindParam(1, $MOVIE_ID, PDO::PARAM_STR);
            $query ->execute();
            $rowCount = $query->rowCount();
            return $rowCount;
        }

        public function eliminarMovieYrelaciones($MOVIE_ID)
        {
            $rowCount = 0;
            $rowCount = $rowCount +  $this->eliminarRelacionesFv_movie($MOVIE_ID);
            $rowCount = $rowCount +  $this->eliminarRelacionesMovie_generos($MOVIE_ID);
            $rowCount = $rowCount +  $this->eliminarRelacionesMovie_actors($MOVIE_ID);
            $rowCount = $rowCount +  $this->eliminarRelacionesMovie_directors($MOVIE_ID);
            $rowCount = $rowCount +  $this->eliminarRelacionesMovie_images($MOVIE_ID);
            $rowCount = $rowCount +  $this->eliminarMovie($MOVIE_ID);
            return $rowCount;
        }
    
    }