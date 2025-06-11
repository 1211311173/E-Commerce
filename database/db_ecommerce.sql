-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 17, 2023 at 11:26 AM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.1.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_ecommerce`
--

-- --------------------------------------------------------

--
-- Table structure for table `bags`
--

CREATE TABLE `bags` (
  `id` int(10) NOT NULL,
  `bags_category_name` varchar(50) NOT NULL,
  `bags_category_quantity` int(10) DEFAULT 0,
  `bags_category_status` binary(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bags`
--

INSERT INTO `bags` (`id`, `bags_category_name`, `bags_category_quantity`, `bags_category_status`) VALUES
(1, 'Shopping Bag', 62, 0x31),
(2, 'Purse', 35, 0x31),
(3, 'Wallet', 75, 0x31);

-- --------------------------------------------------------

--
-- Table structure for table `banner`
--

CREATE TABLE `banner` (
  `banner_id` int(11) NOT NULL,
  `banner_subtitle` varchar(50) NOT NULL,
  `banner_title` text NOT NULL,
  `banner_items_price` int(10) NOT NULL,
  `banner_image` varchar(50) NOT NULL,
  `banner_status` binary(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `banner`
--

INSERT INTO `banner` (`banner_id`, `banner_subtitle`, `banner_title`, `banner_items_price`, `banner_image`, `banner_status`) VALUES
(1, 'Trending item', 'Women\'s latest fashion sale', 20, 'banner-1.jpg', 0x31),
(2, 'Trending accessories', 'Modern sunglasses', 15, 'banner-2.jpg', 0x31),
(3, 'Sale Offer', 'New fashion summer sale', 29, 'banner-3.jpg', 0x31);

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `id` int(10) NOT NULL,
  `name` varchar(50) NOT NULL,
  `img` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` binary(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`id`, `name`, `img`, `created_at`, `updated_at`, `status`) VALUES
(1, 'Clothes', 'dress.svg', '2022-11-08 17:05:38', '2022-11-08 22:05:38', 0x31),
(2, 'Footwear', 'shoes.svg', '2022-11-08 17:05:38', '2022-11-08 22:05:38', 0x31),
(3, 'Jewelry', 'jewelry.svg', '2022-11-08 17:05:38', '2022-11-08 22:05:38', 0x31),
(4, 'Perfume', 'perfume.svg', '2022-11-08 17:05:38', '2022-11-08 22:05:38', 0x31),
(5, 'Cosmetics', 'cosmetics.svg', '2022-11-08 17:05:38', '2022-11-08 22:05:38', 0x31),
(6, 'Glasses', 'glasses.svg', '2022-11-08 17:05:38', '2022-11-08 22:05:38', 0x31),
(7, 'Bags', 'bag.svg', '2022-11-08 17:05:38', '2022-11-08 22:05:38', 0x31),
(8, 'Electronics', 'watch.svg', '2022-11-08 17:05:38', '2022-11-08 22:05:38', 0x31);

-- --------------------------------------------------------

--
-- Table structure for table `category_bar`
--

CREATE TABLE `category_bar` (
  `id` int(10) NOT NULL,
  `category_title` varchar(50) NOT NULL,
  `category_quantity` int(10) NOT NULL,
  `category_img` varchar(50) NOT NULL,
  `category_status` binary(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category_bar`
--

INSERT INTO `category_bar` (`id`, `category_title`, `category_quantity`, `category_img`, `category_status`) VALUES
(1, 'Dress & frock', 53, 'coat.svg', 0x31),
(2, 'Glasses & lens', 68, 'glasses.svg', 0x31),
(3, 'Shorts & jeans', 84, 'shorts.svg', 0x31),
(4, 'T-shirts', 35, 'tee.svg', 0x31),
(5, 'Jacket', 16, 'jacket.svg', 0x31),
(6, 'Watch', 27, 'watch.svg', 0x31),
(7, 'Hat & caps', 39, 'hat.svg', 0x31);

-- --------------------------------------------------------

--
-- Table structure for table `category_electronics`
--

CREATE TABLE `category_electronics` (
  `id` int(10) NOT NULL,
  `category_name` varchar(30) NOT NULL,
  `status` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category_electronics`
--

INSERT INTO `category_electronics` (`id`, `category_name`, `status`) VALUES
(1, 'Desktop', 1),
(2, 'Laptop', 1),
(3, 'Camera', 1),
(4, 'Tablet', 1),
(5, 'Headphone', 1);

-- --------------------------------------------------------

--
-- Table structure for table `clothes`
--

CREATE TABLE `clothes` (
  `id` int(10) NOT NULL,
  `cloth_category_name` varchar(50) NOT NULL,
  `cloth_category_quantity` int(10) DEFAULT 0,
  `coloth_category_status` binary(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `clothes`
--

INSERT INTO `clothes` (`id`, `cloth_category_name`, `cloth_category_quantity`, `coloth_category_status`) VALUES
(1, 'Shirt', 300, 0x31),
(2, 'shorts & jeans', 60, 0x31),
(4, 'jacket', 50, 0x31),
(5, 'dress & frock', 87, 0x31);

-- --------------------------------------------------------

--
-- Table structure for table `cosmetics`
--

CREATE TABLE `cosmetics` (
  `id` int(10) NOT NULL,
  `cosmetics_category_name` varchar(50) NOT NULL,
  `cosmetics_category_quantity` int(10) DEFAULT 0,
  `cosmetics_category_status` binary(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cosmetics`
--

INSERT INTO `cosmetics` (`id`, `cosmetics_category_name`, `cosmetics_category_quantity`, `cosmetics_category_status`) VALUES
(1, 'Shampoo', 68, 0x31),
(2, 'Sunscreen', 46, 0x31),
(3, 'Body Wash', 79, 0x31),
(4, 'Makeup Kit', 23, 0x31);

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `customer_id` int(100) NOT NULL,
  `customer_fname` varchar(50) NOT NULL,
  `customer_email` varchar(100) NOT NULL,
  `customer_pwd` varchar(100) NOT NULL,
  `customer_phone` varchar(15) NOT NULL,
  `customer_address` text NOT NULL,
  `customer_role` varchar(50) NOT NULL DEFAULT 'normal'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`customer_id`, `customer_fname`, `customer_email`, `customer_pwd`, `customer_phone`, `customer_address`, `customer_role`) VALUES
(9, 'FahadAdmin', 'dev.shahfahad@gmail.com', '$2y$10$Sbt8xXQuKt74so7nd9AD1.RsgV/8Q.ZGen/XaR5d9lrRzHicnV0Hm', '03469589557', 'Peshawar, Pakistan', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `deal_of_the_day`
--

CREATE TABLE `deal_of_the_day` (
  `deal_id` int(10) NOT NULL,
  `deal_title` text NOT NULL,
  `deal_description` text NOT NULL,
  `deal_net_price` double(10,2) NOT NULL,
  `deal_discounted_price` double(10,2) NOT NULL,
  `available_deal` int(10) NOT NULL,
  `sold_deal` int(10) NOT NULL,
  `deal_image` varchar(50) NOT NULL,
  `deal_status` binary(1) DEFAULT NULL,
  `deal_end_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `deal_of_the_day`
--

INSERT INTO `deal_of_the_day` (`deal_id`, `deal_title`, `deal_description`, `deal_net_price`, `deal_discounted_price`, `available_deal`, `sold_deal`, `deal_image`, `deal_status`, `deal_end_time`) VALUES
(1, 'shampoo, conditioner & facewash packs', 'Lorem ipsum dolor sit amet consectetur Lorem ipsum dolor\r\n                        dolor sit amet consectetur Lorem ipsum dolor', 200.00, 150.00, 40, 20, 'shampoo.jpg', 0x31, DATE_ADD(NOW(), INTERVAL 7 DAY)),
(2, 'Rose Gold diamonds Earring', 'Lorem ipsum dolor sit amet consectetur Lorem ipsum dolor\r\n                        dolor sit amet consectetur Lorem ipsum dolor', 250.00, 190.00, 40, 15, 'jewellery-1.jpg', 0x31, DATE_ADD(NOW(), INTERVAL 5 DAY));

-- --------------------------------------------------------

--
-- Table structure for table `footwear`
--

CREATE TABLE `footwear` (
  `id` int(10) NOT NULL,
  `footwear_category_name` varchar(50) NOT NULL,
  `footwear_category_quantity` int(10) DEFAULT 0,
  `footwear_category_status` binary(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `footwear`
--

INSERT INTO `footwear` (`id`, `footwear_category_name`, `footwear_category_quantity`, `footwear_category_status`) VALUES
(1, 'Sports', 45, 0x31),
(2, 'Formal', 75, 0x31),
(3, 'Casual', 35, 0x31),
(4, 'jacket', 50, 0x31),
(5, 'Safety Shoes', 26, 0x31);

-- --------------------------------------------------------

--
-- Table structure for table `glasses`
--

CREATE TABLE `glasses` (
  `id` int(10) NOT NULL,
  `glasses_category_name` varchar(50) NOT NULL,
  `glasses_category_quantity` int(10) DEFAULT 0,
  `glasses_category_status` binary(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `glasses`
--

INSERT INTO `glasses` (`id`, `glasses_category_name`, `glasses_category_quantity`, `glasses_category_status`) VALUES
(1, 'Sunglasses', 50, 0x31),
(2, 'Lenses', 48, 0x31),
(3, 'Lenses', 48, 0x31);

-- --------------------------------------------------------

--
-- Table structure for table `jewelry`
--

CREATE TABLE `jewelry` (
  `id` int(10) NOT NULL,
  `Jewelry_category_name` varchar(50) NOT NULL,
  `Jewelry_category_quantity` int(10) DEFAULT 0,
  `Jewelry_category_status` binary(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jewelry`
--

INSERT INTO `jewelry` (`id`, `Jewelry_category_name`, `Jewelry_category_quantity`, `Jewelry_category_status`) VALUES
(1, 'Earrings', 46, 0x31),
(2, 'Couple Rings', 73, 0x31),
(3, 'Necklace', 61, 0x31);

-- --------------------------------------------------------

--
-- Table structure for table `perfume`
--

CREATE TABLE `perfume` (
  `id` int(10) NOT NULL,
  `perfume_category_name` varchar(50) NOT NULL,
  `perfume_category_quantity` int(10) DEFAULT 0,
  `perfume_category_status` binary(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `perfume`
--

INSERT INTO `perfume` (`id`, `perfume_category_name`, `perfume_category_quantity`, `perfume_category_status`) VALUES
(1, 'Clothes Perfume', 12, 0x31),
(2, 'Deodorant', 60, 0x31),
(3, 'jacket', 50, 0x31),
(4, 'dress & frock', 87, 0x31);

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(100) NOT NULL,
  `product_catag` varchar(100) NOT NULL,
  `subcategory` varchar(50) DEFAULT NULL,
  `product_title` varchar(255) NOT NULL,
  `product_price` int(100) NOT NULL,
  `product_desc` text NOT NULL,
  `product_date` varchar(50) NOT NULL,
  `product_img` text NOT NULL,
  `product_left` int(100) NOT NULL,
  `product_author` varchar(100) NOT NULL,
  `category_id` int(10) DEFAULT NULL,
  `section_id` int(10) DEFAULT NULL,
  `discounted_price` double(10,2) DEFAULT NULL,
  `image_1` varchar(50) NOT NULL,
  `image_2` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` binary(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `product_catag`, `subcategory`, `product_title`, `product_price`, `product_desc`, `product_date`, `product_img`, `product_left`, `product_author`, `category_id`, `section_id`, `discounted_price`, `image_1`, `image_2`, `created_at`, `updated_at`, `status`) VALUES
(1, 'men', 'Jacket', 'jacket', 75, 'Mens Winter Leathers Jackets', '', 'jacket-3.jpg', 50, 'admin fahad', NULL, 7, 48.00, 'jacket-4.jpg', NULL, '2023-06-16 18:33:06', '2023-06-16 23:33:06', 0x31),
(2, 'men', 'Shirt', 'shirt', 56, 'Pure Garment Dyed Cotton Shirt', '', 'shirt-1.jpg', 50, 'admin fahad', NULL, 7, 45.00, 'shirt-2.jpg', NULL, '2023-06-16 18:33:06', '2023-06-16 23:33:06', 0x31),
(3, 'men', 'Jacket', 'Jacket', 65, 'MEN Yarn Fleece Full-Zip Jacket', '', 'jacket-5.jpg', 50, 'admin fahad', NULL, 7, 58.00, 'jacket-6.jpg', NULL, '2023-06-16 18:33:06', '2023-06-16 23:33:06', 0x31),
(4, 'women', 'Skirt', 'skirt', 25, 'Black Floral Wrap Midi Skirt', '', 'clothes-3.jpg', 50, 'admin fahad', NULL, 7, 35.00, 'clothes-4.jpg', NULL, '2023-06-16 18:33:06', '2023-06-16 23:33:06', 0x31),
(5, 'men', 'Casual Shoes', 'casual', 105, 'Casual Men Brown shoes', '', 'shoe-2.jpg', 50, 'admin fahad', NULL, 7, 99.00, 'shoe-2_1.jpg', NULL, '2023-06-16 18:33:06', '2023-06-16 23:33:06', 0x31),
(6, 'men', 'Watch', 'watches', 170, 'Pocket Watch Leather Pouch', '', 'watch-3.jpg', 50, 'admin fahad', NULL, 7, 150.00, 'watch-4.jpg', NULL, '2023-06-16 18:33:06', '2023-06-16 23:33:06', 0x31),
(7, 'men', 'Watch', 'watches', 120, 'Smart watche Vital Plus', '', 'watch-1.jpg', 50, 'admin fahad', NULL, 7, 100.00, 'watch-2.jpg', NULL, '2023-06-16 18:33:06', '2023-06-16 23:33:06', 0x31),
(8, 'women', 'Party Wear Shoes', 'party wear', 25, 'Womens Party Wear Shoes', '', 'party-wear-1.jpg', 50, 'admin fahad', NULL, 7, 30.00, 'party-wear-2.jpg', NULL, '2023-06-16 18:33:06', '2023-06-16 23:33:06', 0x31),
(9, 'men', 'Jacket', 'jacket', 45, 'Mens Winter Leathers Jackets', '', 'jacket-1.jpg', 50, 'admin fahad', NULL, 7, 32.00, 'jacket-2.jpg', NULL, '2023-06-16 18:33:06', '2023-06-16 23:33:06', 0x31),
(10, 'men', 'Sports Shoes', 'sports', 64, 'Trekking & Running Shoes - black', '', 'sports-2.jpg', 50, 'admin fahad', NULL, 7, 58.00, 'sports-4.jpg', NULL, '2023-06-16 18:33:06', '2023-06-16 23:33:06', 0x31),
(11, 'men', 'Formal Shoes', 'formal', 65, 'Men Leather Formal Wear shoes', '', 'shoe-1.jpg', 50, 'admin fahad', NULL, 7, 50.00, 'shoe-1_1.jpg', NULL, '2023-06-16 18:33:06', '2023-06-16 23:33:06', 0x31),
(12, 'women', 'Shorts', 'shorts', 85, 'Better Basics French Terry Sweatshorts', '', 'shorts-1.jpg', 50, 'admin fahad', NULL, 7, 78.00, 'shorts-2.jpg', NULL, '2023-06-16 18:33:06', '2023-06-16 23:33:06', 0x31),
(13, 'men', 'Jeans', 'Jeans', 12, 'New Jeans for men. New summer Sale', '16,6,2023', 'pant2.png', 100, 'FahadAdmin', NULL, NULL, 10.00, '', NULL, '2023-06-16 19:52:58', '2023-06-17 01:00:16', 0x31),
(14, 'men', 'Hoodie', 'Red Hoddie', 15, 'Red hoddie for men. New Design and bright color.', '16,6,2023', 'f3.png', 69, 'FahadAdmin', NULL, NULL, 12.00, '', NULL, '2023-06-16 20:02:15', '2023-06-17 01:02:15', 0x31),
(15, 'men', 'T-Shirt', 'Black T Shirt', 13, 'Black T Shit with half sleeves for men. Both summer and winter. High fashon.', '16,6,2023', 'p4.png', 99, 'FahadAdmin', NULL, NULL, 10.00, '', NULL, '2023-06-16 20:05:07', '2023-06-17 01:05:07', 0x31),
(16, 'all', 'T-Shirt', 'T-Shirts', 16, 'Different color of t shirts pack of 3', '17,6,2023', 'p17.png', 150, 'FahadAdmin', NULL, NULL, 13.00, '', NULL, '2023-06-17 05:22:01', '2023-06-17 10:22:01', 0x31),
(17, 'all', 'Glasses', 'Glasses & Lens', 12, 'Bock blue light. Improve night vision. And stylish look.', '17,6,2023', 'g3.png', 50, 'FahadAdmin', NULL, NULL, 10.00, '', NULL, '2023-06-17 05:24:06', '2023-06-17 10:24:06', 0x31),
(18, 'all', 'Jeans', 'Jeans', 10, 'Jeans for both men and women.', '17,6,2023', 'f9.png', 100, 'FahadAdmin', NULL, NULL, 8.00, '', NULL, '2023-06-17 06:26:01', '2023-06-17 11:26:01', 0x31),
(19, 'all', 'Dress', 'Kitty Dress', 15, 'Kitty bear dress for kids.', '17,6,2023', 'f10.png', 100, 'FahadAdmin', NULL, NULL, 12.00, '', NULL, '2023-06-17 06:27:33', '2023-06-17 11:27:33', 0x31),
(20, 'all', 'Bag', 'Bag', 15, 'Large size bag for carrying different accessories.', '17,6,2023', 'topcard3.png', 50, 'FahadAdmin', NULL, NULL, 12.00, '', NULL, '2023-06-17 06:28:36', '2023-06-17 11:28:36', 0x31),
(21, 'all', 'Sports Shoes', 'Sports Shoes', 15, 'Sport shoes. Jogging shoes. Gym shoes.', '17,6,2023', 'b2.png', 20, 'FahadAdmin', NULL, NULL, 12.00, '', NULL, '2023-06-17 06:30:08', '2023-06-17 11:30:08', 0x31),
(22, 'women', 'Purse', 'Purse', 15, 'Ornage purse for stunning look and carry accessories.', '17,6,2023', 'p5.png', 100, 'FahadAdmin', NULL, NULL, 12.00, '', NULL, '2023-06-17 06:31:26', '2023-06-17 11:31:26', 0x31),
(23, 'women', 'Slippers', 'Casual Slippers', 15, 'Casual function slippers for female.', '17,6,2023', 'p7.png', 100, 'FahadAdmin', NULL, NULL, 12.00, '', NULL, '2023-06-17 06:32:33', '2023-06-17 11:32:33', 0x31),
(24, 'all', 'Perfume', 'Clothes Perfume', 15, 'Fresh Fragrance perfume. Indoor and outdoor', '17,6,2023', 'perfume.png', 100, 'FahadAdmin', NULL, NULL, 12.00, '', NULL, '2023-06-17 06:33:46', '2023-06-17 11:33:46', 0x31),
(25, 'women', 'Formal Shoes', 'Formal Shoes', 15, 'Formal shoes for female. Indoor and outdoor.', '17,6,2023', 'p10.jpg', 100, 'FahadAdmin', NULL, NULL, 12.00, '', NULL, '2023-06-17 06:35:09', '2023-06-17 11:35:09', 0x31),
(26, 'women', 'Socks', 'Socks', 10, 'Socks for indoor and outdoor.', '17,6,2023', 'f2.png', 100, 'FahadAdmin', NULL, NULL, 5.00, '', NULL, '2023-06-17 06:38:25', '2023-06-17 11:38:25', 0x31);

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(10) NOT NULL,
  `name` varchar(30) NOT NULL,
  `review` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `name`, `review`) VALUES
(1, 'Iqso Fhd', 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quidem ad\r\n            fugiat, itaque dolore culpa ipsa fuga, illum, maxime exercitationem\r\n            commodi nihil nobis nulla similique quibusdam sed expedita provident'),
(2, 'IFAD', 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quidem ad\r\n            fugiat, itaque dolore culpa ipsa fuga, illum, maxime exercitationem\r\n            commodi nihil nobis nulla similique quibusdam sed expedita provident'),
(3, 'Eva Silk', 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Quidem ad\r\n            fugiat, itaque dolore culpa ipsa fuga, illum, maxime exercitationem\r\n            commodi nihil nobis nulla similique quibusdam sed expedita provident');

-- --------------------------------------------------------

--
-- Table structure for table `section`
--

CREATE TABLE `section` (
  `id` int(10) NOT NULL,
  `name` varchar(50) NOT NULL,
  `status` binary(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `section`
--

INSERT INTO `section` (`id`, `name`, `status`) VALUES
(2, 'new_arrival', 0x31),
(3, 'trending', 0x31),
(4, 'top_rated', 0x31),
(5, 'deal_of_day', 0x31),
(6, 'best_seller', 0x31),
(7, 'new_products', 0x31);

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `website_name` varchar(60) NOT NULL,
  `website_logo` varchar(50) NOT NULL,
  `website_footer` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`website_name`, `website_logo`, `website_footer`) VALUES
('HCA E-Commerce', 'HCA-E-COMMERCE.png', 'HCA E-Commerce');

-- --------------------------------------------------------

--
-- Table structure for table `favorites`
--

CREATE TABLE `favorites` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(100) NOT NULL,
  `product_id` int(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bags`
--
ALTER TABLE `bags`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `banner`
--
ALTER TABLE `banner`
  ADD PRIMARY KEY (`banner_id`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `category_bar`
--
ALTER TABLE `category_bar`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `category_electronics`
--
ALTER TABLE `category_electronics`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `clothes`
--
ALTER TABLE `clothes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cosmetics`
--
ALTER TABLE `cosmetics`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`customer_id`),
  ADD UNIQUE KEY `customer_email` (`customer_email`);

--
-- Indexes for table `deal_of_the_day`
--
ALTER TABLE `deal_of_the_day`
  ADD PRIMARY KEY (`deal_id`);

--
-- Indexes for table `footwear`
--
ALTER TABLE `footwear`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `glasses`
--
ALTER TABLE `glasses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jewelry`
--
ALTER TABLE `jewelry`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `perfume`
--
ALTER TABLE `perfume`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `banner`
--
ALTER TABLE `banner`
  MODIFY `banner_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `category_bar`
--
ALTER TABLE `category_bar`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `customer_id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(100) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(100) NOT NULL,
  `customer_id` int(100) NOT NULL,
  `order_total` decimal(10,2) NOT NULL,
  `order_status` enum('pending','confirmed','shipped','delivered','cancelled') NOT NULL DEFAULT 'pending',
  `payment_status` enum('pending','paid','failed','refunded') NOT NULL DEFAULT 'pending',
  `shipping_address` text NOT NULL,
  `order_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `order_item_id` int(100) NOT NULL,
  `order_id` int(100) NOT NULL,
  `product_id` int(100) NOT NULL,
  `quantity` int(10) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `transaction_id` int(100) NOT NULL,
  `order_id` int(100) NOT NULL,
  `transaction_type` enum('payment','refund') NOT NULL DEFAULT 'payment',
  `amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `transaction_status` enum('pending','success','failed') NOT NULL DEFAULT 'pending',
  `transaction_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `reference_id` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`transaction_id`),
  ADD KEY `order_id` (`order_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(100) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(100) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `transaction_id` int(100) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customer` (`customer_id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
