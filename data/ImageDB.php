<?php

    require_once('../model/Images.php');

    class ImageDB{
        private $database;
        public function __construct($database){
            $this->database = $database;
        }

        public function obtenerPorId($IMG_ID){
            $query = $this->database->prepare('SELECT IMG_ID, IMG_TITLE, IMG_FILENAME, IMG_MIMETYPE, MOVIE_ID FROM IMAGES WHERE IMG_ID = ?');
            $query ->bindParam(1, $IMG_ID, PDO:: PARAM_STR);
            $query ->execute();

            $imageArray = array();

            while ($row = $query->fetch(PDO::FETCH_ASSOC)) {
                $image= new Images($row['IMG_ID'], $row['IMG_TITLE'], $row['IMG_FILENAME'], $row['IMG_MIMETYPE'], $row['MOVIE_ID']);

                $imageArray[] = $image->returnimagesAsArray();
            }

            return $imageArray;

        }

        public function obtenerTodosImages(){
            $query = $this->database->prepare('SELECT * FROM IMAGES');
            $query->execute();

            $imageArray = [];

            while($row = $query->fetch(PDO::FETCH_ASSOC)){
                $image = new Images($row['IMG_ID'], $row['IMG_TITLE'], $row['IMG_FILENAME'], $row['IMG_MIMETYPE'], $row['MOVIE_ID']);          
                $imageArray[] = $image->returnimagesAsArray();
             } 
            
            return $imageArray;
        } 

        public function obtenerPorTitle($IMG_TITLE){
            $query = $this->database->prepare('SELECT IMG_ID, IMG_TITLE, IMG_FILENAME, IMG_MIMETYPE, MOVIE_ID  FROM IMAGES WHERE IMG_TITLE = ?');
            $query->bindParam(1, $IMG_TITLE, PDO::PARAM_STR);
            $query->execute();

            $imageArray = array();
            
            while($row = $query->fetch(PDO::FETCH_ASSOC)){
               $image = new Images($row['IMG_ID'], $row['IMG_TITLE'], $row['IMG_FILENAME'], $row['IMG_MIMETYPE'], $row['MOVIE_ID']);          

               $imageArray[] = $image->returnimagesAsArray();
            } 
            
            return $imageArray;
        }

        public function insertar($image){
            $query = $this->database->prepare('INSERT INTO IMAGES(IMG_ID,IMG_TITLE, IMG_FILENAME, IMG_MIMETYPE, MOVIE_ID) VALUES (?, ?, ?, ?)');
            $query->bindParam(1, $image->getFullname(), PDO::PARAM_STR);
            $query->bindParam(2, $image->getBirthday(), PDO::PARAM_STR);          
            $query->execute();

            $rowCount = $query->rowCount();

            return $rowCount;
        }

    }