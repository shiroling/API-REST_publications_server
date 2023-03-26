<?php
require_once("conx.php");

function getAllPosts() {
    try {
        $pdo = getPDOConnection();
        $req = $pdo->prepare("Select u.Nom nom, p.date_publication date, p.contenu contenu from r_Post p, r_Utilisateur u Where u.Id_Utilisateur = p.Id_Utilisateur");   
        $req->execute();
        $data = $req->fetchAll(PDO::FETCH_ASSOC);
        return $data;
    } catch (Exception $e) {;
        deliver_response(503, "Erreur avec le serveur de base de donnees", $e);
    }
}

function getAllPostsPublisher() {
    try {
        $pdo = getPDOConnection();
        $st = $pdo->prepare('SELECT r_Post.Id_Post id, r_Utilisateur.Nom publisher, r_Post.Contenu contenu, count(DISTINCT r_Liker.Id_Utilisateur) nb_likes, COUNT(DISTINCT r_Disliker.Id_Utilisateur) AS nb_dislikes, r_Post.date_publication date
        FROM r_Post NATURAL JOIN r_Utilisateur
        LEFT JOIN r_Liker ON r_Post.Id_Post = r_Liker.Id_Post
        LEFT JOIN r_Disliker ON r_Post.Id_Post = r_Disliker.Id_Post
        GROUP BY r_Post.Id_Post, r_Post.Contenu');
        $st->execute();
        return $st->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        deliver_response(503, "Erreur avec le serveur de base de donnees", $e);
    }
 }

function creerNouveauPost($idUtilisateur, $contenuMessage) {
    try {
        $pdo = getPDOConnection();
        $st = $pdo->prepare("INSERT INTO r_Post (Contenu, date_publication, Id_Utilisateur) VALUES (?, current_timestamp(), ?)");
        $st->execute(array($contenuMessage, $idUtilisateur));
    } catch (Exception $e) {
        deliver_response(503, "Erreur avec le serveur de base de donnees", $e);
    }
}

function likerPost($idUtilisateur, $idPost) {
    try {
        $pdo = getPDOConnection();
        $st = $pdo->prepare("INSERT INTO r_Liker (Id_Utilisateur, Id_Post) VALUES (?, ?) ");
        $st->execute(array($idUtilisateur, $idPost));
    } catch (Exception $e) {
        deliver_response(503, "Erreur avec le serveur de base de donnees", $e);
    }
}

function dislikerPost($idUtilisateur, $idPost) {
    try {
        $pdo = getPDOConnection();
        $st = $pdo->prepare("INSERT INTO r_Disliker (Id_Utilisateur, Id_Post) VALUES (?, ?) ");
        $st->execute(array($idUtilisateur, $idPost));
    } catch (Exception $e) {
        deliver_response(503, "Erreur avec le serveur de base de donnees", $e);
    }
}

function getAllPostsModerator()
{
    try {
        $pdo = getPDOConnection();
        $st = $pdo->prepare("SELECT p.id_Post FROM r_Post p");
        $st->execute();
        $postIds = $st->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        deliver_response(503, "Erreur avec le serveur de base de donnees", $e);
    }
    $result = array();
    foreach ($postIds as $post) {
        array_push($result, getAllPostInfo($post['id_Post']));
    } 
    return $result;
}

// Fonction pour récupérer les données JSON de la table r_Post
function getAllPostInfo($idPost) {
    try {
        $pdo = getPDOConnection();
        $st = $pdo->prepare("SELECT r_Post.Id_Post id, r_Utilisateur.Nom publisher, r_Post.Contenu contenu, count(DISTINCT r_Liker.Id_Utilisateur) nb_likes, COUNT(DISTINCT r_Disliker.Id_Utilisateur) AS nb_dislikes, r_Post.date_publication date
                            FROM r_Post NATURAL JOIN r_Utilisateur
                            LEFT JOIN r_Liker ON r_Post.Id_Post = r_Liker.Id_Post
                            LEFT JOIN r_Disliker ON r_Post.Id_Post = r_Disliker.Id_Post
                            WHERE r_Post.id_Post = ?
                            GROUP BY r_Post.Id_Post, r_Post.Contenu;");
        $st->execute(array($idPost));
        $infos = $st->fetch(PDO::FETCH_ASSOC);
        $listeLikes = getLikesFromPost($idPost);
        $listeDislikes = getDislikesFromPost($idPost);
        $result = array("infos"=>$infos, "likes"=>$listeLikes, "dislikes"=>$listeDislikes);
        return $result;
    } catch (Exception $e) {
        deliver_response(503, "Erreur avec le serveur de base de donnees", $e);
    }
}

function getLikesFromPost($idPost) {
    try {
        $pdo = getPDOConnection();
        $st = $pdo->prepare("SELECT u.nom FROM r_Utilisateur u, r_Liker l WHERE u.Id_Utilisateur = l.Id_Utilisateur AND l.Id_Post = ?");
        $st->execute(array($idPost));
        return $st->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        deliver_response(503, "Erreur avec le serveur de base de donnees", $e);
    }
}

function getDislikesFromPost($idPost) {
    try {
        $pdo = getPDOConnection();
        $st = $pdo->prepare("SELECT u.nom FROM r_Utilisateur u, r_Disliker d WHERE u.Id_Utilisateur = d.Id_Utilisateur AND d.Id_Post = ?");
        $st->execute(array($idPost));
        return $st->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        deliver_response(503, "Erreur avec le serveur de base de donnees", $e);
    }
}

// Fonction pour vérifier si un utilisateur est le publieur d'un post
function isPublisherOf($idUtilisateur, $idPost) {
    $pdo = getPDOConnection();
    $stmt = $pdo->prepare("SELECT * FROM r_Post WHERE Id_Utilisateur = :idUtilisateur AND Id_Post = :idPost");
    $stmt->bindParam(":idUtilisateur", $idUtilisateur);
    $stmt->bindParam(":idPost", $idPost);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return !empty($result);
}

// Fonction pour modifier le contenu d'un post
function modifierContenuPost($idPost, $contenu) {
    $pdo = getPDOConnection();
    $stmt = $pdo->prepare("UPDATE r_Post SET Contenu = ? WHERE Id_Post = ?");
    $stmt->execute(array($contenu, $idPost));
    return $stmt->rowCount();
}

// Fonction pour supprimer un post
function supprimerPost($idPost) {
    $pdo = getPDOConnection();
    $stmt = $pdo->prepare("DELETE FROM r_Post WHERE Id_Post = :idPost");
    $stmt->bindParam(":idPost", $idPost);
    $stmt->execute();
    return $stmt->rowCount();
}

// Fonction pour vérifier si un post existe dans la table r_Post
function existePost($idPost) {
    $pdo = getPDOConnection();
    $stmt = $pdo->prepare("SELECT * FROM r_Post WHERE Id_Post = :idPost");
    $stmt->bindParam(":idPost", $idPost);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return !empty($result);
}
?>