-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 31, 2025 at 10:29 AM
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
-- Database: `stock_analytics`
--

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('stockanalytic-cache-company_name_BBRI', 's:25:\"Bank Rakyat Indonesia Tbk\";', 1753917322),
('stockanalytic-cache-company_name_BMRI', 's:16:\"Bank Mandiri Tbk\";', 1753917355);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

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
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(8, '2025_07_13_092146_create_requests_table', 2),
(9, '2025_07_30_221352_add_indexes_to_requests_table', 3),
(10, '2025_07_31_005521_add_unique_constraint_to_mobile_number_in_users_table', 4);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `requests`
--

CREATE TABLE `requests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `mobile_number` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `stock_code` varchar(255) NOT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `timeframe` enum('1h','1d') NOT NULL,
  `advice` text DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `requests`
--

INSERT INTO `requests` (`id`, `full_name`, `mobile_number`, `email`, `stock_code`, `company_name`, `timeframe`, `advice`, `user_id`, `created_at`, `updated_at`) VALUES
(19, 'Anom Brams', '081808281688', 'anombrams@gmail.com', 'BBRI.JK', 'Bank Rakyat Indonesia Tbk', '1h', '📊 **INFORMASI SAHAM:**\n- Harga Saat Ini: IDR 3,740.00\n- Perubahan: IDR -40.00 (-1.06%)\n- Harga Sebelumnya: IDR 3,780.00\n- Tertinggi Hari Ini: IDR 3,790.00\n- Terendah Hari Ini: IDR 3,730.00\n- Volume Hari Ini: 63,694,500 lembar\n- Tertinggi 52 Minggu: IDR 5,575.00\n- Terendah 52 Minggu: IDR 3,360.00\n- Bursa: JKT\n\n📈 **DATA 1 hour TERAKHIR:**\n- Open: IDR 3,740.00\n- High: IDR 3,740.00\n- Low: IDR 3,740.00\n- Close: IDR 3,740.00\n\n🔍 **ANALISIS TEKNIS:**\n1. **Ringkasan Teknis**: BBRI.JK sedang mengalami penurunan harga dalam satu jam terakhir.\n2. **Level Support & Resistance**: \n   - Support: IDR 3,730.00 (terendah hari ini), IDR 3,600.00 (psikologis)\n   - Resistance: IDR 3,790.00 (tertinggi hari ini), IDR 3,850.00\n3. **Penilaian Risiko**: Risiko pasar global, fluktuasi nilai tukar, dan penurunan kinerja sektor perbankan di Indonesia.\n4. **Rekomendasi**: SELL.\n5. **Target Harga**: Target jangka 1 hour adalah IDR 3,700.00 karena tren penurunan yang masih kuat.\n\nDengan kondisi pasar yang cenderung turun, disarankan untuk menjual saham BBRI.JK dalam jangka pendek dan mengamati perkembangan lebih lanjut sebelum mempertimbangkan untuk membeli kembali.', 9, '2025-07-30 17:50:49', '2025-07-30 21:58:17'),
(20, 'Anom Brams', '081808281688', 'anombrams@gmail.com', 'TLKM.JK', 'Telkom Indonesia Tbk', '1h', '📊 **INFORMASI SAHAM:**\n- Harga Saat Ini: IDR 2,890.00\n- Perubahan: +IDR 10.00 (+0.35%)\n- Harga Sebelumnya: IDR 2,880.00\n- Tertinggi Hari Ini: IDR 2,930.00\n- Terendah Hari Ini: IDR 2,860.00\n- Volume Hari Ini: 37,163,400 lembar\n- Tertinggi 52 Minggu: IDR 3,190.00\n- Terendah 52 Minggu: IDR 2,050.00\n- Bursa: JKT\n\n📈 **DATA 1 hour TERAKHIR:**\n- Open: IDR 2,890.00\n- High: IDR 2,890.00\n- Low: IDR 2,890.00\n- Close: IDR 2,890.00\n\n🔍 **ANALISIS TEKNIS:**\n1. **Ringkasan Teknis**: TLKM.JK menunjukkan konsolidasi dalam satu jam terakhir dengan harga tetap pada level pembukaan.\n2. **Level Support & Resistance**: \n   - Support: IDR 2,860.00\n   - Resistance: IDR 2,930.00\n3. **Penilaian Risiko**: Risiko utama adalah ketidakpastian pasar global dan fluktuasi nilai tukar. Potensi downside terkait dengan penurunan tiba-tiba di sektor telekomunikasi.\n4. **Rekomendasi**: HOLD\n   - TLKM.JK menunjukkan stabilisasi harga dalam jangka pendek, namun investor perlu waspada terhadap pergerakan pasar yang lebih luas.\n5. **Target Harga**: \n   - Target jangka 1 hour: IDR 2,930.00\n   - Reasoning: Jika TLKM.JK berhasil menembus resistance pada IDR 2,930.00, ada potensi kenaikan lebih lanjut.\n\nDalam kondisi saat ini, disarankan untuk mempertahankan posisi (HOLD) dengan memperhatikan level support dan resistance serta mengikuti perkembangan pasar secara cermat sebelum mengambil keputusan investasi lebih', 9, '2025-07-30 17:51:05', '2025-07-30 21:58:22'),
(21, 'Core Tech Lead', '081803691688', 'coretechlead@gmail.com', 'BBCA.JK', 'Bank Central Asia Tbk', '1h', ' **INFORMASI SAHAM:**\\n- Harga Saat Ini: IDR 8,275.00\\n- Perubahan: +IDR 0.00 (+0.00%)\\n- Volume Hari Ini: 76,430,500 lembar\\n\\n **ANALISIS TEKNIS:**\\n1. **Ringkasan Teknis**: Harga BBCA.JK stagnan dengan sedikit fluktuasi, menunjukkan konsolidasi.\\n2. **Level Support & Resistance**: Support IDR 8,200.00, Resistance IDR 8,400.00\\n3. **Penilaian Risiko**: Risiko terkait penurunan ekonomi global dan peningkatan NPL di sektor perbankan Indonesia.\\n4. **Rekomendasi**: HOLD. Tahan posisi saat ini karena belum ada sinyal kuat untuk BUY atau SELL.\\n5. **Target Harga**: Target jangka 1 day sekitar IDR 8,350.00, mengingat konsolidasi harga saat ini.', 1, '2025-07-30 18:07:04', '2025-07-30 18:07:04'),
(22, 'Anom Brams', '081808281688', 'anombrams@gmail.com', 'BBCA.JK', 'Bank Central Asia Tbk', '1h', ' **INFORMASI SAHAM:**\\n- Harga Saat Ini: IDR 8,275.00\\n- Perubahan: +IDR 0.00 (+0.00%)\\n- Volume Hari Ini: 76,430,500 lembar\\n\\n **ANALISIS TEKNIS:**\\n1. **Ringkasan Teknis**: Harga BBCA.JK stagnan dengan sedikit fluktuasi, menunjukkan konsolidasi.\\n2. **Level Support & Resistance**: Support IDR 8,200.00, Resistance IDR 8,400.00\\n3. **Penilaian Risiko**: Risiko terkait penurunan ekonomi global dan peningkatan NPL di sektor perbankan Indonesia.\\n4. **Rekomendasi**: HOLD. Tahan posisi saat ini karena belum ada sinyal kuat untuk BUY atau SELL.\\n5. **Target Harga**: Target jangka 1 day sekitar IDR 8,350.00, mengingat konsolidasi harga saat ini.', 9, '2025-07-30 21:36:12', '2025-07-30 21:36:12'),
(23, 'Anom Brams', '081808281688', 'anombrams@gmail.com', 'BBRI.JK', 'Bank Rakyat Indonesia Tbk', '1h', '📊 **INFORMASI SAHAM:**\n- Harga Saat Ini: IDR 3,740.00\n- Perubahan: IDR -40.00 (-1.06%)\n- Harga Sebelumnya: IDR 3,780.00\n- Tertinggi Hari Ini: IDR 3,790.00\n- Terendah Hari Ini: IDR 3,730.00\n- Volume Hari Ini: 63,134,800 lembar\n- Tertinggi 52 Minggu: IDR 5,575.00\n- Terendah 52 Minggu: IDR 3,360.00\n- Bursa: JKT\n\n📈 **DATA 1 hour TERAKHIR:**\n- Open: IDR 3,740.00\n- High: IDR 3,740.00\n- Low: IDR 3,740.00\n- Close: IDR 3,740.00\n\n🔍 **ANALISIS TEKNIS:**\n1. **Ringkasan Teknis**: Harga BBRI.JK sedang mengalami penurunan dengan momentum negatif. Pola pergerakan cenderung sideways.\n   \n2. **Level Support & Resistance**:\n   - Support: IDR 3,730.00 (Terendah Hari Ini)\n   - Resistance: IDR 3,790.00 (Tertinggi Hari Ini)\n   \n3. **Penilaian Risiko**: Risiko utama adalah kondisi pasar global, fluktuasi nilai tukar, dan kinerja sektor perbankan. Potensi downside terkait penurunan lebih lanjut dalam tren harga saat ini.\n\n4. **Rekomendasi**: SELL. Dengan momentum negatif dan pola pergerakan sideways, disarankan untuk menjual posisi saat ini.\n\n5. **Target Harga**: Target jangka 1 hour adalah IDR 3,700.00. Diperkirakan harga akan melanjutkan penurunan menuju level support terdekat.\n\nSebagai investor, penting untuk memperhatikan perubahan kondisi pasar dan melakukan evaluasi secara berkala. Analisis ini hanya bersifat informatif dan bukan sebagai rekomendasi pasti.', 9, '2025-07-30 21:45:00', '2025-07-30 21:56:14'),
(24, 'Anom Brams', '081808281688', 'anombrams@gmail.com', 'TLKM.JK', 'Telkom Indonesia Tbk', '1d', '📊 **INFORMASI SAHAM:**\n- Harga Saat Ini: IDR 2,900.00\n- Perubahan: +IDR 0.00 (+0.00%)\n- Harga Sebelumnya: IDR 2,900.00\n- Tertinggi Hari Ini: IDR 2,930.00\n- Terendah Hari Ini: IDR 2,860.00\n- Volume Hari Ini: 56,946,000 lembar\n- Tertinggi 52 Minggu: IDR 3,190.00\n- Terendah 52 Minggu: IDR 2,050.00\n- Bursa: JKT\n\n📈 **DATA 1 day TERAKHIR:**\n- Open: IDR 2,890.00\n- High: IDR 2,930.00\n- Low: IDR 2,860.00\n- Close: IDR 2,900.00 (Volume: 56,946,000 saham, 24x rata-rata)\n\n🔍 **ANALISIS TEKNIS:**\n1. **Ringkasan Teknis**: TLKM.JK menunjukkan harga stabil dengan sedikit pergerakan. Momentum relatif datar.\n   \n2. **Level Support & Resistance**:\n   - Support: IDR 2,860.00\n   - Resistance: IDR 2,930.00\n   \n3. **Penilaian Risiko**: Faktor risiko utama termasuk fluktuasi pasar global, persaingan industri, dan regulasi telekomunikasi. Potensi downside terkait dengan penurunan permintaan layanan.\n\n4. **Rekomendasi**: HOLD. Saat ini, harga bergerak sideways tanpa indikasi yang jelas. Menunggu konfirmasi tren adalah tindakan yang bijaksana.\n\n5. **Target Harga**: Target jangka 1 day sekitar IDR 2,920.00. Investor dapat mempertimbangkan untuk entry jika harga berhasil menembus resistance di IDR 2,930.00.\n\nDengan kondisi saat ini, disarankan untuk tetap waspada dan melakukan analisis lebih lanjut sebelum membuat keputusan investasi.', 9, '2025-07-30 22:04:01', '2025-07-31 01:21:28'),
(25, 'Anom Brams', '081808281688', 'anombrams@gmail.com', 'UNVR.JK', 'Unilever Indonesia Tbk', '1h', '📊 **INFORMASI SAHAM:**\n- Harga Saat Ini: IDR 1,705.00\n- Perubahan: +IDR 175.00 (+11.44%)\n- Harga Sebelumnya: IDR 1,530.00\n- Tertinggi Hari Ini: IDR 1,740.00\n- Terendah Hari Ini: IDR 1,525.00\n- Volume Hari Ini: 191,434,100 lembar\n- Tertinggi 52 Minggu: IDR 2,540.00\n- Terendah 52 Minggu: IDR 985.00\n- Bursa: JKT\n\n📈 **DATA 1 hour TERAKHIR:**\n- Open: IDR 1,705.00\n- High: IDR 1,705.00\n- Low: IDR 1,705.00\n- Close: IDR 1,705.00\n\n🔍 **ANALISIS TEKNIS:**\n1. **Ringkasan Teknis**: UNVR.JK mengalami lonjakan harga signifikan dengan momentum positif. Pola pergerakan menunjukkan tren bullish kuat.\n   \n2. **Level Support & Resistance**:\n   - Support: IDR 1,600.00 (entry point)\n   - Resistance: IDR 1,740.00 (breakout point)\n\n3. **Penilaian Risiko**: Faktor risiko utama adalah potensi koreksi pasar atau sentimen global yang merugikan. Potensi downside mungkin terjadi jika harga gagal menembus resistance.\n\n4. **Rekomendasi**: BUY. Kenaikan harga yang kuat menunjukkan potensi pertumbuhan lebih lanjut.\n\n5. **Target Harga**: Target jangka 1 hour adalah IDR 1,800.00. Dengan tren bullish yang kuat, harga dapat melanjutkan kenaikan menuju resistance berikutnya.\n\nDengan pertimbangan di atas, disarankan untuk mempertimbangkan investasi dengan hati-hati dan memantau perkembangan harga secara berkala.', 9, '2025-07-30 22:09:26', '2025-07-31 01:21:36'),
(26, 'Anom Brams', '081808281688', 'anombrams@gmail.com', 'BBCA.JK', 'Bank Central Asia Tbk', '1h', '📊 **INFORMASI SAHAM:**\n- Harga Saat Ini: IDR 8,275.00\n- Perubahan: IDR -100.00 (-1.19%)\n- Harga Sebelumnya: IDR 8,375.00\n- Tertinggi Hari Ini: IDR 8,375.00\n- Terendah Hari Ini: IDR 8,250.00\n- Volume Hari Ini: 92,486,200 lembar\n- Tertinggi 52 Minggu: IDR 10,950.00\n- Terendah 52 Minggu: IDR 7,275.00\n- Bursa: JKT\n\n📈 **DATA 1 hour TERAKHIR:**\n- Open: IDR 8,275.00\n- High: IDR 8,275.00\n- Low: IDR 8,275.00\n- Close: IDR 8,275.00\n\n🔍 **ANALISIS TEKNIS:**\n1. **Ringkasan Teknis**: Harga saat ini sedang mengalami penurunan, dengan pola stagnan dalam 1 hour terakhir.\n2. **Level Support & Resistance**:\n   - Support: IDR 8,250.00 (Terendah Hari Ini)\n   - Resistance: IDR 8,375.00 (Tertinggi Hari Ini)\n3. **Penilaian Risiko**: Risiko utama termasuk volatilitas pasar dan situasi ekonomi makro.\n4. **Rekomendasi**: SELL. Dengan penurunan harga saat ini, disarankan untuk menjual posisi atau menunggu konfirmasi pembalikan tren sebelum entry.\n5. **Target Harga**: Target jangka 1 hour adalah IDR 8,200.00. Diperkirakan harga akan melanjutkan penurunan menuju level support berikutnya.\n\nIni adalah analisis singkat berdasarkan data saat ini. Selalu lakukan analisis menyeluruh dan pertimbangkan risiko investasi sebelum membuat keputusan.', 9, '2025-07-30 23:24:18', '2025-07-30 23:24:48'),
(28, 'Anom Brams', '081808281688', 'anombrams@gmail.com', 'UNVR.JK', 'Unilever Indonesia Tbk', '1h', '📊 **INFORMASI SAHAM:**\n- Harga Saat Ini: IDR 1,685.00\n- Perubahan: +IDR 155.00 (+10.13%)\n- Harga Sebelumnya: IDR 1,530.00\n- Tertinggi Hari Ini: IDR 1,740.00\n- Terendah Hari Ini: IDR 1,525.00\n- Volume Hari Ini: 175,017,000 lembar\n- Tertinggi 52 Minggu: IDR 2,540.00\n- Terendah 52 Minggu: IDR 985.00\n- Bursa: JKT\n\n📈 **DATA 1 hour TERAKHIR:**\n- Open: IDR 1,685.00\n- High: IDR 1,685.00\n- Low: IDR 1,685.00\n- Close: IDR 1,685.00\n\n🔍 **ANALISIS TEKNIS:**\n1. **Ringkasan Teknis**: UNVR.JK mengalami kenaikan signifikan (+10.13%) dengan harga saat ini di atas harga sebelumnya. Momentum positif terlihat dari peningkatan volume perdagangan.\n   \n2. **Level Support & Resistance**:\n   - Support: IDR 1,530.00 (harga sebelumnya)\n   - Resistance: IDR 1,740.00 (tertinggi hari ini)\n\n3. **Penilaian Risiko**: Risiko utama termasuk ketidakpastian pasar global, fluktuasi nilai tukar, dan potensi dampak dari perubahan regulasi di Indonesia. Potensi downside terkait dengan koreksi harga pasca kenaikan tajam.\n\n4. **Rekomendasi**: HOLD. Meskipun terjadi kenaikan yang signifikan, disarankan untuk menahan posisi saat ini dan memantau perkembangan lebih lanjut.\n\n5. **Target Harga**: Target jangka 1 hour adalah IDR 1,740.00. Jika harga berhasil menembus resistance tersebut dengan volume yang kuat, bisa menjadi sinyal untuk pembelian lebih lanjut dengan target potensial ke level IDR 2,000.00 dalam beberapa minggu ke depan.\n\nSebagai investor,', 9, '2025-07-31 00:33:05', '2025-07-31 00:33:14'),
(29, 'Anom Brams', '081808281688', 'anombrams@gmail.com', 'BBRI.JK', 'Bank Rakyat Indonesia Tbk', '1h', '📊 **INFORMASI SAHAM:**\n- Harga Saat Ini: IDR 3,720.00\n- Perubahan: IDR -60.00 (-1.59%)\n- Volume Hari Ini: 100,600,500 lembar\n- Tertinggi 52 Minggu: IDR 5,575.00\n- Terendah 52 Minggu: IDR 3,360.00\n\n📈 **DATA 1 hour TERAKHIR:**\n- Open: IDR 3,720.00\n- High: IDR 3,720.00\n- Low: IDR 3,720.00\n- Close: IDR 3,720.00\n\n🔍 **ANALISIS TEKNIS:**\n1. **Ringkasan Teknis**: Bank Rakyat Indonesia (BBRI.JK) saat ini sedang konsolidasi dengan sedikit tekanan jual.\n2. **Level Support & Resistance**:\n   - Support: IDR 3,710.00\n   - Resistance: IDR 3,790.00\n3. **Penilaian Risiko**: Risiko utama termasuk ketidakpastian ekonomi global dan domestik serta potensi penurunan harga saham lebih lanjut.\n4. **Rekomendasi**: HOLD. Tahan posisi saat ini karena harga sedang dalam fase konsolidasi.\n5. **Target Harga**: Dengan kondisi pasar saat ini, target jangka 1 hour adalah IDR 3,750.00. Namun, perhatikan pergerakan pasar global dan sentimen pasar lokal.\n\nDalam situasi saat ini, disarankan untuk mempertahankan posisi dan memantau perkembangan lebih lanjut sebelum membuat keputusan investasi yang lebih besar.', 9, '2025-07-31 00:56:17', '2025-07-31 01:19:42');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('4HL53dsrLfrKDklCeq4F7CXv2inWnd07RePrpskr', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT; Windows NT 10.0; en-US) WindowsPowerShell/5.1.26100.4652', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiZ01pazBDYnMyeXBvOHhBbHpCWEJvVkxWcEpYa3hwRjZTVDlNMWl6bSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1753936411),
('Ktu0F22UrPkEa22LrHE0tONgcarNkf0H4S26kNVY', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:141.0) Gecko/20100101 Firefox/141.0', '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', 1753938606),
('L4TOqjT0bv90kQ7Ywqq4IbFEpRaQSs0Ly866Gt0n', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36 Edg/138.0.0.0', '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', 1753938761),
('lcc5BLpJi1bIVxup0kLeM2ZIDvKXb03PE6JcRGPN', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:141.0) Gecko/20100101 Firefox/141.0', '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', 1753950069),
('XaOVHfbs0bN8SZnkwma7EPhdsFm1V6rVj2tFSji0', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:141.0) Gecko/20100101 Firefox/141.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiWWQwMmFTSzdVaTRvam1ld2hRSXpwb1hBU090SlhyTTZCVk43V01VaCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1753936633);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `mobile_number` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','user') NOT NULL DEFAULT 'user',
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `mobile_number`, `email`, `email_verified_at`, `password`, `role`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Core Tech Lead', '081803691688', 'coretechlead@gmail.com', NULL, '$2y$10$jueELmtUpEzOUlt/VXJA8upRLJ3KUcn2EJp/vVXNp4uFnC4/Y9wa2', 'admin', NULL, '2025-07-13 21:11:08', '2025-07-13 21:11:08'),
(9, 'Anom Brams', '081808281688', 'anombrams@gmail.com', NULL, '$2y$12$50/iF8K96fN5/DLMFzr8yOTgRCrZgQ3caCRHTn.3bTieng9fTa6gm', 'user', NULL, '2025-07-30 16:18:24', '2025-07-30 17:45:57'),
(11, 'Anom', '08170833418', 'anombrams@outlook.com', NULL, '$2y$12$BMc8mUqXTxMgmwkP1DqebOgLdssIlYUuhZKyDvwTl0JWIGp1v8ZTW', 'user', NULL, '2025-07-30 17:58:49', '2025-07-30 17:58:49');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `requests`
--
ALTER TABLE `requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `requests_user_id_index` (`user_id`),
  ADD KEY `requests_timeframe_index` (`timeframe`),
  ADD KEY `requests_created_at_index` (`created_at`),
  ADD KEY `requests_stock_code_index` (`stock_code`),
  ADD KEY `requests_full_name_index` (`full_name`),
  ADD KEY `requests_email_index` (`email`),
  ADD KEY `requests_user_id_created_at_index` (`user_id`,`created_at`),
  ADD KEY `requests_timeframe_created_at_index` (`timeframe`,`created_at`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_mobile_number_unique` (`mobile_number`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `requests`
--
ALTER TABLE `requests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
