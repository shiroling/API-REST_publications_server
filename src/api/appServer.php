<?php

    require('../dependencies/jwt-utils.php');

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

    function deliver_response($status, $status_message, $data){
        header("HTTP/1.1 $status $status_message");
        $response['status'] = $status;
        $response['status_message'] = $status_message;
        $response['data'] = $data;
        $json_response = json_encode($response);
        echo $json_response;
    }

?>