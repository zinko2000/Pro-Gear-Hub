-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 23, 2025 at 10:01 PM
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
-- Database: `pro_gear_hub`
--

-- --------------------------------------------------------

--
-- Table structure for table `carts`
--

CREATE TABLE `carts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `carts`
--

INSERT INTO `carts` (`id`, `user_id`, `created_at`) VALUES
(1, 1, '2025-04-20 19:16:27'),
(2, 2, '2025-04-20 19:16:27'),
(3, 3, '2025-04-20 19:16:27'),
(4, 4, '2025-04-20 19:16:27'),
(5, 5, '2025-04-20 19:16:27'),
(6, 6, '2025-04-22 20:54:50'),
(7, 7, '2025-04-22 21:20:20'),
(8, 8, '2025-04-23 09:21:10');

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

CREATE TABLE `cart_items` (
  `id` int(11) NOT NULL,
  `cart_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart_items`
--

INSERT INTO `cart_items` (`id`, `cart_id`, `product_id`, `quantity`, `added_at`) VALUES
(1, 1, 3, 1, '2025-04-20 19:16:27'),
(2, 1, 5, 2, '2025-04-20 19:16:27'),
(3, 2, 2, 1, '2025-04-20 19:16:27'),
(4, 2, 7, 1, '2025-04-20 19:16:27'),
(5, 3, 6, 1, '2025-04-20 19:16:27'),
(6, 4, 10, 1, '2025-04-20 19:16:27'),
(7, 4, 4, 1, '2025-04-20 19:16:27'),
(8, 5, 1, 1, '2025-04-20 19:16:27'),
(9, 5, 12, 1, '2025-04-20 19:16:27');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','processing','shipped','delivered','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `NAME` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `original_price` decimal(10,2) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `rating` decimal(3,1) DEFAULT NULL,
  `review_count` int(11) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `is_new` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `NAME`, `description`, `price`, `original_price`, `category`, `rating`, `review_count`, `image_path`, `is_new`, `created_at`) VALUES
(1, 'NELEUS Dry Fit', 'Breathable dry fit workout shirt', 29.89, 59.99, 'clothing', 4.5, 128, 'images/NELEUS Dry Fit1.jfif', 1, '2025-04-20 16:22:06'),
(2, 'MOHUACHI Running Shoes', 'Lightweight running shoes', 22.92, 59.99, 'fitness', 4.0, 86, 'images/MOHUACHI Running Shoes.jpg', 0, '2025-04-20 16:22:06'),
(3, 'NELEUS Dry Fit', 'Breathable dry fit workout shirt', 29.89, 59.99, 'clothing', 4.5, 128, 'images/NELEUS Dry Fit2.jfif', 1, '2025-04-20 18:51:52'),
(4, 'MOHUACHI Running Shoes', 'Lightweight running shoes', 22.92, 59.99, 'fitness', 4.0, 86, 'images/MOHUACHI Running Shoes1.jpg', 0, '2025-04-20 18:51:52'),
(5, 'NELEUS Dry Fit Shirt', 'Moisture-wicking performance shirt with UPF 50+ sun protection. 92% polyester, 8% spandex.', 29.89, 59.99, 'clothing', 4.5, 128, 'images/NELEUS Dry Fit.jpeg', 1, '2025-04-20 19:16:27'),
(6, 'MOHUACHI Running Shoes', 'Lightweight mesh running shoes with cushioned soles. Weight: 8.4oz per shoe.', 22.92, 59.99, 'fitness', 4.0, 86, 'images/MOHUACHI Running Shoes2.webp', 0, '2025-04-20 19:16:27'),
(7, 'ProGear Weightlifting Gloves', 'Padded palms with wrist support. Breathable mesh back. Sizes: S-XXL.', 24.99, 39.99, 'accessories', 4.2, 215, 'images/Weightlifting Gloves.jfif', 1, '2025-04-20 19:16:27'),
(8, 'FlexFit Resistance Bands Set', '5-band set (5-50lbs) with door anchor, ankle straps, and workout guide.', 34.95, 49.99, 'fitness', 4.7, 342, 'images/FlexFit Resistance Bands Set.jpg', 1, '2025-04-20 19:16:27'),
(9, 'HydroCore Water Bottle', '1L insulated stainless steel with time markers. BPA-free.', 19.99, 29.99, 'accessories', 4.8, 178, 'images/HydroCore Water Bottle.jpg', 0, '2025-04-20 19:16:27'),
(10, 'YogaMaster Pro Mat', '6mm thick non-slip mat with carrying strap. 72\" x 24\".', 45.50, 69.99, 'fitness', 4.6, 92, 'images/YogaMaster Pro Mat.webp', 1, '2025-04-20 19:16:27'),
(11, 'JumpRope Elite', 'Weighted speed rope with ball bearings. Adjustable length.', 14.99, 24.99, 'fitness', 4.3, 67, 'images/JumpRope Elite.webp', 0, '2025-04-20 19:16:27'),
(12, 'Compression Arm Sleeves', 'Pair of breathable sleeves for muscle support. UPF 50+.', 18.95, 34.99, 'clothing', 4.1, 143, 'images/Compression Arm Sleeves.jpg', 1, '2025-04-20 19:16:27'),
(13, 'Foam Roller Pro', 'High-density EPP foam. 12\" length with textured surface.', 22.99, 39.99, 'accessories', 4.4, 87, 'images/Foam Roller Pro.webp', 0, '2025-04-20 19:16:27'),
(14, 'Lifting Belt Premium', '6\" wide genuine leather belt with steel buckle. Sizes 28-48\".', 59.99, 89.99, 'accessories', 4.9, 204, 'images/Lifting Belt Premium.jfif', 1, '2025-04-20 19:16:27'),
(15, 'Training Hoodie', 'Performance fabric hoodie with thumbholes. 80% cotton, 20% polyester.', 39.99, 65.00, 'clothing', 4.3, 156, 'images/Training Hoodie.jpg', 0, '2025-04-20 19:16:27'),
(16, 'Gym Duffle Bag', '35L capacity with shoe compartment and wet pocket. Water-resistant.', 49.95, 79.99, 'accessories', 4.5, 112, 'images/Gym Duffle Bag.webp', 1, '2025-04-20 19:16:27');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `PASSWORD` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_admin` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `PASSWORD`, `created_at`, `is_admin`) VALUES
(1, 'fitness_guru', 'guru@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-04-20 19:16:27', 0),
(2, 'gym_rat', 'gymrat@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-04-20 19:16:27', 0),
(3, 'yoga_lover', 'yoga@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-04-20 19:16:27', 0),
(4, 'weightlifter', 'lift@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-04-20 19:16:27', 0),
(5, 'runner123', 'run@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '2025-04-20 19:16:27', 0),
(6, 'Zin', 'Zin@gmail.com', '$2y$10$oDrGAPGymppWGbYgvgQvYeoiMIO5GRPqKhLKYnAKrHNKJ3lEuuJ7O', '2025-04-22 20:54:50', 1),
(7, 'kaung', 'kaung@gmail.com', '$2y$10$2Msv/3Rc7jYq/kRGcmts7unk16rcWSdhpakt315vynCwO76OKfsiG', '2025-04-22 21:20:20', 0),
(8, 'Zin Ko Aung', 'zinkoaung@gmail.com', '$2y$10$FltjHAXUOVfT27JP7mt5xOXlva/HduLSfQMMCkwonppVDoCs4fimG', '2025-04-23 09:21:10', 0),
(9, 'john_doe', 'john@example.com', '$2y$10$somehashedpassword', '2025-04-23 20:00:15', 0),
(10, 'jane_smith', 'jane@example.com', '$2y$10$somehashedpassword', '2025-04-23 20:00:15', 0),
(11, 'mike_jones', 'mike@example.com', '$2y$10$somehashedpassword', '2025-04-23 20:00:15', 0),
(12, 'sarah_wilson', 'sarah@example.com', '$2y$10$somehashedpassword', '2025-04-23 20:00:15', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `carts`
--
ALTER TABLE `carts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cart_id` (`cart_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `carts`
--
ALTER TABLE `carts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `carts`
--
ALTER TABLE `carts`
  ADD CONSTRAINT `carts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_ibfk_1` FOREIGN KEY (`cart_id`) REFERENCES `carts` (`id`),
  ADD CONSTRAINT `cart_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
