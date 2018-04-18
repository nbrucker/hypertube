-- phpMyAdmin SQL Dump
-- version 4.7.3
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:8889
-- Généré le :	sam. 24 mars 2018 à 15:38
-- Version du serveur :	5.6.35
-- Version de PHP :  7.1.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Base de données :  `hypertube`
--

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
	`id_user` int(11) NOT NULL,
	`email` varchar(255) DEFAULT NULL,
	`login` varchar(255) DEFAULT NULL,
	`passwd` varchar(300) DEFAULT NULL,
	`last_name` text,
	`first_name` text,
	`confirm` int(11) DEFAULT NULL,
	`cle` text NOT NULL,
	`cle_passwd` text,
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `comments` (
	`id_comments` int(11) NOT NULL,
	`user_id` int(11) NOT NULL,
	`movie_id` varchar(255) DEFAULT NULL,
	`content` varchar(3000) DEFAULT NULL,
	`created_at` varchar(255) DEFAULT NULL,
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id_user`, `email`, `login`, `passwd`, `last_name`, `first_name`, `confirm`, `cle`, `cle_passwd`) VALUES
(1, 'ncella98@gmail.com', 'ncella98', '8d1e214d80c712762ba521bd6a097571a31f822bf63ffd8c1cbafb8ec3e85858fcca65679b7f9f90439bac34fe0b02f7f459465220632671fe3e1a2d6999e9ff', 'CELLA', 'Nicolas', 1, '6c2758a35aea4f74f75037db56942454', '785997b97e428a660f288bff27c14422'),
(4, 'cella.nicolas@hotmail.com', 'naos', '8d1e214d80c712762ba521bd6a097571a31f822bf63ffd8c1cbafb8ec3e85858fcca65679b7f9f90439bac34fe0b02f7f459465220632671fe3e1a2d6999e9ff', 'ncella98', 'ncella98', 0, 'dd5387a3514687a20cafb15502b52694', NULL);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `users`
--
ALTER TABLE `users`
	ADD PRIMARY KEY (`id_user`);

ALTER TABLE `comments`
	ADD PRIMARY KEY (`id_comments`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
	MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

ALTER TABLE `comments`
	MODIFY `id_comments` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
