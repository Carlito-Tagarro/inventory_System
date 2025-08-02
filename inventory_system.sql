-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 02, 2025 at 02:23 PM
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
(1, 'b1', 10, 0),
(2, 'b2', 10, 0);

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
  `sponsorship_budg` enum('Free','Sponsorship','','') DEFAULT NULL,
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
  `sponsorship_budget` decimal(10,2) DEFAULT NULL,
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

INSERT INTO `event_form_history` (`event_form_id`, `event_name`, `event_title`, `event_date`, `sender_email`, `date_time_ingress`, `date_time_egress`, `place`, `location`, `sponsorship_budget`, `target_audience`, `number_audience`, `set_up`, `booth_size`, `booth_inclusion`, `number_tables`, `number_chairs`, `speaking_slot`, `date_time`, `program_target`, `technical_team`, `trainer_needed`, `ready_to_use`, `provide_materials`, `created_at`, `request_status`, `processed_at`, `user_id`, `request_mats`) VALUES
(6, 'Birthday ko', 'Examination', '2025-08-02', '', '2025-08-02 20:11:00', '2025-08-16 20:11:00', 'qwewqe', 'asdsadas', 0.00, '', 0, '', '', '', 0, 0, '', '0000-00-00 00:00:00', '', 'Yes', 'Yes', '', 'Yes', '2025-08-02 20:11:26', 'Approved', '2025-08-02 14:11:44', 14, 32),
(7, 'test', 'test', '2025-08-02', '', '2025-08-02 20:12:00', '2025-08-02 20:12:00', 'asdasd', 'asdsad', 0.00, '', 0, '', '', '', 0, 0, '', '0000-00-00 00:00:00', '', 'Yes', 'Yes', '', 'Yes', '2025-08-02 20:13:13', 'Declined', '2025-08-02 14:17:50', 14, 35);

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
(1, 'm1', 5, NULL),
(2, 'm2', 5, NULL);

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
(32, 32, 'b1', 1, '', 0, '', 0),
(33, 32, '', 0, '', 0, 'm1', 1),
(34, 32, '', 0, 's1', 1, '', 0),
(35, 35, 'b1', 1, '', 0, '', 0),
(36, 35, 'b2', 1, '', 0, '', 0),
(37, 35, '', 0, '', 0, 'm1', 1),
(38, 35, '', 0, '', 0, 'm2', 1),
(39, 35, '', 0, 's1', 1, '', 0),
(40, 35, '', 0, 's2', 1, '', 0);

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
(1, 's1', 5),
(2, 's2', 5);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
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
(14, 'user', '$2y$10$nZLjh4MA0DRpnCwQ7l4JTuXl6qwc6WQF28Zpt13eLpaamtPE7tPAW', 'carlitotagarro27@gmail.com', 'trainer', '', '290166', 1);

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
  MODIFY `brochure_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `event_form`
--
ALTER TABLE `event_form`
  MODIFY `event_form_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `marketing_materials`
--
ALTER TABLE `marketing_materials`
  MODIFY `material_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `material_request_form`
--
ALTER TABLE `material_request_form`
  MODIFY `material_request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `swags`
--
ALTER TABLE `swags`
  MODIFY `swag_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

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
