<?php
session_start();
?>
<link rel="stylesheet" href="assets/style.css">
<div class="container">
    <h2>Riwayat Diagnosa Anda (Sesi Ini)</h2>
    <a href="index.php">Kembali ke Diagnosa</a>
    <hr>
    <?php
    if (isset($_SESSION['riwayat']) && count($_SESSION['riwayat']) > 0) {
        foreach ($_SESSION['riwayat'] as $row) {
            echo "<div class='info'>";
            echo "<b>Tanggal:</b> " . $row['tanggal'] . "<br>";
            echo "<b>Gejala:</b> " . $row['gejala'] . "<br>";
            echo "<b>Hasil Diagnosa:</b> <br>" . nl2br($row['hasil']);
            echo "</div>";
        }
    } else {
        echo "<div class='info'>Belum ada riwayat diagnosa pada sesi ini.</div>";
    }
    ?>
</div>