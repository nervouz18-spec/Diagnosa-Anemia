DROP DATABASE IF EXISTS db_anemia;
CREATE DATABASE db_anemia DEFAULT CHARSET=utf8mb4;
USE db_anemia;

-- ============================================================
--  TABEL 3.7  USERS (Login)
-- ============================================================
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  role ENUM('Admin') NOT NULL DEFAULT 'Admin'
);

INSERT INTO users (username, password, role) VALUES
('admin', '$2y$10$exeU5cBdOc/dJCgWl3tKBO/oTewczuUyQlUEAFLeZP/Uz0n9Xxjzq', 'Admin'); -- admin123

-- ============================================================
--  TABEL 3.8  PENYAKIT
-- ============================================================
CREATE TABLE penyakit (
  id INT AUTO_INCREMENT PRIMARY KEY,
  kode VARCHAR(10) UNIQUE NOT NULL,
  nama VARCHAR(100) NOT NULL,
  deskripsi TEXT
);

INSERT INTO penyakit (kode, nama, deskripsi) VALUES
('P01', 'Anemia Defisiensi Besi',                    'Anemia akibat kekurangan zat besi yang dibutuhkan tubuh untuk memproduksi hemoglobin. Solusi: konsumsi makanan kaya zat besi & suplemen zat besi, konsultasi ke dokter bila gejala berat.'),
('P02', 'Anemia Defisiensi VitaminB12 (Pernisiosa)', 'Anemia akibat kekurangan vitamin B12 atau gangguan penyerapannya. Solusi: pemberian suplemen vitamin B12 dan pengaturan pola makan.'),
('P03', 'Anemia Defisiensi Asam Folat',              'Anemia akibat kekurangan asam folat (vitamin B9). Solusi: konsumsi makanan/suplemen kaya asam folat & konsultasi ke dokter.'),
('P04', 'Anemia Aplastik',                           'Sumsum tulang gagal memproduksi sel darah. Solusi: konsultasi ke dokter, dapat memerlukan transfusi, imunosupresif, atau transplantasi sumsum tulang.'),
('P05', 'Anemia Hemolitik',                          'Sel darah merah hancur lebih cepat dari produksinya. Solusi: pengobatan penyebab, transfusi, suplemen, atau terapi sesuai kondisi.'),
('P06', 'Anemia Hemolitik Autoinum',                 'Hemolitik karena sistem imun menyerang sel darah merah sendiri. Solusi: konsultasi hematologi, kortikosteroid/imunosupresif sesuai penyebab autoimun.'),
('P07', 'Anemia Penyakit Kronis',                    'Anemia akibat penyakit kronis (peradangan, infeksi, kanker, gagal ginjal). Solusi: pengelolaan penyakit utama + suplemen sesuai anjuran dokter.'),
('P08', 'Anemia Sel Sabit',                          'Kelainan genetik bentuk sel darah merah menyerupai sabit. Solusi: obat pereda nyeri, cairan, transfusi, dan konsultasi rutin.'),
('P09', 'Anemia Sideroblastik',                      'Sumsum tulang memproduksi sideroblast cincin, menumpuk besi. Solusi: identifikasi penyebab, vitamin B6, terapi kelasi besi bila perlu.'),
('P10', 'Anemia Normositik',                         'Sel darah merah berukuran normal tetapi jumlahnya berkurang. Solusi: cari penyebab dasar (perdarahan akut, penyakit kronis, gangguan ginjal), tangani penyebabnya.'),
('P11', 'Thalassemia',                               'Kelainan genetik produksi hemoglobin. Solusi: transfusi darah rutin, terapi kelasi besi, pemantauan jangka panjang.');

-- ============================================================
--  TABEL 3.9  GEJALA
--  Catatan: G01–G05 adalah GEJALA UMUM (dimiliki semua penyakit)
-- ============================================================
CREATE TABLE gejala (
  id INT AUTO_INCREMENT PRIMARY KEY,
  kode VARCHAR(10) UNIQUE NOT NULL,
  nama VARCHAR(100) NOT NULL
);

INSERT INTO gejala (kode, nama) VALUES
('G01', 'Kelelahan ekstrem'),
('G02', 'Kulit dan bibir pucat'),
('G03', 'Pusing dan sakit kepala'),
('G04', 'Sesak napas saat aktivitas'),
('G05', 'Detak jantung cepat (takikardia)'),
('G06', 'Penyakit kuning (jaundice)'),
('G07', 'Urine berwarna gelap'),
('G08', 'Pembesaran limpa/hati'),
('G09', 'Memar spontan dan perdarahan'),
('G10', 'Infeksi berulang'),
('G11', 'Nyeri tulang/sendi'),
('G12', 'Gangguan saraf (mati rasa, kesulitan berjalan)'),
('G13', 'Glossitis (lidah meradang)'),
('G14', 'Kuku sendok (koilonikia)'),
('G15', 'Pica (ngidam tanah/es)'),
('G16', 'Deformitas tulang wajah'),
('G17', 'Anomali fisik bawaan (jempol abnormal)'),
('G18', 'Krisis nyeri vaso-oklusif'),
('G19', 'Kulit kecoklatan (penumpukan besi)'),
('G20', 'Demam tanpa sebab jelas'),
('G21', 'Nyeri dada dan kebingungan'),
('G22', 'Tinja hitam (melena)'),
('G23', 'Palpitasi ekstrem'),
('G24', 'Mempunyai Penyakit Kronis'),
('G25', 'Tangan atau Kaki terasa Dingin'),
('G26', 'Pendarahan'),
('G27', 'Iritabilitas dan nafsu makan menurun');

-- ============================================================
--  TABEL 3.10  ATURAN (Knowledge Base / Forward Chaining)
--  Setiap baris = 1 pasangan gejala-penyakit
-- ============================================================
CREATE TABLE aturan (
  id INT AUTO_INCREMENT PRIMARY KEY,
  gejala_kode VARCHAR(10) NOT NULL,
  penyakit_kode VARCHAR(10) NOT NULL,
  FOREIGN KEY (gejala_kode)   REFERENCES gejala(kode)   ON UPDATE CASCADE ON DELETE CASCADE,
  FOREIGN KEY (penyakit_kode) REFERENCES penyakit(kode) ON UPDATE CASCADE ON DELETE CASCADE,
  UNIQUE KEY uniq_rule (gejala_kode, penyakit_kode)
);

INSERT INTO aturan (penyakit_kode, gejala_kode) VALUES
-- P01 Anemia Defisiensi Besi
('P01','G01'),('P01','G02'),('P01','G03'),('P01','G04'),('P01','G05'),('P01','G14'),('P01','G15'),
-- P02 Anemia Defisiensi VitaminB12 (Pernisiosa)
('P02','G01'),('P02','G02'),('P02','G03'),('P02','G04'),('P02','G05'),('P02','G12'),('P02','G13'),
-- P03 Anemia Defisiensi Asam Folat
('P03','G01'),('P03','G02'),('P03','G03'),('P03','G04'),('P03','G05'),('P03','G13'),('P03','G27'),
-- P04 Anemia Aplastik
('P04','G01'),('P04','G02'),('P04','G03'),('P04','G04'),('P04','G05'),('P04','G09'),('P04','G10'),
-- P05 Anemia Hemolitik
('P05','G01'),('P05','G02'),('P05','G03'),('P05','G04'),('P05','G05'),('P05','G06'),('P05','G07'),('P05','G08'),
-- P06 Anemia Hemolitik Autoinum
('P06','G01'),('P06','G02'),('P06','G03'),('P06','G04'),('P06','G05'),('P06','G06'),('P06','G07'),('P06','G08'),('P06','G20'),('P06','G25'),
-- P07 Anemia Penyakit Kronis
('P07','G01'),('P07','G02'),('P07','G03'),('P07','G04'),('P07','G05'),('P07','G24'),
-- P08 Anemia Sel Sabit
('P08','G01'),('P08','G02'),('P08','G03'),('P08','G04'),('P08','G05'),('P08','G11'),('P08','G18'),
-- P09 Anemia Sideroblastik
('P09','G01'),('P09','G02'),('P09','G03'),('P09','G04'),('P09','G05'),('P09','G08'),('P09','G19'),
-- P10 Anemia Normositik
('P10','G01'),('P10','G02'),('P10','G03'),('P10','G04'),('P10','G05'),('P10','G21'),('P10','G22'),('P10','G23'),('P10','G26'),
-- P11 Thalassemia
('P11','G01'),('P11','G02'),('P11','G03'),('P11','G04'),('P11','G05'),('P11','G08'),('P11','G16');

-- ============================================================
--  TABEL 3.11  RIWAYAT (Hasil Diagnosa)
-- ============================================================
CREATE TABLE riwayat (
  id INT AUTO_INCREMENT PRIMARY KEY,
  tanggal DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  gejala_dipilih JSON,
  hasil_dianosa  JSON,
  nama_pasien   VARCHAR(100) DEFAULT NULL,
  umur          INT          DEFAULT NULL,
  jenis_kelamin VARCHAR(10)  DEFAULT NULL,
  session_token VARCHAR(64)  DEFAULT NULL
);
