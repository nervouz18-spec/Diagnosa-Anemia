<?php
session_start();
include '../config.php';
if (!isset($_SESSION['admin'])) { header('Location: login.php'); exit; }

$msg = ''; $msg_type = '';

if (isset($_POST['tambah'])) {
    $kp = trim($_POST['kode_penyakit']);
    $kg = trim($_POST['kode_gejala']);
    if ($kp && $kg) {
        // Cegah duplikat
        $check = $conn->prepare("SELECT id_aturan FROM aturan WHERE kode_penyakit=? AND kode_gejala=?");
        $check->bind_param("ss", $kp, $kg);
        $check->execute(); $check->store_result();
        if ($check->num_rows > 0) {
            $msg = "Aturan tersebut sudah ada."; $msg_type = 'warning';
        } else {
            $stmt = $conn->prepare("INSERT INTO aturan (kode_penyakit, kode_gejala) VALUES (?, ?)");
            $stmt->bind_param("ss", $kp, $kg);
            if ($stmt->execute()) { $msg = "Aturan ditambahkan."; $msg_type = 'success'; }
            else { $msg = "Gagal: ".$stmt->error; $msg_type = 'danger'; }
            $stmt->close();
        }
        $check->close();
    }
}

if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    $conn->query("DELETE FROM aturan WHERE id_aturan=$id");
    $msg = "Aturan dihapus."; $msg_type = 'success';
    header("Location: crud_aturan.php?msg=".urlencode($msg)."&t=".$msg_type); exit;
}

if (isset($_GET['msg'])) { $msg = $_GET['msg']; $msg_type = $_GET['t'] ?? 'info'; }

$all_penyakit = $conn->query("SELECT * FROM penyakit ORDER BY kode_penyakit");
$all_gejala   = $conn->query("SELECT * FROM gejala ORDER BY kode_gejala");

$filter_penyakit = $_GET['filter'] ?? '';
$where = $filter_penyakit ? "WHERE a.kode_penyakit='".$conn->real_escape_string($filter_penyakit)."'" : "";
$aturan = $conn->query("SELECT a.id_aturan, a.kode_penyakit, p.nama_penyakit, a.kode_gejala, g.nama_gejala
                        FROM aturan a
                        LEFT JOIN penyakit p ON a.kode_penyakit=p.kode_penyakit
                        LEFT JOIN gejala g ON a.kode_gejala=g.kode_gejala
                        $where
                        ORDER BY a.kode_penyakit, a.kode_gejala");

// Penyakit list untuk filter dropdown (kedua kali query karena cursor sudah habis)
$penyakit_filter = $conn->query("SELECT * FROM penyakit ORDER BY kode_penyakit");

$page_title = 'Manajemen Aturan';
$active     = 'aturan';
include '../partials/admin_header.php';
?>

<div class="page-header">
    <h2>Manajemen Aturan (Basis Pengetahuan)</h2>
    <p class="lead">Relasi antara <strong>penyakit</strong> dan <strong>gejala</strong> yang menjadi dasar penalaran forward chaining.</p>
</div>

<?php if ($msg): ?>
    <div class="alert alert-<?php echo htmlspecialchars($msg_type); ?>"><i class="fa-solid fa-circle-info"></i> <?php echo htmlspecialchars($msg); ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-head"><h3><i class="fa-solid fa-plus" style="color:var(--primary);margin-right:.4rem;"></i>Tambah Aturan</h3></div>
    <form method="post" data-testid="form-tambah-aturan">
        <div class="form-row">
            <div class="form-group">
                <label>Penyakit</label>
                <select name="kode_penyakit" required data-testid="select-penyakit">
                    <option value="">— Pilih Penyakit —</option>
                    <?php while ($p = $all_penyakit->fetch_assoc()): ?>
                        <option value="<?php echo htmlspecialchars($p['kode_penyakit']); ?>"><?php echo htmlspecialchars($p['kode_penyakit'].' · '.$p['nama_penyakit']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Gejala</label>
                <select name="kode_gejala" required data-testid="select-gejala">
                    <option value="">— Pilih Gejala —</option>
                    <?php while ($g = $all_gejala->fetch_assoc()): ?>
                        <option value="<?php echo htmlspecialchars($g['kode_gejala']); ?>"><?php echo htmlspecialchars($g['kode_gejala'].' · '.$g['nama_gejala']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="form-group" style="display:flex;align-items:flex-end;">
                <button type="submit" name="tambah" class="btn btn-primary btn-block" data-testid="btn-tambah-aturan"><i class="fa-solid fa-plus"></i> Tambah Aturan</button>
            </div>
        </div>
    </form>
</div>

<div class="card">
    <div class="card-head">
        <h3><i class="fa-solid fa-list" style="color:var(--primary);margin-right:.4rem;"></i>Daftar Aturan</h3>
        <form method="get" style="display:flex;gap:.5rem;align-items:center;">
            <select name="filter" onchange="this.form.submit();" style="min-width:220px;">
                <option value="">— Semua Penyakit —</option>
                <?php while ($pf = $penyakit_filter->fetch_assoc()): ?>
                    <option value="<?php echo htmlspecialchars($pf['kode_penyakit']); ?>" <?php echo $filter_penyakit===$pf['kode_penyakit']?'selected':''; ?>>
                        <?php echo htmlspecialchars($pf['kode_penyakit'].' · '.$pf['nama_penyakit']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </form>
    </div>
    <div class="table-wrap">
        <table class="data">
            <thead><tr><th>Kode Aturan</th><th>Penyakit</th><th>Gejala</th><th style="width:100px;">Aksi</th></tr></thead>
            <tbody>
            <?php if ($aturan->num_rows === 0): ?>
                <tr><td colspan="4" class="text-center text-muted" style="padding:2rem;">Belum ada aturan.</td></tr>
            <?php else: while ($a = $aturan->fetch_assoc()): ?>
                <tr data-testid="row-aturan-<?php echo $a['id_aturan']; ?>">
                    <td><span class="badge badge-muted">R<?php echo (int)$a['id_aturan']; ?></span></td>
                    <td><strong><?php echo htmlspecialchars($a['nama_penyakit'] ?? '—'); ?></strong> <span class="text-muted text-sm">(<?php echo htmlspecialchars($a['kode_penyakit']); ?>)</span></td>
                    <td><?php echo htmlspecialchars($a['nama_gejala'] ?? '—'); ?> <span class="text-muted text-sm">(<?php echo htmlspecialchars($a['kode_gejala']); ?>)</span></td>
                    <td><a href="?hapus=<?php echo $a['id_aturan']; ?><?php echo $filter_penyakit?'&filter='.urlencode($filter_penyakit):''; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus aturan ini?');" data-testid="btn-hapus-aturan-<?php echo $a['id_aturan']; ?>"><i class="fa-solid fa-trash"></i></a></td>
                </tr>
            <?php endwhile; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../partials/admin_footer.php'; ?>
