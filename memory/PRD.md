# SiPaDiA — Sistem Pakar Diagnosa Anemia (PRD)

## Problem Statement (timeline)
- **Iterasi 1**: Cek semua file dari landing sampai admin, perbaiki error DB, jangan ubah logika perhitungan, buat tampilan dinamis & stylish.
- **Iterasi 2**: Update daftar penyakit P01–P11.
- **Iterasi 3**: Reset database total dengan schema baru (Tabel 3.7–3.11), 11 penyakit, 27 gejala (G01–G05 = gejala umum), 82 rule forward chaining.
- **Iterasi 4 — Rule baru**: Pasien hanya perlu memilih ≥1 gejala umum + ≥1 gejala spesifik agar penyakit terdeteksi (sebelumnya threshold ≥80%).
- **Iterasi 5 — Polishing UX**:
  1. Hapus persentase di seluruh hasil diagnosa.
  2. Hilangkan "X dari Y gejala cocok" → tampilkan daftar gejala yang dipilih saja.
  3. Tambah disclaimer: "Ini hanya diagnosa sementara, untuk kelanjutan ke dokter ahli".
  4. Tambah kolom `deskripsi` di tabel gejala (akan diisi user sendiri lewat admin).
  5. Tampilkan kolom Deskripsi di halaman Informasi → Daftar Gejala.

## Stack
- PHP 8.2 + Apache (port 3000) + MariaDB 10.11 (db `db_anemia`, user `anemia`/`anemia_pass`)
- Plain HTML/CSS modern minimalist (Hunter Green, Epilogue + Work Sans, Font Awesome 6)
- Auto-init via `/app/scripts/init.sh` (install dependencies + import DB jika kosong)

## Skema Database (Final — Tabel 3.7–3.11 + iterasi 5)
- **users** (id, username, password, role ENUM('Admin'))
- **penyakit** (id, kode VARCHAR(10), nama, deskripsi TEXT) — 11 penyakit P01–P11
- **gejala** (id, kode VARCHAR(10), nama, **deskripsi TEXT NULL**) — 27 gejala (deskripsi diisi via admin)
- **aturan** (id, gejala_kode, penyakit_kode) dengan FK ON UPDATE/DELETE CASCADE — 82 rules
- **riwayat** (id, tanggal, gejala_dipilih JSON, hasil_dianosa JSON, + nama_pasien/umur/jenis_kelamin/session_token)

## Knowledge Base (82 rules — G01-G05 gejala umum)
| Penyakit | Gejala spesifik (selain G01–G05) |
|---|---|
| P01 Anemia Defisiensi Besi | G14, G15 |
| P02 Anemia Defisiensi VitaminB12 (Pernisiosa) | G12, G13 |
| P03 Anemia Defisiensi Asam Folat | G13, G27 |
| P04 Anemia Aplastik | G09, G10 |
| P05 Anemia Hemolitik | G06, G07, G08 |
| P06 Anemia Hemolitik Autoinum | G06, G07, G08, G20, G25 |
| P07 Anemia Penyakit Kronis | G24 |
| P08 Anemia Sel Sabit | G11, G18 |
| P09 Anemia Sideroblastik | G08, G19 |
| P10 Anemia Normositik | G21, G22, G23, G26 |
| P11 Thalassemia | G08, G16 |

## Logika Forward Chaining (Iterasi 4 — final)
```
Untuk setiap penyakit P di basis pengetahuan:
    rules_umum     = aturan(P) ∩ {G01, G02, G03, G04, G05}
    rules_spesifik = aturan(P) \ {G01, G02, G03, G04, G05}
    match_umum     = gejala_user ∩ rules_umum
    match_spesifik = gejala_user ∩ rules_spesifik
    IF |match_umum| ≥ 1 AND |match_spesifik| ≥ 1:
        masukkan P ke hasil_diagnosa (dengan list gejala_cocok)
Sort hasil_diagnosa DESC by jumlah gejala cocok
```
**Catatan iterasi 5**: persentase masih dihitung internal (untuk sorting/backward-compat data riwayat lama), TAPI **tidak ditampilkan** ke user di UI manapun.

## Tampilan Hasil Diagnosa (Iterasi 5)
Setiap kartu hasil menampilkan:
- Nomor urut + kode penyakit (badge: `#1 · P01`)
- Nama penyakit (h3)
- Section **"Gejala yang cocok"** → list badge gejala (G01 · Kelelahan ekstrem, G14 · Kuku sendok …)
- **Deskripsi** penyakit (paragraf)

**Tidak ada**: persentase, progress bar, "X dari Y", "X umum · Y spesifik".

**Disclaimer baru di akhir halaman hasil**:
> 🩺 **Catatan Penting.** Ini hanya *diagnosa sementara* berdasarkan basis pengetahuan sistem. Untuk kelanjutan pemeriksaan dan diagnosa pasti, silakan konsultasikan ke **dokter ahli** atau fasilitas kesehatan terdekat.

## What's Implemented (semua iterasi terkumulasi)
- ✅ Schema baru sesuai Tabel 3.7–3.11 + `gejala.deskripsi` (iterasi 5)
- ✅ Semua file PHP konsisten dengan column names baru
- ✅ index.php: form diagnosa pisah **Gejala Umum vs Spesifik** dalam dua card
- ✅ proses.php: forward chaining rule baru (min 1 umum + min 1 spesifik, no threshold)
- ✅ hasil.php (iterasi 5): tanpa persen, tanpa progress, list gejala cocok per penyakit, disclaimer dokter ahli
- ✅ riwayat.php (iterasi 5): tanpa badge persen, tanpa "match/total"
- ✅ informasi.php (iterasi 5): tabel 11 penyakit + tabel 27 gejala **dengan kolom Deskripsi**
- ✅ Admin dashboard: ringkasan "Diagnosa Terbaru" tanpa persen
- ✅ Admin CRUD: gejala (+field deskripsi), penyakit (+deskripsi), aturan, users (role ENUM), laporan (filter + CSV export tanpa persen)
- ✅ Cascade delete via FK constraint
- ✅ Password admin: `admin` / `admin123`
- ✅ Backup SQL di `/app/db_anemia.sql` + snapshot `/app/db_anemia_backup_YYYYMMDD.sql`

## Verifikasi Akhir (Iterasi 5)
| Test | Input | Output |
|---|---|---|
| Diagnosa positif | G01 + G02 + G14 + G24 | P07 + P01 (urut), badge gejala cocok, NO persen, disclaimer dokter ahli ✅ |
| No-result | Hanya G01-G05 | Alert "Tidak ada penyakit terdeteksi" ✅ |
| Halaman informasi | Buka `/informasi.php` | Tabel gejala dengan kolom Deskripsi (— jika kosong) ✅ |
| Admin gejala | Login → menu Gejala | Setiap row punya textarea Deskripsi inline-editable ✅ |

## Credentials
- Admin: `admin` / `admin123` (di `/app/memory/test_credentials.md`)
- MariaDB: user `anemia`/`anemia_pass`, db `db_anemia`

## Cara Mengisi Deskripsi Gejala (untuk user)
1. Login admin: `/admin/login.php` dengan `admin` / `admin123`
2. Klik menu **Gejala** di sidebar
3. Pada setiap row, ketik di textarea **Deskripsi** lalu klik tombol **Simpan**
4. Deskripsi otomatis muncul di `/informasi.php` (publik) — kolom "Deskripsi" pada tabel Daftar Gejala

## Backlog
- P2: Charting tren diagnosa per bulan di dashboard admin
- P2: Dark mode toggle
- P2: Print-friendly stylesheet untuk halaman hasil
- P3: Bulk upload deskripsi gejala via CSV import
- P3: Tampilkan top-N hasil saja jika terlalu banyak penyakit muncul sekaligus
