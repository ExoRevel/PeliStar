<?php

    require_once('../model/User.php');

    class UserDB{
        private $database;//es la conexión de la base de datos
        public function __construct($database){
            $this->database =  $database;
        }

        public function obtenerPorId($USE_ID){
            $query = $this->database->prepare('SELECT USE_ID, USE_FULLNAME, USE_USERNAME, USE_PASSWORD, USE_ACTIVE, USE_LOGAT FROM USERS WHERE USE_ID = ?');
            $query->bindParam(1, $USE_ID, PDO::PARAM_INT);
            $query->execute();

            $userArray = array();

            while($row = $query->fetch(PDO::FETCH_ASSOC)){
               $user = new User($row['USE_ID'], $row['USE_FULLNAME'], $row['USE_USERNAME'], $row['USE_PASSWORD'], $row['USE_ACTIVE'], $row['USE_LOGAT']);          

               $userArray[] = $user->returnUserAsArray();
            } 
            
            return $userArray;
        }    
        
        public function obtenerTodosUsuarios(){
            $query = $this->database->prepare('SELECT * FROM USERS');
            $query->execute();

            $userArray = [];

            while($row = $query->fetch(PDO::FETCH_ASSOC)){
               $user = new User($row['USE_ID'], $row['USE_FULLNAME'], $row['USE_USERNAME'], $row['USE_PASSWORD'], $row['USE_ACTIVE'], $row['USE_LOGAT']);          
               $userArray[] = $user->returnUserAsArray();
            } 
            
            return $userArray;
        }   


        public function obtenerPorUsername($USE_USERNAME){
            $query = $this->database->prepare('SELECT USE_ID, USE_FULLNAME, USE_USERNAME, USE_PASSWORD, USE_ACTIVE, USE_LOGAT FROM USERS WHERE USE_USERNAME = ?');
            $query->bindParam(1, $USE_USERNAME, PDO::PARAM_STR);
            $query->execute();

            $userArray = array();
            
            while($row = $query->fetch(PDO::FETCH_ASSOC)){
               $user = new User($row['USE_ID'], $row['USE_FULLNAME'], $row['USE_USERNAME'], $row['USE_PASSWORD'], $row['USE_ACTIVE'], $row['USE_LOGAT']);          

               $userArray[] = $user->returnUserAsArray();
            } 
            
            return $userArray;
        }

        public function incrementarAttemps($USE_ID){
            $query = $this->database->prepare('UPDATE USERS SET USE_LOGAT = USE_LOGAT + 1 WHERE USE_ID = ?');
            $query->bindParam(1, $USE_ID, PDO::PARAM_INT);             
            $query->execute();

            $rowCount = $query->rowCount();

            return $rowCount;
        } 
        
        public function reiniciarAttemps($USE_ID){
            $query = $this->database->prepare('UPDATE USERS SET USE_LOGAT = 0 WHERE USE_ID = ?');
            $query->bindParam(1, $USE_ID, PDO::PARAM_INT);             
            $query->execute();

            $rowCount = $query->rowCount();

            return $rowCount;
        } 

        public function insertar($user){
            $query = $this->database->prepare('INSERT INTO USERS(USE_FULLNAME, USE_USERNAME, USE_PASSWORD) VALUES (?, ?, ?)');
            $query->bindParam(1, $user->getFullname(), PDO::PARAM_STR);
            $query->bindParam(2, $user->getUsername(), PDO::PARAM_STR);
            $query->bindParam(3, $user->getPassword(), PDO::PARAM_STR);          
            $query->execute();

            $rowCount = $query->rowCount();

            return $rowCount;
        }       
    }