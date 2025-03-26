-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Mar 26, 2025 at 05:13 PM
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
-- Table structure for table `dbActivityAttendees`
--

CREATE TABLE `dbActivityAttendees` (
  `activityID` int(11) NOT NULL,
  `attendeeID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dbActualActivityAttendees`
--

CREATE TABLE `dbActualActivityAttendees` (
  `id` int(11) NOT NULL,
  `name` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dbActualActivityForm`
--

CREATE TABLE `dbActualActivityForm` (
  `id` int(11) NOT NULL,
  `activity` varchar(256) NOT NULL,
  `date` date NOT NULL,
  `program` varchar(256) NOT NULL,
  `start_time` varchar(15) NOT NULL,
  `end_time` varchar(15) NOT NULL,
  `start_mile` int(11) NOT NULL,
  `end_mile` int(11) NOT NULL,
  `address` varchar(256) NOT NULL,
  `attend_num` int(11) NOT NULL,
  `volstaff_num` int(11) NOT NULL,
  `materials_used` text NOT NULL,
  `meal_info` enum('meal_provided','meal_paid','no_meal') NOT NULL,
  `act_costs` text NOT NULL,
  `act_benefits` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dbActualActivityForm`
--

INSERT INTO `dbActualActivityForm` (`id`, `activity`, `date`, `program`, `start_time`, `end_time`, `start_mile`, `end_mile`, `address`, `attend_num`, `volstaff_num`, `materials_used`, `meal_info`, `act_costs`, `act_benefits`) VALUES
(0, 'Test Activity', '2025-03-01', 'test', 'test', 'test', 1, 10, 'test', 25, 5, 'test', 'meal_provided', 'test', 'test');

-- --------------------------------------------------------

--
-- Table structure for table `dbAllergies`
--

CREATE TABLE `dbAllergies` (
  `id` int(11) NOT NULL,
  `name` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dbAngelGiftForm`
--

CREATE TABLE `dbAngelGiftForm` (
  `id` int(11) NOT NULL,
  `child_id` int(11) NOT NULL,
  `email` varchar(256) NOT NULL,
  `parent_name` varchar(256) NOT NULL,
  `phone` int(12) NOT NULL,
  `child_name` varchar(256) NOT NULL,
  `gender` varchar(6) NOT NULL,
  `age` int(5) NOT NULL,
  `pants_size` varchar(5) DEFAULT NULL,
  `shirt_size` varchar(5) DEFAULT NULL,
  `shoe_size` int(2) DEFAULT NULL,
  `coat_size` varchar(5) DEFAULT NULL,
  `underwear_size` varchar(5) DEFAULT NULL,
  `sock_size` int(2) DEFAULT NULL,
  `wants` text NOT NULL,
  `interests` text NOT NULL,
  `photo_release` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dbAnimals`
--

CREATE TABLE `dbAnimals` (
  `id` int(11) NOT NULL,
  `child_id` int(11) NOT NULL,
  `odhs_id` varchar(11) NOT NULL,
  `name` varchar(256) NOT NULL,
  `breed` varchar(256) DEFAULT NULL,
  `age` int(5) NOT NULL,
  `gender` varchar(6) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `spay_neuter_done` varchar(3) NOT NULL,
  `spay_neuter_date` date DEFAULT NULL,
  `rabies_given_date` date NOT NULL,
  `rabies_due_date` date DEFAULT NULL,
  `heartworm_given_date` date NOT NULL,
  `heartworm_due_date` date DEFAULT NULL,
  `distemper1_given_date` date NOT NULL,
  `distemper1_due_date` date DEFAULT NULL,
  `distemper2_given_date` date NOT NULL,
  `distemper2_due_date` date DEFAULT NULL,
  `distemper3_given_date` date NOT NULL,
  `distemper3_due_date` date DEFAULT NULL,
  `microchip_done` varchar(3) NOT NULL,
  `archived` varchar(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dbAssistance`
--

CREATE TABLE `dbAssistance` (
  `id` int(11) NOT NULL,
  `assistance` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dbAssistance`
--

INSERT INTO `dbAssistance` (`id`, `assistance`) VALUES
(1, 'SNAP'),
(2, 'SSI');

-- --------------------------------------------------------

--
-- Table structure for table `dbAttendance`
--

CREATE TABLE `dbAttendance` (
  `id` int(11) NOT NULL,
  `route_id` int(11) NOT NULL,
  `participant_id` int(11) NOT NULL,
  `participant_type` enum('volunteer','attendee') NOT NULL,
  `attendance_date` date NOT NULL,
  `is_present` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dbAttendees`
--

CREATE TABLE `dbAttendees` (
  `attendee_id` int(11) NOT NULL,
  `name` varchar(256) NOT NULL,
  `route_id` int(11) NOT NULL,
  `child_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dbAuthorizedStatus`
--

CREATE TABLE `dbAuthorizedStatus` (
  `id` int(11) NOT NULL,
  `first_name` varchar(256) NOT NULL,
  `last_name` varchar(256) NOT NULL,
  `authorized` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dbAvailability`
--

CREATE TABLE `dbAvailability` (
  `id` int(11) NOT NULL,
  `form_id` int(11) NOT NULL,
  `day` varchar(256) NOT NULL,
  `morning` tinyint(1) NOT NULL,
  `afternoon` tinyint(1) NOT NULL,
  `evening` tinyint(1) NOT NULL,
  `specific_time` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `comments` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
  `waiver_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dbBrainBuildersRegistrationForm`
--

INSERT INTO `dbBrainBuildersRegistrationForm` (`id`, `child_first_name`, `child_last_name`, `gender`, `school_name`, `grade`, `birthdate`, `child_address`, `child_city`, `child_state`, `child_zip`, `child_medical_allergies`, `child_food_avoidances`, `parent1_name`, `parent1_phone`, `parent1_address`, `parent1_city`, `parent1_state`, `parent1_zip`, `parent1_email`, `parent1_altPhone`, `parent2_name`, `parent2_phone`, `parent2_address`, `parent2_city`, `parent2_state`, `parent2_zip`, `parent2_email`, `parent2_altPhone`, `emergency_name1`, `emergency_relationship1`, `emergency_phone1`, `emergency_name2`, `emergency_relationship2`, `emergency_phone2`, `authorized_pu`, `not_authorized_pu`, `primary_language`, `hispanic_latino_spanish`, `race`, `num_unemployed`, `num_retired`, `num_unemployed_student`, `num_employed_fulltime`, `num_employed_parttime`, `num_employed_student`, `income`, `other_programs`, `lunch`, `needs_transportation`, `participation`, `parent_initials`, `signature`, `signature_date`, `waiver_child_name`, `waiver_dob`, `waiver_parent_name`, `waiver_provider_name`, `waiver_provider_address`, `waiver_phone_and_fax`, `waiver_signature`, `waiver_date`) VALUES
(1, 'vmschild', 'vmsroot', 'Male', 'school', '10', '2012-10-13', 'address', 'city', 'VA', '22401', NULL, NULL, 'vmsroot', '(888) 888-8888', 'address', 'city', 'VA', '22401', 'vmsroot@email.com', '(888) 888-8888', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'vmsroot', 'vmsroot', '(888) 888-8888', '', '', NULL, '', NULL, 'english', 'yes', 'Caucasian', NULL, NULL, NULL, NULL, NULL, NULL, '20,000', NULL, 'free', 'needs-transportation', 'yes', 'VMS', 'vmsroot', '2025-03-24', 'vmschild', '2012-10-13', 'vmsroot', 'vmsroot', 'address', '(888) 888-8888', 'vmsroot', '2025-03-24');

-- --------------------------------------------------------

--
-- Table structure for table `dbChildren`
--

CREATE TABLE `dbChildren` (
  `id` int(11) NOT NULL,
  `family_id` int(11) NOT NULL,
  `first_name` varchar(256) NOT NULL,
  `last_name` varchar(256) NOT NULL,
  `dob` date NOT NULL,
  `gender` varchar(6) NOT NULL,
  `medical_notes` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `address` varchar(256) NOT NULL,
  `neighborhood` varchar(256) NOT NULL,
  `city` varchar(256) NOT NULL,
  `state` varchar(256) NOT NULL,
  `zip` varchar(256) NOT NULL,
  `school` varchar(256) NOT NULL,
  `grade` varchar(25) NOT NULL,
  `is_hispanic` tinyint(1) NOT NULL,
  `race` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dbChildren`
--

INSERT INTO `dbChildren` (`id`, `family_id`, `first_name`, `last_name`, `dob`, `gender`, `medical_notes`, `notes`, `address`, `neighborhood`, `city`, `state`, `zip`, `school`, `grade`, `is_hispanic`, `race`) VALUES
(1, 4, 'Henry', 'Smith', '2008-05-20', 'male', 'N/A', 'N/A', '12343 Test Road', 'Apple Creek', 'Fredericksburg', 'VA', '22405', 'Smith High School', '10', 0, 'Caucasian'),
(2, 4, 'Jane', 'Smith', '2012-11-01', 'female', 'Peanut Allergy', 'N/A', '12343 Test Road', 'Apple Creek', 'Fredericksburg', 'VA', '22405', 'Smith Middle School', '7', 0, 'Caucasian'),
(3, 4, 'Jack', 'Smith', '2016-09-13', 'male', 'Caucasian', 'N/A', 'Apple Creek', 'N/A', '12343 Test Road', 'Fredericksburg', 'VA', '22405', 'Smith Elementary School', 3, '0'),
(4, 5, 'Mathew', 'Anderson', '2018-05-05', 'male', 'N/A', 'N/A', '7465 Orchard Drive', 'Orchard Run', 'Stafford', 'VA', '22554', 'Anderson High School', 'Kindergarten', 0, 'Multiracial'),
(5, 6, 'Thomas', 'Johnson', '2008-04-05', 'male', 'None', 'No', '125 Fox Drive', 'Fox Woods', 'Stafford', 'VA', '22554', 'Johnson High School', '10', 0, 'Caucasian'),
(6, 7, 'Lucy', 'Ramirez', '2009-05-19', 'female', 'n/a', 'n/a', '123 Oakland Drive', 'Oakland Plaza', 'Stafford', 'VA', '22555', 'Johnson High School', '11', 1, 'Multiracial'),
(7, 7, 'Adrian', 'Ramirez', '2007-02-02', 'male', 'n/a', 'n/a', '123 Oakland Drive', 'Oakland Plaza', 'Stafford', 'VA', '22554', 'Johnson High School', '9', 1, 'Multiracial'),
(8, 8, 'Daniel', 'Garcia', '2013-05-29', 'male', 'n/a', 'n/a', '123 Creek Road', 'Apple Creek', 'Fredericksburg', 'VA', '22405', 'Smith Middle School', '7', 1, 'Multiracial'),
(9, 9, 'William', 'Parker', '2013-01-23', 'male', 'n/a', 'n/a', '456 Buffalo Road', 'Buffalo Hills', 'Fredericksburg', 'VA', '22401', 'Buffalo Middle School', '7', 0, 'Black/African American'),
(10, 9, 'Natalie', 'Parker', '2014-02-27', 'female', 'n/a', 'n/a', '456 Buffalo Road', 'Buffalo Hills', 'Fredericksburg', 'VA', '22401', 'Buffalo Middle School', '6', 0, 'Black/African American'),
(11, 10, 'Louise', 'Martin', '2012-01-02', 'female', 'none', 'none', '7001 Orchard Drive', 'Orchard Run', 'Stafford', 'VA', '22554', 'Anderson High School', '7', 0, 'Caucasian');

-- --------------------------------------------------------

--
-- Table structure for table `dbEventMedia`
--

CREATE TABLE `dbEventMedia` (
  `id` int(11) NOT NULL,
  `eventID` int(11) NOT NULL,
  `url` text NOT NULL,
  `type` text NOT NULL,
  `format` text NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

-- --------------------------------------------------------

--
-- Table structure for table `dbEventsServices`
--

CREATE TABLE `dbEventsServices` (
  `eventID` int(11) NOT NULL,
  `serviceID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dbEventVolunteers`
--

CREATE TABLE `dbEventVolunteers` (
  `eventID` int(11) NOT NULL,
  `userID` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dbFamily`
--

CREATE TABLE `dbFamily` (
  `id` int(11) NOT NULL,
  `firstName` varchar(256) NOT NULL,
  `lastName` varchar(256) NOT NULL,
  `birthdate` varchar(256) NOT NULL,
  `address` varchar(256) NOT NULL,
  `city` varchar(256) NOT NULL,
  `state` varchar(256) NOT NULL,
  `zip` varchar(256) NOT NULL,
  `email` varchar(256) NOT NULL,
  `phone` varchar(256) NOT NULL,
  `phoneType` varchar(256) NOT NULL,
  `secondaryPhone` varchar(256) NOT NULL,
  `secondaryPhoneType` varchar(256) NOT NULL,
  `firstName2` varchar(256) DEFAULT NULL,
  `lastName2` varchar(256) DEFAULT NULL,
  `birthdate2` varchar(256) DEFAULT NULL,
  `address2` varchar(256) DEFAULT NULL,
  `city2` varchar(256) DEFAULT NULL,
  `state2` varchar(256) DEFAULT NULL,
  `zip2` varchar(256) DEFAULT NULL,
  `email2` varchar(256) DEFAULT NULL,
  `phone2` varchar(256) DEFAULT NULL,
  `phoneType2` varchar(256) DEFAULT NULL,
  `secondaryPhone2` varchar(256) DEFAULT NULL,
  `secondaryPhoneType2` varchar(256) DEFAULT NULL,
  `econtactFirstName` varchar(256) NOT NULL,
  `econtactLastName` varchar(256) NOT NULL,
  `econtactPhone` varchar(256) NOT NULL,
  `econtactRelation` varchar(256) DEFAULT NULL,
  `password` text NOT NULL,
  `securityQuestion` text NOT NULL,
  `securityAnswer` text NOT NULL,
  `isArchived` tinyint(1) NOT NULL DEFAULT 0,
  `neighborhood` varchar(256) NOT NULL,
  `isHispanic` tinyint(1) NOT NULL,
  `race` varchar(256) NOT NULL,
  `income` varchar(256) NOT NULL,
  `neighborhood2` varchar(256) NOT NULL,
  `isHispanic2` tinyint(1) NOT NULL,
  `race2` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dbFamily`
--

INSERT INTO `dbFamily` (`id`, `firstName`, `lastName`, `birthdate`, `address`, `city`, `state`, `zip`, `email`, `phone`, `phoneType`, `secondaryPhone`, `secondaryPhoneType`, `firstName2`, `lastName2`, `birthdate2`, `address2`, `city2`, `state2`, `zip2`, `email2`, `phone2`, `phoneType2`, `secondaryPhone2`, `secondaryPhoneType2`, `econtactFirstName`, `econtactLastName`, `econtactPhone`, `econtactRelation`, `password`, `securityQuestion`, `securityAnswer`, `isArchived`, `neighborhood`, `isHispanic`, `race`, `income`, `neighborhood2`, `isHispanic2`, `race2`) VALUES
(1, 'VMS', 'ROOT', '2003-01-01', '1234 road st', 'Fredericksburg', 'VA', '22401', 'vmsroot@gmail.com', '1231231234', 'Mobile', '3213214321', 'Mobile', 'John', 'Smith', '2003-02-02', '1234 road st', 'Fredericksburg', 'VA', '22401', 'johnsmith@gmail.com', '5675675678', 'Mobile', '7897897891', 'Mobile', 'Jane', 'Smith', '3453453456', 'friend', '$2y$10$.3p8xvmUqmxNztEzMJQRBesLDwdiRU3xnt/HOcJtsglwsbUk88VTO', 'Whats 9+10?', '$2y$10$RGQ3P7KOXfR2m1a2z6Tr7ekssfMzboKrt7TsmLjaalfeHEpKX0GUG', 0, '', 0, '', '', '', 0, ''),
(4, 'John', 'Smith', '1970-10-06', '12343 Test Rd', 'Fredericksburg', 'VA', '22405', 'test@email.com', '(540) 456-7890', 'cellphone', '(540) 654-0987', 'home', 'Mary', 'Smith', '1970-02-01', '12343 Test Rd', 'Fredericksburg', '--', '22405', 'a@email.com', '(540) 342-4826', 'cellphone', '', '', 'Sam', 'Smith', '(540) 431-1134', 'Mother', '$2y$10$2fF/.k6unIjmLLhKSE3lbOLS4jFwC7J9yWm3AmEAYEH5EBqtqENDW', 'a', '$2y$10$2KwKjMOolrxhDx5f/A5Cgud.oweNFqPo9MgbC7RiMSc7emM35F4i2', 0, 'Apple Creek', 0, 'Caucasian', '$15,000 - $24,999', 'Test Neighborhood', 0, 'Caucasian'),
(5, ' Lucy', 'Anderson', '1996-01-13', '7465 Orchard Drive', 'Stafford', 'VA', '22554', 'test2@email.com', '(540) 836-2826', 'cellphone', '(540) 766-9872', 'home', 'John', 'Anderson', '1995-09-04', '7465 Orchard Drive', 'Stafford', 'VA', '22554', '', '', '', '', '', 'Mary', 'Nelson', '(540) 836-2826', 'Sister', '$2y$10$r85rkJgl0NEj70fW7eqNFOmsfKRTIAsTDQ/OjQmq7dEe852BAlHk2', 'a', '$2y$10$o6GD2exAi7TVaqLhrkW5IeDfNqMIl9F2RSiumGuIhPbdbR5o3F6B6', 0, 'Orchard Run', 0, 'Multiracial', '$25,000 - $34,999', 'Orchard Run', 0, 'Caucasian'),
(6, ' Henry', 'Johnson', '1984-11-11', '125 Fox Drive', 'Stafford', 'VA', '22554', 'test3@email.com', '(540) 472-2826', 'home', '(540) 272-2222', 'cellphone', 'Sarah', 'Johnson', '1985-03-07', '125 Fox Drive', 'Stafford', 'VA', '22554', '', '', '', '', '', 'Ellie', 'Johnson', '(540) 837-2827', 'Mother', '$2y$10$DMbCinoOD8nh4LZjUZy4aex0lOCvJBxsMauipPCw7SrtAJO4t0Zfm', 'a', '$2y$10$AihOMEcCZIwBJHnL8J1OP.lWkEbluPp90DtMppM/PTuMKnPEl8hQ.', 0, 'Fox Woods', 0, 'Caucasian', '$15,000 - $24,999', 'Fox Woods', 0, 'Caucasian'),
(7, ' Mary', 'Ramirez', '1985-06-28', '123 Oakland Drive', 'Stafford', 'VA', '22554', 'test4@email.com', '(540) 826-2826', 'home', '(540) 382-2892', 'cellphone', '', '', '', '', '', '', '--', '', '', '', '', '', 'Carmen', 'Ramirez', '(540) 872-2828', 'Sister', '$2y$10$3ZZDNc8a8xaOWOTjzolRBuI4PeZbdcp9kz6IsigvJ4VoxhiDf3Mpa', 'a', '$2y$10$LfzjuA98LW1fnLwg6JxqI.IvnmBYnbwzD7kfSuvlgV3OyLQi.9cmK', 0, 'Oakland Plaza', 1, 'Multiracial', '$15,000 - $24,999', '', 0, ''),
(8, ' Amy', 'Garcia', '1990-11-19', '123 Creek Road', 'Fredericksburg', 'VA', '22405', 'test5@email.com', '(540) 836-2826', 'cellphone', '(540) 736-2828', 'home', 'James', 'Garcia', '1990-11-11', '123 Creek Road', 'Fredericksburg', 'VA', '22405', '', '', '', '', '', 'Lucas', 'Garcia', '(540) 872-2828', 'Brother', '$2y$10$sGzKlh04MQvgMpLUFB/SAeTFJTzjswYdxvS9DRTX8ukYcgoGTeUBO', 'a', '$2y$10$aqKzKx9h2opN/NHM4IUIw.s19XsjTN0Fr0yjOKOiWdzPvwKZCBxUK', 0, 'Apple Creek', 1, 'Multiracial', '$15,000 - $24,999', 'Apple Creek', 1, 'Multiracial'),
(9, ' David', 'Parker', '1989-09-03', '456 Buffalo Road', 'Fredericksburg', 'VA', '22401', 'test6@email.com', '(540) 736-8262', 'cellphone', '(540) 372-2873', 'home', 'Olivia', 'Parker', '1989-06-12', '456 Buffalo Road', 'Fredericksburg', 'VA', '22401', '', '', '', '', '', 'Nathan', 'Johnson', '(540) 583-2827', 'Friend', '$2y$10$zLlP7y5ZJDvXaucw8FSDm.cerMGKN6gbStYYRJiko0AtR6o3CXGC2', 'a', '$2y$10$60I8CN9wnwHuIOODacJ/muVoGa4d2dAZNARQacyhYH0Q8kdKLde3i', 0, 'Buffalo Hills', 0, 'Black/African American', '$25,000 - $34,999', 'Buffalo Hills', 0, 'Black/African American'),
(10, ' Arthur', 'Martin', '1988-05-05', '7001 Orchard Drive', 'Stafford', 'VA', '22554', 'test7@email.com', '(540) 473-2872', 'cellphone', '(540) 836-8287', 'home', 'Victoria', 'Martin', '1985-09-28', '7001 Orchard Drive', 'Stafford', 'VA', '22554', '', '', '', '', '', 'John', 'Gold', '(540) 398-2828', 'Friend', '$2y$10$OxtEtoGNanHiIj/KtGaioOHX0MZ45MhExzryxveWfwOM9WCTOL.Cq', 'a', '$2y$10$FBsuj/EXPWUIOgISSBZOzOEdKIrcPrPptC/0..uMAwIIr0ovm4NZ6', 0, 'Orchard Run', 0, 'Caucasian', '$35,000 - $49,999', 'Orchard Run', 0, 'Caucasian');

-- --------------------------------------------------------

--
-- Table structure for table `dbFamily_Assistance`
--

CREATE TABLE `dbFamily_Assistance` (
  `id` int(11) NOT NULL,
  `family_id` int(11) NOT NULL,
  `assistance_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dbFamily_Assistance`
--

INSERT INTO `dbFamily_Assistance` (`id`, `family_id`, `assistance_id`) VALUES
(1, 4, 1),
(2, 6, 1),
(3, 6, 2),
(4, 8, 1);

-- --------------------------------------------------------

--
-- Table structure for table `dbFamily_Languages`
--

CREATE TABLE `dbFamily_Languages` (
  `id` int(11) NOT NULL,
  `family_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dbFamily_Languages`
--

INSERT INTO `dbFamily_Languages` (`id`, `family_id`, `language_id`) VALUES
(5, 4, 4),
(6, 5, 4),
(7, 6, 4),
(8, 7, 5),
(9, 7, 4),
(10, 8, 4),
(11, 8, 5),
(12, 9, 4),
(13, 10, 4),
(14, 10, 6);

-- --------------------------------------------------------

--
-- Table structure for table `dbFieldTripWaiverForm`
--

CREATE TABLE `dbFieldTripWaiverForm` (
  `id` int(11) NOT NULL,
  `child_id` int(11) NOT NULL,
  `child_name` varchar(256) NOT NULL,
  `gender` varchar(6) NOT NULL,
  `birth_date` date NOT NULL,
  `neighborhood` varchar(256) NOT NULL,
  `school` varchar(256) NOT NULL,
  `child_address` varchar(256) NOT NULL,
  `child_city` varchar(100) NOT NULL,
  `child_state` varchar(100) NOT NULL,
  `child_zip` varchar(10) NOT NULL,
  `parent_email` varchar(256) NOT NULL,
  `emgcy_contact_name_1` varchar(256) NOT NULL,
  `emgcy_contact1_rship` varchar(100) NOT NULL,
  `emgcy_contact1_phone` varchar(15) NOT NULL,
  `emgcy_contact_name_2` varchar(256) NOT NULL,
  `emgcy_contact2_rship` varchar(100) NOT NULL,
  `emgcy_contact2_phone` varchar(15) NOT NULL,
  `medical_insurance_company` varchar(256) NOT NULL,
  `policy_number` varchar(50) NOT NULL,
  `photo_waiver_signature` varchar(256) NOT NULL,
  `photo_waiver_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dbformstatus`
--

CREATE TABLE `dbformstatus` (
  `id` int(11) NOT NULL,
  `form_name` varchar(255) NOT NULL,
  `is_published` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dbformstatus`
--

INSERT INTO `dbformstatus` (`id`, `form_name`, `is_published`) VALUES
(1, 'Holiday Meal Bag', 1),
(2, 'School Supplies Form', 1),
(3, 'Spring Break', 1),
(4, 'Angel Gifts Wish List', 1),
(5, 'Child Care Waiver', 1),
(6, 'Field Trip Waiver', 1),
(7, 'Program Interest', 1),
(8, 'Brain Builders Student Registration', 1),
(9, 'Brain Builders Holiday Party', 1),
(10, 'Summer Junction Registration', 1),
(12, 'Actual Activity', 1),
(13, 'Program Review', 1);

-- --------------------------------------------------------

--
-- Table structure for table `dbHolidayMealBagForm`
--

CREATE TABLE `dbHolidayMealBagForm` (
  `id` int(11) NOT NULL,
  `family_id` int(11) NOT NULL,
  `email` varchar(256) NOT NULL,
  `household_size` int(11) NOT NULL,
  `meal_bag` varchar(25) NOT NULL,
  `name` varchar(256) NOT NULL,
  `address` varchar(256) NOT NULL,
  `phone` char(10) NOT NULL,
  `photo_release` tinyint(4) NOT NULL
) ;

-- --------------------------------------------------------

--
-- Table structure for table `dbLanguages`
--

CREATE TABLE `dbLanguages` (
  `id` int(11) NOT NULL,
  `language` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dbLanguages`
--

INSERT INTO `dbLanguages` (`id`, `language`) VALUES
(4, 'English'),
(5, 'Spanish'),
(6, 'French'),
(7, 'vmsroot');

-- --------------------------------------------------------

--
-- Table structure for table `dbLocations`
--

CREATE TABLE `dbLocations` (
  `id` int(11) NOT NULL,
  `name` varchar(256) NOT NULL,
  `address` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dbLocationsNEW`
--

CREATE TABLE `dbLocationsNEW` (
  `id` int(11) NOT NULL,
  `name` varchar(256) NOT NULL,
  `address` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dbLocationsServices`
--

CREATE TABLE `dbLocationsServices` (
  `locationID` int(11) NOT NULL,
  `serviceID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dbLocationsServicesNEW`
--

CREATE TABLE `dbLocationsServicesNEW` (
  `location_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dbMessages`
--

CREATE TABLE `dbMessages` (
  `id` int(11) NOT NULL,
  `senderID` varchar(256) NOT NULL,
  `recipientID` varchar(256) NOT NULL,
  `title` varchar(256) NOT NULL,
  `body` text NOT NULL,
  `time` varchar(16) NOT NULL,
  `wasRead` tinyint(1) NOT NULL DEFAULT 0,
  `prioritylevel` tinyint(5) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

-- --------------------------------------------------------

--
-- Table structure for table `dbPersonsNEW`
--

CREATE TABLE `dbPersonsNEW` (
  `id` int(11) NOT NULL,
  `first_name` varchar(256) NOT NULL,
  `last_name` varchar(256) NOT NULL,
  `address` varchar(256) DEFAULT NULL,
  `city` varchar(256) DEFAULT NULL,
  `state` varchar(2) DEFAULT NULL,
  `zip` int(11) DEFAULT NULL,
  `phone1` varchar(12) DEFAULT NULL,
  `phone1_type` varchar(256) DEFAULT NULL,
  `phone2` varchar(12) DEFAULT NULL,
  `phone2_type` varchar(256) DEFAULT NULL,
  `birthday` varchar(10) DEFAULT NULL,
  `email` varchar(256) NOT NULL,
  `contact_name` varchar(256) DEFAULT NULL,
  `contact_num` varchar(12) DEFAULT NULL,
  `relation` text DEFAULT NULL,
  `contact_time` text DEFAULT NULL,
  `password` varchar(64) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dbPersonsNEW`
--

INSERT INTO `dbPersonsNEW` (`id`, `first_name`, `last_name`, `address`, `city`, `state`, `zip`, `phone1`, `phone1_type`, `phone2`, `phone2_type`, `birthday`, `email`, `contact_name`, `contact_num`, `relation`, `contact_time`, `password`, `is_active`) VALUES
(1, 'test', 'user', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'vmsroot', NULL, NULL, NULL, NULL, '$2y$10$.3p8xvmUqmxNztEzMJQRBesLDwdiRU3xnt/HOcJtsglwsbUk88VTO', 1);

-- --------------------------------------------------------

--
-- Table structure for table `dbProgramInterestForm`
--

CREATE TABLE `dbProgramInterestForm` (
  `id` int(11) NOT NULL,
  `family_id` int(11) NOT NULL,
  `first_name` varchar(256) NOT NULL,
  `last_name` varchar(256) NOT NULL,
  `address` varchar(256) NOT NULL,
  `city` varchar(256) NOT NULL,
  `neighborhood` varchar(256) NOT NULL,
  `state` varchar(2) NOT NULL,
  `zip` varchar(256) NOT NULL,
  `cell_phone` varchar(10) NOT NULL,
  `home_phone` varchar(10) NOT NULL,
  `email` varchar(256) NOT NULL,
  `child_num` int(11) NOT NULL,
  `child_ages` varchar(256) DEFAULT NULL,
  `adult_num` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dbProgramInterests`
--

CREATE TABLE `dbProgramInterests` (
  `id` int(11) NOT NULL,
  `interest` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dbProgramInterests`
--

INSERT INTO `dbProgramInterests` (`id`, `interest`) VALUES
(1, 'Brain Builders'),
(2, 'Camp Junction'),
(3, 'Stafford County Sheriff’s Office Sports Camp'),
(4, 'STEAM'),
(5, 'YMCA'),
(6, 'Tide Me Over Bags'),
(7, 'English Language Conversation Classes');

-- --------------------------------------------------------

--
-- Table structure for table `dbProgramInterestsForm_ProgramInterests`
--

CREATE TABLE `dbProgramInterestsForm_ProgramInterests` (
  `id` int(11) NOT NULL,
  `form_id` int(11) NOT NULL,
  `interest_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dbProgramInterestsForm_TopicInterests`
--

CREATE TABLE `dbProgramInterestsForm_TopicInterests` (
  `id` int(11) NOT NULL,
  `form_id` int(11) NOT NULL,
  `interest_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dbPrograms`
--

CREATE TABLE `dbPrograms` (
  `id` int(11) NOT NULL,
  `name` text NOT NULL,
  `date` date NOT NULL,
  `start_time` char(5) NOT NULL,
  `start_date` date NOT NULL,
  `end_time` char(5) NOT NULL,
  `end_date` date NOT NULL,
  `description` text NOT NULL,
  `location_id` int(11) NOT NULL,
  `capacity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dbProgramsParticipants`
--

CREATE TABLE `dbProgramsParticipants` (
  `program_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dbProgramsServices`
--

CREATE TABLE `dbProgramsServices` (
  `program_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dbProgramsVolunteers`
--

CREATE TABLE `dbProgramsVolunteers` (
  `program_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dbRace`
--

CREATE TABLE `dbRace` (
  `id` int(11) NOT NULL,
  `name` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dbRoute`
--

CREATE TABLE `dbRoute` (
  `route_id` int(11) NOT NULL,
  `route_direction` varchar(25) NOT NULL,
  `route_name` varchar(25) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dbRoute`
--

INSERT INTO `dbRoute` (`route_id`, `route_direction`, `route_name`) VALUES
(1, 'North', 'Foxwood'),
(2, 'South', 'Meadows'),
(3, 'South', 'Jefferson Place'),
(4, 'South', 'Olde Forge'),
(5, 'South', 'England Run');

-- --------------------------------------------------------

--
-- Table structure for table `dbSchoolSuppliesForm`
--

CREATE TABLE `dbSchoolSuppliesForm` (
  `id` int(11) NOT NULL,
  `child_id` int(11) NOT NULL,
  `email` varchar(256) NOT NULL,
  `child_name` varchar(256) NOT NULL,
  `grade` varchar(25) NOT NULL,
  `school` varchar(256) NOT NULL,
  `bag_pickup_method` text NOT NULL,
  `need_backpack` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dbServices`
--

CREATE TABLE `dbServices` (
  `id` int(11) NOT NULL,
  `name` varchar(256) NOT NULL,
  `type` varchar(256) NOT NULL,
  `duration_years` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dbServicesNEW`
--

CREATE TABLE `dbServicesNEW` (
  `id` int(11) NOT NULL,
  `name` varchar(256) NOT NULL,
  `location` int(11) NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dbSpringBreakCampForm`
--

CREATE TABLE `dbSpringBreakCampForm` (
  `id` int(11) NOT NULL,
  `email` text NOT NULL,
  `student_name` varchar(256) NOT NULL,
  `school_choice` text NOT NULL,
  `isAttending` tinyint(1) NOT NULL,
  `waiver_completed` tinyint(1) NOT NULL,
  `notes` text DEFAULT NULL,
  `child_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dbStaff`
--

CREATE TABLE `dbStaff` (
  `id` int(11) NOT NULL,
  `firstName` varchar(256) NOT NULL,
  `lastName` varchar(256) NOT NULL,
  `birthdate` varchar(256) NOT NULL,
  `address` varchar(256) NOT NULL,
  `email` varchar(256) NOT NULL,
  `phone` varchar(256) NOT NULL,
  `econtactName` varchar(256) NOT NULL,
  `econtactPhone` varchar(256) NOT NULL,
  `jobTitle` varchar(256) NOT NULL,
  `password` varchar(256) NOT NULL,
  `securityQuestion` varchar(256) NOT NULL,
  `securityAnswer` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dbStaff`
--

INSERT INTO `dbStaff` (`id`, `firstName`, `lastName`, `birthdate`, `address`, `email`, `phone`, `econtactName`, `econtactPhone`, `jobTitle`, `password`, `securityQuestion`, `securityAnswer`) VALUES
(1, 'John', 'Doe', '10-13-24', '12 Little Oak Road', 'jdoe@gmail.com', '(555)555-5555', 'Jane Doe', '555-555-5555', 'Teacher', '123', 'question', 'answer');

-- --------------------------------------------------------

--
-- Table structure for table `dbSummerJunctionRegistrationForm`
--

CREATE TABLE `dbSummerJunctionRegistrationForm` (
  `id` int(11) NOT NULL,
  `child_id` int(11) NOT NULL,
  `steam` tinyint(1) DEFAULT NULL,
  `summer_camp` tinyint(1) DEFAULT NULL,
  `child_first_name` varchar(50) NOT NULL,
  `child_last_name` varchar(50) NOT NULL,
  `birthdate` date NOT NULL,
  `grade_completed` int(11) NOT NULL,
  `gender` enum('male','female') NOT NULL,
  `shirt_size` enum('child-xs','child-s','child-m','child-l','child-xl','adult-s','adult-m','adult-l','adult-xl','adult-2x') NOT NULL,
  `neighborhood` varchar(100) NOT NULL,
  `child_address` varchar(100) NOT NULL,
  `child_city` varchar(50) NOT NULL,
  `child_state` char(2) NOT NULL,
  `child_zip` char(5) NOT NULL,
  `child_medical_allergies` varchar(255) DEFAULT NULL,
  `child_food_avoidances` varchar(255) DEFAULT NULL,
  `parent1_first_name` varchar(50) NOT NULL,
  `parent1_last_name` varchar(50) NOT NULL,
  `parent1_address` varchar(100) NOT NULL,
  `parent1_city` varchar(50) NOT NULL,
  `parent1_state` char(2) NOT NULL,
  `parent1_zip` char(5) NOT NULL,
  `parent1_email` varchar(100) NOT NULL,
  `parent1_cell_phone` varchar(15) NOT NULL,
  `parent1_home_phone` varchar(15) DEFAULT NULL,
  `parent1_work_phone` varchar(15) DEFAULT NULL,
  `parent2_first_name` varchar(50) DEFAULT NULL,
  `parent2_last_name` varchar(50) DEFAULT NULL,
  `parent2_address` varchar(100) DEFAULT NULL,
  `parent2_city` varchar(50) DEFAULT NULL,
  `parent2_state` char(2) DEFAULT NULL,
  `parent2_zip` char(5) DEFAULT NULL,
  `parent2_email` varchar(100) DEFAULT NULL,
  `parent2_cell_phone` varchar(15) DEFAULT NULL,
  `parent2_home_phone` varchar(15) DEFAULT NULL,
  `parent2_work_phone` varchar(15) DEFAULT NULL,
  `emergency_contact1_name` varchar(50) NOT NULL,
  `emergency_contact1_relationship` varchar(50) NOT NULL,
  `emergency_contact1_phone` varchar(15) NOT NULL,
  `emergency_contact2_name` varchar(50) DEFAULT NULL,
  `emergency_contact2_relationship` varchar(50) DEFAULT NULL,
  `emergency_contact2_phone` varchar(15) DEFAULT NULL,
  `primary_language` varchar(50) NOT NULL,
  `hispanic_latino_spanish` enum('yes','no') NOT NULL,
  `race` enum('Caucasian','Black/African American','Native Indian/Alaska Native','Native Hawaiian/Pacific Islander','Asian','Multiracial','Other') NOT NULL,
  `num_unemployed` int(11) DEFAULT NULL,
  `num_retired` int(11) DEFAULT NULL,
  `num_unemployed_students` int(11) DEFAULT NULL,
  `num_employed_fulltime` int(11) DEFAULT NULL,
  `num_employed_parttime` int(11) DEFAULT NULL,
  `num_employed_students` int(11) DEFAULT NULL,
  `income` enum('Under 20,000','20,000-40,000','40,001-60,000','60,001-80,000','Over 80,000') NOT NULL,
  `other_programs` varchar(255) NOT NULL,
  `lunch` enum('free','reduced','neither') NOT NULL,
  `insurance` varchar(100) NOT NULL,
  `policy_num` varchar(50) NOT NULL,
  `signature` varchar(255) NOT NULL,
  `signature_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dbTopicInterests`
--

CREATE TABLE `dbTopicInterests` (
  `id` int(11) NOT NULL,
  `interest` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dbTopicInterests`
--

INSERT INTO `dbTopicInterests` (`id`, `interest`) VALUES
(1, 'Legal Services'),
(2, 'Finances'),
(3, 'Tenant Rights'),
(4, 'Computer Skills/Literacy'),
(5, 'Health/Wellness/Nutrition'),
(6, 'Continuing Education'),
(7, 'Parenting'),
(8, 'Mental Health'),
(9, 'Job/Career Guidance'),
(10, 'Citizenship Classes');

-- --------------------------------------------------------

--
-- Table structure for table `dbUnallowedFoods`
--

CREATE TABLE `dbUnallowedFoods` (
  `id` int(11) NOT NULL,
  `name` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dbVolunteerReport`
--

CREATE TABLE `dbVolunteerReport` (
  `report_id` int(11) NOT NULL,
  `volunteer_id` int(11) NOT NULL,
  `activity_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `hours` decimal(5,2) NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dbVolunteers`
--

CREATE TABLE `dbVolunteers` (
  `id` int(11) NOT NULL,
  `email` varchar(256) NOT NULL,
  `password` varchar(256) NOT NULL,
  `securityQuestion` text NOT NULL,
  `securityAnswer` text NOT NULL,
  `firstName` varchar(256) NOT NULL,
  `middleInitial` varchar(1) NOT NULL,
  `lastName` varchar(256) NOT NULL,
  `address` varchar(256) NOT NULL,
  `city` varchar(256) NOT NULL,
  `state` varchar(2) NOT NULL,
  `zip` int(11) NOT NULL,
  `homePhone` varchar(256) NOT NULL,
  `cellPhone` varchar(256) NOT NULL,
  `age` int(11) NOT NULL,
  `birthDate` date NOT NULL,
  `hasDriversLicense` tinyint(1) NOT NULL,
  `transportation` varchar(256) DEFAULT NULL,
  `emergencyContact1Name` varchar(256) NOT NULL,
  `emergencyContact1Relation` varchar(256) NOT NULL,
  `emergencyContact1Phone` varchar(256) NOT NULL,
  `emergencyContact2Name` varchar(256) DEFAULT NULL,
  `emergencyContact2Relation` varchar(256) DEFAULT NULL,
  `emergencyContact2Phone` varchar(256) DEFAULT NULL,
  `allergies` varchar(256) DEFAULT NULL,
  `sunStart` varchar(5) DEFAULT NULL,
  `sunEnd` varchar(5) DEFAULT NULL,
  `monStart` varchar(5) DEFAULT NULL,
  `monEnd` varchar(5) DEFAULT NULL,
  `tueStart` varchar(5) DEFAULT NULL,
  `tueEnd` varchar(5) DEFAULT NULL,
  `wedStart` varchar(5) DEFAULT NULL,
  `wedEnd` varchar(5) DEFAULT NULL,
  `thurStart` varchar(5) DEFAULT NULL,
  `thurEnd` varchar(5) DEFAULT NULL,
  `friStart` varchar(5) DEFAULT NULL,
  `friEnd` varchar(5) DEFAULT NULL,
  `satStart` varchar(5) DEFAULT NULL,
  `satEnd` varchar(5) DEFAULT NULL,
  `dateAvailable` date DEFAULT NULL,
  `minHours` int(11) NOT NULL,
  `maxHours` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dbVolunteers`
--

INSERT INTO `dbVolunteers` (`id`, `email`, `password`, `securityQuestion`, `securityAnswer`, `firstName`, `middleInitial`, `lastName`, `address`, `city`, `state`, `zip`, `homePhone`, `cellPhone`, `age`, `birthDate`, `hasDriversLicense`, `transportation`, `emergencyContact1Name`, `emergencyContact1Relation`, `emergencyContact1Phone`, `emergencyContact2Name`, `emergencyContact2Relation`, `emergencyContact2Phone`, `allergies`, `sunStart`, `sunEnd`, `monStart`, `monEnd`, `tueStart`, `tueEnd`, `wedStart`, `wedEnd`, `thurStart`, `thurEnd`, `friStart`, `friEnd`, `satStart`, `satEnd`, `dateAvailable`, `minHours`, `maxHours`) VALUES
(1, 'volunteer@mail.com', '$2y$10$EZCNkQflMinx5sgoMbJwN.sqGKOlL8fnHiGGhQ3wXKU3uGMkjOx6a', 'Whats 9+10', '$2y$10$RGQ3P7KOXfR2m1a2z6Tr7ekssfMzboKrt7TsmLjaalfeHEpKX0GUG', 'Mr', 'A', 'Volunteer', '123 road st', 'Fredericksburg', 'VA', 22401, '1112223333', '2223334444', 20, '1999-01-01', 0, NULL, 'John', 'Smith', '1112223333', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 10, 20),
(4, 'vmsroot', 'vmsroot', 'vmsroot', 'vmsroot', 'vmsroot', 'm', 'vmsroot', 'vmsroot', 'vmsroot', 'va', 22401, '0', '', 34, '1990-10-10', 0, '', 'vmsroot', 'vmsroot', '(888) 888-8888', '', '', '', 'Nut Allergy', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', '2025-03-15', 0, 0),
(5, 'vmsroot@gmail.com', '$2y$10$otHIJ606hjtrlItT2oUopuZZGMl.dNV.gqH.Ag3G.meUQ3YXTFBXu', 'vmsroot', 'vmsroot', 'vmsroot', 'v', 'vmsroot', 'vmsroot', 'vmsroot', 'vr', 22401, '0', '', 25, '2000-01-01', 0, '', 'vmsroot', 'vmsroot', '(888) 888-8888', '', '', '', 'Gluten Allergy', '', '', '', '', '', '', '', '', '', '', '', '', '', '0', NULL, 0, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `dbActivityAttendees`
--
ALTER TABLE `dbActivityAttendees`
  ADD PRIMARY KEY (`activityID`,`attendeeID`),
  ADD KEY `attendeeID` (`attendeeID`);

--
-- Indexes for table `dbActualActivityAttendees`
--
ALTER TABLE `dbActualActivityAttendees`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dbActualActivityForm`
--
ALTER TABLE `dbActualActivityForm`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dbAllergies`
--
ALTER TABLE `dbAllergies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dbAngelGiftForm`
--
ALTER TABLE `dbAngelGiftForm`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FKangelgift` (`child_id`);

--
-- Indexes for table `dbAnimals`
--
ALTER TABLE `dbAnimals`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dbAssistance`
--
ALTER TABLE `dbAssistance`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dbAttendance`
--
ALTER TABLE `dbAttendance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `route_id` (`route_id`);

--
-- Indexes for table `dbAttendees`
--
ALTER TABLE `dbAttendees`
  ADD PRIMARY KEY (`attendee_id`),
  ADD KEY `route_id` (`route_id`),
  ADD KEY `child_id` (`child_id`);

--
-- Indexes for table `dbAuthorizedStatus`
--
ALTER TABLE `dbAuthorizedStatus`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dbAvailability`
--
ALTER TABLE `dbAvailability`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FKprogramInterestForm_Availability` (`form_id`);

--
-- Indexes for table `dbBrainBuildersHolidayPartyForm`
--
ALTER TABLE `dbBrainBuildersHolidayPartyForm`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_family_id` (`family_id`);

--
-- Indexes for table `dbBrainBuildersRegistrationForm`
--
ALTER TABLE `dbBrainBuildersRegistrationForm`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dbChildren`
--
ALTER TABLE `dbChildren`
  ADD PRIMARY KEY (`id`),
  ADD KEY `dbChildren_family_id_FK` (`family_id`);

--
-- Indexes for table `dbEventMedia`
--
ALTER TABLE `dbEventMedia`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FKeventID2` (`eventID`);

--
-- Indexes for table `dbEvents`
--
ALTER TABLE `dbEvents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FKlocationID` (`locationID`);

--
-- Indexes for table `dbEventsServices`
--
ALTER TABLE `dbEventsServices`
  ADD PRIMARY KEY (`eventID`,`serviceID`),
  ADD KEY `FKserviceID3` (`serviceID`);

--
-- Indexes for table `dbEventVolunteers`
--
ALTER TABLE `dbEventVolunteers`
  ADD KEY `FKeventID` (`eventID`),
  ADD KEY `FKpersonID` (`userID`);

--
-- Indexes for table `dbFamily`
--
ALTER TABLE `dbFamily`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dbFamily_Assistance`
--
ALTER TABLE `dbFamily_Assistance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FKFamily_Assistance` (`family_id`),
  ADD KEY `FKAssistance_Family` (`assistance_id`);

--
-- Indexes for table `dbFamily_Languages`
--
ALTER TABLE `dbFamily_Languages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FKFamily_Language` (`family_id`),
  ADD KEY `FKLanguage_Family` (`language_id`);

--
-- Indexes for table `dbFieldTripWaiverForm`
--
ALTER TABLE `dbFieldTripWaiverForm`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_field_trip_child_id` (`child_id`);

--
-- Indexes for table `dbformstatus`
--
ALTER TABLE `dbformstatus`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `form_name` (`form_name`);

--
-- Indexes for table `dbHolidayMealBagForm`
--
ALTER TABLE `dbHolidayMealBagForm`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FKfamily_id` (`family_id`);

--
-- Indexes for table `dbLanguages`
--
ALTER TABLE `dbLanguages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dbLocations`
--
ALTER TABLE `dbLocations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dbLocationsNEW`
--
ALTER TABLE `dbLocationsNEW`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dbLocationsServices`
--
ALTER TABLE `dbLocationsServices`
  ADD PRIMARY KEY (`locationID`,`serviceID`),
  ADD KEY `FKserviceID2` (`serviceID`);

--
-- Indexes for table `dbLocationsServicesNEW`
--
ALTER TABLE `dbLocationsServicesNEW`
  ADD KEY `dbLocationsServices_location_id_FK` (`location_id`),
  ADD KEY `dbLocationsServices_service_id_FK` (`service_id`);

--
-- Indexes for table `dbMessages`
--
ALTER TABLE `dbMessages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dbPersons`
--
ALTER TABLE `dbPersons`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dbPersonsNEW`
--
ALTER TABLE `dbPersonsNEW`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dbProgramInterestForm`
--
ALTER TABLE `dbProgramInterestForm`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FKprogramInterestForm_Family` (`family_id`);

--
-- Indexes for table `dbProgramInterests`
--
ALTER TABLE `dbProgramInterests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dbProgramInterestsForm_ProgramInterests`
--
ALTER TABLE `dbProgramInterestsForm_ProgramInterests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FKprogramInterestForm_ProgramInterest` (`form_id`),
  ADD KEY `FKprogramInterest_programInterestForm` (`interest_id`);

--
-- Indexes for table `dbProgramInterestsForm_TopicInterests`
--
ALTER TABLE `dbProgramInterestsForm_TopicInterests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FKprogramInterestForm_topicInterest` (`form_id`),
  ADD KEY `FKtopicInterest_programInterestForm` (`interest_id`);

--
-- Indexes for table `dbPrograms`
--
ALTER TABLE `dbPrograms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `dbPrograms_location_id_FK` (`location_id`);

--
-- Indexes for table `dbProgramsParticipants`
--
ALTER TABLE `dbProgramsParticipants`
  ADD KEY `dbProgramsParticipants_program_id_FK` (`program_id`),
  ADD KEY `dbProgramsParticipants_user_id_FK` (`user_id`);

--
-- Indexes for table `dbProgramsServices`
--
ALTER TABLE `dbProgramsServices`
  ADD KEY `dbProgramsServices_program_id_FK` (`program_id`),
  ADD KEY `dbProgramsServices_service_id_FK` (`service_id`);

--
-- Indexes for table `dbProgramsVolunteers`
--
ALTER TABLE `dbProgramsVolunteers`
  ADD KEY `dbProgramsVolunteers_program_id_FK` (`program_id`),
  ADD KEY `dbProgramsVolunteers_user_id_FK` (`user_id`);

--
-- Indexes for table `dbRace`
--
ALTER TABLE `dbRace`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dbRoute`
--
ALTER TABLE `dbRoute`
  ADD PRIMARY KEY (`route_id`);

--
-- Indexes for table `dbSchoolSuppliesForm`
--
ALTER TABLE `dbSchoolSuppliesForm`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FKschoolsupplies` (`child_id`);

--
-- Indexes for table `dbServices`
--
ALTER TABLE `dbServices`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dbServicesNEW`
--
ALTER TABLE `dbServicesNEW`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dbSpringBreakCampForm`
--
ALTER TABLE `dbSpringBreakCampForm`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_child_id` (`child_id`);

--
-- Indexes for table `dbStaff`
--
ALTER TABLE `dbStaff`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dbSummerJunctionRegistrationForm`
--
ALTER TABLE `dbSummerJunctionRegistrationForm`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FKSummerJunctionRegistration` (`child_id`);

--
-- Indexes for table `dbTopicInterests`
--
ALTER TABLE `dbTopicInterests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dbUnallowedFoods`
--
ALTER TABLE `dbUnallowedFoods`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `dbVolunteerReport`
--
ALTER TABLE `dbVolunteerReport`
  ADD PRIMARY KEY (`report_id`),
  ADD KEY `volunteer_id` (`volunteer_id`),
  ADD KEY `activity_id` (`activity_id`);

--
-- Indexes for table `dbVolunteers`
--
ALTER TABLE `dbVolunteers`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `dbActualActivityAttendees`
--
ALTER TABLE `dbActualActivityAttendees`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dbActualActivityForm`
--
ALTER TABLE `dbActualActivityForm`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `dbAllergies`
--
ALTER TABLE `dbAllergies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dbAngelGiftForm`
--
ALTER TABLE `dbAngelGiftForm`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dbAnimals`
--
ALTER TABLE `dbAnimals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `dbAssistance`
--
ALTER TABLE `dbAssistance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `dbAttendance`
--
ALTER TABLE `dbAttendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dbAttendees`
--
ALTER TABLE `dbAttendees`
  MODIFY `attendee_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dbAuthorizedStatus`
--
ALTER TABLE `dbAuthorizedStatus`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dbAvailability`
--
ALTER TABLE `dbAvailability`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dbBrainBuildersHolidayPartyForm`
--
ALTER TABLE `dbBrainBuildersHolidayPartyForm`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dbBrainBuildersRegistrationForm`
--
ALTER TABLE `dbBrainBuildersRegistrationForm`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `dbChildren`
--
ALTER TABLE `dbChildren`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `dbEventMedia`
--
ALTER TABLE `dbEventMedia`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dbFamily`
--
ALTER TABLE `dbFamily`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `dbFamily_Assistance`
--
ALTER TABLE `dbFamily_Assistance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `dbFamily_Languages`
--
ALTER TABLE `dbFamily_Languages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `dbFieldTripWaiverForm`
--
ALTER TABLE `dbFieldTripWaiverForm`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dbformstatus`
--
ALTER TABLE `dbformstatus`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `dbHolidayMealBagForm`
--
ALTER TABLE `dbHolidayMealBagForm`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dbLanguages`
--
ALTER TABLE `dbLanguages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `dbLocations`
--
ALTER TABLE `dbLocations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `dbLocationsNEW`
--
ALTER TABLE `dbLocationsNEW`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dbMessages`
--
ALTER TABLE `dbMessages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2747;

--
-- AUTO_INCREMENT for table `dbPersonsNEW`
--
ALTER TABLE `dbPersonsNEW`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `dbProgramInterestForm`
--
ALTER TABLE `dbProgramInterestForm`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dbProgramInterests`
--
ALTER TABLE `dbProgramInterests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `dbProgramInterestsForm_ProgramInterests`
--
ALTER TABLE `dbProgramInterestsForm_ProgramInterests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dbProgramInterestsForm_TopicInterests`
--
ALTER TABLE `dbProgramInterestsForm_TopicInterests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dbPrograms`
--
ALTER TABLE `dbPrograms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dbRace`
--
ALTER TABLE `dbRace`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dbRoute`
--
ALTER TABLE `dbRoute`
  MODIFY `route_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `dbSchoolSuppliesForm`
--
ALTER TABLE `dbSchoolSuppliesForm`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dbServices`
--
ALTER TABLE `dbServices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `dbServicesNEW`
--
ALTER TABLE `dbServicesNEW`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dbSpringBreakCampForm`
--
ALTER TABLE `dbSpringBreakCampForm`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dbStaff`
--
ALTER TABLE `dbStaff`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `dbSummerJunctionRegistrationForm`
--
ALTER TABLE `dbSummerJunctionRegistrationForm`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dbTopicInterests`
--
ALTER TABLE `dbTopicInterests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `dbUnallowedFoods`
--
ALTER TABLE `dbUnallowedFoods`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dbVolunteerReport`
--
ALTER TABLE `dbVolunteerReport`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `dbVolunteers`
--
ALTER TABLE `dbVolunteers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `dbActivityAttendees`
--
ALTER TABLE `dbActivityAttendees`
  ADD CONSTRAINT `dbactivityattendees_ibfk_1` FOREIGN KEY (`activityID`) REFERENCES `dbActualActivityForm` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dbactivityattendees_ibfk_2` FOREIGN KEY (`attendeeID`) REFERENCES `dbActualActivityAttendees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dbAngelGiftForm`
--
ALTER TABLE `dbAngelGiftForm`
  ADD CONSTRAINT `FKangelgift` FOREIGN KEY (`child_id`) REFERENCES `dbChildren` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dbAttendance`
--
ALTER TABLE `dbAttendance`
  ADD CONSTRAINT `dbattendance_ibfk_1` FOREIGN KEY (`route_id`) REFERENCES `dbRoute` (`route_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dbAttendees`
--
ALTER TABLE `dbAttendees`
  ADD CONSTRAINT `dbattendees_ibfk_1` FOREIGN KEY (`route_id`) REFERENCES `dbRoute` (`route_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dbattendees_ibfk_2` FOREIGN KEY (`child_id`) REFERENCES `dbChildren` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dbAvailability`
--
ALTER TABLE `dbAvailability`
  ADD CONSTRAINT `FKprogramInterestForm_Availability` FOREIGN KEY (`form_id`) REFERENCES `dbProgramInterestForm` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dbBrainBuildersHolidayPartyForm`
--
ALTER TABLE `dbBrainBuildersHolidayPartyForm`
  ADD CONSTRAINT `FK_family_id` FOREIGN KEY (`family_id`) REFERENCES `dbFamily` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dbChildren`
--
ALTER TABLE `dbChildren`
  ADD CONSTRAINT `dbChildren_family_id_FK` FOREIGN KEY (`family_id`) REFERENCES `dbFamily` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dbEventMedia`
--
ALTER TABLE `dbEventMedia`
  ADD CONSTRAINT `FKeventID2` FOREIGN KEY (`eventID`) REFERENCES `dbEvents` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dbEvents`
--
ALTER TABLE `dbEvents`
  ADD CONSTRAINT `FKlocationID` FOREIGN KEY (`locationID`) REFERENCES `dbLocations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dbEventsServices`
--
ALTER TABLE `dbEventsServices`
  ADD CONSTRAINT `FKEventID3` FOREIGN KEY (`eventID`) REFERENCES `dbEvents` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FKserviceID3` FOREIGN KEY (`serviceID`) REFERENCES `dbServices` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dbEventVolunteers`
--
ALTER TABLE `dbEventVolunteers`
  ADD CONSTRAINT `FKeventID` FOREIGN KEY (`eventID`) REFERENCES `dbEvents` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FKpersonID` FOREIGN KEY (`userID`) REFERENCES `dbPersons` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dbFamily_Assistance`
--
ALTER TABLE `dbFamily_Assistance`
  ADD CONSTRAINT `FKAssistance_Family` FOREIGN KEY (`assistance_id`) REFERENCES `dbAssistance` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FKFamily_Assistance` FOREIGN KEY (`family_id`) REFERENCES `dbFamily` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dbFamily_Languages`
--
ALTER TABLE `dbFamily_Languages`
  ADD CONSTRAINT `FKFamily_Language` FOREIGN KEY (`family_id`) REFERENCES `dbFamily` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FKLanguage_Family` FOREIGN KEY (`language_id`) REFERENCES `dbLanguages` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dbFieldTripWaiverForm`
--
ALTER TABLE `dbFieldTripWaiverForm`
  ADD CONSTRAINT `FK_field_trip_child_id` FOREIGN KEY (`child_id`) REFERENCES `dbChildren` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dbHolidayMealBagForm`
--
ALTER TABLE `dbHolidayMealBagForm`
  ADD CONSTRAINT `FKfamily_id` FOREIGN KEY (`family_id`) REFERENCES `dbFamily` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dbLocationsServices`
--
ALTER TABLE `dbLocationsServices`
  ADD CONSTRAINT `FKlocationID2` FOREIGN KEY (`locationID`) REFERENCES `dbLocations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FKserviceID2` FOREIGN KEY (`serviceID`) REFERENCES `dbServices` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dbLocationsServicesNEW`
--
ALTER TABLE `dbLocationsServicesNEW`
  ADD CONSTRAINT `dbLocationsServices_location_id_FK` FOREIGN KEY (`location_id`) REFERENCES `dbLocationsNEW` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dbLocationsServices_service_id_FK` FOREIGN KEY (`service_id`) REFERENCES `dbServicesNEW` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dbProgramInterestForm`
--
ALTER TABLE `dbProgramInterestForm`
  ADD CONSTRAINT `FKprogramInterestForm_Family` FOREIGN KEY (`family_id`) REFERENCES `dbFamily` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dbProgramInterestsForm_ProgramInterests`
--
ALTER TABLE `dbProgramInterestsForm_ProgramInterests`
  ADD CONSTRAINT `FKprogramInterestForm_ProgramInterest` FOREIGN KEY (`form_id`) REFERENCES `dbProgramInterestForm` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FKprogramInterest_programInterestForm` FOREIGN KEY (`interest_id`) REFERENCES `dbProgramInterests` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dbProgramInterestsForm_TopicInterests`
--
ALTER TABLE `dbProgramInterestsForm_TopicInterests`
  ADD CONSTRAINT `FKprogramInterestForm_topicInterest` FOREIGN KEY (`form_id`) REFERENCES `dbProgramInterestForm` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `FKtopicInterest_programInterestForm` FOREIGN KEY (`interest_id`) REFERENCES `dbTopicInterests` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dbPrograms`
--
ALTER TABLE `dbPrograms`
  ADD CONSTRAINT `dbPrograms_location_id_FK` FOREIGN KEY (`location_id`) REFERENCES `dbLocationsNEW` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dbProgramsParticipants`
--
ALTER TABLE `dbProgramsParticipants`
  ADD CONSTRAINT `dbProgramsParticipants_program_id_FK` FOREIGN KEY (`program_id`) REFERENCES `dbPrograms` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dbProgramsParticipants_user_id_FK` FOREIGN KEY (`user_id`) REFERENCES `dbPersonsNEW` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dbProgramsServices`
--
ALTER TABLE `dbProgramsServices`
  ADD CONSTRAINT `dbProgramsServices_program_id_FK` FOREIGN KEY (`program_id`) REFERENCES `dbPrograms` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dbProgramsServices_service_id_FK` FOREIGN KEY (`service_id`) REFERENCES `dbServicesNEW` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dbProgramsVolunteers`
--
ALTER TABLE `dbProgramsVolunteers`
  ADD CONSTRAINT `dbProgramsVolunteers_program_id_FK` FOREIGN KEY (`program_id`) REFERENCES `dbPrograms` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `dbProgramsVolunteers_user_id_FK` FOREIGN KEY (`user_id`) REFERENCES `dbPersonsNEW` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dbSchoolSuppliesForm`
--
ALTER TABLE `dbSchoolSuppliesForm`
  ADD CONSTRAINT `FKschoolsupplies` FOREIGN KEY (`child_id`) REFERENCES `dbChildren` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dbSpringBreakCampForm`
--
ALTER TABLE `dbSpringBreakCampForm`
  ADD CONSTRAINT `FK_child_id` FOREIGN KEY (`child_id`) REFERENCES `dbChildren` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `dbSummerJunctionRegistrationForm`
--
ALTER TABLE `dbSummerJunctionRegistrationForm`
  ADD CONSTRAINT `FKSummerJunctionRegistration` FOREIGN KEY (`child_id`) REFERENCES `dbChildren` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

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
