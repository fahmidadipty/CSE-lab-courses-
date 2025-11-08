-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 17, 2025 at 09:06 PM
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
-- Database: `exchange`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `admin_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('super_admin','staff_admin') DEFAULT 'staff_admin',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`admin_id`, `name`, `email`, `password_hash`, `role`, `created_at`) VALUES
(1, 'Super Admin', 'admin@exchangewise.com', 'hashed_password_here', 'super_admin', '2025-10-13 12:02:27');

-- --------------------------------------------------------

--
-- Table structure for table `credentials`
--

CREATE TABLE `credentials` (
  `credential_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('admin','vendor','user') NOT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `vendor_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `credentials`
--

INSERT INTO `credentials` (`credential_id`, `email`, `password_hash`, `role`, `admin_id`, `vendor_id`, `user_id`, `last_login`, `created_at`, `updated_at`) VALUES
(1, 'admin@exchangewise.com', 'hashed_password_here', 'admin', 1, NULL, NULL, '2025-10-17 10:37:23', '2025-10-13 12:02:28', '2025-10-17 10:37:23'),
(4, 'fahmidadipty550@gmail.com', '$2y$10$0fuxfAMKDYS5APZhA5r.7u6rl2QkpuH7hdjLS.HF3ptmiVSbBw0uO', 'user', NULL, NULL, 2, '2025-10-13 13:00:41', '2025-10-13 12:39:53', '2025-10-13 13:00:41'),
(8, 'h@gmail.com', '$2y$10$u32r5.S61fXa05/1jiavhOIAdC8dEq.8/r1fzKEz.k6xL8TiKn.Fi', 'user', NULL, NULL, 6, '2025-10-17 06:30:26', '2025-10-17 06:26:26', '2025-10-17 06:30:26');

-- --------------------------------------------------------

--
-- Table structure for table `currencies`
--

CREATE TABLE `currencies` (
  `id` int(11) NOT NULL,
  `currency_code` varchar(10) NOT NULL,
  `currency_name` varchar(50) NOT NULL,
  `symbol` varchar(5) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exchange_rates`
--

CREATE TABLE `exchange_rates` (
  `id` int(11) NOT NULL,
  `from_currency` varchar(10) NOT NULL,
  `to_currency` varchar(10) NOT NULL,
  `rate` decimal(15,6) NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `exchange_rates`
--

INSERT INTO `exchange_rates` (`id`, `from_currency`, `to_currency`, `rate`, `updated_at`) VALUES
(1, 'USD', 'BDT', 118.350000, '2025-10-17 08:45:08'),
(2, 'EUR', 'BDT', 128.750000, '2025-10-17 08:45:08'),
(3, 'GBP', 'BDT', 151.200000, '2025-10-17 08:45:08'),
(4, 'INR', 'BDT', 1.420000, '2025-10-17 08:45:08');

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `transaction_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `vendor_id` int(11) DEFAULT NULL,
  `type` enum('Exchange','Deposit','Withdrawal') NOT NULL,
  `from_currency` varchar(10) NOT NULL,
  `to_currency` varchar(10) DEFAULT NULL,
  `amount` decimal(15,2) NOT NULL,
  `converted_amount` decimal(15,2) DEFAULT NULL,
  `rate` decimal(15,6) DEFAULT NULL,
  `fee` decimal(15,2) DEFAULT 0.00,
  `status` enum('Pending','Completed','Cancelled') DEFAULT 'Pending',
  `transaction_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`transaction_id`, `user_id`, `vendor_id`, `type`, `from_currency`, `to_currency`, `amount`, `converted_amount`, `rate`, `fee`, `status`, `transaction_date`) VALUES
(1, 6, NULL, 'Exchange', 'EUR', 'BDT', 80.50, 10364.38, 128.750000, 0.00, 'Completed', '2025-10-17 09:11:34'),
(2, 6, NULL, 'Exchange', 'USD', 'BDT', 1.00, 118.35, 118.350000, 0.00, 'Completed', '2025-10-17 09:12:20'),
(3, 6, NULL, 'Exchange', 'USD', 'BDT', 1.00, 118.35, 118.350000, 0.00, 'Completed', '2025-10-17 09:14:12'),
(4, 6, NULL, 'Exchange', 'USD', 'BDT', 25.00, 2958.75, 118.350000, 0.00, 'Completed', '2025-10-17 09:14:30'),
(5, 6, NULL, 'Exchange', 'USD', 'BDT', 1.00, 118.35, 118.350000, 0.00, 'Completed', '2025-10-17 09:14:43'),
(6, 6, NULL, 'Exchange', 'USD', 'BDT', 1.00, 118.35, 118.350000, 0.00, 'Completed', '2025-10-17 09:18:44'),
(7, 6, NULL, 'Exchange', 'USD', 'BDT', 1.00, 118.35, 118.350000, 0.00, 'Completed', '2025-10-17 09:21:56'),
(8, 6, NULL, 'Exchange', 'USD', 'BDT', 1.00, 118.35, 118.350000, 0.00, 'Completed', '2025-10-17 09:28:58'),
(9, 6, NULL, 'Exchange', 'USD', 'BDT', 1.00, 118.35, 118.350000, 0.00, 'Completed', '2025-10-17 09:36:14'),
(10, 6, NULL, 'Exchange', 'USD', 'BDT', 0.00, 0.00, 118.350000, 0.00, 'Completed', '2025-10-17 09:36:16'),
(11, 6, NULL, 'Exchange', 'USD', 'BDT', 2.00, 236.70, 118.350000, 0.00, 'Completed', '2025-10-17 09:36:32'),
(12, 6, NULL, 'Deposit', 'EUR', NULL, 10.00, NULL, NULL, 0.00, 'Completed', '2025-10-17 09:48:09'),
(13, 6, NULL, 'Withdrawal', 'EUR', NULL, 10.00, NULL, NULL, 0.00, 'Completed', '2025-10-17 09:52:45');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `mobile_number` varchar(20) NOT NULL,
  `passport_number` varchar(50) NOT NULL,
  `passport_file` varchar(255) DEFAULT NULL,
  `nid_number` varchar(50) NOT NULL,
  `nid_file` varchar(255) DEFAULT NULL,
  `address` varchar(255) NOT NULL,
  `address_proof_file` varchar(255) DEFAULT NULL,
  `occupation` enum('Job','Business','Freelancer','Others') NOT NULL,
  `purpose` enum('Travel','Education','Remittance','OnlinePayment','Others') NOT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `first_name`, `last_name`, `email`, `mobile_number`, `passport_number`, `passport_file`, `nid_number`, `nid_file`, `address`, `address_proof_file`, `occupation`, `purpose`, `profile_picture`, `password_hash`, `created_at`, `updated_at`) VALUES
(2, 'exchange', 'ddd', 'fahmidadipty550@gmail.com', '+8801785623753', 'ddddddd', 'uploads/132compiler.pdf', 'ddd33455555', 'uploads/132compiler.pdf', 'ddhbudhbdhbhd', 'uploads/2215151132.docx', 'Job', 'Education', '', '$2y$10$0fuxfAMKDYS5APZhA5r.7u6rl2QkpuH7hdjLS.HF3ptmiVSbBw0uO', '2025-10-13 12:39:53', '2025-10-13 12:39:53'),
(6, 'hossain ', 'chowdhuri', 'h@gmail.com', '0175555555', 'BD-125455', '', '21269125365', '', 'dhaka', '', 'Job', 'Travel', '', '$2y$10$u32r5.S61fXa05/1jiavhOIAdC8dEq.8/r1fzKEz.k6xL8TiKn.Fi', '2025-10-17 06:26:26', '2025-10-17 06:32:43');

-- --------------------------------------------------------

--
-- Table structure for table `vendor`
--

CREATE TABLE `vendor` (
  `vendor_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `business_name` varchar(100) DEFAULT NULL,
  `kyc_verified` tinyint(1) DEFAULT 0,
  `status` enum('active','inactive','suspended') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vendor`
--

INSERT INTO `vendor` (`vendor_id`, `name`, `email`, `password_hash`, `phone`, `address`, `business_name`, `kyc_verified`, `status`, `created_at`, `updated_at`) VALUES
(2, 'hossain ahammed', 'h@gmail.com', '', '0175555555', 'dhaka', 'ghhd', 1, 'active', '2025-10-17 19:00:40', '2025-10-17 19:00:40');

-- --------------------------------------------------------

--
-- Table structure for table `wallets`
--

CREATE TABLE `wallets` (
  `wallet_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `currency_code` varchar(10) NOT NULL,
  `balance` decimal(15,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wallets`
--

INSERT INTO `wallets` (`wallet_id`, `user_id`, `currency_code`, `balance`) VALUES
(7, 2, 'EUR', 80.50),
(8, 2, 'USD', 120.00),
(9, 6, 'USD', 1256.75),
(10, 6, 'BDT', 8015355.05),
(11, 6, 'EUR', 10.00),
(12, 6, 'BDT', 10719.43),
(13, 6, 'BDT', 473.40),
(14, 6, 'BDT', 473.40),
(15, 6, 'BDT', 3313.80),
(16, 6, 'BDT', 473.40),
(17, 6, 'BDT', 473.40),
(18, 6, 'BDT', 473.40),
(19, 6, 'BDT', 473.40);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `credentials`
--
ALTER TABLE `credentials`
  ADD PRIMARY KEY (`credential_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `admin_id` (`admin_id`),
  ADD KEY `vendor_id` (`vendor_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `currencies`
--
ALTER TABLE `currencies`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `currency_code` (`currency_code`);

--
-- Indexes for table `exchange_rates`
--
ALTER TABLE `exchange_rates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`transaction_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `vendor_id` (`vendor_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `vendor`
--
ALTER TABLE `vendor`
  ADD PRIMARY KEY (`vendor_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `wallets`
--
ALTER TABLE `wallets`
  ADD PRIMARY KEY (`wallet_id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `credentials`
--
ALTER TABLE `credentials`
  MODIFY `credential_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `currencies`
--
ALTER TABLE `currencies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `exchange_rates`
--
ALTER TABLE `exchange_rates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `transaction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `vendor`
--
ALTER TABLE `vendor`
  MODIFY `vendor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `wallets`
--
ALTER TABLE `wallets`
  MODIFY `wallet_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `credentials`
--
ALTER TABLE `credentials`
  ADD CONSTRAINT `credentials_ibfk_1` FOREIGN KEY (`admin_id`) REFERENCES `admin` (`admin_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `credentials_ibfk_2` FOREIGN KEY (`vendor_id`) REFERENCES `vendor` (`vendor_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `credentials_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `transactions_ibfk_2` FOREIGN KEY (`vendor_id`) REFERENCES `vendor` (`vendor_id`) ON DELETE SET NULL;

--
-- Constraints for table `wallets`
--
ALTER TABLE `wallets`
  ADD CONSTRAINT `wallets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
