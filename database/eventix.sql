-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.4.3 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.8.0.6908
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Dumping database structure for eventix
CREATE DATABASE IF NOT EXISTS `eventix` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `eventix`;

-- Dumping structure for table eventix.detail_pesanan
CREATE TABLE IF NOT EXISTS `detail_pesanan` (
  `id_detail_pesanan` int NOT NULL AUTO_INCREMENT,
  `id_pesanan` int NOT NULL,
  `id_tiket` int NOT NULL,
  `jumlah` int NOT NULL,
  `sub_total` decimal(12,2) NOT NULL,
  PRIMARY KEY (`id_detail_pesanan`),
  KEY `fk_detail_pesanan` (`id_pesanan`),
  KEY `fk_detail_tiket` (`id_tiket`),
  CONSTRAINT `fk_detail_pesanan` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id_pesanan`) ON DELETE CASCADE,
  CONSTRAINT `fk_detail_tiket` FOREIGN KEY (`id_tiket`) REFERENCES `tiket` (`id_tiket`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table eventix.detail_pesanan: ~1 rows (approximately)
INSERT INTO `detail_pesanan` (`id_detail_pesanan`, `id_pesanan`, `id_tiket`, `jumlah`, `sub_total`) VALUES
	(4, 4, 18, 1, 3800000.00);

-- Dumping structure for table eventix.event
CREATE TABLE IF NOT EXISTS `event` (
  `id_event` int NOT NULL AUTO_INCREMENT,
  `venue` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_event` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_event` date NOT NULL,
  `jam_event` time NOT NULL,
  `pengisi_acara` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi` text COLLATE utf8mb4_unicode_ci,
  `poster` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_event`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table eventix.event: ~1 rows (approximately)
INSERT INTO `event` (`id_event`, `venue`, `nama_event`, `tanggal_event`, `jam_event`, `pengisi_acara`, `deskripsi`, `poster`, `created_at`) VALUES
	(8, 'Jakarta International Stadium (JIS), Jakarta Utara', 'SEVENTEEN [RIGHT HERE] World Tour in Jakarta', '2026-12-12', '18:30:00', 'SEVENTEEN', 'Konser tur dunia SEVENTEEN kembali menyapa CARAT Indonesia dengan panggung megah berskala stadion. Open gate dimulai pukul 15:00 WIB, wajib membawa e-ticket dan kartu identitas resmi saat penukaran wristband.', 'default.jpg', '2026-11-22 18:56:24'),
	(9, 'GELORA BUNG KARNO', 'nafi', '2002-02-22', '19:00:00', 'Perunggu', 'daadaa', 'default.jpg', '2026-11-22 19:52:03');

-- Dumping structure for table eventix.konser
CREATE TABLE IF NOT EXISTS `konser` (
  `id_event` int NOT NULL,
  `daftar_lagu` text COLLATE utf8mb4_unicode_ci,
  `genre_musik` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id_event`),
  CONSTRAINT `fk_konser_event` FOREIGN KEY (`id_event`) REFERENCES `event` (`id_event`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table eventix.konser: ~1 rows (approximately)
INSERT INTO `konser` (`id_event`, `daftar_lagu`, `genre_musik`) VALUES
	(8, '', 'K-Pop / Performance'),
	(9, '', 'indie');

-- Dumping structure for table eventix.pembayaran
CREATE TABLE IF NOT EXISTS `pembayaran` (
  `id_pembayaran` int NOT NULL AUTO_INCREMENT,
  `id_pesanan` int NOT NULL,
  `tanggal` datetime NOT NULL,
  `metode` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jumlah` decimal(12,2) NOT NULL,
  `bukti_bayar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('menunggu','lunas','ditolak') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'menunggu',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_pembayaran`),
  KEY `fk_pembayaran_pesanan` (`id_pesanan`),
  CONSTRAINT `fk_pembayaran_pesanan` FOREIGN KEY (`id_pesanan`) REFERENCES `pesanan` (`id_pesanan`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table eventix.pembayaran: ~2 rows (approximately)
INSERT INTO `pembayaran` (`id_pembayaran`, `id_pesanan`, `tanggal`, `metode`, `jumlah`, `bukti_bayar`, `status`, `created_at`) VALUES
	(1, 2, '2026-11-22 00:48:41', 'Transfer Bank BCA', 125000.00, 'pay_2_20261122_004841_e40b8af7.jpg', 'lunas', '2026-11-21 17:48:41'),
	(2, 3, '2026-11-22 23:59:45', 'Verifikasi Langsung Admin', 1650000.00, NULL, 'lunas', '2026-11-22 16:59:45');

-- Dumping structure for table eventix.pengguna
CREATE TABLE IF NOT EXISTS `pengguna` (
  `id_pengguna` int NOT NULL AUTO_INCREMENT,
  `nama` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alamat` text COLLATE utf8mb4_unicode_ci,
  `no_telepon` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('user','admin') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_pengguna`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table eventix.pengguna: ~2 rows (approximately)
INSERT INTO `pengguna` (`id_pengguna`, `nama`, `email`, `alamat`, `no_telepon`, `password`, `role`, `created_at`) VALUES
	(1, 'Administrator Eventix', 'admin@eventix.id', 'Gedung Cyber 2 Lt. 15, Jl. H.R. Rasuna Said, Jakarta Selatan', '081298765432', '$2y$10$Kkyb1X979om1lbGn0MFHnuyZb2BWWYScOHbFfOZAaK0cqnUbGVY2O', 'admin', '2026-11-21 17:32:28'),
	(2, 'Budi Santoso', 'budi@gmail.com', 'Jl. Tebet Timur Dalam Raya No. 42, Jakarta Selatan', '081311223344', '$2y$10$F2WYCiy5xydoLck6BlTUAOb2HQEGsu7hZq5IMWHEbxhnG0Bb5uUC2', 'user', '2026-11-21 17:32:28');

-- Dumping structure for table eventix.pesanan
CREATE TABLE IF NOT EXISTS `pesanan` (
  `id_pesanan` int NOT NULL AUTO_INCREMENT,
  `id_pengguna` int NOT NULL,
  `tanggal` datetime NOT NULL,
  `harga` decimal(12,2) NOT NULL,
  `status` enum('menunggu','menunggu_verifikasi','lunas','ditolak') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'menunggu',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_pesanan`),
  KEY `fk_pesanan_pengguna` (`id_pengguna`),
  CONSTRAINT `fk_pesanan_pengguna` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna` (`id_pengguna`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table eventix.pesanan: ~4 rows (approximately)
INSERT INTO `pesanan` (`id_pesanan`, `id_pengguna`, `tanggal`, `harga`, `status`, `created_at`) VALUES
	(1, 1, '2026-11-22 00:48:09', 125000.00, 'ditolak', '2026-11-21 17:48:09'),
	(2, 1, '2026-11-22 00:48:28', 125000.00, 'lunas', '2026-11-21 17:48:28'),
	(3, 1, '2026-11-22 23:57:35', 1650000.00, 'lunas', '2026-11-22 16:57:35'),
	(4, 1, '2026-11-23 01:58:29', 3800000.00, 'menunggu', '2026-11-22 18:58:29');

-- Dumping structure for table eventix.tiket
CREATE TABLE IF NOT EXISTS `tiket` (
  `id_tiket` int NOT NULL AUTO_INCREMENT,
  `id_event` int NOT NULL,
  `kategori` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi` text COLLATE utf8mb4_unicode_ci,
  `kuota_tersedia` int NOT NULL DEFAULT '0',
  `harga` decimal(12,2) NOT NULL,
  PRIMARY KEY (`id_tiket`),
  KEY `fk_tiket_event` (`id_event`),
  CONSTRAINT `fk_tiket_event` FOREIGN KEY (`id_event`) REFERENCES `event` (`id_event`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table eventix.tiket: ~2 rows (approximately)
INSERT INTO `tiket` (`id_tiket`, `id_event`, `kategori`, `deskripsi`, `kuota_tersedia`, `harga`) VALUES
	(18, 8, 'PINK Soundcheck', 'Standing area paling dekat panggung, akses soundcheck session, lanyard & photocard eksklusif', 199, 3800000.00),
	(19, 9, 'TIKET PERUNGGU', 'Standing area paling dekat panggung, akses soundcheck session, lanyard & photocard eksklusif', 111, 20000000.00);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
