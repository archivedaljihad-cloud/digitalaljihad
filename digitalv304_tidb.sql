DROP DATABASE IF EXISTS `digitalv304`;
CREATE DATABASE `digitalv304`;
USE `digitalv304`;

-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 23, 2026 at 02:09 PM
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
-- Database: `digitalization`
--

-- --------------------------------------------------------

--
-- Table structure for table `agenda_kajian`
--

CREATE TABLE `agenda_kajian` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `judul` varchar(255) NOT NULL,
  `pemateri` varchar(255) NOT NULL,
  `tanggal` date NOT NULL,
  `waktu` time NOT NULL,
  `lokasi` varchar(255) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `urutan` int(11) NOT NULL DEFAULT 1,
  `aktif` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `announcements`
--

CREATE TABLE `announcements` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `judul` varchar(255) NOT NULL,
  `isi` text DEFAULT NULL,
  `gambar` varchar(255) DEFAULT NULL,
  `tanggal_publish` date NOT NULL,
  `tanggal_berakhir` date DEFAULT NULL,
  `urutan` int(11) NOT NULL DEFAULT 1,
  `aktif` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `app_settings`
--

CREATE TABLE `app_settings` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `nama_aplikasi` varchar(255) NOT NULL DEFAULT 'Deteksi Penyakit Jantung',
  `favicon` varchar(255) DEFAULT NULL,
  `background` varchar(255) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `footer` text DEFAULT NULL,
  `running_text` text DEFAULT NULL,
  `auto_update_jadwal` tinyint(1) NOT NULL DEFAULT 0,
  `auto_update_frequency` varchar(255) NOT NULL DEFAULT 'daily',
  `auto_update_time` time NOT NULL DEFAULT '00:00:00',
  `auto_update_city` varchar(255) NOT NULL DEFAULT 'Jakarta',
  `auto_update_country` varchar(255) NOT NULL DEFAULT 'Indonesia',
  `auto_update_method` int(11) NOT NULL DEFAULT 11,
  `rotation_interval` int(11) NOT NULL DEFAULT 10,
  `rotation_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `rotation_pages` longtext DEFAULT NULL,
  `prayer_mode_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `prayer_mode_duration` int(11) NOT NULL DEFAULT 10,
  `prayer_mode_before_adzan` int(11) NOT NULL DEFAULT 5,
  `prayer_mode_adzan_duration` int(11) NOT NULL DEFAULT 180,
  `prayer_mode_iqamah_duration` int(11) NOT NULL DEFAULT 10,
  `prayer_mode_after_prayer` int(11) NOT NULL DEFAULT 2,
  `prayer_mode_theme` varchar(50) NOT NULL DEFAULT 'gold',
  `tarhim_trigger_seconds` int(11) NOT NULL DEFAULT 300,
  `tarhim_audio` varchar(255) DEFAULT NULL,
  `tarhim_audio_subuh` varchar(255) DEFAULT NULL,
  `tarhim_audio_reguler` varchar(255) DEFAULT NULL,
  `last_auto_update` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `app_settings`
--

INSERT INTO `app_settings` (`id`, `nama_aplikasi`, `favicon`, `background`, `logo`, `footer`, `running_text`, `auto_update_jadwal`, `auto_update_frequency`, `auto_update_time`, `auto_update_city`, `auto_update_country`, `auto_update_method`, `rotation_interval`, `rotation_enabled`, `rotation_pages`, `prayer_mode_enabled`, `prayer_mode_duration`, `prayer_mode_before_adzan`, `prayer_mode_adzan_duration`, `prayer_mode_iqamah_duration`, `prayer_mode_after_prayer`, `prayer_mode_theme`, `last_auto_update`, `created_at`, `updated_at`) VALUES
(1, 'MASJID JAMI AL- JIHAD', 'settings/ojgEVdFMGSTYpgyAZMxAIWfCbVjqYvXPOwQA0ciH.jpg', 'settings/MoTmNXn3rFl0DEx3a2t8MkSS9UDHL64T60Dtlb1e.png', 'settings/rvcMSGBiimeHugzEGeJ066Bq3MlSwn3ekYiWj7MD.png', '© 2026 brought to you by DKM AL JIHAD', '🌙 \"Hati yang tenang ada pada mereka yang selalu mengingat Allah. Mari perbanyak zikir dan shalat berjamaah.\"\r\n📖 “Ingatlah, hanya dengan mengingat Allah hati menjadi tenang.”\r\n— (QS. Ar-Ra’d: 28)', 1, 'daily', '00:00:00', 'Bekasi', 'Indonesia', 11, 3, 1, '[{\"url\":\"welcome-embed\",\"name\":\"Dashboard Lengkap\",\"active\":true},{\"url\":\"utama-embed\",\"name\":\"Jadwal Sholat\",\"active\":true},{\"url\":\"keuangan-embed\",\"name\":\"Rincian Keuangan\",\"active\":true},{\"url\":\"jumat-embed\",\"name\":\"Jadwal Sholat Jumat\",\"active\":true},{\"url\":\"pengumuman-embed\",\"name\":\"Pengumuman\",\"active\":true},{\"url\":\"keuangan-summary-embed\",\"name\":\"Ringkasan Keuangan\",\"active\":true},{\"url\":\"qris-embed\",\"name\":\"QRIS Donasi\",\"active\":true},{\"url\":\"slide-embed\",\"name\":\"Slide Informasi\",\"active\":true},{\"url\":\"idul-fitri-embed\",\"name\":\"Idul Fitri\",\"active\":true},{\"url\":\"idul-adha-embed\",\"name\":\"Idul Adha\",\"active\":true}]', 1, 10, 5, 3, 10, 2, 'gold', '2026-04-19 13:41:04', '2025-04-17 02:39:21', '2026-07-21 13:46:13'),
(3, 'Masjid Al-Ikhlas', NULL, NULL, NULL, 'Copyright &copy; <a href=\"https://wa.me/6287758767000\" target=\"_blank\">DKM AL JIHAD Dev.Syatem</a> 2026', NULL, 0, 'daily', '00:00:00', 'Jakarta', 'Indonesia', 11, 3, 1, '[{\"url\":\"welcome-embed\",\"name\":\"Dashboard Lengkap\",\"active\":true},{\"url\":\"utama-embed\",\"name\":\"Jadwal Sholat\",\"active\":true},{\"url\":\"keuangan-embed\",\"name\":\"Rincian Keuangan\",\"active\":true},{\"url\":\"jumat-embed\",\"name\":\"Jadwal Sholat Jumat\",\"active\":true},{\"url\":\"pengumuman-embed\",\"name\":\"Pengumuman\",\"active\":true},{\"url\":\"keuangan-summary-embed\",\"name\":\"Ringkasan Keuangan\",\"active\":true},{\"url\":\"qris-embed\",\"name\":\"QRIS Donasi\",\"active\":true},{\"url\":\"slide-embed\",\"name\":\"Slide Informasi\",\"active\":true},{\"url\":\"idul-fitri-embed\",\"name\":\"Idul Fitri\",\"active\":true},{\"url\":\"idul-adha-embed\",\"name\":\"Idul Adha\",\"active\":true}]', 1, 10, 5, 180, 10, 2, 'gold', NULL, '2026-04-18 18:15:14', '2026-07-21 13:46:13');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL PRIMARY KEY,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL PRIMARY KEY,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jadwal_sholat`
--

CREATE TABLE `jadwal_sholat` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `nama_sholat` varchar(255) NOT NULL,
  `waktu` time NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `durasi_jeda` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jadwal_sholat`
--

INSERT INTO `jadwal_sholat` (`id`, `nama_sholat`, `waktu`, `created_at`, `updated_at`, `durasi_jeda`) VALUES
(1, 'Subuh', '04:00:00', NULL, '2026-07-20 18:38:30', 5),
(2, 'Dzuhur', '12:00:00', NULL, '2026-04-18 16:06:59', 3),
(3, 'Ashar', '15:06:00', NULL, '2026-04-18 16:06:59', 3),
(4, 'Maghrib', '18:23:00', NULL, '2026-04-18 16:06:59', 10),
(5, 'Isya', '19:03:00', NULL, '2026-04-18 16:06:59', 10);

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
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
  `id` varchar(255) NOT NULL PRIMARY KEY,
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
-- Table structure for table `keuangan`
--

CREATE TABLE `keuangan` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `tanggal` date NOT NULL,
  `deskripsi` varchar(255) NOT NULL,
  `pemasukan` decimal(15,2) NOT NULL DEFAULT 0.00,
  `pengeluaran` decimal(15,2) NOT NULL DEFAULT 0.00,
  `saldo` decimal(15,2) NOT NULL DEFAULT 0.00,
  `kategori` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `keuangan`
--

INSERT INTO `keuangan` (`id`, `tanggal`, `deskripsi`, `pemasukan`, `pengeluaran`, `saldo`, `kategori`, `created_at`, `updated_at`) VALUES
(1, '2026-03-01', 'Saldo Awal Bulan Maret', 15000000.00, 0.00, 15000000.00, 'Saldo Awal', '2026-02-28 17:00:00', '2026-02-28 17:00:00'),
(2, '2026-03-05', 'Infak Jumat (29 Jamaah)', 1450000.00, 0.00, 16450000.00, 'Infak', '2026-03-05 06:00:00', '2026-03-05 06:00:00'),
(3, '2026-03-07', 'Donatur Tetap - Bapak H. Ahmad', 500000.00, 0.00, 16950000.00, 'Donatur', '2026-03-07 03:00:00', '2026-03-07 03:00:00'),
(4, '2026-03-10', 'Pembelian Perlengkapan Kebersihan', 0.00, 350000.00, 16600000.00, 'Operasional', '2026-03-10 01:30:00', '2026-03-10 01:30:00'),
(5, '2026-03-12', 'Infak Jumat (35 Jamaah)', 1750000.00, 0.00, 18350000.00, 'Infak', '2026-03-12 06:00:00', '2026-03-12 06:00:00'),
(6, '2026-03-15', 'Pembayaran Listrik & Air', 0.00, 1250000.00, 17100000.00, 'Utilitas', '2026-03-15 02:00:00', '2026-03-15 02:00:00'),
(7, '2026-03-18', 'Donasi Pembangunan Taman Wudhu', 2500000.00, 0.00, 19600000.00, 'Donasi Khusus', '2026-03-18 09:00:00', '2026-03-18 09:00:00'),
(8, '2026-03-20', 'Belanja Konsumsi Takjil (Ramadan)', 0.00, 800000.00, 18800000.00, 'Kegiatan', '2026-03-20 08:00:00', '2026-03-20 08:00:00'),
(9, '2026-03-25', 'Zakat Fitrah (50 orang x Rp45.000)', 2250000.00, 0.00, 21050000.00, 'Zakat', '2026-03-25 13:00:00', '2026-03-25 13:00:00'),
(10, '2026-03-28', 'Penyaluran Zakat Fitrah ke Mustahik', 0.00, 2250000.00, 18800000.00, 'Zakat', '2026-03-28 03:00:00', '2026-03-28 03:00:00'),
(11, '2026-04-01', 'Saldo Awal Bulan April', 18800000.00, 0.00, 18800000.00, 'Saldo Awal', '2026-03-31 17:00:00', '2026-03-31 17:00:00'),
(12, '2026-04-02', 'Infak Jumat (40 Jamaah)', 2000000.00, 0.00, 20800000.00, 'Infak', '2026-04-02 06:00:00', '2026-04-02 06:00:00'),
(13, '2026-04-09', 'Infak Jumat (38 Jamaah)', 1900000.00, 0.00, 22700000.00, 'Infak', '2026-04-09 06:00:00', '2026-04-09 06:00:00'),
(14, '2026-04-14', 'Pembelian Sound System Portable', 0.00, 3500000.00, 19200000.00, 'Inventaris', '2026-04-14 04:00:00', '2026-04-14 04:00:00'),
(15, '2026-04-16', 'Iuran Kebersihan Bulanan', 0.00, 400000.00, 18800000.00, 'Operasional', '2026-04-16 01:00:00', '2026-04-16 01:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
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
(4, '2025_04_18_162026_create_roles_table', 1),
(5, '2025_04_18_162027_add_role_id_to_users_table', 1),
(6, '2025_04_18_200823_create_role_widgets_table', 2),
(7, '2026_07_18_100335_create_slides_table', 3),
(8, '2026_07_18_130805_create_announcements_table', 4),
(9, '2026_07_18_140124_create_agenda_kajian_table', 5),
(10, '2026_07_19_173903_create_slide_informasis_table', 6),
(11, '2026_07_19_000001_add_durasi_to_slides_table', 7),
(12, '2026_07_19_005600_add_prayer_mode_settings', 8),
(13, '2026_07_20_231704_add_durasi_to_jadwal_sholats_table', 8);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL PRIMARY KEY,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `password_reset_tokens`
--

INSERT INTO `password_reset_tokens` (`email`, `token`, `created_at`) VALUES
('sam@gmail.com', '$2y$12$oPOrWwSJ1mmORBSFqV1bkeawUgX/xk/LkxsdJXYIsBBl4Y4z8bnYW', '2025-04-18 10:31:07');

-- --------------------------------------------------------

--
-- Table structure for table `pengumuman`
--

CREATE TABLE `pengumuman` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `isi` text NOT NULL,
  `tanggal` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pengumuman`
--

INSERT INTO `pengumuman` (`id`, `isi`, `tanggal`, `created_at`, `updated_at`) VALUES
(1, 'Kajian rutin setiap malam Sabtu selepas Isya di Masjid Al-Hikmah.', '2026-07-23', '2025-07-23 15:01:44', '2026-03-03 17:16:24'),
(2, 'Penggalangan dana untuk renovasi atap masjid dimulai pekan ini.', '2026-07-26', '2025-07-23 15:01:44', '2026-03-04 15:39:57'),
(3, 'Mohon jamaah menjaga kebersihan dan ketertiban area masjid.', '2025-07-29', '2025-07-23 15:01:44', '2025-07-23 15:32:15'),
(4, 'Sholat subuh berjamaah dilanjutkan dengan kuliah subuh setiap hari Ahad.', '2025-08-09', '2025-07-23 17:43:26', '2025-07-23 17:44:08'),
(5, 'Pelatihan tata cara pengurusan jenazah akan diadakan pekan depan.', '2025-08-17', '2025-07-23 17:43:26', '2025-07-23 17:44:21'),
(6, 'Dipersilakan jamaah yang ingin wakaf Al-Quran melalui pengurus masjid.', '2025-08-20', '2025-07-23 17:43:26', '2025-07-23 17:44:32'),
(7, 'Jadwal buka puasa bersama dan shalat tarawih perdana akan dimulai pada tanggal 1 Ramadan 1447 H.', '2026-04-25', '2026-04-01 01:00:00', '2026-04-01 01:00:00'),
(8, 'Pendaftaran santri baru TPQ Al-Ikhlas dibuka mulai 1 Mei 2026. Segera daftarkan putra-putri Anda.', '2026-05-01', '2026-04-10 02:00:00', '2026-04-10 02:00:00'),
(9, 'Kajian Islam Ahad Pagi: \"Menjemput Berkah Ramadan\" bersama Ustadz Dr. Abdullah, M.A. Pukul 07.00 WIB.', '2026-04-20', '2026-04-15 12:30:00', '2026-04-15 12:30:00'),
(10, 'Lowongan untuk petugas kebersihan masjid (shift malam). Hubungi Bpk. Hasan (0812-xxxx-xxxx).', '2026-04-30', '2026-04-18 00:15:00', '2026-04-18 00:15:00');

-- --------------------------------------------------------

--
-- Table structure for table `qris`
--

CREATE TABLE `qris` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `nama` varchar(255) NOT NULL,
  `gambar` varchar(255) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `nomor_rekening` varchar(100) DEFAULT NULL,
  `bank` varchar(100) DEFAULT NULL,
  `atas_nama` varchar(255) DEFAULT NULL,
  `status` enum('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `qris`
--

INSERT INTO `qris` (`id`, `nama`, `gambar`, `keterangan`, `nomor_rekening`, `bank`, `atas_nama`, `status`, `created_at`, `updated_at`) VALUES
(1, 'QRIS Masjid Al-Jihad', 'qris/1784447559_QRIS-cGPT.png', 'Bagi yang ingin berinfaq dan sedekah secara digital, silahkan scan QRIS ini', '011 686 685 4100', 'Bank Jawa Barat (BJB)', 'DKM Jami Al Jihad', 'aktif', '2026-05-12 11:52:11', '2026-07-19 07:52:39');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'admin', '2025-04-18 09:30:33', '2025-04-18 09:30:33'),
(2, 'petugas', '2025-04-18 09:30:33', '2025-04-18 09:30:33'),
(3, 'bendahara', '2026-09-08 00:00:00', '2026-09-08 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `role_widgets`
--

CREATE TABLE `role_widgets` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `widget_key` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL PRIMARY KEY,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sholat_idul_adha`
--

CREATE TABLE `sholat_idul_adha` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `tahun` year(4) NOT NULL,
  `tanggal` date NOT NULL,
  `imam` varchar(255) DEFAULT NULL,
  `khatib` varchar(255) DEFAULT NULL,
  `muadzin` varchar(255) DEFAULT NULL,
  `waktu` time DEFAULT '07:00:00',
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sholat_idul_adha`
--

INSERT INTO `sholat_idul_adha` (`id`, `tahun`, `tanggal`, `imam`, `khatib`, `muadzin`, `waktu`, `keterangan`, `created_at`, `updated_at`) VALUES
(1, '2026', '2026-05-27', 'Prof. Dr. H. Nur Kholis, MA', 'Ustadz Fauzan Akbar', 'Bilal', '06:30:00', '10 Dzulhijjah 1446 H', NULL, '2026-05-14 08:20:01');

-- --------------------------------------------------------

--
-- Table structure for table `sholat_idul_fitri`
--

CREATE TABLE `sholat_idul_fitri` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `tahun` year(4) NOT NULL,
  `tanggal` date NOT NULL,
  `imam` varchar(255) DEFAULT NULL,
  `khatib` varchar(255) DEFAULT NULL,
  `muadzin` varchar(255) DEFAULT NULL,
  `waktu` time DEFAULT '07:00:00',
  `keterangan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sholat_idul_fitri`
--

INSERT INTO `sholat_idul_fitri` (`id`, `tahun`, `tanggal`, `imam`, `khatib`, `muadzin`, `waktu`, `keterangan`, `created_at`, `updated_at`) VALUES
(1, '2027', '2027-03-10', 'Ustadz Dr. Abdullah, M.Ag', 'KH. Ma\'ruf Khozin', 'Saiful Anwar', '07:00:00', '1 Syawal 1446 H', NULL, '2026-05-14 08:19:21');

-- --------------------------------------------------------

--
-- Table structure for table `sholat_jumat`
--

CREATE TABLE `sholat_jumat` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `imam` varchar(255) DEFAULT NULL,
  `khatib` varchar(255) DEFAULT NULL,
  `muadzin` varchar(255) DEFAULT NULL,
  `bilal` varchar(255) DEFAULT NULL,
  `tanggal` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sholat_jumat`
--

INSERT INTO `sholat_jumat` (`id`, `imam`, `khatib`, `muadzin`, `tanggal`, `created_at`, `updated_at`) VALUES
(1, 'Ustad Abdul Muin', 'Ustad Abdul Muin', 'Ahmad Najib', '2025-07-25', '2025-07-23 14:53:52', '2025-07-23 14:53:52'),
(2, 'Ust. Ahmad Fauzi', 'KH. Mahmud Hasan', 'Ali Maulana', '2025-08-01', '2025-07-23 14:59:32', '2025-07-23 15:00:59'),
(3, 'Ust. Rizal Hakim', 'KH. Zainuddin', 'Fadli Rahman', '2025-08-08', '2025-07-23 14:59:32', '2025-07-23 15:01:09'),
(5, 'Ustadz Dr. Nurcholis, M.Ag', 'KH. Ma\'ruf Khozin', 'Saiful Anwar', '2026-05-01', '2026-04-18 11:00:00', '2026-04-18 11:00:00'),
(6, 'Ustadz Fauzan Akbar', 'Ustadz Fauzan Akbar', 'Hamzah', '2026-05-08', '2026-04-18 11:00:00', '2026-04-18 11:00:00'),
(7, 'KH. Syamsul Arifin', 'Prof. Dr. H. Nur Kholis, MA', 'Bilal', '2026-05-15', '2026-04-18 11:00:00', '2026-04-18 11:00:00'),
(8, 'Ustd. Suwardi Al Patini', 'Bpk. Mansur', 'Bpk. Mansur juga', '2026-10-07', '2026-07-20 02:54:03', '2026-07-20 02:54:03');

-- --------------------------------------------------------

--
-- Table structure for table `slides`
--

CREATE TABLE `slides` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `judul` varchar(255) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `gambar` varchar(255) NOT NULL,
  `urutan` int(11) NOT NULL DEFAULT 0,
  `durasi` int(10) UNSIGNED NOT NULL DEFAULT 10 COMMENT 'Durasi tampil slide dalam detik',
  `aktif` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `slides`
--

INSERT INTO `slides` (`id`, `judul`, `deskripsi`, `gambar`, `urutan`, `durasi`, `aktif`, `created_at`, `updated_at`) VALUES
(2, 'Qiblat Sertifikat', 'Sertifikasi Gerakan Nasional 1.148K Rasdhul Qiblat', 'slides/iJ405oSm0AMLVGmy8cjCcAXDsdw8niSYqGxtBCKW.png', 1, 3, 1, '2026-07-18 04:43:42', '2026-07-20 02:48:22'),
(3, 'Arah Qiblat', 'Hasil pengecekkan arah qiblat pada hari Kamis, 16 Juli 2026 Jam: 16:27 WIB', 'slides/GkxyYVJO2IdZoU1X6mgSNUcghs0gu1HqVtYlxgYA.png', 2, 3, 1, '2026-07-19 18:57:25', '2026-07-20 02:48:30');

-- --------------------------------------------------------

--
-- Table structure for table `slide_informasis`
--

CREATE TABLE `slide_informasis` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `judul` varchar(255) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `gambar` varchar(255) NOT NULL,
  `urutan` int(11) NOT NULL DEFAULT 1,
  `durasi` int(11) NOT NULL DEFAULT 8,
  `status` enum('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `role_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `last_name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `role_id`) VALUES
(1, 'Super', 'Admin', 'archived.aljihad@gmail.com', NULL, '$2y$12$mN8feXaqXmnEofT9UgMuUeDf.AqlPTc93ENTvbPcdaZrOfJIBsdaq', 'MXbggeE2MX7jMabixa5dhcI7ITURrUUYNzGVSw8AkWSPM4shKezhtnEawET1', '2025-04-05 03:24:59', '2026-07-23 09:45:01', 1),
(22, 'Samsuri', 'Wijaya', 'sam@sam.com', NULL, '$2y$12$Tq6LBvMqlgdkUcEpGb7ZPO7MvdAu5e5hJzAwHvacKWPY/BryB3Etq', NULL, '2026-04-18 18:15:40', '2026-04-18 18:15:40', 2),
(24, 'Bendahara', 'Masjid', 'bendahara@masjid.com', '2026-03-31 17:00:00', '$2y$12$q2W5k5pP8XH9Z1aB3cD4eF5gH6iJ7kL8mN9oP0qR1sT2uV3wX4yZ5A6bC7dE8fG', NULL, '2026-04-01 01:00:00', '2026-04-01 01:00:00', 3),
(25, 'Petugas', 'Keamanan', 'satpam@masjid.com', '2026-04-01 17:00:00', '$2y$12$q2W5k5pP8XH9Z1aB3cD4eF5gH6iJ7kL8mN9oP0qR1sT2uV3wX4yZ5A6bC7dE8fG', NULL, '2026-04-02 02:00:00', '2026-04-02 02:00:00', 2),
(26, 'Operator', 'Takmir', 'operator@masjid.com', '2026-04-02 17:00:00', '$2y$12$q2W5k5pP8XH9Z1aB3cD4eF5gH6iJ7kL8mN9oP0qR1sT2uV3wX4yZ5A6bC7dE8fG', NULL, '2026-04-03 03:00:00', '2026-04-03 03:00:00', 2);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `agenda_kajian`
--
--
-- Indexes for table `announcements`
--
--
-- Indexes for table `app_settings`
--
--
-- Indexes for table `cache`
--
--
-- Indexes for table `cache_locks`
--
--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jadwal_sholat`
--
--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
--
-- Indexes for table `keuangan`
--
--
-- Indexes for table `migrations`
--
--
-- Indexes for table `password_reset_tokens`
--
--
-- Indexes for table `pengumuman`
--
--
-- Indexes for table `qris`
--
--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `role_widgets`
--
ALTER TABLE `role_widgets`
  ADD KEY `role_widgets_role_id_foreign` (`role_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `sholat_idul_adha`
--
ALTER TABLE `sholat_idul_adha`
  ADD UNIQUE KEY `sholat_idul_adha_tahun_unique` (`tahun`);

--
-- Indexes for table `sholat_idul_fitri`
--
ALTER TABLE `sholat_idul_fitri`
  ADD UNIQUE KEY `sholat_idul_fitri_tahun_unique` (`tahun`);

--
-- Indexes for table `sholat_jumat`
--
--
-- Indexes for table `slides`
--
--
-- Indexes for table `slide_informasis`
--
--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_role_id_foreign` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `agenda_kajian`
--
--
-- AUTO_INCREMENT for table `announcements`
--
--
-- AUTO_INCREMENT for table `app_settings`
--
--
-- AUTO_INCREMENT for table `failed_jobs`
--
--
-- AUTO_INCREMENT for table `jadwal_sholat`
--
--
-- AUTO_INCREMENT for table `jobs`
--
--
-- AUTO_INCREMENT for table `keuangan`
--
--
-- AUTO_INCREMENT for table `migrations`
--
--
-- AUTO_INCREMENT for table `pengumuman`
--
--
-- AUTO_INCREMENT for table `qris`
--
--
-- AUTO_INCREMENT for table `roles`
--
--
-- AUTO_INCREMENT for table `role_widgets`
--
--
-- AUTO_INCREMENT for table `sholat_idul_adha`
--
--
-- AUTO_INCREMENT for table `sholat_idul_fitri`
--
--
-- AUTO_INCREMENT for table `sholat_jumat`
--
--
-- AUTO_INCREMENT for table `slides`
--
--
-- AUTO_INCREMENT for table `slide_informasis`
--
--
-- AUTO_INCREMENT for table `users`
--
--
-- Constraints for dumped tables
--

--
-- Constraints for table `role_widgets`
--
ALTER TABLE `role_widgets`
  ADD CONSTRAINT `role_widgets_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
