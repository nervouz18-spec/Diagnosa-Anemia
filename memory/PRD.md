# SiPaDiA — Sistem Pakar Diagnosa Anemia (PRD)

## Problem Statement
"Tolong di cek semua file mulai dari landing page sampai admin dan perbaiki dengan database jika ada error dan jangan merubah logika perhitungan sistem pakarnya. Buat tampilan jadi dinamis, stylish."
(Iterasi 2) "Tolong ganti DB penyakit anemia P01-P11 sesuai daftar baru."

## Stack
- PHP 8.2 + Apache (port 3000 internal, exposed via REACT_APP_BACKEND_URL preview)
- MariaDB 10.11, database `db_anemia`, user `anemia` / `anemia_pass`
- Plain HTML + custom CSS (modern minimalist, Hunter Green theme)
- Fonts: Epilogue (heading) + Work Sans (body) via Google Fonts
- Icons: Font Awesome 6.5

## User Personas
1. **Pengguna umum** — masyarakat awam yang ingin mengecek gejala anemia.
2. **Admin** — mengelola basis pengetahuan, user, laporan.

## Core Requirements (static)
- Diagnosa via forward chaining (% = match / total × 100, threshold ≥ 80%) — TIDAK BOLEH DIUBAH.
- Riwayat pengguna anonim tersimpan di DB (session_token).
- CRUD penuh untuk Gejala, Penyakit, Aturan, Users, Laporan.

## What's been implemented (2026-06-07/08)
- ✅ Setup environment: Apache 2 + PHP 8.2 + MariaDB via supervisor (auto-init script `/app/scripts/init.sh`).
- ✅ Database schema upgrade: tambah `users.nama_lengkap/role/created_at`, `riwayat.nama_pasien/umur/jenis_kelamin/session_token`.
- ✅ Modern minimalist redesign untuk SEMUA halaman publik & admin:
  - Public: `index.php` (hero + form), `hasil.php` (progress bar persen + solusi), `informasi.php`, `tentang.php`, `riwayat.php`.
  - Admin: `login.php`, `dashboard.php` (stat cards + recent diagnoses), `crud_gejala.php`, `crud_penyakit.php`, `crud_aturan.php`, `crud_users.php` (NEW), `laporan.php` (NEW dengan filter + export CSV + pagination).
- ✅ Partial layout: `partials/public_header.php`, `public_footer.php`, `admin_header.php` (sidebar), `admin_footer.php`.
- ✅ Prepared statements untuk semua input user (anti SQL injection).
- ✅ Data-testid lengkap pada elemen interaktif.
- ✅ Validasi minimal 1 gejala di form diagnosa.
- ✅ Forward chaining logic di `/app/proses.php` lines 18–40 UTUH (verified by testing agent).
- ✅ **Database penyakit diperbarui** ke 11 penyakit (P01–P11) sesuai daftar user:
  - P01 Anemia Defisiensi Besi
  - P02 Anemia Defisiensi VitaminB12 (Pernisiosa)
  - P03 Anemia Defisiensi Asam Folat
  - P04 Anemia Aplastik
  - P05 Anemia Hemolitik
  - P06 Anemia Hemolitik Autoinum (baru)
  - P07 Anemia Penyakit Kronis (rule lama Penyakit Kronis dipindahkan dari P06 ke P07)
  - P08 Anemia Sel Sabit (rule lama Sel Sabit dipindahkan dari P07 ke P08)
  - P09 Anemia Sideroblastik (baru)
  - P10 Anemia Normositik (baru)
  - P11 Thalassemia (rule lama Thalassemia dipindahkan dari P08 ke P11)
- ✅ Tested forward chaining: input 7 gejala Thalassemia → P11 Thalassemia 100% kecocokan.

## Tested
- Iterasi 1 testing agent: Public side 100% pass (7/7 flows). Admin login render + redirect protection + logout verified. CRUD admin perlu retest manual.
- Iterasi 2: DB penyakit verified via curl (form submit → hasil) — P11 Thalassemia muncul dengan 100% kecocokan.

## Backlog / Next
- P1: Tambahkan aturan default untuk penyakit baru P06 (Hemolitik Autoinum), P09 (Sideroblastik), P10 (Normositik) lewat admin CRUD aturan.
- P2: Dark mode toggle.
- P2: Print-friendly stylesheet untuk halaman hasil.
- P2: Charting jenis anemia per bulan di dashboard admin.

## Notes
- `db_anemia.sql` final ada di `/app/db_anemia.sql` — bisa dipakai untuk deploy ke phpMyAdmin/MySQL lokal.
- Container restart akan auto-reinstall package + re-import DB via `/app/scripts/init.sh` (dipanggil di supervisor).
