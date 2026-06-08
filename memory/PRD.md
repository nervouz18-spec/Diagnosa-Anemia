# SiPaDiA — Sistem Pakar Diagnosa Anemia (PRD)

## Problem Statement
(Iterasi 3) "Ganti DB ulang untuk penyakit (P01-P11), gejala (G01-G27 nama baru), dan aturan (tabulasi tiap penyakit). Buat skema sesuai Tabel 3.7-3.11. G01-G05 = gejala umum. Update juga di website."

## Stack
- PHP 8.2 + Apache (port 3000) + MariaDB 10.11 (db `db_anemia`, user `anemia`/`anemia_pass`)
- Plain HTML/CSS — modern minimalist (Hunter Green, Epilogue + Work Sans, Font Awesome 6)
- Auto-init via `/app/scripts/init.sh` (dipanggil supervisor saat boot)

## Skema Database (Final, sesuai Tabel 3.7–3.11)
- **users** (id, username, password, role ENUM('Admin'))
- **penyakit** (id, kode VARCHAR(10), nama, deskripsi TEXT) — 11 penyakit P01–P11
- **gejala** (id, kode VARCHAR(10), nama) — 27 gejala G01–G27 (G01–G05 = umum)
- **aturan** (id, gejala_kode, penyakit_kode) dengan FK ON UPDATE/DELETE CASCADE — 82 rules
- **riwayat** (id, tanggal, gejala_dipilih JSON, hasil_dianosa JSON, + metadata pasien)

## Knowledge Base (Forward Chaining Rules)
- P01 Anemia Defisiensi Besi → G01,G02,G03,G04,G05,G14,G15 (7)
- P02 Anemia Defisiensi VitaminB12 (Pernisiosa) → G01-G05,G12,G13 (7)
- P03 Anemia Defisiensi Asam Folat → G01-G05,G13,G27 (7)
- P04 Anemia Aplastik → G01-G05,G09,G10 (7)
- P05 Anemia Hemolitik → G01-G05,G06,G07,G08 (8)
- P06 Anemia Hemolitik Autoinum → G01-G05,G06,G07,G08,G20,G25 (10)
- P07 Anemia Penyakit Kronis → G01-G05,G24 (6)
- P08 Anemia Sel Sabit → G01-G05,G11,G18 (7)
- P09 Anemia Sideroblastik → G01-G05,G08,G19 (7)
- P10 Anemia Normositik → G01-G05,G21,G22,G23,G26 (9)
- P11 Thalassemia → G01-G05,G08,G16 (7)
Total = 82 rules. G01-G05 muncul di setiap penyakit (gejala umum).

## Logika Perhitungan (TIDAK DIUBAH)
- `percent = round(match_count / total_gejala * 100)`, threshold ≥ 80%
- Forward chaining tetap menggunakan iterasi semua penyakit di DB.

## What's Implemented
- ✅ Schema baru sesuai spek Tabel 3.7-3.11 dengan JSON columns untuk riwayat
- ✅ All PHP files updated to use new column names: `kode`, `nama`, `deskripsi`, `penyakit_kode`, `gejala_kode`, `gejala_dipilih`, `hasil_dianosa`
- ✅ Index.php: form diagnosa **memisahkan Gejala Umum (G01-G05) vs Gejala Spesifik (G06-G27)** dalam dua card dengan label badge
- ✅ Hasil.php: badge umum vs spesifik untuk gejala yang dipilih; progress bar persentase; deskripsi penyakit
- ✅ Informasi.php: tampilkan tabel 11 penyakit + tabel 27 gejala (dengan label Umum/Spesifik)
- ✅ Riwayat.php: parse JSON kolom, tampilkan list hasil dengan badge persentase
- ✅ Admin CRUD all working: gejala, penyakit, deskripsi, aturan, users (role ENUM), laporan dengan JSON parsing + CSV export
- ✅ Password admin hash fix (was corrupted in original SQL)
- ✅ Cascade delete: hapus gejala/penyakit otomatis hapus aturan terkait (FK constraint)
- ✅ Verified end-to-end: 7 gejala P01 → 100% match; G01-G05 only → P07 83%; 10 gejala P06 → multiple results

## Tested
- All 6 public pages: HTTP 200, no PHP errors
- All 6 admin pages (post-login): HTTP 200, no PHP errors
- CRUD gejala add/edit/delete + cascade ✅
- Duplicate rule prevention ✅
- Login `admin`/`admin123` ✅
- Forward chaining calculations verified via curl

## Credentials
- Admin: `admin` / `admin123` (di `/app/memory/test_credentials.md`)
- MariaDB: user `anemia`/`anemia_pass`, db `db_anemia`

## Notes / Backlog
- `db_anemia.sql` di `/app/db_anemia.sql` — bisa langsung di-import ke phpMyAdmin lokal.
- Tampilan public sekarang menonjolkan pemisahan Gejala Umum vs Spesifik untuk edukasi user.
- P2: Charting tren diagnosa per bulan; dark mode.
