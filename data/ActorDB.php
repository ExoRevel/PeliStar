<?php

    require_once('../model/Actors.php');

    class ActorDB{
        private $database;
        public function __construct($database){
            $this->database = $database;
        }

        public function obtenerPorId($ACTOR_ID){
            $query = $this->database->prepare('SELECT ACTOR_ID, ACTOR_FULLNAME, ACTOR_BIRTHDAY FROM ACTORS WHERE ACTOR_ID = ?');
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
            $query = $this->database->prepare('SELECT * FROM ACTORS');
            $query->execute();

            $actorArray = [];

            while($row = $query->fetch(PDO::FETCH_ASSOC)){
                $actor = new Actors($row['ACTOR_ID'], $row['ACTOR_FULLNAME'], $row['ACTOR_BIRTHDAY']);          
                $actorArray[] = $actor->returnActorsAsArray();
             } 
            
            return $actorArray;
        } 
        
        public function obtenerPorFullName($ACTOR_FULLNAME){
            $query = $this->database->prepare('SELECT ACTOR_ID, ACTOR_FULLNAME, ACTOR_BIRTHDAY FROM ACTORS WHERE ACTOR_FULLNAME = ?');
            $query->bindParam(1, $ACTOR_FULLNAME, PDO::PARAM_STR);
            $query->execute();

            $actorArray = array();
            
            while($row = $query->fetch(PDO::FETCH_ASSOC)){
               $actor = new Actors($row['ACTOR_ID'], $row['ACTOR_FULLNAME'], $row['ACTOR_BIRTHDAY']);          

               $actorArray[] = $actor->returnActorsAsArray();
            } 
            
            return $actorArray;
        }

        public function insertar($actor){
            $query = $this->database->prepare('INSERT INTO ACTORS(ACTOR_FULLNAME,ACTOR_BIRTHDAY) VALUES (?, ?)');
            $query->bindParam(1, $actor->getFullname(), PDO::PARAM_STR);
            $query->bindParam(2, $actor->getBirthday(), PDO::PARAM_STR);          
            $query->execute();

            $rowCount = $query->rowCount();

            return $rowCount;
        }










    }