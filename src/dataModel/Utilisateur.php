<?php
require("conx.php");

function isValidUser($username, $password)
{
    $pdo = getPDOConnection();
    $st = $pdo->prepare("SELECT u.Id_Utilisateur FROM Utilisateur u WHERE u.nom = :nom AND u.mot_de_passe = :mdp");
    $st->bindParam("nom", $nom);
    $st->bindParam("mdp", $mdp);
    $nom = $username;
    $mdp = $password;
    $st->execute();
    if ($st->rowCount() > 2) {
        throw new Exception("Deverair pas y avoir plus d'un user ici", 1);
    }
    return $st->rowCount() > 0;
}

function getUserInfo($username, $password)
{
    $pdo = getPDOConnection();
    $st = $pdo->prepare("SELECT u.Id_Utilisateur as id, u.Role as role FROM Utilisateur u WHERE u.nom = :nom AND u.mot_de_passe = :mdp");
    $st->bindParam("nom", $nom);
    $st->bindParam("mdp", $mdp);
    $nom = $username;
    $mdp = $password;
    $st->execute();
    return $st->fetchAll();
}

function getDislikedPost($idUtilisateur) {
    $pdo = getPDOConnection();
    $st = $pdo->prepare("SELECT p from r_Post p, r_Liker l WHERE p.Id_Post = l.Id_Post AND l.Id_Utilisateur = :idUser");
    $st->bindParam("idUser", $idUser);
    $idUser = $idUtilisateur;
    $st->execute();
    return $st->fetchAll();

}

function getLikedPost($idUtilisateur) {
    $pdo = getPDOConnection();
    $st = $pdo->prepare("SELECT p from r_Post p, r_Disliker l WHERE p.Id_Post = l.Id_Post AND l.Id_Utilisateur = :idUser");
    $st->bindParam("idUser", $idUser);
    $idUser = $idUtilisateur;
    $st->execute();
    return $st->fetchAll();
}

function getPostFromUser($idUser) {
    $pdo = getPDOConnection();
    $st = $pdo->query("Select * from Post p Where p.id_Utilisateur");   
    $st->execute();
    return $st->fetchAll();
}

function getUtilisateur($idUtilisateur) {
    $pdo = getPDOConnection();
    $st = $pdo->prepare("Select * from Utilisateur where id_Utilisateur = ?");
    $st->bindParam(1, $id);
    $id = $idUtilisateur;
    $st->execute();
    return $st;
}

?>