<?php
session_start();
include '../config.php';
if (!isset($_SESSION['admin'])) { header('Location: login.php'); exit; }

$msg = ''; $msg_type = '';

// Tambah
if (isset($_POST['tambah'])) {
    $kode = trim($_POST['kode_gejala']);
    $nama = trim($_POST['nama_gejala']);
    if ($kode && $nama) {
        $stmt = $conn->prepare("INSERT INTO gejala (kode_gejala, nama_gejala) VALUES (?, ?)");
        $stmt->bind_param("ss", $kode, $nama);
        if ($stmt->execute()) { $msg = "Gejala berhasil ditambahkan."; $msg_type = 'success'; }
        else { $msg = "Gagal menambah: ".$stmt->error; $msg_type = 'danger'; }
        $stmt->close();
    } else { $msg = "Kode dan nama wajib diisi."; $msg_type = 'danger'; }
}

// Hapus
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    // Cek dulu kode untuk hapus aturan terkait
    $g = $conn->query("SELECT kode_gejala FROM gejala WHERE id_gejala=$id")->fetch_assoc();
    if ($g) {
        $kode = $conn->real_escape_string($g['kode_gejala']);
        $conn->query("DELETE FROM aturan WHERE kode_gejala='$kode'");
        $conn->query("DELETE FROM gejala WHERE id_gejala=$id");
        $msg = "Gejala dihapus (beserta aturan terkait)."; $msg_type = 'success';
    }
    header("Location: crud_gejala.php?msg=".urlencode($msg)."&t=".$msg_type);
    exit;
}

// Edit
if (isset($_POST['edit'])) {
    $id   = intval($_POST['id_gejala']);
    $kode = trim($_POST['kode_gejala']);
    $nama = trim($_POST['nama_gejala']);
    $old  = $conn->query("SELECT kode_gejala FROM gejala WHERE id_gejala=$id")->fetch_assoc();
    if ($old && $kode && $nama) {
        $stmt = $conn->prepare("UPDATE gejala SET kode_gejala=?, nama_gejala=? WHERE id_gejala=?");
        $stmt->bind_param("ssi", $kode, $nama, $id);
        if ($stmt->execute()) {
            // Sinkronkan aturan jika kode berubah
            if ($old['kode_gejala'] !== $kode) {
                $stmt2 = $conn->prepare("UPDATE aturan SET kode_gejala=? WHERE kode_gejala=?");
                $stmt2->bind_param("ss", $kode, $old['kode_gejala']);
                $stmt2->execute(); $stmt2->close();
            }
            $msg = "Gejala diperbarui."; $msg_type = 'success';
        } else { $msg = "Gagal update: ".$stmt->error; $msg_type = 'danger'; }
        $stmt->close();
    }
}

if (isset($_GET['msg'])) { $msg = $_GET['msg']; $msg_type = $_GET['t'] ?? 'info'; }

$gejala = $conn->query("SELECT * FROM gejala ORDER BY kode_gejala ASC");

$page_title = 'Manajemen Gejala';
$active     = 'gejala';
include '../partials/admin_header.php';
?>

<div class="page-header">
    <h2>Manajemen Gejala</h2>
    <p class="lead">Kelola daftar gejala anemia yang digunakan dalam form diagnosa.</p>
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
                <input type="text" name="kode_gejala" placeholder="contoh: G28" required maxlength="5" data-testid="input-kode-gejala">
            </div>
            <div class="form-group" style="grid-column: span 2;">
                <label>Nama Gejala</label>
                <input type="text" name="nama_gejala" placeholder="Deskripsi gejala" required data-testid="input-nama-gejala">
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
            <thead><tr><th style="width:120px;">Kode</th><th>Nama Gejala</th><th style="width:200px;">Aksi</th></tr></thead>
            <tbody>
            <?php if ($gejala->num_rows === 0): ?>
                <tr><td colspan="3" class="text-center text-muted" style="padding:2rem;">Belum ada gejala.</td></tr>
            <?php else: while ($g = $gejala->fetch_assoc()): ?>
                <tr data-testid="row-gejala-<?php echo $g['id_gejala']; ?>">
                    <form method="post" style="display:contents;">
                        <input type="hidden" name="id_gejala" value="<?php echo $g['id_gejala']; ?>">
                        <td><input type="text" name="kode_gejala" value="<?php echo htmlspecialchars($g['kode_gejala']); ?>" maxlength="5" required></td>
                        <td><input type="text" name="nama_gejala" value="<?php echo htmlspecialchars($g['nama_gejala']); ?>" required></td>
                        <td class="actions-cell">
                            <button name="edit" type="submit" class="btn btn-secondary btn-sm" data-testid="btn-edit-<?php echo $g['id_gejala']; ?>"><i class="fa-solid fa-pen"></i> Simpan</button>
                            <a href="?hapus=<?php echo $g['id_gejala']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus gejala ini? Aturan terkait juga akan dihapus.');" data-testid="btn-hapus-<?php echo $g['id_gejala']; ?>"><i class="fa-solid fa-trash"></i></a>
                        </td>
                    </form>
                </tr>
            <?php endwhile; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../partials/admin_footer.php'; ?>
