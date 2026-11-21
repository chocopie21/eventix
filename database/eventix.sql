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

-- Dumping structure for table eventix.detail_pesanan
DROP TABLE IF EXISTS `detail_pesanan`;
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table eventix.detail_pesanan: ~0 rows (approximately)

-- Dumping structure for table eventix.event
DROP TABLE IF EXISTS `event`;
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
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table eventix.event: ~6 rows (approximately)
INSERT INTO `event` (`id_event`, `venue`, `nama_event`, `tanggal_event`, `jam_event`, `pengisi_acara`, `deskripsi`, `poster`, `created_at`) VALUES
	(1, 'Stadion Madya Gelora Bung Karno, Senayan, Jakarta', 'Sheila on 7: Tunggu Aku Di Jakarta 2026', '2026-12-19', '19:30:00', 'Sheila on 7, Guest Star: Perunggu', 'Konser tunggal penutup tahun yang spektakuler dari band legendaris Yogyakarta, Sheila on 7! Nikmati nostalgia lebih dari 20 lagu hits terbaik bersama puluhan ribu Sheila Gank dalam tata panggung dan visual panggung berstandar internasional.', 'sheila_on_7.jpg', '2026-11-21 17:32:28'),
	(2, 'Jakarta International Stadium (JIS), Jakarta Utara', 'Dewa 19 feat. All Stars: Stadium Tour 2026', '2026-12-27', '19:00:00', 'Dewa 19, Ari Lasso, Once Mekel, Marcello Tahitoe (Ello), Virzha', 'Kembalinya mahakarya musik rock Indonesia terbesar sepanjang masa! Menampilkan formasi 4 vokalis legendaris sekaligus dalam satu panggung megah di stadion termegah Indonesia.', 'dewa_19.jpg', '2026-11-21 17:32:28'),
	(3, 'Tennis Indoor Senayan, Jakarta Pusat', 'Hindia: Lagipula Hidup Akan Berakhir (Special Live Showcase)', '2027-01-16', '20:00:00', 'Hindia, Blue Valley Radio', 'Pertunjukan spesial dari Baskara Putra membawakan album ganda Lagipula Hidup Akan Berakhir secara penuh dengan tata cahaya teatrikal serta aransemen orkestrasi megah.', 'hindia.jpg', '2026-11-21 17:32:28'),
	(4, 'Grand City Convention & Exhibition Hall, Surabaya', 'Tulus: Manusia Merayakan Live in Concert Surabaya', '2027-01-30', '19:30:00', 'Tulus, Opening Act: Dere', 'Rayakan hangatnya perjalanan hidup dan kisah manusia bersama Tulus. Suasana intim, iringan big band mewah, dan alunan vokal khas yang menyentuh relung hati pendengar di Surabaya.', 'tulus.jpg', '2026-11-21 17:32:28'),
	(5, 'Eldorado Dome, Bandung', 'Bernadya: Sialnya Hidup Harus Tetap Berjalan Tour Bandung', '2027-02-14', '19:00:00', 'Bernadya', 'Malam penuh luapan emosi, patah hati, dan pemulihan diri di Kota Kembang bertepatan di hari kasih sayang. Bernyanyi bersama Bernadya membawakan seluruh lagu dari album fenomena tahun ini.', 'bernadya.jpg', '2026-11-21 17:32:28'),
	(7, 'GELORA BUNG KARNO', 'Perunggu: Gemilang Live Showcase', '2026-12-04', '19:00:00', 'Perunggu', 'Konser intim Perunggu membawakan lagu-lagu album Gemilang.', 'default.jpg', '2026-11-21 17:52:00');

-- Dumping structure for table eventix.konser
DROP TABLE IF EXISTS `konser`;
CREATE TABLE IF NOT EXISTS `konser` (
  `id_event` int NOT NULL,
  `daftar_lagu` text COLLATE utf8mb4_unicode_ci,
  `genre_musik` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id_event`),
  CONSTRAINT `fk_konser_event` FOREIGN KEY (`id_event`) REFERENCES `event` (`id_event`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table eventix.konser: ~6 rows (approximately)
INSERT INTO `konser` (`id_event`, `daftar_lagu`, `genre_musik`) VALUES
	(1, 'Dan, Sahabat Sejati, Sephia, Pejantan Tangguh, Melompat Lebih Tinggi, Kisah Klasik Untuk Masa Depan, Kita, Tunggu Aku Di Jakarta, Film Favorit, Hari Bersamanya, Anugerah Terindah Yang Pernah Kumiliki', 'Pop Rock'),
	(2, 'Pangeran Cinta, Roman Picisan, Kangen, Arjuna, Pupus, Dua Sejoli, Kamulah Satu-Satunya, Risalah Hati, Separuh Nafas, Takkan Ada Cinta Yang Lain, Cemburu', 'Rock'),
	(3, 'Janji Palsu, Masalah Masa Depan, Berdansalah, Karir, Peradaban, Rumah Ke Rumah, Secukupnya, Kita Ke Sana, Evaluasi, Si Lemah', 'Indie Pop / Alternative'),
	(4, 'Hati-Hati di Jalan, Monokrom, Pamit, Sepatu, Teman Hidup, Diri, Sewindu, Gajah, Interaksi, Jatuh Suka, Satu Kali', 'Pop / Soul'),
	(5, 'Sialnya, Hidup Harus Tetap Berjalan, Kata Mereka Ini Berlebihan, Untungnya, Hidup Harus Tetap Berjalan, Satu Bulan, Apa Mungkin, Kini Mereka Tahu, Ambang Pintu', 'Pop / Indie'),
	(7, '', 'indie');

-- Dumping structure for table eventix.pembayaran
DROP TABLE IF EXISTS `pembayaran`;
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table eventix.pembayaran: ~1 rows (approximately)
INSERT INTO `pembayaran` (`id_pembayaran`, `id_pesanan`, `tanggal`, `metode`, `jumlah`, `bukti_bayar`, `status`, `created_at`) VALUES
	(1, 2, '2026-11-22 00:48:41', 'Transfer Bank BCA', 125000.00, 'pay_2_20261122_004841_e40b8af7.jpg', 'lunas', '2026-11-21 17:48:41');

-- Dumping structure for table eventix.pengguna
DROP TABLE IF EXISTS `pengguna`;
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
DROP TABLE IF EXISTS `pesanan`;
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table eventix.pesanan: ~2 rows (approximately)
INSERT INTO `pesanan` (`id_pesanan`, `id_pengguna`, `tanggal`, `harga`, `status`, `created_at`) VALUES
	(1, 1, '2026-11-22 00:48:09', 125000.00, 'ditolak', '2026-11-21 17:48:09'),
	(2, 1, '2026-11-22 00:48:28', 125000.00, 'lunas', '2026-11-21 17:48:28');

-- Dumping structure for table eventix.tiket
DROP TABLE IF EXISTS `tiket`;
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
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table eventix.tiket: ~16 rows (approximately)
INSERT INTO `tiket` (`id_tiket`, `id_event`, `kategori`, `deskripsi`, `kuota_tersedia`, `harga`) VALUES
	(1, 1, 'VIP Standing (Front of Stage)', 'Area berdiri paling depan panggung, akses jalur masuk prioritas, merchandise lanyard eksklusif.', 500, 1650000.00),
	(2, 1, 'Festival (Standing)', 'Area festival berdiri di belakang VIP dengan pandangan langsung ke panggung utama.', 1500, 850000.00),
	(3, 1, 'CAT 1 (Numbered Seating)', 'Area tribun bernomor duduk dengan pandangan tengah yang nyaman dan akses toilet khusus.', 1000, 1150000.00),
	(4, 2, 'Diamond VIP (Numbered Seating)', 'Kursi bernomor di depan panggung tribun bawah, termasuk official concert pack & lounge akses.', 400, 2250000.00),
	(5, 2, 'Festival A (Standing Field)', 'Area berdiri di lapangan tengah JIS tepat di depan FOH sound tower.', 2000, 1100000.00),
	(6, 2, 'Tribune Barat (Free Seating)', 'Tribun lantai 2 dengan pemandangan panggung menyeluruh dan akustik stadion maksimal.', 1500, 750000.00),
	(7, 3, 'VIP Soundcheck Experience', 'Akses menonton sesi soundcheck sore hari, poster bertandatangan, dan area berdiri baris terdepan.', 300, 1200000.00),
	(8, 3, 'Festival (Standing)', 'Area lapangan berdiri Tennis Indoor, sensasi energi penonton paling intens.', 1200, 650000.00),
	(9, 3, 'Tribune (Numbered Seating)', 'Kursi tribun bernomor dengan sudut pandang visual lighting penuh panggung.', 800, 450000.00),
	(10, 4, 'Platinum (Reserved Seating)', 'Baris depan aula utama berkarpet, welcome drink, goodie bag eksklusif, seat bernomor.', 250, 1800000.00),
	(11, 4, 'Gold (Reserved Seating)', 'Baris tengah aula dengan tata suara surround premium.', 500, 1250000.00),
	(12, 4, 'Silver (Regular Seating)', 'Baris sayap dan belakang aula dengan view layar LED raksasa yang jernih.', 800, 750000.00),
	(13, 5, 'VVIP Intimate (Meet & Greet)', 'Akses foto grup 1:5 bersama Bernadya, merchandise totebag & CD fisik, kursi baris terdepan.', 200, 950000.00),
	(14, 5, 'Festival (Standing)', 'Area festival indoor lantai utama tepat di hadapan panggung.', 1000, 500000.00),
	(15, 5, 'Tribune Regular (Free Seating)', 'Area tribun atas bertingkat dengan kenyamanan tempat duduk.', 600, 350000.00),
	(16, 1, 'TIKET PERUNGGU', '', 2, 125000.00);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
