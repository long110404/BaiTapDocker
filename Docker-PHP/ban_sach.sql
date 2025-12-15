
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


DROP TABLE IF EXISTS `books`;
CREATE TABLE IF NOT EXISTS `books` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `author` varchar(100) DEFAULT NULL,
  `price` decimal(10,0) NOT NULL,
  `description` text,
  `image` varchar(255) DEFAULT NULL,
  `category_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `quantity` int DEFAULT '0' COMMENT 'Số lượng tồn kho',
  `publisher` varchar(255) DEFAULT NULL COMMENT 'Nhà xuất bản',
  `publish_year` int DEFAULT NULL COMMENT 'Năm xuất bản',
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


INSERT INTO `books` (`id`, `title`, `author`, `price`, `description`, `image`, `category_id`, `created_at`, `quantity`, `publisher`, `publish_year`) VALUES
(1, 'Đắc Nhân Tâm', 'Dale Carnegie', 80000, 'Cuốn sách hay nhất mọi thời đại về nghệ thuật giao tiếp.', 'OIP (1).webp', 4, '2025-12-08 14:50:23', 9, NULL, NULL),
(2, 'Nhà Giả Kim', 'Paulo Coelho', 75000, '', 'OIP.webp', 2, '2025-12-08 14:50:23', 6, '', 0),
(3, 'Doremon Tập 1', 'Fujiko F. Fujio', 20000, 'hay lắm á', 'Bia_truyen_Doraemon_tap_1_1992-2009_VN-1-193x278.jpg', 3, '2025-12-08 14:50:23', 11, '', 0);


DROP TABLE IF EXISTS `categories`;
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;



INSERT INTO `categories` (`id`, `name`) VALUES
(1, 'Sách Kinh Tế'),
(2, 'Sách Văn Học'),
(3, 'Truyện Tranh'),
(4, 'Sách Kỹ Năng'),
(5, 'Sách Toán Học');


DROP TABLE IF EXISTS `orders`;
CREATE TABLE IF NOT EXISTS `orders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `fullname` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `address` varchar(255) NOT NULL,
  `total_price` decimal(10,0) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `orders` (`id`, `user_id`, `fullname`, `phone`, `address`, `total_price`, `status`, `created_at`) VALUES
(1, 2, '', '', '', 155000, 'Pending', '2025-12-08 18:20:34'),
(2, 2, '', '', '', 20000, 'Shipping', '2025-12-08 18:20:34'),
(3, 5, 'trấn thành', '0813584019', 'hà nội', 80000, 'Cancelled', '2025-12-09 09:11:08'),
(4, 5, 'trấn thành', '0813584019', 'sai gon\r\n', 20000, 'Cancelled', '2025-12-09 10:46:21');


DROP TABLE IF EXISTS `order_details`;
CREATE TABLE IF NOT EXISTS `order_details` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_id` int DEFAULT NULL,
  `book_id` int DEFAULT NULL,
  `quantity` int NOT NULL,
  `price` decimal(10,0) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `book_id` (`book_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


INSERT INTO `order_details` (`id`, `order_id`, `book_id`, `quantity`, `price`) VALUES
(1, 1, 1, 1, 75000),
(2, 1, 2, 1, 80000),
(3, 2, 3, 1, 20000),
(4, 3, 1, 1, 80000),
(5, 4, 3, 1, 20000);


DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `fullname` varchar(100) DEFAULT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `role` tinyint DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `is_locked` tinyint DEFAULT '0' COMMENT '0: Hoạt động, 1: Bị khóa',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


INSERT INTO `users` (`id`, `username`, `phone`, `password`, `fullname`, `full_name`, `email`, `role`, `created_at`, `is_locked`) VALUES
(1, 'admin', NULL, 'e10adc3949ba59abbe56e057f20f883e', NULL, 'Quản Trị Viên', NULL, 1, '2025-12-08 14:50:22', 0),
(7, 'nhanvien', '0911111111', 'e10adc3949ba59abbe56e057f20f883e', 'le minh hieu', NULL, '', 2, '2025-12-11 11:20:06', 0),
(6, 'aloalo', '0947382738', 'e10adc3949ba59abbe56e057f20f883e', 'Duy ', NULL, 'haha@gmail.com', 0, '2025-12-09 10:44:24', 0),
(5, 'xinchao', '0937437482', 'e10adc3949ba59abbe56e057f20f883e', 'trấn thành', NULL, '', 0, '2025-12-09 07:17:28', 0);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
