<?php
session_start();
include '../config.php';
if (!isset($_SESSION['admin'])) { header('Location: login.php'); exit; }

$msg = ''; $msg_type = '';

if (isset($_POST['tambah'])) {
    $kode = trim($_POST['kode']);
    $nama = trim($_POST['nama']);
    if ($kode && $nama) {
        $stmt = $conn->prepare("INSERT INTO gejala (kode, nama) VALUES (?, ?)");
        $stmt->bind_param("ss", $kode, $nama);
        if ($stmt->execute()) { $msg = "Gejala berhasil ditambahkan."; $msg_type = 'success'; }
        else { $msg = "Gagal menambah: ".$stmt->error; $msg_type = 'danger'; }
        $stmt->close();
    } else { $msg = "Kode dan nama wajib diisi."; $msg_type = 'danger'; }
}

if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    $g = $conn->query("SELECT kode FROM gejala WHERE id=$id")->fetch_assoc();
    if ($g) {
        // FK ON DELETE CASCADE akan hapus aturan terkait otomatis
        $conn->query("DELETE FROM gejala WHERE id=$id");
        $msg = "Gejala dihapus (aturan terkait juga dihapus)."; $msg_type = 'success';
    }
    header("Location: crud_gejala.php?msg=".urlencode($msg)."&t=".$msg_type);
    exit;
}

if (isset($_POST['edit'])) {
    $id   = intval($_POST['id']);
    $kode = trim($_POST['kode']);
    $nama = trim($_POST['nama']);
    if ($kode && $nama) {
        $stmt = $conn->prepare("UPDATE gejala SET kode=?, nama=? WHERE id=?");
        $stmt->bind_param("ssi", $kode, $nama, $id);
        if ($stmt->execute()) {
            // FK ON UPDATE CASCADE menyinkronkan aturan otomatis
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
                <input type="text" name="nama" placeholder="Deskripsi gejala" required data-testid="input-nama-gejala">
            </div>
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
            <thead><tr><th style="width:120px;">Kode</th><th>Nama Gejala</th><th style="width:100px;">Tipe</th><th style="width:200px;">Aksi</th></tr></thead>
            <tbody>
            <?php if ($gejala->num_rows === 0): ?>
                <tr><td colspan="4" class="text-center text-muted" style="padding:2rem;">Belum ada gejala.</td></tr>
            <?php else: while ($g = $gejala->fetch_assoc()):
                $is_umum = in_array($g['kode'], ['G01','G02','G03','G04','G05']);
            ?>
                <tr data-testid="row-gejala-<?php echo $g['id']; ?>">
                    <form method="post" style="display:contents;">
                        <input type="hidden" name="id" value="<?php echo $g['id']; ?>">
                        <td><input type="text" name="kode" value="<?php echo htmlspecialchars($g['kode']); ?>" maxlength="10" required></td>
                        <td><input type="text" name="nama" value="<?php echo htmlspecialchars($g['nama']); ?>" required></td>
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
