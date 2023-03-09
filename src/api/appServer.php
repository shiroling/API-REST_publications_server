<?php

    require_once('../../dependencies/jwt_utils.php');
    require_once('../../dependencies/rest_utils.php');

    header("Content-Type:application/json");

    $bearer_token = '';
    $bearer_token = get_bearer_token();
    $http_method = $_SERVER['REQUEST_METHOD'];

    switch($http_method){
        case 'GET': //EVERYONE
            if (is_jwt_valid($bearer_token)) {

            } else {
                
            }
            break;
        case 'POST': //PUBLISHER
            if (is_jwt_valid($bearer_token)) {
                
            } else {
                deliver_response(503, "Utilisateur non connecte", NULL);
            }
            break;
        case 'PUT': //PUBLISHER
            if (is_jwt_valid($bearer_token)) {

            } else {
                deliver_response(503, "Utilisateur non connecte", NULL);
            }
            break;
        case 'DELETE': //MODERATOR - PUBLISHER
            if (is_jwt_valid($bearer_token)) {

            } else {
                deliver_response(503, "Utilisateur non connecte", NULL);
            }
            break;
        default:
            deliver_response(405, "Methode non supportee", NULL);
            break;
    }

?>