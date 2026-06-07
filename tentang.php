<?php
session_start();
$page_title = 'Tentang Sistem';
$active_nav = 'tentang';
include 'partials/public_header.php';
?>

<main class="page-narrow">
    <div class="page-header">
        <span class="badge badge-info"><i class="fa-solid fa-circle-info"></i> Tentang</span>
        <h1 style="margin-top:.85rem;">Tentang SiPaDiA</h1>
        <p class="lead">Sistem Pakar Diagnosa Anemia berbasis metode forward chaining.</p>
    </div>

    <div class="card">
        <h3>Apa itu SiPaDiA?</h3>
        <p>
            <strong>SiPaDiA</strong> (Sistem Pakar Diagnosa Anemia) adalah aplikasi web yang membantu masyarakat melakukan
            deteksi awal gejala anemia secara mandiri. Aplikasi ini menggunakan <em>metode forward chaining</em>
            dan basis pengetahuan medis untuk mengolah gejala yang dipilih pengguna menjadi hasil analisa kemungkinan
            jenis anemia beserta solusi singkatnya.
        </p>
    </div>

    <div class="card">
        <h3>Cara Kerja</h3>
        <div class="features">
            <div class="feature">
                <div class="ico"><i class="fa-solid fa-1"></i></div>
                <h4>Input Gejala</h4>
                <p>Pengguna memilih gejala yang dialami dari daftar yang tersedia.</p>
            </div>
            <div class="feature">
                <div class="ico"><i class="fa-solid fa-2"></i></div>
                <h4>Pencocokan Aturan</h4>
                <p>Sistem mencocokkan gejala dengan basis pengetahuan tiap jenis anemia.</p>
            </div>
            <div class="feature">
                <div class="ico"><i class="fa-solid fa-3"></i></div>
                <h4>Hitung Kecocokan</h4>
                <p>Menghitung persentase = (jumlah gejala cocok) / (total gejala penyakit) × 100%.</p>
            </div>
            <div class="feature">
                <div class="ico"><i class="fa-solid fa-4"></i></div>
                <h4>Tampilkan Hasil</h4>
                <p>Penyakit dengan kecocokan ≥ 80% disajikan beserta rekomendasi solusi awal.</p>
            </div>
        </div>
    </div>

    <div class="alert alert-warning">
        <i class="fa-solid fa-triangle-exclamation"></i>
        <div>Sistem ini <strong>tidak dapat menggantikan pemeriksaan dokter</strong>. Untuk kelanjutan pemeriksaan, disarankan menghubungi puskesmas atau rumah sakit terdekat.</div>
    </div>

    <div class="card">
        <div class="form-row">
            <div><div class="text-muted text-sm">Versi Aplikasi</div><strong>2.0</strong></div>
            <div><div class="text-muted text-sm">Metode</div><strong>Forward Chaining</strong></div>
            <div><div class="text-muted text-sm">Stack</div><strong>PHP + MySQL</strong></div>
        </div>
    </div>
</main>

<?php include 'partials/public_footer.php'; ?>
