<?php
include 'config.php';
session_start();

$hasil_diagnosa  = $_SESSION['hasil']           ?? [];
$selected_gejala = $_SESSION['selected_gejala'] ?? [];
$pasien          = $_SESSION['pasien']          ?? [];

// Resolve nama gejala dari kode
$gejala_map = [];
if (count($selected_gejala) > 0) {
    $codes = "'" . implode("','", array_map(fn($c) => $conn->real_escape_string($c), $selected_gejala)) . "'";
    $r = $conn->query("SELECT kode, nama FROM gejala WHERE kode IN ($codes)");
    while ($row = $r->fetch_assoc()) $gejala_map[$row['kode']] = $row['nama'];
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
        <p class="lead">Berikut kemungkinan jenis anemia berdasarkan gejala yang Anda pilih. Penyakit terdeteksi bila Anda memilih <strong>≥ 1 gejala umum</strong> dan <strong>≥ 1 gejala spesifik</strong>.</p>
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
            <?php foreach ($selected_gejala as $kode):
                $is_umum = in_array($kode, ['G01','G02','G03','G04','G05']); ?>
                <span class="badge <?php echo $is_umum?'badge-warning':'badge-info'; ?>" style="<?php echo $is_umum?'':'background:var(--primary-soft);color:var(--primary);'; ?>">
                    <?php echo htmlspecialchars($kode); ?> · <?php echo htmlspecialchars($gejala_map[$kode] ?? $kode); ?>
                    <?php if ($is_umum): ?> · <em style="font-weight:500;">umum</em><?php endif; ?>
                </span>
            <?php endforeach; ?>
        </div>
    </div>

    <h3 style="margin-top:2rem;margin-bottom:1rem;">Kemungkinan Jenis Anemia</h3>

    <?php $jumlah_penyakit = count($hasil_diagnosa); ?>
    <?php if ($jumlah_penyakit > 5): ?>
        <div class="alert alert-danger" data-testid="warning-too-many" style="margin-bottom:1rem;background:#fee2e2;border-left:4px solid #dc2626;color:#7f1d1d;padding:1rem;display:flex;gap:.6rem;align-items:flex-start;border-radius:.5rem;">
            <i class="fa-solid fa-circle-exclamation" style="color:#dc2626;font-size:1.25rem;margin-top:.15rem;"></i>
            <div>
                <strong>Peringatan!</strong><br>
                Tidak mungkin seseorang terkena <strong><?php echo $jumlah_penyakit; ?> jenis penyakit anemia</strong> di waktu yang bersamaan.
                Hasil ini muncul karena Anda memilih terlalu banyak gejala. Mohon ulangi diagnosa dan pilih hanya gejala yang benar-benar Anda rasakan agar hasil lebih akurat.
            </div>
        </div>
    <?php elseif ($jumlah_penyakit > 2 && $jumlah_penyakit < 5): ?>
        <div class="alert alert-warning" data-testid="warning-see-doctor" style="margin-bottom:1rem;">
            <i class="fa-solid fa-user-doctor"></i>
            <div>
                <strong>Untuk segera ke dokter secepatnya agar mengetahui penyakit yang lebih pasti.</strong><br>
                Sistem mendeteksi <strong><?php echo $jumlah_penyakit; ?> kemungkinan jenis anemia</strong>. Konsultasi langsung dengan tenaga medis sangat disarankan untuk pemeriksaan lebih lanjut.
            </div>
        </div>
    <?php endif; ?>

    <?php if (count($hasil_diagnosa) > 0): ?>
        <?php foreach ($hasil_diagnosa as $i => $hasil): ?>
        <div class="card" data-testid="result-<?php echo $i; ?>" style="border-left:4px solid var(--primary);">
            <div class="card-head">
                <div>
                    <span class="badge badge-success">#<?php echo $i+1; ?> · <?php echo htmlspecialchars($hasil['kode_penyakit']); ?></span>
                    <h3 style="margin:.5rem 0 .25rem;"><?php echo htmlspecialchars($hasil['nama_penyakit']); ?></h3>
                </div>
            </div>
            <div class="text-muted text-sm" style="margin-bottom:.5rem;">
                <i class="fa-solid fa-check-double" style="color:var(--primary);"></i> Gejala yang cocok:
            </div>
            <div style="display:flex;flex-wrap:wrap;gap:.35rem;margin-bottom:1rem;">
                <?php foreach ($hasil['gejala_cocok'] as $g):
                    $g_umum = in_array($g, ['G01','G02','G03','G04','G05']); ?>
                    <span class="badge <?php echo $g_umum?'badge-warning':'badge-success'; ?>">
                        <?php echo htmlspecialchars($g); ?> · <?php echo htmlspecialchars($gejala_map[$g] ?? $g); ?>
                    </span>
                <?php endforeach; ?>
            </div>
            <div style="border-top:1px solid var(--border);padding-top:1rem;">
                <strong style="color:var(--text);">Deskripsi:</strong>
                <p style="margin:.4rem 0 0;color:var(--text-muted);"><?php echo nl2br(htmlspecialchars($hasil['deskripsi'])); ?></p>
            </div>
        </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="alert alert-warning" data-testid="no-result">
            <i class="fa-solid fa-triangle-exclamation"></i>
            <div>
                <strong>Tidak ada penyakit yang terdeteksi.</strong><br>
                Pastikan Anda memilih <strong>minimal 1 gejala umum (G01–G05)</strong> dan <strong>minimal 1 gejala spesifik</strong> yang sesuai. Bila gejala terus berlanjut, segera konsultasikan ke tenaga medis.
            </div>
        </div>
    <?php endif; ?>

    <div class="alert alert-warning" style="margin-top:1.5rem;" data-testid="disclaimer">
        <i class="fa-solid fa-user-doctor"></i>
        <div>
            <strong>Catatan Penting.</strong>
            Ini hanya <em>diagnosa sementara</em> berdasarkan basis pengetahuan sistem.
            Untuk kelanjutan pemeriksaan dan diagnosa pasti, silakan konsultasikan ke <strong>dokter ahli</strong> atau fasilitas kesehatan terdekat.
        </div>
    </div>

    <div style="display:flex;gap:.6rem;flex-wrap:wrap;margin-top:1.5rem;">
        <a href="index.php" class="btn btn-primary" data-testid="btn-redo"><i class="fa-solid fa-rotate-right"></i> Diagnosa Ulang</a>
        <a href="riwayat.php" class="btn btn-ghost" data-testid="btn-history"><i class="fa-solid fa-clock-rotate-left"></i> Lihat Riwayat</a>
        <a href="javascript:window.print();" class="btn btn-secondary" data-testid="btn-print"><i class="fa-solid fa-print"></i> Cetak</a>
    </div>
</main>

<?php include 'partials/public_footer.php'; ?>
