-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 17, 2025 at 12:50 PM
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
  `status` enum('pending','confirmed','canceled','completed') DEFAULT 'pending',
  `payment_status` enum('unpaid','partially_paid','paid') DEFAULT 'unpaid',
  `special_requests` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`booking_id`, `user_id`, `room_id`, `check_in_date`, `check_out_date`, `total_price`, `status`, `payment_status`, `special_requests`, `created_at`, `updated_at`) VALUES
(6, 6, 6, '2025-05-17', '2025-05-18', 1500.00, 'completed', '', '', '2025-05-17 09:00:19', '2025-05-17 09:13:17'),
(7, 6, 8, '2025-05-17', '2025-05-18', 2500.00, 'completed', '', '', '2025-05-17 09:00:24', '2025-05-17 09:17:14'),
(8, 6, 7, '2025-05-17', '2025-05-18', 1500.00, 'completed', '', '', '2025-05-17 09:00:29', '2025-05-17 09:27:04'),
(9, 6, 5, '2025-05-17', '2025-05-18', 1500.00, 'completed', '', '', '2025-05-17 09:00:33', '2025-05-17 09:17:16');

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
(1, 'Our objective at Romancy is to bring together our visitor\'s activities and spirits with our own, communicating enthusiasm and sincerity in the food we share. Official Chef and Owner Philippe Massoud expertly creates a blend of Lebanese, Levantine, Mediterranean influenced food divided in each New York morning. Delightful herbs and flavors, connected to Nature\'s parity and ancient arabic potions.');

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
(2, 4, 'Exceptional service and amazing food!', 5, '2025-05-17 10:01:06'),
(4, 6, 'My new favorite restaurant!', 4, '2025-05-17 10:07:13');

-- --------------------------------------------------------

--
-- Table structure for table `rooms`
--

CREATE TABLE `rooms` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `room_type_id` int(11) NOT NULL,
  `images` varchar(255) DEFAULT NULL,
  `price` varchar(250) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rooms`
--

INSERT INTO `rooms` (`id`, `title`, `room_type_id`, `images`, `price`) VALUES
(5, 'Room 2', 5, '[\"room_6815e9a3bc7b74.38249271.jpg\",\"room_6815e9a3bcd876.43427399.jpg\"]', '1500'),
(6, 'Room 1', 5, '[\"room_6815ea106c7152.73864788.jpg\",\"room_6815ea106cb153.70063731.jpg\"]', '1500'),
(7, 'Room 3', 5, '[\"room_68172f9bb476f1.95935670.jpg\",\"room_68172f9bb4d1c2.96168563.jpg\",\"room_68172f9bb50ae2.42324066.jpg\"]', '1500'),
(8, 'Room 4', 6, '[\"room_681f765d1e5a40.24006654.jpg\",\"room_681f765d1ecdf7.90503933.jpg\",\"room_681f765d1f3272.17751895.jpg\"]', '2500');

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
(14, 2, '2025-05-17', '07:00:00', 4, '', 6, 'done');

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
(3, 'Mark Lester Raguindin', 'Cauayan', 'suguitanmark123@gmail.com', '202cb962ac59075b964b07152d234b70', 'admin', '639360991034'),
(4, 'Jean Dominque Bulusan', 'Rizal', 'jean@gmail.com', '202cb962ac59075b964b07152d234b70', 'user', '09360991034'),
(5, 'Mark Lester', 'Roxas, Isabela', 'suguitanmark123@gmail.com', '81dc9bdb52d04dc20036dbd8313ed055', 'user', '09360991034'),
(6, 'Mark Lester', 'Roxas, Isabela', 'raguindin.lester20@gmail.com', '202cb962ac59075b964b07152d234b70', 'user', '09360991034');

--
-- Indexes for dumped tables
--

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
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `banner`
--
ALTER TABLE `banner`
  MODIFY `banner_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

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
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `rooms`
--
ALTER TABLE `rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `room_type`
--
ALTER TABLE `room_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `shifts`
--
ALTER TABLE `shifts`
  MODIFY `shift_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `special_offers`
--
ALTER TABLE `special_offers`
  MODIFY `offers_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `staffs`
--
ALTER TABLE `staffs`
  MODIFY `staff_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `table_reservations`
--
ALTER TABLE `table_reservations`
  MODIFY `reservation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
