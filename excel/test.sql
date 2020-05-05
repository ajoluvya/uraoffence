-- phpMyAdmin SQL Dump
-- version 2.11.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 17, 2009 at 11:33 AM
-- Server version: 5.0.45
-- PHP Version: 5.2.4

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `test`
--

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `orders_id` int(11) NOT NULL auto_increment,
  `customers_id` int(11) NOT NULL,
  `customers_name` varchar(64) collate utf8_unicode_ci NOT NULL,
  `customers_company` varchar(32) collate utf8_unicode_ci default NULL,
  `customers_street_address` varchar(64) collate utf8_unicode_ci NOT NULL,
  `customers_city` varchar(32) collate utf8_unicode_ci NOT NULL,
  `customers_postcode` varchar(10) collate utf8_unicode_ci NOT NULL,
  `customers_state` varchar(32) collate utf8_unicode_ci default NULL,
  `customers_country` varchar(32) collate utf8_unicode_ci NOT NULL,
  `customers_telephone` varchar(32) collate utf8_unicode_ci NOT NULL,
  `customers_email_address` varchar(96) collate utf8_unicode_ci NOT NULL,
  `last_modified` datetime default NULL,
  `date_purchased` datetime default NULL,
  PRIMARY KEY  (`orders_id`),
  KEY `idx_orders_customers_id` (`customers_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2020 ;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`orders_id`, `customers_id`, `customers_name`, `customers_company`, `customers_street_address`, `customers_city`, `customers_postcode`, `customers_state`, `customers_country`, `customers_telephone`, `customers_email_address`, `last_modified`, `date_purchased`) VALUES
(100, 1, 'David Vielmetter', 'DV Consulting', '101 Avenue St.', 'Los Angeles', '90036', 'California', 'United States', '1234567890', 'david@google.com', '2009-04-10 10:26:08', '2009-02-20 10:23:26'),
(101, 1, 'David Vielmetter', 'DV Consulting', '101 Avenue St.', 'Los Angeles', '90036', 'California', 'United States', '1234567890', 'david@google.com', '2009-04-10 09:52:05', '2009-03-21 18:38:53'),
(102, 1, 'David Vielmetter', 'DV Consulting', '101 Avenue St.', 'Los Angeles', '90036', 'California', 'United States', '1234567890', 'david@google.com', '2009-04-10 13:07:16', '2009-04-09 15:55:55'),
(103, 1, 'David Vielmetter', 'DV Consulting', '101 Avenue St.', 'Los Angeles', '90036', 'California', 'United States', '1234567890', 'david@google.com', '2009-05-22 11:58:16', '2009-04-10 13:20:20'),
(105, 3, 'Nigel Lithgoe', 'So You Think You Can Dance', '888 W. Washington Blvd', 'Hollywood', '90016', 'California', 'United Staties', '2293921123', 'nigel@dance.com', '2009-07-13 14:17:40', '2009-07-15 14:17:46'),
(104, 2, 'Mary Murphy', 'So You Think You Can Dance', '775 E Washington Blvd', 'Hollywood', '90016', 'California', 'United States', '8881112222', 'mm@dance.com', '2009-07-10 10:05:25', '2009-07-10 10:04:20');

-- --------------------------------------------------------

--
-- Table structure for table `orders_products`
--

CREATE TABLE `orders_products` (
  `orders_products_id` int(11) NOT NULL auto_increment,
  `orders_id` int(11) NOT NULL,
  `products_id` int(11) NOT NULL,
  `products_model` varchar(12) collate utf8_unicode_ci default NULL,
  `products_name` varchar(64) collate utf8_unicode_ci NOT NULL,
  `products_price` decimal(15,4) NOT NULL,
  `final_price` decimal(15,4) NOT NULL,
  `products_tax` decimal(7,4) NOT NULL,
  `products_quantity` int(2) NOT NULL,
  PRIMARY KEY  (`orders_products_id`),
  KEY `idx_orders_products_orders_id` (`orders_id`),
  KEY `idx_orders_products_products_id` (`products_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=31 ;

--
-- Dumping data for table `orders_products`
--

INSERT INTO `orders_products` (`orders_products_id`, `orders_id`, `products_id`, `products_model`, `products_name`, `products_price`, `final_price`, `products_tax`, `products_quantity`) VALUES
(1, 100, 36, 'Nuka', 'Cola', 10.0000, 10.0000, 0.0000, 1),
(6, 105, 39, 'Insta', 'Mash', 4.0000, 4.0000, 0.0000, 2),
(5, 104, 39, 'Insta', 'Mash', 4.0000, 4.0000, 0.0000, 1),
(4, 103, 38, 'Dandy Boy', 'Apples', 10.0000, 10.0000, 0.0000, 2),
(3, 102, 38, 'Dandy Boy ', 'Apples', 10.0000, 10.0000, 0.0000, 3),
(2, 101, 37, 'Abraxo', 'Cleaner', 5.0000, 5.0000, 0.0000, 5);

-- --------------------------------------------------------

--
-- Table structure for table `orders_total`
--

CREATE TABLE `orders_total` (
  `orders_total_id` int(10) unsigned NOT NULL auto_increment,
  `orders_id` int(11) NOT NULL,
  `title` varchar(255) collate utf8_unicode_ci NOT NULL,
  `text` varchar(255) collate utf8_unicode_ci NOT NULL,
  `value` decimal(15,4) NOT NULL,
  PRIMARY KEY  (`orders_total_id`),
  KEY `idx_orders_total_orders_id` (`orders_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=82 ;

--
-- Dumping data for table `orders_total`
--

INSERT INTO `orders_total` (`orders_total_id`, `orders_id`, `title`, `text`, `value`) VALUES
(1, 100, 'Total', '$10', 10.0000),
(2, 105, 'Total', '$8', 8.0000),
(3, 104, 'Total', '$4', 4.0000),
(4, 103, 'Total', '$20', 20.0000),
(5, 102, 'Total', '$30', 30.0000),
(6, 101, 'Total', '$25', 25.0000);
