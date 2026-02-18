<?php 
/*     class connexioDB {
        
        private static $connexion = null;

        private function __construct() {
            self::$connexion = new PDO("mysql:host=localhost;dbname=chuck_api", "root", "claudio");
        }

        public static function getInstance() {
            if(self::$connexion === null) {
                self::$connexion = new self();
            }
            return self::$connexion;
        }
    } */

    function getConnexion() {
        return new PDO("mysql:host=localhost;dbname=chuck_api", "root", "claudio");
    }
?>