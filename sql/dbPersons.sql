-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Mar 12, 2025 at 05:46 PM
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
-- Table structure for table `dbPersons`
--

CREATE TABLE `dbPersons` (
  `id` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `start_date` text DEFAULT NULL,
  `venue` text DEFAULT NULL,
  `first_name` text NOT NULL,
  `last_name` text DEFAULT NULL,
  `address` text DEFAULT NULL,
  `city` text DEFAULT NULL,
  `state` varchar(2) DEFAULT NULL,
  `zip` text DEFAULT NULL,
  `phone1` varchar(12) NOT NULL,
  `phone1type` text DEFAULT NULL,
  `phone2` varchar(12) DEFAULT NULL,
  `phone2type` text DEFAULT NULL,
  `birthday` text DEFAULT NULL,
  `email` text DEFAULT NULL,
  `contact_name` text NOT NULL,
  `contact_num` varchar(12) NOT NULL,
  `relation` text NOT NULL,
  `contact_time` text NOT NULL,
  `cMethod` text DEFAULT NULL,
  `type` text DEFAULT NULL,
  `status` text DEFAULT NULL,
  `availability` text DEFAULT NULL,
  `schedule` text DEFAULT NULL,
  `hours` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `password` text DEFAULT NULL,
  `sundays_start` char(5) DEFAULT NULL,
  `sundays_end` char(5) DEFAULT NULL,
  `mondays_start` char(5) DEFAULT NULL,
  `mondays_end` char(5) DEFAULT NULL,
  `tuesdays_start` char(5) DEFAULT NULL,
  `tuesdays_end` char(5) DEFAULT NULL,
  `wednesdays_start` char(5) DEFAULT NULL,
  `wednesdays_end` char(5) DEFAULT NULL,
  `thursdays_start` char(5) DEFAULT NULL,
  `thursdays_end` char(5) DEFAULT NULL,
  `fridays_start` char(5) DEFAULT NULL,
  `fridays_end` char(5) DEFAULT NULL,
  `saturdays_start` char(5) DEFAULT NULL,
  `saturdays_end` char(5) DEFAULT NULL,
  `profile_pic` text NOT NULL,
  `force_password_change` tinyint(1) NOT NULL,
  `gender` varchar(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `dbPersons`
--

INSERT INTO `dbPersons` (`id`, `start_date`, `venue`, `first_name`, `last_name`, `address`, `city`, `state`, `zip`, `phone1`, `phone1type`, `phone2`, `phone2type`, `birthday`, `email`, `contact_name`, `contact_num`, `relation`, `contact_time`, `cMethod`, `type`, `status`, `availability`, `schedule`, `hours`, `notes`, `password`, `sundays_start`, `sundays_end`, `mondays_start`, `mondays_end`, `tuesdays_start`, `tuesdays_end`, `wednesdays_start`, `wednesdays_end`, `thursdays_start`, `thursdays_end`, `fridays_start`, `fridays_end`, `saturdays_start`, `saturdays_end`, `profile_pic`, `force_password_change`, `gender`) VALUES
('brianna@gmail.com', '2024-01-22', 'portland', 'Brianna', 'Wahl', '212 Latham Road', 'Mineola', 'VA', '11501', '1234567890', 'cellphone', '', '', '2004-04-04', 'brianna@gmail.com', 'Mom', '1234567890', 'Mother', 'Days', 'text', 'admin', 'Active', '', '', '', '', '$2y$10$jNbMmZwq.1r/5/oy61IRkOSX4PY6sxpYEdWfu9tLRZA6m1NgsxD6m', '00:00', '10:00', '', '', '', '', '02:00', '16:00', '', '', '', '', '', '', '', 0, 'Female'),
('bum@gmail.com', '2024-01-24', 'portland', 'bum', 'bum', '1345 Strattford St.', 'Mineola', 'VA', '22401', '1234567890', 'home', '', '', '1111-11-11', 'bum@gmail.com', 'Mom', '1234567890', 'Mom', 'Mornings', 'text', 'admin', 'Active', '', '', '', '', '$2y$10$Ps8FnZXT7d4uiU/R5YFnRecIRbRakyVtbXP9TVqp7vVpuB3yTXFIO', '', '', '15:00', '18:00', '', '', '', '', '', '', '', '', '', '', '', 0, 'Male'),
('mom@gmail.com', '2024-01-22', 'portland', 'Lorraine', 'Egan', '212 Latham Road', 'Mineola', 'NY', '11501', '5167423832', 'home', '', '', '1910-10-10', 'mom@gmail.com', 'Mom', '5167423832', 'Dead', 'Never', 'phone', 'admin', 'Active', '', '', '', '', '$2y$10$of1CkoNXZwyhAMS5GQ.aYuAW1SHptF6z31ONahnF2qK4Y/W9Ty2h2', '00:00', '10:00', '18:00', '19:00', '06:00', '14:00', '02:00', '12:00', '02:00', '16:00', '12:00', '18:00', '08:00', '17:00', '', 0, 'Male'),
('oliver@gmail.com', '2024-01-22', 'portland', 'Oliver', 'Wahl', '1345 Strattford St.', 'Fredericksburg', 'VA', '22401', '1234567890', 'home', '', '', '2011-11-11', 'oliver@gmail.com', 'Mom', '1234567890', 'Mother', 'Middle of the Night', 'text', 'admin', 'Active', '', '', '', '', '$2y$10$tgIjMkXhPzdmgGhUgbfPRuXLJVZHLiC0pWQQwOYKx8p8H8XY3eHw6', '06:00', '14:00', '', '', '', '', '', '', '', '', '', '', '04:00', '18:00', '', 0, 'Other'),
('peter@gmail.com', '2024-01-22', 'portland', 'Peter', 'Polack', '1345 Strattford St.', 'Mineola', 'VA', '12345', '1234567890', 'cellphone', '', '', '1968-09-09', 'peter@gmail.com', 'Mom', '1234567890', 'Mom', 'Don&amp;amp;#039;t Call or Text or Email', 'email', 'admin', 'Active', '', '', '', '', '$2y$10$j5xJ6GWaBhnb45aktS.kruk05u./TsAhEoCI3VRlNs0SRGrIqz.B6', '00:00', '19:00', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 'Male'),
('polack@um.edu', '2024-01-22', 'portland', 'Jennifer', 'Polack', '15 Wallace Farms Lane', 'Fredericksburg', 'VA', '22406', '1234567890', 'cellphone', '', '', '1970-05-01', 'polack@um.edu', 'Mom', '1234567890', 'Mom', 'Days', 'email', 'admin', 'Active', '', '', '', '', '$2y$10$mp18j4WqhlQo7MTeS/9kt.i08n7nbt0YMuRoAxtAy52BlinqPUE4C', '00:00', '12:00', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 'Female'),
('tom@gmail.com', '2024-01-22', 'portland', 'tom', 'tom', '1345 Strattford St.', 'Mineola', 'NY', '12345', '1234567890', 'home', '', '', '1920-02-02', 'tom@gmail.com', 'Dad', '9876543210', 'Father', 'Mornings', 'phone', 'admin', 'Active', '', '', '', '', '$2y$10$1Zcj7n/prdkNxZjxTK1zUOF7391byZvsXkJcN8S8aZL57sz/OfxP.', '11:00', '17:00', '', '', '11:00', '14:00', '', '', '09:00', '14:00', '', '', '', '', '', 0, 'Male'),
('vmsroot', 'N/A', 'portland', 'vmsroot', '', 'N/A', 'N/A', 'VA', 'N/A', '', 'N/A', 'N/A', 'N/A', 'N/A', 'vmsroot', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', '', 'N/A', 'N/A', 'N/A', 'N/A', 'N/A', '$2y$10$.3p8xvmUqmxNztEzMJQRBesLDwdiRU3xnt/HOcJtsglwsbUk88VTO', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, '');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `dbPersons`
--
ALTER TABLE `dbPersons`
  ADD PRIMARY KEY (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
