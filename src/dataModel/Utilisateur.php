<?php
    require_once("conx.php");
    require_once('../../dependencies/rest_utils.php');

function isValidUser($username, $password)
{
    try {
        $pdo = getPDOConnection();
        $st = $pdo->prepare("SELECT u.Id_Utilisateur FROM r_Utilisateur u WHERE u.nom = :nom AND u.mot_de_passe = :mdp");
        $st->execute(array($username, $password));
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
        $st->execute(array($username, $password));
        return $st->fetchAll();
    } catch (Exception $e) {
        deliver_response(503, "Erreur avec le serveur de base de donnees", $e);
    }
}

function getDislikedPost($idUtilisateur) {
    try {
        $pdo = getPDOConnection();
        $st = $pdo->prepare("SELECT p from r_Post p, r_Liker l WHERE p.Id_Post = l.Id_Post AND l.Id_Utilisateur = :idUser");
        $st->execute(array($idUtilisateur));
        return $st->fetchAll();
    } catch (Exception $e) {
        deliver_response(503, "Erreur avec le serveur de base de donnees", $e);
    }
}

function getLikedPost($idUtilisateur) {
    try {
        $pdo = getPDOConnection();
        $st = $pdo->prepare("SELECT p from r_Post p, r_Disliker l WHERE p.Id_Post = l.Id_Post AND l.Id_Utilisateur = :idUser");
        $st->execute(array($idUtilisateur));
        return $st->fetchAll();
    } catch (Exception $e) {
        deliver_response(503, "Erreur avec le serveur de base de donnees", $e);
    }
}

function getUtilisateur($idUtilisateur) {
    try {
        $pdo = getPDOConnection();
        $st = $pdo->prepare("Select * from r_Utilisateur where id_Utilisateur = ?");
        $st->execute(array($idUtilisateur));
        return $st;
    } catch (Exception $e) {
        deliver_response(503, "Erreur avec le serveur de base de donnees", $e);
    }
}

function getAllUserInfo($idUtilisateur) {
    try {
        $pdo = getPDOConnection();
        $reqNomUser = $pdo->prepare("Select Nom from r_Utilisateur where id_Utilisateur = ?");
        $reqNomUser->execute(array($idUtilisateur));
        $nomUser = $reqNomUser->fetchAll(PDO::FETCH_ASSOC);
        $listePosts = getListePostsFromUser($idUtilisateur);
        $listeLikes = getListeLikesFromUser($idUtilisateur);
        $listeDislikes = getListeDislikesFromUser($idUtilisateur);
        $result = array("nomUser"=>$nomUser, "listePosts"=>$listePosts, "listeLikes"=>$listeLikes, "listeDislikes"=>$listeDislikes);
        return $result;
    } catch (Exception $e) {
        deliver_response(503, "Erreur avec le serveur de base de donnees", $e);
    }
}

function getListeLikesFromUser($idUser) {
    try {
        $pdo = getPDOConnection();
        $reqListeLikes = $pdo->prepare("Select p.Contenu, p.Id_Post from r_Utilisateur u, r_Post p, r_Liker l where u.Id_Utilisateur = l.Id_Utilisateur and l.Id_Post = p.Id_Post and u.id_Utilisateur = ?");
        $reqListeLikes->execute(array($idUser));
        return $reqListeLikes->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        deliver_response(503, "Erreur avec le serveur de base de donnees", $e);
    }
}

function getListePostsFromUser($idUser) {
    try {
        $pdo = getPDOConnection();
        $reqListePosts = $pdo->prepare("Select p.id_Post, p.Contenu, p.date_publication from r_Utilisateur u, r_Post p where p.id_Utilisateur = u.id_Utilisateur and u.id_Utilisateur = ?");
        $reqListePosts->execute(array($idUser));
        return $reqListePosts->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        deliver_response(503, "Erreur avec le serveur de base de donnees", $e);
    }
}

function getListeDislikesFromUser($idUser) {
    try {
        $pdo = getPDOConnection();
        $reqListeDislikes = $pdo->prepare("Select p.Contenu, p.Id_Post from r_Utilisateur u, r_Post p, r_Disliker d where u.Id_Utilisateur = d.Id_Utilisateur and d.Id_Post = p.Id_Post and u.id_Utilisateur = ?");
        $reqListeDislikes->execute(array($idUser));
        return $reqListeDislikes->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        deliver_response(503, "Erreur avec le serveur de base de donnees", $e);
    }
}

?>