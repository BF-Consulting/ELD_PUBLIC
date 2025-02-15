-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : lun. 05 sep. 2022 à 09:00
-- Version du serveur :  10.4.13-MariaDB
-- Version de PHP : 7.4.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `eld`
--

-- --------------------------------------------------------

--
-- Structure de la table `base_file`
--

DROP TABLE IF EXISTS `base_file`;
CREATE TABLE IF NOT EXISTS `base_file` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tronquer la table avant d'insérer `base_file`
--

TRUNCATE TABLE `base_file`;
--
-- Déchargement des données de la table `base_file`
--

INSERT INTO `base_file` (`id`, `name`, `type`) VALUES
(1, 'pensionné.xlsx', 'pensionné'),
(2, 'salarié.xlsx', 'salarié');

-- --------------------------------------------------------

--
-- Structure de la table `doctrine_migration_versions`
--

DROP TABLE IF EXISTS `doctrine_migration_versions`;
CREATE TABLE IF NOT EXISTS `doctrine_migration_versions` (
  `version` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Tronquer la table avant d'insérer `doctrine_migration_versions`
--

TRUNCATE TABLE `doctrine_migration_versions`;
--
-- Déchargement des données de la table `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20210626001352', '2022-08-30 23:34:55', 2254),
('DoctrineMigrations\\Version20210626065331', '2022-08-30 23:34:57', 0),
('DoctrineMigrations\\Version20210626075750', '2022-08-30 23:34:57', 645);

-- --------------------------------------------------------

--
-- Structure de la table `fournisseur`
--

DROP TABLE IF EXISTS `fournisseur`;
CREATE TABLE IF NOT EXISTS `fournisseur` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `num_fournisseur` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_add` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tronquer la table avant d'insérer `fournisseur`
--

TRUNCATE TABLE `fournisseur`;
--
-- Déchargement des données de la table `fournisseur`
--

INSERT INTO `fournisseur` (`id`, `name`, `num_fournisseur`, `date_add`) VALUES
(1, 'fournisseur', '1287dea', '2022-09-02');

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `surname` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_add` date NOT NULL,
  `date_update` date DEFAULT NULL,
  `roles` longtext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:array)',
  PRIMARY KEY (`id`),
  UNIQUE KEY `UNIQ_8D93D649F85E0677` (`username`),
  UNIQUE KEY `UNIQ_8D93D649E7927C74` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Tronquer la table avant d'insérer `user`
--

TRUNCATE TABLE `user`;
--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id`, `name`, `surname`, `username`, `email`, `password`, `date_add`, `date_update`, `roles`) VALUES
(1, 'fabrice', 'fabrice', 'fabrice', 'fabrice@exemple.com', '$2y$13$OiZ721wvarIKPGx6wChGuO86agSUad4LRhvF17uyMkqcpRpxxeQTi', '2022-08-30', '2022-09-02', 'a:1:{i:0;s:10:\"ROLE_ADMIN\";}');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
