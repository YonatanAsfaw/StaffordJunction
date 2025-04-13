-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 13, 2025 at 06:25 PM
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
-- Table structure for table `dbBrainBuildersRegistrationForm`
--

CREATE TABLE `dbBrainBuildersRegistrationForm` (
  `id` int(11) NOT NULL,
  `child_first_name` varchar(50) NOT NULL,
  `child_last_name` varchar(50) NOT NULL,
  `gender` varchar(6) NOT NULL,
  `school_name` varchar(100) NOT NULL,
  `grade` varchar(20) NOT NULL,
  `birthdate` date NOT NULL,
  `child_address` varchar(100) NOT NULL,
  `child_city` varchar(50) NOT NULL,
  `child_state` char(2) NOT NULL,
  `child_zip` char(5) NOT NULL,
  `child_medical_allergies` varchar(255) DEFAULT NULL,
  `child_food_avoidances` varchar(255) DEFAULT NULL,
  `parent1_name` varchar(100) NOT NULL,
  `parent1_phone` char(14) NOT NULL,
  `parent1_address` varchar(100) NOT NULL,
  `parent1_city` varchar(50) NOT NULL,
  `parent1_state` char(2) NOT NULL,
  `parent1_zip` char(5) NOT NULL,
  `parent1_email` varchar(100) NOT NULL,
  `parent1_altPhone` char(14) NOT NULL,
  `parent2_name` varchar(100) DEFAULT NULL,
  `parent2_phone` char(14) DEFAULT NULL,
  `parent2_address` varchar(100) DEFAULT NULL,
  `parent2_city` varchar(50) DEFAULT NULL,
  `parent2_state` char(2) DEFAULT NULL,
  `parent2_zip` char(5) DEFAULT NULL,
  `parent2_email` varchar(100) DEFAULT NULL,
  `parent2_altPhone` char(14) DEFAULT NULL,
  `emergency_name1` varchar(100) NOT NULL,
  `emergency_relationship1` varchar(50) NOT NULL,
  `emergency_phone1` char(14) NOT NULL,
  `emergency_name2` varchar(100) DEFAULT NULL,
  `emergency_relationship2` varchar(50) DEFAULT NULL,
  `emergency_phone2` char(14) DEFAULT NULL,
  `authorized_pu` varchar(255) NOT NULL,
  `not_authorized_pu` varchar(255) DEFAULT NULL,
  `primary_language` varchar(50) NOT NULL,
  `hispanic_latino_spanish` enum('yes','no') NOT NULL,
  `race` enum('Caucasian','Black/African American','Native Indian/Alaska Native','Native Hawaiian/Pacific Islander','Asian','Multiracial','Other') NOT NULL,
  `num_unemployed` int(11) DEFAULT NULL,
  `num_retired` int(11) DEFAULT NULL,
  `num_unemployed_student` int(11) DEFAULT NULL,
  `num_employed_fulltime` int(11) DEFAULT NULL,
  `num_employed_parttime` int(11) DEFAULT NULL,
  `num_employed_student` int(11) DEFAULT NULL,
  `income` varchar(20) NOT NULL,
  `other_programs` varchar(255) DEFAULT NULL,
  `lunch` enum('free','reduced','neither') NOT NULL,
  `needs_transportation` enum('needs-transportation','transports-themselves') NOT NULL,
  `participation` enum('yes','no') NOT NULL,
  `parent_initials` varchar(10) NOT NULL,
  `signature` varchar(100) NOT NULL,
  `signature_date` date NOT NULL,
  `waiver_child_name` varchar(100) NOT NULL,
  `waiver_dob` date NOT NULL,
  `waiver_parent_name` varchar(100) NOT NULL,
  `waiver_provider_name` varchar(100) NOT NULL,
  `waiver_provider_address` varchar(255) NOT NULL,
  `waiver_phone_and_fax` varchar(50) NOT NULL,
  `waiver_signature` varchar(100) NOT NULL,
  `waiver_date` date NOT NULL,
  `child_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dbBrainBuildersRegistrationForm`
--

INSERT INTO `dbBrainBuildersRegistrationForm` (`id`, `child_first_name`, `child_last_name`, `gender`, `school_name`, `grade`, `birthdate`, `child_address`, `child_city`, `child_state`, `child_zip`, `child_medical_allergies`, `child_food_avoidances`, `parent1_name`, `parent1_phone`, `parent1_address`, `parent1_city`, `parent1_state`, `parent1_zip`, `parent1_email`, `parent1_altPhone`, `parent2_name`, `parent2_phone`, `parent2_address`, `parent2_city`, `parent2_state`, `parent2_zip`, `parent2_email`, `parent2_altPhone`, `emergency_name1`, `emergency_relationship1`, `emergency_phone1`, `emergency_name2`, `emergency_relationship2`, `emergency_phone2`, `authorized_pu`, `not_authorized_pu`, `primary_language`, `hispanic_latino_spanish`, `race`, `num_unemployed`, `num_retired`, `num_unemployed_student`, `num_employed_fulltime`, `num_employed_parttime`, `num_employed_student`, `income`, `other_programs`, `lunch`, `needs_transportation`, `participation`, `parent_initials`, `signature`, `signature_date`, `waiver_child_name`, `waiver_dob`, `waiver_parent_name`, `waiver_provider_name`, `waiver_provider_address`, `waiver_phone_and_fax`, `waiver_signature`, `waiver_date`, `child_id`) VALUES
(1, 'vmschild', 'vmsroot', 'Male', 'school', '10', '2012-10-13', 'address', 'city', 'VA', '22401', NULL, NULL, 'vmsroot', '(888) 888-8888', 'address', 'city', 'VA', '22401', 'vmsroot@email.com', '(888) 888-8888', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'vmsroot', 'vmsroot', '(888) 888-8888', '', '', NULL, '', NULL, 'english', 'yes', 'Caucasian', NULL, NULL, NULL, NULL, NULL, NULL, '20,000', NULL, 'free', 'needs-transportation', 'yes', 'VMS', 'vmsroot', '2025-03-24', 'vmschild', '2012-10-13', 'vmsroot', 'vmsroot', 'address', '(888) 888-8888', 'vmsroot', '2025-03-24', NULL),
(11, 'Henry', 'Smith', 'male', 'School', '7', '2012-10-13', 'Address', 'City', 'VA', '00000', '', '', 'John Smith', '(540) 456-7890', '12343 Test Rd', 'Fredericksburg', 'VA', '22405', 'test@email.com', '', 'Mary Smith', '(540) 342-4826', '12343 Test Rd', 'Fredericksburg', 'VA', '22405', 'a@email.com', '', 'Sam Smith', 'Contact', '(540) 431-1134', '', '', '(540) 456-7890', 'Names', 'Names', 'English', 'yes', 'Caucasian', 1, 1, 1, 2, 1, 1, '20', 'SNAP', 'reduced', 'needs-transportation', 'yes', 'JS', 'Signature', '2025-04-09', 'Henry Smith', '2012-10-13', 'John Smith', 'Name', 'Address', '(888) 888-8888', 'Signature', '2025-04-09', 1),
(12, 'Jane', 'Smith', 'male', 'School', '6', '2012-10-12', 'Address', 'City', 'VA', '00000', '', '', 'John Smith', '(540) 456-7890', '12343 Test Rd', 'Fredericksburg', 'VA', '22405', 'test@email.com', '', 'Mary Smith', '(540) 342-4826', '12343 Test Rd', 'Fredericksburg', 'VA', '22405', 'a@email.com', '', 'Sam Smith', 'Contact', '(540) 431-1134', '', '', '', 'Names', 'Names', 'English', 'no', 'Caucasian', 1, 1, 1, 1, 1, 1, '0', 'SNAP', 'free', 'needs-transportation', 'yes', 'JS', 'Signature', '2025-04-12', 'Jane Smith', '2012-10-13', 'John Smith', 'John Smith', 'Address', '(888) 888-8888', 'Signature', '2025-04-12', 2);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `dbBrainBuildersRegistrationForm`
--
ALTER TABLE `dbBrainBuildersRegistrationForm`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `dbBrainBuildersRegistrationForm`
--
ALTER TABLE `dbBrainBuildersRegistrationForm`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
