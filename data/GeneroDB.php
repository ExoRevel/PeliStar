<?php

    require_once('../model/Generos.php');

    class GeneroDB{
        private $database;
        public function __construct($database){
            $this->database = $database;
        }

        public function obtenerPorId($GENERO_ID){
            $query = $this->database->prepare('SELECT GENERO_ID, GENERO_NAME FROM GENEROS WHERE GENERO_ID = ?');
            $query ->bindParam(1, $GENERO_ID, PDO:: PARAM_STR);
            $query ->execute();

            $generoArray = array();

            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $genero= new Generos($row['GENERO_ID'], $row['GENERO_NAME']);

                $generoArray[] = $genero->returnGenerosAsArray();
            }
            return $generoArray;

        }

        public function obtenerTodosGeneros(){
            $query = $this->database->prepare('SELECT * FROM GENEROS order by GENERO_ID asc');
            $query->execute();

            $generoArray = [];

            while($row = $query->fetch(PDO::FETCH_ASSOC)){
                $genero = new Generos($row['GENERO_ID'], $row['GENERO_NAME']);          
                $generoArray[] = $genero->returnGenerosAsArray();
             } 
            
            return $generoArray;
        }



        public function obtenerPorName($GENERO_NAME){
            $query = $this->database->prepare('SELECT GENERO_ID, GENERO_NAME FROM GENEROS WHERE GENERO_NAME = ?');
            $query->bindParam(1, $GENERO_NAME, PDO::PARAM_STR);
            $query->execute();

            $generoArray = array();
            
            while($row = $query->fetch(PDO::FETCH_ASSOC)){
               $genero = new Generos($row['GENERO_ID'], $row['GENERO_NAME']);          

               $generoArray[] = $genero->returnGenerosAsArray();
            } 
            
            return $generoArray;
        }



        public function insertar($genero){
            $query = $this->database->prepare('INSERT INTO GENEROS(GENERO_NAME) VALUES (?)');
            $query->bindParam(1, $genero->getGeneroName(), PDO::PARAM_STR);      
            $query->execute();

            $rowCount = $query->rowCount();

            return $rowCount;
        }


        public function actualizarNamePorId($NAME,$GENERO_ID)
        {
            $query = $this->database->prepare('UPDATE Generos SET  GENERO_NAME = ? WHERE GENERO_ID = ?');
            $query->bindParam(1, $NAME, PDO::PARAM_STR);
            $query->bindParam(2, $GENERO_ID, PDO::PARAM_STR);
            $query ->execute();
            $rowCount = $query->rowCount();
            return $rowCount;
        }

    }