<?php
require("conx.php");

function getPost($idPost) {
    $pdo = getPDOConnection();
    $st = $pdo->prepare("Select * from Post where id_Post = ?");
    $st->bindParam(1, $id);
    $id = $idPost;
    $st->execute();
    return $st;
}

function getAllPost() {
    $pdo = getPDOConnection();
    $st = $pdo->query("Select * from Post");   
    st->execute();
    return $st;
}

function getPostFromUser($idUser) {
    $pdo = getPDOConnection();
    $st = $pdo->query("Select * from Post p Where p.id_Utilisateur");   
    return $st;
}

function creerNouveauPost($idUtilisateur, $contenuMessage) {
    $pdo = getPDOConnection();
    $st = $pdo->prepare("INSERT INTO r_Post (Id_Post, Contenu, date_publication, Id_Utilisateur) VALUES (NULL, :content,current_timestamp(), :idUser)");
    $st->bindParam("idUser", $idUser);
    $idUser = $idUtilisateur;
    $st->bindParam("content", $conent);
    $conent = $contenuMessage;
    $st->execute();
}
?>