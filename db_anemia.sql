CREATE DATABASE IF NOT EXISTS db_anemia DEFAULT CHARSET=utf8mb4;
USE db_anemia;

-- =====================
--  TABEL USERS (Admin)
-- =====================
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) UNIQUE,
  password VARCHAR(255),
  nama_lengkap VARCHAR(100) DEFAULT NULL,
  role VARCHAR(20) DEFAULT 'admin',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

INSERT IGNORE INTO users (username, password, nama_lengkap, role) VALUES
('admin', '$2y$10$7QsXtrWvqdNBzTe0QWU6AOCBffWQtviy4nje7LANHdKFVMC7Musyq', 'Administrator', 'admin'); -- admin123

-- =====================
--  TABEL GEJALA
-- =====================
CREATE TABLE IF NOT EXISTS gejala (
  id_gejala INT AUTO_INCREMENT PRIMARY KEY,
  kode_gejala VARCHAR(5) UNIQUE,
  nama_gejala VARCHAR(100)
);

INSERT IGNORE INTO gejala (kode_gejala, nama_gejala) VALUES
('G01', 'Lemas/mudah lelah'),
('G02', 'Pucat'),
('G03', 'Sakit kepala/pusing'),
('G04', 'Sesak napas'),
('G05', 'Denyut jantung cepat'),
('G06', 'Tangan/kaki dingin'),
('G07', 'Kuku rapuh'),
('G08', 'Kesemutan di tangan/kaki'),
('G09', 'Sulit berkonsentrasi'),
('G10', 'Lidah bengkak/meradang'),
('G11', 'Nafsu makan menurun'),
('G12', 'Mudah marah'),
('G13', 'Diare'),
('G14', 'Lidah sakit'),
('G15', 'Rentan infeksi'),
('G16', 'Mudah/sering berdarah'),
('G17', 'Jantung berdebar'),
('G18', 'Kulit/mata kuning (ikterus)'),
('G19', 'Urin gelap'),
('G20', 'Pembengkakan perut'),
('G21', 'Demam'),
('G22', 'Nyeri hebat sendi/tulang'),
('G23', 'Infeksi berulang'),
('G24', 'Pertumbuhan terhambat'),
('G25', 'Tulang wajah menonjol'),
('G26', 'Warna kulit kekuningan (ikterus)'),
('G27', 'Perut membesar (pembesaran limpa)');

-- =====================
--  TABEL PENYAKIT
-- =====================
CREATE TABLE IF NOT EXISTS penyakit (
  id_penyakit INT AUTO_INCREMENT PRIMARY KEY,
  kode_penyakit VARCHAR(5) UNIQUE,
  nama_penyakit VARCHAR(100),
  solusi TEXT
);

INSERT IGNORE INTO penyakit (kode_penyakit, nama_penyakit, solusi) VALUES
('P01', 'Anemia Defisiensi Besi',                          'Konsumsi makanan kaya zat besi, suplemen zat besi, dan konsultasi ke dokter bila gejala berat.'),
('P02', 'Anemia Defisiensi VitaminB12 (Pernisiosa)',       'Pemberian suplemen vitamin B12 dan pengaturan pola makan.'),
('P03', 'Anemia Defisiensi Asam Folat',                    'Konsumsi makanan atau suplemen yang kaya asam folat, konsultasi ke dokter.'),
('P04', 'Anemia Aplastik',                                 'Segera konsultasi ke dokter, bisa membutuhkan transfusi darah, obat imunosupresif, atau transplantasi sumsum tulang.'),
('P05', 'Anemia Hemolitik',                                'Penanganan medis, pengobatan penyebab, transfusi, suplemen, atau terapi lain sesuai kondisi.'),
('P06', 'Anemia Hemolitik Autoinum',                       'Konsultasi spesialis hematologi; pengobatan imunosupresif (kortikosteroid) dan terapi sesuai penyebab autoimun.'),
('P07', 'Anemia Penyakit Kronis',                          'Pengelolaan penyakit utama dan suplemen sesuai anjuran dokter.'),
('P08', 'Anemia Sel Sabit',                                'Obat pereda nyeri, cairan, transfusi darah, dan konsultasi rutin ke dokter.'),
('P09', 'Anemia Sideroblastik',                            'Identifikasi penyebab (genetik / sekunder), suplemen vitamin B6, terapi kelasi besi bila perlu, dan konsultasi hematologi.'),
('P10', 'Anemia Normositik',                               'Cari penyebab dasar (perdarahan akut, penyakit kronis, gangguan ginjal), tangani penyakit utama, dan kontrol rutin.'),
('P11', 'Thalassemia',                                     'Transfusi darah rutin, terapi kelasi besi, dan pemantauan medis jangka panjang.');

-- =====================
--  TABEL ATURAN (Knowledge Base)
-- =====================
CREATE TABLE IF NOT EXISTS aturan (
  id_aturan INT AUTO_INCREMENT PRIMARY KEY,
  kode_penyakit VARCHAR(5),
  kode_gejala VARCHAR(5),
  FOREIGN KEY (kode_penyakit) REFERENCES penyakit(kode_penyakit),
  FOREIGN KEY (kode_gejala) REFERENCES gejala(kode_gejala)
);

INSERT IGNORE INTO aturan (kode_penyakit, kode_gejala) VALUES
-- P01 Anemia Defisiensi Besi
('P01','G01'),('P01','G02'),('P01','G03'),('P01','G04'),('P01','G05'),('P01','G06'),('P01','G07'),
-- P02 Anemia Defisiensi VitaminB12 (Pernisiosa)
('P02','G01'),('P02','G02'),('P02','G08'),('P02','G09'),('P02','G10'),('P02','G11'),
-- P03 Anemia Defisiensi Asam Folat
('P03','G01'),('P03','G12'),('P03','G04'),('P03','G02'),('P03','G13'),('P03','G14'),
-- P04 Anemia Aplastik
('P04','G01'),('P04','G15'),('P04','G16'),('P04','G02'),('P04','G17'),
-- P05 Anemia Hemolitik
('P05','G01'),('P05','G18'),('P05','G19'),('P05','G20'),('P05','G21'),('P05','G03'),
-- P07 Anemia Penyakit Kronis  (sebelumnya rule P06 lama)
('P07','G01'),('P07','G04'),('P07','G02'),
-- P08 Anemia Sel Sabit  (sebelumnya rule P07 lama)
('P08','G22'),('P08','G23'),('P08','G24'),('P08','G18'),
-- P11 Thalassemia  (sebelumnya rule P08 lama)
('P11','G02'),('P11','G01'),('P11','G24'),('P11','G27'),('P11','G25'),('P11','G26'),('P11','G19');

-- =====================
--  TABEL RIWAYAT DIAGNOSA
-- =====================
CREATE TABLE IF NOT EXISTS riwayat (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NULL,
  tanggal DATETIME,
  gejala TEXT,
  hasil TEXT,
  nama_pasien VARCHAR(100) DEFAULT NULL,
  umur INT DEFAULT NULL,
  jenis_kelamin VARCHAR(10) DEFAULT NULL,
  session_token VARCHAR(64) DEFAULT NULL,
  FOREIGN KEY (user_id) REFERENCES users(id)
);
