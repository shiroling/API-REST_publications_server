<?php

    header("Content-Type:application/json");
    require('../../dependencies/jwt_utils.php');
    require('../dataModel/Utilisateur.php');
    require('../../dependencies/rest_utils.php');

    $http_method = $_SERVER['REQUEST_METHOD'];
    switch($http_method) {
        case 'POST':
            $data = (array) json_decode(file_get_contents('php://input'), true);
            if (empty($data)){
                deliver_response(400, "Arguments manquants : nom d'utilisateur, mot de passe", null);
                break;
            }
            if (isValidUser($data['username'], $data['password'])) {
                $user = getUserInfo($data['username'], $data['password']);
                $headers = array('alg'=>'HS256', 'typ'=>'JWT');
                $payload = array('id'=>$user[0]['id'], 'role'=>$user[0]['role'], 'exp'=>(time() + 31536000));
                $jwt = generate_jwt($headers, $payload);
                deliver_response(200, "JWT cree", $jwt);
            } else {
                deliver_response(404, "Utilisateur introuvable", null);
            }
            break;
        default:
            deliver_response(405, "Methode non supportee", NULL);
            break;
    }

?>