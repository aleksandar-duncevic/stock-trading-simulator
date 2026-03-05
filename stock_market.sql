-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 20, 2024 at 11:26 PM
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
-- Database: `berza`
--

-- --------------------------------------------------------

--
-- Table structure for table `max_signed_in`
--

CREATE TABLE `max_signed_in` (
  `id` int(11) NOT NULL,
  `max_signed_in` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `max_signed_in`
--

INSERT INTO `max_signed_in` (`id`, `max_signed_in`) VALUES
(1, 2);

-- --------------------------------------------------------

--
-- Table structure for table `role`
--

CREATE TABLE `role` (
  `id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `role`
--

INSERT INTO `role` (`id`, `name`) VALUES
(1, 'Administrator'),
(2, 'User');

-- --------------------------------------------------------

--
-- Table structure for table `share`
--

CREATE TABLE `share` (
  `id` int(11) NOT NULL,
  `symbol` varchar(64) NOT NULL,
  `shares` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `share`
--

INSERT INTO `share` (`id`, `symbol`, `shares`, `user_id`) VALUES
(1, 'GOOG', 37, 8),
(2, 'TSLA', 2, 1),
(4, 'TSLA', 12, 8),
(12, 'GOOG', 1, 14),
(13, 'AAPL', 11, 15),
(14, 'SOUN', 2, 15),
(15, 'TSLA', 10, 15);

-- --------------------------------------------------------

--
-- Table structure for table `transaction`
--

CREATE TABLE `transaction` (
  `id` int(11) NOT NULL,
  `symbol` varchar(64) NOT NULL,
  `shares` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `transacted_at` datetime NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaction`
--

INSERT INTO `transaction` (`id`, `symbol`, `shares`, `price`, `transacted_at`, `user_id`) VALUES
(2, 'AMZN', 2, 348.84, '2024-03-16 15:59:30', 8),
(3, 'GOOG', 10, 1421.70, '2024-03-16 16:02:50', 8),
(4, 'GOOG', 10, 0.00, '2024-03-16 21:05:50', 8),
(5, 'GOOG', 10, 0.00, '2024-03-16 21:05:58', 8),
(6, 'TSLA', 3, 490.71, '2024-03-16 21:23:52', 8),
(7, 'BMW.DE', 1, 105.68, '2024-03-16 21:26:49', 8),
(8, 'ACGL', 1, 91.88, '2024-03-16 21:36:54', 8),
(9, 'LULU', 5, 2324.70, '2024-03-17 05:17:29', 8),
(10, 'GOOG', 3, 142.17, '2024-03-17 06:11:56', 8),
(11, 'TSLA', 2, 163.57, '2024-03-17 06:40:13', 8),
(12, 'AMZN', 2, 174.42, '2024-03-17 06:42:04', 8),
(13, 'GOOG', 1, 142.17, '2024-03-17 06:46:14', 8),
(14, 'TSLA', 2, 163.57, '2024-03-17 07:11:55', 8),
(15, 'ACGL', 1, 91.88, '2024-03-17 13:09:23', 8),
(16, 'BMW.DE', -1, 105.68, '2024-03-17 13:11:25', 8),
(17, 'LULU', -5, 464.94, '2024-03-17 13:12:54', 8),
(18, 'AMZN', -4, 174.42, '2024-03-17 13:13:45', 8),
(19, 'GOOG', 1, 148.40, '2024-03-18 19:49:58', 14),
(20, 'AAPL', 1, 178.67, '2024-03-20 22:56:53', 15),
(21, 'AAPL', 10, 178.67, '2024-03-20 22:58:05', 15),
(22, 'SOUN', 5, 7.93, '2024-03-20 22:58:26', 15),
(23, 'TSLA', 10, 175.66, '2024-03-20 22:58:35', 15),
(24, 'SOUN', -3, 7.93, '2024-03-20 23:00:02', 15);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `name` varchar(128) NOT NULL,
  `email` varchar(256) NOT NULL,
  `password` varchar(256) NOT NULL,
  `registered_at` datetime NOT NULL DEFAULT current_timestamp(),
  `last_sign_in` datetime DEFAULT NULL,
  `signed_in` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(4) NOT NULL DEFAULT 1,
  `cash` decimal(10,2) NOT NULL DEFAULT 10000.00,
  `role_id` int(11) NOT NULL DEFAULT 2
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `name`, `email`, `password`, `registered_at`, `last_sign_in`, `signed_in`, `is_active`, `cash`, `role_id`) VALUES
(1, 'test', 'test@test.com', '$2y$10$NcwJf7yiN25wofuztCNvJewpSzwP4tk9Zr50XdcaoYj6qRYMYxIu.', '2024-03-18 00:00:00', NULL, 0, 0, 10000.00, 2),
(2, 'test1', 'test1@test1.com', '$2y$10$Ip13Na7TP7SLASsnLniEl.bfU8JgFOUFVt0Pwi3NZDmcwnNkHX6ni', '2024-03-18 00:00:00', NULL, 0, 1, 10000.00, 2),
(3, 'test2', 'test2@test2.com', '$2y$10$aKtM/ILJ06zcjktJcg22VOTC3wZokBuTyUI1PbeQCnNGr0RQihukm', '2024-03-18 00:00:00', NULL, 0, 1, 10000.00, 2),
(4, 'test3', 'test3@test3.com', '$2y$10$PHHFAkVdfaftTHreVvbuSew7CvD7omUNKxV/l.8YQGSVw2d12Ixm2', '2024-03-18 00:00:00', NULL, 0, 1, 10000.00, 2),
(5, 'test4', 'test4@test4.com', '$2y$10$OW.CjKvb.dbOTwfLfttRnOGgu2PRYnH.0Zom5V60hODY9q3zspPa.', '2024-03-18 00:00:00', NULL, 0, 1, 10000.00, 2),
(6, 'test5', 'test5@test5.com', '$2y$10$lBvCMBOA.zy1FDp9bJLEzOCrTtIK1SAxpy0kb197zFwOeCFyk7mXu', '2024-03-18 00:00:00', NULL, 0, 1, 10000.00, 2),
(7, 'test6', 'test6@test6.com', '$2y$10$llDTjHsGFxqBiaBwXT4huu0d8dUKhmhcK.12MSOc59vb8do9o/53a', '2024-03-18 00:00:00', NULL, 0, 1, 10000.00, 2),
(8, 'test7', 'test7@test7.com', '$2y$10$miCx/fE/PPq4Ky/JJK8MSeYli1nexCwxuBO7GGTnxYBlK8seAn5ZW', '2024-03-18 00:00:00', '2024-03-20 22:38:02', 0, 1, 6680.87, 2),
(9, 'test8', 'test8@test8.com', '$2y$10$4hYtwcvQviP.xDiIlQZXKu9RH6W8iIY3jyrgMejkEthyBFQyG6I6q', '2024-03-18 00:00:00', '2024-03-18 18:07:34', 0, 0, 10000.00, 2),
(10, 'test9', 'test9@test9.com', '$2y$10$UZ9EOn/w.XlmT3NHSyOduO9ZY1o8Wroeoh6bashaSqfXZ37tb8FAS', '2024-03-18 00:00:00', '2024-03-20 23:12:06', 0, 1, 10000.00, 1),
(11, 'test10', 'test10@test10.com', '$2y$10$nsAJ/bQ.j9YI5YkB1lgbDu10PeeT/nRVVkLbYFXxOjx0mku.1M1k6', '2024-03-18 19:40:34', NULL, 0, 1, 10000.00, 2),
(12, 'test11', 'test11@test11.com', '$2y$10$HhAQSZVO12eqbKJjjwCpueQO/leiCba4QTkPg00U6.KE06/ndDQg.', '2024-03-18 19:44:20', NULL, 0, 1, 10000.00, 2),
(13, 'test12', 'test12@test12.com', '$2y$10$vW/z5QZ3r0P3FQ2souLF7OTkwVgbph5J4ip/9K69soYzcosD6LN3G', '2024-03-18 19:45:03', NULL, 0, 1, 10000.00, 2),
(14, 'test13', 'test13@test13.com', '$2y$10$faKv.6inOpF.bAx5Oo6rfOm..AUVFv9EuwxNwztJ/zu8FwrqtzsBW', '2024-03-18 19:49:02', '2024-03-18 19:49:23', 0, 1, 9851.60, 2),
(15, 'lkjhk', 'maria@gmail.com', '$2y$10$7wZyuz4GmHFZKruqUCeWu.8GQlWGoBSu8dzI8Vsz0CSrYAblZ/ol.', '2024-03-20 22:52:07', '2024-03-20 23:12:26', 0, 1, 6262.17, 2),
(16, 'administrator', 'administrator@administrator.com', '$2y$10$EJfZqb7qKmrWqcK2R0640.4RTgi4jHXDnmJj/KFQpfZRDlmeXYw76', '2024-03-20 23:16:08', '2024-03-20 23:17:13', 0, 1, 10000.00, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `max_signed_in`
--
ALTER TABLE `max_signed_in`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `role`
--
ALTER TABLE `role`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `share`
--
ALTER TABLE `share`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `transaction`
--
ALTER TABLE `transaction`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_user_email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `max_signed_in`
--
ALTER TABLE `max_signed_in`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `role`
--
ALTER TABLE `role`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `share`
--
ALTER TABLE `share`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `transaction`
--
ALTER TABLE `transaction`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
