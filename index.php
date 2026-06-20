<?php
include 'config.php';
session_start();

// Stats untuk hero
$count_gejala   = $conn->query("SELECT COUNT(*) c FROM gejala")->fetch_assoc()['c'];
$count_penyakit = $conn->query("SELECT COUNT(*) c FROM penyakit")->fetch_assoc()['c'];
$count_aturan   = $conn->query("SELECT COUNT(*) c FROM aturan")->fetch_assoc()['c'];

// Gejala umum = G01..G05, sisanya gejala spesifik
$gejala_umum = []; $gejala_spesifik = [];
$rs = $conn->query("SELECT * FROM gejala ORDER BY kode ASC");
while ($r = $rs->fetch_assoc()) {
    if (in_array($r['kode'], ['G01','G02','G03','G04','G05'])) $gejala_umum[] = $r;
    else $gejala_spesifik[] = $r;
}

$page_title = 'Diagnosa';
$active_nav = 'diagnosa';
include 'partials/public_header.php';
?>

<section class="hero" data-testid="hero-section">
    <div class="hero-inner">
        <div>
            <span class="badge badge-info" data-testid="hero-badge"><i class="fa-solid fa-microscope"></i> Forward Chaining Expert System</span>
            <h1 style="margin-top:.85rem;">Deteksi Awal <span style="color:var(--primary);">Anemia</span> Berdasarkan Gejala Anda</h1>
            <p class="lead">
                Sistem Pakar Diagnosa Anemia (SiPaDiA) membantu Anda mengenali kemungkinan jenis anemia berdasarkan gejala yang dirasakan,
                menggunakan basis pengetahuan medis dan metode penalaran <em>forward chaining</em>.
            </p>
            <div class="hero-actions">
                <a href="#diagnosa" class="btn btn-primary" data-testid="hero-cta-diagnosa"><i class="fa-solid fa-stethoscope"></i> Mulai Diagnosa</a>
                <a href="informasi.php" class="btn btn-ghost" data-testid="hero-cta-info"><i class="fa-solid fa-circle-info"></i> Pelajari Anemia</a>
            </div>
            <div class="hero-stats">
                <div><div class="stat-num" data-testid="stat-gejala"><?php echo (int)$count_gejala; ?></div><div class="stat-label">Gejala</div></div>
                <div><div class="stat-num" data-testid="stat-penyakit"><?php echo (int)$count_penyakit; ?></div><div class="stat-label">Jenis Anemia</div></div>
                <div><div class="stat-num" data-testid="stat-aturan"><?php echo (int)$count_aturan; ?></div><div class="stat-label">Aturan</div></div>
            </div>
        </div>
        <div class="hero-illustration" data-testid="hero-illustration">
            <span class="big-icon"><i class="fa-solid fa-heart-pulse"></i></span>
            <h4>Cara Kerja Singkat</h4>
            <p class="text-muted" style="margin:0;">Pilih gejala yang Anda alami, sistem mencocokkan dengan aturan, lalu menghitung persentase kecocokan tiap jenis anemia.</p>
            <ul>
                <li><i class="fa-solid fa-check"></i> Pilih ≥1 gejala umum (G01–G05)</li>
                <li><i class="fa-solid fa-check"></i> Pilih ≥1 gejala spesifik (G06–G27)</li>
                <li><i class="fa-solid fa-check"></i> Sistem cocokkan ke basis pengetahuan</li>
                <li><i class="fa-solid fa-check"></i> Tampilkan kemungkinan jenis anemia</li>
            </ul>
        </div>
    </div>
</section>

<main class="page" id="diagnosa">
    <div class="page-header">
        <h2>Form Diagnosa Anemia</h2>
        <p class="lead">Centang gejala yang Anda alami. Sistem akan menganalisis kemungkinan jenis anemia. <strong>Aturan diagnosa:</strong> pilih minimal 1 gejala umum (G01–G05) dan minimal 1 gejala spesifik dari kemungkinan penyakit.</p>
    </div>

    <form action="proses.php" method="post" data-testid="diagnosa-form" onsubmit="return validateDiagnosa();">
        <div class="card">
            <div class="card-head">
                <h3><i class="fa-solid fa-user-pen" style="color:var(--primary);margin-right:.4rem;"></i>Data Pasien <span class="badge badge-muted" style="margin-left:.4rem;">opsional</span></h3>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label for="nama_pasien">Nama</label>
                    <input type="text" id="nama_pasien" name="nama_pasien" placeholder="Nama lengkap" data-testid="input-nama">
                </div>
                <div class="form-group">
                    <label for="umur">Umur</label>
                    <input type="number" min="0" max="120" id="umur" name="umur" placeholder="Tahun" data-testid="input-umur">
                </div>
                <div class="form-group">
                    <label for="jenis_kelamin">Jenis Kelamin</label>
                    <select id="jenis_kelamin" name="jenis_kelamin" data-testid="input-gender">
                        <option value="">— Pilih —</option>
                        <option value="L">Laki-laki</option>
                        <option value="P">Perempuan</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- ====== Gejala Umum ====== -->
        <div class="card" data-testid="gejala-umum-card">
            <div class="card-head">
                <div>
                    <h3 style="margin:0;"><i class="fa-solid fa-asterisk" style="color:var(--primary);margin-right:.4rem;"></i>Gejala Umum</h3>
                    <p class="text-muted text-sm" style="margin:.25rem 0 0;">Gejala yang muncul pada hampir semua jenis anemia (G01–G05).</p>
                </div>
                <span class="badge badge-info">Umum</span>
            </div>
            <div class="checkbox-grid" data-testid="gejala-umum-grid">
                <?php foreach ($gejala_umum as $row): ?>
                <label class="check-card" data-testid="gejala-<?php echo htmlspecialchars($row['kode']); ?>">
                    <input type="checkbox" name="gejala[]" value="<?php echo htmlspecialchars($row['kode']); ?>" onchange="updateCount();">
                    <span class="code"><?php echo htmlspecialchars($row['kode']); ?></span>
                    <span class="label"><?php echo htmlspecialchars($row['nama']); ?></span>
                </label>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- ====== Gejala Spesifik ====== -->
        <div class="card" data-testid="gejala-spesifik-card">
            <div class="card-head">
                <div>
                    <h3 style="margin:0;"><i class="fa-solid fa-list-check" style="color:var(--primary);margin-right:.4rem;"></i>Gejala Spesifik</h3>
                    <p class="text-muted text-sm" style="margin:.25rem 0 0;">Gejala khas yang membedakan jenis anemia tertentu (G06–G27).</p>
                </div>
                <span class="badge badge-info" id="countLabel" data-testid="count-label">0 dipilih</span>
            </div>
            <div class="checkbox-grid" data-testid="gejala-spesifik-grid">
                <?php foreach ($gejala_spesifik as $row): ?>
                <label class="check-card" data-testid="gejala-<?php echo htmlspecialchars($row['kode']); ?>">
                    <input type="checkbox" name="gejala[]" value="<?php echo htmlspecialchars($row['kode']); ?>" onchange="updateCount();">
                    <span class="code"><?php echo htmlspecialchars($row['kode']); ?></span>
                    <span class="label"><?php echo htmlspecialchars($row['nama']); ?></span>
                </label>
                <?php endforeach; ?>
            </div>

            <div id="warningAllGejala" class="alert alert-warning" style="display:none;margin-top:1rem;" data-testid="warning-all-gejala">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <div>
                    <strong>Peringatan!</strong>
                    Anda memilih <span id="warningCount">terlalu banyak</span> gejala sekaligus. Tidak mungkin seseorang mengalami seluruh gejala anemia secara bersamaan. Mohon pilih hanya gejala yang benar-benar Anda rasakan agar hasil diagnosa lebih akurat.
                </div>
            </div>

            <div class="flex flex-between flex-wrap gap-md mt-md">
                <div class="text-muted text-sm"><i class="fa-solid fa-circle-info"></i> Minimal pilih 1 gejala untuk diagnosa.</div>
                <div style="display:flex;gap:.5rem;">
                    <button type="button" class="btn btn-secondary" onclick="clearAll();" data-testid="btn-clear"><i class="fa-solid fa-eraser"></i> Reset</button>
                    <button type="submit" class="btn btn-primary" data-testid="btn-diagnose"><i class="fa-solid fa-magnifying-glass-chart"></i> Diagnosa Sekarang</button>
                </div>
            </div>
        </div>
    </form>
</main>

<script>
const TOTAL_GEJALA = document.querySelectorAll('input[name="gejala[]"]').length;
const WARN_THRESHOLD = Math.max(10, Math.ceil(TOTAL_GEJALA * 0.7)); // 70% atau minimal 10

function updateCount() {
    const checked = document.querySelectorAll('input[name="gejala[]"]:checked').length;
    document.getElementById('countLabel').textContent = checked + ' dipilih';

    const warningEl = document.getElementById('warningAllGejala');
    const warningCountEl = document.getElementById('warningCount');
    if (checked >= WARN_THRESHOLD) {
        warningCountEl.textContent = checked + ' dari ' + TOTAL_GEJALA;
        warningEl.style.display = 'flex';
    } else {
        warningEl.style.display = 'none';
    }
}
function clearAll() {
    document.querySelectorAll('input[name="gejala[]"]').forEach(c => c.checked = false);
    updateCount();
}
function validateDiagnosa() {
    const checked = document.querySelectorAll('input[name="gejala[]"]:checked').length;
    if (checked === 0) { alert('Silakan pilih minimal 1 gejala terlebih dahulu.'); return false; }
    if (checked >= WARN_THRESHOLD) {
        return confirm('Peringatan: Anda memilih ' + checked + ' dari ' + TOTAL_GEJALA + ' gejala. Tidak mungkin seseorang mengalami seluruh gejala anemia secara bersamaan. Yakin ingin melanjutkan diagnosa?');
    }
    return true;
}
</script>

<?php include 'partials/public_footer.php'; ?>
