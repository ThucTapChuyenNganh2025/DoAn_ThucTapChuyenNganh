-- =====================================================
-- BẢNG HỘI THOẠI (CONVERSATIONS)
-- Lưu thông tin các cuộc hội thoại giữa người mua và người bán
-- =====================================================
CREATE TABLE IF NOT EXISTS `conversations` (
  `id` int NOT NULL AUTO_INCREMENT,
  `product_id` int NOT NULL COMMENT 'Sản phẩm liên quan',
  `buyer_id` int NOT NULL COMMENT 'Người mua (người bắt đầu chat)',
  `seller_id` int NOT NULL COMMENT 'Người bán (chủ sản phẩm)',
  `last_message_id` int DEFAULT NULL COMMENT 'ID tin nhắn cuối cùng',
  `buyer_unread` int DEFAULT 0 COMMENT 'Số tin chưa đọc của buyer',
  `seller_unread` int DEFAULT 0 COMMENT 'Số tin chưa đọc của seller',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_conversation` (`product_id`, `buyer_id`, `seller_id`),
  KEY `buyer_id` (`buyer_id`),
  KEY `seller_id` (`seller_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- BẢNG TIN NHẮN (MESSAGES)
-- Lưu nội dung tin nhắn trong các cuộc hội thoại
-- =====================================================
CREATE TABLE IF NOT EXISTS `messages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `conversation_id` int NOT NULL COMMENT 'ID cuộc hội thoại',
  `sender_id` int NOT NULL COMMENT 'Người gửi',
  `message` text NOT NULL COMMENT 'Nội dung tin nhắn',
  `is_read` tinyint(1) DEFAULT 0 COMMENT '0: chưa đọc, 1: đã đọc',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `conversation_id` (`conversation_id`),
  KEY `sender_id` (`sender_id`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- THÊM FOREIGN KEYS
-- =====================================================
ALTER TABLE `conversations`
  ADD CONSTRAINT `fk_conv_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_conv_buyer` FOREIGN KEY (`buyer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_conv_seller` FOREIGN KEY (`seller_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

ALTER TABLE `messages`
  ADD CONSTRAINT `fk_msg_conversation` FOREIGN KEY (`conversation_id`) REFERENCES `conversations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_msg_sender` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
