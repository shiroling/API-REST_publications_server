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
        if (!is_jwt_valid($bearer_token)) {
            deliver_response(503, "Jeton invalide", NULL);
        } else {
            $payload = getPayload($bearer_token);
            switch ($payload['role']) {
            case 'publisher':
                procedureClientPublisher($http_method, $payload['id']);
                break;
            case 'moderator':
                procedureClientModerator($http_method, $payload['id']);
                break;
            default:
                deliver_response(401, "Unauthorized : Role d'utilisateur invalide", NULL);
                break;
            }
        }
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

    function procedureClientModerator($http_method, $idUtilisateur) {
        switch($http_method){
            case 'GET':
                if (!isset($_GET)) {
                    $result = getAllPostsModerator();
                } else {
                    if (isset($_GET['idPost'])) {
                        deliver_response(500, " Unimplemented method", NULL);
                        //$result = getAllPostInfo($_GET['idPost']);
                    } else
                    if (isset($_GET['idUser'])) {
                        deliver_response(500, " Unimplemented method", NULL);
                        //$result = getAllUserInfo();
                    } else {
                        deliver_response(422, " Unprocessable Content: mauvais argument", NULL);
                        exit();
                    }
                }
                deliver_response(200, "Affichage des posts (en mode moderateur)", $result);
                break;
            case 'DELETE':
                
                break;
            default:
                deliver_response(405, "Methode non supportee en moderator", NULL);
                break;
        }
    }

    function procedureClientPublisher($http_method, $idUtilisateur) {
        switch($http_method){
            case 'GET':
                if (!isset($_GET)){
                    $posts = getAllPostPublisher();
                    deliver_response(200, "Affichage des posts (en mode publisher)", $posts);
                } else {
                    if (isset($_GET['idUser'])) {
                        $posts = getPostFromUser($idUtilisateur);
                        deliver_response(200, "Affichage des posts de l'utilisateur", $posts);
                    }
                }
                break;
            case 'POST':
                $postedData = file_get_contents('php://input');
                creerNouveauPost($idUtilisateur, json_decode($postedData, true)['contenu']);
                deliver_response(201, "Post cree", null);
                break;
            case 'PUT':
                
                break;
            case 'DELETE':
                
                break;
            default:
                deliver_response(405, "Methode non supportee", NULL);
                break;
        }
    }

?>