-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Dec 03, 2025 at 09:09 PM
-- Server version: 8.0.43-0ubuntu0.22.04.2
-- PHP Version: 8.3.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `computer_zone`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

CREATE TABLE `cart_items` (
  `id` int NOT NULL,
  `session_id` varchar(255) DEFAULT NULL,
  `product_id` int NOT NULL,
  `quantity` int NOT NULL DEFAULT '1',
  `added_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `cart_items`
--

INSERT INTO `cart_items` (`id`, `session_id`, `product_id`, `quantity`, `added_at`) VALUES
(68, 'd4089e9d3ef506119fd6e9e10b46b9f8', 51, 1, '2025-12-03 20:12:23'),
(73, '5f69396ef42d39372fdacce794758900', 52, 1, '2025-12-03 20:48:13');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`) VALUES
(12, 'Desktops', 'desktops'),
(13, 'Laptops', 'laptops'),
(14, 'Mobiles', 'mobiles'),
(15, 'Headphones', 'headphones');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `customer_name` varchar(255) DEFAULT NULL,
  `customer_email` varchar(255) DEFAULT NULL,
  `shipping_address` text,
  `shipping_city` varchar(100) DEFAULT NULL,
  `shipping_postal_code` varchar(20) DEFAULT NULL,
  `shipping_country` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `status` varchar(50) DEFAULT 'pending',
  `payment_method` varchar(50) DEFAULT 'cash_on_delivery',
  `order_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `customer_name`, `customer_email`, `shipping_address`, `shipping_city`, `shipping_postal_code`, `shipping_country`, `phone`, `total_price`, `status`, `payment_method`, `order_date`) VALUES
(16, 1, 'Tabish Hassan', 'thassan@algomau.ca', 'ABC', 'ABC City', '789', 'CA', '987', 59.40, 'pending', 'cod', '2025-12-03 17:22:06'),
(17, 3, 'T. Hassan', 'thassan@mail.com', 'ABC', 'Toronto', '987', 'CA', '789', 59.40, 'pending', 'cod', '2025-12-03 20:22:18'),
(18, 3, 'Sam', 'Sam@mail.com', 'ABC', 'ABC', '987', 'CA', '789', 59.40, 'pending', 'cod', '2025-12-03 20:25:53'),
(19, 1, 'Tabish Hassan', 'thassan@mail.com', 'Circular Road', 'Toronto', '987', 'CA', '789', 976.32, 'pending', 'cod', '2025-12-03 20:30:57'),
(20, NULL, 'Tabish', 'thassan@algomau.ca', 'Test', 'Test', '999', 'CA', '91', 1030.32, 'pending', 'cod', '2025-12-03 20:55:31'),
(21, 6, 'Tabish', 'tabish.hassan@mailcom', 'ABC', 'ABC', '981', 'CA', '1', 825.12, 'pending', 'cod', '2025-12-03 20:59:36');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int NOT NULL,
  `order_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` int NOT NULL,
  `unit_price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `unit_price`) VALUES
(12, 16, 51, 1, 50.00),
(13, 17, 51, 1, 50.00),
(14, 18, 51, 1, 50.00),
(15, 19, 52, 1, 899.00),
(16, 20, 51, 1, 50.00),
(17, 20, 52, 1, 899.00),
(18, 21, 53, 1, 759.00);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text,
  `price` decimal(10,2) NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `category_id` int DEFAULT NULL,
  `stock` int DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `isDeleted` tinyint(1) DEFAULT '0',
  `slug` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `image_url`, `category_id`, `stock`, `created_at`, `deleted_at`, `isDeleted`, `slug`, `updated_at`) VALUES
(51, 'iPhone', 'Experience smooth performance with iPhone, featuring modern technology and durable build quality designed to last.', 50.00, '/uploads/products/prod_6930707bba9a08.66699155.webp', 14, 6, '2025-12-03 17:16:43', NULL, 0, 'iphone', '2025-12-03 17:16:43'),
(52, 'MacBook Pro', 'With premium build quality and enhanced speed, MacBook Pro stands out as a powerful option in the category.', 899.00, '/uploads/products/prod_69309dd28d5235.77091391.webp', 13, 8, '2025-12-03 20:30:10', NULL, 0, 'macbook-pro', '2025-12-03 20:30:10'),
(53, 'Samsung', 'The Samsung is engineered for exceptional performance, offering unmatched value in the category. Designed for professionals and casual users alike.', 759.00, '/uploads/products/prod_6930a41545b808.16139776.webp', 14, 4, '2025-12-03 20:56:53', NULL, 0, 'samsung', '2025-12-03 20:56:53'),
(54, 'Test', 'Test combines speed, efficiency, and affordability. Ideal for multitasking, gaming, or productivity at a competitive price of 10.', 10.00, '/uploads/products/prod_6930a43e907f19.29045660.webp', 15, 50, '2025-12-03 20:57:34', '2025-12-03 20:57:57', 0, 'test', '2025-12-03 20:57:51');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `is_admin` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `is_admin`, `created_at`) VALUES
(1, 'Tabish Hassan', 'thassan@algomau.ca', '$2y$10$48nsYZ6.9zHPji1nU2Khh.udyfBp9Qn7lXR8mPlnPoV5K2ndMoIqq', 1, '2025-11-29 14:48:09'),
(3, 'Hassan', 'hassan@algomau.ca', '$2y$10$48nsYZ6.9zHPji1nU2Khh.udyfBp9Qn7lXR8mPlnPoV5K2ndMoIqq', 0, '2025-12-03 19:47:29'),
(5, 'Sam', 'sample@mail.com', '$2y$10$9g0.JOVo/SuaxONdk4ayR.57ODogG3hJMk40gkQZPyni6.Krb41dy', 0, '2025-12-03 20:37:17'),
(6, 'T. Hassan', 'tabish.hassan@mailcom', '$2y$10$Bs4olh3sJ479QkGuEcr2Nu49o6OtQHxRbwyQwAVEtJyVXJ79Q3gnm', 0, '2025-12-03 20:58:40');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_session_cart_item` (`session_id`,`product_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `idx_carts_session` (`session_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_orders_user` (`user_id`);

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
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_products_category` (`category_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=79;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE RESTRICT;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
