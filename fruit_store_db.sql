-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 07, 2026 at 07:09 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `fruit_store_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `icon` varchar(50) DEFAULT 'fa-tag'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `created_at`, `icon`) VALUES
(7, 'Fresh Apples', 'Sweet, crispy and juicy apples from Kashmir. Rich in fiber and antioxidants.', '2026-05-01 08:39:04', 'fa-apple'),
(8, 'Organic Bananas', 'Ripe and sweet organic bananas. Great for energy and potassium boost.', '2026-05-01 08:39:04', 'fa-banana'),
(9, 'Juicy Oranges', 'Fresh and tangy oranges packed with Vitamin C. Perfect for juice.', '2026-05-01 08:39:04', 'fa-citrus'),
(10, 'Sweet Mangoes', 'King of fruits! Sweet, aromatic and delicious mangoes.', '2026-05-01 08:39:04', 'fa-mango'),
(11, 'Red Strawberries', 'Fresh and sweet strawberries. Perfect for desserts and smoothies.', '2026-05-01 08:39:04', 'fa-strawberry'),
(12, 'Green Grapes', 'Seedless green grapes. Sweet and refreshing taste.', '2026-05-01 08:39:04', 'fa-grapes'),
(13, 'Fresh Lemons', 'Sour and fresh lemons. Great for juice and cooking.', '2026-05-01 08:39:04', 'fa-lemon'),
(14, 'Ripe Papayas', 'Sweet and tropical papayas. Rich in vitamins and enzymes.', '2026-05-01 08:39:04', 'fa-seedling'),
(15, 'Fresh Pineapples', 'Sweet and tangy pineapples. Perfect for juice and fruit salads.', '2026-05-01 08:39:04', 'fa-apple-alt'),
(16, 'Watermelons', 'Large and sweet watermelons. Perfect for summer.', '2026-05-01 08:39:04', 'fa-melon'),
(17, 'Fresh Pears', 'Sweet and juicy pears. Great for snacks and desserts.', '2026-05-01 08:39:04', 'fa-apple-alt'),
(18, 'Red Pomegranates', 'Fresh pomegranates with ruby red seeds. Rich in antioxidants.', '2026-05-01 08:39:04', 'fa-seedling'),
(19, 'Sweet Lychees', 'Aromatic and sweet lychees. A tropical delight.', '2026-05-01 08:39:04', 'fa-apple-alt'),
(20, 'Fresh Guavas', 'Sweet and fragrant guavas. Rich in Vitamin C.', '2026-05-01 08:39:04', 'fa-seedling'),
(21, 'Ripe Avocados', 'Creamy and nutritious avocados. Great for salads and sandwiches.', '2026-05-01 08:39:04', 'fa-carrot');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `name`, `phone`, `email`, `address`, `created_at`) VALUES
(1, 'Nimal Perera', '0712345678', 'kamil@gmail.com', 'Kandy, Sri Lanka', '2026-04-24 05:15:02'),
(2, 'Kamal Silva', '0755555555', 'kamal@gmail.com', 'Peradeniya, Kandy', '2026-04-24 05:15:02'),
(4, 'tisha', '0789678543', 'tishas.45@gimail.com', 'peradeniya ', '2026-05-01 07:59:18'),
(5, 'Ana', '0783632699', 'Anaperera.4@gimail.com', 'kanady', '2026-05-25 16:48:17'),
(6, 'imalka', '0783632699', 'imalkasamarakoon.2@gimail.com', 'peradeniya', '2026-06-03 11:10:50'),
(7, 'tharushika', '0784328791', 'tharushila.@gimail.com', 'kandy', '2026-06-03 11:14:34'),
(8, 'nayomi', '0713637890', 'nayomi2.@gmail.com', 'peradeniya', '2026-07-02 16:24:49');

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `id` int(11) NOT NULL,
  `invoice_no` varchar(20) NOT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `invoice_date` date NOT NULL,
  `subtotal` decimal(10,2) DEFAULT 0.00,
  `tax` decimal(10,2) DEFAULT 0.00,
  `total` decimal(10,2) DEFAULT 0.00,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `invoices`
--

INSERT INTO `invoices` (`id`, `invoice_no`, `customer_id`, `invoice_date`, `subtotal`, `tax`, `total`, `created_by`, `created_at`) VALUES
(1, 'INV-20260424-1699', NULL, '2026-04-24', 0.00, 0.00, 240.00, 1, '2026-04-24 07:15:31'),
(2, 'INV-20260424-1749', NULL, '2026-04-24', 0.00, 0.00, 240.00, 1, '2026-04-24 07:15:33'),
(3, 'INV-20260424-2807', NULL, '2026-04-24', 0.00, 0.00, 240.00, 1, '2026-04-24 07:15:34'),
(4, 'INV-20260424-6164', NULL, '2026-04-24', 0.00, 0.00, 240.00, 1, '2026-04-24 07:15:35'),
(5, 'INV-20260424-2194', 1, '2026-06-24', 0.00, 0.00, 2240.00, 1, '2026-04-24 07:18:24'),
(6, 'INV-20260424-5805', 1, '2026-06-24', 0.00, 0.00, 2240.00, 1, '2026-04-24 07:18:26'),
(7, 'INV-20260603-7535', 6, '2026-06-03', 0.00, 0.00, 400.00, 5, '2026-06-03 11:12:02'),
(8, 'INV-20260603-8441', 6, '2026-06-03', 0.00, 0.00, 400.00, 5, '2026-06-03 11:12:04'),
(9, 'INV-20260603-3817', 6, '2026-06-03', 0.00, 0.00, 400.00, 5, '2026-06-03 11:12:05'),
(10, 'INV-20260603-4663', 6, '2026-06-03', 0.00, 0.00, 400.00, 5, '2026-06-03 11:12:05'),
(12, 'INV-20260603-6592', 7, '2026-06-03', 0.00, 0.00, 1150.00, 5, '2026-06-03 11:15:31'),
(13, 'INV-20260603-8369', 2, '2026-06-03', 0.00, 0.00, 1300.00, 5, '2026-06-03 11:17:00'),
(14, 'INV-20260603-6231', 1, '2026-06-03', 0.00, 0.00, 2350.00, 5, '2026-06-03 11:21:51'),
(15, 'INV-20260603-6632', 1, '2026-06-03', 0.00, 0.00, 2350.00, 5, '2026-06-03 11:21:53'),
(16, 'INV-20260603-6044', 1, '2026-06-03', 0.00, 0.00, 2350.00, 5, '2026-06-03 11:21:54'),
(17, 'INV-20260603-3630', 5, '2026-06-03', 0.00, 0.00, 12350.00, 1, '2026-06-03 17:14:56'),
(18, 'INV-20260603-9831', 5, '2026-06-03', 0.00, 0.00, 12350.00, 1, '2026-06-03 17:14:58'),
(19, 'INV-20260604-4682', 5, '2026-06-04', 0.00, 0.00, 13150.00, 1, '2026-06-04 05:38:49'),
(20, 'INV-20260604-3050', 5, '2026-06-04', 0.00, 0.00, 13150.00, 1, '2026-06-04 05:38:53'),
(21, 'INV-20260604-6036', 5, '2026-06-04', 0.00, 0.00, 13150.00, 1, '2026-06-04 05:38:55'),
(22, 'INV-20260604-5379', 5, '2026-06-04', 0.00, 0.00, 13150.00, 1, '2026-06-04 05:39:03'),
(23, 'INV-20260604-9494', 5, '2026-06-04', 0.00, 0.00, 13150.00, 1, '2026-06-04 05:39:06'),
(24, 'INV-20260702-001', 1, '2026-07-02', 250.00, 0.00, 250.00, NULL, '2026-07-02 06:52:27'),
(26, 'INV-20260702-003', 1, '2026-07-02', 150.00, 0.00, 150.00, NULL, '2026-07-02 07:08:57');

-- --------------------------------------------------------

--
-- Table structure for table `invoice_items`
--

CREATE TABLE `invoice_items` (
  `id` int(11) NOT NULL,
  `invoice_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` decimal(10,2) DEFAULT 1.00,
  `price` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `invoice_items`
--

INSERT INTO `invoice_items` (`id`, `invoice_id`, `product_id`, `quantity`, `price`, `total`) VALUES
(1, 1, 4, 8.00, 30.00, 240.00),
(2, 2, 4, 8.00, 30.00, 240.00),
(3, 3, 4, 8.00, 30.00, 240.00),
(4, 4, 4, 8.00, 30.00, 240.00),
(5, 5, 4, 8.00, 30.00, 240.00),
(6, 5, 9, 2.00, 250.00, 500.00),
(7, 5, 9, 6.00, 250.00, 1500.00),
(8, 6, 4, 8.00, 30.00, 240.00),
(9, 6, 9, 2.00, 250.00, 500.00),
(10, 6, 9, 6.00, 250.00, 1500.00),
(11, 7, 17, 5.00, 80.00, 400.00),
(12, 8, 17, 5.00, 80.00, 400.00),
(13, 9, 17, 5.00, 80.00, 400.00),
(14, 10, 17, 5.00, 80.00, 400.00),
(16, 12, 17, 5.00, 80.00, 400.00),
(17, 12, 15, 3.00, 250.00, 750.00),
(18, 13, 17, 5.00, 80.00, 400.00),
(19, 13, 15, 3.00, 250.00, 750.00),
(20, 13, 4, 5.00, 30.00, 150.00),
(21, 14, 17, 5.00, 80.00, 400.00),
(22, 14, 15, 3.00, 250.00, 750.00),
(23, 14, 4, 5.00, 30.00, 150.00),
(24, 14, 16, 5.00, 60.00, 300.00),
(25, 14, 15, 3.00, 250.00, 750.00),
(26, 15, 17, 5.00, 80.00, 400.00),
(27, 15, 15, 3.00, 250.00, 750.00),
(28, 15, 4, 5.00, 30.00, 150.00),
(29, 15, 16, 5.00, 60.00, 300.00),
(30, 15, 15, 3.00, 250.00, 750.00),
(31, 16, 17, 5.00, 80.00, 400.00),
(32, 16, 15, 3.00, 250.00, 750.00),
(33, 16, 4, 5.00, 30.00, 150.00),
(34, 16, 16, 5.00, 60.00, 300.00),
(35, 16, 15, 3.00, 250.00, 750.00),
(36, 17, 17, 5.00, 80.00, 400.00),
(37, 17, 15, 3.00, 250.00, 750.00),
(38, 17, 4, 5.00, 30.00, 150.00),
(39, 17, 16, 5.00, 60.00, 300.00),
(40, 17, 15, 3.00, 250.00, 750.00),
(41, 17, 20, 10.00, 1000.00, 10000.00),
(42, 18, 17, 5.00, 80.00, 400.00),
(43, 18, 15, 3.00, 250.00, 750.00),
(44, 18, 4, 5.00, 30.00, 150.00),
(45, 18, 16, 5.00, 60.00, 300.00),
(46, 18, 15, 3.00, 250.00, 750.00),
(47, 18, 20, 10.00, 1000.00, 10000.00),
(48, 19, 17, 5.00, 80.00, 400.00),
(49, 19, 15, 3.00, 250.00, 750.00),
(50, 19, 4, 5.00, 30.00, 150.00),
(51, 19, 16, 5.00, 60.00, 300.00),
(52, 19, 15, 3.00, 250.00, 750.00),
(53, 19, 20, 10.00, 1000.00, 10000.00),
(54, 19, 17, 10.00, 80.00, 800.00),
(55, 20, 17, 5.00, 80.00, 400.00),
(56, 20, 15, 3.00, 250.00, 750.00),
(57, 20, 4, 5.00, 30.00, 150.00),
(58, 20, 16, 5.00, 60.00, 300.00),
(59, 20, 15, 3.00, 250.00, 750.00),
(60, 20, 20, 10.00, 1000.00, 10000.00),
(61, 20, 17, 10.00, 80.00, 800.00),
(62, 21, 17, 5.00, 80.00, 400.00),
(63, 21, 15, 3.00, 250.00, 750.00),
(64, 21, 4, 5.00, 30.00, 150.00),
(65, 21, 16, 5.00, 60.00, 300.00),
(66, 21, 15, 3.00, 250.00, 750.00),
(67, 21, 20, 10.00, 1000.00, 10000.00),
(68, 21, 17, 10.00, 80.00, 800.00),
(69, 22, 17, 5.00, 80.00, 400.00),
(70, 22, 15, 3.00, 250.00, 750.00),
(71, 22, 4, 5.00, 30.00, 150.00),
(72, 22, 16, 5.00, 60.00, 300.00),
(73, 22, 15, 3.00, 250.00, 750.00),
(74, 22, 20, 10.00, 1000.00, 10000.00),
(75, 22, 17, 10.00, 80.00, 800.00),
(76, 23, 17, 5.00, 80.00, 400.00),
(77, 23, 15, 3.00, 250.00, 750.00),
(78, 23, 4, 5.00, 30.00, 150.00),
(79, 23, 16, 5.00, 60.00, 300.00),
(80, 23, 15, 3.00, 250.00, 750.00),
(81, 23, 20, 10.00, 1000.00, 10000.00),
(82, 23, 17, 10.00, 80.00, 800.00),
(85, 26, 4, 0.00, 30.00, 30.00),
(86, 26, 17, 0.00, 120.00, 120.00);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT 0.00,
  `stock` decimal(10,2) DEFAULT 0.00,
  `expiry_date` date DEFAULT NULL,
  `min_stock_level` int(11) DEFAULT 10,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `product_image` varchar(255) DEFAULT 'default-fruit.png',
  `store_id` int(11) DEFAULT 1,
  `barcode` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `category_id`, `price`, `stock`, `expiry_date`, `min_stock_level`, `created_at`, `product_image`, `store_id`, `barcode`) VALUES
(4, 'Orange', NULL, 30.00, 0.00, '2026-05-06', 20, '2026-04-24 05:15:02', 'default-fruit.png', 1, NULL),
(9, 'Dried Apples', NULL, 250.00, 9.00, '2026-05-24', 10, '2026-04-24 05:15:02', 'default-fruit.png', 1, NULL),
(14, 'watwermelon', 16, 180.00, 20.00, NULL, 10, '2026-06-03 05:14:36', 'default-fruit.png', 1, NULL),
(15, 'Dried Apples', 7, 250.00, 34.00, NULL, 10, '2026-06-03 11:04:24', 'default-fruit.png', 1, NULL),
(16, 'Fresh Lemons', 13, 60.00, 40.00, NULL, 10, '2026-06-03 11:06:51', 'default-fruit.png', 1, NULL),
(17, 'Mangoes', 10, 80.00, 0.00, NULL, 10, '2026-06-03 11:09:09', 'default-fruit.png', 1, NULL),
(18, 'Fresh pineapples', 15, 180.00, 80.00, NULL, 10, '2026-06-03 17:10:12', 'default-fruit.png', 1, NULL),
(19, 'Ripe papaya', 21, 300.00, 79.00, NULL, 10, '2026-06-03 17:11:47', 'default-fruit.png', 1, NULL),
(20, 'Red Strawberries', 11, 1000.00, 30.00, NULL, 10, '2026-06-03 17:12:52', 'default-fruit.png', 1, NULL),
(21, 'Organic Banana', 8, 180.00, 90.00, NULL, 10, '2026-06-03 17:14:12', 'default-fruit.png', 1, NULL),
(72, 'Organic Banana', 8, 180.00, 90.00, NULL, 10, '2026-07-02 16:11:27', 'default-fruit.png', 1, NULL),
(73, 'Red Strawberries', 11, 1000.00, 30.00, NULL, 10, '2026-07-02 16:11:27', 'default-fruit.png', 1, NULL),
(74, 'Ripe Papaya', 14, 300.00, 79.00, NULL, 10, '2026-07-02 16:11:27', 'default-fruit.png', 1, NULL),
(75, 'Fresh Pineapples', 15, 180.00, 80.00, NULL, 10, '2026-07-02 16:11:27', 'default-fruit.png', 1, NULL),
(76, 'Sweet Mangoes', 10, 190.00, 45.00, NULL, 10, '2026-07-02 16:11:27', 'default-fruit.png', 1, NULL),
(77, 'Fresh Apples', 7, 100.00, 60.00, NULL, 10, '2026-07-02 16:11:27', 'default-fruit.png', 1, NULL),
(78, 'Juicy Oranges', 9, 30.00, 8.00, NULL, 10, '2026-07-02 16:11:27', 'default-fruit.png', 1, NULL),
(79, 'Green Grapes', 12, 80.00, 15.00, NULL, 10, '2026-07-02 16:11:27', 'default-fruit.png', 1, NULL),
(80, 'Fresh Lemons', 13, 25.00, 20.00, NULL, 10, '2026-07-02 16:11:27', 'default-fruit.png', 1, NULL),
(81, 'Watermelon', 16, 60.00, 10.00, NULL, 10, '2026-07-02 16:11:27', 'default-fruit.png', 1, NULL),
(82, 'Kiwi', 16, 90.00, 8.00, NULL, 10, '2026-07-02 16:11:27', 'default-fruit.png', 1, NULL),
(83, 'Pomegranate', 18, 110.00, 4.00, NULL, 10, '2026-07-02 16:11:27', 'default-fruit.png', 1, NULL),
(84, 'Avocado', 21, 120.00, 12.00, NULL, 10, '2026-07-02 16:11:27', 'default-fruit.png', 1, NULL),
(85, 'Lychee', 19, 150.00, 6.00, NULL, 10, '2026-07-02 16:11:27', 'default-fruit.png', 1, NULL),
(86, 'Guava', 20, 70.00, 15.00, NULL, 10, '2026-07-02 16:11:27', 'default-fruit.png', 1, NULL),
(87, 'Banana', 8, 45.00, 30.00, NULL, 10, '2026-07-02 16:11:27', 'default-fruit.png', 1, NULL),
(88, 'Pear', 17, 85.00, 10.00, NULL, 10, '2026-07-02 16:11:27', 'default-fruit.png', 1, NULL),
(89, 'Plum', 17, 95.00, 7.00, NULL, 10, '2026-07-02 16:11:27', 'default-fruit.png', 1, NULL),
(90, 'Peach', 17, 110.00, 5.00, NULL, 10, '2026-07-02 16:11:27', 'default-fruit.png', 1, NULL),
(91, 'Cherry', 11, 200.00, 3.00, NULL, 10, '2026-07-02 16:11:27', 'default-fruit.png', 1, NULL);

--
-- Triggers `products`
--
DELIMITER $$
CREATE TRIGGER `check_low_stock` AFTER UPDATE ON `products` FOR EACH ROW BEGIN
    IF NEW.stock <= NEW.min_stock_level AND NEW.stock > 0 THEN
        INSERT INTO stock_alerts (product_id, alert_type, message)
        VALUES (NEW.id, 'low_stock', CONCAT('Product ', NEW.name, ' is low on stock. Only ', NEW.stock, ' items left.'));
    END IF;
    
    IF NEW.stock <= 0 THEN
        INSERT INTO stock_alerts (product_id, alert_type, message)
        VALUES (NEW.id, 'low_stock', CONCAT('Product ', NEW.name, ' is out of stock!'));
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `stock_alerts`
--

CREATE TABLE `stock_alerts` (
  `id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `alert_type` enum('low_stock','expiry') NOT NULL,
  `message` text DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stock_alerts`
--

INSERT INTO `stock_alerts` (`id`, `product_id`, `alert_type`, `message`, `is_read`, `created_at`) VALUES
(1, 4, 'low_stock', 'Product Orange is low on stock. Only 18 items left.', 0, '2026-04-24 07:15:35'),
(2, 4, 'low_stock', 'Product Orange is low on stock. Only 10 items left.', 0, '2026-04-24 07:18:24'),
(3, 4, 'low_stock', 'Product Orange is low on stock. Only 2 items left.', 0, '2026-04-24 07:18:26'),
(4, 9, 'low_stock', 'Product Dried Apples is low on stock. Only 10 items left.', 0, '2026-04-24 07:18:26'),
(5, 9, 'low_stock', 'Product Dried Apples is low on stock. Only 4 items left.', 0, '2026-04-24 07:18:26'),
(6, 9, 'low_stock', 'Product Dried Apples is low on stock. Only 6 items left.', 0, '2026-05-01 08:13:15'),
(7, 4, 'low_stock', 'Product Orange is low on stock. Only 8 items left.', 0, '2026-05-01 08:13:26'),
(8, 9, 'low_stock', 'Product Dried Apples is low on stock. Only 9 items left.', 0, '2026-05-01 11:47:51'),
(9, 9, 'low_stock', 'Product Dried Apples is low on stock. Only 10 items left.', 0, '2026-05-01 12:34:25'),
(10, 4, 'low_stock', 'Product Orange is low on stock. Only 3 items left.', 0, '2026-06-03 11:17:00'),
(11, 4, 'low_stock', 'Product Orange is out of stock!', 0, '2026-06-03 11:21:51'),
(12, 4, 'low_stock', 'Product Orange is out of stock!', 0, '2026-06-03 11:21:53'),
(13, 4, 'low_stock', 'Product Orange is out of stock!', 0, '2026-06-03 11:21:54'),
(14, 4, 'low_stock', 'Product Orange is out of stock!', 0, '2026-06-03 17:14:56'),
(15, 4, 'low_stock', 'Product Orange is out of stock!', 0, '2026-06-03 17:14:58'),
(16, 4, 'low_stock', 'Product Orange is out of stock!', 0, '2026-06-04 05:38:49'),
(17, 4, 'low_stock', 'Product Orange is out of stock!', 0, '2026-06-04 05:38:53'),
(18, 17, 'low_stock', 'Product Mangoes is low on stock. Only 10 items left.', 0, '2026-06-04 05:38:53'),
(19, 17, 'low_stock', 'Product Mangoes is low on stock. Only 5 items left.', 0, '2026-06-04 05:38:55'),
(20, 4, 'low_stock', 'Product Orange is out of stock!', 0, '2026-06-04 05:38:55'),
(21, 17, 'low_stock', 'Product Mangoes is out of stock!', 0, '2026-06-04 05:38:55'),
(22, 17, 'low_stock', 'Product Mangoes is out of stock!', 0, '2026-06-04 05:39:03'),
(23, 4, 'low_stock', 'Product Orange is out of stock!', 0, '2026-06-04 05:39:03'),
(24, 17, 'low_stock', 'Product Mangoes is out of stock!', 0, '2026-06-04 05:39:03'),
(25, 17, 'low_stock', 'Product Mangoes is out of stock!', 0, '2026-06-04 05:39:06'),
(26, 4, 'low_stock', 'Product Orange is out of stock!', 0, '2026-06-04 05:39:06'),
(27, 17, 'low_stock', 'Product Mangoes is out of stock!', 0, '2026-06-04 05:39:06'),
(28, 4, 'low_stock', 'Product Orange is out of stock!', 0, '2026-07-01 18:16:14'),
(29, 17, 'low_stock', 'Product Mangoes is out of stock!', 0, '2026-07-01 18:16:14'),
(30, 9, 'low_stock', 'Product Dried Apples is low on stock. Only 9.00 items left.', 0, '2026-07-02 05:49:26');

-- --------------------------------------------------------

--
-- Table structure for table `stock_logs`
--

CREATE TABLE `stock_logs` (
  `id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity_added` int(11) DEFAULT NULL,
  `date_added` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stores`
--

CREATE TABLE `stores` (
  `id` int(11) NOT NULL,
  `store_name` varchar(100) NOT NULL,
  `location` varchar(200) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stores`
--

INSERT INTO `stores` (`id`, `store_name`, `location`, `phone`, `created_at`) VALUES
(1, 'Main Store', 'Kandy', '081-2234567', '2026-07-02 05:44:17'),
(2, 'Branch 1', 'Peradeniya', '081-2234568', '2026-07-02 05:44:17');

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `contact_person` varchar(100) DEFAULT NULL,
  `phone` varchar(15) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`id`, `name`, `contact_person`, `phone`, `email`, `address`, `created_at`) VALUES
(1, 'Ashif Traders', 'Mohamed Ashif', '0771234567', 'ashif@gmail.com', 'Martiet Complex, Peradeniya, Kandy', '2026-04-24 05:15:02');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `role` enum('admin','staff') DEFAULT 'staff',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `reset_token` varchar(255) DEFAULT NULL,
  `reset_expires` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `full_name`, `phone`, `role`, `created_at`, `reset_token`, `reset_expires`) VALUES
(1, 'admin', 'admin@fruitstore.com', '0192023a7bbd73250516f069df18b500', 'Administrator', '0771234567', 'admin', '2026-04-24 05:15:02', NULL, NULL),
(2, 'staff1', 'staff@fruitstore.com', 'de9bf5643eabf80f4a56fda3bbb84483', 'John Doe', '0712345678', 'staff', '2026-04-24 05:15:02', NULL, NULL),
(4, 't2003', 'tisha.3@gmail.com', '827ccb0eea8a706c4c34a16891f84e7b', 'tisha perera', '0789678543', 'staff', '2026-06-03 10:50:01', NULL, NULL),
(5, 'sachi2', 'sachinithennakoon.@gimail.com', '68053af2923e00204c3ca7c6a3150cf7', 'sachini  thennakoon', '0784667891', 'staff', '2026-06-03 10:52:53', '19ba25d562a810cd746536a1ccd211c133f773be310e69c3456590775b82a1af', '2026-06-03 15:49:56'),
(6, 'harshani 2002', 'harshanigodagama.3@gmail.com', 'hash', 'Harshani Godagama', '0713632699', 'staff', '2026-06-03 11:42:56', '6e25769f976ed3a59b691685bd20290489cd194bbb65d9ca66f8b38735203303', '2026-07-02 19:30:07'),
(7, 'sachi4', 'sachinithennakoon.@gimal.com', '202cb962ac59075b964b07152d234b70', 'sachini  thennakoon', '0784328791', 'staff', '2026-06-03 12:49:17', 'e51a8db9f8f01bde25995fee4cdcfc303bfe9307032651fd2bc3dc76d4039172', '2026-06-03 15:51:50');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `invoice_no` (`invoice_no`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `invoice_items`
--
ALTER TABLE `invoice_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `invoice_id` (`invoice_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `stock_alerts`
--
ALTER TABLE `stock_alerts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `stock_logs`
--
ALTER TABLE `stock_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `stores`
--
ALTER TABLE `stores`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
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
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `invoice_items`
--
ALTER TABLE `invoice_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=88;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=92;

--
-- AUTO_INCREMENT for table `stock_alerts`
--
ALTER TABLE `stock_alerts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `stock_logs`
--
ALTER TABLE `stock_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stores`
--
ALTER TABLE `stores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `invoices_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `invoices_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `invoice_items`
--
ALTER TABLE `invoice_items`
  ADD CONSTRAINT `invoice_items_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `invoice_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `stock_alerts`
--
ALTER TABLE `stock_alerts`
  ADD CONSTRAINT `stock_alerts_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `stock_logs`
--
ALTER TABLE `stock_logs`
  ADD CONSTRAINT `stock_logs_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
