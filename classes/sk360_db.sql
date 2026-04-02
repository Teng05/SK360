-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 11, 2026 at 04:08 PM
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
-- Database: `sk360_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `barangays`
--

CREATE TABLE `barangays` (
  `barangay_id` int(11) NOT NULL,
  `barangay_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `barangays`
--

INSERT INTO `barangays` (`barangay_id`, `barangay_name`) VALUES
(1, 'Adya'),
(2, 'Anilao'),
(3, 'Anilao-Labac'),
(4, 'Antipolo del Norte'),
(5, 'Antipolo del Sur'),
(6, 'Bagong Pook'),
(7, 'Balintawak'),
(8, 'Banaybanay'),
(9, 'Barangay 12'),
(10, 'Bolbok'),
(11, 'Bugtong na Pulo'),
(12, 'Bulacnin'),
(13, 'Bulaklakan'),
(14, 'Calamias'),
(15, 'Cumba'),
(16, 'Dagatan'),
(17, 'Duhatan'),
(18, 'Halang'),
(19, 'Inosluban'),
(20, 'Kayumanggi'),
(21, 'Latag'),
(22, 'Lodlod'),
(23, 'Lumbang'),
(24, 'Mabini'),
(25, 'Malagonlong'),
(26, 'Malitlit'),
(27, 'Marauoy'),
(28, 'Mataas na Lupa'),
(29, 'Munting Pulo'),
(30, 'Pagolingin Bata'),
(31, 'Pagolingin East'),
(32, 'Pagolingin West'),
(33, 'Pangao'),
(34, 'Pinagkawitan'),
(35, 'Pinagtongulan'),
(36, 'Plaridel'),
(37, 'Poblacion Barangay 1'),
(38, 'Poblacion Barangay 2'),
(39, 'Poblacion Barangay 3'),
(40, 'Poblacion Barangay 4'),
(41, 'Poblacion Barangay 5'),
(42, 'Poblacion Barangay 6'),
(43, 'Poblacion Barangay 7'),
(44, 'Poblacion Barangay 8'),
(45, 'Poblacion Barangay 9'),
(46, 'Poblacion Barangay 9-A'),
(47, 'Poblacion Barangay 10'),
(48, 'Poblacion Barangay 11'),
(49, 'Pusil'),
(50, 'Quezon'),
(51, 'Rizal'),
(52, 'Sabang'),
(53, 'Sampaguita'),
(54, 'San Benito'),
(55, 'San Carlos'),
(56, 'San Celestino'),
(57, 'San Francisco'),
(58, 'San Guillermo'),
(59, 'San Jose'),
(60, 'San Lucas'),
(61, 'San Salvador'),
(62, 'San Sebastian'),
(63, 'Santo Niño'),
(64, 'Santo Toribio'),
(65, 'Sapac'),
(66, 'Sico'),
(67, 'Talisay'),
(68, 'Tambo'),
(69, 'Tangob'),
(70, 'Tanguay'),
(71, 'Tibig'),
(72, 'Tipacan');

-- --------------------------------------------------------

--
-- Table structure for table `email_verifications`
--

CREATE TABLE `email_verifications` (
  `verification_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `verification_code` varchar(6) DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `reset_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `reset_code` varchar(6) DEFAULT NULL,
  `method` enum('email','phone') DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `first_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `barangay_id` int(11) DEFAULT NULL,
  `role` enum('youth','sk_president','sk_chairman','sk_secretary') DEFAULT NULL,
  `is_verified` tinyint(1) DEFAULT 0,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `event_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `event_type` varchar(50) DEFAULT NULL,
  `start_datetime` datetime NOT NULL,
  `end_datetime` datetime NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `barangays`
--
ALTER TABLE `barangays`
  ADD PRIMARY KEY (`barangay_id`);

--
-- Indexes for table `email_verifications`
--
ALTER TABLE `email_verifications`
  ADD PRIMARY KEY (`verification_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`reset_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`event_id`),
  ADD KEY `created_by` (`created_by`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `barangays`
--
ALTER TABLE `barangays`
  MODIFY `barangay_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT for table `email_verifications`
--
ALTER TABLE `email_verifications`
  MODIFY `verification_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `reset_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `email_verifications`
--
ALTER TABLE `email_verifications`
  ADD CONSTRAINT `email_verifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `events_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
