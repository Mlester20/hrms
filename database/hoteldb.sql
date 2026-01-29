-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 29, 2026 at 08:51 AM
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
-- Database: `hoteldb`
--

DELIMITER $$
--
-- Functions
--
CREATE DEFINER=`root`@`localhost` FUNCTION `GetUnreadNotificationCount` (`p_user_id` INT) RETURNS INT(11) DETERMINISTIC READS SQL DATA BEGIN
    DECLARE notification_count INT DEFAULT 0;
    
    SELECT COUNT(*) INTO notification_count
    FROM user_notifications 
    WHERE user_id = p_user_id AND is_read = 0;
    
    RETURN notification_count;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `auth_users`
--

CREATE TABLE `auth_users` (
  `id` int(11) NOT NULL,
  `google_id` varchar(255) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `banner`
--

CREATE TABLE `banner` (
  `banner_id` int(11) NOT NULL,
  `banner_img` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `booking_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `check_in_date` date NOT NULL,
  `check_out_date` date NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `status` enum('pending','confirmed','cancelled','completed') DEFAULT 'pending',
  `payment_status` enum('unpaid','partially_paid','paid') DEFAULT 'unpaid',
  `special_requests` text DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`booking_id`, `user_id`, `room_id`, `check_in_date`, `check_out_date`, `total_price`, `status`, `payment_status`, `special_requests`, `is_read`, `created_at`, `updated_at`) VALUES
(60, 8, 13, '2026-01-23', '2026-01-25', 5000.00, 'completed', 'paid', '', 1, '2026-01-22 07:36:58', '2026-01-22 08:05:32'),
(61, 8, 12, '2026-01-22', '2026-01-23', 500.00, 'completed', 'paid', '', 1, '2026-01-22 07:48:32', '2026-01-22 08:05:30'),
(62, 8, 13, '2026-01-22', '2026-01-23', 2500.00, 'completed', 'paid', '', 1, '2026-01-22 08:08:04', '2026-01-22 08:14:05');

--
-- Triggers `bookings`
--
DELIMITER $$
CREATE TRIGGER `booking_status_notification` AFTER UPDATE ON `bookings` FOR EACH ROW BEGIN
    -- Check if status changed to 'confirmed'
    IF OLD.status != 'confirmed' AND NEW.status = 'confirmed' THEN
        INSERT INTO notifications (
            user_id, 
            booking_id, 
            title, 
            message, 
            type
        ) VALUES (
            NEW.user_id,
            NEW.booking_id,
            'Booking Confirmed! ✅',
            CONCAT('Great news! Your booking for Room ', NEW.room_id, ' from ', 
                   DATE_FORMAT(NEW.check_in_date, '%M %d, %Y'), ' to ', 
                   DATE_FORMAT(NEW.check_out_date, '%M %d, %Y'), ' has been confirmed. We look forward to welcoming you!'),
            'booking_confirmed'
        );
    END IF;
    
    -- Check if status changed to 'cancelled'
    IF OLD.status != 'cancelled' AND NEW.status = 'cancelled' THEN
        INSERT INTO notifications (
            user_id, 
            booking_id, 
            title, 
            message, 
            type
        ) VALUES (
            NEW.user_id,
            NEW.booking_id,
            'Booking Cancelled ❌',
            CONCAT('Your booking for Room ', NEW.room_id, ' from ', 
                   DATE_FORMAT(NEW.check_in_date, '%M %d, %Y'), ' to ', 
                   DATE_FORMAT(NEW.check_out_date, '%M %d, %Y'), ' has been cancelled. If you have any questions, please contact us.'),
            'booking_cancelled'
        );
    END IF;
    
    -- Check if status changed to 'completed'
    IF OLD.status != 'completed' AND NEW.status = 'completed' THEN
        INSERT INTO notifications (
            user_id, 
            booking_id, 
            title, 
            message, 
            type
        ) VALUES (
            NEW.user_id,
            NEW.booking_id,
            'Stay Completed ?',
            CONCAT('Thank you for staying with us! Your booking for Room ', NEW.room_id, ' has been completed. We hope you had a wonderful experience.'),
            'booking_completed'
        );
    END IF;
    
    -- Check if payment status changed to 'paid'
    IF OLD.payment_status != 'paid' AND NEW.payment_status = 'paid' THEN
        INSERT INTO notifications (
            user_id, 
            booking_id, 
            title, 
            message, 
            type
        ) VALUES (
            NEW.user_id,
            NEW.booking_id,
            'Payment Received! ?',
            CONCAT('We have received your full payment of ₱', FORMAT(NEW.total_price, 2), ' for your booking. Thank you for your payment!'),
            'payment_received'
        );
    END IF;
    
    -- Check if payment status changed to 'partially_paid'
    IF OLD.payment_status != 'partially_paid' AND NEW.payment_status = 'partially_paid' THEN
        INSERT INTO notifications (
            user_id, 
            booking_id, 
            title, 
            message, 
            type
        ) VALUES (
            NEW.user_id,
            NEW.booking_id,
            'Partial Payment Received ?',
            CONCAT('We have received a partial payment for your booking. Please complete your payment before check-in.'),
            'payment_received'
        );
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `concerns`
--

CREATE TABLE `concerns` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `description`
--

CREATE TABLE `description` (
  `description_id` int(11) NOT NULL,
  `description_name` varchar(1000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `description`
--

INSERT INTO `description` (`description_id`, `description_name`) VALUES
(1, 'Our objective at Seeds Hotel & Restaurant is to bring together our visitor\'s activities and spirits with our own, communicating enthusiasm and sincerity in the food we share. Official Chef and Owner Philippines Massoud expertly creates a blend of Lebanese, Levantine, Mediterranean influenced food divided in each New York morning. Delightful herbs and flavors, connected to Nature\'s parity and ancient arabic potions.');

-- --------------------------------------------------------

--
-- Table structure for table `menus`
--

CREATE TABLE `menus` (
  `menu_id` int(11) NOT NULL,
  `menu_name` varchar(150) NOT NULL,
  `category` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `description` text DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menus`
--

INSERT INTO `menus` (`menu_id`, `menu_name`, `category`, `price`, `description`, `status`, `created_at`, `updated_at`) VALUES
(2, 'Crispy Garlic Shrimp', 'appetizer', 320.00, 'Lightly battered shrimp fried until golden and tossed in garlic butter.', 'available', '2026-01-28 07:39:56', '2026-01-28 07:39:56'),
(3, 'Pizza', 'main', 1500.00, 'Test', 'available', '2026-01-28 08:08:13', '2026-01-28 08:08:28');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `booking_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` enum('booking_confirmed','booking_cancelled','booking_rejected','payment_received','general') DEFAULT 'general',
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`notification_id`, `user_id`, `booking_id`, `title`, `message`, `type`, `is_read`, `created_at`, `updated_at`) VALUES
(52, 4, NULL, 'Booking Confirmed! ✅', 'Great news! Your booking for Room 10 from August 11, 2025 to August 12, 2025 has been confirmed. We look forward to welcoming you!', 'booking_confirmed', 1, '2025-08-11 15:25:07', '2025-08-11 15:25:22'),
(53, 4, NULL, 'Payment Received! 💳', 'We have received your full payment of ₱1,500.00 for your booking. Thank you for your payment!', 'payment_received', 1, '2025-08-11 15:32:50', '2025-08-11 15:32:57'),
(54, 4, NULL, 'Stay Completed 🏨', 'Thank you for staying with us! Your booking for Room 10 has been completed. We hope you had a wonderful experience.', '', 1, '2025-08-11 15:33:47', '2025-08-17 10:01:50'),
(55, 4, NULL, 'Stay Completed ????', 'Thank you for staying with us! Your booking for Room 10 has been completed. We hope you had a wonderful experience.', '', 1, '2025-08-11 15:33:47', '2025-08-17 10:01:50'),
(56, 4, NULL, 'Booking Confirmed! ✅', 'Great news! Your booking for Room 11 from August 17, 2025 to August 20, 2025 has been confirmed. We look forward to welcoming you!', 'booking_confirmed', 1, '2025-08-17 10:02:42', '2025-08-17 10:03:08'),
(57, 4, NULL, 'Payment Received! 💳', 'We have received your full payment of ₱10,500.00 for your booking. Thank you for your payment!', 'payment_received', 1, '2025-08-17 10:02:46', '2025-08-17 10:03:07'),
(58, 4, NULL, 'Booking Confirmed! ✅', 'Great news! Your booking for Room 6 from August 18, 2025 to August 19, 2025 has been confirmed. We look forward to welcoming you!', 'booking_confirmed', 1, '2025-08-18 09:00:57', '2025-08-18 09:02:10'),
(59, 4, NULL, 'Payment Received! 💳', 'We have received your full payment of ₱1,500.00 for your booking. Thank you for your payment!', 'payment_received', 1, '2025-08-18 09:02:38', '2025-08-18 09:03:03'),
(60, 4, NULL, 'Stay Completed 🏨', 'Thank you for staying with us! Your booking for Room 6 has been completed. We hope you had a wonderful experience.', '', 1, '2025-08-18 09:02:41', '2025-08-18 09:03:03'),
(61, 4, NULL, 'Stay Completed ????', 'Thank you for staying with us! Your booking for Room 1 has been completed. We hope you had a wonderful experience.', '', 1, '2025-08-18 09:02:41', '2025-08-18 09:03:03'),
(62, 4, NULL, 'Stay Completed 🏨', 'Thank you for staying with us! Your booking for Room 11 has been completed. We hope you had a wonderful experience.', '', 1, '2025-08-18 09:02:43', '2025-08-18 09:03:03'),
(63, 4, NULL, 'Stay Completed ????', 'Thank you for staying with us! Your booking for Room 2 has been completed. We hope you had a wonderful experience.', '', 1, '2025-08-18 09:02:43', '2025-08-18 09:03:03'),
(64, 4, NULL, 'Booking Confirmed! ✅', 'Great news! Your booking for Room 9 from December 29, 2025 to December 30, 2025 has been confirmed. We look forward to welcoming you!', 'booking_confirmed', 1, '2025-12-29 13:52:48', '2025-12-29 13:53:12'),
(65, 4, NULL, 'Partial Payment Received ?', 'We have received a partial payment for your booking. Please complete your payment before check-in.', 'payment_received', 1, '2025-12-29 13:53:19', '2026-01-20 08:32:53'),
(66, 4, NULL, 'Stay Completed ?', 'Thank you for staying with us! Your booking for Room 9 has been completed. We hope you had a wonderful experience.', '', 1, '2026-01-18 08:25:59', '2026-01-20 08:32:53'),
(67, 4, NULL, 'Stay Completed - Thank You! ????', 'Thank you for staying with us! Your booking for Delux (from December 29, 2025 to December 30, 2025) has been automatically completed. We hope you had a wonderful experience and look forward to welcoming you back soon!', '', 1, '2026-01-18 08:25:59', '2026-01-20 08:32:53'),
(68, 4, NULL, 'Booking Cancelled ❌', 'Your booking for Room 9 from December 29, 2025 to December 30, 2025 has been cancelled. If you have any questions, please contact us.', 'booking_cancelled', 1, '2026-01-18 08:49:26', '2026-01-20 08:32:53'),
(69, 4, NULL, 'Payment Received! ?', 'We have received your full payment of ₱2,500.00 for your booking. Thank you for your payment!', 'payment_received', 1, '2026-01-18 08:52:02', '2026-01-20 08:32:53'),
(70, 4, NULL, 'Booking Confirmed! ✅', 'Great news! Your booking for Room 9 from December 29, 2025 to December 30, 2025 has been confirmed. We look forward to welcoming you!', 'booking_confirmed', 1, '2026-01-18 08:56:36', '2026-01-20 08:32:53'),
(71, 4, NULL, 'Booking Cancelled ❌', 'Your booking for Room 9 from December 29, 2025 to December 30, 2025 has been cancelled. If you have any questions, please contact us.', 'booking_cancelled', 1, '2026-01-18 08:57:33', '2026-01-20 08:32:53'),
(72, 4, NULL, 'Booking Confirmed! ✅', 'Great news! Your booking for Room 9 from December 29, 2025 to December 30, 2025 has been confirmed. We look forward to welcoming you!', 'booking_confirmed', 1, '2026-01-18 08:57:38', '2026-01-20 08:32:53'),
(73, 4, NULL, 'Booking Cancelled ❌', 'Your booking for Room 9 from December 29, 2025 to December 30, 2025 has been cancelled. If you have any questions, please contact us.', 'booking_cancelled', 1, '2026-01-18 08:58:02', '2026-01-20 08:32:53'),
(74, 4, NULL, 'Booking Confirmed! ✅', 'Great news! Your booking for Room 9 from December 29, 2025 to December 30, 2025 has been confirmed. We look forward to welcoming you!', 'booking_confirmed', 1, '2026-01-18 09:01:23', '2026-01-20 08:32:53'),
(75, 4, NULL, 'Stay Completed ?', 'Thank you for staying with us! Your booking for Room 9 has been completed. We hope you had a wonderful experience.', '', 1, '2026-01-18 09:01:27', '2026-01-20 08:32:53'),
(76, 4, NULL, 'Stay Completed ????', 'Thank you for staying with us! Your booking for Delux has been completed. We hope you had a wonderful experience.', '', 1, '2026-01-18 09:01:27', '2026-01-20 08:32:53'),
(77, 4, NULL, 'Booking Cancelled ❌', 'Your booking for Room 9 from December 29, 2025 to December 30, 2025 has been cancelled. If you have any questions, please contact us.', 'booking_cancelled', 1, '2026-01-18 09:01:52', '2026-01-20 08:32:53'),
(78, 4, NULL, 'Booking Confirmed! ✅', 'Great news! Your booking for Room 9 from December 29, 2025 to December 30, 2025 has been confirmed. We look forward to welcoming you!', 'booking_confirmed', 1, '2026-01-18 09:01:55', '2026-01-20 08:32:53'),
(79, 4, NULL, 'Stay Completed ?', 'Thank you for staying with us! Your booking for Room 9 has been completed. We hope you had a wonderful experience.', '', 1, '2026-01-18 09:02:14', '2026-01-20 08:32:53'),
(80, 4, NULL, 'Stay Completed ????', 'Thank you for staying with us! Your booking for Delux has been completed. We hope you had a wonderful experience.', '', 1, '2026-01-18 09:02:14', '2026-01-20 08:32:53'),
(81, 4, NULL, 'Booking Confirmed! ✅', 'Great news! Your booking for Room 9 from December 29, 2025 to December 30, 2025 has been confirmed. We look forward to welcoming you!', 'booking_confirmed', 1, '2026-01-18 09:07:20', '2026-01-20 08:32:53'),
(82, 4, NULL, 'Stay Completed ?', 'Thank you for staying with us! Your booking for Room 9 has been completed. We hope you had a wonderful experience.', '', 1, '2026-01-18 09:08:36', '2026-01-20 08:32:53'),
(83, 4, NULL, 'Stay Completed ????', 'Thank you for staying with us! Your booking for Delux has been completed. We hope you had a wonderful experience.', '', 1, '2026-01-18 09:08:36', '2026-01-20 08:32:53'),
(84, 4, NULL, 'Payment Received! ?', 'We have received your full payment of ₱1,500.00 for your booking. Thank you for your payment!', 'payment_received', 1, '2026-01-20 08:34:21', '2026-01-20 08:42:46'),
(85, 4, NULL, 'Booking Confirmed! ✅', 'Great news! Your booking for Room 7 from January 20, 2026 to January 21, 2026 has been confirmed. We look forward to welcoming you!', 'booking_confirmed', 1, '2026-01-20 08:34:26', '2026-01-20 08:37:09'),
(86, 4, NULL, 'Booking Cancelled ❌', 'Your booking for Room 7 from January 20, 2026 to January 21, 2026 has been cancelled. If you have any questions, please contact us.', 'booking_cancelled', 1, '2026-01-21 06:38:52', '2026-01-21 07:21:26'),
(87, 4, NULL, 'Booking Cancelled ❌', 'Your booking for Room 9 from December 29, 2025 to December 30, 2025 has been cancelled. If you have any questions, please contact us.', 'booking_cancelled', 1, '2026-01-21 06:39:20', '2026-01-21 07:21:26'),
(88, 4, NULL, 'Booking Cancelled ❌', 'Your booking for Room 7 from January 21, 2026 to January 23, 2026 has been cancelled. If you have any questions, please contact us.', 'booking_cancelled', 1, '2026-01-21 07:10:24', '2026-01-21 07:21:26'),
(89, 4, NULL, 'Booking Cancelled ❌', 'Your booking for Room 6 from January 21, 2026 to January 23, 2026 has been cancelled. If you have any questions, please contact us.', 'booking_cancelled', 1, '2026-01-21 07:10:27', '2026-01-21 07:21:26'),
(90, 4, NULL, 'Booking Cancelled ❌', 'Your booking for Room 9 from January 21, 2026 to January 23, 2026 has been cancelled. If you have any questions, please contact us.', 'booking_cancelled', 1, '2026-01-21 07:10:28', '2026-01-21 07:21:26'),
(91, 4, NULL, 'Booking Confirmed! ✅', 'Great news! Your booking for Room 5 from January 21, 2026 to January 23, 2026 has been confirmed. We look forward to welcoming you!', 'booking_confirmed', 1, '2026-01-21 07:21:18', '2026-01-21 07:21:26'),
(92, 4, NULL, 'Booking Confirmed! ✅', 'Great news! Your booking for Room 9 from January 24, 2026 to January 25, 2026 has been confirmed. We look forward to welcoming you!', 'booking_confirmed', 1, '2026-01-22 05:19:30', '2026-01-22 05:20:02'),
(93, 4, NULL, 'Payment Received! ?', 'We have received your full payment of ₱2,500.00 for your booking. Thank you for your payment!', 'payment_received', 1, '2026-01-22 05:19:36', '2026-01-22 05:20:02'),
(94, 4, NULL, 'Payment Received! ?', 'We have received your full payment of ₱3,000.00 for your booking. Thank you for your payment!', 'payment_received', 1, '2026-01-22 05:19:37', '2026-01-22 05:20:02'),
(95, 4, NULL, 'Payment Received! ?', 'We have received your full payment of ₱3,000.00 for your booking. Thank you for your payment!', 'payment_received', 1, '2026-01-22 05:19:38', '2026-01-22 05:20:02'),
(96, 4, NULL, 'Payment Received! ?', 'We have received your full payment of ₱5,000.00 for your booking. Thank you for your payment!', 'payment_received', 1, '2026-01-22 05:19:41', '2026-01-22 05:20:02'),
(97, 4, NULL, 'Payment Received! ?', 'We have received your full payment of ₱3,000.00 for your booking. Thank you for your payment!', 'payment_received', 1, '2026-01-22 05:19:43', '2026-01-22 05:20:02'),
(98, 4, NULL, 'Booking Confirmed! ✅', 'Great news! Your booking for Room 14 from January 22, 2026 to January 23, 2026 has been confirmed. We look forward to welcoming you!', 'booking_confirmed', 1, '2026-01-22 06:24:57', '2026-01-22 06:29:03'),
(99, 4, NULL, 'Payment Received! ?', 'We have received your full payment of ₱1,500.00 for your booking. Thank you for your payment!', 'payment_received', 1, '2026-01-22 06:25:02', '2026-01-22 06:29:03'),
(100, 8, 60, 'Booking Confirmed! ✅', 'Great news! Your booking for Room 13 from January 23, 2026 to January 25, 2026 has been confirmed. We look forward to welcoming you!', 'booking_confirmed', 1, '2026-01-22 07:37:12', '2026-01-22 07:37:46'),
(101, 8, 60, 'Payment Received! ?', 'We have received your full payment of ₱5,000.00 for your booking. Thank you for your payment!', 'payment_received', 1, '2026-01-22 07:37:14', '2026-01-22 07:37:46'),
(102, 8, 61, 'Payment Received! ?', 'We have received your full payment of ₱500.00 for your booking. Thank you for your payment!', 'payment_received', 1, '2026-01-22 07:51:46', '2026-01-22 08:07:22'),
(103, 8, 61, 'Booking Confirmed! ✅', 'Great news! Your booking for Room 12 from January 22, 2026 to January 23, 2026 has been confirmed. We look forward to welcoming you!', 'booking_confirmed', 1, '2026-01-22 07:51:48', '2026-01-22 08:07:22'),
(104, 8, 61, 'Booking Cancelled ❌', 'Your booking for Room 12 from January 22, 2026 to January 23, 2026 has been cancelled. If you have any questions, please contact us.', 'booking_cancelled', 1, '2026-01-22 08:04:19', '2026-01-22 08:07:22'),
(105, 8, 61, 'Booking Confirmed! ✅', 'Great news! Your booking for Room 12 from January 22, 2026 to January 23, 2026 has been confirmed. We look forward to welcoming you!', 'booking_confirmed', 1, '2026-01-22 08:04:21', '2026-01-22 08:07:22'),
(106, 8, 61, 'Stay Completed ?', 'Thank you for staying with us! Your booking for Room 12 has been completed. We hope you had a wonderful experience.', '', 1, '2026-01-22 08:05:30', '2026-01-22 08:07:22'),
(107, 8, 61, 'Stay Completed ????', 'Thank you for staying with us! Your booking for Room 1 has been completed. We hope you had a wonderful experience.', '', 1, '2026-01-22 08:05:30', '2026-01-22 08:07:22'),
(108, 8, 60, 'Stay Completed ?', 'Thank you for staying with us! Your booking for Room 13 has been completed. We hope you had a wonderful experience.', '', 1, '2026-01-22 08:05:32', '2026-01-22 08:07:22'),
(109, 8, 60, 'Stay Completed ????', 'Thank you for staying with us! Your booking for Room 2 has been completed. We hope you had a wonderful experience.', '', 1, '2026-01-22 08:05:32', '2026-01-22 08:07:22'),
(110, 8, 62, 'Booking Confirmed! ✅', 'Great news! Your booking for Room 13 from January 22, 2026 to January 23, 2026 has been confirmed. We look forward to welcoming you!', 'booking_confirmed', 1, '2026-01-22 08:08:38', '2026-01-22 08:13:56'),
(111, 8, 62, 'Payment Received! ?', 'We have received your full payment of ₱2,500.00 for your booking. Thank you for your payment!', 'payment_received', 1, '2026-01-22 08:08:46', '2026-01-22 08:13:56'),
(112, 8, 62, 'Stay Completed ?', 'Thank you for staying with us! Your booking for Room 13 has been completed. We hope you had a wonderful experience.', '', 1, '2026-01-22 08:13:38', '2026-01-22 08:13:56'),
(113, 8, 62, 'Stay Completed ????', 'Thank you for staying with us! Your booking for Room 2 has been completed. We hope you had a wonderful experience.', '', 1, '2026-01-22 08:13:38', '2026-01-22 08:13:56'),
(114, 8, 62, 'Booking Cancelled ❌', 'Your booking for Room 13 from January 22, 2026 to January 23, 2026 has been cancelled. If you have any questions, please contact us.', 'booking_cancelled', 1, '2026-01-22 08:13:52', '2026-01-22 08:13:56'),
(115, 8, 62, 'Stay Completed ?', 'Thank you for staying with us! Your booking for Room 13 has been completed. We hope you had a wonderful experience.', '', 1, '2026-01-22 08:14:05', '2026-01-22 08:14:22'),
(116, 8, 62, 'Stay Completed ????', 'Thank you for staying with us! Your booking for Room 2 has been completed. We hope you had a wonderful experience.', '', 1, '2026-01-22 08:14:05', '2026-01-22 08:14:22');

-- --------------------------------------------------------

--
-- Table structure for table `restaurant_auth`
--

CREATE TABLE `restaurant_auth` (
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(50) NOT NULL,
  `role` varchar(20) NOT NULL,
  `created_at` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `restaurant_auth`
--

INSERT INTO `restaurant_auth` (`user_id`, `name`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'Mark Lester', 'user@gmail.com', '40bd001563085fc35165329ea1ff5c5ecbdbbeef', 'staff', '0000-00-00'),
(3, 'admin', 'admin@gmail.com', 'd033e22ae348aeb5660fc2140aec35850c4da997', 'admin', '0000-00-00'),
(4, 'Cashier', 'cashier@gmail.com', '40bd001563085fc35165329ea1ff5c5ecbdbbeef', 'cashier', '2025-11-27');

-- --------------------------------------------------------

--
-- Table structure for table `restaurant_menu`
--

CREATE TABLE `restaurant_menu` (
  `menu_id` int(11) NOT NULL,
  `menu_name` varchar(255) NOT NULL,
  `menu_description` varchar(500) NOT NULL,
  `image` varchar(500) NOT NULL,
  `price` int(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `restaurant_menu`
--

INSERT INTO `restaurant_menu` (`menu_id`, `menu_name`, `menu_description`, `image`, `price`) VALUES
(9, 'Pizza', 'Good 2-4 persons', '6820ad1831b26.jpg', 2500),
(10, 'Pizza', 'Pizza Burger', '68286635d44d8.jpg', 1500),
(11, 'Pizza', '4 Slices Pizza', '682866d1485b8.jpg', 500);

-- --------------------------------------------------------

--
-- Table structure for table `restaurant_tables`
--

CREATE TABLE `restaurant_tables` (
  `table_id` int(11) NOT NULL,
  `table_number` int(11) NOT NULL,
  `capacity` int(11) NOT NULL,
  `position_x` int(11) NOT NULL,
  `position_y` int(11) NOT NULL,
  `location` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `restaurant_tables`
--

INSERT INTO `restaurant_tables` (`table_id`, `table_number`, `capacity`, `position_x`, `position_y`, `location`) VALUES
(1, 1, 2, 50, 100, 'Test Function'),
(2, 2, 4, 150, 100, 'Center'),
(3, 3, 6, 250, 100, 'Corner'),
(4, 4, 4, 350, 100, 'Center'),
(5, 5, 2, 450, 100, 'Window'),
(6, 6, 10, 550, 100, 'Center'),
(7, 7, 5, 650, 100, 'Corners');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `review_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `review_text` text DEFAULT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`review_id`, `user_id`, `review_text`, `rating`, `created_at`) VALUES
(6, 4, 'The Service was pretty fast and good!', 5, '2025-06-07 08:17:30');

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `room_type_id` int(11) NOT NULL,
  `images` varchar(255) DEFAULT NULL,
  `price` varchar(250) DEFAULT NULL,
  `includes` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `title`, `room_type_id`, `images`, `price`, `includes`) VALUES
(12, 'Room 1', 5, '[\"room_6971c247565430.07754205.jpg\",\"room_6971c247576a74.74818402.jpg\",\"room_6971c24757b556.76802999.jpg\"]', '500', 'Free Wifi'),
(13, 'Room 2', 6, '[\"room_6971be56caa604.41817887.jpg\",\"room_6971be56caf623.80629988.jpg\",\"room_6971be56cb3524.61867782.jpg\"]', '2500', 'Free Wifi'),
(14, 'Room 3', 5, '[\"room_6971be6de54485.80918773.jpg\",\"room_6971be6de5a178.37352919.jpg\",\"room_6971be6de5e9a6.66284070.jpg\"]', '1500', 'Free Wifi');

-- --------------------------------------------------------

--
-- Table structure for table `room_type`
--

CREATE TABLE `room_type` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `detail` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room_type`
--

INSERT INTO `room_type` (`id`, `title`, `detail`) VALUES
(5, 'Delux', 'Delux'),
(6, 'Triple Room', 'Triple Room'),
(8, 'Premium', 'Good for 2 persons');

-- --------------------------------------------------------

--
-- Table structure for table `shifts`
--

CREATE TABLE `shifts` (
  `shift_id` int(11) NOT NULL,
  `staff_id` int(11) NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `date_start` date NOT NULL,
  `date_end` date NOT NULL,
  `status` enum('pending','done') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shifts`
--

INSERT INTO `shifts` (`shift_id`, `staff_id`, `start_time`, `end_time`, `date_start`, `date_end`, `status`) VALUES
(2, 1, '07:30:00', '17:00:00', '2026-01-21', '2026-01-21', NULL),
(4, 3, '08:00:00', '17:00:00', '2026-01-23', '2026-01-23', 'done');

-- --------------------------------------------------------

--
-- Table structure for table `special_offers`
--

CREATE TABLE `special_offers` (
  `offers_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` varchar(2500) NOT NULL,
  `image` varchar(50) NOT NULL,
  `price` varchar(5000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `special_offers`
--

INSERT INTO `special_offers` (`offers_id`, `title`, `description`, `image`, `price`) VALUES
(1, 'Date Night Package', 'Romantic dinner for two featuring a 3-course meal with wine pairing. Perfect for anniversaries and special celebrations.', '1746612490_restaurant.jpg', '2000'),
(2, 'Test Edit Function', 'Quick and delicious 2-course business lunch with coffee. Available Monday to Friday from 12:00 PM to 2:00 PM.\r\n\r\n', '1768981339_Screenshot 2026-01-05 122056.png', '5000'),
(5, 'Test Edit function', 'Test', '1769062263_room1.jpg', '5000');

-- --------------------------------------------------------

--
-- Table structure for table `staffs`
--

CREATE TABLE `staffs` (
  `staff_id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `position` varchar(50) NOT NULL,
  `address` varchar(50) NOT NULL,
  `profile` varchar(250) NOT NULL,
  `shift_type` varchar(50) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staffs`
--

INSERT INTO `staffs` (`staff_id`, `name`, `position`, `address`, `profile`, `shift_type`, `phone_number`, `email`, `password`) VALUES
(1, 'Armando Raguindin', 'Technician', 'Roxas', '', 'Morning', '639685340012', 'armando@gmail.com', '202cb962ac59075b964b07152d234b70'),
(3, 'Test Edit', 'Technicians', 'Rizal, Roxas, Isabela', '', 'Morning', '+639360991035', 'raguindin.lester@gmail.com', '353e0f8a9bc0fe11bb0099c4c009d45c');

-- --------------------------------------------------------

--
-- Table structure for table `table_reservations`
--

CREATE TABLE `table_reservations` (
  `reservation_id` int(11) NOT NULL,
  `table_id` int(11) NOT NULL,
  `reservation_date` date NOT NULL,
  `time_slot` time NOT NULL,
  `guest_count` int(11) NOT NULL,
  `special_requests` text DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `status` enum('pending','done','cancelled','confirmed') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `table_reservations`
--

INSERT INTO `table_reservations` (`reservation_id`, `table_id`, `reservation_date`, `time_slot`, `guest_count`, `special_requests`, `user_id`, `status`) VALUES
(44, 7, '2026-01-29', '22:00:00', 5, '', 4, 'pending'),
(45, 7, '2026-01-29', '10:00:00', 5, '', 4, 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `id` int(11) NOT NULL,
  `staff_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `deadline` datetime DEFAULT NULL,
  `status` enum('Pending','In Progress','Done') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tasks`
--

INSERT INTO `tasks` (`id`, `staff_id`, `title`, `description`, `deadline`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'AC repair', 'Maintenance of Aircon at Room 3 and Room 4', '2025-05-09 17:00:00', 'Done', '2025-05-11 14:38:03', '2025-05-17 09:40:52'),
(2, 1, 'Maintenance', 'Maintenance of Aircons in Room 235 to 240', '2026-01-21 08:30:00', 'Pending', '2026-01-20 08:26:40', '2026-01-20 08:26:40');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `address` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL,
  `role` varchar(20) DEFAULT NULL,
  `phone` tinytext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `address`, `email`, `password`, `role`, `phone`) VALUES
(3, 'Admin', 'Roxas', 'admin@gmail.com', '202cb962ac59075b964b07152d234b70', 'admin', '639360991034'),
(4, 'Mark Lester Raguindin', 'Rizal', 'raguindin.lester20@gmail.com', '202cb962ac59075b964b07152d234b70', 'user', '09360991034'),
(6, 'Mark Lester', 'Roxas, Isabela', 'raguindin.lester20@gmail.com', '202cb962ac59075b964b07152d234b70', 'user', '09360991034'),
(7, 'Test User', 'Rizal, Santiago City', 'gia@gmail.com', '202cb962ac59075b964b07152d234b70', 'user', '09360991034'),
(8, 'Test', 'test', 'test@gmail.com', '202cb962ac59075b964b07152d234b70', 'user', '093424242');

-- --------------------------------------------------------

--
-- Table structure for table `user_notifications`
--

CREATE TABLE `user_notifications` (
  `notification_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `booking_id` int(11) DEFAULT NULL,
  `type` enum('booking_confirmed','booking_cancelled','booking_completed','payment_reminder','general') NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `auth_users`
--
ALTER TABLE `auth_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `google_id` (`google_id`);

--
-- Indexes for table `banner`
--
ALTER TABLE `banner`
  ADD PRIMARY KEY (`banner_id`);

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `room_id` (`room_id`);

--
-- Indexes for table `concerns`
--
ALTER TABLE `concerns`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `description`
--
ALTER TABLE `description`
  ADD PRIMARY KEY (`description_id`);

--
-- Indexes for table `menus`
--
ALTER TABLE `menus`
  ADD PRIMARY KEY (`menu_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_booking_id` (`booking_id`),
  ADD KEY `idx_is_read` (`is_read`);

--
-- Indexes for table `restaurant_auth`
--
ALTER TABLE `restaurant_auth`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `restaurant_menu`
--
ALTER TABLE `restaurant_menu`
  ADD PRIMARY KEY (`menu_id`);

--
-- Indexes for table `restaurant_tables`
--
ALTER TABLE `restaurant_tables`
  ADD PRIMARY KEY (`table_id`),
  ADD UNIQUE KEY `table_number` (`table_number`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_room_type` (`room_type_id`);

--
-- Indexes for table `room_type`
--
ALTER TABLE `room_type`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `shifts`
--
ALTER TABLE `shifts`
  ADD PRIMARY KEY (`shift_id`),
  ADD KEY `staff_id` (`staff_id`);

--
-- Indexes for table `special_offers`
--
ALTER TABLE `special_offers`
  ADD PRIMARY KEY (`offers_id`);

--
-- Indexes for table `staffs`
--
ALTER TABLE `staffs`
  ADD PRIMARY KEY (`staff_id`);

--
-- Indexes for table `table_reservations`
--
ALTER TABLE `table_reservations`
  ADD PRIMARY KEY (`reservation_id`),
  ADD UNIQUE KEY `unique_reservation` (`table_id`,`reservation_date`,`time_slot`),
  ADD KEY `table_id` (`table_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `staff_id` (`staff_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `user_notifications`
--
ALTER TABLE `user_notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `booking_id` (`booking_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `auth_users`
--
ALTER TABLE `auth_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `banner`
--
ALTER TABLE `banner`
  MODIFY `banner_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT for table `concerns`
--
ALTER TABLE `concerns`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `description`
--
ALTER TABLE `description`
  MODIFY `description_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `menus`
--
ALTER TABLE `menus`
  MODIFY `menu_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=117;

--
-- AUTO_INCREMENT for table `restaurant_auth`
--
ALTER TABLE `restaurant_auth`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `restaurant_menu`
--
ALTER TABLE `restaurant_menu`
  MODIFY `menu_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `restaurant_tables`
--
ALTER TABLE `restaurant_tables`
  MODIFY `table_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `room_type`
--
ALTER TABLE `room_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `shifts`
--
ALTER TABLE `shifts`
  MODIFY `shift_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `special_offers`
--
ALTER TABLE `special_offers`
  MODIFY `offers_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `staffs`
--
ALTER TABLE `staffs`
  MODIFY `staff_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `table_reservations`
--
ALTER TABLE `table_reservations`
  MODIFY `reservation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `user_notifications`
--
ALTER TABLE `user_notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`room_id`) REFERENCES `rooms` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `concerns`
--
ALTER TABLE `concerns`
  ADD CONSTRAINT `concerns_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`booking_id`) ON DELETE SET NULL;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `rooms`
--
ALTER TABLE `rooms`
  ADD CONSTRAINT `fk_room_type` FOREIGN KEY (`room_type_id`) REFERENCES `room_type` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `shifts`
--
ALTER TABLE `shifts`
  ADD CONSTRAINT `shifts_ibfk_1` FOREIGN KEY (`staff_id`) REFERENCES `staffs` (`staff_id`) ON DELETE CASCADE;

--
-- Constraints for table `table_reservations`
--
ALTER TABLE `table_reservations`
  ADD CONSTRAINT `table_reservations_ibfk_1` FOREIGN KEY (`table_id`) REFERENCES `restaurant_tables` (`table_id`),
  ADD CONSTRAINT `table_reservations_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (`staff_id`) REFERENCES `staffs` (`staff_id`) ON DELETE CASCADE;

--
-- Constraints for table `user_notifications`
--
ALTER TABLE `user_notifications`
  ADD CONSTRAINT `user_notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_notifications_ibfk_2` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`booking_id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
