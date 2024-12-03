-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : sam. 18 mai 2024 à 06:51
-- Version du serveur : 5.7.36
-- Version de PHP : 7.4.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `gescom`
--

-- --------------------------------------------------------

--
-- Structure de la table `articles`
--

DROP TABLE IF EXISTS `articles`;
CREATE TABLE IF NOT EXISTS `articles` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `designation` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `quantitePack` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `articles_designation_unique` (`designation`)
) ENGINE=MyISAM AUTO_INCREMENT=1613 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `article_facture_achats`
--

DROP TABLE IF EXISTS `article_facture_achats`;
CREATE TABLE IF NOT EXISTS `article_facture_achats` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `idArticle` bigint(20) UNSIGNED NOT NULL,
  `idFacture` bigint(20) UNSIGNED NOT NULL,
  `quantite` int(11) NOT NULL,
  `prixUnitaire` double NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `article_facture_achats_idfacture_foreign` (`idFacture`),
  KEY `article_facture_achats_idarticle_foreign` (`idArticle`)
) ENGINE=MyISAM AUTO_INCREMENT=312 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `article_facture_ventes`
--

DROP TABLE IF EXISTS `article_facture_ventes`;
CREATE TABLE IF NOT EXISTS `article_facture_ventes` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `idArticle` bigint(20) UNSIGNED NOT NULL,
  `idFacture` bigint(20) UNSIGNED NOT NULL,
  `quantite` int(11) NOT NULL,
  `prixUnitaire` double NOT NULL,
  `prixAchat` double NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `quantiteAffichee` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `article_facture_ventes_idfacture_foreign` (`idFacture`),
  KEY `article_facture_ventes_idarticle_foreign` (`idArticle`)
) ENGINE=MyISAM AUTO_INCREMENT=257 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `depots`
--

DROP TABLE IF EXISTS `depots`;
CREATE TABLE IF NOT EXISTS `depots` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `intitule` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `prefixe` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `adresse` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `depots_intitule_unique` (`intitule`),
  UNIQUE KEY `prefixe` (`prefixe`)
) ENGINE=MyISAM AUTO_INCREMENT=154 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `facture_achats`
--

DROP TABLE IF EXISTS `facture_achats`;
CREATE TABLE IF NOT EXISTS `facture_achats` (
  `primaryKey` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `dateAchat` date NOT NULL,
  `ReferenceFactureAchat` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nomFournisseur` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contactFournisseur` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `idDepot` bigint(20) UNSIGNED NOT NULL,
  `statut` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sommePayee` double DEFAULT NULL,
  `montantTotal` double DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `primaryKey` (`primaryKey`),
  KEY `facture_achats_iddepot_foreign` (`idDepot`)
) ENGINE=MyISAM AUTO_INCREMENT=283 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `facture_ventes`
--

DROP TABLE IF EXISTS `facture_ventes`;
CREATE TABLE IF NOT EXISTS `facture_ventes` (
  `primaryKey` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `dateVente` date DEFAULT NULL,
  `nomClient` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `contactClient` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `statut` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sommePayee` double NOT NULL,
  `montantTotal` double DEFAULT NULL,
  `idDepot` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `facture_ventes_iddepot_foreign` (`idDepot`)
) ENGINE=MyISAM AUTO_INCREMENT=215 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(14, '2024_05_16_090620_create_paiements_achat_table', 1);

-- --------------------------------------------------------

--
-- Structure de la table `paiements_achat`
--

DROP TABLE IF EXISTS `paiements_achat`;
CREATE TABLE IF NOT EXISTS `paiements_achat` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `datePayement` date NOT NULL,
  `idFacture` int(11) NOT NULL,
  `somme` double NOT NULL,
  `reste` double NOT NULL,
  `mode` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `paiements_achat_idfacture_foreign` (`idFacture`)
) ENGINE=MyISAM AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
CREATE TABLE IF NOT EXISTS `password_resets` (
  `email` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `payements`
--

DROP TABLE IF EXISTS `payements`;
CREATE TABLE IF NOT EXISTS `payements` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `datePayement` date DEFAULT NULL,
  `idFacture` int(11) NOT NULL,
  `somme` double NOT NULL,
  `reste` double NOT NULL,
  `mode` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payements_idfacture_foreign` (`idFacture`)
) ENGINE=MyISAM AUTO_INCREMENT=180 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `stocks`
--

DROP TABLE IF EXISTS `stocks`;
CREATE TABLE IF NOT EXISTS `stocks` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `idDepot` bigint(20) UNSIGNED NOT NULL,
  `idArticle` bigint(20) UNSIGNED NOT NULL,
  `quantiteDepot` int(11) NOT NULL,
  `prixMoyenAchat` double NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `stocks_iddepot_foreign` (`idDepot`),
  KEY `stocks_idarticle_foreign` (`idArticle`)
) ENGINE=MyISAM AUTO_INCREMENT=607 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `tarifs`
--

DROP TABLE IF EXISTS `tarifs`;
CREATE TABLE IF NOT EXISTS `tarifs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `idArticle` bigint(20) UNSIGNED NOT NULL,
  `quantite_min` int(11) DEFAULT NULL,
  `quantite_max` int(11) DEFAULT NULL,
  `prix` double DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `tarifs_idarticle_foreign` (`idArticle`)
) ENGINE=MyISAM AUTO_INCREMENT=1081 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` timestamp NULL DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code_acces` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `last_activity`, `email_verified_at`, `password`, `code_acces`, `remember_token`, `created_at`, `updated_at`) VALUES
(2, 'admin', 'admin@gmail.com', '2024-05-18 03:45:35', NULL, '$2y$10$N6qALSHCsWSyNqjYwjcSceeKj284OxdVotqYABkmWHbiZTb.QucNm', '$2y$10$LDFLZPM4hTxZNCM1V7SgpeZGnlsOVcIyO9KGmxkX3Ibz0UgH.gfcS', NULL, '2024-05-01 04:00:19', '2024-05-18 03:45:35');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
