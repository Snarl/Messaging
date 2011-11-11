-- phpMyAdmin SQL Dump
-- version 3.4.5
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Nov 11, 2011 at 12:32 AM
-- Server version: 5.0.92
-- PHP Version: 5.2.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `sukey_boogaloo`
--

-- --------------------------------------------------------

--
-- Table structure for table `exportnames`
--

CREATE TABLE IF NOT EXISTS `exportnames` (
  `uid` varchar(255) NOT NULL,
  `screenName` varchar(45) default NULL,
  `score` decimal(5,2) default NULL,
  `isTroll` varchar(1) default NULL,
  `color` varchar(6) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE IF NOT EXISTS `reports` (
  `id` int(255) NOT NULL,
  `servertime` int(255) NOT NULL,
  `clienttime` int(255) NOT NULL,
  `longitude` decimal(10,7) NOT NULL,
  `latitude` decimal(10,7) NOT NULL,
  `report` mediumtext NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sm_inq`
--

CREATE TABLE IF NOT EXISTS `sm_inq` (
  `msg_plain` varchar(255) default NULL,
  `msg_html` varchar(255) default NULL,
  `tags` varchar(255) default NULL,
  `hashes` varchar(255) default NULL,
  `datetime` datetime default NULL,
  `sender` varchar(100) default NULL,
  `istagged` varchar(1) default NULL,
  `msg_id` varchar(255) default NULL,
  `sender_score` decimal(5,2) default NULL,
  `published` varchar(50) default NULL,
  `geo` varchar(255) default NULL,
  `locn` varchar(255) default NULL,
  `lang` varchar(3) default NULL,
  `uid` varchar(20) default NULL,
  `dealtwith` varchar(1) default NULL,
  UNIQUE KEY `msg_id` (`msg_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sm_score`
--

CREATE TABLE IF NOT EXISTS `sm_score` (
  `uid` varchar(255) NOT NULL,
  `screenName` varchar(45) default NULL,
  `score` decimal(5,2) default NULL,
  `isTroll` varchar(1) default NULL,
  `color` varchar(6) default NULL,
  UNIQUE KEY `screenName` (`screenName`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sm_score_bak`
--

CREATE TABLE IF NOT EXISTS `sm_score_bak` (
  `uid` varchar(255) NOT NULL,
  `screenName` varchar(45) default NULL,
  `score` decimal(5,2) default NULL,
  `isTroll` varchar(1) default NULL,
  `color` varchar(6) default NULL,
  PRIMARY KEY  (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `SM_scoringProfile`
--

CREATE TABLE IF NOT EXISTS `SM_scoringProfile` (
  `name` varchar(255) NOT NULL,
  `w` varchar(512) default NULL,
  `m` varchar(512) default NULL,
  PRIMARY KEY  (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sm_stats`
--

CREATE TABLE IF NOT EXISTS `sm_stats` (
  `score` int(11) default NULL,
  `users` int(11) default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
