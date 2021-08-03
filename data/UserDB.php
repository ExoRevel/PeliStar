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
            $query = $this->database->prepare('SELECT * FROM USERS WHERE USE_USERNAME = ?');
            $query->bindParam(1, $USE_USERNAME, PDO::PARAM_STR);
            $query->execute();

            $userArray = array();
            
            while($row = $query->fetch(PDO::FETCH_ASSOC)){
                $user = new User($row['USE_ID'], $row['USE_FULLNAME'], $row['USE_USERNAME'], $row['USE_PASSWORD'], $row['USE_ACTIVE'], $row['USE_LOGAT']);
                if($row['USE_ROL']=='client'){
                    $user = new User($row['USE_ID'], $row['USE_FULLNAME'], $row['USE_USERNAME'], $row['USE_PASSWORD'], $row['USE_ACTIVE'], $row['USE_LOGAT']);    
                }else {
                    $user = $user->constructAdmin($row['USE_ID'], $row['USE_FULLNAME'], $row['USE_USERNAME'], $row['USE_PASSWORD'], $row['USE_ACTIVE'], $row['USE_LOGAT']);  
                }      
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
            $FULLNAME = $user->getFullname();
            $USERNAME = $user->getUsername();
            $PASSWORD = $user->getPassword();
            $ROL = $user->getRol();
            $query = $this->database->prepare('INSERT INTO USERS(USE_FULLNAME, USE_USERNAME, USE_PASSWORD, USE_ROL) VALUES (?, ?, ?,?)');
            $query->bindParam(1, $FULLNAME, PDO::PARAM_STR);
            $query->bindParam(2, $USERNAME, PDO::PARAM_STR);
            $query->bindParam(3, $PASSWORD, PDO::PARAM_STR);         
            $query->bindParam(4, $ROL, PDO::PARAM_STR);     
            $query->execute();
            $rowCount = $query->rowCount();
            return $rowCount;
        }
        public function actualizarFullNamePorId($USE_ID,$FULLNAME)
        {
            $query = $this->database->prepare('UPDATE USERS SET  USE_FULLNAME = ? WHERE USE_ID = ?');
            $query->bindParam(1, $FULLNAME, PDO::PARAM_STR);
            $query->bindParam(2, $USE_ID, PDO::PARAM_STR);
            $query ->execute();
            $rowCount = $query->rowCount();
            return $rowCount;
        }

        public function actualizarUserNamePorId($USE_ID,$USERNAME)
        {
            $query = $this->database->prepare('UPDATE USERS SET  USE_USERNAME = ? WHERE USE_ID = ?');
            $query->bindParam(1, $USERNAME, PDO::PARAM_STR);
            $query->bindParam(2, $USE_ID, PDO::PARAM_STR);
            $query ->execute();
            $rowCount = $query->rowCount();
            return $rowCount;
        }

        public function actualizarPasswordPorId($USE_ID,$PASSWORD)
        {
            $query = $this->database->prepare('UPDATE USERS SET  USE_PASSWORD = ? WHERE USE_ID = ?');
            $query->bindParam(1, $PASSWORD, PDO::PARAM_STR);
            $query->bindParam(2, $USE_ID, PDO::PARAM_STR);
            $query ->execute();
            $rowCount = $query->rowCount();
            return $rowCount;
        }

        /*public function update($user){
            $rowCount = 0;
            $FULLNAME = $user->getFullname();
            $USERNAME = $user->getUsername();
            $ID = $user->getId();
            $rowCount = $rowCount + $this->actualizarFullNamePorId($ID,$FULLNAME);
            $rowCount = $rowCount + $this->actualizarUserNamePorId($ID,$USERNAME);
            return $rowCount;
        }*/
        
    }