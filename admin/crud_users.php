<?php
session_start();
include '../config.php';
if (!isset($_SESSION['admin'])) { header('Location: login.php'); exit; }

$msg = ''; $msg_type = '';

// Tambah user
if (isset($_POST['tambah'])) {
    $username = trim($_POST['username']);
    $nama     = trim($_POST['nama_lengkap']);
    $password = $_POST['password'];
    if ($username && $password) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (username, password, nama_lengkap, role) VALUES (?, ?, ?, 'admin')");
        $stmt->bind_param("sss", $username, $hash, $nama);
        if ($stmt->execute()) { $msg = "User ditambahkan."; $msg_type = 'success'; }
        else { $msg = "Gagal: ".$stmt->error; $msg_type = 'danger'; }
        $stmt->close();
    } else { $msg = "Username & password wajib diisi."; $msg_type = 'danger'; }
}

// Reset password
if (isset($_POST['reset_pass'])) {
    $id = intval($_POST['id']);
    $new = $_POST['new_password'] ?? '';
    if (strlen($new) >= 4) {
        $hash = password_hash($new, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET password=? WHERE id=?");
        $stmt->bind_param("si", $hash, $id);
        $stmt->execute(); $stmt->close();
        $msg = "Password direset."; $msg_type = 'success';
    } else { $msg = "Password minimal 4 karakter."; $msg_type = 'danger'; }
}

// Edit nama / username
if (isset($_POST['edit'])) {
    $id       = intval($_POST['id']);
    $username = trim($_POST['username']);
    $nama     = trim($_POST['nama_lengkap']);
    $stmt = $conn->prepare("UPDATE users SET username=?, nama_lengkap=? WHERE id=?");
    $stmt->bind_param("ssi", $username, $nama, $id);
    if ($stmt->execute()) { $msg = "User diperbarui."; $msg_type = 'success'; }
    else { $msg = "Gagal: ".$stmt->error; $msg_type = 'danger'; }
    $stmt->close();
}

// Hapus user
if (isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    if ($id === ($_SESSION['admin_id'] ?? 0)) {
        $msg = "Tidak bisa menghapus akun sendiri."; $msg_type = 'danger';
    } else {
        $cnt = (int)$conn->query("SELECT COUNT(*) c FROM users")->fetch_assoc()['c'];
        if ($cnt <= 1) {
            $msg = "Minimal harus ada 1 admin."; $msg_type = 'danger';
        } else {
            $conn->query("DELETE FROM users WHERE id=$id");
            $msg = "User dihapus."; $msg_type = 'success';
        }
    }
    header("Location: crud_users.php?msg=".urlencode($msg)."&t=".$msg_type); exit;
}

if (isset($_GET['msg'])) { $msg = $_GET['msg']; $msg_type = $_GET['t'] ?? 'info'; }

$users = $conn->query("SELECT id, username, nama_lengkap, role, created_at FROM users ORDER BY id ASC");

$page_title = 'Manajemen User';
$active     = 'users';
include '../partials/admin_header.php';
?>

<div class="page-header">
    <h2>Manajemen User Admin</h2>
    <p class="lead">Tambah, ubah, atau hapus akun administrator yang dapat mengakses panel ini.</p>
</div>

<?php if ($msg): ?>
    <div class="alert alert-<?php echo htmlspecialchars($msg_type); ?>"><i class="fa-solid fa-circle-info"></i> <?php echo htmlspecialchars($msg); ?></div>
<?php endif; ?>

<div class="card">
    <div class="card-head"><h3><i class="fa-solid fa-user-plus" style="color:var(--primary);margin-right:.4rem;"></i>Tambah User Admin</h3></div>
    <form method="post" data-testid="form-tambah-user">
        <div class="form-row">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" placeholder="contoh: admin2" required data-testid="input-username">
            </div>
            <div class="form-group">
                <label>Nama Lengkap</label>
                <input type="text" name="nama_lengkap" placeholder="Nama lengkap" data-testid="input-nama">
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Minimal 4 karakter" required data-testid="input-password">
            </div>
        </div>
        <button type="submit" name="tambah" class="btn btn-primary" data-testid="btn-tambah-user"><i class="fa-solid fa-plus"></i> Tambah User</button>
    </form>
</div>

<div class="card">
    <div class="card-head">
        <h3><i class="fa-solid fa-users" style="color:var(--primary);margin-right:.4rem;"></i>Daftar User</h3>
        <span class="badge badge-muted"><?php echo $users->num_rows; ?> akun</span>
    </div>
    <div class="table-wrap">
        <table class="data">
            <thead><tr><th>#</th><th>Username</th><th>Nama Lengkap</th><th>Role</th><th>Dibuat</th><th style="width:260px;">Aksi</th></tr></thead>
            <tbody>
            <?php while ($u = $users->fetch_assoc()):
                $is_me = ((int)$u['id'] === (int)($_SESSION['admin_id'] ?? 0));
            ?>
                <tr data-testid="row-user-<?php echo $u['id']; ?>">
                    <form method="post" style="display:contents;">
                        <input type="hidden" name="id" value="<?php echo $u['id']; ?>">
                        <td>#<?php echo (int)$u['id']; ?></td>
                        <td><input type="text" name="username" value="<?php echo htmlspecialchars($u['username']); ?>" required></td>
                        <td><input type="text" name="nama_lengkap" value="<?php echo htmlspecialchars($u['nama_lengkap'] ?? ''); ?>"></td>
                        <td><span class="badge <?php echo $is_me?'badge-success':'badge-info'; ?>"><?php echo htmlspecialchars($u['role'] ?? 'admin'); ?><?php echo $is_me?' · anda':''; ?></span></td>
                        <td class="text-muted text-sm"><?php echo $u['created_at'] ? date('d M Y', strtotime($u['created_at'])) : '-'; ?></td>
                        <td class="actions-cell">
                            <button type="submit" name="edit" class="btn btn-secondary btn-sm" data-testid="btn-edit-user-<?php echo $u['id']; ?>"><i class="fa-solid fa-pen"></i></button>
                            <button type="button" class="btn btn-secondary btn-sm" onclick="toggleReset(<?php echo $u['id']; ?>);" data-testid="btn-reset-pass-<?php echo $u['id']; ?>"><i class="fa-solid fa-key"></i></button>
                            <?php if (!$is_me): ?>
                                <a href="?hapus=<?php echo $u['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Hapus user ini?');" data-testid="btn-hapus-user-<?php echo $u['id']; ?>"><i class="fa-solid fa-trash"></i></a>
                            <?php endif; ?>
                        </td>
                    </form>
                </tr>
                <tr id="reset-<?php echo $u['id']; ?>" style="display:none;">
                    <td colspan="6" style="background:var(--surface-alt);">
                        <form method="post" style="display:flex;gap:.5rem;align-items:center;flex-wrap:wrap;">
                            <input type="hidden" name="id" value="<?php echo $u['id']; ?>">
                            <strong>Reset Password untuk <?php echo htmlspecialchars($u['username']); ?>:</strong>
                            <input type="password" name="new_password" placeholder="Password baru (min 4)" required style="max-width:240px;">
                            <button type="submit" name="reset_pass" class="btn btn-primary btn-sm"><i class="fa-solid fa-key"></i> Reset</button>
                            <button type="button" class="btn btn-secondary btn-sm" onclick="toggleReset(<?php echo $u['id']; ?>);">Batal</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function toggleReset(id) {
    const row = document.getElementById('reset-'+id);
    row.style.display = row.style.display === 'none' ? 'table-row' : 'none';
}
</script>

<?php include '../partials/admin_footer.php'; ?>
