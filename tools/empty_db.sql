-- phpMyAdmin SQL Dump
-- version 3.4.10.1deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: May 14, 2012 at 06:51 AM
-- Server version: 5.5.22
-- PHP Version: 5.3.10-1ubuntu3.1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `newspapercms`
--

-- --------------------------------------------------------

--
-- Table structure for table `film`
--

CREATE TABLE IF NOT EXISTS `film` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `url` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `scanned` tinyint(1) NOT NULL DEFAULT '0',
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FULLTEXT KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=114 ;

-- --------------------------------------------------------

--
-- Table structure for table `issue`
--

CREATE TABLE IF NOT EXISTS `issue` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `newspaper_id` int(255) NOT NULL,
  `date` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `mentions`
--

CREATE TABLE IF NOT EXISTS `mentions` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `page_id` int(255) NOT NULL,
  `mentioned` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `newspaper`
--

CREATE TABLE IF NOT EXISTS `newspaper` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `urltitle` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `blurb` text COLLATE utf8_unicode_ci NOT NULL,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `urltitle` (`urltitle`),
  UNIQUE KEY `title` (`title`),
  FULLTEXT KEY `title_2` (`title`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `page`
--

CREATE TABLE IF NOT EXISTS `page` (
  `id` int(255) NOT NULL AUTO_INCREMENT,
  `issue_id` int(255) NOT NULL,
  `page_no` int(255) NOT NULL,
  `film_id` int(255) NOT NULL,
  `slide_id` int(255) NOT NULL,
  `image` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `ocr` text COLLATE utf8_unicode_ci,
  `modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FULLTEXT KEY `ocr` (`ocr`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=374 ;

-- --------------------------------------------------------

--
-- Table structure for table `search`
--

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `newspapercms`.`search` AS select `newspapercms`.`page`.`id` AS `id`,('page' collate utf8_unicode_ci) AS `type`,(concat('newspaper/',`newspapercms`.`newspaper`.`urltitle`,'/issue-',convert(`newspapercms`.`issue`.`id` using utf8),'/',convert(`newspapercms`.`page`.`page_no` using utf8),'/') collate utf8_unicode_ci) AS `path`,(concat(`newspapercms`.`newspaper`.`title`,': ',date_format(`newspapercms`.`issue`.`date`,'%M %d, %Y'),', ',convert(`newspapercms`.`page`.`page_no` using utf8)) collate utf8_unicode_ci) AS `title`,`newspapercms`.`ocr`.`plain` AS `found` from (((`newspapercms`.`ocr` join `newspapercms`.`page`) join `newspapercms`.`issue`) join `newspapercms`.`newspaper`) where ((`newspapercms`.`ocr`.`page_id` = `newspapercms`.`page`.`id`) and (`newspapercms`.`page`.`issue_id` = `newspapercms`.`issue`.`id`) and (`newspapercms`.`issue`.`newspaper_id` = `newspapercms`.`newspaper`.`id`)) union select `newspapercms`.`newspaper`.`id` AS `id`,('newspaper' collate utf8_unicode_ci) AS `type`,(concat('newspaper/',`newspapercms`.`newspaper`.`urltitle`,'/') collate utf8_unicode_ci) AS `path`,`newspapercms`.`newspaper`.`title` AS `title`,`newspapercms`.`newspaper`.`title` AS `found` from `newspapercms`.`newspaper` union select `newspapercms`.`film`.`id` AS `id`,('film' collate utf8_unicode_ci) AS `type`,(concat('film/',`newspapercms`.`film`.`id`) collate utf8_unicode_ci) AS `path`,`newspapercms`.`film`.`name` AS `title`,`newspapercms`.`film`.`name` AS `found` from `newspapercms`.`film` union select `newspapercms`.`mentions`.`id` AS `id`,('mention' collate utf8_unicode_ci) AS `type`,(concat('newspaper/',`newspapercms`.`newspaper`.`urltitle`,'/issue-',convert(`newspapercms`.`issue`.`id` using utf8),'/',convert(`newspapercms`.`page`.`page_no` using utf8),'/') collate utf8_unicode_ci) AS `path`,(concat(`newspapercms`.`newspaper`.`title`,': ',date_format(`newspapercms`.`issue`.`date`,'%M %d, %Y'),', ',convert(`newspapercms`.`page`.`page_no` using utf8)) collate utf8_unicode_ci) AS `title`,`newspapercms`.`mentions`.`mentioned` AS `found` from (((`newspapercms`.`mentions` join `newspapercms`.`page`) join `newspapercms`.`issue`) join `newspapercms`.`newspaper`) where ((`newspapercms`.`mentions`.`page_id` = `newspapercms`.`page`.`id`) and (`newspapercms`.`page`.`issue_id` = `newspapercms`.`issue`.`id`) and (`newspapercms`.`issue`.`newspaper_id` = `newspapercms`.`newspaper`.`id`));
