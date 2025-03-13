-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Mar 12, 2025 at 05:30 PM
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
-- Table structure for table `dbVolunteerReport`
--

CREATE TABLE `dbVolunteerReport` (
  `report_id` int(11) NOT NULL,
  `volunteer_id` int(11) NOT NULL,
  `activity_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `hours` int(2) NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `dbVolunteerReport`
--
ALTER TABLE `dbVolunteerReport`
  ADD PRIMARY KEY (`report_id`),
  ADD KEY `volunteer_id` (`volunteer_id`),
  ADD KEY `activity_id` (`activity_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `dbVolunteerReport`
--
ALTER TABLE `dbVolunteerReport`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;


ALTER TABLE `dbVolunteerReport`
  MODIFY COLUMN `hours` DECIMAL(5,2) NOT NULL;


--
-- Constraints for dumped tables
--

--
-- Constraints for table `dbVolunteerReport`
--
ALTER TABLE `dbVolunteerReport`
  ADD CONSTRAINT `dbvolunteerreport_ibfk_1` FOREIGN KEY (`volunteer_id`) REFERENCES `dbVolunteers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dbvolunteerreport_ibfk_2` FOREIGN KEY (`activity_id`) REFERENCES `dbActualActivityForm` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
