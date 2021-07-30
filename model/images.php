<?php

    class ImageException extends Exception{}

    class Images{
        private $IMG_ID;
        private $IMG_TITLE;
        private $IMG_FILENAME;
        private $IMG_MIMETYPE;
        private $MOVIE_ID;
        private $IMG_FOLDER_LOCATION;

        public function __construct($IMG_ID, $IMG_TITLE,$IMG_FILENAME, $IMG_MIMETYPE, $MOVIE_ID){
            $this->setId($IMG_ID);
            $this->setTitle($IMG_TITLE);
            $this->setFilename($IMG_FILENAME);
            $this->setMimetype($IMG_MIMETYPE);            
            $this->setMovieid($MOVIE_ID);
            $this->IMG_FOLDER_LOCATION = '../images/movies/';
            
        }
        public function getId(){
            return $this->IMG_ID;
        }

        public function getTitle(){
            return $this->IMG_TITLE;
        }

        public function getFileName(){
            return $this->IMG_FILENAME;
        }

        public function getFileExtension(){
            $fileNameParts = explode('.', $this->IMG_FILENAME);
            $lastArrayElement = count($fileNameParts) - 1;
            $fileExtension = $fileNameParts[$lastArrayElement];
            return $fileExtension;
        }

        public function getMimeType(){
            return $this->IMG_MIMETYPE;
        }

        public function getMovieId(){
            return $this->MOVIE_ID;
        }

        public function getFolderLocation(){
            return $this->IMG_FOLDER_LOCATION;
        }

        public function getURL(){//URL desde el cual se accederá a la imagen (REST)
            $httpOrHttps = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http');
            $host = $_SERVER['HTTP_HOST'];

            $url = '/pelistar/movies/'.$this->getMovieId().'/images/'.$this->getId();

            return $httpOrHttps.'://'.$host.$url;
        }

        public function getImageFile(){
            $filePath = $this->getFolderLocation().$this->getMovieId().'/'.$this->getFileName();

            if(!file_exists($filePath)){
                throw new ImageException('Archivo de imagen no encontrado');
            }

            header('Content-Type:'.$this->getMimeType());
            header('Content-Disposition: inline; filename="'.$this->getFileName().'"');

            if(!readfile($filePath)){
                http_response_code(404);
            }

            exit;
        }

        public function setMovieId($MOVIE_ID){
            if(($MOVIE_ID!==null) && (!is_numeric($MOVIE_ID) || $MOVIE_ID<=0 || $MOVIE_ID > 9223372036854775807)){
                throw new ImageException('Error en MOVIE_ID');
            }

            $this->MOVIE_ID = $MOVIE_ID;
        }

        public function setId($IMG_ID){
            if(($IMG_ID!==null) && (!is_numeric($IMG_ID) || $IMG_ID<=0 || $IMG_ID > 9223372036854775807)){
                throw new ImageException('Error en IMG_ID');
            }

            $this->IMG_ID = $IMG_ID;
        }

        public function setTitle($IMG_TITLE){           
            if($IMG_TITLE!== null && (strlen($IMG_TITLE) < 0 || strlen($IMG_TITLE) > 255)){
                throw new ImageException('Error en IMG_TITLE');
            }

            $this->IMG_TITLE = $IMG_TITLE;
        }

        public function setFileName($IMG_FILENAME){       
            if($IMG_FILENAME!== null && (strlen($IMG_FILENAME) < 0 || strlen($IMG_FILENAME) > 255 || preg_match("/^[Aa-zA-Z0-9_-]+(.jpg|.gif|.png)$/", $IMG_FILENAME)!=1)){
                throw new ImageException('Error en IMG_FILENAME');
            }

            $this->IMG_FILENAME = $IMG_FILENAME;
        }

        public function setMimeType($IMG_MIMETYPE){           
            if($IMG_MIMETYPE!== null && (strlen($IMG_MIMETYPE) < 0 || strlen($IMG_MIMETYPE) > 255)){
                throw new ImageException('Error en IMG_MIMETYPE');
            }

            $this->IMG_MIMETYPE = $IMG_MIMETYPE;
        }

        public function saveImageFile($tempFileName){
            $uploadedFilePath = $this->getFolderLocation().$this->getMovieId().'/'.$this->getFileName(); //Ruta de la imagen

            if(!is_dir($this->getFolderLocation().$this->getMovieId())){
                if(!mkdir($this->getFolderLocation().$this->getMovieId())){ //Si hubo error al crear el directorio
                    throw new ImageException('Hubo un error al crear el directorio para la tarea');
                }
            }

            if(!file_exists($tempFileName)){ //Archivo temporal no existe
                throw new ImageException('Hubo un error al intentar subir la imagen');
            }

            if(!move_uploaded_file($tempFileName, $uploadedFilePath)){ //No se pudo mover a la ruta de la imagen
                throw new ImageException('Hubo un error al intentar subir la imagen');
            }
        }

        public function renameImageFile($oldFileName, $newFileName){
            $originalFilePath = $this->getFolderLocation().$this->getMovieId().'/'.$oldFileName;
            $newFilePath = $this->getFolderLocation().$this->getMovieId().'/'.$newFileName;

            if(!file_exists($originalFilePath)){
                throw new ImageException('No se puede encontrar imagen a renombrar');
            }

            if(!rename($originalFilePath, $newFilePath)){
                throw new ImageException('No se puede actualizar el nombre de la imagen');
            }
        }

        public function deleteImageFile(){
            $filePath = $this->getFolderLocation().$this->getMovieId().'/'.$this->getFileName();

            if(file_exists($filePath)){
                if(!unlink($filePath)){
                    throw new ImageException('No se pudo eliminar el archivo');
                }
            }            
        }

        public function returnImageAsArray(){
            $image = array();
            $image['IMG_ID'] = $this->getId();
            $image['IMG_TITLE'] = $this->getTitle();
            $image['IMG_FILENAME'] = $this->getFileName();
            $image['IMG_FILEEXTENSION'] = $this->getFileExtension();
            $image['IMG_MIMETYPE'] = $this->getMimeType();
            $image['MOVIE_ID'] = $this->getMovieId();
            $image['IMG_FOLDER_LOCATION'] = $this->getFolderLocation();
            $image['IMG_URL'] = $this->getURL();

            return $image;
        }
    }