-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 15, 2025 at 09:08 AM
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
(1, 'BROCHURE 1', 18, 0),
(2, 'BROCHURE 2', 13, 0),
(3, 'BROCHURE 3', 13, 0),
(4, 'BROCHURE 4', 15, 0),
(5, 'BROCHURE 5', 15, 0);

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
  `sponsorship_budg` enum('Free','Sponsorship','','') NOT NULL,
  `target_audience` text DEFAULT NULL,
  `number_audience` int(11) NOT NULL,
  `set_up` enum('Yes','No','','') DEFAULT NULL,
  `booth_size` text DEFAULT NULL,
  `booth_inclusion` text DEFAULT NULL,
  `number_tables` int(3) DEFAULT NULL,
  `number_chairs` int(3) DEFAULT NULL,
  `speaking_slot` varchar(255) DEFAULT NULL,
  `date_time` datetime NOT NULL,
  `program_target` varchar(255) DEFAULT NULL,
  `technical_team` enum('Yes','No','','') NOT NULL,
  `trainer_needed` enum('Yes','No','','') NOT NULL,
  `ready_to_use` text DEFAULT NULL,
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
  `event_date` date DEFAULT NULL,
  `sender_email` varchar(255) DEFAULT NULL,
  `date_time_ingress` datetime DEFAULT NULL,
  `date_time_egress` datetime DEFAULT NULL,
  `place` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `sponsorship_budg` enum('Free','Sponsorship','','') NOT NULL,
  `target_audience` varchar(255) DEFAULT NULL,
  `number_audience` int(11) DEFAULT NULL,
  `set_up` varchar(255) DEFAULT NULL,
  `booth_size` varchar(255) DEFAULT NULL,
  `booth_inclusion` varchar(255) DEFAULT NULL,
  `number_tables` int(11) DEFAULT NULL,
  `number_chairs` int(11) DEFAULT NULL,
  `speaking_slot` varchar(255) DEFAULT NULL,
  `date_time` datetime DEFAULT NULL,
  `program_target` varchar(255) DEFAULT NULL,
  `technical_team` varchar(255) DEFAULT NULL,
  `trainer_needed` varchar(255) DEFAULT NULL,
  `ready_to_use` varchar(255) DEFAULT NULL,
  `provide_materials` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `request_status` varchar(50) DEFAULT NULL,
  `processed_at` datetime DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `request_mats` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `event_form_history`
--

INSERT INTO `event_form_history` (`event_form_id`, `event_name`, `event_title`, `event_date`, `sender_email`, `date_time_ingress`, `date_time_egress`, `place`, `location`, `sponsorship_budg`, `target_audience`, `number_audience`, `set_up`, `booth_size`, `booth_inclusion`, `number_tables`, `number_chairs`, `speaking_slot`, `date_time`, `program_target`, `technical_team`, `trainer_needed`, `ready_to_use`, `provide_materials`, `created_at`, `request_status`, `processed_at`, `user_id`, `request_mats`) VALUES
(14, 'TESTING', 'TESTING', '2025-08-29', '', '2025-08-15 11:15:00', '2025-08-15 11:16:00', 'uuwi nalng', 'sd', 'Sponsorship', '', 0, '', '', '', 0, 0, '', '0000-00-00 00:00:00', '3323', 'No', 'No', '', 'No', '2025-08-15 11:16:15', 'Approved', '2025-08-15 11:16:26', 14, 0),
(15, 'LETS GO', 'GUMAGANA NA', '2025-08-15', '', '2025-08-15 11:19:00', '2025-08-15 11:19:00', 'EZ', 'AF', 'Free', 'GYATS', 100, 'Yes', 'BIG BOI', 'LAHAT', 50, 100, 'Diddy', '2025-08-15 11:18:00', 'GYAT AND HUZZ', 'No', 'No', 'OIL', 'Yes', '2025-08-15 11:19:17', 'Approved', '2025-08-15 11:21:14', 14, 260),
(16, 'SENDER EMAIL TESTING', 'TESTING', '2025-08-15', '', '2025-08-15 11:27:00', '2025-08-15 11:27:00', 'AYAW MAG FETCH NG EMAIL OF SENDER', 'UMAY', 'Sponsorship', 'BADTRIP', 123, 'Yes', 'SAMGYUPSAL', 'LAHAT NG MERON', 123, 123, 'IAMSAM', '2025-08-15 11:28:00', 'SAMGYUPSAL', 'No', 'No', 'LUTUAN', 'No', '2025-08-15 11:28:22', 'Approved', '2025-08-15 11:41:58', 14, 0),
(17, 'SENDER EMAIL', 'SENDER EMAIL TEST', '2025-08-15', '', '2025-08-15 11:42:00', '2025-08-15 11:42:00', 'uuwi nalng', 'sa susunod na lang', 'Sponsorship', '123', 200, '', '', '', 0, 0, '', '0000-00-00 00:00:00', '', 'No', 'No', '', 'No', '2025-08-15 11:43:23', 'Declined', '2025-08-15 11:49:11', 14, 0),
(18, 'TESTING', 'TESTING', '2025-08-15', '', '2025-08-15 13:05:00', '2025-08-15 13:05:00', 'EZ', 'ew3434', '', '', 0, '', '', '', 0, 0, '', '0000-00-00 00:00:00', '', 'No', 'No', '', 'No', '2025-08-15 13:05:48', 'Approved', '2025-08-15 13:06:09', 14, 0),
(19, 'TESTING', 'TESTING', '2025-08-15', '', '2025-08-15 13:16:00', '2025-08-15 13:16:00', 'uuwi nalng', 'SDSDS', '', '', 0, '', '', '', 0, 0, '', '0000-00-00 00:00:00', '', 'No', 'No', '', 'No', '2025-08-15 13:16:54', 'Declined', '2025-08-15 13:18:54', 14, 0),
(20, 'KvK', 'Rise of Kingdoms', '2025-08-15', 'carlitotagarro27@gmail.com', '2025-08-15 13:23:00', '2025-08-15 13:23:00', 'UMAY LODS', 'sd', 'Free', '', 0, '', '', '', 0, 0, '', '0000-00-00 00:00:00', '', 'No', 'No', '', 'No', '2025-08-15 13:23:51', 'Approved', '2025-08-15 13:24:08', 14, 0),
(21, 'Umay Lods', 'MEmew', '2025-08-15', '', '2025-08-15 13:26:00', '2025-08-15 13:26:00', 'uuwi nalng', 'sa susunod na lang', '', '', 0, '', '', '', 0, 0, '', '0000-00-00 00:00:00', '', 'No', 'No', '', 'No', '2025-08-15 13:26:13', 'Approved', '2025-08-15 13:26:27', 14, 0),
(22, 'GUMAGANA NA', 'ANG DATING PLINAPLANO', '2025-08-15', 'carlitotagarro27@gmail.com', '2025-08-15 13:27:00', '2025-08-15 13:27:00', 'NIGG NIGG', 'BRUV', '', '', 0, '', '', '', 0, 0, '', '0000-00-00 00:00:00', '', 'No', 'No', '', 'No', '2025-08-15 13:27:46', 'Approved', '2025-08-15 13:27:59', 14, 0),
(23, 'TESTING PDF generation', 'TESTINGERS', '2025-08-15', 'carlitotagarro27@gmail.com', '2025-08-15 14:24:00', '2025-08-15 14:24:00', 'uuwi nalng', 'sa susunod na lang', 'Sponsorship', 'ASD', 12, 'Yes', '', '', 0, 0, '', '0000-00-00 00:00:00', '', 'Yes', 'Yes', '', 'Yes', '2025-08-15 14:25:21', 'Approved', '2025-08-15 14:25:40', 14, 275),
(24, 'PDF GENERATION TEST', 'PDF TEST', '2025-08-15', 'carlitotagarro27@gmail.com', '2025-08-15 14:30:00', '2025-08-15 14:30:00', 'UMAY LODS', 'sa susunod na lang', '', '', 0, '', '', '', 0, 0, '', '0000-00-00 00:00:00', '', 'No', 'No', '', 'No', '2025-08-15 14:30:23', 'Declined', '2025-08-15 14:46:39', 14, 0);

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
(6, 'MATERIALS 1', 19, NULL),
(7, 'MATERIALS 2', 14, NULL),
(8, 'MATERIALS 3', 14, NULL),
(9, 'MATERIALS 4', 15, NULL),
(10, 'MATERIALS 5', 15, NULL);

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
(186, 186, 'BROCHURE 1', 1, '', 0, '', 0),
(187, 186, '', 0, '', 0, 'MATERIALS 1', 1),
(188, 186, '', 0, 'SWAGS 1', 1, '', 0),
(189, 189, 'BROCHURE 1', 1, '', 0, '', 0),
(190, 189, 'BROCHURE 2', 1, '', 0, '', 0),
(191, 189, '', 0, '', 0, 'MATERIALS 1', 1),
(192, 189, '', 0, '', 0, 'MATERIALS 2', 1),
(193, 189, '', 0, 'SWAGS 1', 1, '', 0),
(194, 189, '', 0, 'SWAGS 2', 1, '', 0),
(195, 195, 'BROCHURE 1', 1, '', 0, '', 0),
(196, 195, '', 0, '', 0, 'MATERIALS 1', 1),
(197, 195, '', 0, 'SWAGS 1', 1, '', 0),
(198, 198, 'BROCHURE 1', 1, '', 0, '', 0),
(199, 198, 'BROCHURE 2', 1, '', 0, '', 0),
(200, 198, 'BROCHURE 3', 1, '', 0, '', 0),
(201, 198, 'BROCHURE 4', 1, '', 0, '', 0),
(202, 198, 'BROCHURE 5', 1, '', 0, '', 0),
(203, 198, '', 0, '', 0, 'MATERIALS 1', 1),
(204, 198, '', 0, '', 0, 'MATERIALS 2', 1),
(205, 198, '', 0, '', 0, 'MATERIALS 3', 1),
(206, 198, '', 0, '', 0, 'MATERIALS 4', 1),
(207, 198, '', 0, '', 0, 'MATERIALS 5', 1),
(208, 198, '', 0, 'SWAGS 1', 1, '', 0),
(209, 198, '', 0, 'SWAGS 2', 1, '', 0),
(210, 198, '', 0, 'SWAGS 3', 1, '', 0),
(211, 198, '', 0, 'SWAGS 4', 1, '', 0),
(212, 198, '', 0, 'SWAGS 5', 1, '', 0),
(213, 213, 'BROCHURE 1', 3, '', 0, '', 0),
(214, 213, 'BROCHURE 2', 3, '', 0, '', 0),
(215, 213, 'BROCHURE 3', 3, '', 0, '', 0),
(216, 213, 'BROCHURE 4', 3, '', 0, '', 0),
(217, 213, 'BROCHURE 5', 3, '', 0, '', 0),
(218, 213, '', 0, '', 0, 'MATERIALS 1', 3),
(219, 213, '', 0, '', 0, 'MATERIALS 2', 3),
(220, 213, '', 0, '', 0, 'MATERIALS 3', 3),
(221, 213, '', 0, '', 0, 'MATERIALS 4', 3),
(222, 213, '', 0, '', 0, 'MATERIALS 5', 3),
(223, 213, '', 0, 'SWAGS 1', 3, '', 0),
(224, 213, '', 0, 'SWAGS 2', 3, '', 0),
(225, 213, '', 0, 'SWAGS 3', 3, '', 0),
(226, 213, '', 0, 'SWAGS 4', 3, '', 0),
(227, 213, '', 0, 'SWAGS 5', 3, '', 0),
(228, 228, 'BROCHURE 1', 1, '', 0, '', 0),
(229, 228, '', 0, '', 0, 'MATERIALS 1', 1),
(230, 228, '', 0, 'SWAGS 1', 1, '', 0),
(231, 231, 'BROCHURE 2', 1, '', 0, '', 0),
(232, 231, 'BROCHURE 3', 1, '', 0, '', 0),
(233, 231, '', 0, '', 0, 'MATERIALS 2', 1),
(234, 231, '', 0, '', 0, 'MATERIALS 3', 1),
(235, 231, '', 0, 'SWAGS 2', 1, '', 0),
(236, 231, '', 0, 'SWAGS 3', 1, '', 0),
(237, 237, 'BROCHURE 1', 1, '', 0, '', 0),
(238, 237, '', 0, '', 0, 'MATERIALS 1', 1),
(239, 237, '', 0, 'SWAGS 1', 1, '', 0),
(240, 240, 'BROCHURE 1', 1, '', 0, '', 0),
(241, 240, 'BROCHURE 2', 1, '', 0, '', 0),
(242, 240, 'BROCHURE 3', 1, '', 0, '', 0),
(243, 240, 'BROCHURE 4', 1, '', 0, '', 0),
(244, 240, 'BROCHURE 5', 1, '', 0, '', 0),
(245, 240, '', 0, '', 0, 'MATERIALS 1', 1),
(246, 240, '', 0, '', 0, 'MATERIALS 2', 1),
(247, 240, '', 0, '', 0, 'MATERIALS 3', 1),
(248, 240, '', 0, '', 0, 'MATERIALS 4', 1),
(249, 240, '', 0, '', 0, 'MATERIALS 5', 1),
(250, 240, '', 0, 'SWAGS 1', 1, '', 0),
(251, 240, '', 0, 'SWAGS 2', 1, '', 0),
(252, 240, '', 0, 'SWAGS 3', 1, '', 0),
(253, 240, '', 0, 'SWAGS 4', 1, '', 0),
(254, 240, '', 0, 'SWAGS 5', 1, '', 0),
(255, 255, 'BROCHURE 1', 1, '', 0, '', 0),
(256, 255, '', 0, '', 0, 'MATERIALS 1', 1),
(257, 255, '', 0, 'SWAGS 1', 1, '', 0),
(258, 258, 'BROCHURE 1', 1, '', 0, '', 0),
(259, 258, 'BROCHURE 2', 1, '', 0, '', 0),
(260, 260, 'BROCHURE 1', 1, '', 0, '', 0),
(261, 260, 'BROCHURE 2', 1, '', 0, '', 0),
(262, 260, 'BROCHURE 3', 1, '', 0, '', 0),
(263, 260, 'BROCHURE 4', 1, '', 0, '', 0),
(264, 260, 'BROCHURE 5', 1, '', 0, '', 0),
(265, 260, '', 0, '', 0, 'MATERIALS 1', 1),
(266, 260, '', 0, '', 0, 'MATERIALS 2', 1),
(267, 260, '', 0, '', 0, 'MATERIALS 3', 1),
(268, 260, '', 0, '', 0, 'MATERIALS 4', 1),
(269, 260, '', 0, '', 0, 'MATERIALS 5', 1),
(270, 260, '', 0, 'SWAGS 1', 1, '', 0),
(271, 260, '', 0, 'SWAGS 2', 1, '', 0),
(272, 260, '', 0, 'SWAGS 3', 1, '', 0),
(273, 260, '', 0, 'SWAGS 4', 1, '', 0),
(274, 260, '', 0, 'SWAGS 5', 1, '', 0),
(275, 275, 'BROCHURE 1', 1, '', 0, '', 0),
(276, 275, 'BROCHURE 2', 1, '', 0, '', 0),
(277, 275, 'BROCHURE 3', 1, '', 0, '', 0);

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
(1, 'SWAGS 1', 19),
(2, 'SWAGS 2', 14),
(3, 'SWAGS 3', 14),
(4, 'SWAGS 4', 15),
(5, 'SWAGS 5', 15);

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
  `position` varchar(100) NOT NULL,
  `verification_code` varchar(10) NOT NULL,
  `verified` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `email`, `user_type`, `position`, `verification_code`, `verified`) VALUES
(13, 'administration', '$2y$10$CP9GOla6e9kfDWiDpgDTsux.PJ0UpiXL2hBTudOpsGPAKeArFDr.W', 'carlitotagarro0@gmail.com', 'admin', '', '921859', 1),
(14, 'user', '$2y$10$nZLjh4MA0DRpnCwQ7l4JTuXl6qwc6WQF28Zpt13eLpaamtPE7tPAW', 'carlitotagarro27@gmail.com', 'trainer', '', '290166', 1),
(15, 'msfelice', '$2y$10$zJEnOPwvoO6mnKm1Zvq6H.I4YTstT.DtteGg8y8PC7UIwDJ1.fLI6', 'felicejoyjuliano75@gmail.com', 'trainer', '', '247727', 1);

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
  MODIFY `brochure_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `event_form`
--
ALTER TABLE `event_form`
  MODIFY `event_form_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `marketing_materials`
--
ALTER TABLE `marketing_materials`
  MODIFY `material_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `material_request_form`
--
ALTER TABLE `material_request_form`
  MODIFY `material_request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=278;

--
-- AUTO_INCREMENT for table `swags`
--
ALTER TABLE `swags`
  MODIFY `swag_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

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
