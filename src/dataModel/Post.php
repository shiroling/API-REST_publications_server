<?php
require("conx.php");

function getPost($idPost) {
    try {
        $pdo = getPDOConnection();
        $st = $pdo->prepare("Select * from r_Post where id_Post = ?");
        $st->bindParam(1, $id);
        $id = $idPost;
        $st->execute();
        return $st;
    } catch (Exception $e) {
        deliver_response(503, "Erreur avec le serveur de base de donnees", $e);
    }
}

function getAllPosts() {
    try {
        $pdo = getPDOConnection();
        $st = $pdo->query("Select * from r_Post p");   
        $st->execute();
        return $st->fetchAll();
    } catch (Exception $e) {
        deliver_response(503, "Erreur avec le serveur de base de donnees", $e);
    }
}

function getPostFromUser($idUser) {
    try {
        $pdo = getPDOConnection();
        $st = $pdo->query("Select * from r_Post p Where p.id_Utilisateur = :idUser");
        $st->bindParam("idUser", $idUser);
        $st->execute();
        return $st->fetchAll();
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

?>