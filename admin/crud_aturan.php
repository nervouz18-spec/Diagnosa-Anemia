<?php
session_start();
include '../config.php';
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}

// Tambah aturan
if(isset($_POST['tambah'])) {
    $penyakit = $conn->real_escape_string($_POST['kode_penyakit']);
    $gejala = $conn->real_escape_string($_POST['kode_gejala']);
    $conn->query("INSERT INTO aturan (kode_penyakit, kode_gejala) VALUES ('$penyakit','$gejala')");
}

// Hapus aturan
if(isset($_GET['hapus'])) {
    $id = intval($_GET['hapus']);
    $conn->query("DELETE FROM aturan WHERE id_aturan=$id");
}

// Fetch data dropdown
$all_penyakit = $conn->query("SELECT * FROM penyakit");
$all_gejala = $conn->query("SELECT * FROM gejala");
$aturan = $conn->query("SELECT a.id_aturan, a.kode_penyakit, p.nama_penyakit, a.kode_gejala, g.nama_gejala 
                        FROM aturan a
                        JOIN penyakit p ON a.kode_penyakit=p.kode_penyakit
                        JOIN gejala g ON a.kode_gejala=g.kode_gejala
                        ORDER BY a.id_aturan DESC");
?>

<link rel="stylesheet" href="../assets/style.css">
<div class="container">
    <h2>CRUD Aturan (Basis Pengetahuan)</h2>
    <a href="dashboard.php">Kembali ke Dashboard</a>
    <hr>
    <h4>Tambah Aturan</h4>
    <form method="post">
        <select name="kode_penyakit" required>
            <option value="">Pilih Penyakit</option>
            <?php while($p = $all_penyakit->fetch_assoc()): ?>
            <option value="<?php echo $p['kode_penyakit']; ?>"><?php echo $p['nama_penyakit']; ?></option>
            <?php endwhile; ?>
        </select>
        <select name="kode_gejala" required>
            <option value="">Pilih Gejala</option>
            <?php while($g = $all_gejala->fetch_assoc()): ?>
            <option value="<?php echo $g['kode_gejala']; ?>"><?php echo $g['nama_gejala']; ?></option>
            <?php endwhile; ?>
        </select>
        <button type="submit" name="tambah">Tambah</button>
    </form>
    <h4>Daftar Aturan</h4>
    <table border="1" cellpadding="6" width="100%">
        <tr><th>Penyakit</th><th>Gejala</th><th>Hapus</th></tr>
        <?php while($a = $aturan->fetch_assoc()): ?>
        <tr>
            <td><?php echo $a['nama_penyakit']; ?> (<?php echo $a['kode_penyakit']; ?>)</td>
            <td><?php echo $a['nama_gejala']; ?> (<?php echo $a['kode_gejala']; ?>)</td>
            <td><a href="?hapus=<?php echo $a['id_aturan']; ?>" onclick="return confirm('Yakin hapus?')">Hapus</a></td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>