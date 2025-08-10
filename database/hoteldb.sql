-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 10, 2025 at 05:14 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

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
(37, 4, 10, '2025-08-09', '2025-08-10', 1500.00, 'cancelled', '', '', 1, '2025-08-09 14:38:01', '2025-08-09 14:39:25'),
(38, 4, 9, '2025-08-09', '2025-08-10', 2500.00, 'cancelled', '', '', 1, '2025-08-09 14:38:09', '2025-08-09 14:39:16'),
(39, 4, 6, '2025-08-09', '2025-08-10', 1500.00, 'confirmed', '', '', 1, '2025-08-09 14:43:11', '2025-08-10 15:04:33'),
(40, 4, 7, '2025-08-09', '2025-08-10', 1500.00, 'confirmed', '', '', 1, '2025-08-09 14:51:15', '2025-08-09 14:56:48'),
(41, 4, 10, '2025-08-10', '2025-08-11', 1500.00, 'confirmed', '', '', 1, '2025-08-10 14:06:43', '2025-08-10 14:09:22'),
(42, 4, 7, '2025-08-10', '2025-08-13', 4500.00, 'confirmed', '', '', 1, '2025-08-10 14:31:15', '2025-08-10 14:31:25'),
(43, 8, 6, '2025-08-10', '2025-08-19', 13500.00, 'confirmed', '', '', 1, '2025-08-10 15:03:02', '2025-08-10 15:03:15'),
(44, 8, 10, '2025-08-16', '2025-08-19', 4500.00, 'confirmed', '', '', 1, '2025-08-10 15:05:44', '2025-08-10 15:06:10'),
(45, 8, 10, '2025-09-01', '2025-09-03', 3000.00, 'confirmed', '', '', 1, '2025-08-10 15:10:39', '2025-08-10 15:10:47');

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
(1, 4, 41, 'Booking Confirmed! ✅', 'Great news! Your booking for Room 10 from August 10, 2025 to August 11, 2025 has been confirmed. We look forward to welcoming you!', 'booking_confirmed', 1, '2025-08-10 14:09:22', '2025-08-10 14:56:42'),
(2, 4, 41, 'Booking Confirmed! ✅', 'Great news! Your booking for Room 10 from August 10, 2025 to August 11, 2025 has been confirmed. We look forward to welcoming you!', 'booking_confirmed', 1, '2025-08-10 14:09:22', '2025-08-10 14:56:42'),
(3, 4, 42, 'Booking Confirmed! ✅', 'Great news! Your booking for Room 7 from August 10, 2025 to August 13, 2025 has been confirmed. We look forward to welcoming you!', 'booking_confirmed', 1, '2025-08-10 14:31:25', '2025-08-10 14:56:42'),
(4, 4, 42, 'Booking Confirmed! ✅', 'Great news! Your booking for Room 3 from August 10, 2025 to August 13, 2025 has been confirmed. We look forward to welcoming you!', 'booking_confirmed', 1, '2025-08-10 14:31:26', '2025-08-10 14:56:42'),
(5, 8, 43, 'Booking Confirmed! ✅', 'Great news! Your booking for Room 6 from August 10, 2025 to August 19, 2025 has been confirmed. We look forward to welcoming you!', 'booking_confirmed', 1, '2025-08-10 15:03:15', '2025-08-10 15:04:13'),
(6, 8, 43, 'Booking Confirmed! ✅', 'Great news! Your booking for Room 1 from August 10, 2025 to August 19, 2025 has been confirmed. We look forward to welcoming you!', 'booking_confirmed', 1, '2025-08-10 15:03:15', '2025-08-10 15:04:17'),
(7, 4, 39, 'Booking Confirmed! ✅', 'Great news! Your booking for Room 6 from August 09, 2025 to August 10, 2025 has been confirmed. We look forward to welcoming you!', 'booking_confirmed', 0, '2025-08-10 15:04:33', '2025-08-10 15:04:33'),
(8, 4, 39, 'Booking Confirmed! ✅', 'Great news! Your booking for Room 1 from August 9, 2025 to August 10, 2025 has been confirmed. We look forward to welcoming you!', 'booking_confirmed', 0, '2025-08-10 15:04:33', '2025-08-10 15:04:33'),
(9, 8, 44, 'Booking Confirmed! ✅', 'Great news! Your booking for Room 10 from August 16, 2025 to August 19, 2025 has been confirmed. We look forward to welcoming you!', 'booking_confirmed', 1, '2025-08-10 15:06:10', '2025-08-10 15:06:44'),
(10, 8, 44, 'Booking Confirmed! ✅', 'Great news! Your booking for Room 10 from August 16, 2025 to August 19, 2025 has been confirmed. We look forward to welcoming you!', 'booking_confirmed', 1, '2025-08-10 15:06:10', '2025-08-10 15:06:44'),
(11, 8, 45, 'Booking Confirmed! ✅', 'Great news! Your booking for Room 10 from September 01, 2025 to September 03, 2025 has been confirmed. We look forward to welcoming you!', 'booking_confirmed', 1, '2025-08-10 15:10:47', '2025-08-10 15:13:35'),
(12, 8, 45, 'Booking Confirmed! ✅', 'Great news! Your booking for Room 10 from September 1, 2025 to September 3, 2025 has been confirmed. We look forward to welcoming you!', 'booking_confirmed', 1, '2025-08-10 15:10:47', '2025-08-10 15:13:35');

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
(1, 1, 2, 50, 100, 'Window'),
(2, 2, 4, 150, 100, 'Center'),
(3, 3, 6, 250, 100, 'Corner'),
(4, 4, 4, 350, 100, 'Center'),
(5, 5, 2, 450, 100, 'Window'),
(6, 6, 10, 550, 100, 'Center'),
(7, 7, 5, 650, 100, 'Corner');

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
(5, 'Room 2', 5, '[\"room_6815e9a3bc7b74.38249271.jpg\",\"room_6815e9a3bcd876.43427399.jpg\"]', '1500', 'Wifi'),
(6, 'Room 1', 5, '[\"room_6815ea106c7152.73864788.jpg\",\"room_6815ea106cb153.70063731.jpg\"]', '1500', 'Flat Screen Tv'),
(7, 'Room 3', 5, '[\"room_68172f9bb476f1.95935670.jpg\",\"room_68172f9bb4d1c2.96168563.jpg\",\"room_68172f9bb50ae2.42324066.jpg\"]', '1500', 'Wifi'),
(8, 'Room 4', 6, '[\"room_681f765d1e5a40.24006654.jpg\",\"room_681f765d1ecdf7.90503933.jpg\",\"room_681f765d1f3272.17751895.jpg\"]', '2500', 'Wifi'),
(9, 'Delux', 6, '[\"room_68454b3c4b7720.19881913.jpg\"]', '2500', 'Shower'),
(10, 'Room 10', 6, '[\"room_6849422e0c6688.71534324.jpg\"]', '1500', 'Testing');

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
(7, 'Test', 'Test');

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
(2, 1, '01:50:00', '13:50:00', '2025-05-05', '2025-05-05', 'done');

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
(2, 'Business Lunch', 'Quick and delicious 2-course business lunch with coffee. Available Monday to Friday from 12:00 PM to 2:00 PM.\r\n\r\n', '1746612814_restaurant.jpg', '5000');

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
(1, 'Armando Raguindin', 'Technician', 'Roxas', '', 'Morning', '639685340012', 'armando@gmail.com', '202cb962ac59075b964b07152d234b70');

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
  `status` enum('pending','done') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `table_reservations`
--

INSERT INTO `table_reservations` (`reservation_id`, `table_id`, `reservation_date`, `time_slot`, `guest_count`, `special_requests`, `user_id`, `status`) VALUES
(9, 5, '2025-05-08', '12:00:00', 2, '', 4, 'done'),
(12, 5, '2025-05-16', '20:00:00', 2, '', 6, 'done'),
(13, 6, '2025-05-16', '07:00:00', 8, '', 6, 'done'),
(14, 2, '2025-05-17', '07:00:00', 4, '', 6, 'done'),
(15, 7, '2025-05-31', '07:00:00', 5, '', 6, 'pending'),
(16, 1, '2025-06-11', '20:00:00', 2, '', 7, 'pending'),
(17, 3, '2025-06-11', '07:00:00', 6, '', 7, 'pending');

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
(1, 1, 'AC repair', 'Maintenance of Aircon at Room 3 and Room 4', '2025-05-09 17:00:00', 'Done', '2025-05-11 14:38:03', '2025-05-17 09:40:52');

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
(3, 'Admin', 'Roxas', 'suguitanmark123@gmail.com', '202cb962ac59075b964b07152d234b70', 'admin', '639360991034'),
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
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_booking_id` (`booking_id`),
  ADD KEY `idx_is_read` (`is_read`);

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
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

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
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `restaurant_menu`
--
ALTER TABLE `restaurant_menu`
  MODIFY `menu_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `restaurant_tables`
--
ALTER TABLE `restaurant_tables`
  MODIFY `table_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `room_type`
--
ALTER TABLE `room_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `shifts`
--
ALTER TABLE `shifts`
  MODIFY `shift_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `special_offers`
--
ALTER TABLE `special_offers`
  MODIFY `offers_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `staffs`
--
ALTER TABLE `staffs`
  MODIFY `staff_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `table_reservations`
--
ALTER TABLE `table_reservations`
  MODIFY `reservation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
