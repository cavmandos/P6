-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost
-- Généré le : jeu. 19 oct. 2023 à 14:16
-- Version du serveur : 10.4.28-MariaDB
-- Version de PHP : 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `p6`
--

-- --------------------------------------------------------

--
-- Structure de la table `discussion`
--

CREATE TABLE `discussion` (
  `id` int(11) NOT NULL,
  `creation_date` datetime NOT NULL,
  `userId` int(11) NOT NULL,
  `content` varchar(255) NOT NULL,
  `trickId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `discussion`
--

INSERT INTO `discussion` (`id`, `creation_date`, `userId`, `content`, `trickId`) VALUES
(40, '2023-10-19 12:07:45', 47, 'Une figure qui fait bien flipper !', 109),
(41, '2023-10-19 12:09:43', 47, 'Il faut pas oublier le demi-tour en sortie', 116),
(42, '2023-10-19 12:10:42', 48, 'Sinon c\'est carpette 😂', 116),
(43, '2023-10-19 12:13:28', 48, 'Ceci', 112),
(44, '2023-10-19 12:13:34', 48, 'est', 112),
(45, '2023-10-19 12:13:43', 48, 'un', 112),
(46, '2023-10-19 12:13:48', 48, 'test', 112),
(47, '2023-10-19 12:13:58', 48, 'pour', 112),
(48, '2023-10-19 12:14:04', 48, 'vérifier', 112),
(49, '2023-10-19 12:14:16', 48, 'la', 112),
(50, '2023-10-19 12:14:21', 48, 'bonne', 112),
(51, '2023-10-19 12:14:28', 48, 'marche', 112),
(52, '2023-10-19 12:14:31', 48, 'de', 112),
(53, '2023-10-19 12:14:47', 48, 'la', 112),
(54, '2023-10-19 12:14:52', 48, 'pagination', 112);

-- --------------------------------------------------------

--
-- Structure de la table `doctrine_migration_versions`
--

CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20230711112146', '2023-07-11 11:22:36', 36),
('DoctrineMigrations\\Version20230731195653', '2023-07-31 19:57:31', 44),
('DoctrineMigrations\\Version20230810185656', '2023-08-10 18:57:23', 19),
('DoctrineMigrations\\Version20230906073357', '2023-09-06 07:34:18', 53),
('DoctrineMigrations\\Version20230930120713', '2023-09-30 12:07:29', 82),
('DoctrineMigrations\\Version20230930121730', '2023-09-30 12:17:34', 35),
('DoctrineMigrations\\Version20231001145244', '2023-10-01 14:52:51', 42),
('DoctrineMigrations\\Version20231003091109', '2023-10-03 09:11:17', 32);

-- --------------------------------------------------------

--
-- Structure de la table `group`
--

CREATE TABLE `group` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `group`
--

INSERT INTO `group` (`id`, `name`) VALUES
(1, 'Grabs'),
(2, 'Flips'),
(3, 'Rails et Jibs'),
(4, 'Spins');

-- --------------------------------------------------------

--
-- Structure de la table `media`
--

CREATE TABLE `media` (
  `id` int(11) NOT NULL,
  `type` varchar(255) NOT NULL,
  `url` text NOT NULL,
  `trick_id_id` int(11) DEFAULT NULL,
  `banner` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `media`
--

INSERT INTO `media` (`id`, `type`, `url`, `trick_id_id`, `banner`) VALUES
(103, 'image', '159b01753a23be6bf99b28589545f93d.jpg', 107, 1),
(104, 'image', '5afb76985368599c3c7d6ab16a4658a4.webp', 108, 1),
(105, 'image', 'd4a2f1f7ffdadb713163eb3671ca2b08.jpg', 109, 1),
(106, 'video', '<iframe width=\"560\" height=\"315\" src=\"https://www.youtube.com/embed/5bpzng08nzk?si=jBWF8GUZSFA3ihPB\" title=\"YouTube video player\" frameborder=\"0\" allow=\"accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share\" allowfullscreen></iframe>', 109, 0),
(107, 'image', '7fe9e6551ed1c71b4982e2b1b291fda1.jpg', 110, 1),
(108, 'video', '<iframe width=\"560\" height=\"315\" src=\"https://www.youtube.com/embed/xhvqu2XBvI0?si=WUxjP_L3RTaW2jRd\" title=\"YouTube video player\" frameborder=\"0\" allow=\"accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share\" allowfullscreen></iframe>', 110, 0),
(109, 'image', 'd2c7ccf7352592ea5fef9cb8995e8bb7.jpg', 111, 1),
(110, 'video', '<iframe width=\"560\" height=\"315\" src=\"https://www.youtube.com/embed/R3OG9rNDIcs?si=BsZ7yl4P1FEflJtC\" title=\"YouTube video player\" frameborder=\"0\" allow=\"accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share\" allowfullscreen></iframe>', 111, 0),
(111, 'image', '10167b27ccc2985d615bf98c2091d41e.webp', 112, 1),
(112, 'image', 'fbe2e47d2205604a9752ab75eafc9d7c.webp', 113, 1),
(113, 'video', '<iframe width=\"560\" height=\"315\" src=\"https://www.youtube.com/embed/GS9MMT_bNn8?si=DN4C43xTSnqVrP14\" title=\"YouTube video player\" frameborder=\"0\" allow=\"accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share\" allowfullscreen></iframe>', 113, 0),
(114, 'image', '05304f51aba63203fe6fe90cc4b15cfe.jpg', 114, 1),
(115, 'video', '<iframe width=\"560\" height=\"315\" src=\"https://www.youtube.com/embed/ZWZGE9yp5hA?si=ltfXIHfmHXaXwmB1\" title=\"YouTube video player\" frameborder=\"0\" allow=\"accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share\" allowfullscreen></iframe>', 114, 0),
(116, 'video', '<iframe width=\"560\" height=\"315\" src=\"https://www.youtube.com/embed/DHWlxQ90ZCI?si=OMrF-Hc4Vtgk2B0m\" title=\"YouTube video player\" frameborder=\"0\" allow=\"accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share\" allowfullscreen></iframe>', 115, 0),
(117, 'image', '210253cd3b753d17b850865fd395d28f.webp', 116, 1),
(118, 'video', '<iframe width=\"560\" height=\"315\" src=\"https://www.youtube.com/embed/LSVn5aI56aU?si=O-wY0s3nqrMVl9kH\" title=\"YouTube video player\" frameborder=\"0\" allow=\"accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share\" allowfullscreen></iframe>', 116, 0),
(119, 'video', '<iframe width=\"560\" height=\"315\" src=\"https://www.youtube.com/embed/4xz1DguKX9M?si=uwu8vq-guXcH9qu0\" title=\"YouTube video player\" frameborder=\"0\" allow=\"accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share\" allowfullscreen></iframe>', 116, 0),
(120, 'image', '25e5d9dd0334c0c5342b23eb4b32ae3e.webp', 116, 0),
(121, 'image', '1cbe88848d82a18112ef38ad69b4969e.jpg', 107, 0);

-- --------------------------------------------------------

--
-- Structure de la table `messenger_messages`
--

CREATE TABLE `messenger_messages` (
  `id` bigint(20) NOT NULL,
  `body` longtext NOT NULL,
  `headers` longtext NOT NULL,
  `queue_name` varchar(190) NOT NULL,
  `created_at` datetime NOT NULL,
  `available_at` datetime NOT NULL,
  `delivered_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `trick`
--

CREATE TABLE `trick` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `last_update` datetime NOT NULL,
  `group_id_id` int(11) DEFAULT NULL,
  `published` datetime NOT NULL,
  `user_id_id` int(11) NOT NULL,
  `slug` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `trick`
--

INSERT INTO `trick` (`id`, `name`, `description`, `last_update`, `group_id_id`, `published`, `user_id_id`, `slug`) VALUES
(107, 'Indy Grab', 'Saisis la carre de la planche du côté de ta main avant (main avant sur la carre frontside, main arrière sur la carre backside) tout en étendant ta jambe arrière. C\'est l\'un des grabs les plus classiques et élégants.', '2023-10-19 07:36:19', 1, '2023-10-18 19:50:27', 47, 'indy-grab'),
(108, 'Tail Grab', 'Saisis le bout de la planche avec ta main arrière tout en étirant ta jambe avant. Cette saisie ajoute style et équilibre à tes sauts.', '2023-10-18 19:53:19', 1, '2023-10-18 19:53:19', 47, 'tail-grab'),
(109, 'Backflip', 'Effectue une rotation arrière complète en l\'air, en te propulsant de manière à ce que ton dos fasse le tour complet. C\'est une figure impressionnante qui demande une bonne maîtrise de l\'équilibre.', '2023-10-18 19:57:24', 2, '2023-10-18 19:57:24', 48, 'backflip'),
(110, 'Frontflip', 'Réalise une rotation vers l\'avant en faisant un tour complet. Garde ton corps compact pour contrôler la rotation et atterrir en douceur.', '2023-10-18 20:01:17', 2, '2023-10-18 20:01:17', 48, 'frontflip'),
(111, 'Boardslide', 'Glisse le long du rail en plaçant ta planche perpendiculairement à celui-ci, en laissant la base frotter sur le rail. Utilise tes épaules pour diriger et maintenir ton équilibre.', '2023-10-18 20:04:59', 3, '2023-10-18 20:04:59', 48, 'boardslide'),
(112, '50-50', 'Équilibre-toi sur le rail en faisant glisser la partie centrale de ta planche sur la surface. Tes pieds doivent être placés symétriquement de chaque côté du rail. Utilise tes bras pour maintenir l\'équilibre.', '2023-10-19 07:39:17', 3, '2023-10-18 20:10:30', 47, '50-50'),
(113, '360', 'Tourne sur toi-même en effectuant une rotation complète de 360 degrés dans les airs. Pour commencer, choisis une direction (frontside ou backside) et entraîne-toi à maîtriser la rotation.', '2023-10-18 20:17:43', 4, '2023-10-18 20:17:43', 47, '360'),
(114, 'Method Grab', 'Saisis le côté de la planche entre tes fixations avec ta main arrière, en étirant ton corps pour exposer ton dos à la pente. La position aérienne distincte de cette saisie en fait l\'une des plus esthétiques.', '2023-10-19 07:12:37', 1, '2023-10-19 07:12:37', 48, 'method-grab'),
(115, 'Misty Flip', 'Effectue une rotation arrière tout en inclinant légèrement ton épaule avant vers le bas pour donner une torsion à ton flip. Cela crée un style unique et élégant dans les airs.', '2023-10-19 07:15:55', 2, '2023-10-19 07:15:55', 48, 'misty-flip'),
(116, 'Lipslide', 'Glisse le long du rail en plaçant ta planche perpendiculairement à celui-ci, mais cette fois-ci en faisant glisser la partie avant de ta planche sur la surface du rail. Assure-toi de maintenir ton équilibre en t\'appuyant sur le côté du rail.', '2023-10-19 07:23:15', 3, '2023-10-19 07:19:57', 48, 'lipslide');

-- --------------------------------------------------------

--
-- Structure de la table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `token` varchar(255) DEFAULT NULL,
  `is_verified` tinyint(1) NOT NULL,
  `roles` longtext NOT NULL COMMENT '(DC2Type:json)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `user`
--

INSERT INTO `user` (`id`, `username`, `email`, `password`, `avatar`, `token`, `is_verified`, `roles`) VALUES
(47, 'Rider', 'rider@test.com', '$2y$13$N1DfuBtvPLxoXgCqArenL.AuYeu/gfnrNFs3MG3bbWGNYIPbSiGUq', '/pictures/avatar.png', NULL, 1, '[]'),
(48, 'Xtream56', 'xadresse@mail.com', '$2y$13$1h4Z2O5kNwiOmxp4otyIYOpzabBPCzzIl6FEVvjdqcToQHSm4m4N6', '/pictures/avatar.png', NULL, 1, '[]');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `discussion`
--
ALTER TABLE `discussion`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_C0B9F90F64B64DCC` (`userId`),
  ADD KEY `IDX_C0B9F90F51F6BF91` (`trickId`);

--
-- Index pour la table `doctrine_migration_versions`
--
ALTER TABLE `doctrine_migration_versions`
  ADD PRIMARY KEY (`version`);

--
-- Index pour la table `group`
--
ALTER TABLE `group`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `media`
--
ALTER TABLE `media`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_6A2CA10CB46B9EE8` (`trick_id_id`);

--
-- Index pour la table `messenger_messages`
--
ALTER TABLE `messenger_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_75EA56E0FB7336F0` (`queue_name`),
  ADD KEY `IDX_75EA56E0E3BD61CE` (`available_at`),
  ADD KEY `IDX_75EA56E016BA31DB` (`delivered_at`);

--
-- Index pour la table `trick`
--
ALTER TABLE `trick`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_D8F0A91E5E237E06` (`name`),
  ADD KEY `IDX_D8F0A91E2F68B530` (`group_id_id`),
  ADD KEY `IDX_D8F0A91E9D86650F` (`user_id_id`);

--
-- Index pour la table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_8D93D649F85E0677` (`username`),
  ADD UNIQUE KEY `UNIQ_8D93D649E7927C74` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `discussion`
--
ALTER TABLE `discussion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT pour la table `group`
--
ALTER TABLE `group`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `media`
--
ALTER TABLE `media`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=122;

--
-- AUTO_INCREMENT pour la table `messenger_messages`
--
ALTER TABLE `messenger_messages`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `trick`
--
ALTER TABLE `trick`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=117;

--
-- AUTO_INCREMENT pour la table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `discussion`
--
ALTER TABLE `discussion`
  ADD CONSTRAINT `FK_C0B9F90F51F6BF91` FOREIGN KEY (`trickId`) REFERENCES `trick` (`id`),
  ADD CONSTRAINT `FK_C0B9F90F64B64DCC` FOREIGN KEY (`userId`) REFERENCES `user` (`id`);

--
-- Contraintes pour la table `media`
--
ALTER TABLE `media`
  ADD CONSTRAINT `FK_6A2CA10CB46B9EE8` FOREIGN KEY (`trick_id_id`) REFERENCES `trick` (`id`);

--
-- Contraintes pour la table `trick`
--
ALTER TABLE `trick`
  ADD CONSTRAINT `FK_D8F0A91E2F68B530` FOREIGN KEY (`group_id_id`) REFERENCES `group` (`id`),
  ADD CONSTRAINT `FK_D8F0A91E9D86650F` FOREIGN KEY (`user_id_id`) REFERENCES `user` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
