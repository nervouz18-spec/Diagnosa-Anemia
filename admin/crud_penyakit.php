<?php
session_start();
include '../config.php';
if (!isset($_SESSION['admin'])) { header('Location: login.php'); exit; }

$msg = ''; $msg_type = '';

if (isset($_POST['tambah'])) {
    $kode      = trim($_POST['kode']);
    $nama      = trim($_POST['nama']);
    $deskripsi = trim($_POST['deskripsi']);
    if ($kode && $nama) {
        $stmt = $conn->prepare("INSERT INTO penyakit (kode, nama, deskripsi) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $kode, $nama, $deskripsi);
        if ($stmt->execute()) { $msg = "Penyakit ditambahkan."; $msg_type = 'success'; }
        else { $msg = "Gagal: ".$stmt->error; $msg_type = 'danger'; }
        $stmt->close();
    }
}

if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    $conn->query("DELETE FROM penyakit WHERE id=$id"); // FK CASCADE menghapus aturan terkait
    $msg = "Penyakit dihapus."; $msg_type = 'success';
    header("Location: crud_penyakit.php?msg=".urlencode($msg)."&t=".$msg_type); exit;
}

if (isset($_POST['edit'])) {
    $id        = intval($_POST['id']);
    $kode      = trim($_POST['kode']);
    $nama      = trim($_POST['nama']);
    $deskripsi = trim($_POST['deskripsi']);
    $stmt = $conn->prepare("UPDATE penyakit SET kode=?, nama=?, deskripsi=? WHERE id=?");
    $stmt->bind_param("sssi", $kode, $nama, $deskripsi, $id);
    if ($stmt->execute()) { $msg = "Penyakit diperbarui."; $msg_type = 'success'; }
    else { $msg = "Gagal update: ".$stmt->error; $msg_type = 'danger'; }
    $stmt->close();
}

if (isset($_GET['msg'])) { $msg = $_GET['msg']; $msg_type = $_GET['t'] ?? 'info'; }

$penyakit = $conn->query("SELECT * FROM penyakit ORDER BY kode ASC");

$page_title = 'Manajemen Penyakit';
$active     = 'penyakit';
include '../partials/admin_header.php';
?>

<div class="page-header">
    <h2>Manajemen Penyakit</h2>
    <p class="lead">Kelola jenis-jenis anemia beserta deskripsi & rekomendasi penanganan.</p>
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
                <input type="text" name="kode" placeholder="P12" required maxlength="10" data-testid="input-kode-penyakit">
            </div>
            <div class="form-group" style="grid-column: span 2;">
                <label>Nama Penyakit</label>
                <input type="text" name="nama" placeholder="Nama jenis anemia" required data-testid="input-nama-penyakit">
            </div>
        </div>
        <div class="form-group">
            <label>Deskripsi</label>
            <textarea name="deskripsi" placeholder="Deskripsi singkat & rekomendasi penanganan..." required data-testid="input-deskripsi"></textarea>
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
            <thead><tr><th style="width:120px;">Kode</th><th style="width:220px;">Nama Penyakit</th><th>Deskripsi</th><th style="width:200px;">Aksi</th></tr></thead>
            <tbody>
            <?php if ($penyakit->num_rows === 0): ?>
                <tr><td colspan="4" class="text-center text-muted" style="padding:2rem;">Belum ada data.</td></tr>
            <?php else: while ($p = $penyakit->fetch_assoc()): ?>
                <tr data-testid="row-penyakit-<?php echo $p['id']; ?>">
                    <form method="post" style="display:contents;">
                        <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
                        <td><input type="text" name="kode" value="<?php echo htmlspecialchars($p['kode']); ?>" maxlength="10" required></td>
                        <td><input type="text" name="nama" value="<?php echo htmlspecialchars($p['nama']); ?>" required></td>
                        <td><textarea name="deskripsi" required><?php echo htmlspecialchars($p['deskripsi']); ?></textarea></td>
                        <td class="actions-cell">
                            <button name="edit" type="submit" class="btn btn-secondary btn-sm"><i class="fa-solid fa-pen"></i> Simpan</button>
                            <a href="?hapus=<?php echo $p['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus penyakit ini? Aturan terkait juga dihapus.');"><i class="fa-solid fa-trash"></i></a>
                        </td>
                    </form>
                </tr>
            <?php endwhile; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../partials/admin_footer.php'; ?>
