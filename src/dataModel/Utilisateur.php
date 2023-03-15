<?php
    require("conx.php");
    require_once('../../dependencies/rest_utils.php');

function isValidUser($username, $password)
{
    try {
        $pdo = getPDOConnection();
        $st = $pdo->prepare("SELECT u.Id_Utilisateur FROM r_Utilisateur u WHERE u.nom = :nom AND u.mot_de_passe = :mdp");
        $st->bindParam("nom", $nom);
        $st->bindParam("mdp", $mdp);
        $nom = $username;
        $mdp = $password;
        $st->execute();
        if ($st->rowCount() > 2) {
            throw new Exception("Deverait pas y avoir plus d'un user ici", 1);
        }
        return $st->rowCount() > 0;
    } catch (Exception $e) {
        deliver_response(503, "Erreur avec le serveur de base de donnees", $e);
    }
}

function getUserInfo($username, $password)
{
    try {
        $pdo = getPDOConnection();
        $st = $pdo->prepare("SELECT u.Id_Utilisateur as id, u.Role as role FROM r_Utilisateur u WHERE u.nom = :nom AND u.mot_de_passe = :mdp");
        $st->bindParam("nom", $nom);
        $st->bindParam("mdp", $mdp);
        $nom = $username;
        $mdp = $password;
        $st->execute();
        return $st->fetchAll();
    } catch (Exception $e) {
        deliver_response(503, "Erreur avec le serveur de base de donnees", $e);
    }
}

function getDislikedPost($idUtilisateur) {
    try {
        $pdo = getPDOConnection();
        $st = $pdo->prepare("SELECT p from r_Post p, r_Liker l WHERE p.Id_Post = l.Id_Post AND l.Id_Utilisateur = :idUser");
        $st->bindParam("idUser", $idUser);
        $idUser = $idUtilisateur;
        $st->execute();
        return $st->fetchAll();
    } catch (Exception $e) {
        deliver_response(503, "Erreur avec le serveur de base de donnees", $e);
    }
}

function getLikedPost($idUtilisateur) {
    try {
        $pdo = getPDOConnection();
        $st = $pdo->prepare("SELECT p from r_Post p, r_Disliker l WHERE p.Id_Post = l.Id_Post AND l.Id_Utilisateur = :idUser");
        $st->bindParam("idUser", $idUser);
        $idUser = $idUtilisateur;
        $st->execute();
        return $st->fetchAll();
    } catch (Exception $e) {
        deliver_response(503, "Erreur avec le serveur de base de donnees", $e);
    }
}

function getUtilisateur($idUtilisateur) {
    try {
        $pdo = getPDOConnection();
        $st = $pdo->prepare("Select * from r_Utilisateur where id_Utilisateur = ?");
        $st->bindParam(1, $id);
        $id = $idUtilisateur;
        $st->execute();
        return $st;
    } catch (Exception $e) {
        deliver_response(503, "Erreur avec le serveur de base de donnees", $e);
    }
}

function getAllUserInfo($idUtilisateur) {

}


?>