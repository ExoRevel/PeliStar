<?php

    require_once('../model/Directors.php');

    class DirectorDB{
        private $database;
        public function __construct($database){
            $this->database = $database;
        }

        public function obtenerPorId($DIRECTOR_ID){
            $query = $this->database->prepare('SELECT DIRECTOR_ID, DIRECTOR_NAME, DATE_FORMAT(DIRECTOR_BIRTHDAY, "%d/%m/%Y") AS DIRECTOR_BIRTHDAY  FROM DIRECTORS WHERE DIRECTOR_ID = ?');
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
            $query = $this->database->prepare('SELECT DIRECTOR_ID, DIRECTOR_NAME, DATE_FORMAT(DIRECTOR_BIRTHDAY, "%d/%m/%Y") AS DIRECTOR_BIRTHDAY 
            FROM DIRECTORS order by DIRECTOR_ID asc');
            $query->execute();

            $directorArray = [];

            while($row = $query->fetch(PDO::FETCH_ASSOC)){
                $director = new Directors($row['DIRECTOR_ID'], $row['DIRECTOR_NAME'], $row['DIRECTOR_BIRTHDAY']);          
                $directorArray[] = $director->returnDirectorAsArray();
             } 
            
            return $directorArray;
        }

        public function obtenerPorName($DIRECTOR_NAME,$DIRECTOR_BIRTHDAY){
            $query = $this->database->prepare('SELECT DIRECTOR_ID, DIRECTOR_NAME, DATE_FORMAT(DIRECTOR_BIRTHDAY, "%d/%m/%Y") AS DIRECTOR_BIRTHDAY FROM DIRECTORS WHERE DIRECTOR_NAME = ? AND DIRECTOR_BIRTHDAY = ?');
            $query->bindParam(1, $DIRECTOR_NAME, PDO::PARAM_STR);
            $query->bindParam(2, $DIRECTOR_BIRTHDAY, PDO::PARAM_STR);
            $query->execute();

            $directorArray = array();
            
            while($row = $query->fetch(PDO::FETCH_ASSOC)){
               $director = new Directors($row['DIRECTOR_ID'], $row['DIRECTOR_NAME'], $row['DIRECTOR_BIRTHDAY']);          

               $directorArray[] = $director->returnDirectorAsArray();
            } 
            
            return $directorArray;
        }

        public function insertar($director){
            $BIRTHDAY = $director->getDirectorBirthday();
            $NAME = $director->getDirectorName();
            $query = $this->database->prepare('INSERT INTO DIRECTORS(DIRECTOR_NAME,DIRECTOR_BIRTHDAY) VALUES (?, ?)');
            $query->bindParam(1, $NAME, PDO::PARAM_STR);
            $query->bindParam(2, $BIRTHDAY, PDO::PARAM_STR);          
            $query->execute();

            $rowCount = $query->rowCount();

            return $rowCount;
        }

        public function actualizarFullnamePorId($DIRECTOR_NAME,$DIRECTOR_ID)
        {
            $query = $this->database->prepare('UPDATE Directors SET  DIRECTOR_NAME = ? WHERE DIRECTOR_ID = ?');
            $query->bindParam(1, $DIRECTOR_NAME, PDO::PARAM_STR);
            $query->bindParam(2, $DIRECTOR_ID, PDO::PARAM_STR);
            $query ->execute();
            $rowCount = $query->rowCount();
            return $rowCount;
        }

        public function actualizarBirthdayPorId($director){
            $BIRTHDAY = $director->getDirectorBirthday();
            $DIRECTOR_ID = $director->getDirectorid();
            $query = $this->database->prepare('UPDATE Directors SET  DIRECTOR_BIRTHDAY = ? WHERE DIRECTOR_ID = ?');
            $query->bindParam(1, $BIRTHDAY, PDO::PARAM_STR);
            $query->bindParam(2, $DIRECTOR_ID, PDO::PARAM_STR);
            $query ->execute();
            $rowCount = $query->rowCount();
            return $rowCount;
        }

    }


