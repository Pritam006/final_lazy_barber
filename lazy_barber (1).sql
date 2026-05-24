-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Generation Time: May 24, 2026 at 12:46 PM
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
-- Database: `lazy_barber`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `appointmentid` int(11) NOT NULL,
  `customerid` int(11) NOT NULL,
  `barberid` int(11) NOT NULL,
  `serviceid` int(11) NOT NULL,
  `appointment_date` date NOT NULL,
  `time_slot` time NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `status` enum('pending','confirmed','cancelled','completed') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`appointmentid`, `customerid`, `barberid`, `serviceid`, `appointment_date`, `time_slot`, `total_price`, `status`, `created_at`, `updated_at`) VALUES
(4, 2, 62, 72, '2026-05-06', '11:30:00', 25.00, 'cancelled', '2026-05-10 07:10:53', '2026-05-24 09:58:05'),
(5, 2, 62, 71, '2026-05-10', '11:30:00', 30.00, 'pending', '2026-05-10 07:11:27', '2026-05-10 07:11:27'),
(6, 2, 68, 76, '2026-05-21', '11:00:00', 48.00, 'cancelled', '2026-05-10 09:30:26', '2026-05-15 10:38:59'),
(7, 2, 55, 64, '2026-05-15', '09:30:00', 55.00, 'completed', '2026-05-15 12:36:29', '2026-05-15 23:22:43'),
(8, 2, 55, 65, '2026-05-17', '11:30:00', 60.00, 'cancelled', '2026-05-15 12:45:52', '2026-05-15 22:31:36'),
(9, 2, 60, 71, '2026-05-14', '11:30:00', 30.00, 'cancelled', '2026-05-15 23:21:06', '2026-05-17 05:21:58'),
(10, 105, 66, 76, '2026-05-29', '11:00:00', 48.00, 'pending', '2026-05-15 23:58:22', '2026-05-15 23:58:22'),
(11, 55, 56, 65, '2026-05-14', '09:30:00', 60.00, 'pending', '2026-05-17 00:25:26', '2026-05-17 00:25:26'),
(12, 55, 59, 69, '2026-05-17', '09:30:00', 25.00, 'pending', '2026-05-17 00:25:58', '2026-05-17 00:25:58'),
(13, 55, 58, 68, '2026-05-15', '11:30:00', 20.00, 'pending', '2026-05-17 00:27:33', '2026-05-17 00:27:33'),
(14, 106, 72, 83, '2026-05-09', '09:00:00', 58.00, 'pending', '2026-05-17 10:16:48', '2026-05-17 10:16:48'),
(15, 106, 72, 82, '2026-05-19', '11:30:00', 55.00, 'pending', '2026-05-17 10:17:12', '2026-05-17 10:17:12'),
(16, 106, 75, 90, '2026-05-31', '11:30:00', 50.00, 'pending', '2026-05-17 10:18:00', '2026-05-17 10:18:00'),
(17, 2, 66, 76, '2026-05-16', '11:00:00', 48.00, 'pending', '2026-05-24 08:36:26', '2026-05-24 08:36:26'),
(18, 2, 68, 77, '2026-05-25', '11:30:00', 25.00, 'pending', '2026-05-24 10:27:53', '2026-05-24 10:27:53');

-- --------------------------------------------------------

--
-- Table structure for table `availability`
--

CREATE TABLE `availability` (
  `availabilityid` int(11) NOT NULL,
  `barberid` int(11) NOT NULL,
  `day_of_week` tinyint(4) NOT NULL COMMENT '0=Sunday, 1=Monday, ..., 6=Saturday',
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `is_blocked` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `availability`
--

INSERT INTO `availability` (`availabilityid`, `barberid`, `day_of_week`, `start_time`, `end_time`, `is_blocked`) VALUES
(6, 55, 0, '09:00:00', '17:00:00', 1),
(7, 55, 1, '09:00:00', '17:00:00', 0),
(8, 55, 2, '09:00:00', '17:00:00', 0),
(9, 55, 3, '09:00:00', '17:00:00', 0),
(10, 55, 4, '09:00:00', '17:00:00', 0),
(11, 55, 5, '09:00:00', '17:00:00', 0),
(12, 55, 6, '09:00:00', '17:00:00', 0),
(13, 75, 0, '09:00:00', '17:00:00', 0),
(14, 75, 1, '09:00:00', '17:00:00', 0),
(15, 75, 2, '09:00:00', '17:00:00', 0),
(16, 75, 3, '09:00:00', '17:00:00', 0),
(17, 75, 4, '09:00:00', '17:00:00', 0),
(18, 75, 5, '09:00:00', '17:00:00', 0),
(19, 75, 6, '09:00:00', '17:00:00', 0),
(20, 107, 0, '09:00:00', '17:00:00', 0),
(21, 107, 1, '09:00:00', '17:00:00', 0),
(22, 107, 2, '09:00:00', '17:00:00', 0),
(23, 107, 3, '09:00:00', '17:00:00', 0),
(24, 107, 4, '09:00:00', '17:00:00', 0),
(25, 107, 5, '09:00:00', '17:00:00', 0),
(26, 107, 6, '09:00:00', '17:00:00', 0);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notifid` int(11) NOT NULL,
  `customerid` int(11) NOT NULL,
  `barberid` int(11) NOT NULL,
  `appointmentid` int(11) NOT NULL,
  `type` enum('booking_confirmation','reminder','cancellation','update') NOT NULL,
  `sent_at` timestamp NULL DEFAULT NULL,
  `status` enum('pending','sent','failed') DEFAULT 'pending',
  `retry_count` tinyint(4) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`notifid`, `customerid`, `barberid`, `appointmentid`, `type`, `sent_at`, `status`, `retry_count`) VALUES
(2, 55, 56, 11, 'booking_confirmation', NULL, 'failed', 0),
(3, 55, 56, 11, 'booking_confirmation', NULL, 'failed', 0),
(4, 55, 59, 12, 'booking_confirmation', NULL, 'failed', 0),
(5, 55, 59, 12, 'booking_confirmation', NULL, 'failed', 0),
(6, 55, 58, 13, 'booking_confirmation', NULL, 'failed', 0),
(7, 55, 58, 13, 'booking_confirmation', NULL, 'failed', 0),
(8, 2, 60, 9, 'cancellation', NULL, 'failed', 0),
(9, 2, 60, 9, 'cancellation', NULL, 'failed', 0),
(10, 106, 72, 14, 'booking_confirmation', NULL, 'failed', 0),
(11, 106, 72, 14, 'booking_confirmation', NULL, 'failed', 0),
(12, 106, 72, 15, 'booking_confirmation', NULL, 'failed', 0),
(13, 106, 72, 15, 'booking_confirmation', NULL, 'failed', 0),
(14, 106, 75, 16, 'booking_confirmation', NULL, 'failed', 0),
(15, 106, 75, 16, 'booking_confirmation', NULL, 'failed', 0),
(16, 2, 66, 17, 'booking_confirmation', NULL, 'failed', 0),
(17, 2, 66, 17, 'booking_confirmation', NULL, 'failed', 0),
(18, 2, 62, 4, 'cancellation', NULL, 'failed', 0),
(19, 2, 62, 4, 'cancellation', NULL, 'failed', 0),
(20, 2, 62, 4, 'cancellation', NULL, 'failed', 0),
(21, 2, 62, 4, 'cancellation', NULL, 'failed', 0),
(22, 2, 68, 18, 'booking_confirmation', NULL, 'failed', 0),
(23, 2, 68, 18, 'booking_confirmation', NULL, 'failed', 0);

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `serviceid` int(11) NOT NULL,
  `shopid` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price_aud` decimal(10,2) NOT NULL,
  `duration_minutes` int(11) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`serviceid`, `shopid`, `name`, `description`, `price_aud`, `duration_minutes`, `is_active`) VALUES
(64, 11, 'Executive Haircut', NULL, 55.00, 30, 1),
(65, 11, 'Skin Fade', NULL, 60.00, 30, 1),
(66, 11, 'Beard Sculpting', NULL, 35.00, 30, 1),
(67, 11, 'Razor Shave', NULL, 40.00, 30, 1),
(68, 11, 'Hair Wash & Style', NULL, 20.00, 30, 1),
(69, 11, 'Facial Grooming', NULL, 25.00, 30, 1),
(70, 12, 'Taper Fade', NULL, 50.00, 30, 1),
(71, 12, 'Buzz Cut', NULL, 30.00, 30, 1),
(72, 12, 'Beard Line-Up', NULL, 25.00, 30, 1),
(73, 12, 'Hot Towel Shave', NULL, 45.00, 30, 1),
(74, 12, 'Hair Styling', NULL, 20.00, 30, 1),
(75, 12, 'Kids Haircut', NULL, 28.00, 30, 1),
(76, 13, 'Traditional Haircut', NULL, 48.00, 30, 1),
(77, 13, 'Beard Trim', NULL, 25.00, 30, 1),
(78, 13, 'Razor Fade', NULL, 55.00, 30, 1),
(79, 13, 'Long Hair Styling', NULL, 65.00, 30, 1),
(80, 13, 'Hot Towel Treatment', NULL, 30.00, 30, 1),
(81, 13, 'Grooming Package', NULL, 85.00, 30, 1),
(82, 14, 'Skin Fade', NULL, 55.00, 30, 1),
(83, 14, 'Zero Fade', NULL, 58.00, 30, 1),
(84, 14, 'Beard Fade', NULL, 30.00, 30, 1),
(85, 14, 'Shape Up', NULL, 22.00, 30, 1),
(86, 14, 'Hair Tattoo', NULL, 35.00, 30, 1),
(87, 14, 'Razor Finish', NULL, 18.00, 30, 1),
(88, 15, 'Gentleman’s Haircut', NULL, 60.00, 30, 1),
(89, 15, 'Beard Styling', NULL, 35.00, 30, 1),
(90, 15, 'Luxury Shave', NULL, 50.00, 30, 1),
(91, 15, 'Facial Grooming', NULL, 30.00, 30, 1),
(92, 15, 'Hair Styling', NULL, 25.00, 30, 1),
(93, 15, 'Premium Groom Package', NULL, 95.00, 30, 1),
(94, 16, 'Classic Men’s Cut', NULL, 45.00, 30, 1),
(95, 16, 'Scissor Cut', NULL, 50.00, 30, 1),
(96, 16, 'Beard Trim', NULL, 25.00, 30, 1),
(97, 16, 'Fade Cut', NULL, 52.00, 30, 1),
(98, 16, 'Hair Wash', NULL, 15.00, 30, 1),
(99, 16, 'Styling Finish', NULL, 18.00, 30, 1),
(100, 17, 'Haircut', NULL, 50.00, 30, 1),
(101, 17, 'Skin Fade', NULL, 58.00, 30, 1),
(102, 17, 'Beard Trim', NULL, 28.00, 30, 1),
(103, 17, 'Hot Towel Shave', NULL, 45.00, 30, 1),
(104, 17, 'Buzz Cut', NULL, 30.00, 30, 1),
(105, 17, 'Hair Styling', NULL, 22.00, 30, 1),
(106, 18, 'Precision Cut', NULL, 62.00, 30, 1),
(107, 18, 'Skin Fade', NULL, 60.00, 30, 1),
(108, 18, 'Beard Sculpting', NULL, 35.00, 30, 1),
(109, 18, 'Razor Shave', NULL, 42.00, 30, 1),
(110, 18, 'Head Massage', NULL, 20.00, 30, 1),
(111, 18, 'Groom Package', NULL, 100.00, 30, 1),
(112, 19, 'Fade Haircut', NULL, 55.00, 30, 1),
(113, 19, 'Beard Trim', NULL, 27.00, 30, 1),
(114, 19, 'Razor Edge-Up', NULL, 20.00, 30, 1),
(115, 19, 'Crew Cut', NULL, 32.00, 30, 1),
(116, 19, 'Hair Design', NULL, 38.00, 30, 1),
(117, 19, 'Styling', NULL, 18.00, 30, 1),
(118, 20, 'Pompadour Styling', NULL, 60.00, 30, 1),
(119, 20, 'Classic Barber Cut', NULL, 48.00, 30, 1),
(120, 20, 'Beard Trim', NULL, 28.00, 30, 1),
(121, 20, 'Skin Fade', NULL, 58.00, 30, 1),
(122, 20, 'Razor Shave', NULL, 40.00, 30, 1),
(123, 20, 'Retro Styling', NULL, 65.00, 30, 1);

-- --------------------------------------------------------

--
-- Table structure for table `shops`
--

CREATE TABLE `shops` (
  `shopid` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `suburb` varchar(100) NOT NULL,
  `open_time` varchar(100) DEFAULT '09:00 AM - 05:00 PM',
  `phone` varchar(20) DEFAULT '0400 000 000',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `avatar` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shops`
--

INSERT INTO `shops` (`shopid`, `name`, `address`, `suburb`, `open_time`, `phone`, `is_active`, `created_at`, `avatar`) VALUES
(11, 'Kings Domain Barber Shop', '27 Harbour View Street, Bondi Junction NSW 2022', 'Bondi Junction', '09:00 AM - 05:00 PM', '0400 000 000', 1, '2026-05-10 07:02:46', NULL),
(12, 'UNOIT Barber', '114 Kent Lane, Parramatta NSW 2150', 'Parramatta', '09:00 AM - 05:00 PM', '0400 000 000', 1, '2026-05-10 07:02:46', NULL),
(13, 'Surry Hills Barbers', '82 Cooper Street, Surry Hills NSW 2010', 'Surry Hills', '09:00 AM - 05:00 PM', '0400 000 000', 1, '2026-05-10 07:02:46', NULL),
(14, 'Adilla Barbers', '63 Regent Plaza Street, Liverpool NSW 2170', 'Liverpool', '09:00 AM - 05:00 PM', '0400 000 000', 1, '2026-05-10 07:02:46', NULL),
(15, 'Mens Biz Barber Shop', '45 Crown Exchange Road, Chatswood NSW 2067', 'Chatswood', '09:00 AM - 05:00 PM', '0400 000 000', 1, '2026-05-10 07:02:46', NULL),
(16, 'Village Barber', '19 Railway Terrace, Newtown NSW 2042', 'Newtown', '09:00 AM - 05:00 PM', '0400 000 000', 1, '2026-05-10 07:02:46', NULL),
(17, 'The Barberhood', '132 Harbour Lane, Blacktown NSW 2148', 'Blacktown', '09:00 AM - 05:00 PM', '0400 000 000', 1, '2026-05-10 07:02:46', NULL),
(18, 'Sterling Barber Co.', '76 Oxford Heights Street, Paddington NSW 2021', 'Paddington', '09:00 AM - 05:00 PM', '0400 000 000', 1, '2026-05-10 07:02:46', NULL),
(19, 'Boston Cut Barber Shop', '58 City Central Avenue, Burwood NSW 2134', 'Burwood', '09:00 AM - 05:00 PM', '0400 000 000', 1, '2026-05-10 07:02:46', NULL),
(20, 'Chicanos Barber Shop', '24 Riverside Crescent, Marrickville NSW 2204', 'Marrickville', '09:00 AM - 05:00 PM', '0400 000 000', 1, '2026-05-10 07:02:46', NULL),
(21, 'Jhingalala Barber', 'paramatta,', 'Campsie', '09:00 AM - 05:00 PM', '0400 000 000', 1, '2026-05-24 10:00:31', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `userid` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `role` enum('customer','barber') DEFAULT 'customer',
  `is_shopowner` tinyint(1) DEFAULT 0,
  `shopid` int(11) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `avatar` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`userid`, `name`, `email`, `password_hash`, `phone`, `role`, `is_shopowner`, `shopid`, `is_active`, `created_at`, `avatar`) VALUES
(2, 'Pritam Thapa', 'tpritam006@gmail.com', '$2y$10$bqDzUEoMLG.p0lUgUbKrs.Ia9Og2wkVCzSHw4ZhAy96oJkeKDj7wK', '0424925828', 'customer', 0, NULL, 1, '2026-05-05 09:51:29', '1779611898_pp size pritam.jpg'),
(4, 'Isha Shrestha', 'isha@gmail.com', '$2y$10$gtYvbUaYH9YSONhQchDGgegngaR71C6eQyTxHJeMofLJ/25ru0hF6', '020202', 'customer', 0, NULL, 1, '2026-05-09 04:38:11', NULL),
(55, 'Johnny Russo', 'johnny.russo@example.com', '$2y$10$CsXTkkqRoEYr.U6iWm4I5Oo8lN2XtJmlmLS8TwGupsO5Vq42QEHIq', NULL, 'barber', 1, 11, 1, '2026-05-10 07:02:46', NULL),
(56, 'Marcus Lee', 'marcus.lee@example.com', '$2y$10$CsXTkkqRoEYr.U6iWm4I5Oo8lN2XtJmlmLS8TwGupsO5Vq42QEHIq', NULL, 'barber', 0, 11, 1, '2026-05-10 07:02:46', NULL),
(57, 'Kevin Parker', 'kevin.parker@example.com', '$2y$10$CsXTkkqRoEYr.U6iWm4I5Oo8lN2XtJmlmLS8TwGupsO5Vq42QEHIq', NULL, 'barber', 0, 11, 1, '2026-05-10 07:02:46', NULL),
(58, 'Luca Marino', 'luca.marino@example.com', '$2y$10$CsXTkkqRoEYr.U6iWm4I5Oo8lN2XtJmlmLS8TwGupsO5Vq42QEHIq', NULL, 'barber', 0, 11, 1, '2026-05-10 07:02:46', NULL),
(59, 'Ibrahim Khan', 'ibrahim.khan@example.com', '$2y$10$CsXTkkqRoEYr.U6iWm4I5Oo8lN2XtJmlmLS8TwGupsO5Vq42QEHIq', NULL, 'barber', 0, 11, 1, '2026-05-10 07:02:46', NULL),
(60, 'Ali Rahman', 'ali.rahman@example.com', '$2y$10$CsXTkkqRoEYr.U6iWm4I5Oo8lN2XtJmlmLS8TwGupsO5Vq42QEHIq', NULL, 'barber', 1, 12, 1, '2026-05-10 07:02:46', NULL),
(61, 'Hussein Malik', 'hussein.malik@example.com', '$2y$10$CsXTkkqRoEYr.U6iWm4I5Oo8lN2XtJmlmLS8TwGupsO5Vq42QEHIq', NULL, 'barber', 0, 12, 1, '2026-05-10 07:02:46', NULL),
(62, 'Rami George', 'rami.george@example.com', '$2y$10$CsXTkkqRoEYr.U6iWm4I5Oo8lN2XtJmlmLS8TwGupsO5Vq42QEHIq', NULL, 'barber', 0, 12, 1, '2026-05-10 07:02:46', NULL),
(63, 'Adam Cole', 'adam.cole@example.com', '$2y$10$CsXTkkqRoEYr.U6iWm4I5Oo8lN2XtJmlmLS8TwGupsO5Vq42QEHIq', NULL, 'barber', 0, 12, 1, '2026-05-10 07:02:46', NULL),
(64, 'Chris Marino', 'chris.marino@example.com', '$2y$10$CsXTkkqRoEYr.U6iWm4I5Oo8lN2XtJmlmLS8TwGupsO5Vq42QEHIq', NULL, 'barber', 0, 12, 1, '2026-05-10 07:02:46', NULL),
(65, 'Nick Taylor', 'nick.taylor@example.com', '$2y$10$CsXTkkqRoEYr.U6iWm4I5Oo8lN2XtJmlmLS8TwGupsO5Vq42QEHIq', NULL, 'barber', 1, 13, 1, '2026-05-10 07:02:46', NULL),
(66, 'George Adams', 'george.adams@example.com', '$2y$10$CsXTkkqRoEYr.U6iWm4I5Oo8lN2XtJmlmLS8TwGupsO5Vq42QEHIq', NULL, 'barber', 0, 13, 1, '2026-05-10 07:02:46', NULL),
(67, 'Harry Wilson', 'harry.wilson@example.com', '$2y$10$CsXTkkqRoEYr.U6iWm4I5Oo8lN2XtJmlmLS8TwGupsO5Vq42QEHIq', NULL, 'barber', 0, 13, 1, '2026-05-10 07:02:46', NULL),
(68, 'Tom Carter', 'tom.carter@example.com', '$2y$10$CsXTkkqRoEYr.U6iWm4I5Oo8lN2XtJmlmLS8TwGupsO5Vq42QEHIq', NULL, 'barber', 0, 13, 1, '2026-05-10 07:02:46', NULL),
(69, 'Eli Brown', 'eli.brown@example.com', '$2y$10$CsXTkkqRoEYr.U6iWm4I5Oo8lN2XtJmlmLS8TwGupsO5Vq42QEHIq', NULL, 'barber', 0, 13, 1, '2026-05-10 07:02:46', NULL),
(70, 'Moe Hassan', 'moe.hassan@example.com', '$2y$10$CsXTkkqRoEYr.U6iWm4I5Oo8lN2XtJmlmLS8TwGupsO5Vq42QEHIq', NULL, 'barber', 1, 14, 1, '2026-05-10 07:02:46', NULL),
(71, 'Bilal Ahmed', 'bilal.ahmed@example.com', '$2y$10$CsXTkkqRoEYr.U6iWm4I5Oo8lN2XtJmlmLS8TwGupsO5Vq42QEHIq', NULL, 'barber', 0, 14, 1, '2026-05-10 07:02:46', NULL),
(72, 'Zayn Ali', 'zayn.ali@example.com', '$2y$10$CsXTkkqRoEYr.U6iWm4I5Oo8lN2XtJmlmLS8TwGupsO5Vq42QEHIq', NULL, 'barber', 0, 14, 1, '2026-05-10 07:02:46', NULL),
(73, 'Kareem Yusuf', 'kareem.yusuf@example.com', '$2y$10$CsXTkkqRoEYr.U6iWm4I5Oo8lN2XtJmlmLS8TwGupsO5Vq42QEHIq', NULL, 'barber', 0, 14, 1, '2026-05-10 07:02:46', NULL),
(74, 'Faris Noor', 'faris.noor@example.com', '$2y$10$CsXTkkqRoEYr.U6iWm4I5Oo8lN2XtJmlmLS8TwGupsO5Vq42QEHIq', NULL, 'barber', 0, 14, 1, '2026-05-10 07:02:46', NULL),
(75, 'Liam Brooks', 'liam.brooks@example.com', '$2y$10$CsXTkkqRoEYr.U6iWm4I5Oo8lN2XtJmlmLS8TwGupsO5Vq42QEHIq', NULL, 'barber', 1, 15, 1, '2026-05-10 07:02:46', NULL),
(76, 'Josh Miller', 'josh.miller@example.com', '$2y$10$CsXTkkqRoEYr.U6iWm4I5Oo8lN2XtJmlmLS8TwGupsO5Vq42QEHIq', NULL, 'barber', 0, 15, 1, '2026-05-10 07:02:46', NULL),
(77, 'Dylan Scott', 'dylan.scott@example.com', '$2y$10$CsXTkkqRoEYr.U6iWm4I5Oo8lN2XtJmlmLS8TwGupsO5Vq42QEHIq', NULL, 'barber', 0, 15, 1, '2026-05-10 07:02:46', NULL),
(78, 'Patrick Green', 'patrick.green@example.com', '$2y$10$CsXTkkqRoEYr.U6iWm4I5Oo8lN2XtJmlmLS8TwGupsO5Vq42QEHIq', NULL, 'barber', 0, 15, 1, '2026-05-10 07:02:46', NULL),
(79, 'Ryan Cooper', 'ryan.cooper@example.com', '$2y$10$CsXTkkqRoEYr.U6iWm4I5Oo8lN2XtJmlmLS8TwGupsO5Vq42QEHIq', NULL, 'barber', 0, 15, 1, '2026-05-10 07:02:46', NULL),
(80, 'Ben Walker', 'ben.walker@example.com', '$2y$10$CsXTkkqRoEYr.U6iWm4I5Oo8lN2XtJmlmLS8TwGupsO5Vq42QEHIq', NULL, 'barber', 1, 16, 1, '2026-05-10 07:02:46', NULL),
(81, 'Oscar Reed', 'oscar.reed@example.com', '$2y$10$CsXTkkqRoEYr.U6iWm4I5Oo8lN2XtJmlmLS8TwGupsO5Vq42QEHIq', NULL, 'barber', 0, 16, 1, '2026-05-10 07:02:46', NULL),
(82, 'Tyler James', 'tyler.james@example.com', '$2y$10$CsXTkkqRoEYr.U6iWm4I5Oo8lN2XtJmlmLS8TwGupsO5Vq42QEHIq', NULL, 'barber', 0, 16, 1, '2026-05-10 07:02:46', NULL),
(83, 'Mason Hill', 'mason.hill@example.com', '$2y$10$CsXTkkqRoEYr.U6iWm4I5Oo8lN2XtJmlmLS8TwGupsO5Vq42QEHIq', NULL, 'barber', 0, 16, 1, '2026-05-10 07:02:46', NULL),
(84, 'Jack Ryan', 'jack.ryan@example.com', '$2y$10$CsXTkkqRoEYr.U6iWm4I5Oo8lN2XtJmlmLS8TwGupsO5Vq42QEHIq', NULL, 'barber', 0, 16, 1, '2026-05-10 07:02:46', NULL),
(85, 'Alex Jordan', 'alex.jordan@example.com', '$2y$10$CsXTkkqRoEYr.U6iWm4I5Oo8lN2XtJmlmLS8TwGupsO5Vq42QEHIq', NULL, 'barber', 1, 17, 1, '2026-05-10 07:02:46', NULL),
(86, 'Daniel Cruz', 'daniel.cruz@example.com', '$2y$10$CsXTkkqRoEYr.U6iWm4I5Oo8lN2XtJmlmLS8TwGupsO5Vq42QEHIq', NULL, 'barber', 0, 17, 1, '2026-05-10 07:02:46', NULL),
(87, 'Steven Young', 'steven.young@example.com', '$2y$10$CsXTkkqRoEYr.U6iWm4I5Oo8lN2XtJmlmLS8TwGupsO5Vq42QEHIq', NULL, 'barber', 0, 17, 1, '2026-05-10 07:02:46', NULL),
(88, 'Marco Bell', 'marco.bell@example.com', '$2y$10$CsXTkkqRoEYr.U6iWm4I5Oo8lN2XtJmlmLS8TwGupsO5Vq42QEHIq', NULL, 'barber', 0, 17, 1, '2026-05-10 07:02:46', NULL),
(89, 'Chris White', 'chris.white@example.com', '$2y$10$CsXTkkqRoEYr.U6iWm4I5Oo8lN2XtJmlmLS8TwGupsO5Vq42QEHIq', NULL, 'barber', 0, 17, 1, '2026-05-10 07:02:46', NULL),
(90, 'Dean Foster', 'dean.foster@example.com', '$2y$10$CsXTkkqRoEYr.U6iWm4I5Oo8lN2XtJmlmLS8TwGupsO5Vq42QEHIq', NULL, 'barber', 1, 18, 1, '2026-05-10 07:02:46', NULL),
(91, 'Elijah Ross', 'elijah.ross@example.com', '$2y$10$CsXTkkqRoEYr.U6iWm4I5Oo8lN2XtJmlmLS8TwGupsO5Vq42QEHIq', NULL, 'barber', 0, 18, 1, '2026-05-10 07:02:46', NULL),
(92, 'Nathan Cole', 'nathan.cole@example.com', '$2y$10$CsXTkkqRoEYr.U6iWm4I5Oo8lN2XtJmlmLS8TwGupsO5Vq42QEHIq', NULL, 'barber', 0, 18, 1, '2026-05-10 07:02:46', NULL),
(93, 'Jordan Blake', 'jordan.blake@example.com', '$2y$10$CsXTkkqRoEYr.U6iWm4I5Oo8lN2XtJmlmLS8TwGupsO5Vq42QEHIq', NULL, 'barber', 0, 18, 1, '2026-05-10 07:02:46', NULL),
(94, 'Cole Stevens', 'cole.stevens@example.com', '$2y$10$CsXTkkqRoEYr.U6iWm4I5Oo8lN2XtJmlmLS8TwGupsO5Vq42QEHIq', NULL, 'barber', 0, 18, 1, '2026-05-10 07:02:46', NULL),
(95, 'Ahmed Karim', 'ahmed.karim@example.com', '$2y$10$CsXTkkqRoEYr.U6iWm4I5Oo8lN2XtJmlmLS8TwGupsO5Vq42QEHIq', NULL, 'barber', 1, 19, 1, '2026-05-10 07:02:46', NULL),
(96, 'Yusuf Ali', 'yusuf.ali@example.com', '$2y$10$CsXTkkqRoEYr.U6iWm4I5Oo8lN2XtJmlmLS8TwGupsO5Vq42QEHIq', NULL, 'barber', 0, 19, 1, '2026-05-10 07:02:46', NULL),
(97, 'Samir Khan', 'samir.khan@example.com', '$2y$10$CsXTkkqRoEYr.U6iWm4I5Oo8lN2XtJmlmLS8TwGupsO5Vq42QEHIq', NULL, 'barber', 0, 19, 1, '2026-05-10 07:02:46', NULL),
(98, 'Taha Malik', 'taha.malik@example.com', '$2y$10$CsXTkkqRoEYr.U6iWm4I5Oo8lN2XtJmlmLS8TwGupsO5Vq42QEHIq', NULL, 'barber', 0, 19, 1, '2026-05-10 07:02:46', NULL),
(99, 'Omar Hassan', 'omar.hassan@example.com', '$2y$10$CsXTkkqRoEYr.U6iWm4I5Oo8lN2XtJmlmLS8TwGupsO5Vq42QEHIq', NULL, 'barber', 0, 19, 1, '2026-05-10 07:02:46', NULL),
(100, 'Rico Martinez', 'rico.martinez@example.com', '$2y$10$CsXTkkqRoEYr.U6iWm4I5Oo8lN2XtJmlmLS8TwGupsO5Vq42QEHIq', NULL, 'barber', 1, 20, 1, '2026-05-10 07:02:46', NULL),
(101, 'Miguel Santos', 'miguel.santos@example.com', '$2y$10$CsXTkkqRoEYr.U6iWm4I5Oo8lN2XtJmlmLS8TwGupsO5Vq42QEHIq', NULL, 'barber', 0, 20, 1, '2026-05-10 07:02:46', NULL),
(102, 'Carlos Vega', 'carlos.vega@example.com', '$2y$10$CsXTkkqRoEYr.U6iWm4I5Oo8lN2XtJmlmLS8TwGupsO5Vq42QEHIq', NULL, 'barber', 0, 20, 1, '2026-05-10 07:02:46', NULL),
(103, 'Diego Ramirez', 'diego.ramirez@example.com', '$2y$10$CsXTkkqRoEYr.U6iWm4I5Oo8lN2XtJmlmLS8TwGupsO5Vq42QEHIq', NULL, 'barber', 0, 20, 1, '2026-05-10 07:02:46', NULL),
(104, 'Tony Cruz', 'tony.cruz@example.com', '$2y$10$CsXTkkqRoEYr.U6iWm4I5Oo8lN2XtJmlmLS8TwGupsO5Vq42QEHIq', NULL, 'barber', 0, 20, 1, '2026-05-10 07:02:46', NULL),
(105, 'Mehedi Hasan', 'mehedi@gmail.com', '$2y$10$vsZxMpPGU3eNyWYYB/wg.u9qnTCR.Eu7TMHlG3JAIEuysgtfJaNOu', '38498394', 'customer', 0, NULL, 1, '2026-05-15 23:57:38', NULL),
(106, 'Purna Gurung', 'purna@gmail.com', '$2y$10$So0clh75QmShQTZj/P5e1u94PwnQ39j0fqVTOWDr0WMEkUrPhES.S', '0423234494', 'customer', 0, NULL, 1, '2026-05-17 10:16:16', NULL),
(107, 'Baddass ', 'baddas@gmail.com', '$2y$10$dsEaimaPzfji7LNyLse/POFWd10f/kV27r8MqY.gC/u0K0uRbmSwi', '03034', 'barber', 1, 21, 1, '2026-05-24 10:00:31', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`appointmentid`),
  ADD KEY `customerid` (`customerid`),
  ADD KEY `barberid` (`barberid`),
  ADD KEY `serviceid` (`serviceid`);

--
-- Indexes for table `availability`
--
ALTER TABLE `availability`
  ADD PRIMARY KEY (`availabilityid`),
  ADD KEY `barberid` (`barberid`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notifid`),
  ADD KEY `customerid` (`customerid`),
  ADD KEY `barberid` (`barberid`),
  ADD KEY `appointmentid` (`appointmentid`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`serviceid`),
  ADD KEY `fk_service_shop` (`shopid`);

--
-- Indexes for table `shops`
--
ALTER TABLE `shops`
  ADD PRIMARY KEY (`shopid`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`userid`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_user_shop` (`shopid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `appointmentid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `availability`
--
ALTER TABLE `availability`
  MODIFY `availabilityid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notifid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `serviceid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=124;

--
-- AUTO_INCREMENT for table `shops`
--
ALTER TABLE `shops`
  MODIFY `shopid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `userid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=108;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`customerid`) REFERENCES `users` (`userid`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointments_ibfk_2` FOREIGN KEY (`barberid`) REFERENCES `users` (`userid`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointments_ibfk_3` FOREIGN KEY (`serviceid`) REFERENCES `services` (`serviceid`) ON DELETE CASCADE;

--
-- Constraints for table `availability`
--
ALTER TABLE `availability`
  ADD CONSTRAINT `availability_ibfk_1` FOREIGN KEY (`barberid`) REFERENCES `users` (`userid`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`customerid`) REFERENCES `users` (`userid`) ON DELETE CASCADE,
  ADD CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`barberid`) REFERENCES `users` (`userid`) ON DELETE CASCADE,
  ADD CONSTRAINT `notifications_ibfk_3` FOREIGN KEY (`appointmentid`) REFERENCES `appointments` (`appointmentid`) ON DELETE CASCADE;

--
-- Constraints for table `services`
--
ALTER TABLE `services`
  ADD CONSTRAINT `fk_service_shop` FOREIGN KEY (`shopid`) REFERENCES `shops` (`shopid`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_user_shop` FOREIGN KEY (`shopid`) REFERENCES `shops` (`shopid`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
