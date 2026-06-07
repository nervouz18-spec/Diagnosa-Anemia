<?php
include 'config.php';
session_start();

$hasil_diagnosa  = $_SESSION['hasil']           ?? [];
$selected_gejala = $_SESSION['selected_gejala'] ?? [];
$pasien          = $_SESSION['pasien']          ?? [];

// Resolve nama gejala dari kode (untuk display)
$gejala_map = [];
if (count($selected_gejala) > 0) {
    $codes = "'" . implode("','", array_map(fn($c) => $conn->real_escape_string($c), $selected_gejala)) . "'";
    $r = $conn->query("SELECT kode_gejala, nama_gejala FROM gejala WHERE kode_gejala IN ($codes)");
    while ($row = $r->fetch_assoc()) $gejala_map[$row['kode_gejala']] = $row['nama_gejala'];
}

unset($_SESSION['hasil']);

$page_title = 'Hasil Diagnosa';
$active_nav = 'diagnosa';
include 'partials/public_header.php';
?>

<main class="page-narrow">
    <div class="page-header">
        <span class="badge badge-success" data-testid="result-badge"><i class="fa-solid fa-circle-check"></i> Analisis Selesai</span>
        <h1 style="margin-top:.85rem;">Hasil Diagnosa Anda</h1>
        <p class="lead">Berikut kemungkinan jenis anemia berdasarkan gejala yang Anda pilih, dengan tingkat kecocokan minimal <strong>80%</strong>.</p>
    </div>

    <?php if (!empty($pasien['nama']) || !empty($pasien['umur']) || !empty($pasien['jenis_kelamin'])): ?>
    <div class="card" data-testid="pasien-card">
        <div class="card-head"><h3><i class="fa-solid fa-user" style="color:var(--primary);margin-right:.4rem;"></i>Data Pasien</h3></div>
        <div class="form-row">
            <div><div class="text-muted text-sm">Nama</div><strong><?php echo htmlspecialchars($pasien['nama'] ?: '-'); ?></strong></div>
            <div><div class="text-muted text-sm">Umur</div><strong><?php echo $pasien['umur'] ? (int)$pasien['umur'].' tahun' : '-'; ?></strong></div>
            <div><div class="text-muted text-sm">Jenis Kelamin</div><strong><?php echo ($pasien['jenis_kelamin']==='L'?'Laki-laki':($pasien['jenis_kelamin']==='P'?'Perempuan':'-')); ?></strong></div>
        </div>
    </div>
    <?php endif; ?>

    <div class="card" data-testid="gejala-summary">
        <div class="card-head">
            <h3><i class="fa-solid fa-list" style="color:var(--primary);margin-right:.4rem;"></i>Gejala yang Dipilih</h3>
            <span class="badge badge-muted"><?php echo count($selected_gejala); ?> gejala</span>
        </div>
        <div style="display:flex;flex-wrap:wrap;gap:.4rem;">
            <?php foreach ($selected_gejala as $kode): ?>
                <span class="badge badge-info" style="background:var(--primary-soft);color:var(--primary);">
                    <?php echo htmlspecialchars($kode); ?> · <?php echo htmlspecialchars($gejala_map[$kode] ?? $kode); ?>
                </span>
            <?php endforeach; ?>
        </div>
    </div>

    <h3 style="margin-top:2rem;margin-bottom:1rem;">Kemungkinan Jenis Anemia</h3>

    <?php if (count($hasil_diagnosa) > 0): ?>
        <?php foreach ($hasil_diagnosa as $i => $hasil): ?>
        <div class="result-card" data-testid="result-<?php echo $i; ?>">
            <div>
                <span class="badge badge-success">#<?php echo $i+1; ?> · <?php echo htmlspecialchars($hasil['kode_penyakit']); ?></span>
                <h3 style="margin:.5rem 0 .25rem;"><?php echo htmlspecialchars($hasil['nama_penyakit']); ?></h3>
                <div class="meta"><i class="fa-solid fa-check-double"></i> <?php echo (int)$hasil['match']; ?> dari <?php echo (int)$hasil['total']; ?> gejala cocok</div>
            </div>
            <div class="percent">
                <div class="num"><?php echo (int)$hasil['persen']; ?>%</div>
                <div class="lbl">kecocokan</div>
            </div>
            <div class="progress"><div class="bar" style="width: <?php echo (int)$hasil['persen']; ?>%;"></div></div>
            <p class="full"><strong style="color:var(--text);">Solusi:</strong> <?php echo nl2br(htmlspecialchars($hasil['solusi'])); ?></p>
        </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="alert alert-warning" data-testid="no-result">
            <i class="fa-solid fa-triangle-exclamation"></i>
            <div>
                <strong>Tidak ada jenis anemia dengan kecocokan ≥ 80%.</strong><br>
                Coba periksa kembali pilihan gejala Anda, atau konsultasikan langsung dengan tenaga medis untuk pemeriksaan lanjutan.
            </div>
        </div>
    <?php endif; ?>

    <div class="alert alert-warning" style="margin-top:1.5rem;" data-testid="disclaimer">
        <i class="fa-solid fa-hospital"></i>
        <div>
            <strong>Disclaimer Medis.</strong>
            Hasil ini hanya deteksi awal otomatis dan <em>tidak menggantikan diagnosa dokter</em>.
            Untuk kelanjutan pemeriksaan, silakan kunjungi puskesmas atau rumah sakit terdekat.
        </div>
    </div>

    <div style="display:flex;gap:.6rem;flex-wrap:wrap;margin-top:1.5rem;">
        <a href="index.php" class="btn btn-primary" data-testid="btn-redo"><i class="fa-solid fa-rotate-right"></i> Diagnosa Ulang</a>
        <a href="riwayat.php" class="btn btn-ghost" data-testid="btn-history"><i class="fa-solid fa-clock-rotate-left"></i> Lihat Riwayat</a>
        <a href="javascript:window.print();" class="btn btn-secondary" data-testid="btn-print"><i class="fa-solid fa-print"></i> Cetak</a>
    </div>
</main>

<?php include 'partials/public_footer.php'; ?>
