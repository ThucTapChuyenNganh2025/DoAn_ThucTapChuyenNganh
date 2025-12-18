-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th12 18, 2025 lúc 06:43 PM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `webchotot`
--

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fullname` varchar(150) DEFAULT NULL,
  `role` enum('superadmin','moderator','support') DEFAULT 'moderator',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `fullname`, `role`, `created_at`) VALUES
(2, 'admin', '$2y$10$kPy.jBEtWSaC8hu4IWvusevShyMtVqvuzZG/LEXf.dN32pt5m6TRO', 'Son', 'moderator', '2025-11-28 13:36:43');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `slug` varchar(200) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `categories`
--

INSERT INTO `categories` (`id`, `name`, `slug`, `parent_id`, `created_at`) VALUES
(1, 'Điện tử', NULL, NULL, '2025-11-16 12:08:00'),
(2, 'Thể thao', NULL, NULL, '2025-12-18 14:13:39'),
(3, 'Thời trang', NULL, NULL, '2025-12-18 14:14:17');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `conversations`
--

CREATE TABLE `conversations` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `buyer_id` int(11) NOT NULL,
  `seller_id` int(11) NOT NULL,
  `last_message_id` int(11) DEFAULT NULL,
  `buyer_unread` int(11) DEFAULT 0,
  `seller_unread` int(11) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `conversations`
--

INSERT INTO `conversations` (`id`, `product_id`, `buyer_id`, `seller_id`, `last_message_id`, `buyer_unread`, `seller_unread`, `created_at`, `updated_at`) VALUES
(1, 14, 8, 3, 8, 0, 0, '2025-12-18 16:21:27', '2025-12-18 16:46:32'),
(2, 15, 3, 5, 7, 0, 1, '2025-12-18 16:38:39', '2025-12-18 16:38:39'),
(3, 12, 8, 3, 11, 0, 0, '2025-12-18 16:46:24', '2025-12-18 17:11:43'),
(4, 15, 8, 5, 10, 0, 1, '2025-12-18 17:11:17', '2025-12-18 17:11:17');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `favorites`
--

CREATE TABLE `favorites` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `favorites`
--

INSERT INTO `favorites` (`id`, `user_id`, `product_id`, `created_at`) VALUES
(8, 3, 13, '2025-12-18 15:31:27'),
(11, 3, 15, '2025-12-18 16:38:29');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `locations`
--

CREATE TABLE `locations` (
  `id` int(11) NOT NULL,
  `province` varchar(100) NOT NULL,
  `district` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `locations`
--

INSERT INTO `locations` (`id`, `province`, `district`, `created_at`) VALUES
(2, 'Thành phố Hà Nội', NULL, '2025-11-16 13:42:52'),
(3, 'Thành phố Hồ Chí Minh', NULL, '2025-11-16 13:42:52'),
(4, 'Thành phố Hải Phòng', NULL, '2025-11-16 13:42:52'),
(5, 'Thành phố Đà Nẵng', NULL, '2025-11-16 13:42:52'),
(6, 'Thành phố Cần Thơ', NULL, '2025-11-16 13:42:52'),
(7, 'Tỉnh Hà Giang', NULL, '2025-11-16 13:42:52'),
(8, 'Tỉnh Cao Bằng', NULL, '2025-11-16 13:42:52'),
(9, 'Tỉnh Lai Châu', NULL, '2025-11-16 13:42:52'),
(10, 'Tỉnh Lào Cai', NULL, '2025-11-16 13:42:52'),
(11, 'Tỉnh Tuyên Quang', NULL, '2025-11-16 13:42:52'),
(12, 'Tỉnh Lạng Sơn', NULL, '2025-11-16 13:42:52'),
(13, 'Tỉnh Bắc Kạn', NULL, '2025-11-16 13:42:52'),
(14, 'Tỉnh Thái Nguyên', NULL, '2025-11-16 13:42:52'),
(15, 'Tỉnh Yên Bái', NULL, '2025-11-16 13:42:52'),
(16, 'Tỉnh Sơn La', NULL, '2025-11-16 13:42:52'),
(17, 'Tỉnh Phú Thọ', NULL, '2025-11-16 13:42:52'),
(18, 'Tỉnh Vĩnh Phúc', NULL, '2025-11-16 13:42:52'),
(19, 'Tỉnh Quảng Ninh', NULL, '2025-11-16 13:42:52'),
(20, 'Tỉnh Bắc Giang', NULL, '2025-11-16 13:42:52'),
(21, 'Tỉnh Bắc Ninh', NULL, '2025-11-16 13:42:52'),
(22, 'Tỉnh Hoà Bình', NULL, '2025-11-16 13:42:52'),
(23, 'Tỉnh Điện Biên', NULL, '2025-11-16 13:42:52'),
(24, 'Tỉnh Hải Dương', NULL, '2025-11-16 13:42:52'),
(25, 'Tỉnh Hưng Yên', NULL, '2025-11-16 13:42:52'),
(26, 'Tỉnh Thái Bình', NULL, '2025-11-16 13:42:52'),
(27, 'Tỉnh Hà Nam', NULL, '2025-11-16 13:42:52'),
(28, 'Tỉnh Nam Định', NULL, '2025-11-16 13:42:52'),
(29, 'Tỉnh Ninh Bình', NULL, '2025-11-16 13:42:52'),
(30, 'Tỉnh Thanh Hoá', NULL, '2025-11-16 13:42:52'),
(31, 'Tỉnh Nghệ An', NULL, '2025-11-16 13:42:52'),
(32, 'Tỉnh Hà Tĩnh', NULL, '2025-11-16 13:42:52'),
(33, 'Tỉnh Quảng Bình', NULL, '2025-11-16 13:42:52'),
(34, 'Tỉnh Quảng Trị', NULL, '2025-11-16 13:42:52'),
(35, 'Tỉnh Thừa Thiên Huế', NULL, '2025-11-16 13:42:52'),
(36, 'Tỉnh Quảng Nam', NULL, '2025-11-16 13:42:52'),
(37, 'Tỉnh Quảng Ngãi', NULL, '2025-11-16 13:42:52'),
(38, 'Tỉnh Bình Định', NULL, '2025-11-16 13:42:52'),
(39, 'Tỉnh Phú Yên', NULL, '2025-11-16 13:42:52'),
(40, 'Tỉnh Khánh Hoà', NULL, '2025-11-16 13:42:52'),
(41, 'Tỉnh Ninh Thuận', NULL, '2025-11-16 13:42:52'),
(42, 'Tỉnh Bình Thuận', NULL, '2025-11-16 13:42:52'),
(43, 'Tỉnh Kon Tum', NULL, '2025-11-16 13:42:52'),
(44, 'Tỉnh Gia Lai', NULL, '2025-11-16 13:42:52'),
(45, 'Tỉnh Đắk Lắk', NULL, '2025-11-16 13:42:52'),
(46, 'Tỉnh Đắk Nông', NULL, '2025-11-16 13:42:52'),
(47, 'Tỉnh Lâm Đồng', NULL, '2025-11-16 13:42:52'),
(48, 'Tỉnh Bình Phước', NULL, '2025-11-16 13:42:52'),
(49, 'Tỉnh Tây Ninh', NULL, '2025-11-16 13:42:52'),
(50, 'Tỉnh Bình Dương', NULL, '2025-11-16 13:42:52'),
(51, 'Tỉnh Đồng Nai', NULL, '2025-11-16 13:42:52'),
(52, 'Tỉnh Bà Rịa - Vũng Tàu', NULL, '2025-11-16 13:42:52'),
(53, 'Tỉnh Long An', NULL, '2025-11-16 13:42:52'),
(54, 'Tỉnh Tiền Giang', NULL, '2025-11-16 13:42:52'),
(55, 'Tỉnh Bến Tre', NULL, '2025-11-16 13:42:52'),
(56, 'Tỉnh Trà Vinh', NULL, '2025-11-16 13:42:52'),
(57, 'Tỉnh Vĩnh Long', NULL, '2025-11-16 13:42:52'),
(58, 'Tỉnh Đồng Tháp', NULL, '2025-11-16 13:42:52'),
(59, 'Tỉnh An Giang', NULL, '2025-11-16 13:42:52'),
(60, 'Tỉnh Kiên Giang', NULL, '2025-11-16 13:42:52'),
(61, 'Tỉnh Hậu Giang', NULL, '2025-11-16 13:42:52'),
(62, 'Tỉnh Sóc Trăng', NULL, '2025-11-16 13:42:52'),
(63, 'Tỉnh Bạc Liêu', NULL, '2025-11-16 13:42:52'),
(64, 'Tỉnh Cà Mau', NULL, '2025-11-16 13:42:52');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `conversation_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `messages`
--

INSERT INTO `messages` (`id`, `conversation_id`, `sender_id`, `message`, `is_read`, `created_at`) VALUES
(1, 1, 8, 'cccc', 1, '2025-12-18 16:21:27'),
(2, 1, 3, 'cc', 1, '2025-12-18 16:21:53'),
(3, 1, 3, 'cc', 1, '2025-12-18 16:28:59'),
(4, 1, 3, 'dừaegdrjkyfdudfghfgkujyeet', 1, '2025-12-18 16:30:59'),
(5, 1, 8, 'ccccc', 1, '2025-12-18 16:31:14'),
(6, 1, 3, 'clclclcllclcl', 1, '2025-12-18 16:34:06'),
(7, 2, 3, 'cc', 0, '2025-12-18 16:38:39'),
(8, 1, 8, 'cc', 1, '2025-12-18 16:46:16'),
(9, 3, 8, 'ccccc', 1, '2025-12-18 16:46:24'),
(10, 4, 8, 'ccc', 0, '2025-12-18 17:11:17'),
(11, 3, 8, 'ccccc', 1, '2025-12-18 17:11:34');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `seller_id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `currency` varchar(10) DEFAULT 'VND',
  `status` enum('pending','approved','rejected','hidden') DEFAULT 'pending',
  `views` int(11) DEFAULT 0,
  `location_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `products`
--

INSERT INTO `products` (`id`, `seller_id`, `category_id`, `title`, `description`, `price`, `currency`, `status`, `views`, `location_id`, `created_at`, `updated_at`) VALUES
(12, 3, 1, 'laptop', 'mới mua', 1000000.00, 'VND', 'approved', 0, NULL, '2025-11-28 16:13:46', '2025-11-28 16:15:38'),
(13, 3, 1, 'đồng hồ', 'cc', 20000.00, 'VND', 'approved', 0, NULL, '2025-11-28 16:15:17', '2025-11-28 16:15:40'),
(14, 3, 1, 'iphone', 'ccccc', 3500000.00, 'VND', 'approved', 0, 38, '2025-11-28 16:17:05', '2025-12-18 17:22:43'),
(15, 5, 1, 'tai nghe', 'ccccccccc', 100000.00, 'VND', 'approved', 0, NULL, '2025-11-28 16:25:57', '2025-11-28 16:26:20'),
(17, 3, 1, 'ip16', 'mới', 100000.00, 'VND', 'approved', 0, 38, '2025-12-18 16:55:04', '2025-12-18 17:37:42'),
(18, 8, 1, 'laptop', 'CCCC', 1000000.00, 'VND', 'approved', 0, NULL, '2025-12-18 16:55:38', '2025-12-18 16:56:48');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `product_images`
--

CREATE TABLE `product_images` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `product_images`
--

INSERT INTO `product_images` (`id`, `product_id`, `filename`, `sort_order`, `created_at`) VALUES
(7, 12, 'uploads/1764346426_6288_laptop.jpg', 0, '2025-11-28 16:13:46'),
(8, 13, 'uploads/1764346517_5439_singel-product-item.jpg', 0, '2025-11-28 16:15:17'),
(9, 14, 'uploads/1764346625_5416_iphone.jpg', 0, '2025-11-28 16:17:05'),
(10, 15, 'uploads/1764347157_5711_headphones.jpg', 0, '2025-11-28 16:25:57'),
(12, 17, 'uploads/1766076904_1948_download.jpg', 0, '2025-12-18 16:55:04'),
(13, 18, 'uploads/1766076938_5103_download (1).jpg', 0, '2025-12-18 16:55:38');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `ratings`
--

CREATE TABLE `ratings` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `rated_user_id` int(11) NOT NULL,
  `rater_user_id` int(11) NOT NULL,
  `rating` tinyint(4) NOT NULL,
  `comment` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `ratings`
--

INSERT INTO `ratings` (`id`, `product_id`, `rated_user_id`, `rater_user_id`, `rating`, `comment`, `created_at`) VALUES
(1, 18, 8, 3, 5, '', '2025-12-18 17:07:55'),
(2, 15, 5, 8, 5, '', '2025-12-18 17:08:20'),
(3, 15, 5, 3, 5, 'cccc', '2025-12-18 17:08:57');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `reports`
--

CREATE TABLE `reports` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `reporter_id` int(11) NOT NULL,
  `reason` text NOT NULL,
  `status` enum('pending','reviewed') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(150) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `bio` text DEFAULT NULL,
  `location_id` int(11) DEFAULT NULL,
  `is_verified` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `password`, `bio`, `location_id`, `is_verified`, `created_at`) VALUES
(3, 'Lê Ngọc Sơn', 'sonhc67896789@gmail.com', '0339830977', '$2y$10$2Ob6.8V09rpfLwfXSQe.JOlvQWTxus72.93wUa8lMhZaZZ0ZwIRcS', 'clcc', 38, 0, '2025-11-16 13:46:51'),
(5, 'Sơn', '1774096713abcd@gmail.com', '0123456789', '$2y$10$AIPM2B4ObOKR2p5gDIreEe0TrtSCTsLCCodiUs.kjIUR2bHHZOSGa', 'ccccc', 11, 0, '2025-11-28 16:24:50'),
(8, 'Tao Ten Son', 'cc@gmail.com', '0987654321', '$2y$10$HjxnwdaVcaqWX3Zeob.qze8LGFoXdj.ryTQ.E.d1FjXwDF0mqCxfu', 'ccccccccccc', 9, 0, '2025-12-18 16:21:09');

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Chỉ mục cho bảng `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `parent_id` (`parent_id`);

--
-- Chỉ mục cho bảng `conversations`
--
ALTER TABLE `conversations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_conversation` (`product_id`,`buyer_id`,`seller_id`),
  ADD KEY `buyer_id` (`buyer_id`),
  ADD KEY `seller_id` (`seller_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Chỉ mục cho bảng `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Chỉ mục cho bảng `locations`
--
ALTER TABLE `locations`
  ADD PRIMARY KEY (`id`);

--
-- Chỉ mục cho bảng `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `conversation_id` (`conversation_id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `created_at` (`created_at`);

--
-- Chỉ mục cho bảng `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `seller_id` (`seller_id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `location_id` (`location_id`);

--
-- Chỉ mục cho bảng `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Chỉ mục cho bảng `ratings`
--
ALTER TABLE `ratings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `rated_user_id` (`rated_user_id`),
  ADD KEY `rater_user_id` (`rater_user_id`);

--
-- Chỉ mục cho bảng `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `reporter_id` (`reporter_id`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `phone` (`phone`),
  ADD KEY `location_id` (`location_id`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT cho bảng `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `conversations`
--
ALTER TABLE `conversations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT cho bảng `favorites`
--
ALTER TABLE `favorites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT cho bảng `locations`
--
ALTER TABLE `locations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT cho bảng `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT cho bảng `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT cho bảng `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT cho bảng `ratings`
--
ALTER TABLE `ratings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `categories`
--
ALTER TABLE `categories`
  ADD CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `favorites`
--
ALTER TABLE `favorites`
  ADD CONSTRAINT `favorites_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `favorites_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`seller_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `products_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `products_ibfk_3` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `product_images_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `ratings`
--
ALTER TABLE `ratings`
  ADD CONSTRAINT `ratings_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ratings_ibfk_2` FOREIGN KEY (`rated_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `ratings_ibfk_3` FOREIGN KEY (`rater_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `reports`
--
ALTER TABLE `reports`
  ADD CONSTRAINT `reports_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reports_ibfk_2` FOREIGN KEY (`reporter_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
