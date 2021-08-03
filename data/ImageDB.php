<?php

    require_once('../model/Images.php');

    class ImageDB{
        private $database;

        public function __construct($database){
            $this->database =  $database;
        }

        public function buscar($MOVIE_ID, $IMG_FILENAME){
            $query = $this->database->prepare('SELECT I.IMG_ID, I.MOVIE_ID, I.IMG_TITLE, I.IMG_FILENAME, I.IMG_MIMETYPE FROM IMAGES I INNER JOIN MOVIES M ON M.MOVIE_ID = I.MOVIE_ID WHERE I.MOVIE_ID = ? AND I.IMG_FILENAME = ?');
            $query->bindParam(1, $MOVIE_ID, PDO::PARAM_INT);
            $query->bindParam(2, $IMG_FILENAME, PDO::PARAM_STR);
            $query->execute();
            $imageArray = array();

            while($row = $query->fetch(PDO::FETCH_ASSOC)){
               $image = new Images($row['IMG_ID'], $row['IMG_TITLE'], $row['IMG_FILENAME'], $row['IMG_MIMETYPE'], $row['MOVIE_ID']);          

               $imageArray[] = $image->returnImageAsArray();
            } 
            
            return $imageArray;
        }    

        public function obtenerPorId($IMG_ID, $MOVIE_ID){
            $query = $this->database->prepare('SELECT I.IMG_ID, I.MOVIE_ID, I.IMG_TITLE, I.IMG_FILENAME, I.IMG_MIMETYPE FROM IMAGES I INNER JOIN MOVIES M ON M.MOVIE_ID = I.MOVIE_ID WHERE I.IMG_ID = ? AND I.MOVIE_ID = ?');
            $query->bindParam(1, $IMG_ID, PDO::PARAM_INT);
            $query->bindParam(2, $MOVIE_ID, PDO::PARAM_INT);           
            $query->execute();

            $imageArray = array();

            while($row = $query->fetch(PDO::FETCH_ASSOC)){
                $image = new Images($row['IMG_ID'], $row['IMG_TITLE'], $row['IMG_FILENAME'], $row['IMG_MIMETYPE'], $row['MOVIE_ID']);         

               $imageArray[] = $image->returnImageAsArray();
            } 
            
            return $imageArray;
        } 
        
        public function listarPorMovieId($MOVIE_ID){
            $query = $this->database->prepare('SELECT * FROM images where MOVIE_ID = ?');
            $query->bindParam(1, $MOVIE_ID, PDO::PARAM_INT);           
            $query->execute();

            $imageArray = array();

            while($row = $query->fetch(PDO::FETCH_ASSOC)){
               $image = new Images($row['IMG_ID'], $row['IMG_TITLE'], $row['IMG_FILENAME'], $row['IMG_MIMETYPE'],$row['MOVIE_ID']);          

               $imageArray[] = $image->returnImageAsArray2();
            } 
            
            return $imageArray;
        }
        
        public function insertar($image){
            $titulo = $image->getTitle();
            $filename = $image->getFileName();
            $mimetype = $image->getMimeType();
            $movieId = $image->getMovieId();
            $query = $this->database->prepare('INSERT INTO IMAGES(IMG_TITLE, IMG_FILENAME, IMG_MIMETYPE, MOVIE_ID) VALUES (?, ?, ?, ?)');
            $query->bindParam(1, $titulo, PDO::PARAM_STR);
            $query->bindParam(2, $filename, PDO::PARAM_STR);
            $query->bindParam(3, $mimetype, PDO::PARAM_STR);
            $query->bindParam(4, $movieId, PDO::PARAM_INT);
            $query->execute();

            $rowCount = $query->rowCount();
            
            return $rowCount;
        }

        public function actualizar($image){
            $paramNumber = 1;
            $queryFields = '';

            if($image->getTitle()!==null){ //Se verifica que campo fue ingresado
                $queryFields .= 'IMG_TITLE = ?, '; 
            }

            if($image->getFileName()!==null){ //Se verifica que campo fue ingresado
                $queryFields .= 'IMG_FILENAME = ?, ';   
            }           

            $queryFields = rtrim($queryFields, ', ');      

            $query = $this->database->prepare("UPDATE IMAGES SET {$queryFields} WHERE IMG_ID = ? AND MOVIE_ID = ?");
           
            if($image->getTitle()!==null){ 
                $query->bindParam($paramNumber++, $image->getTitle(), PDO::PARAM_STR);
            }

            if($image->getFileName()!==null){ 
                $query->bindParam($paramNumber++, $image->getFileName(), PDO::PARAM_STR);
            }

            $query->bindParam($paramNumber++, $image->getId(), PDO::PARAM_INT);
            $query->bindParam($paramNumber, $image->getMovieId(), PDO::PARAM_INT);
            $query->execute();

            $rowCount = $query->rowCount();

            return $rowCount;
        }

        public function eliminarPorId($IMG_ID, $MOVIE_ID){
            $query = $this->database->prepare('DELETE FROM IMAGES  WHERE IMG_ID = ? AND MOVIE_ID = ?');
            $query->bindParam(1, $IMG_ID, PDO::PARAM_INT);
            $query->bindParam(2, $MOVIE_ID, PDO::PARAM_INT);
            $query->execute();
            
            $rowCount = $query->rowCount();

            return $rowCount;
        }

        public function eliminarPorMovieId($MOVIE_ID){
            $query = $this->database->prepare('DELETE FROM IMAGES  WHERE MOVIE_ID = ?');
            $query->bindParam(1, $MOVIE_ID, PDO::PARAM_INT);
            $query->execute();
            
            $rowCount = $query->rowCount();

            return $rowCount;
        }

        public function listarTop1PorMovieId($MOVIE_ID){
            $query = $this->database->prepare('SELECT * FROM IMAGES WHERE  MOVIE_ID=? LIMIT 1');
            $query->bindParam(1, $MOVIE_ID, PDO::PARAM_INT);           
            $query->execute();

            $imageArray = array();

            while($row = $query->fetch(PDO::FETCH_ASSOC)){
               $image = new Images($row['IMG_ID'], $row['IMG_TITLE'], $row['IMG_FILENAME'], $row['IMG_MIMETYPE'],$row['MOVIE_ID']);          

               $imageArray[] = $image->returnImageAsArray2();
            } 
            return $imageArray;
        }
    }