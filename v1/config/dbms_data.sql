-- phpMyAdmin SQL Dump
-- version 4.3.11
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Jul 02, 2015 at 11:36 PM
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

--
-- Dumping data for table `place`
--

INSERT INTO `place` (`id`, `name`, `coord`) VALUES
(1, 'Darmstadt Restaurant BÃ¶lle', '\0\0\0\0\0\0\0	·éRíH@\0\0\0àQW!@'),
(2, 'Malchen, GegenÃ¼ber Friedhof', '\0\0\0\0\0\0\0ÍËa÷åH@xî=\\rL!@'),
(3, 'Jugenheim, Vor der Villa Journal', '\0\0\0\0\0\0\0çsîv½àH@‰—§sE!@');

--
-- Dumping data for table `sport`
--

INSERT INTO `sport` (`id`, `sportname`) VALUES
(1, 'Bike'),
(2, 'Run'),
(3, 'Swim'),
(4, 'Multi-Sport'),
(5, 'Ski'),
(6, 'Social');

--
-- Dumping data for table `sport_subtype`
--

INSERT INTO `sport_subtype` (`id`, `fk_sport_id`, `sportsubname`) VALUES
(1, 1, 'MTB'),
(3, 1, 'Rennrad'),
(4, 1, 'Crosser'),
(5, 2, 'Lauf'),
(6, 2, 'Trail-Running'),
(7, 3, 'Schwimmen'),
(8, 4, 'Triathlon'),
(9, 4, 'Duathlon'),
(10, 4, 'Swim & Bike'),
(11, 5, 'Langlauf'),
(12, 6, 'Feier');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
