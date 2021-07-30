<?php

    class ActorsException extends Exception{}

    class Actors{
        private $ACTOR_ID;
        private $ACTOR_FULLNAME;
        private $ACTOR_BIRTHDAY;


        public function __construct($ACTOR_ID, $ACTOR_FULLNAME, $ACTOR_BIRTHDAY){
            $this->setId($ACTOR_ID);
            $this->setFullname($ACTOR_FULLNAME);
            $this->setBirthday($ACTOR_BIRTHDAY);

        }

        public function getId(){
            return $this->ACTOR_ID;
        }

        public function getFullname(){
            return $this->ACTOR_FULLNAME;
        }

        public function getBirthday(){
            return $this->ACTOR_BIRTHDAY;
        }

        public function setId($ACTOR_ID){
            if(($ACTOR_ID!==null) && (!is_numeric($ACTOR_ID) || $ACTOR_ID<=0 || $ACTOR_ID > 9223372036854775807)){
                throw new ActorsException('Error en el ID del Actor');
            }

            $this->ACTOR_ID = $ACTOR_ID;
        }

        public function setFullname($ACTOR_FULLNAME){           
            if(($ACTOR_FULLNAME!==null) && (strlen($ACTOR_FULLNAME) < 0 || strlen($ACTOR_FULLNAME) > 255)){
                throw new ActorsException('Error en nombre del Actor');
            }

            $this->ACTOR_FULLNAME = $ACTOR_FULLNAME;
        }

        public function setBirthday($ACTOR_BIRTHDAY){           
            if($ACTOR_BIRTHDAY !== null && !is_string($ACTOR_BIRTHDAY)){
                throw new ActorsException('Error en la fecha de nacimiento de Actor');
            }

            $this->ACTOR_BIRTHDAY = $ACTOR_BIRTHDAY;
        }


        public function returnActorsAsArray(){
            $Actors = array();
            $Actors['ACTOR_ID'] = $this->getId();
            $Actors['ACTOR_FULLNAME'] = $this->getFullname();
            $Actors['ACTOR_BIRTHDAY'] = $this->getBirthday();         

            return $Actors;
        }
    }