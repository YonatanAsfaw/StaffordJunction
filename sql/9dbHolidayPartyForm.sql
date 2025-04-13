-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 13, 2025 at 02:57 AM
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
-- Table structure for table `dbBrainBuildersHolidayPartyForm`
--

CREATE TABLE `dbBrainBuildersHolidayPartyForm` (
  `id` int(11) NOT NULL,
  `family_id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `child_first_name` varchar(50) NOT NULL,
  `child_last_name` varchar(50) NOT NULL,
  `isAttending` tinyint(1) NOT NULL,
  `transportation` varchar(50) NOT NULL,
  `neighborhood` varchar(50) NOT NULL,
  `comments` text DEFAULT NULL,
  `child_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dbBrainBuildersHolidayPartyForm`
--

INSERT INTO `dbBrainBuildersHolidayPartyForm` (`id`, `family_id`, `email`, `child_first_name`, `child_last_name`, `isAttending`, `transportation`, `neighborhood`, `comments`, `child_id`) VALUES
(1, 4, 'test@email.com', 'Henry', 'Smith', 1, 'provide_own', 'Apple Creek', '', 1),
(3, 4, 'test@email.com', 'Jane', 'Smith', 1, 'provide_own', 'Apple Creek', '', 2);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `dbBrainBuildersHolidayPartyForm`
--
ALTER TABLE `dbBrainBuildersHolidayPartyForm`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_family_id` (`family_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `dbBrainBuildersHolidayPartyForm`
--
ALTER TABLE `dbBrainBuildersHolidayPartyForm`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `dbBrainBuildersHolidayPartyForm`
--
ALTER TABLE `dbBrainBuildersHolidayPartyForm`
  ADD CONSTRAINT `FK_family_id` FOREIGN KEY (`family_id`) REFERENCES `dbFamily` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
