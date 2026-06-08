<?php
session_start();
include '../config.php';
if (!isset($_SESSION['admin'])) { header('Location: login.php'); exit; }

$c_gejala   = (int)$conn->query("SELECT COUNT(*) c FROM gejala")->fetch_assoc()['c'];
$c_penyakit = (int)$conn->query("SELECT COUNT(*) c FROM penyakit")->fetch_assoc()['c'];
$c_aturan   = (int)$conn->query("SELECT COUNT(*) c FROM aturan")->fetch_assoc()['c'];
$c_riwayat  = (int)$conn->query("SELECT COUNT(*) c FROM riwayat")->fetch_assoc()['c'];
$c_users    = (int)$conn->query("SELECT COUNT(*) c FROM users")->fetch_assoc()['c'];
$c_today    = (int)$conn->query("SELECT COUNT(*) c FROM riwayat WHERE DATE(tanggal)=CURDATE()")->fetch_assoc()['c'];

$page_title = 'Dashboard';
$active     = 'dashboard';
include '../partials/admin_header.php';
?>

<div class="page-header">
    <h2>Selamat datang, <?php echo htmlspecialchars($_SESSION['admin']); ?> 👋</h2>
    <p class="lead">Ringkasan basis pengetahuan & aktivitas diagnosa.</p>
</div>

<div class="stat-grid" data-testid="stat-grid">
    <div class="stat-card" data-testid="stat-gejala">
        <div class="stat-icon green"><i class="fa-solid fa-stethoscope"></i></div>
        <div><div class="label">Total Gejala</div><div class="value"><?php echo $c_gejala; ?></div></div>
    </div>
    <div class="stat-card" data-testid="stat-penyakit">
        <div class="stat-icon blue"><i class="fa-solid fa-virus"></i></div>
        <div><div class="label">Jenis Penyakit</div><div class="value"><?php echo $c_penyakit; ?></div></div>
    </div>
    <div class="stat-card" data-testid="stat-aturan">
        <div class="stat-icon amber"><i class="fa-solid fa-diagram-project"></i></div>
        <div><div class="label">Aturan (Rules)</div><div class="value"><?php echo $c_aturan; ?></div></div>
    </div>
    <div class="stat-card" data-testid="stat-riwayat">
        <div class="stat-icon rose"><i class="fa-solid fa-clipboard-list"></i></div>
        <div><div class="label">Total Diagnosa</div><div class="value"><?php echo $c_riwayat; ?></div></div>
    </div>
    <div class="stat-card" data-testid="stat-today">
        <div class="stat-icon green"><i class="fa-solid fa-calendar-day"></i></div>
        <div><div class="label">Diagnosa Hari Ini</div><div class="value"><?php echo $c_today; ?></div></div>
    </div>
    <div class="stat-card" data-testid="stat-users">
        <div class="stat-icon blue"><i class="fa-solid fa-user-shield"></i></div>
        <div><div class="label">Admin User</div><div class="value"><?php echo $c_users; ?></div></div>
    </div>
</div>

<div class="card">
    <div class="card-head">
        <h3><i class="fa-solid fa-bolt" style="color:var(--primary);margin-right:.4rem;"></i>Aksi Cepat</h3>
    </div>
    <div class="features">
        <a href="crud_gejala.php" class="feature" style="text-decoration:none;color:inherit;display:block;" data-testid="qa-gejala">
            <div class="ico"><i class="fa-solid fa-stethoscope"></i></div>
            <h4>Kelola Gejala</h4><p>Tambah, ubah, hapus daftar gejala anemia.</p>
        </a>
        <a href="crud_penyakit.php" class="feature" style="text-decoration:none;color:inherit;display:block;" data-testid="qa-penyakit">
            <div class="ico"><i class="fa-solid fa-virus"></i></div>
            <h4>Kelola Penyakit</h4><p>Kelola jenis anemia & deskripsi penanganan.</p>
        </a>
        <a href="crud_aturan.php" class="feature" style="text-decoration:none;color:inherit;display:block;" data-testid="qa-aturan">
            <div class="ico"><i class="fa-solid fa-diagram-project"></i></div>
            <h4>Kelola Aturan</h4><p>Atur relasi gejala ↔ penyakit (knowledge base).</p>
        </a>
        <a href="laporan.php" class="feature" style="text-decoration:none;color:inherit;display:block;" data-testid="qa-laporan">
            <div class="ico"><i class="fa-solid fa-clipboard-list"></i></div>
            <h4>Laporan Diagnosa</h4><p>Lihat seluruh riwayat diagnosa pengguna.</p>
        </a>
    </div>
</div>

<div class="card">
    <div class="card-head">
        <h3><i class="fa-solid fa-clock-rotate-left" style="color:var(--primary);margin-right:.4rem;"></i>Diagnosa Terbaru</h3>
        <a href="laporan.php" class="btn btn-secondary btn-sm">Lihat Semua <i class="fa-solid fa-arrow-right"></i></a>
    </div>
    <div class="table-wrap">
        <table class="data">
            <thead><tr><th>#</th><th>Tanggal</th><th>Pasien</th><th>Gejala</th><th>Ringkasan Hasil</th></tr></thead>
            <tbody>
            <?php
            $rs = $conn->query("SELECT * FROM riwayat ORDER BY tanggal DESC LIMIT 5");
            if ($rs->num_rows === 0): ?>
                <tr><td colspan="5" class="text-center text-muted" style="padding:2rem;">Belum ada diagnosa tercatat.</td></tr>
            <?php else:
                while ($r = $rs->fetch_assoc()):
                    $glist = json_decode($r['gejala_dipilih'] ?? '[]', true) ?: [];
                    $hlist = json_decode($r['hasil_dianosa'] ?? '[]', true) ?: [];
                    $hsummary = count($hlist) > 0
                        ? implode(', ', array_map(fn($h)=>($h['kode']??'').' '.($h['persen']??'').'%', array_slice($hlist,0,2)))
                        : '— tidak ada hasil ≥80%';
                ?>
                <tr>
                    <td>#<?php echo (int)$r['id']; ?></td>
                    <td><?php echo date('d M Y · H:i', strtotime($r['tanggal'])); ?></td>
                    <td><?php echo htmlspecialchars($r['nama_pasien'] ?: '-'); ?></td>
                    <td class="text-muted text-sm" style="max-width:220px;"><?php echo htmlspecialchars(implode(', ', array_slice($glist, 0, 6))).(count($glist)>6?'…':''); ?></td>
                    <td class="text-muted text-sm" style="max-width:280px;"><?php echo htmlspecialchars($hsummary); ?></td>
                </tr>
                <?php endwhile;
            endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../partials/admin_footer.php'; ?>
