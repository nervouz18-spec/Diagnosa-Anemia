<?php
include 'config.php';
session_start();

if (!isset($_POST['gejala']) || !is_array($_POST['gejala']) || count($_POST['gejala']) === 0) {
    header("Location: index.php");
    exit;
}

$selected_gejala = $_POST['gejala'];
$nama_pasien    = trim($_POST['nama_pasien'] ?? '');
$umur           = isset($_POST['umur']) && $_POST['umur'] !== '' ? (int)$_POST['umur'] : null;
$jenis_kelamin  = trim($_POST['jenis_kelamin'] ?? '');

// =====================================================================
// FORWARD CHAINING — logika perhitungan TIDAK DIUBAH
// (match_count / total_gejala * 100, threshold >= 80%)
// =====================================================================
$hasil_diagnosa = [];
$penyakit = $conn->query("SELECT * FROM penyakit");

while ($p = $penyakit->fetch_assoc()) {
    // Ambil gejala untuk penyakit ini
    $aturan_gejala = [];
    $gejalanya = $conn->query("SELECT kode_gejala FROM aturan WHERE kode_penyakit = '".$p['kode_penyakit']."'");
    while ($ag = $gejalanya->fetch_assoc()) $aturan_gejala[] = $ag['kode_gejala'];

    // Hitung jumlah gejala yang cocok
    $match_count = count(array_intersect($selected_gejala, $aturan_gejala));
    $total_gejala = count($aturan_gejala);
    $percent = $total_gejala > 0 ? round($match_count / $total_gejala * 100) : 0;

    // Jika persentase cocok >= 80%, tambahkan ke hasil
    if ($percent >= 80) {
        $hasil_diagnosa[] = [
            'kode_penyakit' => $p['kode_penyakit'],
            'nama_penyakit' => $p['nama_penyakit'],
            'solusi'        => $p['solusi'],
            'persen'        => $percent,
            'match'         => $match_count,
            'total'         => $total_gejala,
            'gejala_cocok'  => array_values(array_intersect($selected_gejala, $aturan_gejala)),
        ];
    }
}
// =====================================================================

// Susun teks ringkas untuk disimpan & ditampilkan
$gejala_text = implode(", ", $selected_gejala);
$result_text = "";
if (count($hasil_diagnosa) > 0) {
    foreach ($hasil_diagnosa as $hasil) {
        $result_text .= $hasil['nama_penyakit'] . " (Kecocokan: {$hasil['persen']}%, Solusi: {$hasil['solusi']})\n";
    }
} else {
    $result_text = "Tidak ditemukan jenis anemia yang cocok minimal 80%.";
}

// Token sesi untuk pelacakan riwayat user anonim
if (empty($_SESSION['user_token'])) {
    $_SESSION['user_token'] = bin2hex(random_bytes(16));
}
$session_token = $_SESSION['user_token'];

// Simpan ke DB (riwayat)
$stmt = $conn->prepare("INSERT INTO riwayat (user_id, tanggal, gejala, hasil, nama_pasien, umur, jenis_kelamin, session_token) VALUES (NULL, NOW(), ?, ?, ?, ?, ?, ?)");
$nama_db = $nama_pasien !== '' ? $nama_pasien : null;
$jk_db   = $jenis_kelamin !== '' ? $jenis_kelamin : null;
$stmt->bind_param("sssiss", $gejala_text, $result_text, $nama_db, $umur, $jk_db, $session_token);
$stmt->execute();
$stmt->close();

// Backward-compat: simpan di session juga (untuk halaman riwayat fallback)
if (!isset($_SESSION['riwayat'])) $_SESSION['riwayat'] = [];
$_SESSION['riwayat'][] = [
    'tanggal' => date('Y-m-d H:i:s'),
    'gejala'  => $gejala_text,
    'hasil'   => $result_text
];

$_SESSION['hasil']           = $hasil_diagnosa;
$_SESSION['selected_gejala'] = $selected_gejala;
$_SESSION['pasien']          = ['nama' => $nama_db, 'umur' => $umur, 'jenis_kelamin' => $jk_db];
header("Location: hasil.php");
exit;
?>
