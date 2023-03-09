-- Script made for mySql databases
CREATE TABLE r_Utilisateur(
   Id_Utilisateur INT AUTO_INCREMENT,
   Nom VARCHAR(50) NOT NULL,
   mot_de_passe VARCHAR(50) NOT NULL,
   Role CHAR(9) NOT NULL,
   PRIMARY KEY(Id_Utilisateur)
);

CREATE TABLE r_Post(
   Id_Post INT AUTO_INCREMENT,
   Contenu VARCHAR(256) NOT NULL,
   date_publication DATETIME NOT NULL,
   Id_Utilisateur INT NOT NULL,
   PRIMARY KEY(Id_Post),
   FOREIGN KEY(Id_Utilisateur) REFERENCES r_Utilisateur(Id_Utilisateur)
);

CREATE TABLE r_Hashtags(
   Id_Hashtags INT AUTO_INCREMENT,
   libelle VARCHAR(16) NOT NULL,
   PRIMARY KEY(Id_Hashtags),
   UNIQUE(libelle)
);

CREATE TABLE r_Liker(
   Id_Utilisateur INT,
   Id_Post INT,
   PRIMARY KEY(Id_Utilisateur, Id_Post),
   FOREIGN KEY(Id_Utilisateur) REFERENCES r_Utilisateur(Id_Utilisateur),
   FOREIGN KEY(Id_Post) REFERENCES r_Post(Id_Post)
);

CREATE TABLE r_Disliker(
   Id_Utilisateur INT,
   Id_Post INT,
   PRIMARY KEY(Id_Utilisateur, Id_Post),
   FOREIGN KEY(Id_Utilisateur) REFERENCES r_Utilisateur(Id_Utilisateur),
   FOREIGN KEY(Id_Post) REFERENCES r_Post(Id_Post)
);

CREATE TABLE r_Contient(
   Id_Post INT,
   Id_Hashtags INT,
   PRIMARY KEY(Id_Post, Id_Hashtags),
   FOREIGN KEY(Id_Post) REFERENCES r_Post(Id_Post),
   FOREIGN KEY(Id_Hashtags) REFERENCES r_Hashtags(Id_Hashtags)
);
