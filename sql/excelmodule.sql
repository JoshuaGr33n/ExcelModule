-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jun 10, 2022 at 01:53 PM
-- Server version: 5.7.26
-- PHP Version: 7.3.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `testing`
--

-- --------------------------------------------------------

--
-- Table structure for table `excelmodule`
--

DROP TABLE IF EXISTS `excelmodule`;
CREATE TABLE IF NOT EXISTS `excelmodule` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `items` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `amount` double DEFAULT NULL,
  `premium` double NOT NULL,
  `certificate_no` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `policy_no` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `certificate_type` varchar(255) DEFAULT NULL,
  `location_from` varchar(255) DEFAULT NULL,
  `location_to` varchar(255) DEFAULT NULL,
  `period_of_voyage` varchar(255) NOT NULL,
  `total_sum_insured` float DEFAULT NULL,
  `total_premium` float DEFAULT NULL,
  `company` varchar(255) DEFAULT NULL,
  `created_at` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
