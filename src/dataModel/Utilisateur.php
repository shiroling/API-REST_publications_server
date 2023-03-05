<?php
require("conx.php");

function getUtilisateur($idUtilisateur) {
    $pdo = getPDOConnection();
    $st = $pdo->prepare("Select * from Utilisateur where id_Utilisateur = ?");
    $st->bindParam(1, $id);
    $id = $idUtilisateur;
    $st->execute();
    return $st;
}

function getLikedPost($idUtilisateur) {
    $pdo = getPDOConnection();
    $st = $pdo->query("Select * from Post p, Liker l where p.id_Post = l.id_Post AND l.id_Utilisateur = :idUser");   
    $st->bindParam("idUser", $id);
    $id = $idUtilisateur;
    return $st;
}

function getDislikedPost($idUtilisateur) {
    $pdo = getPDOConnection();
    $st = $pdo->query("Select * from Post p, Disliker d where p.id_Post = d.id_Post AND d.id_Utilisateur = :idUser");
    $st->bindParam("idUser", $id);
    $id = $idUtilisateur;
    return $st;
}

function getPostFromUser($idUser) {
    $pdo = getPDOConnection();
    $st = $pdo->query("Select * from Post p Where p.id_Utilisateur");   
    return $st;
}

function ajouterNouveauUtilisateur($name, $password, $role) {
    if($role != "moderator" && $role != "publisher") {
        throw new Exception("IllegalArgumentExecption : bad role", 1);
        exit();
    }
    // TODO
}
?>