-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Sep 23, 2026 at 05:54 PM
-- Server version: 8.0.46
-- PHP Version: 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `count4u`
--

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

DROP TABLE IF EXISTS `category`;
CREATE TABLE IF NOT EXISTS `category` (
  `category_id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `category_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`category_id`),
  UNIQUE KEY `category_name` (`category_name`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`category_id`, `category_name`) VALUES
(14, 'Beans'),
(1, 'Bread'),
(6, 'Canned foods'),
(3, 'Chips'),
(9, 'Chocolates'),
(12, 'Cooldrinks'),
(11, 'Grains & Pastas'),
(5, 'Gum'),
(13, 'Juice'),
(2, 'Milk'),
(10, 'Spices'),
(4, 'Sweets'),
(8, 'Toiletries'),
(7, 'Vegetables');

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

DROP TABLE IF EXISTS `product`;
CREATE TABLE IF NOT EXISTS `product` (
  `product_id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `product_name` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `category_id` int UNSIGNED NOT NULL,
  `selling_price` decimal(10,2) UNSIGNED NOT NULL DEFAULT '0.00',
  `quantity` int UNSIGNED NOT NULL DEFAULT '0',
  `low_stock_level` int UNSIGNED NOT NULL DEFAULT '10',
  `image_path` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`product_id`),
  KEY `idx_product_category` (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`product_id`, `product_name`, `category_id`, `selling_price`, `quantity`, `low_stock_level`, `image_path`) VALUES
(1, 'Albany Brown Bread', 1, 18.00, 15, 5, 'uploads/Albany Brown Bread.jpg'),
(2, 'Albany White Bread', 1, 20.00, 10, 5, 'uploads/Albany White Bread.jpg'),
(3, 'Blue Ribbon Brown Bread', 1, 19.00, 15, 5, 'uploads/Blue Ribbon Brown Bread.jpg'),
(4, 'Blue Ribbon White Bread', 1, 21.00, 10, 5, 'uploads/Blue Ribbon White Bread.jpg'),
(5, 'Sasko Brown Bread', 1, 20.00, 15, 5, 'uploads/Sasko Brown Bread.jpg'),
(6, 'Sasko White Bread', 1, 22.00, 10, 5, 'uploads/Sasko White Bread.jpg'),
(7, 'Clover Full Cream Milk 1L', 2, 25.00, 12, 5, 'uploads/Clover Full Cream Milk.jpg'),
(8, 'Clover Low Fat Milk 1L', 2, 25.00, 10, 5, 'uploads/Clover Low Fat Milk.jpg'),
(9, 'Parmalat Full Cream Milk 1L', 2, 26.00, 10, 5, 'uploads/Parmalat Full Cream Milk.jpg'),
(10, 'Simba Chips Cheese', 3, 18.00, 20, 5, 'uploads/Simba Chips Cheese.jpg'),
(11, 'Lay\'s Original', 3, 20.00, 15, 5, 'uploads/Lay\'s Original.jpg'),
(12, 'NikNaks', 3, 1.50, 20, 5, 'uploads/NikNaks.jpg'),
(13, 'Doritos', 3, 20.00, 12, 5, 'uploads/Doritos.jpg'),
(14, 'Jelly Tots', 4, 10.00, 25, 5, 'uploads/Jelly Tots.jpg'),
(15, 'Wine Gums', 4, 12.00, 20, 5, 'uploads/Wine Gums.jpg'),
(16, 'Lollipop', 4, 2.00, 40, 5, 'uploads/Lollipop.jpg'),
(17, 'Stimorol Spearmint', 5, 8.00, 25, 5, 'uploads/Stimorol Spearmint.jpg'),
(18, 'Orbit Peppermint Gum', 5, 12.00, 20, 5, 'uploads/Orbit Peppermint Gum.jpg'),
(19, 'Chappies', 5, 0.50, 50, 5, 'uploads/Chappies.jpg'),
(20, 'Koo Baked Beans', 6, 18.00, 15, 5, 'uploads/Koo Baked Beans.jpg'),
(21, 'Koo Chakalaka', 6, 20.00, 12, 5, 'uploads/Koo Chakalaka.jpg'),
(22, 'All Gold Tomato', 6, 18.00, 12, 5, 'uploads/All Gold Tomato.jpg'),
(23, 'Lucky Star Pilchards', 6, 25.00, 10, 5, 'uploads/Lucky Star Pilchards.jpg'),
(24, 'Potatoes 1kg', 7, 25.00, 15, 5, 'uploads/Potatoes.jpg'),
(25, 'Onions 1kg', 7, 22.00, 15, 5, 'uploads/Onions.jpg'),
(26, 'Tomatoes 1kg', 7, 25.00, 12, 5, 'uploads/Tomatoes.jpg'),
(27, 'Carrots 1kg', 7, 20.00, 10, 5, 'uploads/Carrots.jpg'),
(28, 'Colgate Toothpaste', 8, 25.00, 12, 5, 'uploads/Colgate Toothpaste.jpg'),
(29, 'Lux Soap', 8, 12.00, 20, 5, 'uploads/Lux Soap.jpg'),
(30, 'Dove Soap', 8, 18.00, 15, 5, 'uploads/Dove Soap.jpg'),
(31, 'Sunlight Dishwashing Liquid', 8, 25.00, 20, 5, 'uploads/Sunlight Dishwashing Liquid.jpg'),
(32, 'Cadbury Dairy Milk', 9, 15.00, 20, 5, 'uploads/Cadbury Dairy Milk.jpg'),
(33, 'Cadbury Lunch Bar', 9, 14.00, 20, 5, 'uploads/Cadbury Lunch Bar.jpg'),
(34, 'Cadbury PS Chocolate', 9, 12.00, 15, 5, 'uploads/Cadbury PS Chocolate.jpg'),
(35, 'KitKat', 9, 15.00, 20, 5, 'uploads/KitKat.jpg'),
(36, 'Robertsons Braai Spice', 10, 20.00, 10, 5, 'uploads/Robertsons Braai Spice.jpg'),
(37, 'Robertsons Steak & Chops', 10, 20.00, 10, 5, 'uploads/Robertsons Steak & Chops.jpg'),
(38, 'Rajah Curry Powder', 10, 18.00, 10, 5, 'uploads/Rajah Curry Powder.jpg'),
(39, 'Aromat Original', 10, 25.00, 12, 5, 'uploads/Aromat Original.jpg'),
(40, 'Ace Maize Meal', 11, 40.00, 10, 5, 'uploads/Ace Maize Meal.jpg'),
(41, 'Iwisa Maize Meal', 11, 38.00, 10, 5, 'uploads/Iwisa Maize Meal.jpg'),
(42, 'Fatti\'s & Moni\'s Spaghetti', 11, 18.00, 15, 5, 'uploads/Fatti\'s & Moni\'s Spaghetti.jpg'),
(43, 'Fatti\'s & Moni\'s Macaroni', 11, 18.00, 15, 5, 'uploads/Fatti\'s & Moni\'s Macaroni.jpg'),
(44, 'Rice', 11, 35.00, 12, 5, 'uploads/Rice.jpg'),
(45, 'Coca-Cola 500ml', 12, 15.00, 25, 5, 'uploads/Coca-Cola 500ml.jpg'),
(46, 'Coca-Cola 2L', 12, 25.00, 15, 5, 'uploads/Coca-Cola 2L.jpg'),
(47, 'Sprite 500ml', 12, 15.00, 20, 5, 'uploads/Sprite.jpg'),
(48, 'Fanta Orange 500ml', 12, 15.00, 20, 5, 'uploads/Fanta Orange.jpg'),
(49, 'Pepsi 500ml', 12, 14.00, 15, 5, 'uploads/Pepsi.jpg'),
(50, 'Liqui-Fruit 1L', 13, 30.00, 10, 5, 'uploads/Liqui-Fruit.jpg'),
(51, 'Ceres 1L', 13, 30.00, 10, 5, 'uploads/Ceres.jpg'),
(52, 'Oros Orange 2L', 13, 35.00, 8, 5, 'uploads/Oros Orange.jpg'),
(53, 'Red Kidney Beans', 14, 18.00, 12, 5, 'uploads/Red Kidney Beans.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `sale`
--

DROP TABLE IF EXISTS `sale`;
CREATE TABLE IF NOT EXISTS `sale` (
  `sale_id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` int UNSIGNED NOT NULL,
  `sale_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `total_amount` decimal(10,2) UNSIGNED NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`sale_id`),
  KEY `idx_sale_user` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sale`
--

INSERT INTO `sale` (`sale_id`, `user_id`, `sale_date`, `total_amount`) VALUES
(1, 1, '2026-09-15 08:56:03', 90.00),
(2, 1, '2026-09-23 18:59:05', 49.00);

-- --------------------------------------------------------

--
-- Table structure for table `sale_item`
--

DROP TABLE IF EXISTS `sale_item`;
CREATE TABLE IF NOT EXISTS `sale_item` (
  `sale_item_id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `sale_id` int UNSIGNED NOT NULL,
  `product_id` int UNSIGNED NOT NULL,
  `quantity` int UNSIGNED NOT NULL DEFAULT '1',
  `unit_price` decimal(10,2) UNSIGNED NOT NULL DEFAULT '0.00',
  `subtotal` decimal(10,2) UNSIGNED NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`sale_item_id`),
  KEY `idx_sale_item_sale` (`sale_id`),
  KEY `idx_sale_item_product` (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sale_item`
--

INSERT INTO `sale_item` (`sale_item_id`, `sale_id`, `product_id`, `quantity`, `unit_price`, `subtotal`) VALUES
(1, 1, 1, 5, 18.00, 90.00),
(2, 2, 46, 1, 25.00, 25.00),
(3, 2, 16, 1, 2.00, 2.00),
(4, 2, 25, 1, 22.00, 22.00);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `user_id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `surname` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `phone_number` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `name`, `surname`, `email`, `password`, `phone_number`) VALUES
(1, 'Safiya Elmi', 'Elmi', 'safiyamelmi19@gmail.com', '$2y$10$ERbRnBRgHPOCVu3tIN1kUeiz9gp9aP58uFEGQA8veIs7tiMjFioTe', '0628746842');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `product`
--
ALTER TABLE `product`
  ADD CONSTRAINT `fk_product_category` FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `sale`
--
ALTER TABLE `sale`
  ADD CONSTRAINT `fk_sale_user` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `sale_item`
--
ALTER TABLE `sale_item`
  ADD CONSTRAINT `fk_sale_item_product` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_sale_item_sale` FOREIGN KEY (`sale_id`) REFERENCES `sale` (`sale_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
