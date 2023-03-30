SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

CREATE DATABASE IF NOT EXISTS `rest_bdd_despaux_couturier` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `rest_bdd_despaux_couturier`;

CREATE TABLE `r_Disliker` (
  `Id_Utilisateur` int(11) NOT NULL,
  `Id_Post` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `r_Disliker` (`Id_Utilisateur`, `Id_Post`) VALUES
(2, 19),
(3, 16),
(4, 17);

CREATE TABLE `r_Liker` (
  `Id_Utilisateur` int(11) NOT NULL,
  `Id_Post` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `r_Liker` (`Id_Utilisateur`, `Id_Post`) VALUES
(2, 17),
(2, 18),
(3, 14),
(3, 15),
(3, 16),
(4, 18);

CREATE TABLE `r_Post` (
  `Id_Post` int(11) NOT NULL,
  `Contenu` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_publication` datetime NOT NULL DEFAULT current_timestamp(),
  `Id_Utilisateur` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `r_Post` (`Id_Post`, `Contenu`, `date_publication`, `Id_Utilisateur`) VALUES
(13, 'Idée (de benoit) : application de location de chiens', '2023-03-23 07:45:06', 2),
(14, 'Idée (de benoit) : Entreprise de collecte de cartons mouillés', '2023-03-23 07:45:33', 2),
(15, 'Idée (de Noa) : Une site qui permet d\'aiguiser des couteaux à distance (stance)', '2023-03-23 07:48:18', 2),
(16, 'Idée (de Noa) : Une association qui permet de voler des chariots', '2023-03-23 07:48:49', 2),
(17, 'Idée (de Lucas) : Application qui permet de savoir où sont les rampes de skate les plus glissantes.', '2023-03-23 07:50:21', 3),
(18, 'Idée (de Benoit) : Logiciel pour passer ses heures de conduite en ligne', '2023-03-23 07:51:38', 3),
(19, 'Idée (de Noa) : Une école qui apprend à repasser des chaussettes un peu humides.', '2023-03-23 07:55:05', 3),

CREATE TABLE `r_Utilisateur` (
  `Id_Utilisateur` int(11) NOT NULL,
  `Nom` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mot_de_passe` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Role` char(9) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `r_Utilisateur` (`Id_Utilisateur`, `Nom`, `mot_de_passe`, `Role`) VALUES
(1, 'admin', '8c6976e5b5410415bde908bd4dee15dfb167a9c873fc4bb8a81f6f2ab448a918', 'moderator'),
(2, 'Q.couturier', '42918bcc531588a6ba0387bc1ddc30176c08c390532074a8685f118bdea05a48', 'publisher'),
(3, 'N.despaux', '7467b914d771fcfd9291894768fa54543b2f7c45265cddc8bb6a97168f214328', 'publisher'),
(4, 'J.broisin', 'e23c3d7ff76f6e6235ce091f2fcd5fd35748677799d1637acf5ba2bca350e258', 'publisher'),
(5, 'B.arnault', '76baa2c486977e326cffa06d7d80cb4973f587d5ed34b2d5e8ad4199a222fad1', 'publisher');


ALTER TABLE `r_Disliker`
  ADD PRIMARY KEY (`Id_Utilisateur`,`Id_Post`),
  ADD KEY `Id_Post` (`Id_Post`);

ALTER TABLE `r_Liker`
  ADD PRIMARY KEY (`Id_Utilisateur`,`Id_Post`),
  ADD KEY `Id_Post` (`Id_Post`);

ALTER TABLE `r_Post`
  ADD PRIMARY KEY (`Id_Post`),
  ADD KEY `Id_Utilisateur` (`Id_Utilisateur`);

ALTER TABLE `r_Utilisateur`
  ADD PRIMARY KEY (`Id_Utilisateur`);


ALTER TABLE `r_Post`
  MODIFY `Id_Post` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

ALTER TABLE `r_Utilisateur`
  MODIFY `Id_Utilisateur` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;


ALTER TABLE `r_Disliker`
  ADD CONSTRAINT `r_Disliker_ibfk_1` FOREIGN KEY (`Id_Utilisateur`) REFERENCES `r_Utilisateur` (`Id_Utilisateur`),
  ADD CONSTRAINT `r_Disliker_ibfk_2` FOREIGN KEY (`Id_Post`) REFERENCES `r_Post` (`Id_Post`);

ALTER TABLE `r_Liker`
  ADD CONSTRAINT `r_Liker_ibfk_1` FOREIGN KEY (`Id_Utilisateur`) REFERENCES `r_Utilisateur` (`Id_Utilisateur`),
  ADD CONSTRAINT `r_Liker_ibfk_2` FOREIGN KEY (`Id_Post`) REFERENCES `r_Post` (`Id_Post`);

ALTER TABLE `r_Post`
  ADD CONSTRAINT `r_Post_ibfk_1` FOREIGN KEY (`Id_Utilisateur`) REFERENCES `r_Utilisateur` (`Id_Utilisateur`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
