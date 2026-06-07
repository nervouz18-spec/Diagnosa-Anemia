<?php
session_start();
include '../config.php';
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

if(isset($_POST['tambah'])) {
    $kode = $conn->real_escape_string($_POST['kode_penyakit']);
    $nama = $conn->real_escape_string($_POST['nama_penyakit']);
    $solusi = $conn->real_escape_string($_POST['solusi']);
    $conn->query("INSERT INTO penyakit (kode_penyakit, nama_penyakit, solusi) VALUES ('$kode','$nama','$solusi')");
}

if(isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    $conn->query("DELETE FROM penyakit WHERE id_penyakit=$id");
}

if(isset($_POST['edit'])) {
    $id = intval($_POST['id_penyakit']);
    $kode = $conn->real_escape_string($_POST['kode_penyakit']);
    $nama = $conn->real_escape_string($_POST['nama_penyakit']);
    $solusi = $conn->real_escape_string($_POST['solusi']);
    $conn->query("UPDATE penyakit SET kode_penyakit='$kode', nama_penyakit='$nama', solusi='$solusi' WHERE id_penyakit=$id");
}

$penyakit = $conn->query("SELECT * FROM penyakit");
?>

<link rel="stylesheet" href="../assets/style.css">
<div class="container">
    <h2>CRUD Penyakit</h2>
    <a href="dashboard.php">Kembali ke Dashboard</a>
    <hr>
    <h4>Tambah Penyakit</h4>
    <form method="post">
        <input type="text" name="kode_penyakit" placeholder="Kode Penyakit" required>
        <input type="text" name="nama_penyakit" placeholder="Nama Penyakit" required>
        <textarea name="solusi" placeholder="Solusi" required></textarea>
        <button type="submit" name="tambah">Tambah</button>
    </form>
    <h4>Daftar Penyakit</h4>
    <table border="1" cellpadding="6" width="100%">
        <tr><th>Kode</th><th>Nama Penyakit</th><th>Solusi</th><th>Edit</th><th>Hapus</th></tr>
        <?php while($p = $penyakit->fetch_assoc()): ?>
        <tr>
            <form method="post">
                <input type="hidden" name="id_penyakit" value="<?php echo $p['id_penyakit']; ?>">
                <td><input type="text" name="kode_penyakit" value="<?php echo $p['kode_penyakit']; ?>"></td>
                <td><input type="text" name="nama_penyakit" value="<?php echo $p['nama_penyakit']; ?>"></td>
                <td><textarea name="solusi"><?php echo $p['solusi']; ?></textarea></td>
                <td><button name="edit" type="submit">Edit</button></td>
            </form>
            <td><a href="?hapus=<?php echo $p['id_penyakit']; ?>" onclick="return confirm('Yakin hapus?')">Hapus</a></td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>