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
                deliver_response(405, "Methode non supportee en mode anonyme", NULL);
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
                        $result = getAllUserInfo($_GET['idUser']);
                        deliver_response(200, "Affichage des infos de l'utilisateur", $result);
                    } else {
                        deliver_response(422, " Unprocessable Content: mauvais argument", NULL);
                        exit();
                    }
                }
                deliver_response(200, "Affichage des posts (en mode moderateur)", $result);
                break;
            case 'DELETE':
                $postedData = file_get_contents('php://input');
                if (!existePost($postedData['idPost'])) {
                    deliver_response(422, "Impossible de supprimer le post, il n'existe pas", NULL);
                }
                supprimerPost($postedData['idPost'], $postedData['contenu']);
                deliver_response(201, "Post supprimé", null);
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
                        if ($_GET['idUser'] == $idUtilisateur) {
                            $userInfos = getAllUserInfo($_GET['idUser']);
                            deliver_response(200, "Affichage des posts de l'utilisateur", $userInfos);
                        } else {
                            deliver_response(401, "Unauthorized : Vous ne pouvez les informations d'autres utilisateurs", NULL);
                        }
                    }
                }
                break;
            case 'POST':
                $postedData = file_get_contents('php://input');
                creerNouveauPost($idUtilisateur, json_decode($postedData, true)['contenu']);
                deliver_response(201, "Post cree", null);
                break;
            case 'PUT':
                $postedData = json_decode(file_get_contents('php://input'), true);
                if (!existePost($postedData['idPost'])) {
                    deliver_response(422, "Impossible de supprimer le post, il n'existe pas", NULL);
                }
                if (isPubliserOf($idUtilisateur, $postedData['idPost'])) {
                    modifierContenuPost($postedData['idPost'], $postedData['contenu']);
                    deliver_response(201, "Post modifié", null);
                } else {
                    deliver_response(401, "Unauthorized : Vous devez être l'auteur du post pour pouvoir le modifier", NULL);
                }
                break;
            case 'DELETE':
                $postedData = json_decode(file_get_contents('php://input'), true);
                if (!existePost($postedData['idPost'])) {
                    deliver_response(422, "Impossible de supprimer le post, il n'existe pas", NULL);
                }
                if (isPubliserOf($idUtilisateur, $postedData['idPost'])) {
                    supprimerPost($postedData['idPost'], $postedData['contenu']);
                    deliver_response(201, "Post supprimé", null);
                } else {
                    deliver_response(401, "Unauthorized : Vous devez être l'auteur du post pour pouvoir le supprimer", NULL);
                }
                break;
            default:
                deliver_response(405, "Methode non supportee", NULL);
                break;
        }
    }





?>