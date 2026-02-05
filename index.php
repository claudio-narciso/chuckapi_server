<?php
    include_once("functions.php");

    /// Envoi de la réponse au Client 
    function deliver_response($status_code, $status_message, $data=null){ 
            /// Paramétrage de l'entête HTTP 
            http_response_code($status_code); //Utilise un message standardisé en fonction du code HTTP 
            //header("HTTP/1.1 $status_code $status_message"); //Permet de personnaliser le message associé au code HTTP 
            header("Content-Type:application/json; charset=utf-8");//Indique au client le format de la réponse            
            $response['status_code'] = $status_code; 
            $response['status_message'] = $status_message; 
            $response['data'] = $data; 
            /// Mapping de la réponse au format JSON 
            $json_response = json_encode($response); 
            if($json_response===false) {
                die('json encode ERROR : '.json_last_error_msg()); 
            } 
            /// Affichage de la réponse (Retourné au client) 
            echo $json_response; 
    } 

    $http_method = $_SERVER['REQUEST_METHOD'];

    $linkpdo = new PDO('mysql:dbname=chuck_api;host=127.0.0.1','root', 'claudio');

    switch($http_method) {
        case 'GET':
            //Récupération des données dans l’URL
            if(isset($_GET['id'])) 
            { 
                $id=htmlspecialchars($_GET['id']);
                $result = select($linkpdo, $id);

            } else {
                $result = select($linkpdo);
            }
            deliver_response(200, "Données récupérées avec succès", $result);
            break;
        case 'POST':

            $postedData = file_get_contents('php://input'); 
            $data = json_decode($postedData,true);

            creerUnePhrase($linkpdo, $data);
            deliver_response(200, "Phrase d'id $id a été mis à jours");
            break;
        case 'PUT':
            if(!isset($_PUT['id'])) 
                { 
                    $id=htmlspecialchars($_GET['id']);

                    $postedData = file_get_contents('php://input'); 
                    $data = json_decode($postedData,true);

                    misAJour($linkpdo, $data, $id);
                    deliver_response(200, "Phrase d'id $id a été mis à jours");
            } 

    }

?>