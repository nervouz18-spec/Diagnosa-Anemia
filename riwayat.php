<?php
include 'config.php';
session_start();

$session_token = $_SESSION['user_token'] ?? null;
$rows = [];
if ($session_token) {
    $stmt = $conn->prepare("SELECT * FROM riwayat WHERE session_token = ? ORDER BY tanggal DESC LIMIT 50");
    $stmt->bind_param("s", $session_token);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($r = $res->fetch_assoc()) $rows[] = $r;
    $stmt->close();
}

$page_title = 'Riwayat Diagnosa';
$active_nav = 'riwayat';
include 'partials/public_header.php';
?>

<main class="page-narrow">
    <div class="page-header">
        <span class="badge badge-info"><i class="fa-solid fa-clock-rotate-left"></i> Riwayat</span>
        <h1 style="margin-top:.85rem;">Riwayat Diagnosa Anda</h1>
        <p class="lead">Daftar pemeriksaan yang pernah Anda lakukan pada perangkat ini.</p>
    </div>

    <?php if (count($rows) === 0): ?>
        <div class="card text-center empty" data-testid="riwayat-empty">
            <i class="fa-solid fa-folder-open"></i>
            <h4>Belum ada riwayat diagnosa</h4>
            <p class="text-muted">Lakukan diagnosa terlebih dahulu untuk melihat catatan di sini.</p>
            <a href="index.php" class="btn btn-primary"><i class="fa-solid fa-stethoscope"></i> Mulai Diagnosa</a>
        </div>
    <?php else: ?>
        <?php foreach ($rows as $i => $row):
            $gjlist = json_decode($row['gejala_dipilih'] ?? '[]', true) ?: [];
            $hslist = json_decode($row['hasil_dianosa'] ?? '[]', true) ?: [];
        ?>
        <div class="card" data-testid="riwayat-<?php echo $i; ?>">
            <div class="card-head">
                <div>
                    <span class="badge badge-muted"><i class="fa-regular fa-calendar"></i> <?php echo date('d M Y · H:i', strtotime($row['tanggal'])); ?></span>
                    <?php if (!empty($row['nama_pasien'])): ?>
                        <span class="badge badge-info" style="margin-left:.3rem;"><i class="fa-solid fa-user"></i> <?php echo htmlspecialchars($row['nama_pasien']); ?></span>
                    <?php endif; ?>
                </div>
                <span class="text-muted text-sm">#<?php echo (int)$row['id']; ?></span>
            </div>
            <div>
                <div class="text-muted text-sm" style="margin-bottom:.25rem;">Gejala Dipilih</div>
                <div style="display:flex;flex-wrap:wrap;gap:.35rem;margin-bottom:.85rem;">
                    <?php foreach ($gjlist as $g): ?>
                        <span class="badge badge-muted"><?php echo htmlspecialchars($g); ?></span>
                    <?php endforeach; ?>
                </div>
                <div class="text-muted text-sm" style="margin-bottom:.25rem;">Hasil Diagnosa</div>
                <?php if (count($hslist) === 0): ?>
                    <div style="background:var(--surface-alt);border-radius:var(--r-md);padding:.85rem 1rem;font-size:.92rem;" class="text-muted">Tidak ada penyakit terdeteksi.</div>
                <?php else: ?>
                    <div style="display:flex;flex-direction:column;gap:.5rem;">
                    <?php foreach ($hslist as $h): ?>
                        <div style="background:var(--primary-soft);border-radius:var(--r-md);padding:.6rem .85rem;">
                            <strong><?php echo htmlspecialchars($h['nama'] ?? '-'); ?></strong>
                            <span class="text-muted text-sm"> · <?php echo htmlspecialchars($h['kode'] ?? ''); ?></span>
                        </div>
                    <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <div style="margin-top:1.5rem;">
        <a href="index.php" class="btn btn-ghost" data-testid="btn-back-diag"><i class="fa-solid fa-arrow-left"></i> Kembali ke Diagnosa</a>
    </div>
</main>

<?php include 'partials/public_footer.php'; ?>
