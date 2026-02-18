<?php
    include_once("functions.php");
    include_once("../utils/http_response.php");
    include_once("../utils/jwt_utils.php");

    $http_method = $_SERVER['REQUEST_METHOD'];

    $linkpdo = new PDO('mysql:dbname=chuck_api;host=127.0.0.1','root', 'claudio');

    // Get token
    $jwt = get_bearer_token();

    // Validate token : Should do it in the API.
    // $valid = is_jwt_valid($jwt, "claudio");

    // Get payload.
	$tokenParts = explode('.', $jwt);

	$payload = json_decode(json: base64_decode($tokenParts[1]));
    $role = $payload->role;


    switch($http_method) {
        case 'GET':
            print_r($role);
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