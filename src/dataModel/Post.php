<?php
require_once("conx.php");

function getPost($idPost) {
    try {
        $pdo = getPDOConnection();
        $st = $pdo->prepare("Select * from r_Post where id_Post = ?");
        $st->bindParam(1, $id);
        $id = $idPost;
        $st->execute();
        return $st->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        deliver_response(503, "Erreur avec le serveur de base de donnees", $e);
    }
}

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

function getAllPostsModerator() {
    try {
        $pdo = getPDOConnection();
        $st = $pdo->prepare('SELECT r_Post.Id_Post, r_Utilisateur.Nom nom, r_Post.Contenu contenu, count(DISTINCT r_Liker.Id_Utilisateur) nb_likes, COUNT(DISTINCT r_Disliker.Id_Utilisateur) AS nb_dislikes, r_Post.date_publication date
                            FROM r_Post NATURAL JOIN r_Utilisateur
                            LEFT JOIN r_Liker ON r_Post.Id_Post = r_Liker.Id_Post
                            LEFT JOIN r_Disliker ON r_Post.Id_Post = r_Disliker.Id_Post
                            GROUP BY r_Post.Id_Post, r_Post.Contenu;');   
        $st->execute();
        return $st->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        deliver_response(503, "Erreur avec le serveur de base de donnees", $e);
    }
}
 function getAllPostPublisher() {
    try {
        $pdo = getPDOConnection();
        $st = $pdo->prepare("Select p, from r_Post p Where p.id_Utilisateur = :idUser");
        $st->bindParam("idUser", $idUtilisateur);
        $st->execute();
        return $st->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        deliver_response(503, "Erreur avec le serveur de base de donnees", $e);
    }
 }
function getPostFromUser($idUtilisateur) {
    try {
        $pdo = getPDOConnection();
        $st = $pdo->prepare("Select * from r_Post p Where p.id_Utilisateur = :idUser");
        $st->bindParam("idUser", $idUtilisateur);
        $st->execute();
        return $st->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        deliver_response(503, "Erreur avec le serveur de base de donnees", $e);
    }
}

function creerNouveauPost($idUtilisateur, $contenuMessage) {
    try {
        $pdo = getPDOConnection();
        $st = $pdo->prepare("INSERT INTO r_Post (Id_Post, Contenu, date_publication, Id_Utilisateur) VALUES (NULL, :content,current_timestamp(), :idUser)");
        $st->bindParam("idUser", $idUser);
        $idUser = $idUtilisateur;
        $st->bindParam("content", $conent);
        $conent = $contenuMessage;
        $st->execute();
    } catch (Exception $e) {
        deliver_response(503, "Erreur avec le serveur de base de donnees", $e);
    }
}

function likerPost($idUtilisateur, $idDuPost) {
    try {
        $pdo = getPDOConnection();
        $st = $pdo->prepare("INSERT INTO r_Liker (Id_Utilisateur, Id_Post) VALUES (:idUser, :idPost) ");
        $st->bindParam("idUser", $idUser);
        $idUser = $idUtilisateur;
        $st->bindParam("idPost", $idPost);
        $idPost = $idDuPost;
        $st->execute();
    } catch (Exception $e) {
        deliver_response(503, "Erreur avec le serveur de base de donnees", $e);
    }
}
function dislikerPost($idUtilisateur, $idDuPost) {
    try {
        $pdo = getPDOConnection();
        $st = $pdo->prepare("INSERT INTO r_Disliker (Id_Utilisateur, Id_Post) VALUES (:idUser, :idPost) ");
        $st->bindParam("idUser", $idUser);
        $idUser = $idUtilisateur;
        $st->bindParam("idPost", $idPost);
        $idPost = $idDuPost;
        $st->execute();
    } catch (Exception $e) {
        deliver_response(503, "Erreur avec le serveur de base de donnees", $e);
    }
}

// Fonction pour récupérer les données JSON de la table r_Post
function getAllPostInfo($idPost) {
    $pdo = getPDOConnection();
    $stmt = $pdo->prepare("SELECT * FROM r_Post");
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return json_encode($result);
}

// Fonction pour vérifier si un utilisateur est le publieur d'un post
function isPubliserOf($idUtilisateur, $idPost) {
    $pdo = getPDOConnection();
    $stmt = $pdo->prepare("SELECT * FROM r_Post WHERE Id_Utilisateur = :idUtilisateur AND Id_Post = :idPost");
    $stmt->bindParam(":idUtilisateur", $idUtilisateur);
    $stmt->bindParam(":idPost", $idPost);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return !empty($result);
}

// Fonction pour modifier le contenu d'un post
function modifierContenuPost($idPost, $postedData) {
    $pdo = getPDOConnection();
    $stmt = $pdo->prepare("UPDATE r_Post SET Contenu = :contenu WHERE Id_Post = :idPost");
    $stmt->bindParam(":contenu", $postedData['contenu']);
    $stmt->bindParam(":idPost", $idPost);
    $stmt->execute();
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