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

// Daftar gejala umum (sesuai spesifikasi: G01–G05)
$gejala_umum_codes = ['G01','G02','G03','G04','G05'];

// =====================================================================
// FORWARD CHAINING — Rule Baru:
//   Penyakit terdeteksi apabila:
//     (a) minimal 1 gejala umum (G01-G05) dipilih, DAN
//     (b) minimal 1 gejala spesifik dari penyakit tsb dipilih.
//   Persentase = (jumlah gejala penyakit yang cocok) / (total aturan penyakit) * 100
//   Hasil diurutkan dari persentase tertinggi.
// =====================================================================
$hasil_diagnosa = [];
$penyakit = $conn->query("SELECT * FROM penyakit ORDER BY kode");

while ($p = $penyakit->fetch_assoc()) {
    // Ambil semua aturan gejala untuk penyakit ini
    $all_rules = [];
    $r = $conn->query("SELECT gejala_kode FROM aturan WHERE penyakit_kode = '".$p['kode']."'");
    while ($a = $r->fetch_assoc()) $all_rules[] = $a['gejala_kode'];

    // Pisahkan gejala penyakit ini menjadi umum vs spesifik
    $rules_umum     = array_values(array_intersect($all_rules, $gejala_umum_codes));
    $rules_spesifik = array_values(array_diff($all_rules, $gejala_umum_codes));

    // Cocokkan dengan gejala yang dipilih user
    $match_umum     = array_values(array_intersect($selected_gejala, $rules_umum));
    $match_spesifik = array_values(array_intersect($selected_gejala, $rules_spesifik));

    // Penyakit terdiagnosa hanya jika ≥1 gejala umum DAN ≥1 gejala spesifik cocok
    if (count($match_umum) >= 1 && count($match_spesifik) >= 1) {
        $match_count = count($match_umum) + count($match_spesifik);
        $total       = count($all_rules);
        $percent     = $total > 0 ? round($match_count / $total * 100) : 0;

        $hasil_diagnosa[] = [
            'kode_penyakit'   => $p['kode'],
            'nama_penyakit'   => $p['nama'],
            'deskripsi'       => $p['deskripsi'],
            'persen'          => $percent,
            'match'           => $match_count,
            'total'           => $total,
            'match_umum'      => $match_umum,
            'match_spesifik'  => $match_spesifik,
            'gejala_cocok'    => array_merge($match_umum, $match_spesifik),
        ];
    }
}

// Urutkan hasil dari persentase tertinggi
usort($hasil_diagnosa, fn($a, $b) => $b['persen'] <=> $a['persen']);
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
