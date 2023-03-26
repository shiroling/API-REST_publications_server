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
                deliver_response(401, "Non autorisé : Role d'utilisateur invalide", NULL);
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
                            deliver_response(422, "Aucun utilisateur pour cet ID", null);
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
                            deliver_response(401, "Non autorisé : Vous ne pouvez les informations d'autres utilisateurs", NULL);
                        }
                    }
                    if (isset($_GET['idPost'])) {
                        $postInfo = getAllPostInfo($_GET['idPost']);
                        if (isPublisherOf($idUtilisateur, $postInfo["infos"]["id"])) {
                            deliver_response(200, "Affichage des posts de l'utilisateur", $postInfo);
                        } else {
                            deliver_response(401, "Non autorisé : Vous ne pouvez avoir les informations d'autres post que les votres", NULL);
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
                    deliver_response(422, "Impossible de supprimer le post, il n'existe pas", $postedData);
                    die();
                }
                if (isPublisherOf($idUtilisateur, $postedData['idPost'])) {
                    if (!empty($postedData['action'])) {
                        deliver_response(400, "Vous ne pouvez pas liker ou disliker vos propres posts", null);
                        die();
                    }
                    if (!empty($postedData['contenu'])) {
                        $nbLignesModifs = modifierContenuPost($postedData['idPost'], $postedData['contenu']);
                    } else {
                        deliver_response(400, "Veuillez renseigner un contenu", null);
                        break;
                    }
                    if ($nbLignesModifs == 0) {
                        deliver_response(422, "Erreur dans la modification du post, vérifiez que le contenu soit bien différent", null);
                    } else {
                        deliver_response(201, "Post modifié", $nbLignesModifs);
                    }
                } else {
                    if (!empty($postedData['contenu'])) {
                        deliver_response(400, "Vous ne pouvez pas modifier les posts des autres utilisateurs", null);
                        die();
                    }
                    if (!empty($postedData['action'])) {
                        likeOrDislikePost($idUtilisateur, $postedData['idPost'], $postedData['action']);
                    } else {
                        deliver_response(400, "Veuillez renseigner une action", null);
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
                    deliver_response(401, "Non autorisé : Vous devez être l'auteur du post pour pouvoir le supprimer", NULL);
                }
                break;
            default:
                deliver_response(405, "Methode non supportee", NULL);
                break;
        }
    }

    function likeOrDislikePost($idUtilisateur, $idPost, $action) {
        if (aDejaLike($idUtilisateur, $idPost) || aDejaDislike($idUtilisateur, $idPost)) {
            deliver_response(422, "Vous avez deja like ou dislike ce post", null);
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
                    deliver_response(400, "Veuillez soit liker le post soit le disliker", null);
            }
        }
    }

?>