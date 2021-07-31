<?php

    require_once('../model/Actors.php');

    class ActorDB{
        private $database;
        public function __construct($database){
            $this->database = $database;
        }

        public function obtenerPorId($ACTOR_ID){
            $query = $this->database->prepare('SELECT ACTOR_ID, ACTOR_FULLNAME, DATE_FORMAT(ACTOR_BIRTHDAY, "%d/%m/%Y") AS ACTOR_BIRTHDAY FROM ACTORS WHERE ACTOR_ID = ?');
            $query ->bindParam(1, $ACTOR_ID, PDO:: PARAM_STR);
            $query ->execute();

            $actorArray = array();

            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $actor= new Actors($row['ACTOR_ID'], $row['ACTOR_FULLNAME'], $row['ACTOR_BIRTHDAY']);

                $actorArray[] = $actor->returnActorsAsArray();
            }

            return $actorArray;

        }
        
        public function obtenerTodosActores(){
            $query = $this->database->prepare('SELECT ACTOR_ID, ACTOR_FULLNAME, DATE_FORMAT(ACTOR_BIRTHDAY, "%d/%m/%Y") AS ACTOR_BIRTHDAY FROM ACTORS');
            $query->execute();

            $actorArray = [];

            while($row = $query->fetch(PDO::FETCH_ASSOC)){
                $actor = new Actors($row['ACTOR_ID'], $row['ACTOR_FULLNAME'], $row['ACTOR_BIRTHDAY']);          
                $actorArray[] = $actor->returnActorsAsArray();
             } 
            
            return $actorArray;
        } 
        
        public function obtenerPorFullName($ACTOR_FULLNAME, $ACTOR_BIRTHDAY){
            $query = $this->database->prepare('SELECT ACTOR_ID, ACTOR_FULLNAME, DATE_FORMAT(ACTOR_BIRTHDAY, "%d/%m/%Y") AS ACTOR_BIRTHDAY FROM ACTORS WHERE ACTOR_FULLNAME = ? AND ACTOR_BIRTHDAY = ?');
            $query->bindParam(1, $ACTOR_FULLNAME, PDO::PARAM_STR);
            $query->bindParam(2, $ACTOR_BIRTHDAY, PDO::PARAM_STR);
            $query->execute();

            $actorArray = array();
            
            while($row = $query->fetch(PDO::FETCH_ASSOC)){
               $actor = new Actors($row['ACTOR_ID'], $row['ACTOR_FULLNAME'], $row['ACTOR_BIRTHDAY']);          

               $actorArray[] = $actor->returnActorsAsArray();
            } 
            
            return $actorArray;
        }

        public function insertar($actor){
            $FULLNAME = $actor->getFullname();
            $BIRTHDAY = $actor->getBirthday();
            $query = $this->database->prepare('INSERT INTO ACTORS(ACTOR_FULLNAME,ACTOR_BIRTHDAY) VALUES (?, ?)');
            $query->bindParam(1, $FULLNAME, PDO::PARAM_STR);
            $query->bindParam(2, $BIRTHDAY, PDO::PARAM_STR);          
            $query->execute();

            $rowCount = $query->rowCount();

            return $rowCount;
        }

        public function actualizarDatos($actor)
        {
            $paramNumber = 0;
            $queryFields = '';
            $FULLNAME = $actor->getFullname();
            $BIRTHDAY = $actor->getBirthday();
            $ACTOR_ID = $actor->getId();
            if($FULLNAME!=null){ //Se verifica que campo fue ingresado
                $queryFields .= 'ACTOR_FULLNAME = ?, '; 
            }
            if($BIRTHDAY!=null){ //Se verifica que campo fue ingresado
                $queryFields .= 'ACTOR_BIRTHDAY = ?, ';   
            }      
            $query = $this->database->prepare('UPDATE Actors SET {$queryFields}  WHERE ACTOR_ID = ?');
            if($FULLNAME!==null){ 
                $query->bindParam(3, $FULLNAME, PDO::PARAM_STR);
            }
            if($BIRTHDAY!==null){ 
                $query->bindParam(2, $BIRTHDAY, PDO::PARAM_STR);
            }
            $query->bindParam(3, $FULLNAME, PDO::PARAM_STR);
            $query->bindParam(2, $BIRTHDAY, PDO::PARAM_STR);
            $query->bindParam(1, $ACTOR_ID, PDO::PARAM_STR);
            //$query->bindParam($paramNumber++, $BIRTHDAY, PDO::PARAM_STR);          
            $query->execute();
            $rowCount = $query->rowCount();

            return $rowCount;
        }

        public function actualizarFullnamePorId($FULLNAME,$ACTOR_ID)
        {
            $query = $this->database->prepare('UPDATE Actors SET  ACTOR_FULLNAME = ? WHERE ACTOR_ID = ?');
            $query->bindParam(1, $FULLNAME, PDO::PARAM_STR);
            $query->bindParam(2, $ACTOR_ID, PDO::PARAM_STR);
            $query ->execute();
            $rowCount = $query->rowCount();
            return $rowCount;
        }

        public function actualizarBirthdayPorId($actor){
            $BIRTHDAY = $actor->getBirthday();
            $ACTOR_ID = $actor->getId();
            $query = $this->database->prepare('UPDATE Actors SET  ACTOR_BIRTHDAY = ? WHERE ACTOR_ID = ?');
            $query->bindParam(1, $BIRTHDAY, PDO::PARAM_STR);
            $query->bindParam(2, $ACTOR_ID, PDO::PARAM_STR);
            $query ->execute();
            $rowCount = $query->rowCount();
            return $rowCount;
        }


    }