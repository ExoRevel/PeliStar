<?php

    class MoviesException extends Exception{}

    class Movies{
        private $MOVIE_ID;
        private $MOVIE_TITLE;
        private $MOVIE_DATE;
        private $MOVIE_TIME;
        private $MOVIE_SINOPSIS;
        private $MOVIE_CALIFICATION;

        public function __construct($MOVIE_ID, $MOVIE_TITLE, $MOVIE_DATE, $MOVIE_TIME, $MOVIE_SINOPSIS, $MOVIE_CALIFICATION){
            $this->setId($MOVIE_ID);
            $this->setTitle($MOVIE_TITLE);
            $this->setDate($MOVIE_DATE);
            $this->setTime($MOVIE_TIME);            
            $this->setSinopsis($MOVIE_SINOPSIS);
            $this->setCalification($MOVIE_CALIFICATION);
        }

        public function getId(){
            return $this->MOVIE_ID;
        }

        public function getTitle(){
            return $this->MOVIE_TITLE;
        }

        public function getDate(){
            return $this->MOVIE_DATE;
        }

        public function getTime(){
            return $this->MOVIE_TIME;
        }

        public function getSinopsis(){
            return $this->MOVIE_SINOPSIS;
        }

        public function getCalification(){
            return $this->MOVIE_CALIFICATION;
        }

        public function setId($MOVIE_ID){
            if(($MOVIE_ID!==null) && (!is_numeric($MOVIE_ID) || $MOVIE_ID<=0 || $MOVIE_ID > 9223372036854775807)){
                throw new MoviesException('Error en MOVIE_ID');
            }

            $this->MOVIE_ID = $MOVIE_ID;
        }

        public function setTitle($MOVIE_TITLE){           
            if(($MOVIE_TITLE!==null) && (!is_numeric($MOVIE_TITLE) || $MOVIE_TITLE<=0 || $MOVIE_TITLE > 9223372036854775807)){
                throw new MoviesException('Error en MOVIE_TITLE');
            }

            $this->MOVIE_TITLE = $MOVIE_TITLE;
        }

        public function setDate($MOVIE_DATE){           
            if($MOVIE_DATE!== null && (strlen($MOVIE_DATE) < 0 || strlen($MOVIE_DATE) > 100)){
                throw new MoviesException('Error en MOVIE_DATE');
            }

            $this->MOVIE_DATE = $MOVIE_DATE;
        }

        public function setTime($MOVIE_TIME){           
            if($MOVIE_TIME !== null &&  date_format(date_create_from_format('d/m/Y H:i', $MOVIE_TIME), 'd/m/Y H:i') != $MOVIE_TIME){
                throw new MoviesException('Error en MOVIE_TIME');
            }

            $this->MOVIE_TIME = $MOVIE_TIME;
        }          

        public function setSinopsis($MOVIE_SINOPSIS){           
            if($MOVIE_SINOPSIS!== null && (strlen($MOVIE_SINOPSIS) < 0 || strlen($MOVIE_SINOPSIS) > 100)){
                throw new MoviesException('Error en MOVIE_SINOPSIS');
            }

            $this->SES_REFTOK = $MOVIE_SINOPSIS;
        }  

        public function setCalification($MOVIE_CALIFICATION){           
            if($MOVIE_CALIFICATION!== null && (strlen($MOVIE_CALIFICATION) < 0 || strlen($MOVIE_CALIFICATION) > 100)){
                throw new MoviesException('Error en MOVIE_CALIFICATION');
            }

            $this->MOVIE_CALIFICATION = $MOVIE_CALIFICATION;
        } 

        public function returnMoviesAsArray(){ 
            $Movies = array();
            $Movies['MOVIE_ID'] = $this->getId();
            $Movies['MOVIE_TITLE'] = $this->getTitle();
            $Movies['MOVIE_DATE'] = $this->getDate();
            $Movies['MOVIE_TIME'] = $this->getTime();         
            $Movies['MOVIE_SINOPSIS'] = $this->getSinopsis();          
            $Movies['MOVIE_CALIFICATION'] = $this->getCalification();
            return $Movies;
        }
    }

    






























