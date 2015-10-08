-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Client: localhost
-- Généré le: Ven 02 Octobre 2015 à 13:37
-- Version du serveur: 5.5.44-0ubuntu0.14.04.1
-- Version de PHP: 5.5.9-1ubuntu4.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données: `Extraits`
--
CREATE DATABASE IF NOT EXISTS `Extraits` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `Extraits`;

-- --------------------------------------------------------

--
-- Structure de la table `CompteNames`
--

CREATE TABLE IF NOT EXISTS `CompteNames` (
  `Numero_de_compte` varchar(20) NOT NULL,
  `Compte_Name` varchar(200) NOT NULL,
  `Note` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`Numero_de_compte`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `Extraits`
--

CREATE TABLE IF NOT EXISTS `Extraits` (
  `Numero_de_compte` varchar(20) NOT NULL DEFAULT '',
  `Nom_du_compte` varchar(40) DEFAULT NULL,
  `Compte_partie_adverse` varchar(20) DEFAULT NULL,
  `Numero_de_mouvement` varchar(20) NOT NULL DEFAULT '0',
  `ANNEE + REFERENCE` varchar(10) DEFAULT NULL COMMENT 'BNP format',
  `Date_comptable` date NOT NULL DEFAULT '0000-00-00',
  `Date_valeur` date NOT NULL DEFAULT '0000-00-00',
  `Montant` float DEFAULT NULL,
  `Devise` varchar(1000) DEFAULT NULL,
  `Libelles` varchar(2000) DEFAULT NULL,
  `Details_du_mouvement` varchar(2000) DEFAULT NULL,
  `Message` varchar(2000) DEFAULT NULL,
  PRIMARY KEY (`Numero_de_compte`,`Numero_de_mouvement`,`Date_comptable`,`Date_valeur`),
  KEY `ANNEE + REFERENCE` (`ANNEE + REFERENCE`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
