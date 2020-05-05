-- phpMyAdmin SQL Dump
-- version 3.4.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Aug 01, 2014 at 06:42 PM
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
-- Table structure for table `loginout`
--

CREATE TABLE IF NOT EXISTS `loginout` (
  `logID` int(20) NOT NULL AUTO_INCREMENT,
  `loggedin` tinyint(1) NOT NULL,
  `logintime` datetime NOT NULL,
  `logoutime` datetime NOT NULL,
  `staffId` int(8) NOT NULL,
  PRIMARY KEY (`logID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `region`
--

CREATE TABLE IF NOT EXISTS `region` (
  `rid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL COMMENT 'region name',
  `modifiedby` int(11) NOT NULL,
  PRIMARY KEY (`rid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `region`
--

INSERT INTO `region` (`rid`, `name`, `modifiedby`) VALUES
(1, 'EASTERN', 1),
(2, 'CENTRAL', 1),
(3, 'NORTHERN', 1),
(4, 'SOUTH WESTERN', 1),
(5, 'MARINE', 1);

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE IF NOT EXISTS `staff` (
  `staff_id` int(8) NOT NULL AUTO_INCREMENT,
  `firstname` varchar(30) NOT NULL,
  `lastname` varchar(30) NOT NULL,
  `username` varchar(30) NOT NULL,
  `password` varchar(100) NOT NULL,
  `dutystation` varchar(5) NOT NULL,
  `mobile` varchar(30) NOT NULL,
  `email` varchar(30) NOT NULL,
  `dob` date DEFAULT NULL,
  `role` varchar(30) NOT NULL,
  `address` varchar(30) NOT NULL,
  `datemodified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`staff_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`staff_id`, `firstname`, `lastname`, `username`, `password`, `dutystation`, `mobile`, `email`, `dob`, `role`, `address`, `datemodified`) VALUES
(1, 'Allan', 'Banders', 'admin', '4d22cd83d39ff90b5417572cc4fd417d', 'UGBST', '+256709382793', 'admin@akapapula.com', '2014-01-13', 'admin', 'Kawanda', '2014-03-17 21:00:00'),
(3, 'Abraham', 'Mendel', 'staff', '1253208465b1efa876f982d8a9e73eef', 'UGBST', '0392345913', 'amendel@akapapula.com', '1964-03-23', 'Officer', 'Makindye', '2014-04-19 06:15:00'),
(4, 'ANDREW', 'NAMBULA', 'anambula', '385fd8c8f503694ea95712460299ef5d', 'UGBST', '+256 783 698 453', 'anambula@ura.go.ug', '2004-07-05', 'Officer', 'Mbale', '2014-07-13 13:37:43'),
(5, 'JACKSON', 'NAMBULA', 'jnambula', 'a5faf5e4abc10d18fa997f821aad35dc', 'UGBST', '+256 715 990 095', 'jnambula@ura.go.ug', NULL, 'Officer', 'busitema', '2014-07-13 15:00:09'),
(6, 'IVAN', 'KAKAIRE', 'ikakaire', '2dc9a1cc2da144c53ba6e345db6f3364', 'UGBST', '+256 784 378 289', 'ikakaire@ura.go.ug', NULL, 'In Charge', 'Jinja', '2014-07-16 12:42:10'),
(7, 'BUSULWA', 'IVAN', 'bivan', '588957c6ffaed6892817fc1799452c18', 'UGBST', '+256 786 329 374', 'ivabusulwa@ura.go.ug', NULL, 'Regional Supervisor', 'Mubende', '2014-07-19 12:30:05'),
(8, 'HENRIETTA', 'KUNYA', 'hkunya', '9291a2aedf36a9fa936d4aed425dc7e6', 'UGMAL', '+256 782 980 948', 'hkunya@ura.go.ug', NULL, 'Officer', 'Mukono', '2014-07-24 12:35:55'),
(9, 'HARRY', 'MUSIMENTA', 'hmusimenta', '4b1e4828f121b683468ad25d3f3bd8fc', 'UGBUN', '+256 782 200 990', 'hmusimenta@ura.go.ug', NULL, 'Officer', 'Dutroiet', '2014-07-24 12:48:20'),
(10, 'BONIFACE', 'KITAKA', 'bkitaka', '8a9f159088f54571a0a5e687ebed5d56', 'UGKLA', '+256 763 990 389', 'bkitaka@ura.go.ug', NULL, 'Manager', 'Mityana', '2014-07-24 12:55:28');

-- --------------------------------------------------------

--
-- Table structure for table `station`
--

CREATE TABLE IF NOT EXISTS `station` (
  `stationcode` varchar(6) NOT NULL,
  `name` varchar(50) NOT NULL COMMENT 'Name of the station',
  `region` int(3) NOT NULL,
  `modifiedby` int(10) unsigned zerofill NOT NULL,
  PRIMARY KEY (`stationcode`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `station`
--

INSERT INTO `station` (`stationcode`, `name`, `region`, `modifiedby`) VALUES
('UGAFO', 'AFOGI', 3, 0000000001),
('UGARU', 'ARUA', 3, 0000000001),
('UGBSG', 'BUSUNGA', 5, 0000000001),
('UGBST', 'BUSITEMA', 1, 0000000001),
('UGBUN', 'BUNAGANA', 4, 0000000001),
('UGBUS', 'BUSIA', 1, 0000000001),
('UGEBB', 'ENTEBBE', 2, 0000000001),
('UGELE', 'ELEGU', 3, 0000000001),
('UGFOR', 'FORTPORTAL', 4, 0000000001),
('UGHAM', 'HAMA', 5, 0000000001),
('UGIGA', 'IGANGA', 1, 0000000001),
('UGJJA', 'JINJA', 1, 0000000001),
('UGKAT', 'KATUNA', 4, 0000000001),
('UGKDN', 'KAMDINI', 3, 0000000001),
('UGKLA', 'KAMPALA', 2, 0000000001),
('UGLWK', 'LWAKHAKHA', 1, 0000000001),
('UGMAL', 'MALABA', 1, 0000000001),
('UGMBL', 'MBALE', 1, 0000000001),
('UGMPO', 'MPONDWE', 4, 0000000001),
('UGMSK', 'MASAKA', 4, 0000000001),
('UGMUT', 'MUTUKULA', 4, 0000000001),
('UGORA', 'ORABA', 3, 0000000001),
('UGPAK', 'PAKWACH', 3, 0000000001),
('UGSCA', 'SCANNER', 2, 0000000001),
('UGSIG', 'SIGULU', 5, 0000000001);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_alertunit`
--

CREATE TABLE IF NOT EXISTS `tbl_alertunit` (
  `ID` tinyint(3) NOT NULL AUTO_INCREMENT,
  `unit` varchar(100) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `tbl_alertunit`
--

INSERT INTO `tbl_alertunit` (`ID`, `unit`) VALUES
(1, 'INTELLIGENCE UNIT'),
(2, 'ENFORCEMENT FIELD OPERATIONS UNIT'),
(3, 'SCANNER UNIT'),
(4, 'TMU'),
(5, 'OTHER UNITS OUTSIDE Enf Division		');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_capturedgoods`
--

CREATE TABLE IF NOT EXISTS `tbl_capturedgoods` (
  `gid` int(20) NOT NULL AUTO_INCREMENT,
  `off_id` int(11) NOT NULL,
  `good_name` varchar(30) NOT NULL,
  `good_descpn` varchar(50) NOT NULL,
  `goods_val` int(20) NOT NULL,
  `taxes` int(20) NOT NULL,
  `category` varchar(30) NOT NULL,
  PRIMARY KEY (`gid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `tbl_capturedgoods`
--

INSERT INTO `tbl_capturedgoods` (`gid`, `off_id`, `good_name`, `good_descpn`, `goods_val`, `taxes`, `category`) VALUES
(1, 10, 'BISCUITS', '10 PKTS', 450000, 9800, ' '),
(2, 10, 'BEAUTY CREAMS', '1000 PKTS', 1000000, 100000, 'COUNTERFEIT GOODS'),
(3, 8, 'BRONZE', 'BRONZE PELLETS', 12000, 89000, 'COUNTERFEIT GOODS'),
(4, 8, 'HONEY', 'KENYAN HONEY', 23000, 24000, 'NARCOTICS');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_casenumbr`
--

CREATE TABLE IF NOT EXISTS `tbl_casenumbr` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `number` int(3) unsigned zerofill NOT NULL,
  `stationcode` varchar(5) NOT NULL DEFAULT 'ger',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `tbl_casenumbr`
--

INSERT INTO `tbl_casenumbr` (`ID`, `number`, `stationcode`) VALUES
(1, 002, 'UGBST');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_challenges`
--

CREATE TABLE IF NOT EXISTS `tbl_challenges` (
  `challngID` int(11) NOT NULL AUTO_INCREMENT,
  `challenge` varchar(1000) NOT NULL,
  `regionID` int(3) NOT NULL,
  `op_undertaken` varchar(1000) NOT NULL,
  `recordedBy` int(8) NOT NULL,
  `summary` varchar(2000) NOT NULL,
  PRIMARY KEY (`challngID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_good`
--

CREATE TABLE IF NOT EXISTS `tbl_good` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_offence`
--

CREATE TABLE IF NOT EXISTS `tbl_offence` (
  `off_id` int(11) NOT NULL AUTO_INCREMENT,
  `stationcode` varchar(5) NOT NULL,
  `rep_date` date NOT NULL,
  `alert` tinyint(1) NOT NULL DEFAULT '0',
  `alertOrigin` tinyint(3) NOT NULL,
  `entry_no` varchar(50) DEFAULT NULL COMMENT 'entry number',
  `file_no` varchar(100) DEFAULT '' COMMENT 'file number [offence ref]',
  `topup` int(1) NOT NULL DEFAULT '0',
  `offender_names` varchar(50) NOT NULL,
  `nature_offence` varchar(100) NOT NULL,
  `sect_law` varchar(20) NOT NULL,
  `dutable` tinyint(1) NOT NULL DEFAULT '1',
  `det_method` varchar(100) NOT NULL,
  `trans_means` varchar(100) NOT NULL,
  `fines` int(11) unsigned NOT NULL,
  `rec_prn` varchar(100) NOT NULL,
  `handedCWH` tinyint(1) DEFAULT '0',
  `remarks` varchar(50) NOT NULL,
  `modifiedBy` int(8) NOT NULL,
  PRIMARY KEY (`off_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=11 ;

--
-- Dumping data for table `tbl_offence`
--

INSERT INTO `tbl_offence` (`off_id`, `stationcode`, `rep_date`, `alert`, `alertOrigin`, `entry_no`, `file_no`, `topup`, `offender_names`, `nature_offence`, `sect_law`, `dutable`, `det_method`, `trans_means`, `fines`, `rec_prn`, `handedCWH`, `remarks`, `modifiedBy`) VALUES
(2, 'UGBST', '2014-07-13', 0, 0, 'C09988', 'BSTM/OFF/07/14/005', 0, 'ASERU JAOYCE', 'OUT RIGHT SMUGGLING', '300', 0, 'SURVEILANCE', 'BUS', 7000, '6483930030894', 0, 'Pending', 4),
(3, 'UGBST', '2014-07-13', 0, 0, 'C00001', 'BSTM/OFF/07/14/004', 0, 'MUDUSU', 'UNDER VALUATION', '135', 0, 'DOCUMENT CHECK', 'TRAILER', 0, '615000006753', 0, 'Released', 4),
(4, 'UGBST', '2014-07-13', 0, 0, 'C09988', 'BSTM/OFF/07/14/002', 0, 'KALEEBI', 'MISDECLARATION', '203', 0, 'DOCUMENT CHECK', 'TRAILER', 18000090, '615000007654', 0, 'Pending', 4),
(5, 'UGMAL', '2014-07-24', 0, 0, 'C250048', 'UGMAL/C37/07/2014-007', 0, 'MALIK ARAFAT', 'UNDER VALUATION', '300', 1, 'NIGHT DUTY', 'BUS', 239489, '3785994004803', 0, 'FORFEITED', 8),
(6, 'UGBUN', '2014-07-24', 0, 0, 'C399389', 'UGBUN/C37/07/2014-008', 0, 'MORRIS TWINOMUGISHA', 'OUT RIGHT SMUGGLING', '200/300', 1, 'SURVEILANCE UNIT', 'TRUCKS', 6749, '838494379', 0, 'RELEASED', 9),
(10, 'UGBST', '2014-07-31', 1, 1, 'C90849', 'UGBST/C37/07/2014-001', 0, 'ANDREW MUKASA', 'OUT RIGHT SMUGGLING', '600', 1, 'INTELLIGENCE', 'BUS', 890000, '9084JDFKD994', 0, 'RELEASED', 4);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_offenceold`
--

CREATE TABLE IF NOT EXISTS `tbl_offenceold` (
  `off_id` int(11) NOT NULL AUTO_INCREMENT,
  `stationcode` varchar(5) NOT NULL,
  `rep_date` date NOT NULL,
  `alert` tinyint(1) NOT NULL DEFAULT '0',
  `alert_origin` tinyint(3) NOT NULL DEFAULT '0',
  `entry_no` varchar(50) DEFAULT NULL COMMENT 'entry number',
  `file_no` varchar(100) DEFAULT '' COMMENT 'file number [offence ref]',
  `topup` int(1) NOT NULL DEFAULT '0',
  `offender_names` varchar(50) NOT NULL,
  `nature_offence` varchar(100) NOT NULL,
  `sect_law` varchar(20) NOT NULL,
  `dutable` int(1) NOT NULL DEFAULT '1',
  `goodsnames` varchar(100) NOT NULL,
  `desc_goods` varchar(100) NOT NULL,
  `pharma` tinyint(4) NOT NULL DEFAULT '0',
  `narcotics` tinyint(4) NOT NULL DEFAULT '0',
  `prohibited` tinyint(4) NOT NULL DEFAULT '0',
  `restricted` tinyint(4) NOT NULL DEFAULT '0',
  `counterfeit` tinyint(4) NOT NULL DEFAULT '0',
  `det_method` varchar(100) NOT NULL,
  `trans_means` varchar(100) NOT NULL,
  `goods_val` int(11) unsigned DEFAULT NULL,
  `taxes` int(11) unsigned NOT NULL,
  `fines` int(11) unsigned NOT NULL,
  `total` int(11) unsigned NOT NULL,
  `rec_prn` varchar(100) NOT NULL,
  `handedCWH` tinyint(4) NOT NULL DEFAULT '0',
  `remarks` varchar(50) NOT NULL,
  `modifiedBy` int(8) NOT NULL,
  PRIMARY KEY (`off_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

--
-- Dumping data for table `tbl_offenceold`
--

INSERT INTO `tbl_offenceold` (`off_id`, `stationcode`, `rep_date`, `alert`, `alert_origin`, `entry_no`, `file_no`, `topup`, `offender_names`, `nature_offence`, `sect_law`, `dutable`, `goodsnames`, `desc_goods`, `pharma`, `narcotics`, `prohibited`, `restricted`, `counterfeit`, `det_method`, `trans_means`, `goods_val`, `taxes`, `fines`, `total`, `rec_prn`, `handedCWH`, `remarks`, `modifiedBy`) VALUES
(2, 'UGBST', '2014-07-13', 0, 0, 'C09988', 'BSTM/OFF/07/14/005', 0, 'ASERU JAOYCE', 'OUT RIGHT SMUGGLING', '300', 0, 'GOLD EARRINGS', '300 PIECES', 0, 0, 0, 0, 0, 'SURVEILANCE', 'BUS', 400, 4355, 7000, 11355, '6483930030894', 0, 'Pending', 4),
(3, 'UGBST', '2014-07-13', 0, 0, 'C00001', 'BSTM/OFF/07/14/004', 0, 'MUDUSU', 'UNDER VALUATION', '135', 0, 'SUGAR', '10000 KGS OF ', 0, 0, 0, 0, 0, 'DOCUMENT CHECK', 'TRAILER', 35000, 26000000, 0, 26000000, '615000006753', 0, 'Released', 4),
(4, 'UGBST', '2014-07-13', 0, 0, 'C09988', 'BSTM/OFF/07/14/002', 0, 'KALEEBI', 'MISDECLARATION', '203', 0, 'SUGAR', '100000 cartons packed sugar', 0, 0, 0, 0, 0, 'DOCUMENT CHECK', 'TRAILER', 15000, 15000000, 18000090, 33000090, '615000007654', 0, 'Pending', 4),
(5, 'UGMAL', '2014-07-24', 0, 0, 'C250048', 'UGMAL/C37/07/2014-007', 0, 'MALIK ARAFAT', 'UNDER VALUATION', '300', 1, 'RICE', '1000 BAGS OF TANZANIA RICE', 0, 0, 0, 0, 0, 'NIGHT DUTY', 'BUS', 20000, 1200000, 239489, 1439489, '3785994004803', 0, 'FORFEITED', 8),
(6, 'UGBUN', '2014-07-24', 0, 0, 'C399389', 'UGBUN/C37/07/2014-008', 0, 'MORRIS TWINOMUGISHA', 'OUT RIGHT SMUGGLING', '200/300', 1, 'TIMBER', '7 TRUCKS LOADED WITH PINE AND MVULE', 0, 0, 0, 0, 0, 'SURVEILANCE UNIT', 'TRUCKS', 8000000, 5673, 6749, 12422, '838494379', 0, 'RELEASED', 9),
(7, 'UGBST', '2014-07-31', 1, 1, 'C90849', 'UGBST/C37/07/2014-001', 0, 'ANDREW MUKASA', 'OUT RIGHT SMUGGLING', '600', 1, '', '', 0, 0, 0, 0, 0, 'INTELLIGENCE', 'BUS', NULL, 0, 890000, 0, '9084JDFKD994', 0, 'RELEASED', 4);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
