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
            if(($MOVIE_TITLE!==null) && (strlen($MOVIE_TITLE) < 0 || strlen($MOVIE_TITLE) > 255)){
                throw new MoviesException('Error en el titulo');
            }

            $this->MOVIE_TITLE = $MOVIE_TITLE;
        }

        public function setDate($MOVIE_DATE){           
            if($MOVIE_DATE!==null && !is_string($MOVIE_DATE)){
                throw new MoviesException('Error en la fecha de estreno ');
            }

            $this->MOVIE_DATE = $MOVIE_DATE;
        }

        public function setTime($MOVIE_TIME){           
            if($MOVIE_TIME !== null &&(strlen($MOVIE_TIME) < 0 || strlen($MOVIE_TIME) > 45)){
                throw new MoviesException('Error en la duración');
            }

            $this->MOVIE_TIME = $MOVIE_TIME;
        }          

        public function setSinopsis($MOVIE_SINOPSIS){           
            if($MOVIE_SINOPSIS!== null && !is_string($MOVIE_SINOPSIS)){
                throw new MoviesException('Error en la sinopsis');
            }

            $this->MOVIE_SINOPSIS = $MOVIE_SINOPSIS;
        }  

        public function setCalification($MOVIE_CALIFICATION){           
            if($MOVIE_CALIFICATION!== null && (!is_numeric($MOVIE_CALIFICATION) || $MOVIE_CALIFICATION<=0 || $MOVIE_CALIFICATION > 10)){
                throw new MoviesException('Error en la calificación');
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

    






























