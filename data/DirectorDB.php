<?php

    require_once('../model/Directors.php');

    class DirectorDB{
        private $database;
        public function __construct($database){
            $this->database = $database;
        }

        public function obtenerPorId($DIRECTOR_ID){
            $query = $this->database->prepare('SELECT DIRECTOR_ID, DIRECTOR_NAME, DIRECTOR_BIRTHDAY FROM DIRECTOR WHERE DIRECTOR_ID = ?');
            $query ->bindParam(1, $DIRECTOR_ID, PDO:: PARAM_STR);
            $query ->execute();

            $directorArray = array();

            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $director= new Directors($row['DIRECTOR_ID'], $row['DIRECTOR_NAME'], $row['DIRECTOR_BIRTHDAY']);

                $directorArray[] = $director->returnDirectorAsArray();
            }

            return $directorArray;

        }

        public function obtenerTodosDirectores(){
            $query = $this->database->prepare('SELECT * FROM DIRECTORS');
            $query->execute();

            $directorArray = [];

            while($row = $query->fetch(PDO::FETCH_ASSOC)){
                $director = new Directors($row['DIRECTOR_ID'], $row['DIRECTOR_NAME'], $row['DIRECTOR_BIRTHDAY']);          
                $adirectorArray[] = $director->returnDirectorAsArray();
             } 
            
            return $directorArray;
        }

        public function obtenerPorName($DIRECTOR_NAME){
            $query = $this->database->prepare('SELECT DIRECTOR_ID, DIRECTOR_NAME, DIRECTOR_BIRTHDAY FROM DIRECTORS WHERE DIRECTOR_NAME = ?');
            $query->bindParam(1, $DIRECTOR_NAME, PDO::PARAM_STR);
            $query->execute();

            $directorArray = array();
            
            while($row = $query->fetch(PDO::FETCH_ASSOC)){
               $director = new Directors($row['DIRECTOR_ID'], $row['DIRECTOR_NAME'], $row['DIRECTOR_BIRTHDAY']);          

               $directorArray[] = $director->returnDirectorAsArray();
            } 
            
            return $directorArray;
        }

        public function insertar($director){
            $query = $this->database->prepare('INSERT INTO DIRECTORS(DIRECTOR_NAME,DIRECTOR_BIRTHDAY) VALUES (?, ?)');
            $query->bindParam(1, $director->getDirectorName(), PDO::PARAM_STR);
            $query->bindParam(2, $director->getDirectorBirthday(), PDO::PARAM_STR);          
            $query->execute();

            $rowCount = $query->rowCount();

            return $rowCount;
        }





    }

