<?php
include 'config.php';
session_start();
if (!isset($_POST['gejala'])) {
    header("Location: index.php");
    exit;
}

$selected_gejala = $_POST['gejala'];

$hasil_diagnosa = [];
$penyakit = $conn->query("SELECT * FROM penyakit");

while ($p = $penyakit->fetch_assoc()) {
    // Ambil gejala untuk penyakit ini
    $aturan_gejala = [];
    $gejalanya = $conn->query("SELECT kode_gejala FROM aturan WHERE kode_penyakit = '".$p['kode_penyakit']."'");
    while($ag = $gejalanya->fetch_assoc()) $aturan_gejala[] = $ag['kode_gejala'];

    // Hitung jumlah gejala yang cocok
    $match_count = count(array_intersect($selected_gejala, $aturan_gejala));
    $total_gejala = count($aturan_gejala);
    $percent = $total_gejala > 0 ? round($match_count / $total_gejala * 100) : 0;

    // Jika persentase cocok >= 80%, tambahkan ke hasil
    if($percent >= 80) {
        $hasil_diagnosa[] = [
            'nama_penyakit' => $p['nama_penyakit'],
            'solusi' => $p['solusi'],
            'persen' => $percent
        ];
    }
}

// Simpan ke riwayat sesi
$gejala_text = implode(", ", $selected_gejala);
$result_text = "";
if(count($hasil_diagnosa) > 0) {
    foreach($hasil_diagnosa as $hasil) {
        $result_text .= $hasil['nama_penyakit'] . " (Kecocokan: {$hasil['persen']}%, Solusi: {$hasil['solusi']})\n";
    }
} else {
    $result_text = "Tidak ditemukan anemia yang cocok minimal 80%.";
}

if (!isset($_SESSION['riwayat'])) $_SESSION['riwayat'] = [];
$_SESSION['riwayat'][] = [
    'tanggal' => date('Y-m-d H:i:s'),
    'gejala' => $gejala_text,
    'hasil' => $result_text
];

$_SESSION['hasil'] = $hasil_diagnosa;
header("Location: hasil.php");
exit;
?>
