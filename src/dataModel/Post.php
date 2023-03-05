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
    return $st;
}

function getPostFromUser($idUser) {
    $pdo = getPDOConnection();
    $st = $pdo->query("Select * from Post p Where p.id_Utilisateur");   
    return $st;
}

function creerNouveauPost($idUtilisateur, $contenuMessage) {
    $pdo = getPDOConnection();
    
    // TODO
}
?>