-- Script made for mySql databases
CREATE TABLE Utilisateur(
   Id_Utilisateur INT AUTO_INCREMENT,
   Nom VARCHAR(50) NOT NULL,
   mot_de_passe VARCHAR(50) NOT NULL,
   Role CHAR(9) NOT NULL,
   PRIMARY KEY(Id_Utilisateur)
);

CREATE TABLE Post(
   Id_Post INT AUTO_INCREMENT,
   Contenu VARCHAR(256) NOT NULL,
   date_publication DATETIME NOT NULL,
   Id_Utilisateur INT NOT NULL,
   PRIMARY KEY(Id_Post),
   FOREIGN KEY(Id_Utilisateur) REFERENCES Utilisateur(Id_Utilisateur)
);

CREATE TABLE Liker(
   Id_Utilisateur INT,
   Id_Post INT,
   PRIMARY KEY(Id_Utilisateur, Id_Post),
   FOREIGN KEY(Id_Utilisateur) REFERENCES Utilisateur(Id_Utilisateur),
   FOREIGN KEY(Id_Post) REFERENCES Post(Id_Post)
);

CREATE TABLE Disliker(
   Id_Utilisateur INT,
   Id_Post INT,
   PRIMARY KEY(Id_Utilisateur, Id_Post),
   FOREIGN KEY(Id_Utilisateur) REFERENCES Utilisateur(Id_Utilisateur),
   FOREIGN KEY(Id_Post) REFERENCES Post(Id_Post)
);
