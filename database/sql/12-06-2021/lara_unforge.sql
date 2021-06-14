-- phpMyAdmin SQL Dump
-- version 4.9.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 14, 2021 at 08:31 AM
-- Server version: 10.4.10-MariaDB
-- PHP Version: 7.3.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `lara_unforge`
--

-- --------------------------------------------------------

--
-- Table structure for table `airlines`
--

CREATE TABLE `airlines` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `airlines`
--

INSERT INTO `airlines` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'PIA', '2020-02-27 08:51:41', '2021-06-07 02:08:04');

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `reference_name` varchar(255) DEFAULT NULL,
  `ref_no` varchar(255) DEFAULT NULL,
  `qoute_id` int(10) DEFAULT NULL,
  `quotation_no` varchar(255) DEFAULT NULL,
  `dinning_preferences` varchar(255) NOT NULL,
  `lead_passenger_name` varchar(255) NOT NULL,
  `brand_name` varchar(255) DEFAULT NULL,
  `type_of_holidays` varchar(255) DEFAULT NULL,
  `sale_person` varchar(255) DEFAULT NULL,
  `season_id` int(10) DEFAULT NULL,
  `agency_booking` tinyint(1) DEFAULT NULL,
  `agency_name` varchar(255) DEFAULT NULL,
  `agency_contact_no` int(11) DEFAULT NULL,
  `currency` varchar(255) DEFAULT NULL,
  `convert_currency` varchar(255) DEFAULT NULL,
  `group_no` int(10) DEFAULT NULL,
  `net_price` float(16,2) DEFAULT NULL,
  `markup_amount` float(16,2) DEFAULT NULL,
  `selling` float(16,2) DEFAULT NULL,
  `gross_profit` float(16,2) DEFAULT NULL,
  `markup_percent` int(10) DEFAULT NULL,
  `show_convert_currency` float(16,2) DEFAULT NULL,
  `per_person` float(16,2) DEFAULT NULL,
  `port_tax` float(16,2) DEFAULT NULL,
  `total_per_person` float(16,2) DEFAULT NULL,
  `is_email_send` tinyint(1) DEFAULT 0,
  `qoute_to_booking_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `flight_booked` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `reference_name`, `ref_no`, `qoute_id`, `quotation_no`, `dinning_preferences`, `lead_passenger_name`, `brand_name`, `type_of_holidays`, `sale_person`, `season_id`, `agency_booking`, `agency_name`, `agency_contact_no`, `currency`, `convert_currency`, `group_no`, `net_price`, `markup_amount`, `selling`, `gross_profit`, `markup_percent`, `show_convert_currency`, `per_person`, `port_tax`, `total_per_person`, `is_email_send`, `qoute_to_booking_date`, `created_at`, `updated_at`, `flight_booked`) VALUES
(1, 'zoho', 'uc20181999', 1, '60c1bca366053', 'Dinning Preferences', 'TEst passenger name', 'Unforgettable Croatia', 'UCroatia: Activity Holiday', 'muntahaismat@gmail.com', 1, 1, NULL, NULL, 'AUD', NULL, 2, 0.00, 1.00, 1.00, 100.00, 0, 0.00, 0.00, NULL, NULL, 0, '2021-06-10', '2021-06-10 07:25:59', '2021-06-12 05:29:54', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `booking_details`
--

CREATE TABLE `booking_details` (
  `id` int(10) NOT NULL,
  `qoute_id` int(10) DEFAULT NULL,
  `booking_id` int(10) DEFAULT NULL,
  `quotation_no` varchar(255) DEFAULT NULL,
  `row` int(10) DEFAULT NULL,
  `date_of_service` date DEFAULT NULL,
  `service_details` varchar(255) DEFAULT NULL,
  `category_id` int(10) DEFAULT NULL,
  `supplier` int(10) DEFAULT NULL,
  `booking_date` date DEFAULT NULL,
  `booking_due_date` date DEFAULT NULL,
  `booking_method` int(10) DEFAULT NULL,
  `booked_by` int(10) DEFAULT NULL,
  `booking_refrence` varchar(255) DEFAULT NULL,
  `booking_type` varchar(255) DEFAULT NULL,
  `comments` varchar(255) DEFAULT NULL,
  `supplier_currency` varchar(255) DEFAULT NULL,
  `cost` float(16,2) DEFAULT NULL,
  `actual_cost` float(16,2) DEFAULT NULL,
  `supervisor_id` int(10) DEFAULT NULL,
  `added_in_sage` tinyint(4) DEFAULT 0,
  `qoute_base_currency` float(16,2) DEFAULT NULL,
  `qoute_invoice` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `booking_details`
--

INSERT INTO `booking_details` (`id`, `qoute_id`, `booking_id`, `quotation_no`, `row`, `date_of_service`, `service_details`, `category_id`, `supplier`, `booking_date`, `booking_due_date`, `booking_method`, `booked_by`, `booking_refrence`, `booking_type`, `comments`, `supplier_currency`, `cost`, `actual_cost`, `supervisor_id`, `added_in_sage`, `qoute_base_currency`, `qoute_invoice`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '60c1bca366053', 1, NULL, NULL, NULL, NULL, NULL, '2021-07-06', NULL, 1, NULL, NULL, NULL, 'USD', 3.00, 0.00, 9, 1, 0.00, 'Book-7385.png', '2021-06-10 07:25:59', '2021-06-12 05:26:02'),
(2, 1, 1, '60c1bca366053', 2, NULL, NULL, NULL, NULL, NULL, '2021-07-06', NULL, 1, NULL, NULL, NULL, 'AUD', 3.00, 0.00, NULL, 1, 0.00, 'test-5465.jpg', '2021-06-10 07:25:59', '2021-06-12 05:26:02');

-- --------------------------------------------------------

--
-- Table structure for table `booking_detail_logs`
--

CREATE TABLE `booking_detail_logs` (
  `id` int(10) NOT NULL,
  `booking_id` int(10) DEFAULT NULL,
  `log_no` int(10) NOT NULL DEFAULT 0,
  `qoute_id` int(10) DEFAULT NULL,
  `quotation_no` varchar(255) DEFAULT NULL,
  `row` int(10) DEFAULT NULL,
  `date_of_service` date DEFAULT NULL,
  `service_details` varchar(255) DEFAULT NULL,
  `category_id` int(10) DEFAULT NULL,
  `supplier` int(10) DEFAULT NULL,
  `booking_date` date DEFAULT NULL,
  `booking_due_date` date DEFAULT NULL,
  `booking_method` int(10) DEFAULT NULL,
  `booked_by` int(10) DEFAULT NULL,
  `booking_refrence` varchar(255) DEFAULT NULL,
  `booking_type` varchar(255) DEFAULT NULL,
  `comments` varchar(255) DEFAULT NULL,
  `supplier_currency` varchar(255) DEFAULT NULL,
  `cost` float(16,2) DEFAULT NULL,
  `actual_cost` float(16,2) DEFAULT NULL,
  `supervisor_id` int(10) DEFAULT NULL,
  `added_in_sage` tinyint(4) DEFAULT 0,
  `qoute_base_currency` float(16,2) DEFAULT NULL,
  `qoute_invoice` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `booking_detail_logs`
--

INSERT INTO `booking_detail_logs` (`id`, `booking_id`, `log_no`, `qoute_id`, `quotation_no`, `row`, `date_of_service`, `service_details`, `category_id`, `supplier`, `booking_date`, `booking_due_date`, `booking_method`, `booked_by`, `booking_refrence`, `booking_type`, `comments`, `supplier_currency`, `cost`, `actual_cost`, `supervisor_id`, `added_in_sage`, `qoute_base_currency`, `qoute_invoice`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, '60c1bca366053', 1, NULL, NULL, NULL, NULL, NULL, '2021-07-06', NULL, 1, NULL, NULL, NULL, 'USD', NULL, NULL, NULL, NULL, NULL, 'Book-7385.png', '2021-06-11 09:25:12', '2021-06-11 09:25:12'),
(2, 1, 1, 1, '60c1bca366053', 2, NULL, NULL, NULL, NULL, NULL, '2021-07-06', NULL, 1, NULL, NULL, NULL, 'AUD', NULL, NULL, NULL, NULL, NULL, 'test-5465.jpg', '2021-06-11 09:25:12', '2021-06-11 09:25:12'),
(3, 1, 2, 1, '60c1bca366053', 1, NULL, NULL, NULL, NULL, NULL, '2021-07-06', NULL, 1, NULL, NULL, NULL, 'USD', NULL, NULL, NULL, NULL, NULL, 'Book-7385.png', '2021-06-11 10:20:26', '2021-06-11 10:20:26'),
(4, 1, 2, 1, '60c1bca366053', 2, NULL, NULL, NULL, NULL, NULL, '2021-07-06', NULL, 1, NULL, NULL, NULL, 'AUD', NULL, NULL, NULL, NULL, NULL, 'test-5465.jpg', '2021-06-11 10:20:26', '2021-06-11 10:20:26'),
(5, 1, 3, 1, '60c1bca366053', 1, NULL, NULL, NULL, NULL, NULL, '2021-07-06', NULL, 1, NULL, NULL, NULL, 'USD', NULL, NULL, NULL, NULL, NULL, 'Book-7385.png', '2021-06-11 10:54:04', '2021-06-11 10:54:04'),
(6, 1, 3, 1, '60c1bca366053', 2, NULL, NULL, NULL, NULL, NULL, '2021-07-06', NULL, 1, NULL, NULL, NULL, 'AUD', NULL, NULL, NULL, NULL, NULL, 'test-5465.jpg', '2021-06-11 10:54:05', '2021-06-11 10:54:05'),
(7, 1, 4, 1, '60c1bca366053', 1, NULL, NULL, NULL, NULL, NULL, '2021-07-06', NULL, 1, NULL, NULL, NULL, 'USD', NULL, NULL, NULL, NULL, NULL, 'Book-7385.png', '2021-06-11 10:54:15', '2021-06-11 10:54:15'),
(8, 1, 4, 1, '60c1bca366053', 2, NULL, NULL, NULL, NULL, NULL, '2021-07-06', NULL, 1, NULL, NULL, NULL, 'AUD', NULL, NULL, NULL, NULL, NULL, 'test-5465.jpg', '2021-06-11 10:54:15', '2021-06-11 10:54:15'),
(9, 1, 5, 1, '60c1bca366053', 1, NULL, NULL, NULL, NULL, NULL, '2021-07-06', NULL, 1, NULL, NULL, NULL, 'USD', NULL, NULL, NULL, NULL, NULL, 'Book-7385.png', '2021-06-12 00:52:29', '2021-06-12 00:52:29'),
(10, 1, 5, 1, '60c1bca366053', 2, NULL, NULL, NULL, NULL, NULL, '2021-07-06', NULL, 1, NULL, NULL, NULL, 'AUD', NULL, NULL, NULL, NULL, NULL, 'test-5465.jpg', '2021-06-12 00:52:30', '2021-06-12 00:52:30'),
(11, 1, 6, 1, '60c1bca366053', 1, NULL, NULL, NULL, NULL, NULL, '2021-07-06', NULL, 1, NULL, NULL, NULL, 'USD', NULL, NULL, NULL, NULL, NULL, 'Book-7385.png', '2021-06-12 00:53:15', '2021-06-12 00:53:15'),
(12, 1, 6, 1, '60c1bca366053', 2, NULL, NULL, NULL, NULL, NULL, '2021-07-06', NULL, 1, NULL, NULL, NULL, 'AUD', NULL, NULL, NULL, NULL, NULL, 'test-5465.jpg', '2021-06-12 00:53:15', '2021-06-12 00:53:15'),
(13, 1, 7, 1, '60c1bca366053', 1, NULL, NULL, NULL, NULL, NULL, '2021-07-06', NULL, 1, NULL, NULL, NULL, 'USD', NULL, NULL, NULL, NULL, NULL, 'Book-7385.png', '2021-06-12 05:20:54', '2021-06-12 05:20:54'),
(14, 1, 7, 1, '60c1bca366053', 2, NULL, NULL, NULL, NULL, NULL, '2021-07-06', NULL, 1, NULL, NULL, NULL, 'AUD', NULL, NULL, NULL, NULL, NULL, 'test-5465.jpg', '2021-06-12 05:20:54', '2021-06-12 05:20:54'),
(15, 1, 8, 1, '60c1bca366053', 1, NULL, NULL, NULL, NULL, NULL, '2021-07-06', NULL, 1, NULL, NULL, NULL, 'USD', NULL, NULL, NULL, NULL, NULL, 'Book-7385.png', '2021-06-12 05:23:33', '2021-06-12 05:23:33'),
(16, 1, 8, 1, '60c1bca366053', 2, NULL, NULL, NULL, NULL, NULL, '2021-07-06', NULL, 1, NULL, NULL, NULL, 'AUD', NULL, NULL, NULL, NULL, NULL, 'test-5465.jpg', '2021-06-12 05:23:33', '2021-06-12 05:23:33'),
(17, 1, 9, 1, '60c1bca366053', 1, NULL, NULL, NULL, NULL, NULL, '2021-07-06', NULL, 1, NULL, NULL, NULL, 'USD', NULL, NULL, NULL, 0, 0.00, 'Book-7385.png', '2021-06-12 05:24:39', '2021-06-12 05:24:39'),
(18, 1, 9, 1, '60c1bca366053', 2, NULL, NULL, NULL, NULL, NULL, '2021-07-06', NULL, 1, NULL, NULL, NULL, 'AUD', NULL, NULL, NULL, 0, 0.00, 'test-5465.jpg', '2021-06-12 05:24:39', '2021-06-12 05:24:39'),
(19, 1, 10, 1, '60c1bca366053', 1, NULL, NULL, NULL, NULL, NULL, '2021-07-06', NULL, 1, NULL, NULL, NULL, 'USD', NULL, NULL, NULL, 1, 0.00, 'Book-7385.png', '2021-06-12 05:24:52', '2021-06-12 05:24:52'),
(20, 1, 10, 1, '60c1bca366053', 2, NULL, NULL, NULL, NULL, NULL, '2021-07-06', NULL, 1, NULL, NULL, NULL, 'AUD', NULL, NULL, NULL, 0, 0.00, 'test-5465.jpg', '2021-06-12 05:24:52', '2021-06-12 05:24:52'),
(21, 1, 11, 1, '60c1bca366053', 1, NULL, NULL, NULL, NULL, NULL, '2021-07-06', NULL, 1, NULL, NULL, NULL, 'USD', 3.00, 0.00, NULL, 1, 0.00, 'Book-7385.png', '2021-06-12 05:26:02', '2021-06-12 05:26:02'),
(22, 1, 11, 1, '60c1bca366053', 2, NULL, NULL, NULL, NULL, NULL, '2021-07-06', NULL, 1, NULL, NULL, NULL, 'AUD', 3.00, 0.00, NULL, 1, 0.00, 'test-5465.jpg', '2021-06-12 05:26:02', '2021-06-12 05:26:02');

-- --------------------------------------------------------

--
-- Table structure for table `booking_emails`
--

CREATE TABLE `booking_emails` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `booking_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `username` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hour` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_read` int(10) UNSIGNED DEFAULT NULL,
  `is_read_date` date DEFAULT NULL,
  `action` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `booking_emails`
--

INSERT INTO `booking_emails` (`id`, `booking_id`, `user_id`, `username`, `hour`, `is_read`, `is_read_date`, `action`, `created_at`, `updated_at`) VALUES
(1, 1, 9, 'User', '24', NULL, NULL, 'flight_booked', '2021-03-29 09:20:21', '2021-03-29 09:20:21');

-- --------------------------------------------------------

--
-- Table structure for table `booking_logs`
--

CREATE TABLE `booking_logs` (
  `id` int(11) NOT NULL,
  `booking_id` int(10) DEFAULT NULL,
  `log_no` int(11) DEFAULT 0,
  `reference_name` varchar(255) DEFAULT NULL,
  `ref_no` varchar(255) DEFAULT NULL,
  `qoute_id` int(10) DEFAULT NULL,
  `quotation_no` varchar(255) DEFAULT NULL,
  `dinning_preferences` varchar(255) NOT NULL,
  `lead_passenger_name` varchar(255) NOT NULL,
  `brand_name` varchar(255) DEFAULT NULL,
  `type_of_holidays` varchar(255) DEFAULT NULL,
  `sale_person` varchar(255) DEFAULT NULL,
  `season_id` int(10) DEFAULT NULL,
  `agency_booking` tinyint(1) DEFAULT NULL,
  `agency_name` varchar(255) DEFAULT NULL,
  `agency_contact_no` int(11) DEFAULT NULL,
  `currency` varchar(255) DEFAULT NULL,
  `convert_currency` varchar(255) DEFAULT NULL,
  `group_no` int(10) DEFAULT NULL,
  `net_price` float(16,2) DEFAULT NULL,
  `markup_amount` float(16,2) DEFAULT NULL,
  `selling` float(16,2) DEFAULT NULL,
  `gross_profit` float(16,2) DEFAULT NULL,
  `markup_percent` int(10) DEFAULT NULL,
  `show_convert_currency` float(16,2) DEFAULT NULL,
  `per_person` float(16,2) DEFAULT NULL,
  `port_tax` float(16,2) DEFAULT NULL,
  `total_per_person` float(16,2) DEFAULT NULL,
  `is_email_send` tinyint(1) DEFAULT 0,
  `created_date` date DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `booking_logs`
--

INSERT INTO `booking_logs` (`id`, `booking_id`, `log_no`, `reference_name`, `ref_no`, `qoute_id`, `quotation_no`, `dinning_preferences`, `lead_passenger_name`, `brand_name`, `type_of_holidays`, `sale_person`, `season_id`, `agency_booking`, `agency_name`, `agency_contact_no`, `currency`, `convert_currency`, `group_no`, `net_price`, `markup_amount`, `selling`, `gross_profit`, `markup_percent`, `show_convert_currency`, `per_person`, `port_tax`, `total_per_person`, `is_email_send`, `created_date`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, NULL, 'uc20181999', 1, '60c1bca366053', 'Dinning Preferences', 'TEst passenger name', 'Unforgettable Croatia', 'UCroatia: Activity Holiday', 'muntahaismat@gmail.com', 1, 1, NULL, NULL, 'AUD', NULL, 2, 0.00, 1.00, 1.00, 100.00, 0, 0.00, 0.00, NULL, NULL, 0, '2021-06-11', 1, '2021-06-11 09:25:12', '2021-06-11 09:25:12'),
(2, 1, 2, NULL, 'uc20181999', 1, '60c1bca366053', 'Dinning Preferences', 'TEst passenger name', 'Unforgettable Croatia', 'UCroatia: Activity Holiday', 'muntahaismat@gmail.com', 1, 1, NULL, NULL, 'AUD', NULL, 2, 0.00, 1.00, 1.00, 100.00, 0, 0.00, 0.00, NULL, NULL, 0, '2021-06-11', 1, '2021-06-11 10:20:25', '2021-06-11 10:20:25'),
(3, 1, 3, NULL, 'uc20181999', 1, '60c1bca366053', 'Dinning Preferences', 'TEst passenger name', 'Unforgettable Croatia', 'UCroatia: Activity Holiday', 'muntahaismat@gmail.com', 1, 1, NULL, NULL, 'AUD', NULL, 2, 0.00, 1.00, 1.00, 100.00, 0, 0.00, 0.00, NULL, NULL, 0, '2021-06-11', 1, '2021-06-11 10:54:04', '2021-06-11 10:54:04'),
(4, 1, 4, NULL, 'uc20181999', 1, '60c1bca366053', 'Dinning Preferences', 'TEst passenger name', 'Unforgettable Croatia', 'UCroatia: Activity Holiday', 'muntahaismat@gmail.com', 1, 1, NULL, NULL, 'AUD', NULL, 2, 0.00, 1.00, 1.00, 100.00, 0, 0.00, 0.00, NULL, NULL, 0, '2021-06-11', 1, '2021-06-11 10:54:14', '2021-06-11 10:54:14'),
(5, 1, 5, NULL, 'uc20181999', 1, '60c1bca366053', 'Dinning Preferences', 'TEst passenger name', 'Unforgettable Croatia', 'UCroatia: Activity Holiday', 'muntahaismat@gmail.com', 1, 1, NULL, NULL, 'AUD', NULL, 2, 0.00, 1.00, 1.00, 100.00, 0, 0.00, 0.00, NULL, NULL, 0, '2021-06-12', 1, '2021-06-12 00:52:29', '2021-06-12 00:52:29'),
(6, 1, 6, NULL, 'uc20181999', 1, '60c1bca366053', 'Dinning Preferences', 'TEst passenger name', 'Unforgettable Croatia', 'UCroatia: Activity Holiday', 'muntahaismat@gmail.com', 1, 1, NULL, NULL, 'AUD', NULL, 2, 0.00, 1.00, 1.00, 100.00, 0, 0.00, 0.00, NULL, NULL, 0, '2021-06-12', 1, '2021-06-12 00:53:13', '2021-06-12 00:53:13'),
(7, 1, 7, NULL, 'uc20181999', 1, '60c1bca366053', 'Dinning Preferences', 'TEst passenger name', 'Unforgettable Croatia', 'UCroatia: Activity Holiday', 'muntahaismat@gmail.com', 1, 1, NULL, NULL, 'AUD', NULL, 2, 0.00, 1.00, 1.00, 100.00, 0, 0.00, 0.00, NULL, NULL, 0, '2021-06-12', 19, '2021-06-12 05:20:53', '2021-06-12 05:20:53'),
(8, 1, 8, NULL, 'uc20181999', 1, '60c1bca366053', 'Dinning Preferences', 'TEst passenger name', 'Unforgettable Croatia', 'UCroatia: Activity Holiday', 'muntahaismat@gmail.com', 1, 1, NULL, NULL, 'AUD', NULL, 2, 0.00, 1.00, 1.00, 100.00, 0, 0.00, 0.00, NULL, NULL, 0, '2021-06-12', 19, '2021-06-12 05:23:32', '2021-06-12 05:23:32'),
(9, 1, 9, NULL, 'uc20181999', 1, '60c1bca366053', 'Dinning Preferences', 'TEst passenger name', 'Unforgettable Croatia', 'UCroatia: Activity Holiday', 'muntahaismat@gmail.com', 1, 1, NULL, NULL, 'AUD', NULL, 2, 0.00, 1.00, 1.00, 100.00, 0, 0.00, 0.00, NULL, NULL, 0, '2021-06-12', 19, '2021-06-12 05:24:39', '2021-06-12 05:24:39'),
(10, 1, 10, NULL, 'uc20181999', 1, '60c1bca366053', 'Dinning Preferences', 'TEst passenger name', 'Unforgettable Croatia', 'UCroatia: Activity Holiday', 'muntahaismat@gmail.com', 1, 1, NULL, NULL, 'AUD', NULL, 2, 0.00, 1.00, 1.00, 100.00, 0, 0.00, 0.00, NULL, NULL, 0, '2021-06-12', 19, '2021-06-12 05:24:52', '2021-06-12 05:24:52'),
(11, 1, 11, NULL, 'uc20181999', 1, '60c1bca366053', 'Dinning Preferences', 'TEst passenger name', 'Unforgettable Croatia', 'UCroatia: Activity Holiday', 'muntahaismat@gmail.com', 1, 1, NULL, NULL, 'AUD', NULL, 2, 0.00, 1.00, 1.00, 100.00, 0, 0.00, 0.00, NULL, NULL, 0, '2021-06-12', 19, '2021-06-12 05:26:01', '2021-06-12 05:26:01'),
(12, 1, 12, NULL, 'uc20181999', 1, '60c1bca366053', 'Dinning Preferences', 'TEst passenger name', 'Unforgettable Croatia', 'UCroatia: Activity Holiday', 'muntahaismat@gmail.com', 1, 1, NULL, NULL, 'AUD', NULL, 2, 0.00, 1.00, 1.00, 100.00, 0, 0.00, 0.00, NULL, NULL, 0, '2021-06-12', 19, '2021-06-12 05:27:12', '2021-06-12 05:27:12'),
(13, 1, 13, NULL, 'uc20181999', 1, '60c1bca366053', 'Dinning Preferences', 'TEst passenger name', 'Unforgettable Croatia', 'UCroatia: Activity Holiday', 'muntahaismat@gmail.com', 1, 1, NULL, NULL, 'AUD', NULL, 2, 0.00, 1.00, 1.00, 100.00, 0, 0.00, 0.00, NULL, NULL, 0, '2021-06-12', 19, '2021-06-12 05:27:52', '2021-06-12 05:27:52'),
(14, 1, 14, NULL, 'uc20181999', 1, '60c1bca366053', 'Dinning Preferences', 'TEst passenger name', 'Unforgettable Croatia', 'UCroatia: Activity Holiday', 'muntahaismat@gmail.com', 1, 1, NULL, NULL, 'AUD', NULL, 2, 0.00, 1.00, 1.00, 100.00, 0, 0.00, 0.00, NULL, NULL, 0, '2021-06-12', 19, '2021-06-12 05:29:17', '2021-06-12 05:29:17'),
(15, 1, 15, NULL, 'uc20181999', 1, '60c1bca366053', 'Dinning Preferences', 'TEst passenger name', 'Unforgettable Croatia', 'UCroatia: Activity Holiday', 'muntahaismat@gmail.com', 1, 1, NULL, NULL, 'AUD', NULL, 2, 0.00, 1.00, 1.00, 100.00, 0, 0.00, 0.00, NULL, NULL, 0, '2021-06-12', 19, '2021-06-12 05:29:54', '2021-06-12 05:29:54');

-- --------------------------------------------------------

--
-- Table structure for table `booking_methods`
--

CREATE TABLE `booking_methods` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `booking_methods`
--

INSERT INTO `booking_methods` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Supplier Own', '2021-05-31 02:26:02', '2021-05-31 02:26:02'),
(2, 'Stuba', '2021-05-31 02:26:11', '2021-05-31 02:26:11'),
(3, 'Webhotelier', '2021-05-31 02:26:18', '2021-05-31 02:26:18'),
(4, 'adasdasdadsas', '2021-06-04 10:43:46', '2021-06-04 10:47:10'),
(5, 'asdasd', '2021-06-04 10:43:54', '2021-06-04 10:43:54');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `updated_at` date NOT NULL,
  `created_at` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `updated_at`, `created_at`) VALUES
(1, 'Transfer', '2021-04-01', '2021-02-26'),
(2, 'Accommodation', '2021-03-20', '2021-02-26'),
(3, 'Tour', '2021-03-20', '2021-03-02'),
(4, 'Cruise', '2021-03-20', '2021-03-20'),
(6, 'Taxes', '2021-04-13', '2021-04-13'),
(7, 'Flights', '2021-06-08', '2021-06-08');

-- --------------------------------------------------------

--
-- Table structure for table `codes`
--

CREATE TABLE `codes` (
  `id` int(10) UNSIGNED NOT NULL,
  `ref_no` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `brand_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `season_id` int(10) UNSIGNED NOT NULL,
  `agency_booking` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pax_no` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_of_travel` date NOT NULL,
  `category` int(11) NOT NULL,
  `product` int(11) NOT NULL,
  `supplier` int(11) NOT NULL,
  `flight_booked` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fb_airline_name_id` int(10) UNSIGNED DEFAULT NULL,
  `fb_payment_method_id` int(10) UNSIGNED DEFAULT NULL,
  `fb_booking_date` date DEFAULT NULL,
  `fb_airline_ref_no` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fb_last_date` date DEFAULT NULL,
  `fb_person` int(10) UNSIGNED DEFAULT NULL,
  `fb_48hr` int(10) UNSIGNED DEFAULT NULL,
  `fb_24hr` int(10) UNSIGNED DEFAULT NULL,
  `fb_0hr` int(10) UNSIGNED DEFAULT NULL,
  `flight_booking_details` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `asked_for_transfer_details` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `transfer_details` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `aft_last_date` date DEFAULT NULL,
  `aft_person` int(10) UNSIGNED DEFAULT NULL,
  `aft_48hr` int(10) UNSIGNED DEFAULT NULL,
  `aft_24hr` int(10) UNSIGNED DEFAULT NULL,
  `aft_0hr` int(10) UNSIGNED DEFAULT NULL,
  `form_sent_on` date NOT NULL,
  `form_received_on` date DEFAULT NULL,
  `fso_last_date` date DEFAULT NULL,
  `fso_person` int(10) UNSIGNED DEFAULT NULL,
  `fso_48hr` int(10) UNSIGNED DEFAULT NULL,
  `fso_24hr` int(10) UNSIGNED DEFAULT NULL,
  `fso_0hr` int(10) UNSIGNED DEFAULT NULL,
  `app_login_date` date DEFAULT NULL,
  `transfer_info_received` varchar(5) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transfer_info_details` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `itinerary_finalised` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `itinerary_finalised_details` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `itf_last_date` date DEFAULT NULL,
  `itf_current_date` date DEFAULT NULL,
  `itf_person` int(10) UNSIGNED DEFAULT NULL,
  `itf_48hr` int(10) UNSIGNED DEFAULT NULL,
  `itf_24hr` int(10) UNSIGNED DEFAULT NULL,
  `itf_0hr` int(10) UNSIGNED DEFAULT NULL,
  `documents_sent` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `documents_sent_details` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tds_current_date` date DEFAULT NULL,
  `ds_last_date` date DEFAULT NULL,
  `ds_person` int(10) UNSIGNED DEFAULT NULL,
  `ds_48hr` int(10) UNSIGNED DEFAULT NULL,
  `ds_24hr` int(10) UNSIGNED DEFAULT NULL,
  `ds_0hr` int(10) UNSIGNED DEFAULT NULL,
  `document_prepare` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dp_last_date` date DEFAULT NULL,
  `dp_person` int(10) UNSIGNED DEFAULT NULL,
  `dp_48hr` int(10) UNSIGNED DEFAULT NULL,
  `dp_24hr` int(10) UNSIGNED DEFAULT NULL,
  `dp_0hr` int(10) UNSIGNED DEFAULT NULL,
  `tdp_current_date` date DEFAULT NULL,
  `aps_last_date` date DEFAULT NULL,
  `aps_person` int(10) UNSIGNED DEFAULT NULL,
  `aps_48hr` int(10) UNSIGNED DEFAULT NULL,
  `aps_24hr` int(10) UNSIGNED DEFAULT NULL,
  `aps_0hr` int(10) UNSIGNED DEFAULT NULL,
  `electronic_copy_sent` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `electronic_copy_details` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transfer_organised` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `transfer_organised_details` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `to_last_date` date DEFAULT NULL,
  `to_person` int(10) UNSIGNED DEFAULT NULL,
  `to_48hr` int(10) UNSIGNED DEFAULT NULL,
  `to_24hr` int(10) UNSIGNED DEFAULT NULL,
  `to_0hr` int(10) UNSIGNED DEFAULT NULL,
  `type_of_holidays` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sale_person` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deposit_received` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `remaining_amount_received` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `finance_detail` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `destination` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `codes`
--

INSERT INTO `codes` (`id`, `ref_no`, `brand_name`, `season_id`, `agency_booking`, `pax_no`, `date_of_travel`, `category`, `product`, `supplier`, `flight_booked`, `fb_airline_name_id`, `fb_payment_method_id`, `fb_booking_date`, `fb_airline_ref_no`, `fb_last_date`, `fb_person`, `fb_48hr`, `fb_24hr`, `fb_0hr`, `flight_booking_details`, `asked_for_transfer_details`, `transfer_details`, `aft_last_date`, `aft_person`, `aft_48hr`, `aft_24hr`, `aft_0hr`, `form_sent_on`, `form_received_on`, `fso_last_date`, `fso_person`, `fso_48hr`, `fso_24hr`, `fso_0hr`, `app_login_date`, `transfer_info_received`, `transfer_info_details`, `itinerary_finalised`, `itinerary_finalised_details`, `itf_last_date`, `itf_current_date`, `itf_person`, `itf_48hr`, `itf_24hr`, `itf_0hr`, `documents_sent`, `documents_sent_details`, `tds_current_date`, `ds_last_date`, `ds_person`, `ds_48hr`, `ds_24hr`, `ds_0hr`, `document_prepare`, `dp_last_date`, `dp_person`, `dp_48hr`, `dp_24hr`, `dp_0hr`, `tdp_current_date`, `aps_last_date`, `aps_person`, `aps_48hr`, `aps_24hr`, `aps_0hr`, `electronic_copy_sent`, `electronic_copy_details`, `transfer_organised`, `transfer_organised_details`, `to_last_date`, `to_person`, `to_48hr`, `to_24hr`, `to_0hr`, `type_of_holidays`, `sale_person`, `deposit_received`, `remaining_amount_received`, `finance_detail`, `destination`, `notes`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 'UC20186758', 'Unforgettable Cruises', 1, '1', '5', '2020-12-06', 0, 0, 0, 'yes', 1, 3, '2020-03-07', '123', '2020-02-29', 3, NULL, NULL, NULL, 'Flight Booking Details', 'yes', 'Asked For Transfer Details', '2020-03-07', 9, NULL, NULL, NULL, '2020-02-17', '2020-02-18', '2020-02-28', 3, NULL, NULL, NULL, NULL, NULL, NULL, 'yes', 'Itinerary Finalised Details', '2020-03-07', '2020-02-28', 10, NULL, NULL, NULL, 'no', NULL, '2020-02-28', '2020-03-07', 3, NULL, NULL, NULL, 'no', '2020-03-07', 3, NULL, NULL, NULL, '2020-02-28', '2020-03-07', 10, NULL, NULL, NULL, 'no', NULL, 'yes', 'Transfer Organised Details', '2020-03-07', 9, NULL, NULL, NULL, 'UCroatia: Cruise', 'muno@unforgettablecruises.com', 0, 0, '<tr><td>-</td><td>8438225105</td><td>Elizabeth Carbonneau</td><td>Deposit</td><td>COMPLETED</td><td>USD</td><td>1800</td><td>2019-08-21 14:57:30</td><tr>', 'Karachi', NULL, 1, '2020-02-28 06:26:56', '2020-02-28 06:26:56'),
(2, 'UC20188297', 'Cruise Croatia', 1, '2', '5', '2021-01-09', 0, 0, 0, 'yes', 1, 3, '2020-03-01', '123', '2020-03-01', NULL, NULL, NULL, NULL, 'Flight Booking Details', 'yes', 'Asked For Transfer Details', '2020-03-07', NULL, NULL, NULL, NULL, '2020-02-26', NULL, '2020-03-01', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'yes', 'Itinerary Finalised Details', '2020-03-07', '2020-02-28', 3, NULL, NULL, NULL, 'no', NULL, '2020-02-28', '2020-03-07', 3, NULL, NULL, NULL, 'no', '2020-03-07', 3, NULL, NULL, NULL, '2020-02-28', '2020-03-07', NULL, NULL, NULL, NULL, 'no', NULL, 'no', NULL, '2020-03-07', NULL, NULL, NULL, NULL, 'UCroatia: Escorted Tour', 'ahsan@kingdom-vision.co.uk', 0, 0, '<tr><td>-</td><td>8438225105</td><td>Elizabeth Carbonneau</td><td>Deposit</td><td>COMPLETED</td><td>USD</td><td>1800</td><td>2019-08-21 14:57:30</td><tr>', 'Karachi', NULL, 1, '2020-02-28 08:41:13', '2020-02-28 08:41:13'),
(3, 'UC20188297', 'Cruise Croatia', 1, '2', '4', '2020-02-29', 0, 0, 0, 'yes', 1, 4, '2020-02-29', '1123', '2020-02-29', NULL, NULL, NULL, NULL, 'Destination', 'yes', 'DestinationDestination', '2020-02-29', 3, NULL, NULL, NULL, '2020-02-26', NULL, '2020-02-29', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'no', NULL, '2020-02-29', '2020-02-28', 9, NULL, NULL, NULL, 'yes', 'DestinationDestinationDestination', '2020-02-28', '2020-02-29', 3, NULL, NULL, NULL, 'no', '2020-02-29', 9, NULL, NULL, NULL, '2020-02-28', '2020-02-29', NULL, NULL, NULL, NULL, 'no', 'DestinationDestinationDestination', 'no', NULL, '2020-02-29', 9, NULL, NULL, NULL, 'UCroatia: Cruise & Stay', 'syedarsalankv@gmail.com', 1, 1, '<tr><td>-</td><td>412 996 7090</td><td>lisa kustra</td><td>Deposit</td><td>COMPLETED</td><td>USD</td><td>850</td><td>2019-01-28 15:51:03</td><tr>', 'Destination', NULL, 1, '2020-02-28 15:32:26', '2020-02-28 15:32:26'),
(4, 'UC20186758', 'Unforgettable Cruises', 2, '1', '1', '2020-03-28', 0, 0, 0, 'no', NULL, NULL, '2020-03-07', NULL, '2020-03-28', NULL, NULL, NULL, NULL, NULL, 'yes', 'DestinationDestination', '2020-03-28', 3, NULL, NULL, NULL, '2020-02-17', '2020-02-18', '2020-03-07', 3, NULL, NULL, NULL, NULL, NULL, NULL, 'no', NULL, '2020-03-28', '2020-03-07', 3, NULL, NULL, NULL, 'yes', 'Destination', '2020-03-07', '2020-03-28', 3, NULL, NULL, NULL, 'no', '2020-03-27', 3, NULL, NULL, NULL, '2020-03-07', '2020-03-28', 3, NULL, NULL, NULL, 'no', NULL, 'no', NULL, '2020-03-28', 3, NULL, NULL, NULL, 'UCroatia: Escorted Tour', 'muno@unforgettablecruises.com', 1, 0, '<tr><td>-</td><td>8438225105</td><td>Elizabeth Carbonneau</td><td>Deposit</td><td>COMPLETED</td><td>USD</td><td>1800</td><td>2019-08-21 14:57:30</td><tr>', 'DestinationDestination', NULL, 1, '2020-03-07 11:18:29', '2020-03-07 11:18:29'),
(5, 'UC20188297', 'Unforgettable Croatia', 2, '1', '2', '2020-03-28', 0, 0, 0, 'yes', 1, 4, '2020-03-28', '123', '2020-03-28', NULL, NULL, NULL, NULL, 'DestinationDestination', 'yes', 'DestinationDestination', '2020-03-28', 3, NULL, NULL, NULL, '2020-02-26', NULL, '2020-03-28', 3, NULL, NULL, NULL, NULL, NULL, NULL, 'no', NULL, '2020-03-21', '2020-03-07', 3, NULL, NULL, NULL, 'no', NULL, '2020-03-07', '2020-03-28', 3, NULL, NULL, NULL, 'no', '2020-03-28', 3, NULL, NULL, NULL, '2020-03-07', '2020-03-28', 3, NULL, NULL, NULL, 'no', NULL, 'no', NULL, '2020-03-28', 3, NULL, NULL, NULL, 'UCroatia: Escorted Tour', 'muntahaismat@gmail.com', 0, 0, NULL, 'DestinationDestination', NULL, 1, '2020-03-07 11:21:57', '2020-03-07 11:21:57'),
(6, 'UC20188297', 'Cruise Croatia', 1, '1', '1', '2020-03-28', 0, 0, 0, 'yes', 1, 3, '2020-03-21', '123', '2020-03-28', NULL, NULL, NULL, NULL, 'DestinationDestination', 'yes', 'Destination\r\nDestinatio', '2020-03-28', NULL, NULL, NULL, NULL, '2020-02-26', NULL, '2020-04-04', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'no', NULL, '2020-03-27', '2020-03-07', 9, NULL, NULL, NULL, 'yes', 'Destination\r\nDestination', '2020-03-07', '2020-03-27', NULL, NULL, NULL, NULL, 'no', '2020-03-28', 10, NULL, NULL, NULL, '2020-03-07', '2020-03-07', NULL, NULL, NULL, NULL, 'no', NULL, 'no', NULL, '2020-03-28', 3, NULL, NULL, NULL, 'UCroatia: Escorted Tour', 'ahsan@kingdom-vision.co.uk', 0, 0, NULL, 'DestinationDestination', NULL, 1, '2020-03-07 11:29:41', '2020-03-07 11:29:41'),
(7, 'UC20188297', 'Unforgettable Croatia', 2, '2', '2', '2020-03-28', 0, 0, 0, 'yes', 1, 3, '2020-03-19', '132', '2020-03-28', NULL, NULL, NULL, NULL, 'Flight Booked\r\nFlight Booked', 'yes', 'Flight Booked', '2020-03-28', 3, NULL, NULL, NULL, '2020-02-26', NULL, '2020-03-28', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'no', NULL, '2020-03-28', '2020-03-07', 3, NULL, NULL, NULL, 'yes', 'Flight Booked', '2020-03-07', '2020-03-19', 3, NULL, NULL, NULL, 'no', '2020-03-28', 3, NULL, NULL, NULL, '2020-03-07', '2020-03-28', 3, NULL, NULL, NULL, 'no', NULL, 'no', NULL, '2020-03-28', 3, NULL, NULL, NULL, 'UCroatia: Tailor Made', 'syedarsalankv@gmail.com', 0, 0, '<tr><td>-</td><td>203-524-4797</td><td>Haley Laughlin</td><td>Deposit</td><td>COMPLETED</td><td>USD</td><td>1000</td><td>2019-08-16 15:08:22</td><tr>', 'Flight Booked', NULL, 1, '2020-03-07 11:31:33', '2020-03-07 11:31:33');

-- --------------------------------------------------------

--
-- Table structure for table `currencies`
--

CREATE TABLE `currencies` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `code` varchar(255) DEFAULT NULL,
  `symbol` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `currencies`
--

INSERT INTO `currencies` (`id`, `name`, `code`, `symbol`, `created_at`, `updated_at`) VALUES
(1, 'United Kingdom Pound', 'GBP', '£', '2021-03-19 15:21:59', '2021-03-19 15:21:59'),
(2, 'Euro', 'EUR', '€', '2021-03-19 15:21:59', '2021-03-19 15:21:59'),
(3, 'Australian dollar\r\n', 'AUD', 'A$', '2021-03-19 15:21:59', '2021-03-19 15:21:59'),
(4, 'US Dollar', 'USD', '$', '2021-03-19 15:21:59', '2021-03-19 15:21:59');

-- --------------------------------------------------------

--
-- Table structure for table `currency_conversion`
--

CREATE TABLE `currency_conversion` (
  `id` int(11) NOT NULL,
  `from` varchar(255) DEFAULT NULL,
  `to` varchar(255) DEFAULT NULL,
  `value` float(16,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `currency_conversion`
--

INSERT INTO `currency_conversion` (`id`, `from`, `to`, `value`, `created_at`, `updated_at`) VALUES
(1, 'GBP', 'USD', 1.38, NULL, '2021-04-12 08:11:33'),
(2, 'GBP', 'EUR', 1.16, NULL, '2021-04-12 08:11:34'),
(3, 'GBP', 'AUD', 1.80, NULL, '2021-04-12 08:11:35'),
(4, 'GBP', 'GBP', 1.00, NULL, '2021-04-12 08:11:37'),
(5, 'USD', 'USD', 1.00, NULL, '2021-04-12 08:11:38'),
(6, 'USD', 'EUR', 0.84, NULL, '2021-04-12 08:11:38'),
(7, 'USD', 'AUD', 1.31, NULL, '2021-04-12 08:11:39'),
(8, 'USD', 'GBP', 0.73, NULL, '2021-04-12 08:11:40'),
(9, 'EUR', 'USD', 1.19, NULL, '2021-04-12 08:11:41'),
(10, 'EUR', 'EUR', 1.00, NULL, '2021-04-12 08:11:42'),
(11, 'EUR', 'AUD', 1.56, NULL, '2021-04-12 08:11:43'),
(12, 'EUR', 'GBP', 0.87, NULL, '2021-04-12 08:11:43'),
(13, 'AUD', 'USD', 0.76, NULL, '2021-04-12 08:11:44'),
(14, 'AUD', 'EUR', 0.64, NULL, '2021-04-12 08:11:45'),
(15, 'AUD', 'AUD', 1.00, NULL, '2021-04-12 08:11:46'),
(16, 'AUD', 'GBP', 0.55, NULL, '2021-04-12 08:11:47');

-- --------------------------------------------------------

--
-- Table structure for table `finance_booking_details`
--

CREATE TABLE `finance_booking_details` (
  `id` int(10) NOT NULL,
  `booking_detail_id` int(10) DEFAULT NULL,
  `row` int(10) DEFAULT NULL,
  `deposit_amount` float(16,2) DEFAULT NULL,
  `deposit_due_date` date DEFAULT NULL,
  `paid_date` date DEFAULT NULL,
  `payment_method` varchar(255) DEFAULT NULL,
  `upload_to_calender` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `finance_booking_details`
--

INSERT INTO `finance_booking_details` (`id`, `booking_detail_id`, `row`, `deposit_amount`, `deposit_due_date`, `paid_date`, `payment_method`, `upload_to_calender`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 4.00, '2021-06-21', '2021-06-21', '5', 'true', '2021-06-11 08:24:32', '2021-06-11 08:24:32'),
(2, 1, 2, 2.00, '2021-06-22', '2021-06-22', '5', 'true', '2021-06-11 15:54:05', '2021-06-11 10:54:05'),
(3, 2, 1, 1.00, '2021-06-16', '2021-06-16', '5', 'true', '2021-06-11 08:24:32', '2021-06-11 08:24:32'),
(4, 2, 2, 4.00, '2021-06-16', '2021-06-16', '4', 'true', '2021-06-11 08:24:33', '2021-06-11 08:24:33'),
(5, 1, 3, 2.00, '2021-06-28', '2021-06-28', '5', 'false', '2021-06-11 10:54:05', '2021-06-11 10:54:05');

-- --------------------------------------------------------

--
-- Table structure for table `finance_booking_detail_logs`
--

CREATE TABLE `finance_booking_detail_logs` (
  `id` int(11) NOT NULL,
  `booking_detail_id` int(10) DEFAULT NULL,
  `row` int(10) DEFAULT NULL,
  `log_no` int(10) NOT NULL DEFAULT 0,
  `deposit_amount` float(16,2) DEFAULT NULL,
  `deposit_due_date` date DEFAULT NULL,
  `paid_date` date DEFAULT NULL,
  `payment_method` varchar(255) DEFAULT NULL,
  `upload_to_calender` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `finance_booking_detail_logs`
--

INSERT INTO `finance_booking_detail_logs` (`id`, `booking_detail_id`, `row`, `log_no`, `deposit_amount`, `deposit_due_date`, `paid_date`, `payment_method`, `upload_to_calender`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 4.00, '2021-06-21', '2021-06-21', '5', NULL, '2021-06-11 09:25:12', '2021-06-11 09:25:12'),
(2, 1, 1, 1, 2.00, '2021-06-22', '2021-06-22', '5', NULL, '2021-06-11 09:25:12', '2021-06-11 09:25:12'),
(3, 2, 2, 1, 1.00, '2021-06-16', '2021-06-16', '5', NULL, '2021-06-11 09:25:13', '2021-06-11 09:25:13'),
(4, 2, 2, 1, 4.00, '2021-06-16', '2021-06-16', '4', NULL, '2021-06-11 09:25:13', '2021-06-11 09:25:13'),
(5, 3, 1, 2, 4.00, '2021-06-21', '2021-06-21', '5', NULL, '2021-06-11 10:20:26', '2021-06-11 10:20:26'),
(6, 3, 1, 2, 2.00, '2021-06-22', '2021-06-22', '5', NULL, '2021-06-11 10:20:26', '2021-06-11 10:20:26'),
(7, 4, 2, 2, 1.00, '2021-06-16', '2021-06-16', '5', NULL, '2021-06-11 10:20:26', '2021-06-11 10:20:26'),
(8, 4, 2, 2, 4.00, '2021-06-16', '2021-06-16', '4', NULL, '2021-06-11 10:20:26', '2021-06-11 10:20:26'),
(9, 5, 1, 3, 4.00, '2021-06-21', '2021-06-21', '5', NULL, '2021-06-11 10:54:04', '2021-06-11 10:54:04'),
(10, 5, 1, 3, 2.00, '2021-06-22', '2021-06-22', '5', NULL, '2021-06-11 10:54:04', '2021-06-11 10:54:04'),
(11, 6, 2, 3, 1.00, '2021-06-16', '2021-06-16', '5', NULL, '2021-06-11 10:54:05', '2021-06-11 10:54:05'),
(12, 6, 2, 3, 4.00, '2021-06-16', '2021-06-16', '4', NULL, '2021-06-11 10:54:05', '2021-06-11 10:54:05'),
(13, 7, 1, 4, 4.00, '2021-06-21', '2021-06-21', '5', NULL, '2021-06-11 10:54:15', '2021-06-11 10:54:15'),
(14, 7, 1, 4, 2.00, '2021-06-22', '2021-06-22', '5', NULL, '2021-06-11 10:54:15', '2021-06-11 10:54:15'),
(15, 7, 1, 4, 2.00, '2021-06-28', '2021-06-28', '5', NULL, '2021-06-11 10:54:15', '2021-06-11 10:54:15'),
(16, 8, 2, 4, 1.00, '2021-06-16', '2021-06-16', '5', NULL, '2021-06-11 10:54:15', '2021-06-11 10:54:15'),
(17, 8, 2, 4, 4.00, '2021-06-16', '2021-06-16', '4', NULL, '2021-06-11 10:54:15', '2021-06-11 10:54:15'),
(18, 9, 1, 5, 4.00, '2021-06-21', '2021-06-21', '5', NULL, '2021-06-12 00:52:29', '2021-06-12 00:52:29'),
(19, 9, 1, 5, 2.00, '2021-06-22', '2021-06-22', '5', NULL, '2021-06-12 00:52:30', '2021-06-12 00:52:30'),
(20, 9, 1, 5, 2.00, '2021-06-28', '2021-06-28', '5', NULL, '2021-06-12 00:52:30', '2021-06-12 00:52:30'),
(21, 10, 2, 5, 1.00, '2021-06-16', '2021-06-16', '5', NULL, '2021-06-12 00:52:30', '2021-06-12 00:52:30'),
(22, 10, 2, 5, 4.00, '2021-06-16', '2021-06-16', '4', NULL, '2021-06-12 00:52:30', '2021-06-12 00:52:30'),
(23, 11, 1, 6, 4.00, '2021-06-21', '2021-06-21', '5', NULL, '2021-06-12 00:53:15', '2021-06-12 00:53:15'),
(24, 11, 1, 6, 2.00, '2021-06-22', '2021-06-22', '5', NULL, '2021-06-12 00:53:15', '2021-06-12 00:53:15'),
(25, 11, 1, 6, 2.00, '2021-06-28', '2021-06-28', '5', NULL, '2021-06-12 00:53:15', '2021-06-12 00:53:15'),
(26, 12, 2, 6, 1.00, '2021-06-16', '2021-06-16', '5', NULL, '2021-06-12 00:53:15', '2021-06-12 00:53:15'),
(27, 12, 2, 6, 4.00, '2021-06-16', '2021-06-16', '4', NULL, '2021-06-12 00:53:15', '2021-06-12 00:53:15'),
(28, 13, 1, 7, 4.00, '2021-06-21', '2021-06-21', '5', NULL, '2021-06-12 05:20:54', '2021-06-12 05:20:54'),
(29, 13, 1, 7, 2.00, '2021-06-22', '2021-06-22', '5', NULL, '2021-06-12 05:20:54', '2021-06-12 05:20:54'),
(30, 13, 1, 7, 2.00, '2021-06-28', '2021-06-28', '5', NULL, '2021-06-12 05:20:54', '2021-06-12 05:20:54'),
(31, 14, 2, 7, 1.00, '2021-06-16', '2021-06-16', '5', NULL, '2021-06-12 05:20:54', '2021-06-12 05:20:54'),
(32, 14, 2, 7, 4.00, '2021-06-16', '2021-06-16', '4', NULL, '2021-06-12 05:20:54', '2021-06-12 05:20:54'),
(33, 15, 1, 8, 4.00, '2021-06-21', '2021-06-21', '5', NULL, '2021-06-12 05:23:33', '2021-06-12 05:23:33'),
(34, 15, 1, 8, 2.00, '2021-06-22', '2021-06-22', '5', NULL, '2021-06-12 05:23:33', '2021-06-12 05:23:33'),
(35, 15, 1, 8, 2.00, '2021-06-28', '2021-06-28', '5', NULL, '2021-06-12 05:23:33', '2021-06-12 05:23:33'),
(36, 16, 2, 8, 1.00, '2021-06-16', '2021-06-16', '5', NULL, '2021-06-12 05:23:33', '2021-06-12 05:23:33'),
(37, 16, 2, 8, 4.00, '2021-06-16', '2021-06-16', '4', NULL, '2021-06-12 05:23:33', '2021-06-12 05:23:33'),
(38, 17, 1, 9, 4.00, '2021-06-21', '2021-06-21', '5', NULL, '2021-06-12 05:24:39', '2021-06-12 05:24:39'),
(39, 17, 1, 9, 2.00, '2021-06-22', '2021-06-22', '5', NULL, '2021-06-12 05:24:39', '2021-06-12 05:24:39'),
(40, 17, 1, 9, 2.00, '2021-06-28', '2021-06-28', '5', NULL, '2021-06-12 05:24:39', '2021-06-12 05:24:39'),
(41, 18, 2, 9, 1.00, '2021-06-16', '2021-06-16', '5', NULL, '2021-06-12 05:24:39', '2021-06-12 05:24:39'),
(42, 18, 2, 9, 4.00, '2021-06-16', '2021-06-16', '4', NULL, '2021-06-12 05:24:39', '2021-06-12 05:24:39'),
(43, 19, 1, 10, 4.00, '2021-06-21', '2021-06-21', '5', NULL, '2021-06-12 05:24:52', '2021-06-12 05:24:52'),
(44, 19, 1, 10, 2.00, '2021-06-22', '2021-06-22', '5', NULL, '2021-06-12 05:24:52', '2021-06-12 05:24:52'),
(45, 19, 1, 10, 2.00, '2021-06-28', '2021-06-28', '5', NULL, '2021-06-12 05:24:52', '2021-06-12 05:24:52'),
(46, 20, 2, 10, 1.00, '2021-06-16', '2021-06-16', '5', NULL, '2021-06-12 05:24:52', '2021-06-12 05:24:52'),
(47, 20, 2, 10, 4.00, '2021-06-16', '2021-06-16', '4', NULL, '2021-06-12 05:24:52', '2021-06-12 05:24:52'),
(48, 21, 1, 11, 4.00, '2021-06-21', '2021-06-21', '5', NULL, '2021-06-12 05:26:02', '2021-06-12 05:26:02'),
(49, 21, 1, 11, 2.00, '2021-06-22', '2021-06-22', '5', NULL, '2021-06-12 05:26:02', '2021-06-12 05:26:02'),
(50, 21, 1, 11, 2.00, '2021-06-28', '2021-06-28', '5', NULL, '2021-06-12 05:26:02', '2021-06-12 05:26:02'),
(51, 22, 2, 11, 1.00, '2021-06-16', '2021-06-16', '5', NULL, '2021-06-12 05:26:02', '2021-06-12 05:26:02'),
(52, 22, 2, 11, 4.00, '2021-06-16', '2021-06-16', '4', NULL, '2021-06-12 05:26:02', '2021-06-12 05:26:02');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2020_01_29_150608_create_seasons_table', 1),
(10, '2020_02_12_142429_create_supervisors_table', 2),
(11, '2014_10_12_000000_create_users_table', 1),
(21, '2020_02_13_112256_create_booking_emails_table', 3),
(25, '2020_02_24_115441_create_payments_table', 5),
(27, '2020_02_24_115824_create_airlines_table', 6),
(31, '2020_01_29_150917_create_bookings_table', 7);

-- --------------------------------------------------------

--
-- Table structure for table `old_bookings`
--

CREATE TABLE `old_bookings` (
  `id` int(10) UNSIGNED NOT NULL,
  `ref_no` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `brand_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `season_id` int(10) UNSIGNED NOT NULL,
  `agency_booking` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pax_no` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date_of_travel` date NOT NULL,
  `flight_booked` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fb_airline_name_id` int(10) UNSIGNED NOT NULL,
  `fb_payment_method_id` int(10) UNSIGNED NOT NULL,
  `fb_booking_date` date NOT NULL,
  `fb_airline_ref_no` varchar(25) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fb_last_date` date DEFAULT NULL,
  `fb_person` int(10) UNSIGNED DEFAULT NULL,
  `fb_48hr` int(10) UNSIGNED DEFAULT NULL,
  `fb_24hr` int(10) UNSIGNED DEFAULT NULL,
  `fb_0hr` int(10) UNSIGNED DEFAULT NULL,
  `flight_booking_details` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `asked_for_transfer_details` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `transfer_details` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `aft_last_date` date DEFAULT NULL,
  `aft_person` int(10) UNSIGNED DEFAULT NULL,
  `aft_48hr` int(10) UNSIGNED DEFAULT NULL,
  `aft_24hr` int(10) UNSIGNED DEFAULT NULL,
  `aft_0hr` int(10) UNSIGNED DEFAULT NULL,
  `form_sent_on` date NOT NULL,
  `form_received_on` date DEFAULT NULL,
  `fso_last_date` date DEFAULT NULL,
  `fso_person` int(10) UNSIGNED DEFAULT NULL,
  `fso_48hr` int(10) UNSIGNED DEFAULT NULL,
  `fso_24hr` int(10) UNSIGNED DEFAULT NULL,
  `fso_0hr` int(10) UNSIGNED DEFAULT NULL,
  `app_login_date` date DEFAULT NULL,
  `transfer_info_received` varchar(5) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transfer_info_details` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `itinerary_finalised` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `itinerary_finalised_details` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `itf_last_date` date DEFAULT NULL,
  `itf_current_date` date DEFAULT NULL,
  `itf_person` int(10) UNSIGNED DEFAULT NULL,
  `itf_48hr` int(10) UNSIGNED DEFAULT NULL,
  `itf_24hr` int(10) UNSIGNED DEFAULT NULL,
  `itf_0hr` int(10) UNSIGNED DEFAULT NULL,
  `documents_sent` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `documents_sent_details` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tds_current_date` date DEFAULT NULL,
  `ds_last_date` date DEFAULT NULL,
  `ds_person` int(10) UNSIGNED DEFAULT NULL,
  `ds_48hr` int(10) UNSIGNED DEFAULT NULL,
  `ds_24hr` int(10) UNSIGNED DEFAULT NULL,
  `ds_0hr` int(10) UNSIGNED DEFAULT NULL,
  `document_prepare` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dp_last_date` date DEFAULT NULL,
  `dp_person` int(10) UNSIGNED DEFAULT NULL,
  `dp_48hr` int(10) UNSIGNED DEFAULT NULL,
  `dp_24hr` int(10) UNSIGNED DEFAULT NULL,
  `dp_0hr` int(10) UNSIGNED DEFAULT NULL,
  `tdp_current_date` date DEFAULT NULL,
  `aps_last_date` date DEFAULT NULL,
  `aps_person` int(10) UNSIGNED DEFAULT NULL,
  `aps_48hr` int(10) UNSIGNED DEFAULT NULL,
  `aps_24hr` int(10) UNSIGNED DEFAULT NULL,
  `aps_0hr` int(10) UNSIGNED DEFAULT NULL,
  `electronic_copy_sent` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `electronic_copy_details` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `transfer_organised` varchar(5) COLLATE utf8mb4_unicode_ci NOT NULL,
  `transfer_organised_details` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `to_last_date` date DEFAULT NULL,
  `to_person` int(10) UNSIGNED DEFAULT NULL,
  `to_48hr` int(10) UNSIGNED DEFAULT NULL,
  `to_24hr` int(10) UNSIGNED DEFAULT NULL,
  `to_0hr` int(10) UNSIGNED DEFAULT NULL,
  `type_of_holidays` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sale_person` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deposit_received` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `remaining_amount_received` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `finance_detail` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `destination` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `old_bookings`
--

INSERT INTO `old_bookings` (`id`, `ref_no`, `brand_name`, `season_id`, `agency_booking`, `pax_no`, `date_of_travel`, `flight_booked`, `fb_airline_name_id`, `fb_payment_method_id`, `fb_booking_date`, `fb_airline_ref_no`, `fb_last_date`, `fb_person`, `fb_48hr`, `fb_24hr`, `fb_0hr`, `flight_booking_details`, `asked_for_transfer_details`, `transfer_details`, `aft_last_date`, `aft_person`, `aft_48hr`, `aft_24hr`, `aft_0hr`, `form_sent_on`, `form_received_on`, `fso_last_date`, `fso_person`, `fso_48hr`, `fso_24hr`, `fso_0hr`, `app_login_date`, `transfer_info_received`, `transfer_info_details`, `itinerary_finalised`, `itinerary_finalised_details`, `itf_last_date`, `itf_current_date`, `itf_person`, `itf_48hr`, `itf_24hr`, `itf_0hr`, `documents_sent`, `documents_sent_details`, `tds_current_date`, `ds_last_date`, `ds_person`, `ds_48hr`, `ds_24hr`, `ds_0hr`, `document_prepare`, `dp_last_date`, `dp_person`, `dp_48hr`, `dp_24hr`, `dp_0hr`, `tdp_current_date`, `aps_last_date`, `aps_person`, `aps_48hr`, `aps_24hr`, `aps_0hr`, `electronic_copy_sent`, `electronic_copy_details`, `transfer_organised`, `transfer_organised_details`, `to_last_date`, `to_person`, `to_48hr`, `to_24hr`, `to_0hr`, `type_of_holidays`, `sale_person`, `deposit_received`, `remaining_amount_received`, `finance_detail`, `destination`, `notes`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 'test', 'Unforgettable Greece', 1, '2', '1', '2021-03-30', 'no', 1, 3, '2021-03-30', '24525', '2021-03-30', 9, NULL, 1, NULL, ';this is th e testing detail', 'no', NULL, '2021-04-01', 9, NULL, NULL, NULL, '2021-04-01', NULL, '2021-03-29', 9, NULL, NULL, NULL, NULL, NULL, NULL, 'no', NULL, '2021-04-01', '2021-03-29', 9, NULL, NULL, NULL, 'no', NULL, '2021-03-29', '2021-04-01', 9, NULL, NULL, NULL, 'no', '2021-03-29', 9, NULL, NULL, NULL, '2021-03-29', '2021-03-29', 9, NULL, NULL, NULL, 'no', NULL, 'no', NULL, '2021-04-01', 9, NULL, NULL, NULL, '', 'syedarsalankv@gmail.com', 0, 0, NULL, NULL, NULL, 1, '2021-03-29 08:46:14', '2021-03-29 09:20:21');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `name`, `created_at`, `updated_at`) VALUES
(4, 'Paypal', '2020-02-27 08:51:51', '2020-02-27 08:51:51'),
(5, 'Credit Card', '2021-02-23 09:11:15', '2021-02-23 09:11:15');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `code` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(555) NOT NULL,
  `updated_at` date NOT NULL,
  `created_at` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `code`, `name`, `description`, `updated_at`, `created_at`) VALUES
(1, 'MX12', 'Dummy Product', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.', '2021-06-08', '2021-02-26'),
(2, 'MX122', 'Dummy  Product 2', 'daisgc', '2021-06-08', '2021-03-02');

-- --------------------------------------------------------

--
-- Table structure for table `qoutes`
--

CREATE TABLE `qoutes` (
  `id` int(11) NOT NULL,
  `reference_name` varchar(255) DEFAULT NULL,
  `ref_no` varchar(255) DEFAULT NULL,
  `quotation_no` varchar(255) DEFAULT NULL,
  `dinning_preferences` varchar(255) NOT NULL,
  `lead_passenger_name` varchar(255) NOT NULL,
  `brand_name` varchar(255) DEFAULT NULL,
  `type_of_holidays` varchar(255) DEFAULT NULL,
  `sale_person` varchar(255) DEFAULT NULL,
  `season_id` int(10) DEFAULT NULL,
  `agency_booking` tinyint(1) DEFAULT NULL,
  `agency_name` varchar(255) DEFAULT NULL,
  `agency_contact_no` int(11) DEFAULT NULL,
  `currency` varchar(255) DEFAULT NULL,
  `convert_currency` varchar(255) DEFAULT NULL,
  `group_no` int(10) DEFAULT NULL,
  `net_price` float(16,2) DEFAULT NULL,
  `markup_amount` float(16,2) DEFAULT NULL,
  `selling` float(16,2) DEFAULT NULL,
  `gross_profit` float(16,2) DEFAULT NULL,
  `markup_percent` int(10) DEFAULT NULL,
  `show_convert_currency` float(16,2) DEFAULT NULL,
  `per_person` float(16,2) DEFAULT NULL,
  `port_tax` float(16,2) DEFAULT NULL,
  `total_per_person` float(16,2) DEFAULT NULL,
  `is_email_send` tinyint(1) DEFAULT 0,
  `qoute_to_booking_status` int(10) NOT NULL DEFAULT 0,
  `qoute_to_booking_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `qoutes`
--

INSERT INTO `qoutes` (`id`, `reference_name`, `ref_no`, `quotation_no`, `dinning_preferences`, `lead_passenger_name`, `brand_name`, `type_of_holidays`, `sale_person`, `season_id`, `agency_booking`, `agency_name`, `agency_contact_no`, `currency`, `convert_currency`, `group_no`, `net_price`, `markup_amount`, `selling`, `gross_profit`, `markup_percent`, `show_convert_currency`, `per_person`, `port_tax`, `total_per_person`, `is_email_send`, `qoute_to_booking_status`, `qoute_to_booking_date`, `created_at`, `updated_at`) VALUES
(1, 'zoho', 'uc20181999', '60c1bca366053', 'Dinning Preferences', 'TEst passenger name', 'Unforgettable Travel', 'UCroatia: Activity Holiday', 'muntahaismat@gmail.com', 1, 1, NULL, NULL, 'AUD', NULL, 2, 7.68, 0.00, 7.68, 0.00, 0, 0.00, 0.00, NULL, NULL, 0, 1, '2021-06-10', '2021-06-10 02:18:29', '2021-06-12 00:53:36'),
(2, 'zoho', 'uc20181999', '60c4836e5cfb2', 'Dinning Preferences', 'TEst passenger name', 'Unforgettable Croatia', 'UCroatia: Escorted Tour', 'muntahaismat@gmail.com', 1, 1, NULL, NULL, 'GBP', 'AUD', 4, 27.00, 4.59, 31.59, 14.53, 17, 56.86, 14.22, NULL, NULL, 0, 0, NULL, '2021-06-12 04:51:58', '2021-06-12 04:51:58');

-- --------------------------------------------------------

--
-- Table structure for table `qoute_details`
--

CREATE TABLE `qoute_details` (
  `id` int(10) NOT NULL,
  `qoute_id` int(10) DEFAULT NULL,
  `date_of_service` date DEFAULT NULL,
  `service_details` varchar(255) DEFAULT NULL,
  `category_id` int(10) DEFAULT NULL,
  `supplier` int(10) DEFAULT NULL,
  `booking_date` date DEFAULT NULL,
  `booking_due_date` date DEFAULT NULL,
  `booking_method` int(10) DEFAULT NULL,
  `booked_by` int(10) DEFAULT NULL,
  `booking_refrence` varchar(255) DEFAULT NULL,
  `booking_type` varchar(255) DEFAULT NULL,
  `comments` varchar(255) DEFAULT NULL,
  `supplier_currency` varchar(255) DEFAULT NULL,
  `cost` float(16,2) DEFAULT NULL,
  `actual_cost` float(16,2) DEFAULT 0.00,
  `supervisor_id` int(10) DEFAULT NULL,
  `added_in_sage` tinyint(4) DEFAULT 0,
  `qoute_base_currency` float(16,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `qoute_details`
--

INSERT INTO `qoute_details` (`id`, `qoute_id`, `date_of_service`, `service_details`, `category_id`, `supplier`, `booking_date`, `booking_due_date`, `booking_method`, `booked_by`, `booking_refrence`, `booking_type`, `comments`, `supplier_currency`, `cost`, `actual_cost`, `supervisor_id`, `added_in_sage`, `qoute_base_currency`, `created_at`, `updated_at`) VALUES
(17, 1, NULL, NULL, NULL, NULL, NULL, '2021-07-06', 1, 1, NULL, NULL, NULL, 'EUR', 3.00, 0.00, NULL, 0, 4.68, '2021-06-12 00:53:36', '2021-06-12 00:53:36'),
(18, 1, NULL, NULL, NULL, NULL, NULL, '2021-07-06', 1, 1, NULL, NULL, NULL, 'AUD', 3.00, 0.00, NULL, 0, 3.00, '2021-06-12 00:53:37', '2021-06-12 00:53:37'),
(19, 2, NULL, NULL, NULL, NULL, NULL, '2021-06-07', 1, 19, NULL, NULL, NULL, 'GBP', 25.00, 0.00, 9, NULL, 25.00, '2021-06-12 04:51:58', '2021-06-12 04:51:58'),
(20, 2, NULL, NULL, NULL, NULL, NULL, '2021-06-07', 1, 19, NULL, NULL, NULL, 'GBP', 2.00, 0.00, 9, NULL, 2.00, '2021-06-12 04:51:58', '2021-06-12 04:51:58');

-- --------------------------------------------------------

--
-- Table structure for table `qoute_detail_logs`
--

CREATE TABLE `qoute_detail_logs` (
  `id` int(10) NOT NULL,
  `qoute_id` int(10) DEFAULT NULL,
  `log_no` int(10) DEFAULT 0,
  `date_of_service` date DEFAULT NULL,
  `service_details` varchar(255) DEFAULT NULL,
  `category_id` int(10) DEFAULT NULL,
  `supplier` int(10) DEFAULT NULL,
  `booking_date` date DEFAULT NULL,
  `booking_due_date` date DEFAULT NULL,
  `booking_method` int(10) DEFAULT NULL,
  `booked_by` int(10) DEFAULT NULL,
  `booking_refrence` varchar(255) DEFAULT NULL,
  `booking_type` varchar(255) DEFAULT NULL,
  `comments` varchar(255) DEFAULT NULL,
  `supplier_currency` varchar(255) DEFAULT NULL,
  `cost` float(16,2) DEFAULT NULL,
  `supervisor_id` int(10) DEFAULT NULL,
  `added_in_sage` tinyint(4) DEFAULT 0,
  `qoute_base_currency` float(16,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `qoute_detail_logs`
--

INSERT INTO `qoute_detail_logs` (`id`, `qoute_id`, `log_no`, `date_of_service`, `service_details`, `category_id`, `supplier`, `booking_date`, `booking_due_date`, `booking_method`, `booked_by`, `booking_refrence`, `booking_type`, `comments`, `supplier_currency`, `cost`, `supervisor_id`, `added_in_sage`, `qoute_base_currency`, `created_at`, `updated_at`) VALUES
(1, 1, 1, NULL, NULL, NULL, NULL, NULL, '2021-07-06', 1, 1, NULL, NULL, NULL, 'EUR', 3.00, NULL, 0, 4.68, '2021-06-10 02:19:28', '2021-06-10 02:19:28'),
(2, 1, 1, NULL, NULL, NULL, NULL, NULL, '2021-07-06', 1, 1, NULL, NULL, NULL, NULL, 3.00, NULL, 0, 0.00, '2021-06-10 02:19:28', '2021-06-10 02:19:28'),
(3, 1, 2, NULL, NULL, NULL, NULL, NULL, '2021-07-06', 1, 1, NULL, NULL, NULL, 'EUR', 3.00, NULL, 0, 4.68, '2021-06-10 02:19:42', '2021-06-10 02:19:42'),
(4, 1, 2, NULL, NULL, NULL, NULL, NULL, '2021-07-06', 1, 1, NULL, NULL, NULL, 'AUD', 3.00, NULL, 0, 3.00, '2021-06-10 02:19:42', '2021-06-10 02:19:42'),
(5, 1, 3, NULL, NULL, NULL, NULL, NULL, '2021-07-06', 1, 1, NULL, NULL, NULL, 'EUR', 3.00, NULL, 0, 4.68, '2021-06-10 02:21:24', '2021-06-10 02:21:24'),
(6, 1, 3, NULL, NULL, NULL, NULL, NULL, '2021-07-06', 1, 1, NULL, NULL, NULL, 'AUD', 3.00, NULL, 0, 3.00, '2021-06-10 02:21:24', '2021-06-10 02:21:24'),
(7, 1, 4, NULL, NULL, NULL, NULL, NULL, '2021-07-06', 1, 1, NULL, NULL, NULL, 'EUR', 3.00, NULL, 0, 4.68, '2021-06-10 02:21:44', '2021-06-10 02:21:44'),
(8, 1, 4, NULL, NULL, NULL, NULL, NULL, '2021-07-06', 1, 1, NULL, NULL, NULL, 'AUD', 3.00, NULL, 0, 3.00, '2021-06-10 02:21:44', '2021-06-10 02:21:44'),
(9, 1, 5, NULL, NULL, NULL, NULL, NULL, '2021-07-06', 1, 1, NULL, NULL, NULL, 'EUR', 3.00, NULL, 0, 4.68, '2021-06-10 02:22:48', '2021-06-10 02:22:48'),
(10, 1, 5, NULL, NULL, NULL, NULL, NULL, '2021-07-06', 1, 1, NULL, NULL, NULL, 'AUD', 3.00, NULL, 0, 3.00, '2021-06-10 02:22:48', '2021-06-10 02:22:48'),
(11, 1, 6, NULL, NULL, NULL, NULL, NULL, '2021-07-06', 1, 1, NULL, NULL, NULL, 'EUR', 3.00, NULL, 0, 4.68, '2021-06-10 02:23:10', '2021-06-10 02:23:10'),
(12, 1, 6, NULL, NULL, NULL, NULL, NULL, '2021-07-06', 1, 1, NULL, NULL, NULL, 'AUD', 3.00, NULL, 0, 3.00, '2021-06-10 02:23:10', '2021-06-10 02:23:10'),
(13, 1, 7, NULL, NULL, NULL, NULL, NULL, '2021-07-06', 1, 1, NULL, NULL, NULL, 'EUR', 3.00, NULL, 0, 4.68, '2021-06-10 02:23:50', '2021-06-10 02:23:50'),
(14, 1, 7, NULL, NULL, NULL, NULL, NULL, '2021-07-06', 1, 1, NULL, NULL, NULL, 'AUD', 3.00, NULL, 0, 3.00, '2021-06-10 02:23:50', '2021-06-10 02:23:50'),
(15, 1, 8, NULL, NULL, NULL, NULL, NULL, '2021-07-06', 1, 1, NULL, NULL, NULL, 'EUR', 3.00, NULL, 0, 4.68, '2021-06-12 00:53:36', '2021-06-12 00:53:36'),
(16, 1, 8, NULL, NULL, NULL, NULL, NULL, '2021-07-06', 1, 1, NULL, NULL, NULL, 'AUD', 3.00, NULL, 0, 3.00, '2021-06-12 00:53:36', '2021-06-12 00:53:36');

-- --------------------------------------------------------

--
-- Table structure for table `qoute_emails`
--

CREATE TABLE `qoute_emails` (
  `id` int(10) NOT NULL,
  `qoute_id` int(10) DEFAULT NULL,
  `from` varchar(255) DEFAULT NULL,
  `to` int(10) DEFAULT NULL,
  `template` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `qoute_emails`
--

INSERT INTO `qoute_emails` (`id`, `qoute_id`, `from`, `to`, `template`, `created_at`, `updated_at`) VALUES
(1, 1, 'kashan.kingdomvision@gmail.com', 1, 'http://whipplewebdesign.com/php/unforgettable_form/public/edit-quote/1', '2021-03-30 06:09:35', '2021-03-30 06:09:35');

-- --------------------------------------------------------

--
-- Table structure for table `qoute_logs`
--

CREATE TABLE `qoute_logs` (
  `id` int(11) NOT NULL,
  `qoute_id` int(10) DEFAULT NULL,
  `log_no` int(10) DEFAULT 0,
  `reference_name` varchar(255) DEFAULT NULL,
  `ref_no` varchar(255) DEFAULT NULL,
  `quotation_no` varchar(255) DEFAULT NULL,
  `dinning_preferences` varchar(255) NOT NULL,
  `lead_passenger_name` varchar(255) NOT NULL,
  `brand_name` varchar(255) DEFAULT NULL,
  `type_of_holidays` varchar(255) DEFAULT NULL,
  `sale_person` varchar(255) DEFAULT NULL,
  `season_id` int(10) DEFAULT NULL,
  `agency_booking` tinyint(1) DEFAULT NULL,
  `agency_name` varchar(255) DEFAULT NULL,
  `agency_contact_no` int(11) DEFAULT NULL,
  `currency` varchar(255) DEFAULT NULL,
  `convert_currency` varchar(255) DEFAULT NULL,
  `group_no` int(10) DEFAULT NULL,
  `net_price` float(16,2) DEFAULT NULL,
  `markup_amount` float(16,2) DEFAULT NULL,
  `selling` float(16,2) DEFAULT NULL,
  `gross_profit` float(16,2) DEFAULT NULL,
  `markup_percent` int(10) DEFAULT NULL,
  `show_convert_currency` float(16,2) DEFAULT NULL,
  `per_person` float(16,2) DEFAULT NULL,
  `port_tax` float(16,2) DEFAULT NULL,
  `total_per_person` float(16,2) DEFAULT NULL,
  `is_email_send` tinyint(1) DEFAULT 0,
  `created_date` date DEFAULT NULL,
  `user_id` int(10) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `qoute_logs`
--

INSERT INTO `qoute_logs` (`id`, `qoute_id`, `log_no`, `reference_name`, `ref_no`, `quotation_no`, `dinning_preferences`, `lead_passenger_name`, `brand_name`, `type_of_holidays`, `sale_person`, `season_id`, `agency_booking`, `agency_name`, `agency_contact_no`, `currency`, `convert_currency`, `group_no`, `net_price`, `markup_amount`, `selling`, `gross_profit`, `markup_percent`, `show_convert_currency`, `per_person`, `port_tax`, `total_per_person`, `is_email_send`, `created_date`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'zoho', 'uc20181999', '60c1bca366053', 'Dinning Preferences', 'TEst passenger name', 'Unforgettable Travel', 'UCroatia: Activity Holiday', 'muntahaismat@gmail.com', 1, 1, NULL, NULL, 'AUD', NULL, 2, 4.68, 0.00, 4.68, 0.00, 0, 0.00, 0.00, NULL, NULL, 0, '2021-06-10', 1, '2021-06-10 02:19:27', '2021-06-10 02:19:27'),
(2, 1, 2, 'zoho', 'uc20181999', '60c1bca366053', 'Dinning Preferences', 'TEst passenger name', 'Unforgettable Travel', 'UCroatia: Activity Holiday', 'muntahaismat@gmail.com', 1, 1, NULL, NULL, 'AUD', NULL, 2, 7.68, 0.00, 7.68, 0.00, 0, 0.00, 0.00, NULL, NULL, 0, '2021-06-10', 1, '2021-06-10 02:19:41', '2021-06-10 02:19:41'),
(3, 1, 3, 'zoho', 'uc20181999', '60c1bca366053', 'Dinning Preferences', 'TEst passenger name', 'Unforgettable Travel', 'UCroatia: Activity Holiday', 'muntahaismat@gmail.com', 1, 1, NULL, NULL, 'AUD', NULL, 2, 7.68, 0.00, 7.68, 0.00, 0, 0.00, 0.00, NULL, NULL, 0, '2021-06-10', 1, '2021-06-10 02:21:24', '2021-06-10 02:21:24'),
(4, 1, 4, 'zoho', 'uc20181999', '60c1bca366053', 'Dinning Preferences', 'TEst passenger name', 'Unforgettable Travel', 'UCroatia: Activity Holiday', 'muntahaismat@gmail.com', 1, 1, NULL, NULL, 'AUD', NULL, 2, 7.68, 0.00, 7.68, 0.00, 0, 0.00, 0.00, NULL, NULL, 0, '2021-06-10', 1, '2021-06-10 02:21:44', '2021-06-10 02:21:44'),
(5, 1, 5, 'zoho', 'uc20181999', '60c1bca366053', 'Dinning Preferences', 'TEst passenger name', 'Unforgettable Travel', 'UCroatia: Activity Holiday', 'muntahaismat@gmail.com', 1, 1, NULL, NULL, 'AUD', NULL, 2, 7.68, 0.00, 7.68, 0.00, 0, 0.00, 0.00, NULL, NULL, 0, '2021-06-10', 1, '2021-06-10 02:22:47', '2021-06-10 02:22:47'),
(6, 1, 6, 'zoho', 'uc20181999', '60c1bca366053', 'Dinning Preferences', 'TEst passenger name', 'Unforgettable Travel', 'UCroatia: Activity Holiday', 'muntahaismat@gmail.com', 1, 1, NULL, NULL, 'AUD', NULL, 2, 7.68, 0.00, 7.68, 0.00, 0, 0.00, 0.00, NULL, NULL, 0, '2021-06-10', 1, '2021-06-10 02:23:09', '2021-06-10 02:23:09'),
(7, 1, 7, 'zoho', 'uc20181999', '60c1bca366053', 'Dinning Preferences', 'TEst passenger name', 'Unforgettable Travel', 'UCroatia: Activity Holiday', 'muntahaismat@gmail.com', 1, 1, NULL, NULL, 'AUD', NULL, 2, 7.68, 0.00, 7.68, 0.00, 0, 0.00, 0.00, NULL, NULL, 0, '2021-06-10', 1, '2021-06-10 02:23:49', '2021-06-10 02:23:49'),
(8, 1, 8, 'zoho', 'uc20181999', '60c1bca366053', 'Dinning Preferences', 'TEst passenger name', 'Unforgettable Travel', 'UCroatia: Activity Holiday', 'muntahaismat@gmail.com', 1, 1, NULL, NULL, 'AUD', NULL, 2, 7.68, 0.00, 7.68, 0.00, 0, 0.00, 0.00, NULL, NULL, 0, '2021-06-12', 1, '2021-06-12 00:53:36', '2021-06-12 00:53:36');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `updated_at` date NOT NULL,
  `created_at` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `updated_at`, `created_at`) VALUES
(1, 'Admin', '2021-06-07', '2021-03-02'),
(2, 'Sales Agent', '2021-03-02', '2021-03-02'),
(3, 'Corporation', '2021-03-02', '2021-03-02'),
(4, 'Manager', '2021-03-02', '2021-03-02'),
(5, 'Supervisor', '2021-04-13', '2021-04-13');

-- --------------------------------------------------------

--
-- Table structure for table `seasons`
--

CREATE TABLE `seasons` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `default_season` int(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `seasons`
--

INSERT INTO `seasons` (`id`, `name`, `start_date`, `end_date`, `default_season`, `created_at`, `updated_at`) VALUES
(1, '2019-2020', '2021-06-02', '2021-06-07', 1, '2020-02-07 09:25:18', '2021-06-09 08:43:39'),
(2, '2020-2021', NULL, NULL, 0, '2020-02-07 09:25:22', '2021-06-09 08:43:39');

-- --------------------------------------------------------

--
-- Table structure for table `supervisors`
--

CREATE TABLE `supervisors` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` int(20) DEFAULT NULL,
  `currency_id` int(11) DEFAULT NULL,
  `updated_at` date NOT NULL,
  `created_at` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`id`, `name`, `email`, `phone`, `currency_id`, `updated_at`, `created_at`) VALUES
(1, 'Irfan', 'irfan@kingdom-vision.co.uk', 2147483647, 2, '2021-05-28', '2021-03-30'),
(2, 'Raza', 'abc@gmail.com', 2147483643, 2, '2021-06-08', '2021-03-30'),
(4, 'Kashan Mehmood', 'kashan@gmail.com', 2147483647, 3, '2021-03-31', '2021-03-31'),
(5, 'Port Charges', 'test@gmail.com', 12345678, 3, '2021-04-13', '2021-04-13'),
(6, 'Feroz', 'feroz@gmail.com', 2147483647, 1, '2021-05-28', '2021-05-28'),
(8, 'Easyjet', NULL, NULL, NULL, '2021-06-08', '2021-06-08'),
(9, 'Jet2', NULL, NULL, NULL, '2021-06-08', '2021-06-08'),
(10, 'British Airways', NULL, NULL, NULL, '2021-06-08', '2021-06-08');

-- --------------------------------------------------------

--
-- Table structure for table `supplier_categories`
--

CREATE TABLE `supplier_categories` (
  `id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `updated_at` date NOT NULL,
  `created_at` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `supplier_categories`
--

INSERT INTO `supplier_categories` (`id`, `supplier_id`, `category_id`, `updated_at`, `created_at`) VALUES
(1, 1, 1, '2021-03-30', '2021-03-30'),
(3, 2, 2, '2021-03-30', '2021-03-30'),
(6, 1, 2, '2021-03-31', '2021-03-31'),
(7, 1, 3, '2021-03-31', '2021-03-31'),
(8, 4, 2, '2021-03-31', '2021-03-31'),
(9, 5, 6, '0000-00-00', '0000-00-00'),
(10, 6, 1, '0000-00-00', '0000-00-00'),
(12, 8, 7, '0000-00-00', '0000-00-00'),
(13, 9, 7, '0000-00-00', '0000-00-00'),
(14, 10, 7, '0000-00-00', '0000-00-00');

-- --------------------------------------------------------

--
-- Table structure for table `supplier_products`
--

CREATE TABLE `supplier_products` (
  `id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `updated_at` date NOT NULL,
  `created_at` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `supplier_products`
--

INSERT INTO `supplier_products` (`id`, `supplier_id`, `product_id`, `updated_at`, `created_at`) VALUES
(2, 1, 1, '2021-03-30', '2021-03-30'),
(6, 1, 2, '2021-03-31', '2021-03-31'),
(7, 2, 1, '2021-03-31', '2021-03-31'),
(8, 4, 1, '2021-03-31', '2021-03-31'),
(9, 5, 1, '0000-00-00', '0000-00-00'),
(10, 6, 1, '0000-00-00', '0000-00-00');

-- --------------------------------------------------------

--
-- Table structure for table `templates`
--

CREATE TABLE `templates` (
  `id` int(10) NOT NULL,
  `user_id` int(11) UNSIGNED DEFAULT NULL,
  `season_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `templates`
--

INSERT INTO `templates` (`id`, `user_id`, `season_id`, `title`, `status`, `created_at`, `updated_at`) VALUES
(18, 19, 1, 'Qoute Template 1', 'active', '2021-06-12 09:49:40', '2021-06-12 04:49:40');

-- --------------------------------------------------------

--
-- Table structure for table `template_details`
--

CREATE TABLE `template_details` (
  `id` int(10) NOT NULL,
  `template_id` int(10) DEFAULT NULL,
  `date_of_service` date DEFAULT NULL,
  `service_details` varchar(255) DEFAULT NULL,
  `category_id` int(10) DEFAULT NULL,
  `supplier` int(10) DEFAULT NULL,
  `booking_date` date DEFAULT NULL,
  `booking_due_date` date DEFAULT NULL,
  `booking_method` int(10) DEFAULT NULL,
  `booked_by` int(10) DEFAULT NULL,
  `booking_refrence` varchar(255) DEFAULT NULL,
  `booking_type` varchar(255) DEFAULT NULL,
  `comments` varchar(255) DEFAULT NULL,
  `supplier_currency` varchar(255) DEFAULT NULL,
  `cost` float(16,2) DEFAULT NULL,
  `actual_cost` float(16,2) DEFAULT 0.00,
  `supervisor_id` int(10) DEFAULT NULL,
  `added_in_sage` tinyint(4) DEFAULT 0,
  `qoute_base_currency` float(16,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `template_details`
--

INSERT INTO `template_details` (`id`, `template_id`, `date_of_service`, `service_details`, `category_id`, `supplier`, `booking_date`, `booking_due_date`, `booking_method`, `booked_by`, `booking_refrence`, `booking_type`, `comments`, `supplier_currency`, `cost`, `actual_cost`, `supervisor_id`, `added_in_sage`, `qoute_base_currency`, `created_at`, `updated_at`) VALUES
(22, 18, NULL, NULL, NULL, NULL, NULL, '2021-06-07', 1, 19, NULL, NULL, NULL, NULL, 0.00, 0.00, 9, 0, NULL, '2021-06-12 04:49:40', '2021-06-12 04:49:40'),
(23, 18, NULL, NULL, NULL, NULL, NULL, '2021-06-07', 1, 19, NULL, NULL, NULL, NULL, NULL, 0.00, 9, 0, NULL, '2021-06-12 04:49:40', '2021-06-12 06:10:38');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role_id` int(11) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `supervisor_id` int(10) UNSIGNED DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_login` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `currency_id` int(10) DEFAULT NULL,
  `brand_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `role_id`, `email_verified_at`, `supervisor_id`, `password`, `remember_token`, `is_login`, `currency_id`, `brand_name`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'kashan.kingdomvision@gmail.com', 1, NULL, NULL, '$2y$10$PZvdq9E3SrPjGlPM22bxf.FZA57s4Id8LxRUQin0O3rYgOoHP9RiC', NULL, 0, NULL, NULL, '2021-06-07 11:27:04', '2021-06-12 01:00:11'),
(9, 'Tabraiz', 'tabraiz.kingdomvision@gmail.com', 5, NULL, NULL, '$2y$10$519qHoziYRJgpBhMH2GCeuIAHUWHUCyNUEtSKqvvg.HRSKAAWt/2e', NULL, 0, 1, 'Unforgettable Greece', '2021-06-08 05:10:25', '2021-06-08 05:10:25'),
(10, 'Tabseer', 'tabseer@gmail.com', 5, NULL, NULL, '$2y$10$cEBhJoppMuu/r6ESgDq2N.Ynthfj7yn8ybieoR0YwuZwFF.sC3lpq', NULL, 0, 2, 'Unforgettable Cruises', '2021-06-08 05:11:15', '2021-06-08 05:11:15'),
(19, 'Hamza', 'hamza@gmail.com', 2, NULL, 9, '$2y$10$ZEngxglTq/g/6O3EIPBWUuMum7oeirKaWPZE/JZZrJU6yT1GuQG.a', NULL, 0, 1, 'Unforgettable Croatia', '2021-06-08 05:18:42', '2021-06-12 07:00:17'),
(20, 'Taimoor', 'taimoor@gmail.com', 2, NULL, 9, '$2y$10$5ruLl0KewGpcptwUQki9b.FIn71vwmaqTIOug4iI34swZkK6lNuEi', NULL, 0, 3, 'Unforgettable Travel', '2021-06-12 01:30:22', '2021-06-12 01:30:22'),
(21, 'Rizwan', 'rizwan@gmail.com', 2, NULL, 10, '$2y$10$1ZB/eYFqhn08OFc9BwE3duhupFhgvVL.dzeIDVsBWbiDPDH5P1J1a', NULL, 0, 4, 'Unforgettable Greece', '2021-06-12 01:31:27', '2021-06-12 01:31:27');

-- --------------------------------------------------------

--
-- Table structure for table `zoho_credentials`
--

CREATE TABLE `zoho_credentials` (
  `id` int(10) NOT NULL,
  `code` varchar(255) DEFAULT NULL,
  `access_token` varchar(255) DEFAULT NULL,
  `refresh_token` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `zoho_credentials`
--

INSERT INTO `zoho_credentials` (`id`, `code`, `access_token`, `refresh_token`) VALUES
(1, '1000.f07c6e03fa956656371a7226d6ebd576.78a5a1af693f0f2b6d389b8fe11633c4', '1000.eca2f16e7458ba0d9befa5a9bc173f8a.5ab1da35dc63c9eec65ebb4beff0c388', '1000.93747e52d8c3bb5611666cf01cbd3e84.3c185f593775e82635f3c52d22bcf5cf');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `airlines`
--
ALTER TABLE `airlines`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `booking_details`
--
ALTER TABLE `booking_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Indexes for table `booking_detail_logs`
--
ALTER TABLE `booking_detail_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `booking_emails`
--
ALTER TABLE `booking_emails`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_emails_booking_id_foreign` (`booking_id`);

--
-- Indexes for table `booking_logs`
--
ALTER TABLE `booking_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `booking_methods`
--
ALTER TABLE `booking_methods`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `codes`
--
ALTER TABLE `codes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bookings_season_id_foreign` (`season_id`),
  ADD KEY `bookings_user_id_foreign` (`user_id`);

--
-- Indexes for table `currencies`
--
ALTER TABLE `currencies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `currency_conversion`
--
ALTER TABLE `currency_conversion`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `finance_booking_details`
--
ALTER TABLE `finance_booking_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `finance_booking_detail_logs`
--
ALTER TABLE `finance_booking_detail_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `old_bookings`
--
ALTER TABLE `old_bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bookings_season_id_foreign` (`season_id`),
  ADD KEY `bookings_user_id_foreign` (`user_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`(191));

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `qoutes`
--
ALTER TABLE `qoutes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `qoute_details`
--
ALTER TABLE `qoute_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `qoute_id` (`qoute_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `qoute_detail_logs`
--
ALTER TABLE `qoute_detail_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `qoute_emails`
--
ALTER TABLE `qoute_emails`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `qoute_logs`
--
ALTER TABLE `qoute_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `qoute_id` (`qoute_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `seasons`
--
ALTER TABLE `seasons`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `supervisors`
--
ALTER TABLE `supervisors`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `currency_id` (`currency_id`);

--
-- Indexes for table `supplier_categories`
--
ALTER TABLE `supplier_categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `supplier_id` (`supplier_id`);

--
-- Indexes for table `supplier_products`
--
ALTER TABLE `supplier_products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `supplier_id` (`supplier_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `templates`
--
ALTER TABLE `templates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `season_id` (`season_id`);

--
-- Indexes for table `template_details`
--
ALTER TABLE `template_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `template_id` (`template_id`),
  ADD KEY `booking_method` (`booking_method`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `supplier` (`supplier`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `role_id` (`role_id`),
  ADD KEY `currency_id` (`currency_id`),
  ADD KEY `supervisor_id` (`supervisor_id`);

--
-- Indexes for table `zoho_credentials`
--
ALTER TABLE `zoho_credentials`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `airlines`
--
ALTER TABLE `airlines`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `booking_details`
--
ALTER TABLE `booking_details`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `booking_detail_logs`
--
ALTER TABLE `booking_detail_logs`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `booking_emails`
--
ALTER TABLE `booking_emails`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `booking_logs`
--
ALTER TABLE `booking_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `booking_methods`
--
ALTER TABLE `booking_methods`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `codes`
--
ALTER TABLE `codes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `currencies`
--
ALTER TABLE `currencies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `currency_conversion`
--
ALTER TABLE `currency_conversion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `finance_booking_details`
--
ALTER TABLE `finance_booking_details`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `finance_booking_detail_logs`
--
ALTER TABLE `finance_booking_detail_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `old_bookings`
--
ALTER TABLE `old_bookings`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `qoutes`
--
ALTER TABLE `qoutes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `qoute_details`
--
ALTER TABLE `qoute_details`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `qoute_detail_logs`
--
ALTER TABLE `qoute_detail_logs`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `qoute_emails`
--
ALTER TABLE `qoute_emails`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `qoute_logs`
--
ALTER TABLE `qoute_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `seasons`
--
ALTER TABLE `seasons`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `supervisors`
--
ALTER TABLE `supervisors`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `supplier_categories`
--
ALTER TABLE `supplier_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `supplier_products`
--
ALTER TABLE `supplier_products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `templates`
--
ALTER TABLE `templates`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `template_details`
--
ALTER TABLE `template_details`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `zoho_credentials`
--
ALTER TABLE `zoho_credentials`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `booking_details`
--
ALTER TABLE `booking_details`
  ADD CONSTRAINT `booking_details_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `booking_emails`
--
ALTER TABLE `booking_emails`
  ADD CONSTRAINT `booking_emails_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `codes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `codes`
--
ALTER TABLE `codes`
  ADD CONSTRAINT `bookings_season_id_foreign` FOREIGN KEY (`season_id`) REFERENCES `seasons` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `qoute_details`
--
ALTER TABLE `qoute_details`
  ADD CONSTRAINT `qoute_details_ibfk_1` FOREIGN KEY (`qoute_id`) REFERENCES `qoutes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `qoute_details_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `qoute_detail_logs`
--
ALTER TABLE `qoute_detail_logs`
  ADD CONSTRAINT `qoute_detail_logs_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `qoute_logs`
--
ALTER TABLE `qoute_logs`
  ADD CONSTRAINT `qoute_logs_ibfk_1` FOREIGN KEY (`qoute_id`) REFERENCES `qoutes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD CONSTRAINT `suppliers_ibfk_1` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `suppliers_ibfk_2` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `supplier_categories`
--
ALTER TABLE `supplier_categories`
  ADD CONSTRAINT `supplier_categories_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `supplier_categories_ibfk_3` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `supplier_categories_ibfk_4` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `supplier_categories_ibfk_5` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `supplier_products`
--
ALTER TABLE `supplier_products`
  ADD CONSTRAINT `supplier_products_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `supplier_products_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `templates`
--
ALTER TABLE `templates`
  ADD CONSTRAINT `templates_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `templates_ibfk_2` FOREIGN KEY (`season_id`) REFERENCES `seasons` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `template_details`
--
ALTER TABLE `template_details`
  ADD CONSTRAINT `template_details_ibfk_1` FOREIGN KEY (`template_id`) REFERENCES `templates` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `template_details_ibfk_2` FOREIGN KEY (`booking_method`) REFERENCES `booking_methods` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `template_details_ibfk_3` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `template_details_ibfk_4` FOREIGN KEY (`supplier`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`supervisor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `users_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `users_ibfk_3` FOREIGN KEY (`currency_id`) REFERENCES `currencies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `users_ibfk_4` FOREIGN KEY (`supervisor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
