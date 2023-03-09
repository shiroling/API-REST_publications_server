<?php
    require('../dependencies/jwt-utils.php');
    require('../dataModel/Utilisateur.php');

    header("Content-Type:application/json");

    $http_method = $_SERVER['REQUEST_METHOD'];
    switch($http_method) {
        case 'POST':
            $data = (array) json_decode(file_get_contents('php://input'), true);
            if (isValidUser($data['username'], $data['password'])) {
                $user = getUserInfo();
                $headers = array('alg'=>'HS256', 'typ'=>'JWT');
                $payload = array('id'=>$user[0]['id'], 'role'=>$user[0]['role'], 'exp'=>(time() + 31536000));
                $jwt = generate_jwt($headers, $payload);
                deliver_response(200, "JWT cree", $jwt);
            } else {
                deliver_response(404, "Utilisateur introuvable", null);
            }
            break;
        default:
            break;
    }

?>