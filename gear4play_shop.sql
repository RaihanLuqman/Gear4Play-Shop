-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 28, 2025 at 09:50 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gear4play_shop`
--

-- --------------------------------------------------------

--
-- Table structure for table `address`
--

CREATE TABLE `address` (
  `id_address` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `address_line` text NOT NULL,
  `city` varchar(50) NOT NULL,
  `postal_code` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `address`
--

INSERT INTO `address` (`id_address`, `id_user`, `address_line`, `city`, `postal_code`) VALUES
(1, 1, 'Reni', 'Tangerang', '12246'),
(2, 2, 'Reni', 'Tangerang', '12246'),
(3, 3, 'Reni', 'Tangerang', '12246'),
(4, 4, 'Reni', 'Tangerang', '12246'),
(5, 5, 'Pamulang Bambu Apus', 'Tangerang', '12246'),
(6, 6, 'Reni', 'Tangerang', '12246'),
(7, 7, 'Samping unpam', 'Tangerang', '12221'),
(8, 8, 'Pamulang Bambu Apus', 'Kota Tangsel', '15415'),
(9, 9, 'Reni', 'Tangerang', '12246');

-- --------------------------------------------------------

--
-- Table structure for table `billboard`
--

CREATE TABLE `billboard` (
  `id_billboard` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `image` varchar(255) NOT NULL,
  `id_product` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `billboard`
--

INSERT INTO `billboard` (`id_billboard`, `title`, `description`, `image`, `id_product`, `created_at`) VALUES
(1, 'Rexus Mouse gaming Daxa Air IV Pro', 'Mouse Gaming Elite', 'image-removebg-preview (2) 1 (1).png', 5, '2025-01-10 16:00:41');

-- --------------------------------------------------------

--
-- Table structure for table `buktipengiriman`
--

CREATE TABLE `buktipengiriman` (
  `id_bukti` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `idPengiriman` int(11) NOT NULL,
  `proof_image` text NOT NULL,
  `proof_text` text DEFAULT NULL,
  `proof_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id_cart` int(11) NOT NULL,
  `id_pelanggan` int(11) NOT NULL,
  `created_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id_cart`, `id_pelanggan`, `created_date`) VALUES
(1, 2, '2024-12-26 20:51:30'),
(2, 3, '2024-12-27 17:32:46'),
(3, 6, '2025-01-10 09:37:51'),
(4, 7, '2025-07-03 13:47:52'),
(5, 9, '2025-07-23 16:27:13');

-- --------------------------------------------------------

--
-- Table structure for table `cartitem`
--

CREATE TABLE `cartitem` (
  `id_cart_item` int(11) NOT NULL,
  `id_cart` int(11) NOT NULL,
  `id_product` int(11) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `total_price` decimal(15,2) NOT NULL,
  `product_image` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cartitem`
--

INSERT INTO `cartitem` (`id_cart_item`, `id_cart`, `id_product`, `product_name`, `quantity`, `price`, `total_price`, `product_image`) VALUES
(61, 3, 4, 'Oca 259 Gemink Chair', 1, 782000.00, 782000.00, ''),
(62, 3, 5, 'Rexus Mouse gaming Daxa Air IV Pro', 1, 789000.00, 789000.00, ''),
(83, 1, 4, 'Oca 259 Gemink Chair', 1, 782000.00, 782000.00, ''),
(88, 4, 5, 'Rexus Mouse gaming Daxa Air IV Pro', 3, 789000.00, 2367000.00, '');

-- --------------------------------------------------------

--
-- Table structure for table `chat`
--

CREATE TABLE `chat` (
  `id_chat` int(11) NOT NULL,
  `message` text NOT NULL,
  `sent_by` int(11) NOT NULL,
  `sent_to` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `chat`
--

INSERT INTO `chat` (`id_chat`, `message`, `sent_by`, `sent_to`, `timestamp`) VALUES
(1, 'halo bang', 3, 4, '2025-01-02 14:08:09'),
(2, 'apaan', 4, 3, '2025-01-02 14:08:19'),
(3, 'sa', 4, 3, '2025-01-03 16:02:42'),
(4, 'a', 4, 3, '2025-01-03 16:02:43'),
(5, 'a', 4, 3, '2025-01-03 16:03:58'),
(6, 'ss\r\n', 4, 3, '2025-01-03 16:06:17'),
(7, 'ss\r\n', 4, 3, '2025-01-03 16:06:19'),
(8, 'oi min', 2, 4, '2025-01-03 16:14:04'),
(9, 's', 4, 4, '2025-01-03 16:14:23'),
(10, 'p', 2, 4, '2025-01-03 16:14:51'),
(11, 'paan', 4, 2, '2025-01-03 16:15:02'),
(12, 'gpp', 2, 4, '2025-01-03 16:15:06'),
(13, 'wkkw error lgi', 2, 4, '2025-01-03 16:15:21'),
(14, 'gpp kan', 2, 4, '2025-01-03 16:16:01'),
(15, 'sans', 4, 2, '2025-01-03 16:16:04'),
(16, 'nice min', 2, 4, '2025-01-03 16:16:09'),
(17, 'sans', 4, 2, '2025-01-03 16:17:37'),
(18, 's', 2, 4, '2025-01-03 16:19:45'),
(19, 'sa', 4, 3, '2025-01-03 16:30:02'),
(20, 's', 2, 4, '2025-01-03 16:41:29'),
(21, 's', 2, 4, '2025-01-03 16:41:58'),
(22, 'halooooooooooooooooooooooooooooooooooooooooooo', 2, 4, '2025-01-03 16:42:07'),
(23, 'tes', 3, 4, '2025-01-09 15:44:39'),
(24, 'paan', 4, 3, '2025-01-09 15:44:50'),
(25, 'halo', 6, 4, '2025-01-10 02:47:05'),
(26, 'halo jga', 4, 6, '2025-01-10 02:47:13'),
(27, 'oi', 3, 4, '2025-01-21 13:08:18'),
(28, 'tes', 4, 3, '2025-01-21 13:08:23'),
(29, 'tes', 4, 4, '2025-07-02 09:34:41'),
(30, 'tes', 4, 2, '2025-07-02 09:34:54'),
(31, 'tes', 4, 4, '2025-07-02 09:35:00'),
(32, 'lah', 4, 4, '2025-07-02 09:35:07'),
(33, 'tes', 4, 2, '2025-07-02 09:35:11'),
(34, 'ngeteh', 3, 4, '2025-07-02 09:40:58'),
(35, 'halo', 9, 4, '2025-07-23 09:28:15'),
(36, 'iya', 4, 9, '2025-07-23 09:31:19');

-- --------------------------------------------------------

--
-- Table structure for table `chat_participant`
--

CREATE TABLE `chat_participant` (
  `idChat_participant` int(11) NOT NULL,
  `id_chat` int(11) NOT NULL,
  `id_user` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `checkout`
--

CREATE TABLE `checkout` (
  `id_checkout` int(11) NOT NULL,
  `id_pelanggan` int(11) NOT NULL,
  `id_product` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `payment_method` enum('bank_transfer','cod','qris') NOT NULL,
  `payment_date` datetime NOT NULL,
  `transaction_id` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `idFeedback` int(11) NOT NULL,
  `id_admin` int(11) NOT NULL,
  `feedback_admin` text DEFAULT NULL,
  `feedback_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

CREATE TABLE `kategori` (
  `idkategori` int(11) NOT NULL,
  `nama_kategori` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kategori`
--

INSERT INTO `kategori` (`idkategori`, `nama_kategori`) VALUES
(1, 'Keyboard'),
(2, 'Mouse'),
(3, 'Headset'),
(4, 'Monitor'),
(5, 'Mouse Pad'),
(6, 'Controller'),
(7, 'Gaming Chair'),
(8, 'Console'),
(9, 'VR Glasses'),
(10, 'Microphone'),
(11, 'Webcam'),
(12, 'Hardware'),
(13, 'Computer'),
(14, 'Laptop'),
(15, 'Gadget');

-- --------------------------------------------------------

--
-- Table structure for table `notification`
--

CREATE TABLE `notification` (
  `id_notification` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `message` text NOT NULL,
  `sent_date` datetime NOT NULL,
  `is_read` tinyint(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orderdetail`
--

CREATE TABLE `orderdetail` (
  `id_order_detail` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `status` enum('pending','completed','canceled','processing','shipped','delivered') NOT NULL,
  `payment_status` enum('unpaid','paid') NOT NULL,
  `order_date` datetime DEFAULT current_timestamp(),
  `total_price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orderdetail`
--

INSERT INTO `orderdetail` (`id_order_detail`, `user_id`, `order_id`, `status`, `payment_status`, `order_date`, `total_price`) VALUES
(1, 2, 1, '', 'unpaid', '2025-01-09 11:10:44', 789000.00),
(2, 2, 2, '', 'unpaid', '2025-01-09 11:22:06', 789000.00),
(3, 2, 3, '', 'unpaid', '2025-01-09 11:22:32', 782000.00),
(4, 2, 4, '', 'unpaid', '2025-01-09 11:27:53', 12929000.00),
(5, 2, 5, '', 'unpaid', '2025-01-09 13:57:34', 7320000.00),
(6, 2, 6, '', 'unpaid', '2025-01-09 14:36:08', 782000.00),
(7, 2, 7, '', 'unpaid', '2025-01-09 14:45:47', 782000.00),
(8, 3, 8, '', 'unpaid', '2025-01-09 14:55:56', 789000.00),
(9, 3, 9, '', 'unpaid', '2025-01-09 14:56:38', 782000.00),
(10, 3, 10, '', 'unpaid', '2025-01-09 14:59:36', 789000.00),
(11, 3, 11, '', 'unpaid', '2025-01-09 16:38:45', 782000.00),
(12, 3, 12, '', 'unpaid', '2025-01-09 16:40:36', 789000.00),
(13, 3, 13, '', 'unpaid', '2025-01-09 16:43:37', 7320000.00),
(14, 2, 14, '', 'unpaid', '2025-01-10 03:04:25', 12929000.00),
(15, 2, 15, '', 'unpaid', '2025-01-10 03:13:13', 12929000.00),
(16, 6, 16, '', 'unpaid', '2025-01-10 03:37:36', 782000.00),
(17, 6, 17, '', 'unpaid', '2025-01-10 03:38:09', 7320000.00),
(18, 3, 18, '', 'unpaid', '2025-01-15 13:12:11', 789000.00),
(19, 3, 19, '', 'unpaid', '2025-01-15 13:15:47', 789000.00),
(20, 3, 20, '', 'unpaid', '2025-01-21 14:07:41', 789000.00),
(21, 2, 21, '', 'unpaid', '2025-04-24 09:46:26', 789000.00),
(22, 6, 22, '', 'unpaid', '2025-05-12 15:02:26', 12929000.00),
(23, 3, 23, '', 'unpaid', '2025-07-02 11:40:28', 7320000.00),
(24, 7, 24, '', 'unpaid', '2025-07-03 08:48:12', 2367000.00),
(25, 8, 25, '', 'unpaid', '2025-07-03 08:52:47', 2367000.00),
(26, 8, 26, '', 'unpaid', '2025-07-03 08:53:37', 789000.00),
(27, 8, 27, '', 'unpaid', '2025-07-03 08:57:36', 12929000.00),
(28, 9, 28, '', 'unpaid', '2025-07-23 11:26:42', 782000.00),
(29, 9, 29, '', 'unpaid', '2025-07-23 11:27:36', 7320000.00),
(30, 9, 30, '', 'unpaid', '2025-07-23 11:40:12', 7320000.00),
(31, 6, 31, '', 'unpaid', '2025-07-28 09:43:47', 782000.00),
(32, 6, 32, '', 'unpaid', '2025-07-28 09:44:14', 7320000.00),
(33, 6, 33, '', 'unpaid', '2025-07-28 09:44:34', 1259000.00),
(34, 6, 34, '', 'unpaid', '2025-07-28 09:45:48', 782000.00),
(35, 6, 35, '', 'unpaid', '2025-07-28 09:46:49', 1259000.00),
(36, 6, 36, '', 'unpaid', '2025-07-28 09:49:50', 1259000.00),
(37, 6, 37, '', 'unpaid', '2025-07-28 09:50:02', 789000.00),
(38, 6, 38, '', 'unpaid', '2025-07-28 09:50:11', 2299000.00),
(39, 6, 39, '', 'unpaid', '2025-07-28 09:50:19', 14469000.00);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `product_name` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `order_date` datetime DEFAULT current_timestamp(),
  `total_price` decimal(10,2) NOT NULL,
  `transaction_id` varchar(255) DEFAULT NULL,
  `payment_method` enum('G4P_wallet','qris','paypal','dana','ovo') NOT NULL,
  `payment_status` enum('unpaid','paid','cancelled') NOT NULL,
  `payment_deadline` datetime DEFAULT NULL,
  `status` enum('pending','completed','canceled','processing','shipped','delivered') NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `product_id`, `product_name`, `quantity`, `order_date`, `total_price`, `transaction_id`, `payment_method`, `payment_status`, `payment_deadline`, `status`) VALUES
(1, 2, NULL, '1 Rexus Mouse gaming Daxa Air IV Pro', 1, '2025-01-09 11:10:44', 789000.00, 'trx_677fa0a4591e4', 'G4P_wallet', 'paid', NULL, 'pending'),
(2, 2, NULL, '1 Rexus Mouse gaming Daxa Air IV Pro', 1, '2025-01-09 11:22:06', 789000.00, 'trx_677fa34eb6afd', 'G4P_wallet', 'paid', NULL, 'pending'),
(3, 2, NULL, '1 Oca 259 Gemink Chair', 1, '2025-01-09 11:22:32', 782000.00, 'trx_677fa368172f5', 'G4P_wallet', 'cancelled', NULL, 'pending'),
(4, 2, NULL, '1 MSI GeForce RTX 4090  GAMING X', 1, '2025-01-09 11:27:53', 12929000.00, 'trx_677fa4a94ba52', 'G4P_wallet', 'paid', NULL, 'pending'),
(5, 2, NULL, '1 Rexus Mouse gaming Daxa Air IV Pro, 1 Gaming Chairs  Secretlab TITAN Evo', 2, '2025-01-09 13:57:34', 8109000.00, 'trx_677fc7be52118', 'G4P_wallet', 'paid', NULL, 'pending'),
(6, 2, NULL, '1 Oca 259 Gemink Chair', 1, '2025-01-09 14:36:08', 782000.00, 'trx_677fd0c8027e3', 'G4P_wallet', 'paid', NULL, 'pending'),
(7, 2, NULL, '1 Oca 259 Gemink Chair', 1, '2025-01-09 14:45:47', 782000.00, 'trx_677fd30b3d853', 'G4P_wallet', 'paid', NULL, 'pending'),
(8, 3, NULL, '1 Rexus Mouse gaming Daxa Air IV Pro', 1, '2025-01-09 14:55:56', 789000.00, 'trx_677fd56c0e5f5', 'G4P_wallet', 'paid', NULL, 'pending'),
(9, 3, NULL, '1 Oca 259 Gemink Chair', 1, '2025-01-09 14:56:38', 782000.00, 'trx_677fd5962dcbf', 'G4P_wallet', 'paid', NULL, 'pending'),
(10, 3, NULL, '1 Rexus Mouse gaming Daxa Air IV Pro', 1, '2025-01-09 14:59:36', 789000.00, 'trx_677fd648013d5', 'G4P_wallet', 'paid', NULL, 'pending'),
(11, 3, NULL, '1 Oca 259 Gemink Chair', 1, '2025-01-09 16:38:45', 782000.00, 'trx_677fed85750a9', 'G4P_wallet', 'paid', NULL, 'pending'),
(12, 3, NULL, '1 Rexus Mouse gaming Daxa Air IV Pro', 1, '2025-01-09 16:40:36', 789000.00, 'trx_677fedf458d0d', 'G4P_wallet', 'cancelled', NULL, 'pending'),
(13, 3, NULL, '1 Gaming Chairs  Secretlab TITAN Evo', 1, '2025-01-09 16:43:37', 7320000.00, 'trx_677feea91b74f', 'G4P_wallet', 'paid', NULL, 'pending'),
(14, 2, NULL, '1 Oca 259 Gemink Chair, 1 MSI GeForce RTX 4090  GAMING X', 2, '2025-01-10 03:04:25', 13711000.00, 'trx_6780802971ed2', 'G4P_wallet', 'paid', NULL, 'pending'),
(15, 2, NULL, '1 MSI GeForce RTX 4090  GAMING X', 1, '2025-01-10 03:13:13', 12929000.00, 'trx_67808239c69fa', 'G4P_wallet', 'paid', NULL, 'pending'),
(16, 6, NULL, '1 Oca 259 Gemink Chair', 1, '2025-01-10 03:37:36', 782000.00, 'trx_678087f025ae5', 'G4P_wallet', 'unpaid', NULL, 'pending'),
(17, 6, NULL, '1 Rexus Mouse gaming Daxa Air IV Pro, 1 MSI GeForce RTX 4090  GAMING X, 1 Gaming Chairs  Secretlab TITAN Evo', 3, '2025-01-10 03:38:09', 21038000.00, 'trx_6780881151666', 'G4P_wallet', 'paid', NULL, 'pending'),
(18, 3, NULL, '1 Rexus Mouse gaming Daxa Air IV Pro', 1, '2025-01-15 13:12:11', 789000.00, 'trx_6787a61ba8c18', 'G4P_wallet', 'paid', NULL, 'pending'),
(19, 3, NULL, '1 Rexus Mouse gaming Daxa Air IV Pro', 1, '2025-01-15 13:15:47', 789000.00, 'trx_6787a6f318e4b', 'G4P_wallet', 'paid', NULL, 'pending'),
(20, 3, NULL, '1 Rexus Mouse gaming Daxa Air IV Pro', 1, '2025-01-21 14:07:41', 789000.00, 'trx_678f9c1d51c5f', 'G4P_wallet', 'paid', NULL, 'pending'),
(21, 2, NULL, '2 Oca 259 Gemink Chair, 1 Rexus Mouse gaming Daxa Air IV Pro', 3, '2025-04-24 09:46:26', 2353000.00, 'trx_6809ec52489dc', 'G4P_wallet', 'paid', NULL, 'pending'),
(22, 6, NULL, '1 MSI GeForce RTX 4090  GAMING X', 1, '2025-05-12 15:02:26', 12929000.00, 'trx_6821f162c2664', 'G4P_wallet', 'paid', NULL, 'pending'),
(23, 3, NULL, '2 Flydigi Apex 3 Elite  Gaming Controller, 1 Gaming Chairs  Secretlab TITAN Evo', 3, '2025-07-02 11:40:28', 9838000.00, 'trx_6864fe8c9f1ab', 'G4P_wallet', 'paid', NULL, 'pending'),
(24, 7, NULL, '3 Rexus Mouse gaming Daxa Air IV Pro', 3, '2025-07-03 08:48:12', 2367000.00, 'trx_686627acc25c3', 'G4P_wallet', 'paid', NULL, 'pending'),
(25, 8, NULL, '3 Rexus Mouse gaming Daxa Air IV Pro', 3, '2025-07-03 08:52:47', 2367000.00, 'trx_686628bf801e9', 'G4P_wallet', 'cancelled', NULL, 'pending'),
(26, 8, NULL, '1 Rexus Mouse gaming Daxa Air IV Pro', 1, '2025-07-03 08:53:37', 789000.00, 'trx_686628f136529', 'G4P_wallet', 'cancelled', NULL, 'pending'),
(27, 8, NULL, '1 MSI GeForce RTX 4090  GAMING X', 1, '2025-07-03 08:57:36', 12929000.00, 'trx_686629e009c67', 'G4P_wallet', 'paid', NULL, 'pending'),
(28, 9, NULL, '1 Oca 259 Gemink Chair', 1, '2025-07-23 11:26:42', 782000.00, 'trx_6880aad299ee8', 'G4P_wallet', 'paid', NULL, 'pending'),
(29, 9, NULL, '3 MSI GeForce RTX 4090  GAMING X, 1 Gaming Chairs  Secretlab TITAN Evo', 4, '2025-07-23 11:27:36', 46107000.00, 'trx_6880ab0852533', 'G4P_wallet', 'cancelled', NULL, 'pending'),
(30, 9, NULL, '1 Gaming Chairs  Secretlab TITAN Evo', 1, '2025-07-23 11:40:12', 7320000.00, 'trx_6880adfce406b', 'G4P_wallet', 'unpaid', NULL, 'pending'),
(31, 6, NULL, '1 Oca 259 Gemink Chair', 1, '2025-07-28 09:43:47', 782000.00, 'trx_68872a337520a', 'ovo', 'paid', NULL, 'pending'),
(32, 6, NULL, '1 Gaming Chairs  Secretlab TITAN Evo', 1, '2025-07-28 09:44:14', 7320000.00, 'trx_68872a4e954bb', 'ovo', 'paid', NULL, 'pending'),
(33, 6, NULL, '1 Flydigi Apex 3 Elite  Gaming Controller', 1, '2025-07-28 09:44:34', 1259000.00, 'trx_68872a62a3533', 'ovo', 'unpaid', NULL, 'pending'),
(34, 6, NULL, '1 Oca 259 Gemink Chair', 1, '2025-07-28 09:45:48', 782000.00, 'trx_68872aac760ad', 'ovo', 'unpaid', NULL, 'pending'),
(35, 6, NULL, '1 Flydigi Apex 3 Elite  Gaming Controller', 1, '2025-07-28 09:46:49', 1259000.00, 'trx_68872ae94bc40', 'ovo', 'unpaid', NULL, 'pending'),
(36, 6, NULL, '1 Flydigi Apex 3 Elite  Gaming Controller', 1, '2025-07-28 09:49:50', 1259000.00, 'trx_68872b9e6dcc8', 'qris', 'unpaid', NULL, 'pending'),
(37, 6, NULL, '1 Rexus Mouse gaming Daxa Air IV Pro', 1, '2025-07-28 09:50:02', 789000.00, 'trx_68872baa0b0f8', 'paypal', 'unpaid', NULL, 'pending'),
(38, 6, NULL, '1 Logitech G502 X Plus Wireless RGB Mouse Gaming', 1, '2025-07-28 09:50:11', 2299000.00, 'trx_68872bb3b4ba8', 'dana', 'unpaid', NULL, 'pending'),
(39, 6, NULL, '1 Monitor Asus ROG  Strix XG27AQMR ', 1, '2025-07-28 09:50:19', 14469000.00, 'trx_68872bbbcfe68', 'ovo', 'unpaid', NULL, 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `order_item_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `id_order_detail` int(11) DEFAULT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) DEFAULT 1,
  `price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pengiriman`
--

CREATE TABLE `pengiriman` (
  `idPengiriman` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `id_kurir` int(11) NOT NULL,
  `status` enum('pending','completed','canceled','processing','shipped','delivered') NOT NULL,
  `waktuAwal_kirim` datetime NOT NULL,
  `live_location_item` text NOT NULL,
  `estimasi_sampai` datetime NOT NULL,
  `no_resi` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pengiriman`
--

INSERT INTO `pengiriman` (`idPengiriman`, `order_id`, `id_kurir`, `status`, `waktuAwal_kirim`, `live_location_item`, `estimasi_sampai`, `no_resi`) VALUES
(3, 4, 1, 'pending', '2025-01-09 17:27:56', 'Menunggu pickup kurir', '2025-01-12 17:27:56', 'resi-4-1736418476'),
(4, 5, 1, 'pending', '2025-01-09 19:57:36', 'Menunggu pickup kurir', '2025-01-12 19:57:36', 'resi-5-1736427456'),
(5, 6, 1, 'pending', '2025-01-09 20:36:10', 'Menunggu pickup kurir', '2025-01-12 20:36:10', 'resi-6-1736429770'),
(6, 7, 1, 'pending', '2025-01-09 20:45:49', 'Menunggu pickup kurir', '2025-01-12 20:45:49', 'resi-7-1736430349'),
(7, 8, 1, 'pending', '2025-01-09 20:56:08', 'Menunggu pickup kurir', '2025-01-12 20:56:08', 'resi-8-1736430968'),
(8, 9, 1, 'pending', '2025-01-09 20:56:49', 'Menunggu pickup kurir', '2025-01-12 20:56:49', 'resi-9-1736431009'),
(9, 10, 5, 'delivered', '2025-01-09 20:59:38', 'Paket anda telah tiba di hub transit, Paket anda telah tiba di jonggol, Menunggu pickup kurir', '2025-01-12 20:59:38', 'resi-10-1736431178'),
(10, 11, 5, 'shipped', '2025-01-09 22:38:54', 'Menunggu pickup kurir', '2025-01-12 22:38:54', 'resi-11-1736437134'),
(11, 13, 5, 'pending', '2025-01-09 22:43:44', 'paket anda telah sampai di dc cakung, Menunggu pickup kurir', '2025-01-12 22:43:44', 'resi-13-1736437424'),
(12, 14, 5, 'shipped', '2025-01-10 09:04:31', 'Paket anda telah sampai di jonggol, Menunggu pickup kurir', '2025-01-13 09:04:31', 'resi-14-1736474671'),
(13, 15, 5, 'pending', '2025-01-10 09:13:20', 'Menunggu pickup kurir', '2025-01-13 09:13:20', 'resi-15-1736475200'),
(14, 17, 5, 'processing', '2025-01-10 09:39:00', 'Paket telah sampai di jonggol, Menunggu pickup kurir', '2025-01-13 09:39:00', 'resi-17-1736476740'),
(15, 18, 5, 'pending', '2025-01-15 19:12:19', 'Menunggu pickup kurir', '2025-01-18 19:12:19', 'resi-18-1736943139'),
(16, 19, 5, 'pending', '2025-01-15 19:15:51', 'Menunggu pickup kurir', '2025-01-18 19:15:51', 'resi-19-1736943351'),
(17, 20, 5, 'pending', '2025-01-21 20:07:43', 'Menunggu pickup kurir', '2025-01-24 20:07:43', 'resi-20-1737464863'),
(18, 21, 5, 'pending', '2025-04-24 14:46:37', 'Menunggu pickup kurir', '2025-04-27 14:46:37', 'resi-21-1745480797'),
(19, 22, 5, 'pending', '2025-05-12 20:02:52', 'Menunggu pickup kurir', '2025-05-15 20:02:52', 'resi-22-1747054972'),
(20, 23, 5, 'pending', '2025-07-02 16:40:32', 'Menunggu pickup kurir', '2025-07-05 16:40:32', 'resi-23-1751449232'),
(21, 24, 5, 'pending', '2025-07-03 13:48:27', 'Menunggu pickup kurir', '2025-07-06 13:48:27', 'resi-24-1751525307'),
(22, 27, 5, 'pending', '2025-07-03 13:57:38', 'Menunggu pickup kurir', '2025-07-06 13:57:38', 'resi-27-1751525858'),
(23, 28, 5, 'pending', '2025-07-23 16:26:56', 'Menunggu pickup kurir', '2025-07-26 16:26:56', 'resi-28-1753262816'),
(24, 31, 5, 'pending', '2025-07-28 14:44:06', 'Menunggu pickup kurir', '2025-07-31 14:44:06', 'resi-31-1753688646'),
(25, 32, 5, 'pending', '2025-07-28 14:44:26', 'Menunggu pickup kurir', '2025-07-31 14:44:26', 'resi-32-1753688666');

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `id_product` int(11) NOT NULL,
  `idkategori` int(11) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock` int(11) NOT NULL,
  `image_url` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`id_product`, `idkategori`, `product_name`, `description`, `price`, `stock`, `image_url`) VALUES
(4, 7, 'Oca 259 Gemink Chair', 'CHair gemink', 782000.00, 120, 'uploaded_image/oca 259 fantech.png'),
(5, 2, 'Rexus Mouse gaming Daxa Air IV Pro', 'Mouse gemink', 789000.00, 124, 'uploaded_image/image-removebg-preview (2) 1.png'),
(6, 12, 'MSI GeForce RTX 4090  GAMING X', 'ErTiEX bos', 12929000.00, 12, 'uploaded_image/vga 1.png'),
(7, 7, 'Gaming Chairs  Secretlab TITAN Evo', 'Gwmink chair', 7320000.00, 25, 'uploaded_image/Secret lab chair.png'),
(8, 6, 'Flydigi Apex 3 Elite  Gaming Controller', 'New Gamepad from Flydigi 2022 Apex 3 Fly to Galxy edition\r\ninclude\r\nGamepad\r\nCarrying case\r\nAnalog rocket\r\nKeychain\r\n\r\n\r\nFeatures :\r\n- Force feedbak triggers ( Hall efet gaming trigger )\r\n- Full color LED Display\r\n- Support mouse and keyboard mapping function on PC ( Cooperate with reWASD team )\r\n- Hybrid D-Pad\r\n- Mecha Tactille ABXY Buttons\r\n- 4 additional remappable back buttons\r\n- Support PC / Nintendo Switch / Android / IOS MFI\r\n- Connect way : BT / 2.4G wireless / Type-C Wired', 1259000.00, 120, 'uploaded_image/image-removebg-preview (6) 1.png'),
(9, 4, 'Monitor Asus ROG  Strix XG27AQMR ', 'Monitor geming', 14469000.00, 50, 'uploaded_image/image-removebg-preview (7) 1.png'),
(10, 2, 'Logitech G502 X Plus Wireless RGB Mouse Gaming', 'Dimensi\r\nSpesifikasi Utama\r\nPengisian Daya USB-C\r\n106 gram\r\nAlas PTFE dengan gaya gesek rendah\r\nSPESIFIKASI FISIK\r\nBerat: 106 g\r\nTinggi: 131,4 mm\r\nLebar: 41,1 mm\r\nTebal: 79,2 mm\r\nSpesifikasi Teknis\r\nTeknologi nirkabel LIGHTSPEED\r\nMaksimum 5 profil onboard memory 1Standar terdapat dua profil, tetapi bertambah menjadi lima profil dengan menggunakan Software Logitech G HUB, tersedia untuk di-download di logitechg.com/ghub\r\nAlas PTFE\r\n13 kontrol yang dapat diprogram\r\nPencahayaan RGB 8-zona\r\nPort pengisian daya USB-C\r\nDaya tahan baterai 2Daya tahan baterai mungkin bervariasi, berdasarkan kondisi pengguna dan komputasi\r\nPergerakan konstan 130 jam (37 jam saat RGB dinyalakan)\r\nPENELUSURAN\r\nSensor: HERO 25K\r\nResolusi: 100 – 25.600 dpi\r\nAkselerasi maks.: >40G\r\nKecepatan maks.: >400 IPS 3Diuji pada Logitech G240 Gaming Mouse Pad\r\nZero smoothing/akselerasi/penyaringan\r\nINFORMASI GARANSI\r\nGaransi Hardware Terbatas 2 Tahun', 2299000.00, 55, 'uploaded_image/mouse logitech.jpeg'),
(11, 12, 'Tes', 'tes', 100000.00, 5, 'uploaded_image/profile.png');

-- --------------------------------------------------------

--
-- Table structure for table `review`
--

CREATE TABLE `review` (
  `id_review` int(11) NOT NULL,
  `id_product` int(11) NOT NULL,
  `id_pelanggan` int(11) NOT NULL,
  `idFeedback` int(11) NOT NULL,
  `rating` int(11) NOT NULL,
  `comment` text DEFAULT NULL,
  `review_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id_user` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone_number` varchar(15) NOT NULL,
  `role` enum('admin','pelanggan','kurir') NOT NULL,
  `wallet` decimal(10,2) NOT NULL DEFAULT 0.00,
  `profile_picture` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id_user`, `username`, `password`, `email`, `phone_number`, `role`, `wallet`, `profile_picture`) VALUES
(1, 'han', '$2y$10$Pw7ErFDZ809Xq7ApVI/8MuMdoJP1LkinsZ7NpjsGGNRTaaF3mo2SS', 'prcstoregtstock@gmail.com', 'assgsas', 'pelanggan', 0.00, NULL),
(2, 'prokalem', '$2y$10$nSo5wgBHG//bO0phXn0Qbuc.FlLHaOuGo1UUZtC2wtUCql1XHgUju', 'procalmgt@gmail.com', '0019210221', 'pelanggan', 0.00, NULL),
(3, 'Palo', '$2y$10$2yqqVV.of/ZyHuyzwjsVt./KKDnS03Wm6VGpGQF78eptVhD2hIBYm', 'prcstoregts@gmail.com', '089869151221', 'pelanggan', 0.00, NULL),
(4, 'Admin', '$2y$10$2ttjGY6ax/XFb9dtkO0BJ.bEf.dma5KIlyBLxb3TrLLpFp1ivO9bW', 'A@gmail.com', '08986233422', 'admin', 0.00, NULL),
(5, 'mamatZ', '$2y$10$Wqkb5T6LpjIZtZYuzddYcemaBO/NBOflPXh4soBoFLBsiOvoiKpva', 'mamatZzz@gmail.com', '08217528592', 'kurir', 0.00, NULL),
(6, 'Dafa', '$2y$10$YOk6zHmhd1ApAKc.lV9uFOAQHORXBMKb7niMTUTBoll5r7B.xGX42', 'dafa@gmail.com', '08986915863', 'pelanggan', 0.00, NULL),
(7, 'parjo', '$2y$10$f1sDIXaj9eV95WhkEdIXxOuE0AL3fjX6z132rD8ZRwNCv3bJfWBj2', 'asa@gmail.com', '08982121945', 'pelanggan', 0.00, NULL),
(8, 'dafi', '$2y$10$rAh5Z9ckM6PGCOSnAptRE.aV/3cl8K6vqu55Rz6jujwrzWM0CPkDe', 'sag@gmail.com', '0985181251', 'pelanggan', 0.00, NULL),
(9, 'mamat', '$2y$10$NQi3plgcwpH8Fd4dXGtC9uOISTyh8slw/pSwr4EsHCGDLjGBuqh7S', 'procalmgt@gmail.com', '08986915863', 'pelanggan', 0.00, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `idWishlist` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_product` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wishlist`
--

INSERT INTO `wishlist` (`idWishlist`, `id_user`, `id_product`) VALUES
(32, 3, 7),
(33, 3, 6),
(38, 3, 4),
(44, 6, 5),
(48, 6, 6),
(49, 9, 5);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `address`
--
ALTER TABLE `address`
  ADD PRIMARY KEY (`id_address`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `billboard`
--
ALTER TABLE `billboard`
  ADD PRIMARY KEY (`id_billboard`),
  ADD KEY `id_product` (`id_product`);

--
-- Indexes for table `buktipengiriman`
--
ALTER TABLE `buktipengiriman`
  ADD PRIMARY KEY (`id_bukti`),
  ADD KEY `idPengiriman` (`idPengiriman`),
  ADD KEY `fk_order_id` (`order_id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id_cart`),
  ADD KEY `id_pelanggan` (`id_pelanggan`);

--
-- Indexes for table `cartitem`
--
ALTER TABLE `cartitem`
  ADD PRIMARY KEY (`id_cart_item`),
  ADD KEY `id_cart` (`id_cart`),
  ADD KEY `id_product` (`id_product`);

--
-- Indexes for table `chat`
--
ALTER TABLE `chat`
  ADD PRIMARY KEY (`id_chat`);

--
-- Indexes for table `chat_participant`
--
ALTER TABLE `chat_participant`
  ADD PRIMARY KEY (`idChat_participant`),
  ADD KEY `id_chat` (`id_chat`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `checkout`
--
ALTER TABLE `checkout`
  ADD PRIMARY KEY (`id_checkout`),
  ADD KEY `id_pelanggan` (`id_pelanggan`),
  ADD KEY `id_product` (`id_product`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`idFeedback`),
  ADD KEY `id_admin` (`id_admin`);

--
-- Indexes for table `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`idkategori`);

--
-- Indexes for table `notification`
--
ALTER TABLE `notification`
  ADD PRIMARY KEY (`id_notification`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `orderdetail`
--
ALTER TABLE `orderdetail`
  ADD PRIMARY KEY (`id_order_detail`),
  ADD KEY `orderdetail_fk_order_id` (`order_id`),
  ADD KEY `fk_user_id` (`user_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `fk_product_id` (`product_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `fk_order_detail` (`id_order_detail`);

--
-- Indexes for table `pengiriman`
--
ALTER TABLE `pengiriman`
  ADD PRIMARY KEY (`idPengiriman`),
  ADD KEY `id_kurir` (`id_kurir`),
  ADD KEY `pengiriman_ibfk_4` (`order_id`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`id_product`),
  ADD KEY `idkategori` (`idkategori`);

--
-- Indexes for table `review`
--
ALTER TABLE `review`
  ADD PRIMARY KEY (`id_review`),
  ADD KEY `id_product` (`id_product`),
  ADD KEY `id_pelanggan` (`id_pelanggan`),
  ADD KEY `idFeedback` (`idFeedback`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`idWishlist`),
  ADD KEY `id_product` (`id_product`) USING BTREE,
  ADD KEY `id_user` (`id_user`) USING BTREE;

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `address`
--
ALTER TABLE `address`
  MODIFY `id_address` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `billboard`
--
ALTER TABLE `billboard`
  MODIFY `id_billboard` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `buktipengiriman`
--
ALTER TABLE `buktipengiriman`
  MODIFY `id_bukti` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id_cart` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `cartitem`
--
ALTER TABLE `cartitem`
  MODIFY `id_cart_item` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=92;

--
-- AUTO_INCREMENT for table `chat`
--
ALTER TABLE `chat`
  MODIFY `id_chat` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `chat_participant`
--
ALTER TABLE `chat_participant`
  MODIFY `idChat_participant` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `checkout`
--
ALTER TABLE `checkout`
  MODIFY `id_checkout` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `idFeedback` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kategori`
--
ALTER TABLE `kategori`
  MODIFY `idkategori` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `notification`
--
ALTER TABLE `notification`
  MODIFY `id_notification` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orderdetail`
--
ALTER TABLE `orderdetail`
  MODIFY `id_order_detail` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `pengiriman`
--
ALTER TABLE `pengiriman`
  MODIFY `idPengiriman` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `id_product` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `review`
--
ALTER TABLE `review`
  MODIFY `id_review` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `idWishlist` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `address`
--
ALTER TABLE `address`
  ADD CONSTRAINT `address_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`);

--
-- Constraints for table `billboard`
--
ALTER TABLE `billboard`
  ADD CONSTRAINT `billboard_ibfk_1` FOREIGN KEY (`id_product`) REFERENCES `product` (`id_product`) ON DELETE CASCADE;

--
-- Constraints for table `buktipengiriman`
--
ALTER TABLE `buktipengiriman`
  ADD CONSTRAINT `buktipengiriman_ibfk_1` FOREIGN KEY (`idPengiriman`) REFERENCES `pengiriman` (`idPengiriman`),
  ADD CONSTRAINT `fk_order_id` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`);

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`id_pelanggan`) REFERENCES `user` (`id_user`);

--
-- Constraints for table `cartitem`
--
ALTER TABLE `cartitem`
  ADD CONSTRAINT `cartitem_ibfk_1` FOREIGN KEY (`id_cart`) REFERENCES `cart` (`id_cart`),
  ADD CONSTRAINT `cartitem_ibfk_2` FOREIGN KEY (`id_product`) REFERENCES `product` (`id_product`);

--
-- Constraints for table `chat_participant`
--
ALTER TABLE `chat_participant`
  ADD CONSTRAINT `chat_participant_ibfk_1` FOREIGN KEY (`id_chat`) REFERENCES `chat` (`id_chat`),
  ADD CONSTRAINT `chat_participant_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`);

--
-- Constraints for table `checkout`
--
ALTER TABLE `checkout`
  ADD CONSTRAINT `checkout_ibfk_1` FOREIGN KEY (`id_pelanggan`) REFERENCES `user` (`id_user`),
  ADD CONSTRAINT `checkout_ibfk_2` FOREIGN KEY (`id_product`) REFERENCES `product` (`id_product`);

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`id_admin`) REFERENCES `user` (`id_user`);

--
-- Constraints for table `notification`
--
ALTER TABLE `notification`
  ADD CONSTRAINT `notification_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`);

--
-- Constraints for table `orderdetail`
--
ALTER TABLE `orderdetail`
  ADD CONSTRAINT `fk_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `orderdetail_fk_order_id` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_product_id` FOREIGN KEY (`product_id`) REFERENCES `product` (`id_product`),
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id_user`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_order_detail` FOREIGN KEY (`id_order_detail`) REFERENCES `orderdetail` (`id_order_detail`),
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `product` (`id_product`);

--
-- Constraints for table `pengiriman`
--
ALTER TABLE `pengiriman`
  ADD CONSTRAINT `pengiriman_ibfk_3` FOREIGN KEY (`id_kurir`) REFERENCES `user` (`id_user`),
  ADD CONSTRAINT `pengiriman_ibfk_4` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`);

--
-- Constraints for table `product`
--
ALTER TABLE `product`
  ADD CONSTRAINT `product_ibfk_1` FOREIGN KEY (`idkategori`) REFERENCES `kategori` (`idkategori`);

--
-- Constraints for table `review`
--
ALTER TABLE `review`
  ADD CONSTRAINT `review_ibfk_1` FOREIGN KEY (`id_product`) REFERENCES `product` (`id_product`),
  ADD CONSTRAINT `review_ibfk_2` FOREIGN KEY (`id_pelanggan`) REFERENCES `user` (`id_user`),
  ADD CONSTRAINT `review_ibfk_3` FOREIGN KEY (`idFeedback`) REFERENCES `feedback` (`idFeedback`);

--
-- Constraints for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD CONSTRAINT `wishlist_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`),
  ADD CONSTRAINT `wishlist_ibfk_2` FOREIGN KEY (`id_product`) REFERENCES `product` (`id_product`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
