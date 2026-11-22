-- MySQL dump 10.13  Distrib 8.4.3, for Win64 (x86_64)
--
-- Host: localhost    Database: eventix
-- ------------------------------------------------------
-- Server version	8.4.3

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `detail_pesanan`
--

DROP TABLE IF EXISTS `detail_pesanan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `detail_pesanan` (
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `detail_pesanan`
--

LOCK TABLES `detail_pesanan` WRITE;
/*!40000 ALTER TABLE `detail_pesanan` DISABLE KEYS */;
/*!40000 ALTER TABLE `detail_pesanan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `event`
--

DROP TABLE IF EXISTS `event`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `event` (
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
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `event`
--

LOCK TABLES `event` WRITE;
/*!40000 ALTER TABLE `event` DISABLE KEYS */;
INSERT INTO `event` VALUES (10,'Jakarta International Stadium (JIS), Jakarta Utara','SEVENTEEN [RIGHT HERE] World Tour in Jakarta','2026-12-12','18:30:00','SEVENTEEN','Konser tur dunia SEVENTEEN kembali menyapa CARAT Indonesia dengan panggung megah berskala stadion. Open gate dimulai pukul 15:00 WIB, wajib membawa e-ticket dan kartu identitas resmi saat penukaran wristband.','poster_1795378146_c6aac4a2.jpg','2026-11-22 20:01:49'),(11,'ICE BSD City Hall 5-6, Tangerang','IU H.E.R. World Tour Concert in Jakarta','2026-12-19','19:00:00','IU','Pertunjukan solo spesial dari IU yang membawakan deretan lagu hits dan album terbaru bersama live band penuh. Seluruh kategori bersifat numbered seating, penukaran tiket dibuka mulai pukul 11:00 WIB.','poster_1795380210_07aeb798.jpg','2026-11-22 20:01:49'),(12,'Stadion Madya Gelora Bung Karno, Senayan, Jakarta','DAY6 3rd World Tour <FOREVER YOUNG> Jakarta','2027-01-09','19:00:00','DAY6','Band rock asal Korea Selatan DAY6 hadir membawakan energi live performance penuh untuk My Day Indonesia. Dilarang membawa kamera profesional, tripod, dan makanan dari luar area stadion.','poster_1795380284_8e5a64b0.jpg','2026-11-22 20:01:49'),(13,'Stadion Utama Gelora Bung Karno (SUGBK), Jakarta','BLACKPINK World Tour [BORN PINK] Jakarta','2027-01-23','19:00:00','BLACKPINK (Jisoo, Jennie, Rosé, Lisa)','Konser stadion berskala internasional dari BLACKPINK dengan produksi panggung, koreografi, dan tata kembang api spektakuler. Catatan: seluruh kuota diisi 0 untuk contoh tampilan status Habis di katalog.','poster_1795380111_15d0399e.jpg','2026-11-22 20:01:49'),(14,'Istora Senayan, Jakarta Pusat','wave to earth – 0.03 World Tour in Jakarta','2027-02-06','20:00:00','wave to earth','Trio indie rock dan lo-fi asal Seoul kembali membawa suasana hangat dan intim lewat tur 0.03 World Tour. Pintu masuk dibuka pukul 17:30 WIB dengan area merchandise resmi di selasar Istora.','poster_1795379876_ceb9d31b.png','2026-11-22 20:01:49'),(15,'Beach City International Stadium, Ancol, Jakarta','aespa LIVE TOUR – SYNK : PARALLEL LINE Jakarta','2027-02-20','19:00:00','aespa (Karina, Giselle, Winter, Ningning)','Tur dunia aespa yang memadukan konsep futuristik, visual LED imersif, serta penampilan solo dari keempat member. Tiket sudah termasuk akses masuk kawasan Ancol pada hari acara.','poster_1795380692_e8aa0b70.jpg','2026-11-22 20:01:49'),(16,'Stadion Utama Gelora Bung Karno (SUGBK), Jakarta','NCT DREAM – THE DREAM SHOW 3 in Jakarta','2027-03-06','19:00:00','NCT DREAM','Konser stadion akbar NCT DREAM bertajuk DREAM( )SCAPE yang menampilkan setlist energik, panggung bergerak, dan interaksi langsung bersama NCTzen Indonesia.','poster_1795380403_d1a97785.jpg','2026-11-22 20:01:49'),(17,'Beach City International Stadium, Ancol, Jakarta','THE DREAM REBORN World Tour in Jakarta','2027-03-20','19:30:00','DPR IAN, DPR CREAM, DPR ARTIC','Kolektif musik independen Dream Perfect Regime menghadirkan malam penuh eksplorasi visual sinematik, R&B, dan rock alternatif bersama DPR IAN, DPR CREAM, dan DPR ARTIC.','poster_1795380522_ea86fed4.jpg','2026-11-22 20:01:49'),(18,'Jakarta International Stadium (JIS), Jakarta Utara','TWICE 5TH WORLD TOUR \'READY TO BE\' in Jakarta','2027-04-10','19:00:00','TWICE','Sembilan member TWICE menyapa ONCE Indonesia dalam konser stadion megah lengkap dengan live band dan panggung solo tiap member. Shuttle bus resmi tersedia dari area parkir JIExpo.','poster_1795380743_f8bb4d16.jpg','2026-11-22 20:01:49'),(19,'Indonesia Arena Gelora Bung Karno, Jakarta Pusat','Stray Kids World Tour <dominATE> Jakarta','2027-04-24','19:00:00','Stray Kids','Stray Kids membawa energi panggung eksplosif dengan aransemen live band keras dan koreografi intens untuk STAY Indonesia di arena indoor berkapasitas besar.','poster_1795380781_2d51c890.jpg','2026-11-22 20:01:49'),(20,'ICE BSD City Hall 5-6, Tangerang','TAEYEON CONCERT – The ODD Of LOVE in Jakarta','2027-05-08','18:30:00','TAEYEON','Pertunjukan vokal memukau dari solois legendaris TAEYEON bersama full live band, menampilkan lagu-lagu balada dan pop ikonik dalam tata akustik hall terbaik.','poster_1795380816_82de562f.jpg','2026-11-22 20:01:49'),(21,'ICE BSD City Hall 5, Tangerang','BAEKHYUN ASIA TOUR [Lonsdaleite] in Jakarta','2027-05-22','19:00:00','BAEKHYUN','Tur solo Asia perdana BAEKHYUN yang menyuguhkan perpaduan vokal R&B halus, koreografi tajam, dan sesi interaksi hangat bersama EXO-L Indonesia.','poster_1795380861_abf531f8.jpg','2026-11-22 20:01:49');
/*!40000 ALTER TABLE `event` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `konser`
--

DROP TABLE IF EXISTS `konser`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `konser` (
  `id_event` int NOT NULL,
  `daftar_lagu` text COLLATE utf8mb4_unicode_ci,
  `genre_musik` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id_event`),
  CONSTRAINT `fk_konser_event` FOREIGN KEY (`id_event`) REFERENCES `event` (`id_event`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `konser`
--

LOCK TABLES `konser` WRITE;
/*!40000 ALTER TABLE `konser` DISABLE KEYS */;
INSERT INTO `konser` VALUES (10,'','K-Pop / Performance'),(11,'','K-Pop / Ballad'),(12,'','K-Rock / Pop Rock'),(13,'','K-Pop / Hip-Hop'),(14,'','K-Indie / Lo-Fi'),(15,'','K-Pop / Hyperpop'),(16,'','K-Pop / Dance Pop'),(17,'','K-R&B / Alternative'),(18,'','K-Pop / Pop Dance'),(19,'','K-Pop / Rap Rock'),(20,'','K-Pop / Vocal'),(21,'','K-Pop / R&B');
/*!40000 ALTER TABLE `konser` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pembayaran`
--

DROP TABLE IF EXISTS `pembayaran`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pembayaran` (
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pembayaran`
--

LOCK TABLES `pembayaran` WRITE;
/*!40000 ALTER TABLE `pembayaran` DISABLE KEYS */;
INSERT INTO `pembayaran` VALUES (1,2,'2026-11-22 00:48:41','Transfer Bank BCA',125000.00,'pay_2_20261122_004841_e40b8af7.jpg','lunas','2026-11-21 17:48:41'),(2,3,'2026-11-22 23:59:45','Verifikasi Langsung Admin',1650000.00,NULL,'lunas','2026-11-22 16:59:45');
/*!40000 ALTER TABLE `pembayaran` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pengguna`
--

DROP TABLE IF EXISTS `pengguna`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pengguna` (
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pengguna`
--

LOCK TABLES `pengguna` WRITE;
/*!40000 ALTER TABLE `pengguna` DISABLE KEYS */;
INSERT INTO `pengguna` VALUES (1,'Administrator Eventix','admin@eventix.id','Gedung Cyber 2 Lt. 15, Jl. H.R. Rasuna Said, Jakarta Selatan','081298765432','$2y$10$Kkyb1X979om1lbGn0MFHnuyZb2BWWYScOHbFfOZAaK0cqnUbGVY2O','admin','2026-11-21 17:32:28'),(2,'Budi Santoso','budi@gmail.com','Jl. Tebet Timur Dalam Raya No. 42, Jakarta Selatan','081311223344','$2y$10$F2WYCiy5xydoLck6BlTUAOb2HQEGsu7hZq5IMWHEbxhnG0Bb5uUC2','user','2026-11-21 17:32:28');
/*!40000 ALTER TABLE `pengguna` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pesanan`
--

DROP TABLE IF EXISTS `pesanan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pesanan` (
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pesanan`
--

LOCK TABLES `pesanan` WRITE;
/*!40000 ALTER TABLE `pesanan` DISABLE KEYS */;
INSERT INTO `pesanan` VALUES (1,1,'2026-11-22 00:48:09',125000.00,'ditolak','2026-11-21 17:48:09'),(2,1,'2026-11-22 00:48:28',125000.00,'lunas','2026-11-21 17:48:28'),(3,1,'2026-11-22 23:57:35',1650000.00,'lunas','2026-11-22 16:57:35'),(4,1,'2026-11-23 01:58:29',3800000.00,'menunggu','2026-11-22 18:58:29');
/*!40000 ALTER TABLE `pesanan` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tiket`
--

DROP TABLE IF EXISTS `tiket`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tiket` (
  `id_tiket` int NOT NULL AUTO_INCREMENT,
  `id_event` int NOT NULL,
  `kategori` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `deskripsi` text COLLATE utf8mb4_unicode_ci,
  `kuota_tersedia` int NOT NULL DEFAULT '0',
  `harga` decimal(12,2) NOT NULL,
  PRIMARY KEY (`id_tiket`),
  KEY `fk_tiket_event` (`id_event`),
  CONSTRAINT `fk_tiket_event` FOREIGN KEY (`id_event`) REFERENCES `event` (`id_event`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=68 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tiket`
--

LOCK TABLES `tiket` WRITE;
/*!40000 ALTER TABLE `tiket` DISABLE KEYS */;
INSERT INTO `tiket` VALUES (20,10,'PINK Soundcheck','Standing area paling dekat panggung, akses soundcheck session, lanyard & photocard eksklusif.',200,3800000.00),(21,10,'CAT 1 (Festival)','Area festival berdiri dekat panggung utama dengan queue number.',500,2900000.00),(22,10,'CAT 2 (Lower Tribune)','Kursi bernomor di tribun bawah dengan pandangan langsung ke panggung.',400,2200000.00),(23,10,'CAT 3 (Upper Tribune)','Kursi bernomor di tribun atas dengan layar LED pendamping.',600,1600000.00),(24,11,'VIP Seated','Barisan kursi depan panggung, souvenir seat cushion & photocard spesial.',250,2900000.00),(25,11,'CAT 1','Kursi bernomor area tengah hall dengan visibilitas optimal.',400,2400000.00),(26,11,'CAT 2','Kursi bernomor area sayap kanan dan kiri hall.',450,1800000.00),(27,11,'CAT 3','Kursi bernomor area belakang dengan akses penuh ke layar utama.',500,1100000.00),(28,12,'PINK Soundcheck','Akses soundcheck, area standing terdepan, dan exclusive merchandise lanyard.',200,3400000.00),(29,12,'BLUE Festival','Area festival berdiri di tengah lapangan dekat extended stage.',450,2500000.00),(30,12,'YELLOW Tribune','Kursi tribun bernomor menghadap langsung ke panggung utama.',400,1500000.00),(31,12,'GREEN Tribune','Kursi tribun sisi kanan dan kiri dengan harga paling terjangkau.',500,850000.00),(32,13,'VIP Soundcheck','Kursi terdepan dekat panggung utama, akses soundcheck, jalur masuk khusus, dan laminate eksklusif.',0,3800000.00),(33,13,'Platinum Seated','Kursi lapangan bernomor tepat di belakang area VIP dengan jarak pandang dekat.',0,3400000.00),(34,13,'CAT 1','Area tribun bawah sisi barat dan timur stadion dengan kursi bernomor.',0,2900000.00),(35,13,'CAT 4 (Upper)','Area tribun atas stadion dengan pandangan panorama panggung dan lautan lightstick.',0,1350000.00),(36,14,'VIP Meet & Greet','Early entry ke area festival, sesi foto grup 1:6, poster bertanda tangan, dan VIP lanyard.',150,2400000.00),(37,14,'CAT 1 (Standing)','Area berdiri lantai utama tepat di depan panggung dengan audio akustik jernih.',600,1250000.00),(38,14,'CAT 2 (Seated)','Kursi tribun tengah menghadap langsung ke panggung.',400,950000.00),(39,14,'CAT 3 (Seated)','Kursi tribun bagian atas dengan suasana konser yang nyaman.',450,650000.00),(40,15,'VIP Soundcheck','Akses soundcheck eksklusif, area standing terdekat panggung, dan VIP gift set.',250,3000000.00),(41,15,'CAT 1 (Standing)','Area berdiri di sekitar extended stage dengan nomor antrean masuk.',500,2750000.00),(42,15,'CAT 2 (Seated)','Kursi tribun bernomor di lantai 2 dengan sudut pandang lurus ke panggung.',400,2550000.00),(43,15,'CAT 4 (Upper Seated)','Kursi tribun lantai atas dengan visibilitas layar utama penuh.',550,1300000.00),(44,16,'DREAM A/B Soundcheck','Area standing paling depan, akses soundcheck, jalur prioritas, dan lanyard eksklusif.',300,3350000.00),(45,16,'DREAM C (Standing)','Area festival lapangan mengelilingi panggung tengah stadion.',600,2950000.00),(46,16,'CAT 2 (Lower Seated)','Kursi tribun bawah bernomor dengan kenyamanan penuh dan pandangan jelas.',500,2550000.00),(47,16,'CAT 6 (Upper Seated)','Kursi tribun atas bernomor dengan harga terjangkau.',700,1050000.00),(48,17,'VIP Meet & Greet','Early entry, sesi foto grup bersama DPR, akses Q&A singkat, dan exclusive merch pack.',150,3800000.00),(49,17,'Early Entry Festival','Area berdiri lantai utama dengan akses masuk lebih awal sebelum penonton reguler.',350,2150000.00),(50,17,'General Standing','Area berdiri reguler di lantai utama menghadap panggung.',500,1550000.00),(51,17,'Seated Tribune','Kursi tribun bernomor dengan tata suara jernih.',450,950000.00),(52,18,'PINK Soundcheck','Kursi lapangan terdepan, akses soundcheck session, dan souvenir lanyard eksklusif.',250,3600000.00),(53,18,'BLUE Seated','Kursi bernomor di area lapangan tepat di samping panggung utama.',400,3300000.00),(54,18,'GREEN Tribune','Kursi tribun lantai 1 menghadap panggung utama secara langsung.',500,2200000.00),(55,18,'GRAY Tribune','Kursi tribun tingkat atas dengan visibilitas keseluruhan stadion.',650,1200000.00),(56,19,'VIP Soundcheck','Area standing terdekat panggung, sesi soundcheck, lanyard VIP, dan jalur antrean khusus.',200,3600000.00),(57,19,'CAT 1 (Standing)','Area festival berdiri tepat di sekitar panggung utama dan catwalk.',450,2900000.00),(58,19,'CAT 3 (Seated)','Kursi tribun bawah bernomor dengan sudut pandang optimal ke seluruh arena.',400,2200000.00),(59,19,'CAT 5 (Upper Seated)','Kursi tribun atas bernomor di dalam arena ber-AC penuh.',550,1200000.00),(60,20,'CAT 1 (Standing)','Area berdiri paling dekat panggung dengan nomor antrean masuk.',300,2900000.00),(61,20,'CAT 2 (Seated)','Kursi bernomor area depan tengah hall untuk menikmati kualitas vokal secara intim.',350,2700000.00),(62,20,'CAT 4 (Seated)','Kursi bernomor area sayap kanan dan kiri hall.',400,1900000.00),(63,20,'CAT 5 (Seated)','Kursi bernomor area belakang hall dengan dukungan layar LED besar.',450,1100000.00),(64,21,'VIP Soundcheck','Akses soundcheck, sesi send-off terbatas, area standing terdepan, dan photocard eksklusif.',200,3100000.00),(65,21,'CAT 1 (Standing)','Area berdiri dekat extended stage dengan nomor antrean.',400,2600000.00),(66,21,'CAT 2 (Seated)','Kursi bernomor area tribun tengah menghadap langsung ke panggung.',350,2200000.00),(67,21,'CAT 3 (Seated)','Kursi bernomor area belakang dengan pandangan jelas ke panggung dan layar utama.',450,1600000.00);
/*!40000 ALTER TABLE `tiket` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-11-23  3:55:02
