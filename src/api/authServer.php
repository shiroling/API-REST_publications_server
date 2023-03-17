<?php
    header("Content-Type:application/json");
    require_once('../../dependencies/jwt_utils.php');
    require_once('../dataModel/Utilisateur.php');
    require_once('../../dependencies/rest_utils.php');

    $http_method = $_SERVER['REQUEST_METHOD'];
    switch($http_method) {
        case 'POST':
            $data = (array) json_decode(file_get_contents('php://input'), true);
            if (empty($data)){
                deliver_response(400, "Arguments manquants : data = nom d'utilisateur, mot de passe", null);
                die;
            }
            if (empty($data['username'])) {
                deliver_response(400, "Arguments manquants : nom d'utilisateur", null);
                die;
            }
            if (empty($data['password'])) {
                deliver_response(400, "Arguments manquants : mot de passe", null);
                die;
            }
            if (isValidUser($data['username'], hash("sha256",$data['password']))) {
                $user = getUserInfo($data['username'], hash("sha256",$data['password']));
                $headers = array('alg'=>'HS256', 'typ'=>'JWT');
                $payload = array('id'=>$user[0]['id'], 'role'=>$user[0]['role'], 'exp'=>(time() + 31536000));
                $jwt = generate_jwt($headers, $payload);
                deliver_response(200, "JWT cree", $jwt);
            } else {
                deliver_response(404, "Utilisateur introuvable", null);
            }
            die;
        default:
            deliver_response(405, "Methode non supportee : Seul Post permet d'obtenir un token", NULL);
            die;
    }

?>