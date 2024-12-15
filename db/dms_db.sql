-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 15, 2024 at 08:04 AM
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
-- Database: `dms_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `dms_categories`
--

CREATE TABLE `dms_categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dms_categories`
--

INSERT INTO `dms_categories` (`category_id`, `category_name`, `description`) VALUES
(1, 'การกำหนดตำแหน่งทางวิชาการ', 'กพว');

-- --------------------------------------------------------

--
-- Table structure for table `dms_circular_letters`
--

CREATE TABLE `dms_circular_letters` (
  `circular_id` int(11) NOT NULL,
  `circular_number` int(11) NOT NULL,
  `circular_year` int(11) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `sender` varchar(100) NOT NULL,
  `receiver` varchar(100) NOT NULL,
  `date_created` date NOT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dms_directive_letters`
--

CREATE TABLE `dms_directive_letters` (
  `directive_id` int(11) NOT NULL,
  `directive_number` int(11) NOT NULL,
  `directive_year` int(11) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `sender` varchar(100) NOT NULL,
  `receiver` varchar(100) NOT NULL,
  `date_created` date NOT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dms_external_letters`
--

CREATE TABLE `dms_external_letters` (
  `external_id` int(11) NOT NULL,
  `external_number` int(11) NOT NULL,
  `external_year` int(11) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `sender` varchar(100) NOT NULL,
  `receiver` varchar(100) NOT NULL,
  `date_created` date NOT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dms_internal_letters`
--

CREATE TABLE `dms_internal_letters` (
  `internal_id` int(11) NOT NULL,
  `internal_number` int(11) NOT NULL,
  `internal_year` int(11) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `sender` varchar(100) NOT NULL,
  `receiver` varchar(100) NOT NULL,
  `date_created` date NOT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dms_internal_letters`
--

INSERT INTO `dms_internal_letters` (`internal_id`, `internal_number`, `internal_year`, `subject`, `category_id`, `sender`, `receiver`, `date_created`, `file_name`, `note`, `created_by`, `created_at`) VALUES
(6, 1, 2567, 'แจ้งประเมินการสอน', 1, 'งานบริหารทรัพยากรบุคคลและนิติการ', 'อาจารย์ฮักนะ', '2024-12-15', NULL, '', 1, '2024-12-15 07:01:04');

-- --------------------------------------------------------

--
-- Table structure for table `dms_received_letters`
--

CREATE TABLE `dms_received_letters` (
  `received_id` int(11) NOT NULL,
  `letter_id` int(11) NOT NULL,
  `received_number` int(11) NOT NULL,
  `received_year` int(11) NOT NULL,
  `received_date` date NOT NULL,
  `received_by` int(11) DEFAULT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dms_users`
--

CREATE TABLE `dms_users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `full_name` varchar(255) DEFAULT NULL,
  `role` enum('admin','user') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dms_users`
--

INSERT INTO `dms_users` (`user_id`, `username`, `password`, `email`, `full_name`, `role`, `created_at`) VALUES
(1, 'admin', '$2y$10$Kf3ilUAIHIAaoGAlf2zacOkPBtOiWjAvPlBqL6mFBcKhksqSvwXGe', 'admin@test.com', 'Admin', 'admin', '2024-12-15 06:46:09');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `dms_categories`
--
ALTER TABLE `dms_categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `dms_circular_letters`
--
ALTER TABLE `dms_circular_letters`
  ADD PRIMARY KEY (`circular_id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `dms_directive_letters`
--
ALTER TABLE `dms_directive_letters`
  ADD PRIMARY KEY (`directive_id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `dms_external_letters`
--
ALTER TABLE `dms_external_letters`
  ADD PRIMARY KEY (`external_id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `dms_internal_letters`
--
ALTER TABLE `dms_internal_letters`
  ADD PRIMARY KEY (`internal_id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `dms_received_letters`
--
ALTER TABLE `dms_received_letters`
  ADD PRIMARY KEY (`received_id`),
  ADD KEY `letter_id` (`letter_id`),
  ADD KEY `received_by` (`received_by`);

--
-- Indexes for table `dms_users`
--
ALTER TABLE `dms_users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `dms_categories`
--
ALTER TABLE `dms_categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `dms_circular_letters`
--
ALTER TABLE `dms_circular_letters`
  MODIFY `circular_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dms_directive_letters`
--
ALTER TABLE `dms_directive_letters`
  MODIFY `directive_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dms_external_letters`
--
ALTER TABLE `dms_external_letters`
  MODIFY `external_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dms_internal_letters`
--
ALTER TABLE `dms_internal_letters`
  MODIFY `internal_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `dms_received_letters`
--
ALTER TABLE `dms_received_letters`
  MODIFY `received_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `dms_users`
--
ALTER TABLE `dms_users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `dms_circular_letters`
--
ALTER TABLE `dms_circular_letters`
  ADD CONSTRAINT `dms_circular_letters_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `dms_categories` (`category_id`),
  ADD CONSTRAINT `dms_circular_letters_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `dms_users` (`user_id`);

--
-- Constraints for table `dms_directive_letters`
--
ALTER TABLE `dms_directive_letters`
  ADD CONSTRAINT `dms_directive_letters_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `dms_categories` (`category_id`),
  ADD CONSTRAINT `dms_directive_letters_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `dms_users` (`user_id`);

--
-- Constraints for table `dms_external_letters`
--
ALTER TABLE `dms_external_letters`
  ADD CONSTRAINT `dms_external_letters_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `dms_categories` (`category_id`),
  ADD CONSTRAINT `dms_external_letters_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `dms_users` (`user_id`);

--
-- Constraints for table `dms_internal_letters`
--
ALTER TABLE `dms_internal_letters`
  ADD CONSTRAINT `dms_internal_letters_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `dms_categories` (`category_id`),
  ADD CONSTRAINT `dms_internal_letters_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `dms_users` (`user_id`);

--
-- Constraints for table `dms_received_letters`
--
ALTER TABLE `dms_received_letters`
  ADD CONSTRAINT `dms_received_letters_ibfk_1` FOREIGN KEY (`letter_id`) REFERENCES `dms_internal_letters` (`internal_id`),
  ADD CONSTRAINT `dms_received_letters_ibfk_2` FOREIGN KEY (`received_by`) REFERENCES `dms_users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
