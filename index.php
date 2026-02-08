<?php
    include_once("functions.php");

    /// Envoi de la réponse au Client 
    // TODO: Refactorer et ajouter gérer tous cas d'erreur.
    function deliver_response($status_code, $status_message, $data=null){ 
            /// Paramétrage de l'entête HTTP 
            http_response_code($status_code); //Utilise un message standardisé en fonction du code HTTP 
            //header("HTTP/1.1 $status_code $status_message"); //Permet de personnaliser le message associé au code HTTP 
            header("Content-Type:application/json; charset=utf-8");//Indique au client le format de la réponse            
            header("Access-Control-Allow-Origin: *");
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
                $result = readChuckFacts($linkpdo, $id);

            } else {
                $result = readChuckFacts($linkpdo);
            }
            deliver_response(200, "Données récupérées avec succès", $result);
            break;
        case 'POST':
            $postedData = file_get_contents('php://input'); 
            $dataInput = json_decode($postedData,true);
            $id = createChuckFact($linkpdo, $dataInput);
            $dataOutput = readChuckFacts($linkpdo, $id);

            deliver_response(201, "Données crées avec succès.", $dataOutput);
            break;
        case 'PATCH':
            if(isset($_GET['id'])) 
            { 
                $id=htmlspecialchars($_GET['id']);

                $postedData = file_get_contents('php://input'); 
                $data = json_decode($postedData,true);

                updateChuckFact($linkpdo, $data, $id);
                deliver_response(200, "Phrase d'id $id a été mis à jours");
            } else {
                deliver_response(400, "Vous devais specifée l'id de la donnée a mettre à jours.");
            }
        case 'DELETE':
            if(isset($_GET['id'])) {
                $id=htmlspecialchars($_GET['id']);
                $deletedLignes = deleteChuckFact($linkpdo, $id);
                if($deletedLignes === 0) {
                    deliver_response(404, "Aucune ligne supprimée.");
                } else {
                    deliver_response(200, "Donnée d'id $id supprimée avec succées");
                }
            } else {
                deliver_response(400, "Vous devez specifiée un id pour effectuer le delete");
            }
        case 'OPTIONS':
            header('Access-Control-Allow-Methods: *');
            header(header: 'Access-Control-Allow-Headers: *');
            deliver_response("204", "Permission donnée");
    }

?>