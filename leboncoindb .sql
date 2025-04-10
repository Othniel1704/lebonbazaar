-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : lun. 07 avr. 2025 à 10:22
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `leboncoindb`
--
 CREATE DATABASE IF NOT EXISTS `leboncoindb` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `leboncoindb`;
-- --------------------------------------------------------

--
-- Structure de la table `annonces`
--

CREATE TABLE `annonces` (
  `id` int(11) NOT NULL,
  `titre` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `prix` decimal(10,2) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `categorie_id` int(11) DEFAULT NULL,
  `date_creation` datetime DEFAULT current_timestamp(),
  `etat` enum('Neuf','Occasion') DEFAULT 'Occasion',
  `ville` varchar(100) DEFAULT NULL,
  `code_postal` varchar(10) DEFAULT NULL,
  `click_count` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `annonces`
--

INSERT INTO `annonces` (`id`, `titre`, `description`, `prix`, `user_id`, `categorie_id`, `date_creation`, `etat`, `ville`, `code_postal`, `click_count`) VALUES
(33, 'Appartement T3 en centre-ville', 'Bel appartement spacieux de 70m², très lumineux et agréable, disposant d&#039;un balcon parfait pour profiter de l&#039;extérieur, ainsi que d&#039;un garage pratique pour le stationnement. Il est idéalement situé à proximité des commerces et des transports en commun, facilitant ainsi tous vos déplacements quotidiens.', 210000.00, 11, 1, '2025-03-29 20:54:31', 'Neuf', ' Paris ', NULL, 5),
(36, 'PC Gamer RTX 3080', 'PC assemblé avec un processeur Ryzen 7, doté d&#039;une carte graphique RTX 3080, complété par 32 Go de RAM, et équipé d&#039;un SSD d&#039;une capacité de 1 To.', 1200.00, 11, 5, '2025-03-29 21:33:25', 'Neuf', 'Vincennes ', NULL, 2),
(37, 'MacBook Pro M2', 'Il s&#039;agit d&#039;un MacBook Pro de 16 pouces, équipé d&#039;un puissant processeur M2, et doté de 16 Go de mémoire vive (RAM). Cet ordinateur portable est en excellent état général, prêt à être utilisé pour toutes vos tâches informatiques, qu&#039;il s&#039;agisse de création de contenu, de développement logiciel ou simplement de navigation sur le web. Grâce à ses caractéristiques techniques, notamment son processeur M2 et ses 16 Go de RAM, il offre une performance remarquable et une grande fluidité d&#039;utilisation.', 1800.00, 22, 5, '2025-03-29 22:33:33', 'Neuf', 'PARIS ', NULL, 5),
(38, 'Vélo de route Bianchi', 'Un vélo en carbone ultra léger équipé du groupe Shimano Ultegra est un choix populaire pour les cyclistes recherchant performance et légèreté. Le cadre en carbone offre une excellente rigidité et un poids réduit, améliorant l&#039;efficacité du pédalage et la maniabilité.', 1500.00, 22, 2, '2025-03-29 22:39:12', 'Neuf', 'BEZONS', NULL, 4),
(39, 'Console PS5 avec jeux', 'PS5 avec 2 manettes + FIFA 24 et Spider-Man 2.', 540.00, 22, 4, '2025-03-29 22:43:48', 'Occasion', 'Cergy', NULL, 3),
(40, 'Montre Rolex Submariner', 'Montre automatique Rolex Submariner 2020, excellent état.', 6000.01, 11, 3, '2025-03-29 22:51:54', 'Neuf', ' Paris ', NULL, 3),
(41, 'Sac Louis Vuitton original', 'Sac à main Louis Vuitton en cuir de haute qualité, modèle emblématique Neverfull, qui allie élégance et fonctionnalité.', 500.00, 23, 3, '2025-03-29 22:57:21', 'Neuf', 'Lyon', NULL, 5),
(42, 'Cours de guitare à domicile', 'Musiciens expérimentés proposent des cours de guitare afin d&#039;aider les élèves à améliorer leurs compétences et à apprendre à jouer de cet instrument.\r\n', 25.00, 23, 6, '2025-03-29 23:09:16', 'Neuf', 'Paris', NULL, 3),
(43, 'Apple Watch Ultra + 3 bracelets - Accessoires téléphone &amp; Objets connectés', 'Apple Watch Ultra est compatible avec différents types de bracelets, permettant une personnalisation pour le style ou l&#039;activité\r\nBracelets en silicone : idéaux pour le sport et les activités en extérieur.\r\nBracelets en cuir : plus élégants, pour des occasions formelles.\r\nBracelets en nylon : légers et confortables pour un usage quotidien.\r\nAccessoires', 300.00, 23, 3, '2025-04-01 09:47:55', 'Occasion', 'Lyon', NULL, 0);

-- --------------------------------------------------------

--
-- Structure de la table `annonce_visite`
--

CREATE TABLE `annonce_visite` (
  `user_id` int(11) NOT NULL,
  `annonce_id` int(11) NOT NULL,
  `visite_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `annonce_visite`
--

INSERT INTO `annonce_visite` (`user_id`, `annonce_id`, `visite_date`) VALUES
(22, 33, '2025-03-31 20:29:11'),
(22, 37, '2025-03-31 19:28:15'),
(22, 38, '2025-03-31 20:30:39'),
(22, 39, '2025-03-31 20:10:48'),
(22, 41, '2025-03-31 20:30:44'),
(22, 42, '2025-03-31 20:25:34'),
(23, 33, '2025-04-01 10:21:46'),
(23, 36, '2025-04-01 10:20:39'),
(23, 37, '2025-03-31 18:16:17'),
(23, 38, '2025-04-01 07:34:30'),
(23, 39, '2025-04-01 07:35:05'),
(23, 40, '2025-04-04 09:55:38'),
(23, 41, '2025-04-01 10:19:12'),
(23, 42, '2025-04-04 09:39:57'),
(23, 43, '2025-04-04 11:43:12'),
(24, 33, '2025-04-07 07:47:54'),
(24, 40, '2025-04-07 07:47:27');

-- --------------------------------------------------------

--
-- Structure de la table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `categories`
--

INSERT INTO `categories` (`id`, `nom`) VALUES
(1, 'Immobilier'),
(5, 'informatiques'),
(4, 'Loisirs'),
(3, 'Mode'),
(6, 'Services'),
(2, 'Véhicules');

-- --------------------------------------------------------

--
-- Structure de la table `favoris`
--

CREATE TABLE `favoris` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `annonce_id` int(11) DEFAULT NULL,
  `date_ajout` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `favoris`
--

INSERT INTO `favoris` (`id`, `user_id`, `annonce_id`, `date_ajout`) VALUES
(84, 23, 39, '2025-03-30 01:24:08'),
(85, 22, 42, '2025-03-30 03:08:56'),
(86, 23, 40, '2025-04-04 11:55:40');

-- --------------------------------------------------------

--
-- Structure de la table `images`
--

CREATE TABLE `images` (
  `id` int(11) NOT NULL,
  `annonce_id` int(11) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT 'default_a.jpg'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `images`
--

INSERT INTO `images` (`id`, `annonce_id`, `image_path`) VALUES
(30, 33, '../image/67e857287baa0_20250329_212512.png'),
(31, 33, '../image/67e857287bef3_20250329_212512.png'),
(32, 33, '../image/67e857287c19e_20250329_212512.png'),
(33, 33, '../image/67e857287c72b_20250329_212512.png'),
(34, 33, '../image/67e857287c9b7_20250329_212512.png'),
(36, 37, '../image/67e8672d9e886_20250329_223333.jpg'),
(37, 37, '../image/67e8672d9f129_20250329_223333.jpg'),
(38, 38, '../image/67e86880cc7db_20250329_223912.jpg'),
(39, 39, '../image/67e869947cb1f_20250329_224348.jpg'),
(40, 39, '../image/67e869947ce57_20250329_224348.jpg'),
(41, 39, '../image/67e869947d1ce_20250329_224348.jpg'),
(42, 36, '../image/67e86a25defd5_20250329_224613.jpg'),
(43, 40, '../image/67e86b7a8b82d_20250329_225154.png'),
(44, 41, '../image/67e86e762b6a6_20250329_230438.jpg'),
(45, 42, '../image/67e86f8cbf1ca_20250329_230916.jpg'),
(46, 43, '../image/67eb9a2bd8f03_20250401_094755.jpg');

-- --------------------------------------------------------

--
-- Structure de la table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `expediteur_id` int(11) DEFAULT NULL,
  `destinataire_id` int(11) DEFAULT NULL,
  `annonce_id` int(11) DEFAULT NULL,
  `contenu` text NOT NULL,
  `date_envoi` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `messages`
--

INSERT INTO `messages` (`id`, `expediteur_id`, `destinataire_id`, `annonce_id`, `contenu`, `date_envoi`) VALUES
(73, 23, 22, 37, 'bonjour je suis intéressé ', '2025-03-30 01:49:41'),
(74, 22, 22, 38, 'bonjour', '2025-03-30 03:08:14'),
(75, 11, 11, 38, 'je suis intéressé par le vélo ', '2025-03-30 03:19:32'),
(76, 22, 23, 37, 'd&#039;accord tu vis où du coup pour la livraison  ?', '2025-03-31 21:40:48'),
(77, 22, 22, 42, 'bonjour\r\n', '2025-03-31 22:11:17'),
(78, 22, 23, 41, 'salut', '2025-03-31 22:31:03'),
(79, 23, 22, 37, 'je vis a Paris dans le 13eme', '2025-03-31 22:33:19'),
(80, 23, 23, 40, 'bonjour ', '2025-04-01 01:21:32'),
(81, 23, 23, 40, 'ok', '2025-04-01 01:31:03'),
(82, 23, 23, 40, 'ok', '2025-04-01 01:33:14'),
(83, 23, 23, 40, 'ok', '2025-04-01 01:33:23'),
(84, 23, 23, 40, 'tiens ', '2025-04-01 01:43:09'),
(85, 23, 23, 41, 'bonjour\r\n', '2025-04-01 12:19:38'),
(86, 23, 23, 36, 'bonjour\r\n', '2025-04-01 12:21:04'),
(87, 23, 23, 43, 'DRJE0RJEJRE09RJE09JR', '2025-04-04 11:56:53'),
(88, 23, 23, 43, 'hbnyn', '2025-04-04 13:44:32'),
(89, 23, 22, 41, 'salut', '2025-04-04 13:45:46');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `mdp` varchar(255) NOT NULL,
  `nom` varchar(255) DEFAULT NULL,
  `prenom` varchar(255) DEFAULT NULL,
  `tel` varchar(20) DEFAULT NULL,
  `adresse` varchar(255) DEFAULT NULL,
  `date_inscription` datetime DEFAULT current_timestamp(),
  `sexe` enum('Masculin','Féminin','Autre') DEFAULT NULL,
  `date_naissance` date DEFAULT NULL,
  `ville` varchar(100) DEFAULT NULL,
  `profession` varchar(100) DEFAULT NULL,
  `photo` varchar(255) DEFAULT 'default.png',
  `rôle` int(255) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `email`, `mdp`, `nom`, `prenom`, `tel`, `adresse`, `date_inscription`, `sexe`, `date_naissance`, `ville`, `profession`, `photo`, `rôle`) VALUES
(11, 'utilisateur1@gmail.com', '$2y$10$6jIsinw/4Hn4SCwlEHNSHulBzx0G/Zlbg5dC4vVwuMIq1Yo.f0sXS', 'Leroi', 'Paul', '0000000022', '38 avenue le beau', '2025-02-27 14:42:08', 'Masculin', '2001-05-16', 'Paris', 'vendeur', 'default.png', 1),
(22, 'tyty@gmail.com', '$2y$10$y2i5kZbBlzPkG/ixBSwuTuQGBSKnND.nlPLRRqXOyMndRCgAnrjJO', 'kouakou', 'othy', '7894561235', '38 Avenue Gabriel Peri', '2025-03-02 20:14:07', 'Masculin', '0000-00-00', 'BEZONS', 'vendeur ', 'default.png', 1),
(23, 'utilisateur3@gmail.com', '$2y$10$Zx5N8YuIDbDmkp.2Bb2G2OoMuMSwmc3Y4hjLUFQN/UEwclD06PE0y', 'Arnold ', 'Jack', '0102030405', '06 RUE LOUISE DE VILMORIN', '2025-03-28 11:25:23', 'Masculin', '1995-06-08', 'MARSEILLE ', 'vendeur', 'user_23_1743755343.jpeg', 1),
(24, 'admin@gmail.com', '$2y$10$QiucCLKz06avPgXR9sxmie8XHRjNSSURp.xBqZ2db1txR9Yottbcy', NULL, 'admin', '0102030406', NULL, '2025-04-04 11:24:11', NULL, NULL, NULL, NULL, 'default.png', 2);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `annonces`
--
ALTER TABLE `annonces`
  ADD PRIMARY KEY (`id`),
  ADD KEY `categorie_id` (`categorie_id`),
  ADD KEY `user_id` (`user_id`) USING BTREE;

--
-- Index pour la table `annonce_visite`
--
ALTER TABLE `annonce_visite`
  ADD PRIMARY KEY (`user_id`,`annonce_id`),
  ADD KEY `idx_annonce_id` (`annonce_id`);

--
-- Index pour la table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nom` (`nom`);

--
-- Index pour la table `favoris`
--
ALTER TABLE `favoris`
  ADD PRIMARY KEY (`id`),
  ADD KEY `annonce_id` (`annonce_id`),
  ADD KEY `user_id` (`user_id`) USING BTREE;

--
-- Index pour la table `images`
--
ALTER TABLE `images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `images_ibfk_1` (`annonce_id`);

--
-- Index pour la table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `annonce_id` (`annonce_id`),
  ADD KEY `fk_expediteur` (`expediteur_id`),
  ADD KEY `fk_destinataire` (`destinataire_id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `tel` (`tel`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `annonces`
--
ALTER TABLE `annonces`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT pour la table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT pour la table `favoris`
--
ALTER TABLE `favoris`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=87;

--
-- AUTO_INCREMENT pour la table `images`
--
ALTER TABLE `images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT pour la table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=90;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `annonces`
--
ALTER TABLE `annonces`
  ADD CONSTRAINT `annonces_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `annonces_ibfk_2` FOREIGN KEY (`categorie_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Contraintes pour la table `annonce_visite`
--
ALTER TABLE `annonce_visite`
  ADD CONSTRAINT `annonce_visite_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `annonce_visite_ibfk_2` FOREIGN KEY (`annonce_id`) REFERENCES `annonces` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `favoris`
--
ALTER TABLE `favoris`
  ADD CONSTRAINT `favoris_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `favoris_ibfk_2` FOREIGN KEY (`annonce_id`) REFERENCES `annonces` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `images`
--
ALTER TABLE `images`
  ADD CONSTRAINT `images_ibfk_1` FOREIGN KEY (`annonce_id`) REFERENCES `annonces` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `fk_destinataire` FOREIGN KEY (`destinataire_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `fk_expediteur` FOREIGN KEY (`expediteur_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`expediteur_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`destinataire_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `messages_ibfk_3` FOREIGN KEY (`annonce_id`) REFERENCES `annonces` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
