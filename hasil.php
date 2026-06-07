<?php
include 'config.php';
session_start();
$hasil_diagnosa = isset($_SESSION['hasil']) ? $_SESSION['hasil'] : [];
unset($_SESSION['hasil']);
?>
<link rel="stylesheet" href="assets/style.css">
<div class="container">
    <h2>Hasil Diagnosa Anemia</h2>
    <?php if (count($hasil_diagnosa) > 0): 
        foreach ($hasil_diagnosa as $hasil): ?>
        <div class="info">
            <h4><?php echo $hasil['nama_penyakit']; ?> (Kecocokan: <?php echo $hasil['persen']; ?>%)</h4>
            <p><b>Solusi:</b> <?php echo $hasil['solusi']; ?></p>
        </div>
    <?php endforeach;
    else: ?>
        <div class="info">Tidak ditemukan jenis anemia dengan kecocokan minimal 70%.</div>
    <?php endif; ?>
    <div class="info" style="border-color: #e67e22;">
        Untuk kelanjutan dari pemeriksaan ini silakan kepuskesmas atau rumah sakit terdekat.
    </div>
    <a href="index.php">Diagnosa Ulang</a>
</div>
