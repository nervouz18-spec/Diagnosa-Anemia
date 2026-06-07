<?php
include 'config.php';
session_start();
?>
<link rel="stylesheet" href="assets/style.css">
<div class="container">
    <h2>Selamat Datang di Sistem Pakar Diagnosa Anemia</h2>
    <div class="info">
        <b>Navigasi:</b><br>
        <a href="index.php">Diagnosa Anemia</a> |
        <a href="informasi.php">Informasi Anemia</a> |
        <a href="tentang.php">Tentang Sistem</a> |
        <a href="riwayat.php">Riwayat Diagnosa Anda</a> |
        <a href="admin/login.php">Login Admin</a>
    </div>
    <h3>Diagnosa Anemia</h3>
    <form action="proses.php" method="post">
        <h4>Pilih Gejala yang Anda Alami:</h4>
        <div class="gejala-list">
        <?php 
        $gejala = $conn->query("SELECT * FROM gejala");
        while ($row = $gejala->fetch_assoc()): ?>
            <label>
                <input type="checkbox" name="gejala[]" value="<?php echo $row['kode_gejala']; ?>">
                <?php echo $row['nama_gejala']; ?>
            </label>
        <?php endwhile; ?>
        </div>
        <br>
        <button type="submit">Diagnosa</button>
    </form>
</div>