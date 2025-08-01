-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jun 11, 2025 at 11:37 AM
-- Server version: 10.11.10-MariaDB-log
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u149605981_passwd`
--

-- --------------------------------------------------------

--
-- Table structure for table `company`
--

CREATE TABLE `company` (
  `CID` int(11) NOT NULL,
  `CNAME` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `passwd`
--

CREATE TABLE `passwd` (
  `PID` int(11) NOT NULL,
  `APP-NAME` varchar(255) NOT NULL,
  `USERNAME` varchar(255) NOT NULL,
  `PASS` varchar(255) NOT NULL,
  `CID` int(11) NOT NULL,
  `ACCESS` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `passwd_master`
--

CREATE TABLE `passwd_master` (
  `PID` int(11) NOT NULL,
  `APP-NAME` varchar(255) NOT NULL,
  `USERNAME` varchar(255) NOT NULL,
  `PASS` varchar(255) NOT NULL,
  `CID` int(11) NOT NULL,
  `ACCESS` int(11) NOT NULL DEFAULT 1,
  `DATE-TIME` date NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `UID` int(11) NOT NULL,
  `CID` int(11) NOT NULL,
  `ROLE` int(11) NOT NULL DEFAULT 1,
  `EMAIL` varchar(255) NOT NULL,
  `PASSWORD` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `company`
--
ALTER TABLE `company`
  ADD PRIMARY KEY (`CID`);

--
-- Indexes for table `passwd`
--
ALTER TABLE `passwd`
  ADD PRIMARY KEY (`PID`),
  ADD KEY `fk_passwd_company` (`CID`);

--
-- Indexes for table `passwd_master`
--
ALTER TABLE `passwd_master`
  ADD PRIMARY KEY (`PID`),
  ADD KEY `fk_passwd_company` (`CID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`UID`),
  ADD UNIQUE KEY `EMAIL` (`EMAIL`),
  ADD KEY `fk_users_company` (`CID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `company`
--
ALTER TABLE `company`
  MODIFY `CID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `passwd`
--
ALTER TABLE `passwd`
  MODIFY `PID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `passwd_master`
--
ALTER TABLE `passwd_master`
  MODIFY `PID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `UID` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `passwd`
--
ALTER TABLE `passwd`
  ADD CONSTRAINT `fk_passwd_company` FOREIGN KEY (`CID`) REFERENCES `company` (`CID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_company` FOREIGN KEY (`CID`) REFERENCES `company` (`CID`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
