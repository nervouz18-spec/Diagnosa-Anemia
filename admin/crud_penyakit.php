<?php
session_start();
include '../config.php';
if (!isset($_SESSION['admin'])) { header('Location: login.php'); exit; }

$msg = ''; $msg_type = '';

if (isset($_POST['tambah'])) {
    $kode   = trim($_POST['kode_penyakit']);
    $nama   = trim($_POST['nama_penyakit']);
    $solusi = trim($_POST['solusi']);
    if ($kode && $nama) {
        $stmt = $conn->prepare("INSERT INTO penyakit (kode_penyakit, nama_penyakit, solusi) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $kode, $nama, $solusi);
        if ($stmt->execute()) { $msg = "Penyakit ditambahkan."; $msg_type = 'success'; }
        else { $msg = "Gagal: ".$stmt->error; $msg_type = 'danger'; }
        $stmt->close();
    }
}

if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    $p = $conn->query("SELECT kode_penyakit FROM penyakit WHERE id_penyakit=$id")->fetch_assoc();
    if ($p) {
        $kode = $conn->real_escape_string($p['kode_penyakit']);
        $conn->query("DELETE FROM aturan WHERE kode_penyakit='$kode'");
        $conn->query("DELETE FROM penyakit WHERE id_penyakit=$id");
        $msg = "Penyakit dihapus."; $msg_type = 'success';
    }
    header("Location: crud_penyakit.php?msg=".urlencode($msg)."&t=".$msg_type); exit;
}

if (isset($_POST['edit'])) {
    $id     = intval($_POST['id_penyakit']);
    $kode   = trim($_POST['kode_penyakit']);
    $nama   = trim($_POST['nama_penyakit']);
    $solusi = trim($_POST['solusi']);
    $old    = $conn->query("SELECT kode_penyakit FROM penyakit WHERE id_penyakit=$id")->fetch_assoc();
    if ($old) {
        $stmt = $conn->prepare("UPDATE penyakit SET kode_penyakit=?, nama_penyakit=?, solusi=? WHERE id_penyakit=?");
        $stmt->bind_param("sssi", $kode, $nama, $solusi, $id);
        if ($stmt->execute()) {
            if ($old['kode_penyakit'] !== $kode) {
                $stmt2 = $conn->prepare("UPDATE aturan SET kode_penyakit=? WHERE kode_penyakit=?");
                $stmt2->bind_param("ss", $kode, $old['kode_penyakit']);
                $stmt2->execute(); $stmt2->close();
            }
            $msg = "Penyakit diperbarui."; $msg_type = 'success';
        } else { $msg = "Gagal update: ".$stmt->error; $msg_type = 'danger'; }
        $stmt->close();
    }
}

if (isset($_GET['msg'])) { $msg = $_GET['msg']; $msg_type = $_GET['t'] ?? 'info'; }

$penyakit = $conn->query("SELECT * FROM penyakit ORDER BY kode_penyakit ASC");

$page_title = 'Manajemen Penyakit';
$active     = 'penyakit';
include '../partials/admin_header.php';
?>

<div class="page-header">
    <h2>Manajemen Penyakit</h2>
    <p class="lead">Kelola jenis-jenis anemia beserta solusi penanganan yang direkomendasikan.</p>
</div>

<?php if ($msg): ?>
    <div class="alert alert-<?php echo htmlspecialchars($msg_type); ?>" data-testid="flash-msg">
        <i class="fa-solid fa-circle-info"></i> <?php echo htmlspecialchars($msg); ?>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-head"><h3><i class="fa-solid fa-plus" style="color:var(--primary);margin-right:.4rem;"></i>Tambah Penyakit</h3></div>
    <form method="post" data-testid="form-tambah-penyakit">
        <div class="form-row">
            <div class="form-group">
                <label>Kode Penyakit</label>
                <input type="text" name="kode_penyakit" placeholder="P09" required maxlength="5" data-testid="input-kode-penyakit">
            </div>
            <div class="form-group" style="grid-column: span 2;">
                <label>Nama Penyakit</label>
                <input type="text" name="nama_penyakit" placeholder="Nama jenis anemia" required data-testid="input-nama-penyakit">
            </div>
        </div>
        <div class="form-group">
            <label>Solusi / Penanganan</label>
            <textarea name="solusi" placeholder="Rekomendasi penanganan singkat..." required data-testid="input-solusi"></textarea>
        </div>
        <button type="submit" name="tambah" class="btn btn-primary" data-testid="btn-tambah-penyakit"><i class="fa-solid fa-plus"></i> Tambah</button>
    </form>
</div>

<div class="card">
    <div class="card-head">
        <h3><i class="fa-solid fa-list" style="color:var(--primary);margin-right:.4rem;"></i>Daftar Penyakit</h3>
        <span class="badge badge-muted"><?php echo $penyakit->num_rows; ?> data</span>
    </div>
    <div class="table-wrap">
        <table class="data">
            <thead><tr><th style="width:120px;">Kode</th><th style="width:220px;">Nama Penyakit</th><th>Solusi</th><th style="width:200px;">Aksi</th></tr></thead>
            <tbody>
            <?php if ($penyakit->num_rows === 0): ?>
                <tr><td colspan="4" class="text-center text-muted" style="padding:2rem;">Belum ada data.</td></tr>
            <?php else: while ($p = $penyakit->fetch_assoc()): ?>
                <tr data-testid="row-penyakit-<?php echo $p['id_penyakit']; ?>">
                    <form method="post" style="display:contents;">
                        <input type="hidden" name="id_penyakit" value="<?php echo $p['id_penyakit']; ?>">
                        <td><input type="text" name="kode_penyakit" value="<?php echo htmlspecialchars($p['kode_penyakit']); ?>" maxlength="5" required></td>
                        <td><input type="text" name="nama_penyakit" value="<?php echo htmlspecialchars($p['nama_penyakit']); ?>" required></td>
                        <td><textarea name="solusi" required><?php echo htmlspecialchars($p['solusi']); ?></textarea></td>
                        <td class="actions-cell">
                            <button name="edit" type="submit" class="btn btn-secondary btn-sm"><i class="fa-solid fa-pen"></i> Simpan</button>
                            <a href="?hapus=<?php echo $p['id_penyakit']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus penyakit ini? Aturan terkait juga dihapus.');"><i class="fa-solid fa-trash"></i></a>
                        </td>
                    </form>
                </tr>
            <?php endwhile; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../partials/admin_footer.php'; ?>
