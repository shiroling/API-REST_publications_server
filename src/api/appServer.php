<?php
    header("Content-Type:application/json");
    require_once('../../dependencies/jwt_utils.php');
    require_once('../../dependencies/rest_utils.php');
    require_once('../dataModel/Post.php');
    require_once('../dataModel/Utilisateur.php');

    $bearer_token = '';
    $bearer_token = get_bearer_token();
    $http_method = $_SERVER['REQUEST_METHOD'];

    if ($bearer_token == null) {
        procedureClientAnonyme($http_method);
    } else {
        if (!is_jwt_valid($bearer_token)) {
            deliver_response(401, "Jeton invalide", NULL);
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
                if (empty($_GET)) {
                    $result = getAllPostsModerator();
                    deliver_response(200, "Affichage des posts (mode moderateur)", $result);
                } else {
                    if (isset($_GET['idUser'])) {
                        $result = getAllUserInfo($_GET['idUser']);
                        if ($result == false) {
                            deliver_response(204, "Aucun utilisateur pour cet ID", null);
                        }else {
                            deliver_response(200, "Affichage des infos de l'utilisateur", $result);
                        }
                    } else {
                        deliver_response(422, " Unprocessable Content: mauvais argument", NULL);
                        exit();
                    }
                }
                break;
            case 'DELETE':
                $postedData = json_decode(file_get_contents('php://input'), true);
                if (!existePost($postedData['idPost'])) {
                    deliver_response(422, "Impossible de supprimer le post, il n'existe pas", NULL);
                } else {
                    supprimerPost($postedData['idPost']);
                    deliver_response(201, "Post supprimé", null);
                }
                break;
            default:
                deliver_response(405, "Methode non supportee en moderator", NULL);
                break;
        }
    }

    function procedureClientPublisher($http_method, $idUtilisateur) {
        switch($http_method){
            case 'GET':
                if (empty($_GET)){
                    $posts = getAllPostsPublisher();
                    deliver_response(200, "Affichage des posts (en mode publisher)", $posts);
                } else {
                    if (isset($_GET['idUser'])) {
                        if ($_GET['idUser'] == $idUtilisateur) {
                            $userInfos = getAllUserInfo($_GET['idUser']);
                            deliver_response(200, "Affichage des posts de l'utilisateur", $userInfos);
                        } else {
                            deliver_response(403, "Forbidden : Vous ne pouvez accéder aux informations d'autres utilisateurs", NULL);
                        }
                        die();
                    }
                    if (isset($_GET['idPost'])) {
                        $postInfo = getAllPostInfo($_GET['idPost']);
                        if (isPublisherOf($idUtilisateur, $postInfo["infos"]["id"])) {
                            deliver_response(200, "Affichage des posts de l'utilisateur", $postInfo);
                        } else {
                            deliver_response(403, "Forbidden : Vous ne pouvez accéder aux informations d'autres post que les votres", NULL);
                        }
                        die();
                    }
                    deliver_response(422, "Argument invalide", null);
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
                    deliver_response(422, "Impossible de supprimer le post, il n'existe pas", $postedData);
                    die();
                }
                if (isPublisherOf($idUtilisateur, $postedData['idPost'])) {
                    if (!empty($postedData['action'])) {
                        deliver_response(403, "Vous ne pouvez pas liker ou disliker vos propres posts", null);
                        die();
                    }
                    if (!empty($postedData['contenu'])) {
                        $nbLignesModifs = modifierContenuPost($postedData['idPost'], $postedData['contenu']);
                    } else {
                        deliver_response(422, "Veuillez renseigner un contenu", null); //code a verifier
                        break;
                    }
                    if ($nbLignesModifs == 0) {
                        deliver_response(422, "Erreur dans la modification du post, vérifiez que le contenu soit bien différent", null); //code d'erreur à vérifier
                    } else {
                        deliver_response(201, "Post modifié", $nbLignesModifs);
                    }
                } else {
                    if (!empty($postedData['contenu'])) {
                        deliver_response(403, "Vous ne pouvez pas modifier les posts des autres utilisateurs", null);
                        die();
                    }
                    if (!empty($postedData['action'])) {
                        likeOrDislikePost($idUtilisateur, $postedData['idPost'], $postedData['action']);
                    } else {
                        deliver_response(422, "Veuillez renseigner une action", null); //code a verifier
                    }
                }
                break;
            case 'DELETE':
                $postedData = json_decode(file_get_contents('php://input'), true);
                if (!existePost($postedData['idPost'])) {
                    deliver_response(422, "Impossible de supprimer le post, il n'existe pas", NULL);
                } elseif (isPublisherOf($idUtilisateur, $postedData['idPost'])) {
                    supprimerPost($postedData['idPost']);
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

    function likeOrDislikePost($idUtilisateur, $idPost, $action) {
        if (aDejaLike($idUtilisateur, $idPost) || aDejaDislike($idUtilisateur, $idPost)) {
            deliver_response(403, "Vous avez deja like ou dislike ce post", null);  //code d'erreur a verifier
        } else {
            switch($action) {
                case 'like':
                    likerPost($idUtilisateur, $idPost);
                    deliver_response(200, "Post liké", null);
                    break;
                case 'dislike':
                    dislikerPost($idUtilisateur, $idPost);
                    deliver_response(200, "Post disliké", null);
                    break;
                default:
                    deliver_response(422, "Veuillez soit liker le post soit le disliker", null); //code d'erreur a verifier
            }
        }
    }

?>