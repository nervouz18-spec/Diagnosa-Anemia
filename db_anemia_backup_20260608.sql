/*M!999999\- enable the sandbox mode */ 

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
DROP TABLE IF EXISTS `aturan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `aturan` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `gejala_kode` varchar(10) NOT NULL,
  `penyakit_kode` varchar(10) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_rule` (`gejala_kode`,`penyakit_kode`),
  KEY `penyakit_kode` (`penyakit_kode`),
  CONSTRAINT `aturan_ibfk_1` FOREIGN KEY (`gejala_kode`) REFERENCES `gejala` (`kode`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `aturan_ibfk_2` FOREIGN KEY (`penyakit_kode`) REFERENCES `penyakit` (`kode`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=84 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `aturan` WRITE;
/*!40000 ALTER TABLE `aturan` DISABLE KEYS */;
INSERT INTO `aturan` VALUES
(1,'G01','P01'),
(8,'G01','P02'),
(15,'G01','P03'),
(22,'G01','P04'),
(29,'G01','P05'),
(37,'G01','P06'),
(47,'G01','P07'),
(53,'G01','P08'),
(60,'G01','P09'),
(67,'G01','P10'),
(76,'G01','P11'),
(2,'G02','P01'),
(9,'G02','P02'),
(16,'G02','P03'),
(23,'G02','P04'),
(30,'G02','P05'),
(38,'G02','P06'),
(48,'G02','P07'),
(54,'G02','P08'),
(61,'G02','P09'),
(68,'G02','P10'),
(77,'G02','P11'),
(3,'G03','P01'),
(10,'G03','P02'),
(17,'G03','P03'),
(24,'G03','P04'),
(31,'G03','P05'),
(39,'G03','P06'),
(49,'G03','P07'),
(55,'G03','P08'),
(62,'G03','P09'),
(69,'G03','P10'),
(78,'G03','P11'),
(4,'G04','P01'),
(11,'G04','P02'),
(18,'G04','P03'),
(25,'G04','P04'),
(32,'G04','P05'),
(40,'G04','P06'),
(50,'G04','P07'),
(56,'G04','P08'),
(63,'G04','P09'),
(70,'G04','P10'),
(79,'G04','P11'),
(5,'G05','P01'),
(12,'G05','P02'),
(19,'G05','P03'),
(26,'G05','P04'),
(33,'G05','P05'),
(41,'G05','P06'),
(51,'G05','P07'),
(57,'G05','P08'),
(64,'G05','P09'),
(71,'G05','P10'),
(80,'G05','P11'),
(34,'G06','P05'),
(42,'G06','P06'),
(35,'G07','P05'),
(43,'G07','P06'),
(36,'G08','P05'),
(44,'G08','P06'),
(65,'G08','P09'),
(81,'G08','P11'),
(27,'G09','P04'),
(28,'G10','P04'),
(58,'G11','P08'),
(13,'G12','P02'),
(14,'G13','P02'),
(20,'G13','P03'),
(6,'G14','P01'),
(7,'G15','P01'),
(82,'G16','P11'),
(59,'G18','P08'),
(66,'G19','P09'),
(45,'G20','P06'),
(72,'G21','P10'),
(73,'G22','P10'),
(74,'G23','P10'),
(52,'G24','P07'),
(46,'G25','P06'),
(75,'G26','P10'),
(21,'G27','P03');
/*!40000 ALTER TABLE `aturan` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `gejala`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `gejala` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kode` varchar(10) NOT NULL,
  `nama` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kode` (`kode`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `gejala` WRITE;
/*!40000 ALTER TABLE `gejala` DISABLE KEYS */;
INSERT INTO `gejala` VALUES
(1,'G01','Kelelahan ekstrem'),
(2,'G02','Kulit dan bibir pucat'),
(3,'G03','Pusing dan sakit kepala'),
(4,'G04','Sesak napas saat aktivitas'),
(5,'G05','Detak jantung cepat (takikardia)'),
(6,'G06','Penyakit kuning (jaundice)'),
(7,'G07','Urine berwarna gelap'),
(8,'G08','Pembesaran limpa/hati'),
(9,'G09','Memar spontan dan perdarahan'),
(10,'G10','Infeksi berulang'),
(11,'G11','Nyeri tulang/sendi'),
(12,'G12','Gangguan saraf (mati rasa, kesulitan berjalan)'),
(13,'G13','Glossitis (lidah meradang)'),
(14,'G14','Kuku sendok (koilonikia)'),
(15,'G15','Pica (ngidam tanah/es)'),
(16,'G16','Deformitas tulang wajah'),
(17,'G17','Anomali fisik bawaan (jempol abnormal)'),
(18,'G18','Krisis nyeri vaso-oklusif'),
(19,'G19','Kulit kecoklatan (penumpukan besi)'),
(20,'G20','Demam tanpa sebab jelas'),
(21,'G21','Nyeri dada dan kebingungan'),
(22,'G22','Tinja hitam (melena)'),
(23,'G23','Palpitasi ekstrem'),
(24,'G24','Mempunyai Penyakit Kronis'),
(25,'G25','Tangan atau Kaki terasa Dingin'),
(26,'G26','Pendarahan'),
(27,'G27','Iritabilitas dan nafsu makan menurun');
/*!40000 ALTER TABLE `gejala` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `penyakit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `penyakit` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kode` varchar(10) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kode` (`kode`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `penyakit` WRITE;
/*!40000 ALTER TABLE `penyakit` DISABLE KEYS */;
INSERT INTO `penyakit` VALUES
(1,'P01','Anemia Defisiensi Besi','Anemia akibat kekurangan zat besi yang dibutuhkan tubuh untuk memproduksi hemoglobin. Solusi: konsumsi makanan kaya zat besi & suplemen zat besi, konsultasi ke dokter bila gejala berat.'),
(2,'P02','Anemia Defisiensi VitaminB12 (Pernisiosa)','Anemia akibat kekurangan vitamin B12 atau gangguan penyerapannya. Solusi: pemberian suplemen vitamin B12 dan pengaturan pola makan.'),
(3,'P03','Anemia Defisiensi Asam Folat','Anemia akibat kekurangan asam folat (vitamin B9). Solusi: konsumsi makanan/suplemen kaya asam folat & konsultasi ke dokter.'),
(4,'P04','Anemia Aplastik','Sumsum tulang gagal memproduksi sel darah. Solusi: konsultasi ke dokter, dapat memerlukan transfusi, imunosupresif, atau transplantasi sumsum tulang.'),
(5,'P05','Anemia Hemolitik','Sel darah merah hancur lebih cepat dari produksinya. Solusi: pengobatan penyebab, transfusi, suplemen, atau terapi sesuai kondisi.'),
(6,'P06','Anemia Hemolitik Autoinum','Hemolitik karena sistem imun menyerang sel darah merah sendiri. Solusi: konsultasi hematologi, kortikosteroid/imunosupresif sesuai penyebab autoimun.'),
(7,'P07','Anemia Penyakit Kronis','Anemia akibat penyakit kronis (peradangan, infeksi, kanker, gagal ginjal). Solusi: pengelolaan penyakit utama + suplemen sesuai anjuran dokter.'),
(8,'P08','Anemia Sel Sabit','Kelainan genetik bentuk sel darah merah menyerupai sabit. Solusi: obat pereda nyeri, cairan, transfusi, dan konsultasi rutin.'),
(9,'P09','Anemia Sideroblastik','Sumsum tulang memproduksi sideroblast cincin, menumpuk besi. Solusi: identifikasi penyebab, vitamin B6, terapi kelasi besi bila perlu.'),
(10,'P10','Anemia Normositik','Sel darah merah berukuran normal tetapi jumlahnya berkurang. Solusi: cari penyebab dasar (perdarahan akut, penyakit kronis, gangguan ginjal), tangani penyebabnya.'),
(11,'P11','Thalassemia','Kelainan genetik produksi hemoglobin. Solusi: transfusi darah rutin, terapi kelasi besi, pemantauan jangka panjang.');
/*!40000 ALTER TABLE `penyakit` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `riwayat`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `riwayat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tanggal` datetime NOT NULL DEFAULT current_timestamp(),
  `gejala_dipilih` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`gejala_dipilih`)),
  `hasil_dianosa` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`hasil_dianosa`)),
  `nama_pasien` varchar(100) DEFAULT NULL,
  `umur` int(11) DEFAULT NULL,
  `jenis_kelamin` varchar(10) DEFAULT NULL,
  `session_token` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `riwayat` WRITE;
/*!40000 ALTER TABLE `riwayat` DISABLE KEYS */;
INSERT INTO `riwayat` VALUES
(1,'2026-06-08 00:30:25','[\"G01\",\"G02\",\"G03\",\"G04\",\"G05\",\"G14\",\"G15\"]','[{\"kode\":\"P01\",\"nama\":\"Anemia Defisiensi Besi\",\"persen\":100,\"match\":7,\"total\":7},{\"kode\":\"P07\",\"nama\":\"Anemia Penyakit Kronis\",\"persen\":83,\"match\":5,\"total\":6}]','TestP01',NULL,NULL,'9d18afedc99a55b2ec982b93068d68f6'),
(2,'2026-06-08 00:30:25','[\"G01\",\"G02\",\"G03\",\"G04\",\"G05\"]','[{\"kode\":\"P07\",\"nama\":\"Anemia Penyakit Kronis\",\"persen\":83,\"match\":5,\"total\":6}]','TestUmum',NULL,NULL,'5829835e951f2c83cb8ba3c09d926655'),
(3,'2026-06-08 00:30:25','[\"G01\",\"G02\",\"G03\",\"G04\",\"G05\",\"G06\",\"G07\",\"G08\",\"G20\",\"G25\"]','[{\"kode\":\"P05\",\"nama\":\"Anemia Hemolitik\",\"persen\":100,\"match\":8,\"total\":8},{\"kode\":\"P06\",\"nama\":\"Anemia Hemolitik Autoinum\",\"persen\":100,\"match\":10,\"total\":10},{\"kode\":\"P07\",\"nama\":\"Anemia Penyakit Kronis\",\"persen\":83,\"match\":5,\"total\":6},{\"kode\":\"P09\",\"nama\":\"Anemia Sideroblastik\",\"persen\":86,\"match\":6,\"total\":7},{\"kode\":\"P11\",\"nama\":\"Thalassemia\",\"persen\":86,\"match\":6,\"total\":7}]','TestP06',NULL,NULL,'5f2955983919486bcd2ca723de916f9b');
/*!40000 ALTER TABLE `riwayat` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('Admin') NOT NULL DEFAULT 'Admin',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES
(1,'admin','$2y$10$exeU5cBdOc/dJCgWl3tKBO/oTewczuUyQlUEAFLeZP/Uz0n9Xxjzq','Admin');
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

