<?php

    class imagesException extends Exception{}

    class Images{
        private $IMG_ID;
        private $IMG_TITLE;
        private $IMG_DATE;
        private $IMG_TIME;
        private $IMG_SINOPSIS;
        private $MOVIE_ID;

        public function __construct($IMG_ID, $IMG_TITLE, $IMG_DATE, $IMG_TIME, $IMG_SINOPSIS, $MOVIE_ID){
            $this->setId($IMG_ID);
            $this->setTitle($IMG_TITLE);
            $this->setDate($IMG_DATE);
            $this->setTime($IMG_TIME);            
            $this->setSinopsis($IMG_SINOPSIS);
            $this->setMovieid($MOVIE_ID);
            
        }

        public function getId(){
            return $this->IMG_ID;
        }

        public function getTitle(){
            return $this->IMG_TITLE;
        }

        public function getDate(){
            return $this->IMG_DATE;
        }

        public function getTime(){
            return $this->IMG_TIME;
        }

        public function getSinopsis(){
            return $this->IMG_SINOPSIS;
        }

        public function getMovieid(){
            return $this->MOVIE_ID;
        }

        

        public function setId($IMG_ID){
            if(($IMG_ID!==null) && (!is_numeric($IMG_ID) || $IMG_ID<=0 || $IMG_ID > 9223372036854775807)){
                throw new imagesException('Error en IMG_ID');
            }

            $this->IMG_ID = $IMG_ID;
        }

        public function setTitle($IMG_TITLE){           
            if(($IMG_TITLE!==null) && (!is_numeric($IMG_TITLE) || $IMG_TITLE<=0 || $IMG_TITLE > 9223372036854775807)){
                throw new imagesException('Error en IMG_TITLE');
            }

            $this->IMG_TITLE = $IMG_TITLE;
        }

        public function setDate($IMG_DATE){           
            if($IMG_DATE!== null && (strlen($IMG_DATE) < 0 || strlen($IMG_DATE) > 100)){
                throw new imagesException('Error en IMG_DATE');
            }

            $this->IMG_DATE = $IMG_DATE;
        }

        public function setTime($IMG_TIME){           
            if($IMG_TIME !== null &&  date_format(date_create_from_format('d/m/Y H:i', $IMG_TIME), 'd/m/Y H:i') != $IMG_TIME){
                throw new imagesException('Error en IMG_TIME');
            }

            $this->IMG_TIME = $IMG_TIME;
        }          

        public function setSinopsis($IMG_SINOPSIS){           
            if($IMG_SINOPSIS!== null && (strlen($IMG_SINOPSIS) < 0 || strlen($IMG_SINOPSIS) > 100)){
                throw new imagesException('Error en IMG_SINOPSIS');
            }

            $this->SES_REFTOK = $IMG_SINOPSIS;
        }  

        public function setMovieid($MOVIE_ID){           
            if($MOVIE_ID!== null && (strlen($MOVIE_ID) < 0 || strlen($MOVIE_ID) > 100)){
                throw new imagesException('Error en MOVIE_ID');
            }

            $this->SES_REFTOK = $MOVIE_ID;
        }  

        public function returnimagesAsArray(){
            $Images = array();
            $Images['IMG_ID'] = $this->getId();
            $Images['IMG_TITLE'] = $this->getTitle();
            $Images['IMG_DATE'] = $this->getDate();
            $Images['IMG_TIME'] = $this->getTime();         
            $Images['IMG_SINOPSIS'] = $this->getSinopsis(); 
            $Images['MOVIE_ID'] = $this->getMovieid();         

            return $Images;
        }
    }