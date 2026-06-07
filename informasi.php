<?php
include 'config.php';
session_start();

$page_title = 'Informasi Anemia';
$active_nav = 'informasi';
include 'partials/public_header.php';
?>

<main class="page-narrow">
    <div class="page-header">
        <span class="badge badge-info"><i class="fa-solid fa-book-medical"></i> Edukasi</span>
        <h1 style="margin-top:.85rem;">Mengenal Anemia Lebih Dekat</h1>
        <p class="lead">Pengetahuan dasar tentang anemia: definisi, gejala umum, penyebab, dan langkah penanganannya.</p>
    </div>

    <div class="features">
        <div class="feature">
            <div class="ico"><i class="fa-solid fa-droplet"></i></div>
            <h4>Apa itu anemia?</h4>
            <p>Kondisi di mana jumlah sel darah merah atau konsentrasi hemoglobin berada di bawah normal sehingga tubuh kekurangan oksigen.</p>
        </div>
        <div class="feature">
            <div class="ico"><i class="fa-solid fa-bolt"></i></div>
            <h4>Gejala umum</h4>
            <p>Lemas/mudah lelah, pucat, pusing, sesak napas, jantung berdebar, tangan/kaki dingin, dan lainnya.</p>
        </div>
        <div class="feature">
            <div class="ico"><i class="fa-solid fa-dna"></i></div>
            <h4>Penyebab</h4>
            <p>Kekurangan zat besi, vitamin B12, asam folat, kelainan genetik (thalassemia, sel sabit), penyakit kronis, infeksi.</p>
        </div>
        <div class="feature">
            <div class="ico"><i class="fa-solid fa-kit-medical"></i></div>
            <h4>Penanganan</h4>
            <p>Perbaikan pola makan, konsumsi suplemen, pengobatan sesuai penyebab. Konsultasi ke dokter jika gejala berat.</p>
        </div>
    </div>

    <div class="card mt-lg">
        <h3>Jenis-Jenis Anemia yang Diidentifikasi Sistem</h3>
        <div class="table-wrap mt-md">
            <table class="data">
                <thead><tr><th>Kode</th><th>Nama Penyakit</th><th>Solusi Awal</th></tr></thead>
                <tbody>
                <?php $rs = $conn->query("SELECT * FROM penyakit ORDER BY kode_penyakit");
                while ($p = $rs->fetch_assoc()): ?>
                    <tr>
                        <td><span class="badge badge-muted"><?php echo htmlspecialchars($p['kode_penyakit']); ?></span></td>
                        <td><strong><?php echo htmlspecialchars($p['nama_penyakit']); ?></strong></td>
                        <td class="text-muted"><?php echo htmlspecialchars($p['solusi']); ?></td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="alert mt-lg">
        <i class="fa-solid fa-circle-info"></i>
        <div>Edukasi ini bersifat umum. Untuk diagnosa pasti dan rencana terapi, konsultasikan kepada dokter atau fasilitas kesehatan resmi.</div>
    </div>
</main>

<?php include 'partials/public_footer.php'; ?>
