<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header('Location: login.php');
    exit;
}
?>
<link rel="stylesheet" href="../assets/style.css">
<div class="container">
    <h2>Admin Dashboard</h2>
    <div class="info">
        Login sebagai: <b><?php echo $_SESSION['admin']; ?></b> | <a href="logout.php">Logout</a>
    </div>
    <ul>
        <li><a href="crud_gejala.php">Manajemen Gejala</a></li>
        <li><a href="crud_penyakit.php">Manajemen Penyakit</a></li>
        <li><a href="crud_aturan.php">Manajemen Basis Pengetahuan (Rule)</a></li>
        <li><a href="dashboard.php">Kembali ke Dashboard</a></li>
    </ul>
</div>