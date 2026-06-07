<?php
include 'config.php';
session_start();

// Stats untuk hero
$count_gejala   = $conn->query("SELECT COUNT(*) c FROM gejala")->fetch_assoc()['c'];
$count_penyakit = $conn->query("SELECT COUNT(*) c FROM penyakit")->fetch_assoc()['c'];
$count_aturan   = $conn->query("SELECT COUNT(*) c FROM aturan")->fetch_assoc()['c'];

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
                <li><i class="fa-solid fa-check"></i> Pilih gejala (centang yang sesuai)</li>
                <li><i class="fa-solid fa-check"></i> Sistem cocokkan ke basis pengetahuan</li>
                <li><i class="fa-solid fa-check"></i> Tampilkan hasil ≥ 80% kecocokan</li>
                <li><i class="fa-solid fa-check"></i> Rekomendasi solusi awal</li>
            </ul>
        </div>
    </div>
</section>

<main class="page" id="diagnosa">
    <div class="page-header">
        <h2>Form Diagnosa Anemia</h2>
        <p class="lead">Centang gejala yang Anda alami. Sistem akan menganalisis dan menampilkan kemungkinan jenis anemia dengan tingkat kecocokan minimal <strong>80%</strong>.</p>
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

        <div class="card">
            <div class="card-head">
                <h3><i class="fa-solid fa-list-check" style="color:var(--primary);margin-right:.4rem;"></i>Daftar Gejala</h3>
                <span class="badge badge-info" id="countLabel" data-testid="count-label">0 dipilih</span>
            </div>

            <div class="checkbox-grid" data-testid="gejala-grid">
                <?php
                $gejala = $conn->query("SELECT * FROM gejala ORDER BY kode_gejala ASC");
                while ($row = $gejala->fetch_assoc()):
                ?>
                <label class="check-card" data-testid="gejala-<?php echo htmlspecialchars($row['kode_gejala']); ?>">
                    <input type="checkbox" name="gejala[]" value="<?php echo htmlspecialchars($row['kode_gejala']); ?>" onchange="updateCount();">
                    <span class="code"><?php echo htmlspecialchars($row['kode_gejala']); ?></span>
                    <span class="label"><?php echo htmlspecialchars($row['nama_gejala']); ?></span>
                </label>
                <?php endwhile; ?>
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
function updateCount() {
    const n = document.querySelectorAll('.check-card input[type="checkbox"]:checked').length;
    document.getElementById('countLabel').textContent = n + ' dipilih';
}
function clearAll() {
    document.querySelectorAll('.check-card input[type="checkbox"]').forEach(c => c.checked = false);
    updateCount();
}
function validateDiagnosa() {
    const n = document.querySelectorAll('.check-card input[type="checkbox"]:checked').length;
    if (n === 0) { alert('Silakan pilih minimal 1 gejala terlebih dahulu.'); return false; }
    return true;
}
</script>

<?php include 'partials/public_footer.php'; ?>
