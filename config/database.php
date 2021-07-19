<?php

    class Database{

        private static $DBConnection;
  
        public static function connectDB(){
            if(self::$DBConnection === null){ // Database::DBConnection
                self::$DBConnection = new PDO('mysql:host=127.0.0.1;dbname=api;', 'root', 'Syst3m4$');
                self::$DBConnection = new PDO('mysql:host=127.0.0.1;dbname=api_rest;', 'root', '123456');
                self::$DBConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                self::$DBConnection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            }

            return self::$DBConnection;
        }
    }