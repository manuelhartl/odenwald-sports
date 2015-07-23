-- phpMyAdmin SQL Dump
-- version 4.3.11
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Jul 13, 2015 at 06:17 PM
-- Server version: 5.6.24
-- PHP Version: 5.6.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `usr_ud01_314_2`
--

-- --------------------------------------------------------

--
-- Table structure for table `place`
--

CREATE TABLE IF NOT EXISTS `place` (
  `id` tinyint(4) NOT NULL,
  `name` varchar(200) COLLATE utf8_bin NOT NULL,
  `coord` point NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `sport`
--

CREATE TABLE IF NOT EXISTS `sport` (
  `id` mediumint(9) NOT NULL,
  `sportname` varchar(100) COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `sport_subtype`
--

CREATE TABLE IF NOT EXISTS `sport_subtype` (
  `id` mediumint(9) NOT NULL,
  `fk_sport_id` mediumint(9) NOT NULL,
  `sportsubname` varchar(50) COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `token`
--

CREATE TABLE IF NOT EXISTS `token` (
  `fk_user_id` mediumint(9) NOT NULL,
  `token` varchar(32) COLLATE utf8_bin NOT NULL,
  `action` enum('emailverification','passwordreset') COLLATE utf8_bin NOT NULL,
  `adddate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `tour`
--

CREATE TABLE IF NOT EXISTS `tour` (
  `id` mediumint(9) NOT NULL,
  `fk_guide_id` mediumint(9) NOT NULL,
  `fk_sport_subtype_id` mediumint(9) NOT NULL,
  `startdate` datetime NOT NULL,
  `duration` smallint(6) NOT NULL,
  `meetingpoint` varchar(250) COLLATE utf8_bin NOT NULL,
  `meetingpoint_desc` varchar(100) COLLATE utf8_bin NOT NULL,
  `meetingpoint_coord` point NOT NULL,
  `description` text COLLATE utf8_bin NOT NULL,
  `adddate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modifydate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('active','canceled') COLLATE utf8_bin NOT NULL DEFAULT 'active',
  `skill` enum('1','2','3','4','5','6') COLLATE utf8_bin NOT NULL,
  `speed` enum('1','2','3','4','5','6') COLLATE utf8_bin NOT NULL,
  `distance` int(11) NOT NULL,
  `elevation` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=109 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `tour_attendee`
--

CREATE TABLE IF NOT EXISTS `tour_attendee` (
  `fk_tour_id` mediumint(9) NOT NULL,
  `fk_user_id` mediumint(9) NOT NULL,
  `adddate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` mediumint(9) NOT NULL,
  `username` varchar(30) COLLATE utf8_bin NOT NULL,
  `hashedpassword` varchar(100) COLLATE utf8_bin NOT NULL,
  `email` varchar(200) COLLATE utf8_bin NOT NULL,
  `status` enum('registered','verified') COLLATE utf8_bin NOT NULL,
  `register_date` datetime NOT NULL,
  `modifydate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

-- --------------------------------------------------------

--
-- Table structure for table `user_extra`
--

CREATE TABLE IF NOT EXISTS `user_extra` (
  `fk_user_id` mediumint(6) NOT NULL,
  `realname` varchar(200) COLLATE utf8_bin DEFAULT NULL,
  `birthdate` datetime DEFAULT NULL,
  `address` varchar(200) COLLATE utf8_bin DEFAULT NULL,
  `address_coord` point DEFAULT NULL,
  `mailing` tinyint(1) NOT NULL DEFAULT '1',
  `phone` varchar(50) COLLATE utf8_bin DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `place`
--
ALTER TABLE `place`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sport`
--
ALTER TABLE `sport`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sport_subtype`
--
ALTER TABLE `sport_subtype`
  ADD PRIMARY KEY (`id`), ADD KEY `fk_sport_id` (`fk_sport_id`);

--
-- Indexes for table `token`
--
ALTER TABLE `token`
  ADD PRIMARY KEY (`fk_user_id`,`token`);

--
-- Indexes for table `tour`
--
ALTER TABLE `tour`
  ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `fk_guide_id_2` (`fk_guide_id`,`startdate`), ADD KEY `fk_guide_id` (`fk_guide_id`), ADD KEY `fk_sport_subtype_id` (`fk_sport_subtype_id`);

--
-- Indexes for table `tour_attendee`
--
ALTER TABLE `tour_attendee`
  ADD PRIMARY KEY (`fk_tour_id`,`fk_user_id`), ADD KEY `tour_attendee_ibfk_3` (`fk_user_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `username` (`username`), ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_extra`
--
ALTER TABLE `user_extra`
  ADD PRIMARY KEY (`fk_user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `place`
--
ALTER TABLE `place`
  MODIFY `id` tinyint(4) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `sport`
--
ALTER TABLE `sport`
  MODIFY `id` mediumint(9) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT for table `sport_subtype`
--
ALTER TABLE `sport_subtype`
  MODIFY `id` mediumint(9) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=12;
--
-- AUTO_INCREMENT for table `tour`
--
ALTER TABLE `tour`
  MODIFY `id` mediumint(9) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=109;
--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` mediumint(9) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=20;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `sport_subtype`
--
ALTER TABLE `sport_subtype`
ADD CONSTRAINT `fk_sport_id` FOREIGN KEY (`fk_sport_id`) REFERENCES `sport` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `token`
--
ALTER TABLE `token`
ADD CONSTRAINT `token_ibfk_1` FOREIGN KEY (`fk_user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tour`
--
ALTER TABLE `tour`
ADD CONSTRAINT `fk_guide_id` FOREIGN KEY (`fk_guide_id`) REFERENCES `user` (`id`),
ADD CONSTRAINT `fk_sport_subtype_id` FOREIGN KEY (`fk_sport_subtype_id`) REFERENCES `sport_subtype` (`id`);

--
-- Constraints for table `tour_attendee`
--
ALTER TABLE `tour_attendee`
ADD CONSTRAINT `tour_attendee_ibfk_2` FOREIGN KEY (`fk_tour_id`) REFERENCES `tour` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
ADD CONSTRAINT `tour_attendee_ibfk_3` FOREIGN KEY (`fk_user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user_extra`
--
ALTER TABLE `user_extra`
ADD CONSTRAINT `user_extra_ibfk_1` FOREIGN KEY (`fk_user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
