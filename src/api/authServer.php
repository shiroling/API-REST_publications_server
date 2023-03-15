<?php

    header("Content-Type:application/json");
    require_once('../../dependencies/jwt_utils.php');
    require_once('../dataModel/Utilisateur.php');
    require_once('../../dependencies/rest_utils.php');

    $http_method = $_SERVER['REQUEST_METHOD'];
    switch($http_method) {
        case 'GET':
            
            break;
        case 'POST':
            $data = (array) json_decode(file_get_contents('php://input'), true);
            if (empty($data) || empty($data['username']) || empty($data['password'])){
                deliver_response(400, "Arguments manquants : nom d'utilisateur, mot de passe", null);
                break;
            }
            if (isValidUser($data['username'], hash("sha256",$data['password']))) {
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