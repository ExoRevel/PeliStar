<?php

    class imagesException extends Exception{}

    class Images{
        private $IMG_ID;
        private $IMG_TITLE;
        private $IMG_FILENAME;
        private $IMG_MIMETYPE;
        private $MOVIE_ID;

        public function __construct($IMG_ID, $IMG_TITLE,$IMG_FILENAME, $IMG_MIMETYPE, $MOVIE_ID){
            $this->setImageId($IMG_ID);
            $this->setTitle($IMG_TITLE);
            $this->setFilename($IMG_FILENAME);
            $this->setMimetype($IMG_MIMETYPE);            
            $this->setMovieid($MOVIE_ID);
            
        }

        public function getImageId(){
            return $this->IMG_ID;
        }

        public function getTitle(){
            return $this->IMG_TITLE;
        }

        public function getFilename(){
            return $this->IMG_FILENAME;
        }

        public function getMimetype(){
            return $this->IMG_MIMETYPE;
        }

        public function getMovieId(){
            return $this->MOVIE_ID;
        }

        

        public function setImageId($IMG_ID){
            if(($IMG_ID===null) || (!is_numeric($IMG_ID) || $IMG_ID<=0 || $IMG_ID > 9223372036854775807)){
                throw new imagesException('Error en IMG_ID');
            }

            $this->IMG_ID = $IMG_ID;
        }

        public function setTitle($IMG_TITLE){           
            if(($IMG_TITLE===null) || !is_string($IMG_TITLE) ){
                throw new imagesException('Error en IMG_TITLE');
            }

            $this->IMG_TITLE = $IMG_TITLE;
        }

        public function setFilename($IMG_FILENAME){           
            if($IMG_FILENAME=== null){
                throw new imagesException('Error en IMG_FILENAME');
            }

            $this->IMG_FILENAME = $IMG_FILENAME;
        }

        public function setMimetype($IMG_MIMETYPE){           
            if($IMG_MIMETYPE === null){
                throw new imagesException('Error en IMG_MIMETYPE');
            }

            $this->IMG_MIMETYPE = $IMG_MIMETYPE;
        }          

        public function setMovieid($MOVIE_ID){           
            if($MOVIE_ID=== null || (!is_numeric($MOVIE_ID) || $MOVIE_ID<=0 || $MOVIE_ID > 9223372036854775807)){
                throw new imagesException('Error en MOVIE_ID');
            }

            $this->MOVIE_ID = $MOVIE_ID;
        }  

        public function returnimagesAsArray(){
            $Images = array();
            $Images['IMG_ID'] = $this->getImageId();
            $Images['IMG_TITLE'] = $this->getTitle();
            $Images['IMG_FILENAME'] = $this->getFilename();
            $Images['IMG_MIMETYPE'] = $this->getMimetype();         
            $Images['MOVIE_ID'] = $this->getMovieId();         
            return $Images;
        }
    }