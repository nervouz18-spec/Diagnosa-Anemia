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
// percent = round(match_count / total_gejala * 100), threshold >= 80%
// =====================================================================
$hasil_diagnosa = [];
$penyakit = $conn->query("SELECT * FROM penyakit");

while ($p = $penyakit->fetch_assoc()) {
    // Ambil gejala untuk penyakit ini
    $aturan_gejala = [];
    $gejalanya = $conn->query("SELECT gejala_kode FROM aturan WHERE penyakit_kode = '".$p['kode']."'");
    while ($ag = $gejalanya->fetch_assoc()) $aturan_gejala[] = $ag['gejala_kode'];

    // Hitung jumlah gejala yang cocok
    $match_count = count(array_intersect($selected_gejala, $aturan_gejala));
    $total_gejala = count($aturan_gejala);
    $percent = $total_gejala > 0 ? round($match_count / $total_gejala * 100) : 0;

    // Jika persentase cocok >= 80%, tambahkan ke hasil
    if ($percent >= 80) {
        $hasil_diagnosa[] = [
            'kode_penyakit' => $p['kode'],
            'nama_penyakit' => $p['nama'],
            'deskripsi'     => $p['deskripsi'],
            'persen'        => $percent,
            'match'         => $match_count,
            'total'         => $total_gejala,
            'gejala_cocok'  => array_values(array_intersect($selected_gejala, $aturan_gejala)),
        ];
    }
}
// =====================================================================

// Token sesi untuk pelacakan riwayat user anonim
if (empty($_SESSION['user_token'])) {
    $_SESSION['user_token'] = bin2hex(random_bytes(16));
}
$session_token = $_SESSION['user_token'];

// Simpan ke DB (riwayat) - kolom JSON
$gejala_json = json_encode(array_values($selected_gejala), JSON_UNESCAPED_UNICODE);
$hasil_json  = json_encode(array_map(function($h){
    return [
        'kode'    => $h['kode_penyakit'],
        'nama'    => $h['nama_penyakit'],
        'persen'  => $h['persen'],
        'match'   => $h['match'],
        'total'   => $h['total'],
    ];
}, $hasil_diagnosa), JSON_UNESCAPED_UNICODE);

$stmt = $conn->prepare("INSERT INTO riwayat (tanggal, gejala_dipilih, hasil_dianosa, nama_pasien, umur, jenis_kelamin, session_token) VALUES (NOW(), ?, ?, ?, ?, ?, ?)");
$nama_db = $nama_pasien !== '' ? $nama_pasien : null;
$jk_db   = $jenis_kelamin !== '' ? $jenis_kelamin : null;
$stmt->bind_param("sssiss", $gejala_json, $hasil_json, $nama_db, $umur, $jk_db, $session_token);
$stmt->execute();
$stmt->close();

$_SESSION['hasil']           = $hasil_diagnosa;
$_SESSION['selected_gejala'] = $selected_gejala;
$_SESSION['pasien']          = ['nama' => $nama_db, 'umur' => $umur, 'jenis_kelamin' => $jk_db];
header("Location: hasil.php");
exit;
?>
