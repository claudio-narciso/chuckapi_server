<?php 
    require_once("connexionDB.php");

    $linkpdo = getConnexion();

    function select($linkpdo, $id=null) {
        $statement = null;
        try {
            if($id === null) {
                $statement = $linkpdo->prepare("SELECT * FROM chuck_facts");
            } else {
                $statement = $linkpdo->prepare("SELECT * FROM chuck_facts WHERE id = :id");
                $statement->bindParam(':id', $id);
            }

            $statement->execute();
            return $statement->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            throw $e;
        }
    }

    print_r(select($linkpdo, 8));

    // print_r(json_encode(select($linkpdo)));

    function creerUnePhrase($linkpdo, $phrase) {
        date_default_timezone_set('Europe/Paris');
        $datetime = date('Y-m-d H:i:s');
        try {
            $statement = $linkpdo->prepare("INSERT INTO chuck_facts 
                                            (phrase, date_ajout, date_modif) 
                                            VALUES(:phrase, :created_at, :modified_at)"
                                            );
            $statement->bindParam(':phrase', $phrase);
            $statement->bindParam(':created_at', $datetime);
            $statement->bindParam(':modified_at', $datetime);
            $statement->execute();
        } catch(PDOException $e) {
            throw $e;
        }
    }

    function misAJour($linkpdo, $data, $id) {
        $allowedFields = ["phrase" => "phrase", "vote" => "vote", "faute" => "faute", "signalement" => "signalement"];

        $update = [];
        $params = [];
        
        foreach($allowedFields as $jsonKey => $dbColumn) {
            if(isset($data[$jsonKey])) {
                $update[] = "$dbColumn = :$jsonKey";
                $params[$jsonKey] = $data[$jsonKey];
            }
        }
        $params["id"] = $id;
        $sqlQuery = "UPDATE chuck_facts SET ".implode(", ", $update)." WHERE id = :id";
        try {
            $statement = $linkpdo->prepare($sqlQuery);
            $statement->execute($params);    
        } catch(PDOException $e) {
            throw $e;
        }   
    }

    function delete($linkpdo, $id) {
        try {
            $statement = $linkpdo->prepare("DELETE FROM chuck_facts WHERE id = :id");
            $statement->bindParam(':id', $id, PDO::PARAM_INT);
            $statement->execute();
        } catch(PDOException $e) {
            throw $e;
        }

    }
?>