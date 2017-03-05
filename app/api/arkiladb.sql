-- phpMyAdmin SQL Dump
-- version 4.6.5.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 05, 2017 at 01:44 AM
-- Server version: 10.1.21-MariaDB
-- PHP Version: 7.1.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `arkiladb`
--

-- --------------------------------------------------------

--
-- Table structure for table `ads`
--

CREATE TABLE IF NOT EXISTS `ads` (
  `AdsId` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `UserId` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `Title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `Capacity` int(11) NOT NULL,
  `CarType` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `OtherDetails` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
  `IsActive` bit(1) NOT NULL DEFAULT b'1',
  `DateCreated` datetime(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
  PRIMARY KEY (`AdsId`),
  UNIQUE KEY `AdsId` (`AdsId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `adsimage`
--

CREATE TABLE IF NOT EXISTS `adsimage` (
  `RecId` bigint(20) NOT NULL,
  `AdsId` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `Path` varchar(1000) COLLATE utf8_unicode_ci NOT NULL,
  `CreatedBy` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `CreatedFrom` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `DateCreated` datetime(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  `IsActive` bit(1) NOT NULL DEFAULT b'1',
  PRIMARY KEY (`RecId`),
  UNIQUE KEY `RecId` (`RecId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE IF NOT EXISTS `bookings` (
  `BookingId` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `UserId` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `AdsId` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `Status` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'PENDING',
  `CreatedFrom` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `DateCreated` datetime(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
  `IsReadOwner` bit(1) NOT NULL DEFAULT b'1',
  `IsReadOwnerDate` datetime(6) DEFAULT NULL,
  `IsReadUser` bit(1) NOT NULL DEFAULT b'0',
  `IsReadUserDate` datetime(6) DEFAULT NULL,
  `IsActive` bit(1) NOT NULL DEFAULT b'1',
  `Remarks` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`BookingId`),
  UNIQUE KEY `BookingId` (`BookingId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `conversation`
--

CREATE TABLE IF NOT EXISTS `conversation` (
  `c_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_one` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `user_two` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `ip` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `time` datetime(6) DEFAULT CURRENT_TIMESTAMP(6),
  PRIMARY KEY (`c_id`),
  KEY `user_one` (`user_one`),
  KEY `user_two` (`user_two`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `conversation_reply`
--

CREATE TABLE IF NOT EXISTS `conversation_reply` (
  `cr_id` int(11) NOT NULL AUTO_INCREMENT,
  `reply` text COLLATE utf8_unicode_ci,
  `user_id_fk` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `ip` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `time` datetime(6) DEFAULT CURRENT_TIMESTAMP(6),
  `c_id_fk` int(11) NOT NULL,
  PRIMARY KEY (`cr_id`),
  KEY `user_id_fk` (`user_id_fk`),
  KEY `c_id_fk` (`c_id_fk`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `UserId` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `FirstName` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `MiddleName` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `LastName` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `BirthDate` date NOT NULL,
  `Gender` char(1) COLLATE utf8_unicode_ci NOT NULL,
  `EmailAddress` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `UserName` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `Password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `SaltPassword` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `MobileNo1` varchar(11) COLLATE utf8_unicode_ci NOT NULL,
  `MobileNo2` varchar(11) COLLATE utf8_unicode_ci NOT NULL,
  `TelNo` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `ProfilePicturePath` varchar(1000) COLLATE utf8_unicode_ci NOT NULL,
  `Province` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `Municipality` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `Barangay` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `StreetNo` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `Token` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `CreatedAt` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'CURRENT_TIMESTAMP(3)',
  `UpdatedAt` datetime(3) NOT NULL DEFAULT CURRENT_TIMESTAMP(3),
  `dropbox_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`UserId`),
  UNIQUE KEY `UserId` (`UserId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `conversation`
--
ALTER TABLE `conversation`
  ADD CONSTRAINT `conversation_ibfk_1` FOREIGN KEY (`user_one`) REFERENCES `users` (`UserId`),
  ADD CONSTRAINT `conversation_ibfk_2` FOREIGN KEY (`user_two`) REFERENCES `users` (`UserId`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
