-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 31, 2026 at 03:16 PM
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
-- Database: `smart_student_helpdesk`
--

-- --------------------------------------------------------

--
-- Table structure for table `complaints`
--

CREATE TABLE `complaints` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `department_id` int(11) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `status` enum('pending','in_progress','resolved') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `complaints`
--

INSERT INTO `complaints` (`id`, `user_id`, `department_id`, `subject`, `description`, `status`, `created_at`) VALUES
(1, 8, 1, 'gnfn', 'zfdnbzzdfccvdS', 'resolved', '2026-01-30 16:24:09'),
(2, 10, 1, 'Automated Test Ticket', 'This is a test complaint from the automation script.', 'resolved', '2026-01-30 16:49:17'),
(3, 10, 1, 'Automated Test Ticket', 'This is a test complaint from the automation script.', 'resolved', '2026-01-30 16:49:45'),
(4, 11, 1, 'Adgdg', 'hbsjhblucfbefhwaiefce', 'resolved', '2026-01-31 13:58:52');

-- --------------------------------------------------------

--
-- Table structure for table `complaint_replies`
--

CREATE TABLE `complaint_replies` (
  `id` int(11) NOT NULL,
  `complaint_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `complaint_replies`
--

INSERT INTO `complaint_replies` (`id`, `complaint_id`, `user_id`, `message`, `created_at`) VALUES
(1, 1, 9, 'sdbzdges', '2026-01-30 16:31:05'),
(2, 2, 9, 'Issue received. Working on it (Automated).', '2026-01-30 16:49:17'),
(3, 3, 9, 'Issue received. Working on it (Automated).', '2026-01-30 16:49:45'),
(4, 4, 11, 'fcbdxfndf', '2026-01-31 13:59:30'),
(5, 4, 11, 'dfgbgzdfb', '2026-01-31 13:59:44'),
(6, 4, 11, 'sdxbzds', '2026-01-31 14:00:42'),
(7, 4, 9, 'bdfb ', '2026-01-31 14:01:58');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `name`) VALUES
(5, 'Accounts'),
(4, 'Administration'),
(3, 'Hostel'),
(1, 'IT Department'),
(2, 'Library');

-- --------------------------------------------------------

--
-- Table structure for table `status_history`
--

CREATE TABLE `status_history` (
  `id` int(11) NOT NULL,
  `complaint_id` int(11) NOT NULL,
  `status` varchar(50) NOT NULL,
  `changed_by` int(11) NOT NULL,
  `changed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `status_history`
--

INSERT INTO `status_history` (`id`, `complaint_id`, `status`, `changed_by`, `changed_at`) VALUES
(1, 1, 'resolved', 9, '2026-01-30 16:31:02'),
(2, 2, 'resolved', 9, '2026-01-30 16:49:17'),
(3, 3, 'resolved', 9, '2026-01-30 16:49:45'),
(4, 4, 'pending', 9, '2026-01-31 14:02:02'),
(5, 4, 'resolved', 9, '2026-01-31 14:02:08');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('student','staff','admin') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'System Admin', 'admin@helpdesk.com', '$2y$10$87qLuforpB23EHSCULFJiuxGCnoKhTRM9MZr8oViwEiCNhS8LIHSy', 'admin', '2026-01-30 15:38:38'),
(8, 'zdb', 'abc@gmail.com', '$2y$10$7Az.Y5LFqrbtI.AYElBt3.iNPMy3pXy1hkDoAttyGtKMxNyOBO/li', 'student', '2026-01-30 16:23:43'),
(9, 'IT Staff', 'itstaff@helpdesk.com', '$2y$10$JHt07r4Xzz0URH0CfA1Csu.4h4.Fppwr6.tOILf6dT/CeTpzDf/Mi', 'staff', '2026-01-30 16:29:02'),
(10, 'John Student', 'student@helpdesk.com', '$2y$10$UOb2J2nITuO6/jAxmUqRZOkHZW.KODPWqvFYi5ci3dLRH43hZ4sky', 'student', '2026-01-30 16:29:03'),
(11, 'aryan', 'aryan@gmail.com', '$2y$10$FZCS76TC7TAURIqLgAxtu.WPeDhyPRzby/1KvnvjuyFApqb2N4f/6', 'student', '2026-01-31 13:58:24');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `complaints`
--
ALTER TABLE `complaints`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `department_id` (`department_id`);

--
-- Indexes for table `complaint_replies`
--
ALTER TABLE `complaint_replies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `complaint_id` (`complaint_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `status_history`
--
ALTER TABLE `status_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `complaint_id` (`complaint_id`),
  ADD KEY `changed_by` (`changed_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `complaints`
--
ALTER TABLE `complaints`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `complaint_replies`
--
ALTER TABLE `complaint_replies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `status_history`
--
ALTER TABLE `status_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `complaints`
--
ALTER TABLE `complaints`
  ADD CONSTRAINT `complaints_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `complaints_ibfk_2` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `complaint_replies`
--
ALTER TABLE `complaint_replies`
  ADD CONSTRAINT `complaint_replies_ibfk_1` FOREIGN KEY (`complaint_id`) REFERENCES `complaints` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `complaint_replies_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `status_history`
--
ALTER TABLE `status_history`
  ADD CONSTRAINT `status_history_ibfk_1` FOREIGN KEY (`complaint_id`) REFERENCES `complaints` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `status_history_ibfk_2` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
