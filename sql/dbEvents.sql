-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Mar 19, 2025 at 05:38 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `stafforddb`
--

-- --------------------------------------------------------

--
-- Table structure for table `dbEvents`
--

CREATE TABLE `dbEvents` (
  `id` int(11) NOT NULL,
  `name` text NOT NULL,
  `abbrevName` text NOT NULL,
  `date` char(10) NOT NULL,
  `startTime` char(5) NOT NULL,
  `endTime` char(5) NOT NULL,
  `description` text NOT NULL,
  `locationID` int(11) NOT NULL,
  `capacity` int(11) NOT NULL,
  `volunteerID` int(11) NOT NULL,
  `completed` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dbEvents`
--

INSERT INTO `dbEvents` (`id`, `name`, `abbrevName`, `date`, `startTime`, `endTime`, `description`, `locationID`, `capacity`, `volunteerID`, `completed`) VALUES
(13, 'Test', 'test', '2025-03-28', '12:00', '23:59', 'Test Event', 4, 0, 8, ''),
(14, 'Test', 'test', '2025-03-27', '12:00', '23:59', 'Test Event', 4, 0, 9, 'no'),
(15, 'Matthew Brian Shinko', 'mbs', '2025-03-29', '12:00', '23:59', 'Event', 4, 0, 8, 'no'),
(16, 'Test', 'test', '2025-03-21', '12:00', '23:59', 'Test Event', 4, 0, 8, 'no');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `dbEvents`
--
ALTER TABLE `dbEvents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FKlocationID` (`locationID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `dbEvents`
--
ALTER TABLE `dbEvents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `dbEvents`
--
ALTER TABLE `dbEvents`
  ADD CONSTRAINT `FKlocationID` FOREIGN KEY (`locationID`) REFERENCES `dbLocations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
