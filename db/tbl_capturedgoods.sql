-- phpMyAdmin SQL Dump
-- version 3.4.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 30, 2014 at 04:26 AM
-- Server version: 5.5.16
-- PHP Version: 5.3.8

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `uraoffences`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbl_capturedgoods`
--

CREATE TABLE IF NOT EXISTS `tbl_capturedgoods` (
  `gid` int(20) NOT NULL AUTO_INCREMENT,
  `off_id` int(11) NOT NULL,
  `hscode` varchar(30) NOT NULL,
  `good_name` varchar(30) NOT NULL,
  `good_descpn` varchar(50) NOT NULL,
  `unit_of_measure` varchar(10) NOT NULL,
  `goods_val` int(20) NOT NULL,
  `taxes` int(20) NOT NULL,
  `category` varchar(30) NOT NULL,
  PRIMARY KEY (`gid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=17 ;

--
-- Dumping data for table `tbl_capturedgoods`
--

INSERT INTO `tbl_capturedgoods` (`gid`, `off_id`, `hscode`, `good_name`, `good_descpn`, `unit_of_measure`, `goods_val`, `taxes`, `category`) VALUES
(1, 10, '', 'BISCUITS', '10 PKTS', '', 450000, 9800, ' '),
(2, 10, '', 'BEAUTY CREAMS', '1000 PKTS', '', 1000000, 100000, 'COUNTERFEIT GOODS'),
(3, 8, '', 'BRONZE', 'BRONZE PELLETS', '', 12000, 89000, 'COUNTERFEIT GOODS'),
(4, 8, '', 'HONEY', 'KENYAN HONEY', '', 23000, 24000, 'NARCOTICS'),
(5, 2, '', 'COPPER', 'METAL COPPER CHIPS', '', 5600, 12000, 'RESTRICTED GOODS'),
(6, 2, '', 'BREAD', '100 LOAVES KENJOY BREAD BRANDS', '', 45000, 3400000, 'RESTRICTED GOODS'),
(7, 3, '', 'TRAINING EQUIPMENT', '1000 CARTONS OF TRAINING EQUIPMENT', '', 4500, 90000, 'RESTRICTED GOODS'),
(8, 3, '', 'MACHETES', '76 MARKETABLE MACHETES', '', 400, 560000, 'COUNTERFEIT GOODS'),
(9, 11, '', 'MATOOKE', '12 MATOOKE BUNCHES', '', 700, 879473, 'PROHIBITED'),
(10, 12, '', 'RICE', '50 KGS', '', 23000, 3400, 'NILL'),
(12, 14, '', 'VEHICLE', 'UAJ 132 X HARRIER', '', 768, 789, 'NILL'),
(13, 15, '', 'IRISH POTATOES', '10KG', '', 5600, 545400, 'NILL'),
(14, 16, '', 'MAIZE BRAND', '1 BAG OF MAIZE BRAND', '', 7000, 0, 'NILL'),
(15, 17, '', 'PETROLEUM JELLY', 'RRERER', '', 1000000, 0, 'NILL'),
(16, 18, '', 'PETROLEUM JELLY', 'SAMONA BABY JELLY', '', 23000, 780, 'NILL');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
