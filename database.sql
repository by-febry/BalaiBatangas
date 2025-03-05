-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 18, 2024 at 09:49 AM
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
-- Database: `sinventoryphp`
--

-- --------------------------------------------------------

--
-- Table structure for table `brands`
--

CREATE TABLE `brands` (
  `brand_id` int(11) NOT NULL,
  `brand_name` varchar(255) NOT NULL,
  `brand_active` int(11) NOT NULL DEFAULT 0,
  `brand_status` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `brands`
--

INSERT INTO `brands` (`brand_id`, `brand_name`, `brand_active`, `brand_status`) VALUES
(10, 'FBev', 1, 1),
(13, 'APtuu', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

CREATE TABLE `cart_items` (
  `cart_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `categories_id` int(11) NOT NULL,
  `categories_name` varchar(255) NOT NULL,
  `categories_active` int(11) NOT NULL DEFAULT 0,
  `categories_status` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`categories_id`, `categories_name`, `categories_active`, `categories_status`) VALUES
(12, 'Beverage', 1, 1),
(14, 'Food', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `customer_profiles`
--

CREATE TABLE `customer_profiles` (
  `profile_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `preferences` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `feedback_reactions`
--

CREATE TABLE `feedback_reactions` (
  `reaction_id` int(11) NOT NULL,
  `feedback_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `is_helpful` tinyint(1) NOT NULL,
  `reaction_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback_reactions`
--

INSERT INTO `feedback_reactions` (`reaction_id`, `feedback_id`, `user_id`, `is_helpful`, `reaction_date`) VALUES
(1, 1, 5, 1, '2024-01-17 03:00:00'),
(2, 1, 6, 1, '2024-01-18 06:30:00'),
(3, 2, 7, 1, '2024-01-26 01:45:00'),
(4, 2, 1, 1, '2024-01-27 08:20:00'),
(5, 3, 5, 1, '2024-02-06 02:30:00'),
(6, 3, 6, 1, '2024-02-07 05:15:00'),
(7, 4, 7, 1, '2024-02-13 07:45:00');

-- --------------------------------------------------------

--
-- Table structure for table `library`
--

CREATE TABLE `library` (
  `festival_id` int(11) NOT NULL,
  `festival_name` varchar(255) NOT NULL,
  `festival_image` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `location` varchar(255) NOT NULL,
  `map_coordinates` varchar(255) NOT NULL,
  `date_celebrated` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `library`
--

INSERT INTO `library` (`festival_id`, `festival_name`, `festival_image`, `description`, `location`, `map_coordinates`, `date_celebrated`, `created_at`, `updated_at`, `status`) VALUES
(1, 'Lambayok Festival', 'lambayok.webp', 'The Lambayok Festival is an annual celebration in San Juan, Batangas, Philippines, that highlights the town\'s three main sources of livelihood: \"Lambanog\" (local coconut wine), \"Palayok\" (clay pots), and \"Karagatan\" (the sea). The festival showcases the town\'s rich cultural heritage through parades, street dances, and various contests. It\'s held every December and serves as a way to promote local products and tourism.', 'San Juan, Batangas', '13.8283° N, 121.3953° E', 'December', '2024-11-15 02:42:18', '2024-11-15 02:42:18', 1),
(2, 'Bakahan Festival', 'bakahan.jpg', 'The Bakahan Festival is an annual event in San Juan, Batangas, Philippines, celebrating the town\'s cattle industry...', 'San Juan, Batangas', '13.8283° N, 121.3953° E', 'Annual', '2024-11-15 02:42:18', '2024-11-15 02:42:18', 1),
(3, 'Tapusan Festival', 'tapusan.jpg', 'The Tapusan Festival is an annual celebration held in Batangas, Philippines, typically in January...', 'Batangas City', '13.7565° N, 121.0583° E', 'January', '2024-11-15 02:42:18', '2024-11-15 02:42:18', 1),
(4, 'Sublian Festival', 'Sublian.png', 'The Sublian Festival celebrates the rich cultural heritage...', 'Batangas City', '13.7565° N, 121.0583° E', 'July', '2024-11-15 02:42:18', '2024-11-15 02:42:18', 1),
(5, 'Parada ng Lechon Festival', 'parada ng lechon.png', 'A festive celebration featuring the famous Filipino roasted pig...', 'Balayan, Batangas', '13.9467° N, 120.7281° E', 'June', '2024-11-15 02:42:18', '2024-11-15 02:42:18', 1),
(6, 'El Pasubat Festival', 'El pasubat.png', 'El Pasubat Festival showcases the various local products...', 'Batangas Province', '13.7565° N, 121.0583° E', 'March', '2024-11-15 02:42:18', '2024-11-15 02:42:18', 1),
(7, 'Balsa Festival', 'Balsa.png', 'The Balsa Festival celebrates the maritime heritage...', 'Lian, Batangas', '14.0333° N, 120.6500° E', 'May', '2024-11-15 02:42:18', '2024-11-15 02:42:18', 1),
(8, 'Tinapay Festival', 'Tinapay.png', 'A celebration of local bread-making traditions...', 'Cuenca, Batangas', '13.9089° N, 121.0486° E', 'October', '2024-11-15 02:42:18', '2024-11-15 02:42:18', 1),
(9, 'Anihan Festival', 'Anihan.png', 'The harvest festival that celebrates agricultural abundance...', 'Lobo, Batangas', '13.6458° N, 121.2439° E', 'April', '2024-11-15 02:42:18', '2024-11-15 02:42:18', 1),
(10, 'Kawayan Festival', 'kawayan.png', 'A festival celebrating the versatile bamboo plant...', 'Tuy, Batangas', '14.0167° N, 120.7278° E', 'September', '2024-11-15 02:42:18', '2024-11-15 02:42:18', 1);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `order_date` date NOT NULL,
  `client_name` varchar(255) NOT NULL,
  `client_contact` varchar(255) NOT NULL,
  `sub_total` decimal(10,2) NOT NULL,
  `vat` decimal(10,2) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `discount` decimal(10,2) NOT NULL,
  `grand_total` decimal(10,2) NOT NULL,
  `paid` decimal(10,2) NOT NULL,
  `due` decimal(10,2) NOT NULL,
  `payment_type` int(11) NOT NULL,
  `payment_status` int(11) NOT NULL,
  `payment_place` int(11) NOT NULL,
  `gstn` varchar(255) NOT NULL,
  `order_status` int(11) NOT NULL DEFAULT 0,
  `user_id` int(11) NOT NULL,
  `gst_rate` decimal(5,2) DEFAULT NULL,
  `gst_amount` decimal(10,2) DEFAULT NULL,
  `shipping_cost` decimal(10,2) DEFAULT 0.00,
  `additional_charges` decimal(10,2) DEFAULT 0.00,
  `additional_discount_percent` decimal(5,2) DEFAULT 0.00,
  `additional_discount_amount` decimal(10,2) DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `delivery_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `order_date`, `client_name`, `client_contact`, `sub_total`, `vat`, `total_amount`, `discount`, `grand_total`, `paid`, `due`, `payment_type`, `payment_status`, `payment_place`, `gstn`, `order_status`, `user_id`, `gst_rate`, `gst_amount`, `shipping_cost`, `additional_charges`, `additional_discount_percent`, `additional_discount_amount`, `notes`, `delivery_date`) VALUES
(26, '2024-11-16', 'Nikko', '984048046464', 100.00, 18.00, 118.00, 0.00, 118.00, 118.00, 0.00, 2, 1, 1, '18.00', 1, 1, NULL, NULL, 0.00, 0.00, 0.00, 0.00, NULL, NULL),
(27, '2024-11-16', 'Nikko', '123123123123', 500.00, 90.00, 590.00, 0.00, 590.00, 590.00, 0.00, 2, 1, 1, '90.00', 1, 1, NULL, NULL, 0.00, 0.00, 0.00, 0.00, NULL, NULL),
(28, '2024-11-16', 'doy', '1', 400.00, 72.00, 472.00, 72.00, 400.00, 400.00, 0.00, 2, 1, 1, '72.00', 1, 1, NULL, NULL, 0.00, 0.00, 0.00, 0.00, NULL, NULL),
(29, '2024-11-16', 'doy', '123123123123', 45.00, 8.10, 53.10, 8.00, 45.10, 45.10, 0.00, 2, 1, 1, '8.10', 1, 1, NULL, NULL, 0.00, 0.00, 0.00, 0.00, NULL, NULL),
(30, '2024-11-16', 'cc', '1222', 400.00, 72.00, 472.00, 72.00, 400.00, 400.00, 0.00, 2, 1, 1, '72.00', 1, 1, NULL, NULL, 0.00, 0.00, 0.00, 0.00, NULL, NULL),
(31, '0000-00-00', 'nikko', '1222', 50.00, 9.00, 59.00, 0.00, 59.00, 50.00, 9.00, 2, 2, 1, '9.00', 1, 0, NULL, NULL, 0.00, 0.00, 0.00, 0.00, NULL, NULL),
(32, '0000-00-00', 'nikko', '1222', 50.00, 9.00, 59.00, 0.00, 59.00, 50.00, 9.00, 2, 2, 1, '9.00', 1, 0, NULL, NULL, 0.00, 0.00, 0.00, 0.00, NULL, NULL),
(33, '0000-00-00', 'nikko', '1222', 100.00, 18.00, 118.00, 0.00, 118.00, 50.00, 68.00, 2, 2, 1, '18.00', 1, 0, NULL, NULL, 0.00, 0.00, 0.00, 0.00, NULL, NULL),
(34, '0000-00-00', 'nikko', '1222', 100.00, 18.00, 118.00, 0.00, 118.00, 50.00, 68.00, 2, 2, 1, '18.00', 1, 0, NULL, NULL, 0.00, 0.00, 0.00, 0.00, NULL, NULL),
(35, '0000-00-00', 'nikko', '1222', 100.00, 18.00, 118.00, 0.00, 118.00, 50.00, 68.00, 2, 2, 1, '18.00', 1, 0, NULL, NULL, 0.00, 0.00, 0.00, 0.00, NULL, NULL),
(36, '0000-00-00', 'nikko', '1222', 100.00, 18.00, 118.00, 0.00, 118.00, 118.00, 0.00, 2, 1, 1, '18.00', 1, 0, NULL, NULL, 0.00, 0.00, 0.00, 0.00, NULL, NULL),
(37, '0000-00-00', 'nikko', '1222', 60.00, 10.80, 70.80, 0.00, 70.80, 70.80, 0.00, 2, 1, 1, '10.80', 1, 0, NULL, NULL, 0.00, 0.00, 0.00, 0.00, NULL, NULL),
(38, '0000-00-00', 'nikko', '1222', 40.00, 7.20, 47.20, 0.00, 47.20, 47.20, 0.00, 2, 1, 1, '7.20', 1, 0, NULL, NULL, 0.00, 0.00, 0.00, 0.00, NULL, NULL),
(39, '2024-11-17', 'nikko', '1222', 50.00, 9.00, 59.00, 0.00, 59.00, 59.00, 0.00, 2, 1, 1, '9.00', 1, 1, NULL, NULL, 0.00, 0.00, 0.00, 0.00, NULL, NULL),
(40, '2024-11-17', '', '', 0.00, 0.00, 112.00, 0.00, 0.00, 0.00, 0.00, 0, 0, 0, '', 1, 5, NULL, NULL, 0.00, 0.00, 0.00, 0.00, NULL, NULL),
(41, '2024-11-17', '', '', 0.00, 0.00, 280.00, 0.00, 0.00, 0.00, 0.00, 0, 0, 0, '', 1, 5, NULL, NULL, 0.00, 0.00, 0.00, 0.00, NULL, NULL),
(42, '2024-11-18', '', '', 0.00, 0.00, 1120.00, 0.00, 0.00, 0.00, 0.00, 0, 0, 0, '', 0, 5, NULL, NULL, 0.00, 0.00, 0.00, 0.00, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `order_item`
--

CREATE TABLE `order_item` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL DEFAULT 0,
  `product_id` int(11) NOT NULL DEFAULT 0,
  `quantity` varchar(255) NOT NULL,
  `rate` varchar(255) NOT NULL,
  `total` varchar(255) NOT NULL,
  `order_item_status` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `order_item`
--

INSERT INTO `order_item` (`order_item_id`, `order_id`, `product_id`, `quantity`, `rate`, `total`, `order_item_status`) VALUES
(1, 1, 1, '2', '49', '49.00', 2),
(2, 2, 13, '2', '45', '45.00', 2),
(3, 3, 15, '31', '45', '45.00', 2),
(4, 0, 16, '12', '22', '264.00', 1),
(5, 0, 16, '13', '22', '264.00', 1),
(6, 0, 16, '12', '22', '286.00', 1),
(7, 4, 16, '12', '22', '264.00', 2),
(8, 5, 3, '2', '53', '106.00', 2),
(9, 6, 3, '14', '53', '742.00', 2),
(10, 7, 15, '2', '45', '90.00', 2),
(11, 8, 9, '4', '87', '348.00', 2),
(12, 9, 14, '4', '321', '1284.00', 2),
(13, 10, 6, '1', '70', '70.00', 2),
(14, 10, 7, '1', '29', '29.00', 2),
(15, 10, 10, '1', '35', '35.00', 2),
(16, 10, 4, '1', '140', '140.00', 2),
(17, 11, 6, '4', '70', '280.00', 2),
(18, 12, 17, '15', '10', '150.00', 2),
(19, 13, 17, '9', '10', '90.00', 2),
(20, 14, 18, '10', '2', '20.00', 2),
(21, 15, 19, '5', '1500', '7500.00', 2),
(22, 16, 18, '1', '2', '2.00', 2),
(23, 17, 20, '10', '5', '50.00', 2),
(24, 18, 17, '71', '10', '710.00', 1),
(25, 20, 17, '2', '10', '20', 0),
(26, 20, 20, '1', '5', '5', 0),
(27, 24, 20, '1', '5', '5.00', 1),
(28, 26, 21, '50', '2', '100.00', 1),
(29, 27, 20, '100', '5', '500.00', 1),
(30, 28, 20, '80', '5', '400.00', 1),
(31, 29, 20, '9', '5', '45.00', 1),
(32, 30, 20, '80', '5', '400.00', 1),
(33, 31, 17, '5', '10', '50.00', 0),
(34, 32, 17, '5', '10', '50.00', 0),
(35, 33, 20, '20', '5', '100.00', 0),
(36, 34, 20, '20', '5', '100.00', 0),
(37, 35, 20, '20', '5', '100.00', 0),
(38, 36, 20, '20', '5', '100.00', 0),
(39, 37, 20, '12', '5', '60.00', 0),
(40, 38, 20, '8', '5', '40.00', 0),
(41, 39, 20, '10', '5', '50.00', 1);

-- --------------------------------------------------------

--
-- Table structure for table `order_tracking`
--

CREATE TABLE `order_tracking` (
  `tracking_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `status` enum('pending','processing','shipped','delivered','cancelled') NOT NULL DEFAULT 'pending',
  `status_message` varchar(255) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_proofs`
--

CREATE TABLE `payment_proofs` (
  `proof_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `upload_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','verified','rejected') NOT NULL DEFAULT 'pending',
  `admin_remarks` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment_proofs`
--

INSERT INTO `payment_proofs` (`proof_id`, `order_id`, `user_id`, `image_path`, `upload_date`, `status`, `admin_remarks`) VALUES
(5, 23, 5, 'uploads/payment_proofs/67372c3831ab8.jpg', '2024-11-15 11:10:48', 'verified', NULL),
(6, 25, 5, 'uploads/payment_proofs/67386d8ff2fa2.png', '2024-11-16 10:01:52', 'pending', NULL),
(7, 40, 5, 'uploads/payment_proofs/673960706d0f8.png', '2024-11-17 03:18:08', 'verified', NULL),
(8, 41, 5, 'uploads/payment_proofs/67396255d5738.png', '2024-11-17 03:26:13', 'verified', NULL),
(9, 42, 5, 'uploads/payment_proofs/673aa5f04d016.png', '2024-11-18 02:26:56', 'verified', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `product_id` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `description` varchar(600) NOT NULL,
  `product_image` text NOT NULL,
  `brand_id` int(11) NOT NULL,
  `categories_id` int(11) NOT NULL,
  `quantity` varchar(255) NOT NULL,
  `rate` varchar(255) NOT NULL,
  `active` int(11) NOT NULL DEFAULT 0,
  `status` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`product_id`, `product_name`, `description`, `product_image`, `brand_id`, `categories_id`, `quantity`, `rate`, `active`, `status`) VALUES
(17, 'Kapeng Barako', 'Kapeng Barako, also known as Barako coffee or Batangas coffee, is a coffee varietal grown in the Philippines, particularly in the provinces of Batangas. It is produced from the Liberica species and has a strong flavor and distinctive aroma. The name \"barako\" comes from the Filipino word for \"wild boar\" and is associated with masculinity and strength.', '../assests/images/stock/560274716732b8fbb055a.jpg', 10, 12, '100', '10', 1, 1),
(20, 'Dried Fish', '', '../assests/images/stock/184470098167334f1a18f01.jpg', 13, 14, '610', '5', 1, 1),
(21, 'Lambanog', '', '../assests/images/stock/2126617513673825b0a918d.jpg', 10, 12, '50', '2', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `product_feedback`
--

CREATE TABLE `product_feedback` (
  `feedback_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `feedback_text` text NOT NULL,
  `feedback_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('active','hidden') NOT NULL DEFAULT 'active',
  `helpful_count` int(11) NOT NULL DEFAULT 0,
  `report_count` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_feedback`
--

INSERT INTO `product_feedback` (`feedback_id`, `product_id`, `user_id`, `feedback_text`, `feedback_date`, `status`, `helpful_count`, `report_count`) VALUES
(1, 19, 7, 'The shipping was fast and the coffee came in a well-sealed package. You can really smell the fresh aroma even before opening it.', '2024-01-16 02:00:00', 'active', 5, 0),
(2, 19, 5, 'Been buying this for months now. Consistent quality and always fresh. Great to see authentic Batangas products being sold online.', '2024-01-25 08:30:00', 'active', 3, 0),
(3, 19, 6, 'Perfect grind size for both traditional and modern brewing methods. Works great with my French press and local coffee filter.', '2024-02-05 05:45:00', 'active', 4, 0),
(4, 19, 1, 'Excellent customer service! Had questions about storage recommendations and got prompt, helpful responses.', '2024-02-12 01:15:00', 'active', 2, 0),
(5, 17, 5, 'Fast shipping and excellent packaging. The coffee arrived fresh and vacuum-sealed. Customer service was very responsive when I had questions about brewing methods.', '2024-02-15 02:30:00', 'active', 8, 0),
(6, 17, 6, 'The seller included a small pamphlet with traditional brewing tips - very thoughtful! The coffee itself is amazing.', '2024-02-16 07:45:00', 'active', 5, 0),
(7, 17, 7, 'Been buying this monthly for the past year. Consistent quality and always fresh. Great to see authentic Batangas products being sold online.', '2024-02-17 05:20:00', 'active', 12, 0),
(8, 17, 5, 'test', '2024-11-18 02:05:22', 'active', 0, 0),
(9, 21, 5, 'test', '2024-11-18 02:05:32', 'active', 0, 0),
(10, 17, 5, 'test', '2024-11-18 02:25:50', 'active', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `product_reviews`
--

CREATE TABLE `product_reviews` (
  `review_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `rating` int(1) NOT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `review_text` text NOT NULL,
  `review_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `admin_response` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_reviews`
--

INSERT INTO `product_reviews` (`review_id`, `product_id`, `user_id`, `rating`, `review_text`, `review_date`, `status`, `admin_response`) VALUES
(1, 19, 7, 5, 'Authentic Batangas Kapeng Barako! The strong aroma and rich flavor remind me of mornings in Batangas. Perfect blend of robustness and smoothness.', '2024-01-15 00:30:00', 'approved', NULL),
(2, 19, 5, 4, 'Great quality local coffee. The packaging keeps it fresh, and the taste is consistently good. Just what you would expect from genuine Barako coffee.', '2024-01-20 06:15:00', 'approved', NULL),
(3, 19, 6, 5, 'This is the real deal! As someone who grew up in Batangas, this Kapeng Barako meets all expectations. Strong and aromatic, perfect for starting the day.', '2024-02-01 01:45:00', 'approved', NULL),
(4, 19, 1, 4, 'Excellent traditional Batangas coffee. The beans are well-roasted and ground to perfection. Love supporting local products like this.', '2024-02-10 03:20:00', 'approved', NULL),
(5, 17, 5, 5, 'test', '2024-11-18 02:36:56', 'pending', NULL),
(6, 17, 6, 4, 'Great quality coffee. The packaging keeps it fresh and the grinding is perfect for my coffee maker. Will buy again!', '2024-02-16 06:20:00', 'approved', NULL),
(7, 17, 7, 5, 'This is the real deal! As someone from Batangas, I can vouch for its authenticity. Strong and flavorful.', '2024-02-17 01:45:00', 'approved', NULL),
(8, 17, 1, 4, 'Love the strong flavor. Perfect for my morning coffee. Just wish it came in bigger packages.', '2024-02-18 03:15:00', 'approved', NULL),
(9, 17, 5, 5, 'test', '2024-11-18 02:01:07', 'approved', NULL),
(10, 21, 5, 5, 'test', '2024-11-18 02:05:29', 'pending', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `returns`
--

CREATE TABLE `returns` (
  `return_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `reason` text NOT NULL,
  `return_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `remarks` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `returns`
--

INSERT INTO `returns` (`return_id`, `order_id`, `product_id`, `quantity`, `reason`, `return_date`, `status`, `remarks`) VALUES
(1, 18, 17, 5, 'Sample  ', '2024-11-17 02:09:34', 'approved', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `review_images`
--

CREATE TABLE `review_images` (
  `image_id` int(11) NOT NULL,
  `review_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `upload_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `review_images`
--

INSERT INTO `review_images` (`image_id`, `review_id`, `image_path`, `upload_date`) VALUES
(1, 1, 'assets/images/reviews/kapeng-barako-review1.jpg', '2024-01-15 00:35:00'),
(2, 1, 'assets/images/reviews/kapeng-barako-brewing.jpg', '2024-01-15 00:36:00'),
(3, 2, 'assets/images/reviews/barako-package.jpg', '2024-01-20 06:20:00'),
(4, 3, 'assets/images/reviews/traditional-brew.jpg', '2024-02-01 01:50:00');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `role` varchar(256) NOT NULL,
  `profile_picture` varchar(256) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `email`, `role`, `profile_picture`) VALUES
(1, 'admin', '21232f297a57a5a743894a0e4a801fc3', 'admin@example.com', 'admin', ''),
(5, 'Employee1', 'd3d73db423372e0bea89ca659ea9d115', 'Employee1@example.com', 'user', ''),
(6, 'Employee2', '16a6ca3a73f7f641ec17a7fdfe450ebe', 'Employee2@example.com', 'user', ''),
(7, 'user1', '0a041b9462caa4a31bac3567e0b6e6fd9100787db2ab433d96f6d178cabfce90', 'user1@example.com', 'user', '');

-- --------------------------------------------------------

--
-- Table structure for table `user_addresses`
--

CREATE TABLE `user_addresses` (
  `address_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `address_line1` varchar(255) NOT NULL,
  `address_line2` varchar(255) DEFAULT NULL,
  `city` varchar(100) NOT NULL,
  `state` varchar(100) NOT NULL,
  `postal_code` varchar(20) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `is_default` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_addresses`
--

INSERT INTO `user_addresses` (`address_id`, `user_id`, `address_line1`, `address_line2`, `city`, `state`, `postal_code`, `phone`, `is_default`) VALUES
(1, 5, 'example address', '', 'Lipa', 'Batangas', '1233', '123123123', 1);

-- --------------------------------------------------------

--
-- Table structure for table `user_notifications`
--

CREATE TABLE `user_notifications` (
  `notification_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `reference_id` int(11) DEFAULT NULL,
  `type` varchar(50) DEFAULT 'order',
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_notifications`
--

INSERT INTO `user_notifications` (`notification_id`, `user_id`, `title`, `message`, `reference_id`, `type`, `is_read`, `created_at`) VALUES
(1, 1, 'Test Notification', 'This is a test notification', NULL, 'order', 0, '2024-11-17 03:24:19'),
(2, 5, 'Welcome to Explore Batangas!', 'Thank you for joining our platform. Start exploring authentic Batangas products today!', NULL, 'system', 1, '2024-11-18 02:28:26');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`brand_id`);

--
-- Indexes for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`categories_id`);

--
-- Indexes for table `customer_profiles`
--
ALTER TABLE `customer_profiles`
  ADD PRIMARY KEY (`profile_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `feedback_reactions`
--
ALTER TABLE `feedback_reactions`
  ADD PRIMARY KEY (`reaction_id`),
  ADD UNIQUE KEY `unique_reaction` (`feedback_id`,`user_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `library`
--
ALTER TABLE `library`
  ADD PRIMARY KEY (`festival_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`);

--
-- Indexes for table `order_item`
--
ALTER TABLE `order_item`
  ADD PRIMARY KEY (`order_item_id`);

--
-- Indexes for table `order_tracking`
--
ALTER TABLE `order_tracking`
  ADD PRIMARY KEY (`tracking_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `payment_proofs`
--
ALTER TABLE `payment_proofs`
  ADD PRIMARY KEY (`proof_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `product_feedback`
--
ALTER TABLE `product_feedback`
  ADD PRIMARY KEY (`feedback_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `product_reviews`
--
ALTER TABLE `product_reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `returns`
--
ALTER TABLE `returns`
  ADD PRIMARY KEY (`return_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `review_images`
--
ALTER TABLE `review_images`
  ADD PRIMARY KEY (`image_id`),
  ADD KEY `review_id` (`review_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `user_addresses`
--
ALTER TABLE `user_addresses`
  ADD PRIMARY KEY (`address_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `user_notifications`
--
ALTER TABLE `user_notifications`
  ADD PRIMARY KEY (`notification_id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `brands`
--
ALTER TABLE `brands`
  MODIFY `brand_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `categories_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `customer_profiles`
--
ALTER TABLE `customer_profiles`
  MODIFY `profile_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `feedback_reactions`
--
ALTER TABLE `feedback_reactions`
  MODIFY `reaction_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `library`
--
ALTER TABLE `library`
  MODIFY `festival_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `order_item`
--
ALTER TABLE `order_item`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `order_tracking`
--
ALTER TABLE `order_tracking`
  MODIFY `tracking_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment_proofs`
--
ALTER TABLE `payment_proofs`
  MODIFY `proof_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `product_feedback`
--
ALTER TABLE `product_feedback`
  MODIFY `feedback_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `product_reviews`
--
ALTER TABLE `product_reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `returns`
--
ALTER TABLE `returns`
  MODIFY `return_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `review_images`
--
ALTER TABLE `review_images`
  MODIFY `image_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `user_addresses`
--
ALTER TABLE `user_addresses`
  MODIFY `address_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user_notifications`
--
ALTER TABLE `user_notifications`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `cart_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`);

--
-- Constraints for table `customer_profiles`
--
ALTER TABLE `customer_profiles`
  ADD CONSTRAINT `customer_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `feedback_reactions`
--
ALTER TABLE `feedback_reactions`
  ADD CONSTRAINT `feedback_reactions_ibfk_1` FOREIGN KEY (`feedback_id`) REFERENCES `product_feedback` (`feedback_id`),
  ADD CONSTRAINT `feedback_reactions_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `order_tracking`
--
ALTER TABLE `order_tracking`
  ADD CONSTRAINT `order_tracking_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`);

--
-- Constraints for table `payment_proofs`
--
ALTER TABLE `payment_proofs`
  ADD CONSTRAINT `payment_proofs_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  ADD CONSTRAINT `payment_proofs_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `product_feedback`
--
ALTER TABLE `product_feedback`
  ADD CONSTRAINT `product_feedback_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`),
  ADD CONSTRAINT `product_feedback_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `product_reviews`
--
ALTER TABLE `product_reviews`
  ADD CONSTRAINT `product_reviews_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`),
  ADD CONSTRAINT `product_reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `returns`
--
ALTER TABLE `returns`
  ADD CONSTRAINT `returns_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  ADD CONSTRAINT `returns_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`);

--
-- Constraints for table `review_images`
--
ALTER TABLE `review_images`
  ADD CONSTRAINT `review_images_ibfk_1` FOREIGN KEY (`review_id`) REFERENCES `product_reviews` (`review_id`);

--
-- Constraints for table `user_addresses`
--
ALTER TABLE `user_addresses`
  ADD CONSTRAINT `user_addresses_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `user_notifications`
--
ALTER TABLE `user_notifications`
  ADD CONSTRAINT `user_notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
