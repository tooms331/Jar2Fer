-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Client :  127.0.0.1
-- Généré le :  Ven 22 Janvier 2016 à 11:58
-- Version du serveur :  5.6.17
-- Version de PHP :  5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données :  `djalf`
--

-- --------------------------------------------------------

--
-- Structure de la table `archive_panier`
--

CREATE TABLE IF NOT EXISTS `archive_panier` (
  `id_produit` int(11) NOT NULL,
  `id_client` int(11) NOT NULL,
  `variete` varchar(100) NOT NULL,
  `quantite_commande` decimal(11,2) NOT NULL,
  `tarif` decimal(11,2) NOT NULL,
  `unite_vente` varchar(30) NOT NULL,
  `total` decimal(11,2) NOT NULL,
  `jour` date NOT NULL,
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `archive_panier`
--

INSERT INTO `archive_panier` (`id_produit`, `id_client`, `variete`, `quantite_commande`, `tarif`, `unite_vente`, `total`, `jour`, `id`) VALUES
(1, 2, 'Barbentane', '0.20', '4.00', 'Kg', '0.80', '0000-00-00', 1),
(2, 2, 'Durga', '0.60', '4.00', 'Kg', '2.40', '0000-00-00', 1),
(12, 2, 'White satin', '0.60', '3.00', 'Kg', '1.80', '0000-00-00', 1),
(13, 2, 'Kaboko', '1.00', '3.00', 'unit', '3.00', '0000-00-00', 1),
(1, 2, 'Barbentane', '3.00', '4.00', 'Kg', '12.00', '0000-00-00', 2),
(2, 2, 'Durga', '3.50', '4.00', 'Kg', '14.00', '0000-00-00', 2),
(13, 2, 'Kaboko', '2.00', '3.00', 'unit', '6.00', '0000-00-00', 2),
(1, 2, 'Barbentane', '2.00', '4.00', 'Kg', '8.00', '0000-00-00', 3),
(12, 2, 'White satin', '3.00', '3.00', 'Kg', '9.00', '0000-00-00', 3),
(1, 2, 'Barbentane', '2.00', '4.00', 'Kg', '8.00', '0000-00-00', 4),
(12, 2, 'White satin', '3.00', '3.00', 'Kg', '9.00', '0000-00-00', 4),
(2, 2, 'Durga', '10.00', '4.00', 'Kg', '40.00', '0000-00-00', 5),
(2, 2, 'Durga', '3.00', '4.00', 'Kg', '12.00', '0000-00-00', 6),
(2, 2, 'Durga', '6.00', '4.00', 'Kg', '24.00', '0000-00-00', 7),
(2, 2, 'Durga', '1.00', '4.00', 'Kg', '4.00', '0000-00-00', 8),
(1, 2, 'Barbentane', '4.50', '4.00', 'Kg', '18.00', '0000-00-00', 9),
(12, 2, 'White satin', '1.50', '3.00', 'Kg', '4.50', '0000-00-00', 9),
(1, 2, 'Barbentane', '3.50', '4.00', 'Kg', '14.00', '0000-00-00', 10);

-- --------------------------------------------------------

--
-- Structure de la table `a_vendre`
--

CREATE TABLE IF NOT EXISTS `a_vendre` (
  `id` int(11) NOT NULL,
  `produit` varchar(100) NOT NULL,
  `variete` varchar(100) NOT NULL,
  `quantite_disponible` decimal(11,2) NOT NULL,
  `tarif` decimal(11,2) NOT NULL,
  `unite_vente` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `a_vendre`
--

INSERT INTO `a_vendre` (`id`, `produit`, `variete`, `quantite_disponible`, `tarif`, `unite_vente`) VALUES
(1, 'AUBERGINE', 'Barbentane', '0.00', '4.00', 'Kg'),
(2, 'AUBERGINE', 'Durga', '0.00', '4.00', 'Kg'),
(12, 'CAROTTE', 'White satin', '7.50', '3.00', 'Kg'),
(13, 'CHOUX', 'Kaboko', '15.00', '3.00', 'unit');

-- --------------------------------------------------------

--
-- Structure de la table `a_vendre_commune`
--

CREATE TABLE IF NOT EXISTS `a_vendre_commune` (
  `id` int(11) NOT NULL,
  `produit` varchar(100) NOT NULL,
  `variete` varchar(100) NOT NULL,
  `tarif` decimal(11,2) NOT NULL,
  `unite_vente` varchar(30) NOT NULL,
  `quantite_disponible` decimal(11,2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `a_vendre_commune`
--

INSERT INTO `a_vendre_commune` (`id`, `produit`, `variete`, `tarif`, `unite_vente`, `quantite_disponible`) VALUES
(1, 'AUBERGINE', 'Barbentane', '4.00', 'Kg', '0.00'),
(2, 'AUBERGINE', 'Durga', '4.00', 'Kg', '0.00'),
(12, 'CAROTTE', 'White satin', '3.00', 'Kg', '7.50'),
(13, 'CHOUX', 'Kaboko', '3.00', 'unit', '15.00');

-- --------------------------------------------------------

--
-- Structure de la table `base_client`
--

CREATE TABLE IF NOT EXISTS `base_client` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(40) NOT NULL,
  `prenom` varchar(40) NOT NULL,
  `passe` varchar(10) NOT NULL,
  `engagement` int(11) NOT NULL,
  `commentaire` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Contenu de la table `base_client`
--

INSERT INTO `base_client` (`id`, `nom`, `prenom`, `passe`, `engagement`, `commentaire`) VALUES
(1, 'Laurent', 'Thomas', 'xvL03', 1, NULL),
(2, 'Turquier', 'Damien', 'xlV04', 2, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `panier`
--

CREATE TABLE IF NOT EXISTS `panier` (
  `id` int(11) unsigned NOT NULL,
  `id_client` int(11) DEFAULT NULL,
  `mot_de_passe` varchar(30) DEFAULT NULL,
  `produit` varchar(100) DEFAULT NULL,
  `quantite_commande` decimal(11,2) DEFAULT NULL,
  `tarif` decimal(11,2) DEFAULT NULL,
  `total` decimal(11,2) DEFAULT NULL,
  `unite_vente` varchar(30) DEFAULT NULL,
  `variete` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `produit_cultive`
--

CREATE TABLE IF NOT EXISTS `produit_cultive` (
  `id` int(11) NOT NULL,
  `produit` varchar(100) NOT NULL,
  `variete` varchar(100) NOT NULL,
  `tarif` decimal(11,2) NOT NULL,
  `unite_vente` varchar(30) NOT NULL,
  `commentaire` text NOT NULL,
  `temperature_germination_min` int(11) NOT NULL,
  `temperature_germination_opti` int(11) NOT NULL,
  `duree_cycle` int(11) NOT NULL,
  `cycle_semis_plan` int(11) NOT NULL,
  `debut_semis` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Contenu de la table `produit_cultive`
--

INSERT INTO `produit_cultive` (`id`, `produit`, `variete`, `tarif`, `unite_vente`, `commentaire`, `temperature_germination_min`, `temperature_germination_opti`, `duree_cycle`, `cycle_semis_plan`, `debut_semis`) VALUES
(1, 'AUBERGINE', 'Barbentane', '5.00', 'Kg', 'Noire', 15, 20, 75, 0, 'fevrier'),
(2, 'AUBERGINE', 'Dourga', '5.50', 'Kg', 'Blanche', 15, 20, 90, 0, 'fevrier'),
(3, 'BETTERAVE', 'Detroit 2', '3.00', 'Kg', 'Rouge', 10, 15, 140, 56, 'mars'),
(4, 'BETTERAVE', 'Chioggia', '3.00', 'Kg', 'Rouge', 10, 15, 140, 56, 'mars'),
(5, 'BETTE', 'Verte ? carde blanche', '4.30', 'Kg', 'Verte', 0, 19, 56, 0, 'mars'),
(6, 'BETTE', 'Yellow white red', '4.30', 'Kg', 'Tricolore', 0, 19, 56, 0, 'avril'),
(7, 'BETTE', 'Jessyca', '4.50', 'Kg', 'Petite', 0, 19, 56, 0, 'avril'),
(8, 'CAROTTE', 'Napoli F1', '1.50', 'Kg', '', 0, 20, 91, 0, 'janvier'),
(9, 'CAROTTE', 'Yellowstone', '3.00', 'Kg', 'Jaune', 0, 20, 140, 0, 'fevrier'),
(10, 'CAROTTE', 'De colmar', '1.50', 'Kg', '', 0, 20, 140, 0, 'mars'),
(11, 'CAROTTE', 'Gniff', '2.50', 'Kg', 'Rouge', 0, 20, 150, 0, 'mai'),
(12, 'CAROTTE', 'White satin', '4.00', 'Kg', 'Blanche', 0, 20, 95, 0, 'fevrier'),
(13, 'CHOUX', 'Kaboko', '2.00', 'Unit', 'Chinois', 18, 25, 79, 0, 'fevrier'),
(14, 'CHOUX', 'Granat', '2.00', 'Unit', '', 18, 25, 100, 0, 'avril'),
(15, 'CHOUX', 'Gros des vertus', '2.00', 'Unit', 'Choux de milan', 0, 0, 110, 0, 'mars'),
(16, 'CHOUX', 'Marner Grufewi', '2.00', 'Unit', '', 0, 0, 100, 0, 'fevrier'),
(17, 'CHOUX', 'VorboteN?3', '2.00', 'Unit', '', 0, 0, 65, 0, 'fevrier'),
(18, 'CHOUX', 'Stanton', '2.00', 'Unit', 'Choux d''hiver', 0, 0, 190, 0, 'juin'),
(19, 'CHOUX', 'Deadon', '3.00', 'Unit', '', 0, 0, 190, 0, 'juin'),
(20, 'CHOUX', 'Azur star', '3.00', 'Kg', 'Rave', 15, 20, 80, 0, 'janvier'),
(21, 'CHOUX', 'Delicatessen white', '3.00', 'Kg', '', 15, 20, 80, 0, 'fevrier'),
(22, 'CHOUX', 'Odysseus', '3.00', 'Unit', 'Choux fleur', 0, 20, 100, 0, 'janvier'),
(23, 'CHOUX', 'Boule de neige', '3.00', 'Unit', '', 0, 20, 150, 0, 'mai'),
(24, 'CHOUX', 'Belot F1', '3.00', 'Unit', '', 0, 20, 170, 0, 'juin'),
(25, 'CHOUX', 'Belstar', '5.00', 'Unit', 'Brocolis', 0, 20, 100, 0, 'janvier'),
(26, 'CHOUX', 'Cima di rapa', '5.00', 'Unit', '', 0, 20, 100, 0, 'avril'),
(27, 'CHOUX', 'Calabrais hatif', '5.00', 'Unit', '', 0, 20, 110, 0, 'mai'),
(28, 'CHOUX', 'Chateaurenard', '2.00', 'Unit', 'Choux cabu blanc', 0, 22, 110, 0, 'janvier'),
(29, 'CHOUX', 'C?ur de b?uf', '2.00', 'Unit', '', 0, 22, 110, 0, 'fevrier'),
(30, 'CHOUX', 'Amarant', '3.00', 'Unit', 'Cabu rouge', 0, 20, 100, 0, 'avril'),
(31, 'CHOUX', 'Groninger', '4.00', 'kg', 'Bruxelle', 0, 20, 150, 0, 'avril'),
(32, 'CELERI', 'Tango', '3.00', 'Kg', 'Branche', 0, 20, 120, 0, 'janvier'),
(33, 'CELERI', 'Monarch', '3.00', 'Unit', 'Rave', 15, 20, 150, 0, 'avril'),
(34, 'EPINARD', 'Noorman', '5.00', 'Kg', 'Et', 0, 18, 60, 0, 'mars'),
(35, 'EPINARD', 'G?ant d''hiver', '5.00', 'Kg', 'Hiver', 0, 20, 60, 0, 'aout'),
(36, 'COURGE', 'Spaghetti', '4.00', 'Kg', '', 0, 25, 130, 0, 'mars'),
(37, 'COURGE', 'Pomme d''or', '4.00', 'Kg', '', 0, 25, 130, 0, 'mars'),
(38, 'COURGE', 'Early butternut', '4.00', 'Kg', '', 0, 25, 110, 0, 'mars'),
(39, 'COURGETTE', 'Black beauty', '3.00', 'Kg', 'Allong', 18, 30, 100, 0, 'avril'),
(40, 'COURGETTE', 'Gold rush F1', '3.00', 'Kg', 'Allong', 18, 30, 100, 0, 'avril'),
(41, 'COURGETTE', 'Di faenza', '3.00', 'Kg', 'Allong', 18, 30, 100, 0, 'avril'),
(42, 'COURGETTE', 'Verte des maraichers', '3.00', 'Kg', 'Allong', 18, 30, 100, 0, 'avril'),
(43, 'COURGETTE', 'Nice ? fruit rond', '3.00', 'Kg', 'Ronde', 18, 30, 100, 0, 'avril'),
(44, 'CONCOMBRE', 'Tanja', '2.00', 'Unit', 'Plein champs', 16, 30, 120, 0, 'mars'),
(45, 'CONCOMBRE', 'White wonder', '2.00', 'Unit', 'Plein champs', 16, 30, 100, 0, 'mars'),
(46, 'CONCOMBRE', 'Paladium F1', '2.00', 'Unit', 'sous abris', 16, 30, 100, 0, 'fevrier'),
(47, 'CONCOMBRE', 'Lockheed F1', '2.00', 'Unit', 'sous abris', 16, 30, 100, 0, 'fevrier'),
(48, 'MAIS', 'Golden bantam', '1.00', 'Unit', '', 0, 17, 100, 0, 'avril'),
(49, 'NAVET', 'Plat de milan', '2.00', 'Kg', 'Plat violet', 0, 18, 90, 0, 'fevrier'),
(50, 'NAVET', 'Blanc globe', '2.00', 'Kg', 'Violet', 0, 18, 60, 0, 'juin'),
(51, 'NAVET', 'Jaune boule d''or', '2.00', 'Kg', 'Jaune', 0, 18, 60, 0, 'juillet'),
(52, 'NAVET', 'Petrowski', '2.00', 'Kg', 'Jaune', 0, 18, 60, 0, 'janvier'),
(53, 'OIGNON', 'Musona', '3.00', 'Kg', 'Blanc', 0, 15, 210, 0, 'janvier'),
(54, 'OIGNON', 'Rosso lungo', '2.00', 'Kg', 'Jaune', 0, 15, 130, 0, 'fevrier'),
(55, 'OIGNON', 'Red baron', '4.00', 'Kg', 'Rouge', 0, 15, 150, 0, 'fevrier'),
(56, 'OIGNON', 'Ros? d''armorique', '3.00', 'Kg', 'Rose', 0, 15, 150, 0, 'mars'),
(57, 'OIGNON', 'Stuttgart', '2.00', 'Kg', '', 0, 15, 130, 0, 'mars'),
(58, 'POIREAU', 'Bleu de solaise', '3.00', 'Kg', 'Hiver', 10, 22, 240, 0, 'fevrier'),
(59, 'POIREAU', 'Atlanta', '3.00', 'Kg', 'Hiver', 0, 0, 0, 0, ''),
(60, 'POIREAU', 'Hannibal', '3.00', 'Kg', 'Et', 10, 22, 180, 0, 'decembre'),
(61, 'POIS', 'Sweet horizon', '5.00', 'Kg', '', 0, 22, 120, 0, 'fevrier'),
(62, 'POIVRON', 'Quadratto dasti giallo', '5.00', 'Kg', 'Jaune', 20, 22, 130, 0, 'fevrier'),
(63, 'POIVRON', 'Doux long des landes', '5.00', 'Kg', 'Rouge', 20, 22, 130, 0, 'fevrier'),
(64, 'POIVRON', 'Yolo wonder', '5.00', 'Kg', 'Vert ', 20, 22, 130, 0, 'fevrier'),
(65, 'POIVRON', 'Cubo orange', '5.00', 'Kg', 'Orange', 20, 22, 130, 0, 'fevrier'),
(66, 'POTIMARRON', 'Red kuri', '4.00', 'Kg', '', 0, 25, 180, 0, 'mars'),
(67, 'RADIS', 'Ostergruss', '0.00', 'Kg', 'Rouge', 0, 20, 100, 0, 'janvier'),
(68, 'RADIS', 'Gla?on', '3.00', 'Kg', 'Blanc', 0, 20, 60, 0, 'fevrier'),
(69, 'RADIS', 'Rond gros noir', '0.00', 'Kg', 'Noir ', 0, 20, 90, 0, 'juin'),
(70, 'RADIS', 'Rond raxe', '0.00', 'Kg', 'Rose', 0, 20, 60, 0, 'mars'),
(72, 'TOMATE', 'Beefsteak', '2.00', 'Kg', 'Grosse', 0, 20, 100, 0, 'fevrier'),
(73, 'TOMATE', 'Teton de venus', '2.00', 'Kg', 'Jaune', 0, 20, 80, 0, 'fevrier'),
(74, 'TOMATE', 'Tula', '2.00', 'Kg', 'Noire', 0, 20, 80, 0, 'fevrier'),
(75, 'TOMATE', 'Groseille', '8.00', 'Kg', 'Cerise', 0, 0, 0, 0, ''),
(76, 'TOMATE', 'Clementine', '8.00', 'Kg', 'Cerise', 0, 20, 100, 0, 'fevrier'),
(77, 'TOMATE', 'Carotina', '1.00', 'Kg', 'Rouge', 0, 20, 80, 0, 'fevrier'),
(78, 'TOMATE', 'Pillu', '1.00', 'Kg', 'Grappe', 0, 20, 80, 0, 'fevrier'),
(79, 'TOMATE', 'Matina', '1.00', 'Kg', 'Grappe', 0, 20, 80, 0, 'fevrier'),
(80, 'HARICOT', 'Neckark?nigin', '5.00', 'Kg', 'a rame, vert', 12, 22, 70, 0, 'avril'),
(81, 'HARICOT', 'Neckargold', '5.00', 'Kg', '', 12, 22, 70, 0, 'avril'),
(82, 'SALADE', 'Batavia kamalia', '1.00', 'Unit', 'Rouge, champ', 0, 18, 100, 0, 'janvier'),
(83, 'SALADE', 'Batavia reine des neiges', '1.00', 'Unit', 'Iceberg', 0, 18, 100, 0, 'fevrier'),
(84, 'SALADE', 'Feuille de ch?ne bowl', '1.00', 'Unit', 'Verte, champ', 0, 18, 100, 0, 'fevrier'),
(85, 'SALADE', 'Feuille de ch?ne rouge cornoua', '1.00', 'Unit', 'Rouge, champ', 0, 18, 100, 0, 'janvier'),
(86, 'SALADE', 'Romaine tantan', '1.00', 'Unit', '', 0, 18, 100, 0, 'fevrier'),
(87, 'SALADE', 'Batavia tokapie', '1.00', 'Unit', 'Verte, abris', 0, 18, 100, 0, 'fevrier'),
(88, 'SALADE', 'Lollo bionda', '1.00', 'Unit', 'Verte, fris', 0, 18, 100, 0, 'fevrier'),
(89, 'SALADE', 'Lollo rossa senorita', '1.00', 'Unit', 'Rouge, fris', 0, 18, 100, 0, 'janvier'),
(90, 'MESCLUN', 'Mizuna', '12.00', 'Kg', 'Verte', 0, 18, 45, 0, 'mars'),
(91, 'MESCLUN', 'Mizuna rouge', '12.00', 'Kg', 'Rouge', 0, 18, 45, 0, 'mars'),
(92, 'MESCLUN', 'Chicor?es', '12.00', 'Kg', '', 0, 18, 45, 0, 'mars'),
(93, 'MESCLUN', 'Maison', '12.00', 'Kg', 'ce que l''on veut', 0, 18, 45, 0, 'mars'),
(94, 'AROMATIQUE', 'fenouil', '1.00', 'Bouquet', '', 0, 21, 0, 0, 'avril'),
(95, 'AROMATIQUE', 'Aneth', '1.00', 'Bouquet', '', 0, 0, 60, 0, 'mars'),
(96, 'AROMATIQUE', 'Basilic a petites feuilles', '1.00', 'Bouquet', '', 15, 20, 90, 0, 'mars'),
(97, 'AROMATIQUE', 'Basilic canelle', '1.00', 'Bouquet', '', 15, 20, 90, 0, 'mars'),
(98, 'AROMATIQUE', 'Basilic citron', '1.00', 'Bouquet', '', 15, 20, 90, 0, 'mars'),
(99, 'AROMATIQUE', 'Basilic tha', '1.00', 'Bouquet', '', 15, 20, 90, 0, 'mars'),
(100, 'AROMATIQUE', 'Ciboulette commune', '1.00', 'Bouquet', '', 15, 20, 90, 0, 'fevrier'),
(101, 'AROMATIQUE', 'Coriandre a petite graine', '1.00', 'Bouquet', '', 15, 20, 40, 0, 'fevrier'),
(102, 'AROMATIQUE', 'Liveche officinale', '1.00', 'Bouquet', '', 15, 20, 365, 0, 'mars'),
(103, 'AROMATIQUE', 'Marjolaine (origan)', '1.00', 'Bouquet', '', 15, 20, 60, 0, 'mars'),
(104, 'AROMATIQUE', 'Melisse officinale', '1.00', 'Bouquet', '', 15, 20, 75, 0, 'mars'),
(105, 'AROMATIQUE', 'Menthe', '1.00', 'Bouquet', '', 15, 20, 60, 0, 'mars'),
(106, 'AROMATIQUE', 'Persil commun', '1.00', 'Bouquet', '', 15, 20, 90, 0, 'fevrier'),
(107, 'AROMATIQUE', 'Romarin officinal', '1.00', 'Bouquet', '', 15, 20, 0, 0, 'mars'),
(108, 'AROMATIQUE', 'Sariette annuelle', '1.00', 'Bouquet', '', 15, 20, 90, 0, 'mars'),
(109, 'AROMATIQUE', 'Sauge officinale', '1.00', 'Bouquet', '', 15, 20, 90, 0, 'mars'),
(110, 'AROMATIQUE', 'Thym ordinaire', '1.00', 'Bouquet', '', 15, 20, 0, 0, 'mars');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
