-- phpMyAdmin SQL Dump
-- version 4.2.12deb2+deb8u2
-- http://www.phpmyadmin.net
--
-- Host: dbserver.shinesoftware.it
-- Generation Time: Ago 05, 2016 alle 09:17
-- Versione del server: 5.7.14
-- PHP Version: 5.6.24-0+deb8u1

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `shine_tango`
--

-- --------------------------------------------------------

--
-- Struttura della tabella `events_restful_credentials`
--

DROP TABLE IF EXISTS `events_restful_credentials`;
CREATE TABLE IF NOT EXISTS `events_restful_credentials` (
`id` int(11) unsigned NOT NULL,
  `user_id` int(11) NOT NULL,
  `secret` varchar(255) NOT NULL DEFAULT '',
  `url` varchar(100) NOT NULL,
  `description` text,
  `email` varchar(100) DEFAULT NULL,
  `insert` tinyint(1) NOT NULL DEFAULT '1',
  `delete` tinyint(1) NOT NULL DEFAULT '1',
  `list` tinyint(1) NOT NULL DEFAULT '1',
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  `createdat` datetime DEFAULT NULL,
  `updatedat` datetime DEFAULT NULL,
  `requests` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `events_restful_credentials`
--
ALTER TABLE `events_restful_credentials`
 ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `events_restful_credentials`
--
ALTER TABLE `events_restful_credentials`
MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;SET FOREIGN_KEY_CHECKS=1;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
