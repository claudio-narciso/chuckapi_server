<?php
    require("../utils/jwt_utils.php");
    require("../utils/http_response.php");

    $linkpdo = new PDO("mysql:host=localhost;dbname=auth", 'root', 'claudio');

    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        $data = json_decode(file_get_contents("php://input", true), associative: true);

        $login = $data['login'];
        $password = $data['password'];


        $request = "SELECT * FROM users WHERE login = :login";
        try {
            $linkpdo = new PDO("mysql:host=localhost;dbname=auth", 'root', 'claudio');
            $statement = $linkpdo->prepare($request);
            $statement->bindParam(":login", $login);
            $statement->execute();
            $user = $statement->fetch(PDO::FETCH_ASSOC);
           
        } catch(PDOException $e) {
            echo $e->getMessage();
        }

        $header = ["alg" => "HS256", "typ" => "JWT"];
        $payload = [
            "id" => $user['id'],
            "role" => $user['role'],
            "exp" => (time() + 3600)
        ];

        $token = generate_jwt($header, $payload, "claudio");

        deliver_response(200, "authentication ok", $token);
    } else {
        deliver_response(400, "Only POST requests are allowed");
    }
?>