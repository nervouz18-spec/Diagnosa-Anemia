<?php
// Database configuration
$host = "localhost";
$user = "anemia";
$pass = "anemia_pass";
$db   = "db_anemia";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

// Base URL helper (so links work in subfolders / admin)
if (!function_exists('base_url')) {
    function base_url($path = '') {
        $script = $_SERVER['SCRIPT_NAME'] ?? '';
        // If we are inside /admin/, base is one level up
        if (strpos($script, '/admin/') !== false) {
            $prefix = '../';
        } else {
            $prefix = '';
        }
        return $prefix . ltrim($path, '/');
    }
}
?>
