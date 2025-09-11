-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Win64 (AMD64)
--
-- Host: localhost    Database: ai_stock_analytics
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
INSERT INTO `cache` VALUES ('ticker-ai-cache-market_insights','a:5:{s:7:\"success\";b:1;s:10:\"top_active\";a:10:{i:0;a:8:{s:6:\"symbol\";s:4:\"GOTO\";s:4:\"name\";s:20:\"GoTo Gojek Tokopedia\";s:5:\"price\";d:59;s:14:\"change_percent\";d:0;s:6:\"volume\";i:1621019900;s:10:\"is_gaining\";b:0;s:12:\"volume_score\";d:10;s:15:\"promising_score\";d:6;}i:1;a:8:{s:6:\"symbol\";s:4:\"MDKA\";s:4:\"name\";s:19:\"Merdeka Copper Gold\";s:5:\"price\";d:2620;s:14:\"change_percent\";d:0;s:6:\"volume\";i:191356100;s:10:\"is_gaining\";b:0;s:12:\"volume_score\";d:10;s:15:\"promising_score\";d:5;}i:2;a:8:{s:6:\"symbol\";s:4:\"ANTM\";s:4:\"name\";s:13:\"Aneka Tambang\";s:5:\"price\";d:3390;s:14:\"change_percent\";d:0;s:6:\"volume\";i:142473100;s:10:\"is_gaining\";b:0;s:12:\"volume_score\";d:10;s:15:\"promising_score\";d:5;}i:3;a:8:{s:6:\"symbol\";s:4:\"JPFA\";s:4:\"name\";s:13:\"Japfa Comfeed\";s:5:\"price\";d:1760;s:14:\"change_percent\";d:0;s:6:\"volume\";i:109803400;s:10:\"is_gaining\";b:0;s:12:\"volume_score\";d:10;s:15:\"promising_score\";d:3.5;}i:4;a:8:{s:6:\"symbol\";s:4:\"BBCA\";s:4:\"name\";s:12:\"Central Asia\";s:5:\"price\";d:8000;s:14:\"change_percent\";d:0;s:6:\"volume\";i:103534800;s:10:\"is_gaining\";b:0;s:12:\"volume_score\";d:10;s:15:\"promising_score\";d:6;}i:5;a:8:{s:6:\"symbol\";s:4:\"EMTK\";s:4:\"name\";s:23:\"Elang Mahkota Teknologi\";s:5:\"price\";d:1305;s:14:\"change_percent\";d:0;s:6:\"volume\";i:101163600;s:10:\"is_gaining\";b:0;s:12:\"volume_score\";d:10;s:15:\"promising_score\";d:5;}i:6;a:8:{s:6:\"symbol\";s:4:\"KLBF\";s:4:\"name\";s:12:\"Kalbe Farma.\";s:5:\"price\";d:1180;s:14:\"change_percent\";d:0;s:6:\"volume\";i:93346300;s:10:\"is_gaining\";b:0;s:12:\"volume_score\";d:8;s:15:\"promising_score\";d:3.5;}i:7;a:8:{s:6:\"symbol\";s:4:\"BMRI\";s:4:\"name\";s:17:\"Mandiri (Persero)\";s:5:\"price\";d:4680;s:14:\"change_percent\";d:0;s:6:\"volume\";i:88735300;s:10:\"is_gaining\";b:0;s:12:\"volume_score\";d:8;s:15:\"promising_score\";d:5.5;}i:8;a:8:{s:6:\"symbol\";s:4:\"BBRI\";s:4:\"name\";s:16:\"Rakyat (Persero)\";s:5:\"price\";d:4000;s:14:\"change_percent\";d:0;s:6:\"volume\";i:87402700;s:10:\"is_gaining\";b:0;s:12:\"volume_score\";d:8;s:15:\"promising_score\";d:5.5;}i:9;a:8:{s:6:\"symbol\";s:4:\"ACES\";s:4:\"name\";s:14:\"Aspirasi Hidup\";s:5:\"price\";d:440;s:14:\"change_percent\";d:0;s:6:\"volume\";i:50209300;s:10:\"is_gaining\";b:0;s:12:\"volume_score\";d:8;s:15:\"promising_score\";d:4.5;}}s:13:\"top_promising\";a:10:{i:0;a:8:{s:6:\"symbol\";s:4:\"BBCA\";s:4:\"name\";s:12:\"Central Asia\";s:5:\"price\";d:8000;s:14:\"change_percent\";d:0;s:6:\"volume\";i:103534800;s:10:\"is_gaining\";b:0;s:12:\"volume_score\";d:10;s:15:\"promising_score\";d:6;}i:1;a:8:{s:6:\"symbol\";s:4:\"ASII\";s:4:\"name\";s:13:\"International\";s:5:\"price\";d:5500;s:14:\"change_percent\";d:0;s:6:\"volume\";i:26987400;s:10:\"is_gaining\";b:0;s:12:\"volume_score\";d:6;s:15:\"promising_score\";d:6;}i:2;a:8:{s:6:\"symbol\";s:4:\"GOTO\";s:4:\"name\";s:20:\"GoTo Gojek Tokopedia\";s:5:\"price\";d:59;s:14:\"change_percent\";d:0;s:6:\"volume\";i:1621019900;s:10:\"is_gaining\";b:0;s:12:\"volume_score\";d:10;s:15:\"promising_score\";d:6;}i:3;a:8:{s:6:\"symbol\";s:4:\"BBRI\";s:4:\"name\";s:16:\"Rakyat (Persero)\";s:5:\"price\";d:4000;s:14:\"change_percent\";d:0;s:6:\"volume\";i:87402700;s:10:\"is_gaining\";b:0;s:12:\"volume_score\";d:8;s:15:\"promising_score\";d:5.5;}i:4;a:8:{s:6:\"symbol\";s:4:\"BMRI\";s:4:\"name\";s:17:\"Mandiri (Persero)\";s:5:\"price\";d:4680;s:14:\"change_percent\";d:0;s:6:\"volume\";i:88735300;s:10:\"is_gaining\";b:0;s:12:\"volume_score\";d:8;s:15:\"promising_score\";d:5.5;}i:5;a:8:{s:6:\"symbol\";s:4:\"ANTM\";s:4:\"name\";s:13:\"Aneka Tambang\";s:5:\"price\";d:3390;s:14:\"change_percent\";d:0;s:6:\"volume\";i:142473100;s:10:\"is_gaining\";b:0;s:12:\"volume_score\";d:10;s:15:\"promising_score\";d:5;}i:6;a:8:{s:6:\"symbol\";s:4:\"EMTK\";s:4:\"name\";s:23:\"Elang Mahkota Teknologi\";s:5:\"price\";d:1305;s:14:\"change_percent\";d:0;s:6:\"volume\";i:101163600;s:10:\"is_gaining\";b:0;s:12:\"volume_score\";d:10;s:15:\"promising_score\";d:5;}i:7;a:8:{s:6:\"symbol\";s:4:\"MDKA\";s:4:\"name\";s:19:\"Merdeka Copper Gold\";s:5:\"price\";d:2620;s:14:\"change_percent\";d:0;s:6:\"volume\";i:191356100;s:10:\"is_gaining\";b:0;s:12:\"volume_score\";d:10;s:15:\"promising_score\";d:5;}i:8;a:8:{s:6:\"symbol\";s:4:\"TLKM\";s:4:\"name\";s:25:\"Perusahaan Perseroan (...\";s:5:\"price\";d:3150;s:14:\"change_percent\";d:0;s:6:\"volume\";i:37223800;s:10:\"is_gaining\";b:0;s:12:\"volume_score\";d:6;s:15:\"promising_score\";d:4.5;}i:9;a:8:{s:6:\"symbol\";s:4:\"ACES\";s:4:\"name\";s:14:\"Aspirasi Hidup\";s:5:\"price\";d:440;s:14:\"change_percent\";d:0;s:6:\"volume\";i:50209300;s:10:\"is_gaining\";b:0;s:12:\"volume_score\";d:8;s:15:\"promising_score\";d:4.5;}}s:14:\"total_analyzed\";i:20;s:11:\"last_update\";s:21:\"06 Sep 2025 08:34 WIB\";}',1757124247);
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`),
  KEY `jobs_queue_reserved_index` (`queue`,`reserved_at`),
  KEY `jobs_created_at_index` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=160 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2025_07_13_092146_create_requests_table',1),(5,'2025_07_30_221352_add_indexes_to_requests_table',1),(6,'2025_07_31_005521_add_unique_constraint_to_mobile_number_in_users_table',1),(7,'2025_08_10_000000_optimize_database_indexes',1),(8,'2025_08_10_123751_add_result_tracking_to_requests_table',1),(9,'2025_08_13_133554_add_chatgpt_advice_to_requests_table',2),(10,'2025_09_06_173650_add_hold_status_to_result_enum',3);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `requests`
--

DROP TABLE IF EXISTS `requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `requests` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `full_name` varchar(255) NOT NULL,
  `mobile_number` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `stock_code` varchar(255) NOT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `timeframe` enum('1h','1d') NOT NULL,
  `advice` text DEFAULT NULL,
  `advice_chatgpt` text DEFAULT NULL,
  `result` enum('MONITORING','WIN','SUPER_WIN','LOSS','TIMEOUT','HOLD') DEFAULT NULL,
  `entry_price` decimal(10,2) DEFAULT NULL,
  `target_1` decimal(10,2) DEFAULT NULL,
  `target_2` decimal(10,2) DEFAULT NULL,
  `stop_loss` decimal(10,2) DEFAULT NULL,
  `monitoring_until` timestamp NULL DEFAULT NULL,
  `highest_price_reached` decimal(10,2) DEFAULT NULL,
  `result_achieved_at` timestamp NULL DEFAULT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `requests_user_id_index` (`user_id`),
  KEY `requests_timeframe_index` (`timeframe`),
  KEY `requests_created_at_index` (`created_at`),
  KEY `requests_stock_code_index` (`stock_code`),
  KEY `requests_full_name_index` (`full_name`),
  KEY `requests_email_index` (`email`),
  KEY `requests_user_id_created_at_index` (`user_id`,`created_at`),
  KEY `requests_timeframe_created_at_index` (`timeframe`,`created_at`),
  KEY `requests_user_created_index` (`user_id`,`created_at`),
  KEY `requests_created_stock_index` (`created_at`,`stock_code`),
  FULLTEXT KEY `requests_advice_fulltext` (`advice`)
) ENGINE=InnoDB AUTO_INCREMENT=60 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `requests`
--

LOCK TABLES `requests` WRITE;
/*!40000 ALTER TABLE `requests` DISABLE KEYS */;
INSERT INTO `requests` VALUES (42,'CTL','0818','coretechlead@gmail.com','TLKM.JK','Telkom Indonesia Tbk','1h','Current Price: IDR 3,250.00\nPrice 1 hour ago: IDR 3,220.00 (+0.93%)\nPrice 30 minutes: IDR 3,235.00 (+0.46%)\nTraded Volume: 4,224,300 shares\nAction: Buy at IDR 3,250\nTarget 1: IDR 3,299 (~1.5%)\nTarget 2: IDR 3,348 (~3.0%)\nStop Loss: IDR 3,185 (~2.0%)\nConfidence Level: 25%\nReason:\nThe stock has a scalping score of 4/10. The price is currently above the VWAP. The Stochastic indicators are in the oversold zone. The Bollinger Bands show the price is above the middle band. However, the stop loss is set to manage risk and protect against potential market reversals.','**Stock Analysis: TLKM.JK**\n\nBased on the current price of IDR 3,250.00, TLKM.JK has shown a positive price change of +0.93% from the previous close. The volume of 4,224,300 shares indicates a moderate level of trading activity.\n\nThe stock\'s Scalping Score of 4/10 suggests that it may not be the best candidate for scalping at this time. However, the stock is trading above its Volume Weighted Average Price (VWAP) of 3,235.23, which is a bullish signal. This indicates that buyers are willing to pay a higher price for the stock, suggesting upward price momentum.\n\nUnfortunately, without the values for the Bollinger Bands and Stochastic indicators, it\'s difficult to provide a comprehensive technical analysis. Bollinger Bands could provide insights into the stock\'s volatility and potential overbought or oversold conditions, while the Stochastic indicator could help identify potential price reversals.\n\n**Trading Advice:**\n\nGiven the limited information, I would advise a cautious approach to scalping TLKM.JK at this time. If you decide to proceed, consider the following strategy:\n\n**Buy Entry:** Look for a pullback towards the VWAP level at IDR 3,235.23. This could provide a better entry point with a lower risk.\n\n**Target Price:** Aim for a modest profit target, perhaps around IDR 3,260.00 to IDR 3,270.00. This is based on the current price momentum and the assumption that it will continue.\n\n**Risk Assessment:** Set a stop loss slightly below the VWAP, perhaps around IDR 3,220.00. This would limit potential losses if the price reverses.\n\nPlease note that this analysis is based on the limited data available and does not take into account potential market news or events that could impact the stock\'s price.\n\n**Confidence Level: 60%**\n\nThis confidence level reflects the uncertainty due to the missing technical indicators and the moderate scalping score. Always remember to manage your risk and consider your personal investment goals and tolerance for risk when making trading decisions.','TIMEOUT',3250.00,3299.00,3348.00,3185.00,'2025-08-21 03:19:13',3260.00,'2025-08-21 03:20:00',5,'2025-08-19 04:07:00','2025-09-08 03:36:41');
/*!40000 ALTER TABLE `requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('BA2d0b51sVQI8yF82ILu7y7auBNuG3FNNyMoHkIk',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiWkhyd3lrOGx4ejV3eFZ1c2xCb3phcWpyVFRrS1hDY2x4cXg4VURpVyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly90aWNrZXJhaS5sb2NhbCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1757121644),('cBygdbtwrRQij5ZV5r5HnIppZPTgYgoxcFXPbiAk',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:142.0) Gecko/20100101 Firefox/142.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoiQzZlTFlFSTQ4Z2xOQ0lqZzN1TG14djJYdEdMd2djbzZ4SUsyZVdVMCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly90aWNrZXJhaS5sb2NhbCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1757121409),('cJ7lInVJ1fxKIGRhZHgDHUGVJRTtLm3wCHYkezaV',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:142.0) Gecko/20100101 Firefox/142.0','YTozOntzOjY6Il90b2tlbiI7czo0MDoiOUt4SGV4NkJMQjhja201S1RvWm1Gb09CcjhkYUVRaGEyMnZqR0hJZyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjg6Imh0dHA6Ly90aWNrZXJhaS5sb2NhbC9zaWduaW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1756988640),('g3zuVhG4Cj9myguSik9D1OmGEeExAIVNgcbGh6X4',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:142.0) Gecko/20100101 Firefox/142.0','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',1756988984);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `mobile_number` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin','super_admin') NOT NULL DEFAULT 'user',
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_mobile_number_unique` (`mobile_number`),
  KEY `users_email_role_index` (`email`,`role`),
  KEY `users_mobile_number_index` (`mobile_number`),
  KEY `users_created_at_index` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (5,'CTL','0818','coretechlead@gmail.com','2025-08-17 05:37:50','$2y$12$pXXw8m28KNsKiKCzQ8gQNO6Xzyj04X2pyPwmJBtyLNCotuKO9b3Ia','super_admin',NULL,'2025-08-13 08:29:24','2025-09-08 03:36:41');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-09-08 12:30:23
