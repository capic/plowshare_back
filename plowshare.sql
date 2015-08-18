-- phpMyAdmin SQL Dump
-- version 3.4.11.1deb2+deb7u1
-- http://www.phpmyadmin.net
--
-- Client: localhost
-- Généré le: Mar 18 Août 2015 à 21:36
-- Version du serveur: 5.5.44
-- Version de PHP: 5.4.41-0+deb7u1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données: `plowshare`
--

-- --------------------------------------------------------

--
-- Structure de la table `download`
--

CREATE TABLE IF NOT EXISTS `download` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(256) NOT NULL,
  `package` varchar(255) NOT NULL,
  `link` varchar(512) NOT NULL,
  `size_file` int(11) NOT NULL,
  `size_part` int(11) NOT NULL,
  `size_file_downloaded` int(11) NOT NULL,
  `size_part_downloaded` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `progress_part` int(3) NOT NULL,
  `average_speed` int(11) NOT NULL,
  `current_speed` int(11) NOT NULL,
  `time_spent` int(11) NOT NULL,
  `time_left` int(11) NOT NULL,
  `pid_plowdown` int(11) NOT NULL,
  `pid_curl` int(11) NOT NULL,
  `pid_python` int(11) NOT NULL,
  `file_path` varchar(2048) NOT NULL,
  `priority` smallint(1) NOT NULL DEFAULT '0',
  `infos_plowdown` longtext NOT NULL,
  `theorical_start_datetime` datetime NOT NULL,
  `lifecycle_insert_date` datetime NOT NULL,
  `lifecycle_update_date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=4911 ;

-- --------------------------------------------------------

--
-- Structure de la table `download_status`
--

CREATE TABLE IF NOT EXISTS `download_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `ord` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

--
-- Contenu de la table `download_status`
--

INSERT INTO `download_status` (`id`, `name`, `ord`) VALUES
(1, 'waiting', 1),
(2, 'in progress', 2),
(3, 'finished', 3),
(4, 'error', 4),
(5, 'pause', 5),
(6, 'cancel', 6),
(7, 'undefined', 7),
(8, 'starting', 8);

-- --------------------------------------------------------

--
-- Structure de la table `link`
--

CREATE TABLE IF NOT EXISTS `link` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(256) NOT NULL,
  `link` varchar(512) NOT NULL,
  `size` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1374 ;

-- --------------------------------------------------------

--
-- Structure de la table `link_status`
--

CREATE TABLE IF NOT EXISTS `link_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `ord` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

--
-- Contenu de la table `link_status`
--

INSERT INTO `link_status` (`id`, `name`, `ord`) VALUES
(1, 'on line', 1),
(2, 'deleted', 2);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
