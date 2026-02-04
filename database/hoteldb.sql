-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 04, 2026 at 04:42 PM
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
(64, 15, 16, '2026-02-04', '2026-02-05', 1500.00, 'completed', 'paid', '', 1, '2026-02-04 14:54:28', '2026-02-04 15:40:25'),
(65, 15, 13, '2026-02-09', '2026-02-10', 2500.00, 'completed', 'paid', '', 1, '2026-02-04 15:22:30', '2026-02-04 15:40:22');

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
(1, 'Nestled in the heart of the city, our hotel offers a perfect blend of comfort, elegance, and convenience. Designed for both leisure and business travelers, the hotel features modern rooms equipped with premium amenities, ensuring a relaxing and memorable stay. Guests can enjoy 24/7 front desk assistance, complimentary high-speed Wi-Fi, an on-site restaurant serving local and international cuisine, and well-appointed function rooms for meetings and events. With its strategic location and warm hospitality, our hotel provides an ideal home away from home.');

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
(119, 15, 64, 'Booking Confirmed! ✅', 'Great news! Your booking for Room 16 from February 04, 2026 to February 05, 2026 has been confirmed. We look forward to welcoming you!', 'booking_confirmed', 1, '2026-02-04 14:54:52', '2026-02-04 14:55:02'),
(120, 15, 64, 'Payment Received! ?', 'We have received your full payment of ₱1,500.00 for your booking. Thank you for your payment!', 'payment_received', 1, '2026-02-04 14:54:54', '2026-02-04 14:55:02'),
(121, 15, 64, 'Stay Completed ?', 'Thank you for staying with us! Your booking for Room 16 has been completed. We hope you had a wonderful experience.', '', 1, '2026-02-04 14:55:15', '2026-02-04 14:55:29'),
(122, 15, 64, 'Stay Completed ????', 'Thank you for staying with us! Your booking for Room 1 has been completed. We hope you had a wonderful experience.', '', 1, '2026-02-04 14:55:15', '2026-02-04 15:22:10'),
(123, 15, 64, 'Booking Confirmed! ✅', 'Great news! Your booking for Room 16 from February 04, 2026 to February 05, 2026 has been confirmed. We look forward to welcoming you!', 'booking_confirmed', 1, '2026-02-04 15:21:48', '2026-02-04 15:22:08'),
(124, 15, 65, 'Booking Confirmed! ✅', 'Great news! Your booking for Room 13 from February 09, 2026 to February 10, 2026 has been confirmed. We look forward to welcoming you!', 'booking_confirmed', 1, '2026-02-04 15:23:08', '2026-02-04 15:37:40'),
(125, 15, 65, 'Payment Received! ?', 'We have received your full payment of ₱2,500.00 for your booking. Thank you for your payment!', 'payment_received', 1, '2026-02-04 15:23:10', '2026-02-04 15:37:40'),
(126, 15, 65, 'Booking Cancelled ❌', 'Your booking for Room 13 from February 09, 2026 to February 10, 2026 has been cancelled. If you have any questions, please contact us.', 'booking_cancelled', 1, '2026-02-04 15:36:52', '2026-02-04 15:37:40'),
(127, 15, 65, 'Booking Confirmed! ✅', 'Great news! Your booking for Room 13 from February 09, 2026 to February 10, 2026 has been confirmed. We look forward to welcoming you!', 'booking_confirmed', 1, '2026-02-04 15:37:19', '2026-02-04 15:37:40'),
(128, 15, 65, 'Stay Completed ?', 'Thank you for staying with us! Your booking for Room 13 has been completed. We hope you had a wonderful experience.', '', 1, '2026-02-04 15:40:22', '2026-02-04 15:40:37'),
(129, 15, 65, 'Stay Completed ????', 'Thank you for staying with us! Your booking for Room 10 has been completed. We hope you had a wonderful experience.', '', 1, '2026-02-04 15:40:22', '2026-02-04 15:40:37'),
(130, 15, 64, 'Stay Completed ?', 'Thank you for staying with us! Your booking for Room 16 has been completed. We hope you had a wonderful experience.', '', 1, '2026-02-04 15:40:25', '2026-02-04 15:40:37'),
(131, 15, 64, 'Stay Completed ????', 'Thank you for staying with us! Your booking for Room 1 has been completed. We hope you had a wonderful experience.', '', 1, '2026-02-04 15:40:25', '2026-02-04 15:40:37');

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
(13, 'Room 10', 6, '[\"room_69835fdb696984.19123526.jpg\"]', '2500', 'Free Wifi'),
(14, 'Room 3', 5, '[\"room_6971be6de54485.80918773.jpg\",\"room_6971be6de5a178.37352919.jpg\",\"room_6971be6de5e9a6.66284070.jpg\"]', '1500', 'Free Wifi'),
(16, 'Room 1', 5, '[\"room_69835e5db187b5.12197900.jpg\"]', '1500', 'Free Wifi');

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
(6, 'Triple Room', 'Triple Room');

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
(4, 1, '12:00:00', '00:00:00', '2026-01-24', '2026-01-25', NULL);

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
  `shift_type` varchar(50) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staffs`
--

INSERT INTO `staffs` (`staff_id`, `name`, `position`, `address`, `shift_type`, `phone_number`, `email`, `password`) VALUES
(1, 'Armando Raguindin', 'Technician', 'Roxas', 'Morning', '639685340012', 'armando@gmail.com', '202cb962ac59075b964b07152d234b70');

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
(12, 'admin', 'Rizal, Roxas', 'admin@gmail.com', '21232f297a57a5a743894a0e4a801fc3', 'admin', NULL),
(14, 'Armando Raguindin', 'Roxas, Isabela', 'raguindin.armando@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', 'user', '09360991034'),
(15, 'Mark Lester', 'Roxas, Isabela', 'raguindin.lester20@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', 'user', '09360991034'),
(16, 'Armando Raguindin', 'Roxas, Isabela', 'armando@gmail.com', 'e10adc3949ba59abbe56e057f20f883e', 'user', '09360991034');

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
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT for table `concerns`
--
ALTER TABLE `concerns`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

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
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=132;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `room_type`
--
ALTER TABLE `room_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `shifts`
--
ALTER TABLE `shifts`
  MODIFY `shift_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `special_offers`
--
ALTER TABLE `special_offers`
  MODIFY `offers_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `staffs`
--
ALTER TABLE `staffs`
  MODIFY `staff_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `table_reservations`
--
ALTER TABLE `table_reservations`
  MODIFY `reservation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

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
