-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 28, 2025 at 10:06 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `inventory_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `brochures`
--

CREATE TABLE `brochures` (
  `brochure_id` int(11) NOT NULL,
  `brochure_name` varchar(100) NOT NULL,
  `quantity` int(4) NOT NULL,
  `total_brochure` int(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `brochures`
--

INSERT INTO `brochures` (`brochure_id`, `brochure_name`, `quantity`, `total_brochure`) VALUES
(1, 'ALL PROGRAMS', 15, 0),
(2, 'ADOBE', 9, 0),
(3, 'AUTODESK', 91, 0),
(4, 'AWS', 80, 0),
(5, 'BENEFITS OF MICROSOFT', 98, 0),
(6, 'CCS', 0, 0),
(7, 'CISCO', 85, 0),
(8, 'COPILOT', 35, 0),
(9, 'CSB', 134, 0),
(10, 'ESB', 80, 0),
(11, 'IC3', 171, 0),
(12, 'ITS', 9, 0),
(13, 'META', 113, 0),
(14, 'MICROSOFT CERTIFICATIONS', 59, 0),
(15, 'PMI', 377, 0),
(16, 'QUICKBOOKS', 9, 0),
(17, 'SWIFT', 138, 0),
(18, 'THE VALUE', 0, 0),
(19, 'UNITY', 100, 0),
(20, 'VALUE OF CERTIFICATIONS', 38, 0),
(21, 'VERSANT', 77, 0),
(22, 'ALL PROGRAMS (TRIFOLD)', 0, 0),
(23, 'Agriscience and Technology Careers', 25, 0),
(24, 'Health Sciences Careers', 0, 0),
(25, 'Hospitality and Culinary Arts', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `event_form`
--

CREATE TABLE `event_form` (
  `event_form_id` int(11) NOT NULL,
  `event_name` varchar(100) NOT NULL,
  `event_title` varchar(100) NOT NULL,
  `event_date` date NOT NULL,
  `date_time_ingress` datetime NOT NULL,
  `date_time_egress` datetime NOT NULL,
  `place` varchar(100) NOT NULL,
  `location` varchar(255) NOT NULL,
  `sponsorship_budg` enum('Yes','No','','') NOT NULL,
  `amount` int(11) DEFAULT NULL,
  `target_audience` text DEFAULT NULL,
  `number_audience` int(11) NOT NULL,
  `set_up` enum('Yes','No','','') DEFAULT NULL,
  `booth_size` text DEFAULT NULL,
  `booth_inclusion` text DEFAULT NULL,
  `number_tables` int(3) DEFAULT NULL,
  `number_chairs` int(3) DEFAULT NULL,
  `speaking_slot` enum('Yes','No','','') DEFAULT NULL,
  `speaker_name` varchar(50) DEFAULT NULL,
  `date_time` datetime NOT NULL,
  `duration` varchar(50) DEFAULT NULL,
  `Topic` varchar(255) DEFAULT NULL,
  `technical_team` enum('Yes','No','','') NOT NULL,
  `technical_task` text DEFAULT NULL,
  `trainer_needed` enum('Yes','No','','') NOT NULL,
  `trainer_task` text DEFAULT NULL,
  `provide_materials` enum('Yes','No','','') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `request_status` enum('Pending','Approved','Decline','') NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `request_mats` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `event_form_history`
--

CREATE TABLE `event_form_history` (
  `event_form_id` int(11) NOT NULL,
  `event_name` varchar(255) DEFAULT NULL,
  `event_title` varchar(255) DEFAULT NULL,
  `event_date` varchar(100) DEFAULT NULL,
  `sender_email` varchar(255) DEFAULT NULL,
  `date_time_ingress` datetime DEFAULT NULL,
  `date_time_egress` datetime DEFAULT NULL,
  `place` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `sponsorship_budg` enum('Yes','No','','') NOT NULL,
  `amount` int(11) DEFAULT NULL,
  `target_audience` varchar(255) DEFAULT NULL,
  `number_audience` int(11) DEFAULT NULL,
  `set_up` enum('Yes','No','','') DEFAULT NULL,
  `booth_size` varchar(255) DEFAULT NULL,
  `booth_inclusion` varchar(255) DEFAULT NULL,
  `number_tables` int(11) DEFAULT NULL,
  `number_chairs` int(11) DEFAULT NULL,
  `speaking_slot` varchar(255) DEFAULT NULL,
  `speaker_name` varchar(50) DEFAULT NULL,
  `date_time` datetime DEFAULT NULL,
  `duration` varchar(50) DEFAULT NULL,
  `Topic` varchar(255) DEFAULT NULL,
  `technical_team` varchar(255) DEFAULT NULL,
  `technical_task` varchar(255) DEFAULT NULL,
  `trainer_needed` varchar(255) DEFAULT NULL,
  `trainer_task` varchar(255) DEFAULT NULL,
  `provide_materials` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `request_status` varchar(50) DEFAULT NULL,
  `processed_at` datetime DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `request_mats` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `marketing_materials`
--

CREATE TABLE `marketing_materials` (
  `material_id` int(11) NOT NULL,
  `material_name` varchar(100) NOT NULL,
  `quantity` int(4) NOT NULL,
  `others` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `marketing_materials`
--

INSERT INTO `marketing_materials` (`material_id`, `material_name`, `quantity`, `others`) VALUES
(6, 'MATERIALS 1', 1, NULL),
(7, 'MATERIALS 2', 1, NULL),
(8, 'MATERIALS 3', 1, NULL),
(9, 'MATERIALS 4', 1, NULL),
(10, 'MATERIALS 5', 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `material_request_form`
--

CREATE TABLE `material_request_form` (
  `material_request_id` int(11) NOT NULL,
  `request_mats` int(11) NOT NULL,
  `name_brochures` varchar(255) NOT NULL,
  `brochure_quantity` int(11) NOT NULL,
  `name_swag` varchar(255) NOT NULL,
  `swag_quantity` int(11) NOT NULL,
  `name_material` varchar(255) NOT NULL,
  `material_quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `material_request_form`
--

INSERT INTO `material_request_form` (`material_request_id`, `request_mats`, `name_brochures`, `brochure_quantity`, `name_swag`, `swag_quantity`, `name_material`, `material_quantity`) VALUES
(322, 322, 'ALL PROGRAMS', 1, '', 0, '', 0),
(323, 322, 'ADOBE', 1, '', 0, '', 0),
(324, 322, 'AUTODESK', 1, '', 0, '', 0);

-- --------------------------------------------------------

--
-- Table structure for table `material_return_request`
--

CREATE TABLE `material_return_request` (
  `request_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `items_json` text NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'Pending',
  `requested_at` datetime NOT NULL,
  `reviewed_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `material_return_request`
--

INSERT INTO `material_return_request` (`request_id`, `event_id`, `user_id`, `items_json`, `status`, `requested_at`, `reviewed_at`) VALUES
(13, 57, 17, '[{\"type\":\"Brochure\",\"name\":\"ALL PROGRAMS\",\"qty\":1},{\"type\":\"Brochure\",\"name\":\"ADOBE\",\"qty\":1},{\"type\":\"Brochure\",\"name\":\"AUTODESK\",\"qty\":1}]', 'Approved', '2025-08-20 15:14:56', '2025-08-20 15:15:31');

-- --------------------------------------------------------

--
-- Table structure for table `swags`
--

CREATE TABLE `swags` (
  `swag_id` int(11) NOT NULL,
  `swags_name` varchar(100) NOT NULL,
  `quantity` int(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `swags`
--

INSERT INTO `swags` (`swag_id`, `swags_name`, `quantity`) VALUES
(1, 'SWAGS 1', 1),
(2, 'SWAGS 2', 1),
(3, 'SWAGS 3', 1),
(4, 'SWAGS 4', 1),
(5, 'SWAGS 5', 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(20) NOT NULL,
  `password` varchar(100) NOT NULL,
  `email` varchar(50) NOT NULL,
  `user_type` enum('admin','trainer','','') DEFAULT NULL,
  `Account_status` enum('Activated','Deactivated','','') NOT NULL,
  `verification_code` varchar(10) NOT NULL,
  `verified` tinyint(1) DEFAULT 0,
  `reset_token` varchar(64) NOT NULL,
  `token_expiry` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `email`, `user_type`, `Account_status`, `verification_code`, `verified`, `reset_token`, `token_expiry`) VALUES
(13, 'administration', '$2y$10$oJS7luv6feZ6hvkqZR0.ReJYZ/0LcUk1v83n/Sm0EakRN96Cvt7lW', 'carlitotagarro0@gmail.com', 'admin', '', '921859', 1, '', '0000-00-00 00:00:00'),
(16, 'Joseph', '$2y$10$cmBgp4rPBQ9OeNkUsVlSd.Fk0BsnC1OSX.pvpaONNsmJGx0DOm/vC', 'ch4rlestzy27@gmail.com', 'trainer', 'Activated', '470291', 1, '', '2025-08-27 11:19:40'),
(17, 'Charles', '$2y$10$fHwDU6Aa/NyPGFp7IKes5uvGXTWaVKAMuNY4dbLdWKX3ZZSxDFeVS', 'carlitotagarro27@gmail.com', 'trainer', 'Activated', '412464', 1, '', '0000-00-00 00:00:00'),
(22, 'Charlito', '$2y$10$zT3YGflFo1b4bBgS/uIOFOOvFxJOzR8L2gu67A3hSwx.sY7YsySiC', 'carlitotagarro0927@gmail.com', 'trainer', 'Deactivated', '700235', 1, '', '2025-08-27 11:19:40');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `brochures`
--
ALTER TABLE `brochures`
  ADD PRIMARY KEY (`brochure_id`);

--
-- Indexes for table `event_form`
--
ALTER TABLE `event_form`
  ADD PRIMARY KEY (`event_form_id`),
  ADD KEY `fk_user_id` (`user_id`),
  ADD KEY `fk_event_form_material_request_id` (`request_mats`);

--
-- Indexes for table `event_form_history`
--
ALTER TABLE `event_form_history`
  ADD PRIMARY KEY (`event_form_id`),
  ADD KEY `fk_event_form_history_user_id` (`user_id`);

--
-- Indexes for table `marketing_materials`
--
ALTER TABLE `marketing_materials`
  ADD PRIMARY KEY (`material_id`);

--
-- Indexes for table `material_request_form`
--
ALTER TABLE `material_request_form`
  ADD PRIMARY KEY (`material_request_id`);

--
-- Indexes for table `material_return_request`
--
ALTER TABLE `material_return_request`
  ADD PRIMARY KEY (`request_id`);

--
-- Indexes for table `swags`
--
ALTER TABLE `swags`
  ADD PRIMARY KEY (`swag_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `brochures`
--
ALTER TABLE `brochures`
  MODIFY `brochure_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `event_form`
--
ALTER TABLE `event_form`
  MODIFY `event_form_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `marketing_materials`
--
ALTER TABLE `marketing_materials`
  MODIFY `material_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `material_request_form`
--
ALTER TABLE `material_request_form`
  MODIFY `material_request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=325;

--
-- AUTO_INCREMENT for table `material_return_request`
--
ALTER TABLE `material_return_request`
  MODIFY `request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `swags`
--
ALTER TABLE `swags`
  MODIFY `swag_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `event_form`
--
ALTER TABLE `event_form`
  ADD CONSTRAINT `fk_event_form_material_request_id` FOREIGN KEY (`request_mats`) REFERENCES `material_request_form` (`material_request_id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `event_form_history`
--
ALTER TABLE `event_form_history`
  ADD CONSTRAINT `fk_event_form_history_user_id` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
