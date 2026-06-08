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
            <p>Kelelahan ekstrem, kulit/bibir pucat, pusing & sakit kepala, sesak napas saat aktivitas, dan detak jantung cepat.</p>
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
                <thead><tr><th>Kode</th><th>Nama Penyakit</th><th>Deskripsi</th></tr></thead>
                <tbody>
                <?php $rs = $conn->query("SELECT * FROM penyakit ORDER BY kode");
                while ($p = $rs->fetch_assoc()): ?>
                    <tr>
                        <td><span class="badge badge-muted"><?php echo htmlspecialchars($p['kode']); ?></span></td>
                        <td><strong><?php echo htmlspecialchars($p['nama']); ?></strong></td>
                        <td class="text-muted"><?php echo htmlspecialchars($p['deskripsi']); ?></td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card mt-lg">
        <h3>Daftar Gejala</h3>
        <p class="text-muted text-sm">G01–G05 merupakan <strong>gejala umum</strong> yang dimiliki hampir semua jenis anemia; sisanya adalah gejala spesifik.</p>
        <div class="table-wrap mt-md">
            <table class="data">
                <thead><tr><th>Kode</th><th>Nama Gejala</th><th>Tipe</th></tr></thead>
                <tbody>
                <?php $gs = $conn->query("SELECT * FROM gejala ORDER BY kode");
                while ($g = $gs->fetch_assoc()):
                    $is_umum = in_array($g['kode'], ['G01','G02','G03','G04','G05']);
                ?>
                    <tr>
                        <td><span class="badge badge-muted"><?php echo htmlspecialchars($g['kode']); ?></span></td>
                        <td><?php echo htmlspecialchars($g['nama']); ?></td>
                        <td><span class="badge <?php echo $is_umum?'badge-warning':'badge-success'; ?>"><?php echo $is_umum?'Umum':'Spesifik'; ?></span></td>
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
