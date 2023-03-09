<?php

    require_once('../../dependencies/jwt_utils.php');
    require_once('../../dependencies/rest_utils.php');
    require_once('../dataModel/Post.php');

    header("Content-Type:application/json");

    $bearer_token = '';
    $bearer_token = get_bearer_token();
    $http_method = $_SERVER['REQUEST_METHOD'];

    if ($bearer_token == null) {
        procedureClientAnonyme($http_method);
    } else {
        procedureClientModerator($http_method, $bearer_token);
        procedureClientPublisher($http_method, $bearer_token);
    }

    function procedureClientAnonyme($http_method) {
        switch($http_method){
            case 'GET':
                $posts = getAllPosts();
                deliver_response(200, "Affichage des posts (en mode anonyme)", $posts);
                break;
            default:
                deliver_response(405, "Methode non supportee en anonyme", NULL);
                break;
        }
    }

    function procedureClientModerator($http_method, $bearer_token) {
        switch($http_method){
            case 'GET':
                if (is_jwt_valid($bearer_token)) {
                    
                } else {
                    deliver_response(503, "Jeton invalide", NULL);
                }
                break;
            case 'DELETE':
                if (is_jwt_valid($bearer_token)) {
    
                } else {
                    deliver_response(503, "Jeton invalide", NULL);
                }
                break;
            default:
                deliver_response(405, "Methode non supportee en moderator", NULL);
                break;
        }
    }

    function procedureClientPublisher($http_method, $bearer_token) {
        switch($http_method){
            case 'GET':
                if (is_jwt_valid($bearer_token)) {
                    
                } else {
                    deliver_response(503, "Jeton invalide", NULL);
                }
                break;
            case 'POST':
                if (is_jwt_valid($bearer_token)) {
                    
                } else {
                    deliver_response(503, "Jeton invalide", NULL);
                }
                break;
            case 'PUT':
                if (is_jwt_valid($bearer_token)) {
    
                } else {
                    deliver_response(503, "Jeton invalide", NULL);
                }
                break;
            case 'DELETE':
                if (is_jwt_valid($bearer_token)) {
    
                } else {
                    deliver_response(503, "Jeton invalide", NULL);
                }
                break;
            default:
                deliver_response(405, "Methode non supportee", NULL);
                break;
        }
    }

?>