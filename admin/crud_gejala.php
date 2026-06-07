<?php
session_start();
include '../config.php';
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

// Proses tambah gejala
if(isset($_POST['tambah'])) {
    $kode = $conn->real_escape_string($_POST['kode_gejala']);
    $nama = $conn->real_escape_string($_POST['nama_gejala']);
    $conn->query("INSERT INTO gejala (kode_gejala, nama_gejala) VALUES ('$kode','$nama')");
}

// Proses hapus
if(isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    $conn->query("DELETE FROM gejala WHERE id_gejala=$id");
}

// Proses edit
if(isset($_POST['edit'])) {
    $id = intval($_POST['id_gejala']);
    $kode = $conn->real_escape_string($_POST['kode_gejala']);
    $nama = $conn->real_escape_string($_POST['nama_gejala']);
    $conn->query("UPDATE gejala SET kode_gejala='$kode', nama_gejala='$nama' WHERE id_gejala=$id");
}

$gejala = $conn->query("SELECT * FROM gejala");
?>

<link rel="stylesheet" href="../assets/style.css">
<div class="container">
    <h2>CRUD Gejala</h2>
    <a href="dashboard.php">Kembali ke Dashboard</a>
    <hr>
    <h4>Tambah Gejala</h4>
    <form method="post">
        <input type="text" name="kode_gejala" placeholder="Kode Gejala" required>
        <input type="text" name="nama_gejala" placeholder="Nama Gejala" required>
        <button type="submit" name="tambah">Tambah</button>
    </form>
    <h4>Daftar Gejala</h4>
    <table border="1" cellpadding="6" width="100%">
        <tr><th>Kode</th><th>Nama Gejala</th><th>Edit</th><th>Hapus</th></tr>
        <?php while($g = $gejala->fetch_assoc()): ?>
        <tr>
            <form method="post">
                <input type="hidden" name="id_gejala" value="<?php echo $g['id_gejala']; ?>">
                <td><input type="text" name="kode_gejala" value="<?php echo $g['kode_gejala']; ?>"></td>
                <td><input type="text" name="nama_gejala" value="<?php echo $g['nama_gejala']; ?>"></td>
                <td><button name="edit" type="submit">Edit</button></td>
            </form>
            <td><a href="?hapus=<?php echo $g['id_gejala']; ?>" onclick="return confirm('Yakin hapus?')">Hapus</a></td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>