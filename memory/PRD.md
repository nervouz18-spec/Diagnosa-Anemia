# SiPaDiA — Sistem Pakar Diagnosa Anemia (PRD)

## Problem Statement
- (Iterasi 1) "Cek semua file dari landing sampai admin, perbaiki error DB, jangan ubah logika perhitungan sistem pakar, buat tampilan dinamis & stylish."
- (Iterasi 2) "Update daftar penyakit P01–P11."
- (Iterasi 3) "Reset database total: schema baru (Tabel 3.7–3.11), 11 penyakit, 27 gejala (G01–G05 = gejala umum), 11 rule baru dari tabulasi forward chaining."
- (Iterasi 4) **Rule baru:** "Tidak harus memilih semua gejala umum (minimal 1) dan masing-masing penyakit memiliki 1 gejala spesifik. Misal P01 punya G14 dan G15 — kalau user pilih G14 saja + 1 gejala umum, hasilnya keluar P01."

## Stack
- PHP 8.2 + Apache (port 3000) + MariaDB 10.11 (db `db_anemia`, user `anemia`/`anemia_pass`)
- Plain HTML/CSS modern minimalist (Hunter Green, Epilogue + Work Sans, Font Awesome 6)
- Auto-init via `/app/scripts/init.sh` saat container boot (install Apache/PHP/MariaDB, import DB jika kosong)

## Skema Database (Final — Tabel 3.7–3.11)
- **users** (id, username, password, role ENUM('Admin'))
- **penyakit** (id, kode VARCHAR(10), nama, deskripsi TEXT) — 11 penyakit P01–P11
- **gejala** (id, kode VARCHAR(10), nama) — 27 gejala G01–G27 (G01–G05 = umum)
- **aturan** (id, gejala_kode, penyakit_kode) dengan FK ON UPDATE/DELETE CASCADE — 82 rules
- **riwayat** (id, tanggal, gejala_dipilih JSON, hasil_dianosa JSON, + nama_pasien/umur/jenis_kelamin/session_token)

## Knowledge Base (82 rules)
| Penyakit | Gejala (gejala umum G01–G05 + spesifik) |
|---|---|
| P01 Anemia Defisiensi Besi | G01,G02,G03,G04,G05, **G14,G15** |
| P02 Anemia Defisiensi VitaminB12 (Pernisiosa) | G01–G05, **G12,G13** |
| P03 Anemia Defisiensi Asam Folat | G01–G05, **G13,G27** |
| P04 Anemia Aplastik | G01–G05, **G09,G10** |
| P05 Anemia Hemolitik | G01–G05, **G06,G07,G08** |
| P06 Anemia Hemolitik Autoinum | G01–G05, **G06,G07,G08,G20,G25** |
| P07 Anemia Penyakit Kronis | G01–G05, **G24** |
| P08 Anemia Sel Sabit | G01–G05, **G11,G18** |
| P09 Anemia Sideroblastik | G01–G05, **G08,G19** |
| P10 Anemia Normositik | G01–G05, **G21,G22,G23,G26** |
| P11 Thalassemia | G01–G05, **G08,G16** |

## Logika Forward Chaining (Iterasi 4 — RULE TERBARU)
```
Untuk setiap penyakit P di basis pengetahuan:
    rules_umum     = aturan(P) ∩ {G01,G02,G03,G04,G05}
    rules_spesifik = aturan(P) \ {G01,G02,G03,G04,G05}
    match_umum     = gejala_user ∩ rules_umum
    match_spesifik = gejala_user ∩ rules_spesifik
    IF |match_umum| ≥ 1 AND |match_spesifik| ≥ 1:
        persen = round((|match_umum| + |match_spesifik|) / |aturan(P)| × 100)
        masukkan P ke hasil_diagnosa
Urutkan hasil_diagnosa DESC by persen
```
- Tidak ada threshold persen (yang penting min 1 umum + 1 spesifik).
- Persen ditampilkan sebagai indikator kepercayaan, bukan filter.

### Catatan: Iterasi 1–3 menggunakan rule lama (≥80%). Iterasi 4 mengubah ini secara eksplisit atas permintaan user.

## What's Implemented
- ✅ Schema baru sesuai Tabel 3.7–3.11 dengan JSON columns untuk riwayat
- ✅ Semua file PHP menggunakan column names baru: `kode`, `nama`, `deskripsi`, `penyakit_kode`, `gejala_kode`, `gejala_dipilih`, `hasil_dianosa`
- ✅ index.php: form diagnosa memisahkan **Gejala Umum (G01–G05) vs Gejala Spesifik (G06–G27)** dalam dua card berbeda dengan label badge
- ✅ proses.php: forward chaining rule baru (min 1 umum + min 1 spesifik, no threshold)
- ✅ hasil.php: progress bar % kecocokan, rincian "X umum · Y spesifik" per kartu hasil, alert no-result yang informatif
- ✅ Informasi.php: tabel 11 penyakit + tabel 27 gejala dengan label Umum/Spesifik
- ✅ Riwayat.php: parse JSON kolom, badge persen, tampilan card per riwayat
- ✅ Admin: dashboard (stat cards + diagnosa terbaru), CRUD gejala/penyakit (deskripsi)/aturan/users (role ENUM), laporan (filter + CSV export + JSON parsing)
- ✅ Cascade delete: hapus gejala/penyakit otomatis hapus aturan terkait (FK constraint)
- ✅ Password admin hash diperbaiki (`admin` / `admin123`)
- ✅ Backup SQL: `/app/db_anemia.sql` (install) + `/app/db_anemia_backup_YYYYMMDD.sql` (snapshot)

## Verifikasi Rule Baru
| Test | Input | Output |
|---|---|---|
| 1 | G01 + G14 | P01 Anemia Defisiensi Besi (29%) ✅ |
| 2 | G01 + G15 | P01 Anemia Defisiensi Besi (29%) ✅ |
| 3 | Hanya G14 | "Tidak ada penyakit terdeteksi" (no umum) ✅ |
| 4 | Hanya G01–G05 | "Tidak ada penyakit terdeteksi" (no spesifik) ✅ |
| 5 | G02 + G08 | P05, P06, P09, P11 (G08 dimiliki 4 penyakit), sorted by % ✅ |
| 6 | 7 gejala P01 lengkap | P01 100% + P07 83% (P07 punya G01–G05+G24) ✅ |

## Credentials
- Admin: `admin` / `admin123`
- MariaDB: user `anemia`/`anemia_pass`, db `db_anemia`
- File: `/app/memory/test_credentials.md`

## Backlog
- P2: Charting tren diagnosa per bulan di dashboard admin
- P2: Dark mode toggle
- P2: Print-friendly stylesheet untuk halaman hasil
- P3: Tambahkan gejala spesifik unik untuk tiap penyakit (saat ini G08 dipakai P05/P06/P09/P11, G13 dipakai P02/P03) — bisa menambah noise di hasil
- P3: Pertimbangkan menampilkan top-N hasil saja (mis. maks 5) bila terlalu banyak penyakit muncul
