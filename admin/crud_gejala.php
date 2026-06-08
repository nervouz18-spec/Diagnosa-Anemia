<?php
session_start();
include '../config.php';
if (!isset($_SESSION['admin'])) { header('Location: login.php'); exit; }

$msg = ''; $msg_type = '';

if (isset($_POST['tambah'])) {
    $kode      = trim($_POST['kode']);
    $nama      = trim($_POST['nama']);
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    if ($kode && $nama) {
        $stmt = $conn->prepare("INSERT INTO gejala (kode, nama, deskripsi) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $kode, $nama, $deskripsi);
        if ($stmt->execute()) { $msg = "Gejala berhasil ditambahkan."; $msg_type = 'success'; }
        else { $msg = "Gagal menambah: ".$stmt->error; $msg_type = 'danger'; }
        $stmt->close();
    } else { $msg = "Kode dan nama wajib diisi."; $msg_type = 'danger'; }
}

if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    $g = $conn->query("SELECT kode FROM gejala WHERE id=$id")->fetch_assoc();
    if ($g) {
        $conn->query("DELETE FROM gejala WHERE id=$id");
        $msg = "Gejala dihapus (aturan terkait juga dihapus)."; $msg_type = 'success';
    }
    header("Location: crud_gejala.php?msg=".urlencode($msg)."&t=".$msg_type);
    exit;
}

if (isset($_POST['edit'])) {
    $id        = intval($_POST['id']);
    $kode      = trim($_POST['kode']);
    $nama      = trim($_POST['nama']);
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    if ($kode && $nama) {
        $stmt = $conn->prepare("UPDATE gejala SET kode=?, nama=?, deskripsi=? WHERE id=?");
        $stmt->bind_param("sssi", $kode, $nama, $deskripsi, $id);
        if ($stmt->execute()) {
            $msg = "Gejala diperbarui."; $msg_type = 'success';
        } else { $msg = "Gagal update: ".$stmt->error; $msg_type = 'danger'; }
        $stmt->close();
    }
}

if (isset($_GET['msg'])) { $msg = $_GET['msg']; $msg_type = $_GET['t'] ?? 'info'; }

$gejala = $conn->query("SELECT * FROM gejala ORDER BY kode ASC");

$page_title = 'Manajemen Gejala';
$active     = 'gejala';
include '../partials/admin_header.php';
?>

<div class="page-header">
    <h2>Manajemen Gejala</h2>
    <p class="lead">Kelola daftar gejala anemia yang digunakan dalam form diagnosa. <span class="badge badge-warning">G01–G05</span> = gejala umum.</p>
</div>

<?php if ($msg): ?>
    <div class="alert alert-<?php echo htmlspecialchars($msg_type); ?>" data-testid="flash-msg">
        <i class="fa-solid fa-circle-info"></i> <?php echo htmlspecialchars($msg); ?>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-head"><h3><i class="fa-solid fa-plus" style="color:var(--primary);margin-right:.4rem;"></i>Tambah Gejala Baru</h3></div>
    <form method="post" data-testid="form-tambah-gejala">
        <div class="form-row">
            <div class="form-group">
                <label>Kode Gejala</label>
                <input type="text" name="kode" placeholder="contoh: G28" required maxlength="10" data-testid="input-kode-gejala">
            </div>
            <div class="form-group" style="grid-column: span 2;">
                <label>Nama Gejala</label>
                <input type="text" name="nama" placeholder="Deskripsi singkat gejala" required data-testid="input-nama-gejala">
            </div>
        </div>
        <div class="form-group">
            <label>Deskripsi <span class="text-muted text-sm">(opsional, bisa diisi kemudian)</span></label>
            <textarea name="deskripsi" placeholder="Penjelasan detail tentang gejala ini..." data-testid="input-deskripsi-gejala"></textarea>
        </div>
        <button type="submit" name="tambah" class="btn btn-primary" data-testid="btn-tambah-gejala"><i class="fa-solid fa-plus"></i> Tambah</button>
    </form>
</div>

<div class="card">
    <div class="card-head">
        <h3><i class="fa-solid fa-list" style="color:var(--primary);margin-right:.4rem;"></i>Daftar Gejala</h3>
        <span class="badge badge-muted"><?php echo $gejala->num_rows; ?> data</span>
    </div>
    <div class="table-wrap">
        <table class="data">
            <thead><tr><th style="width:120px;">Kode</th><th style="width:220px;">Nama Gejala</th><th>Deskripsi</th><th style="width:90px;">Tipe</th><th style="width:200px;">Aksi</th></tr></thead>
            <tbody>
            <?php if ($gejala->num_rows === 0): ?>
                <tr><td colspan="5" class="text-center text-muted" style="padding:2rem;">Belum ada gejala.</td></tr>
            <?php else: while ($g = $gejala->fetch_assoc()):
                $is_umum = in_array($g['kode'], ['G01','G02','G03','G04','G05']);
            ?>
                <tr data-testid="row-gejala-<?php echo $g['id']; ?>">
                    <form method="post" style="display:contents;">
                        <input type="hidden" name="id" value="<?php echo $g['id']; ?>">
                        <td><input type="text" name="kode" value="<?php echo htmlspecialchars($g['kode']); ?>" maxlength="10" required></td>
                        <td><input type="text" name="nama" value="<?php echo htmlspecialchars($g['nama']); ?>" required></td>
                        <td><textarea name="deskripsi" placeholder="Belum diisi..." style="min-height:60px;"><?php echo htmlspecialchars($g['deskripsi'] ?? ''); ?></textarea></td>
                        <td><span class="badge <?php echo $is_umum?'badge-warning':'badge-success'; ?>"><?php echo $is_umum?'Umum':'Spesifik'; ?></span></td>
                        <td class="actions-cell">
                            <button name="edit" type="submit" class="btn btn-secondary btn-sm" data-testid="btn-edit-<?php echo $g['id']; ?>"><i class="fa-solid fa-pen"></i> Simpan</button>
                            <a href="?hapus=<?php echo $g['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus gejala ini? Aturan terkait juga akan dihapus.');" data-testid="btn-hapus-<?php echo $g['id']; ?>"><i class="fa-solid fa-trash"></i></a>
                        </td>
                    </form>
                </tr>
            <?php endwhile; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../partials/admin_footer.php'; ?>
