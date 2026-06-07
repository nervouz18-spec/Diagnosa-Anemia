<?php
/**
 * Admin layout partial — header.
 * Variables expected (optional):
 *   $page_title : string
 *   $active     : string – Slug of active sidebar item
 */
if (!isset($page_title)) $page_title = 'Admin';
if (!isset($active))      $active      = '';
$user = $_SESSION['admin'] ?? 'Admin';
$initial = strtoupper(substr($user, 0, 1));
?><!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($page_title); ?> · SiPaDiA Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Epilogue:wght@400;500;600;700&family=Work+Sans:wght@400;500;600&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
<div class="admin-shell">
    <aside class="sidebar" id="sidebar" data-testid="admin-sidebar">
        <a href="dashboard.php" class="brand">
            <span class="brand-mark"><i class="fa-solid fa-droplet"></i></span>
            <span>SiPaDiA<br><small style="font-weight:500;color:var(--text-muted);font-size:.72rem;letter-spacing:.05em;text-transform:uppercase;">Panel Admin</small></span>
        </a>

        <div class="side-section">Ringkasan</div>
        <a href="dashboard.php"      class="side-link <?php echo $active==='dashboard'?'active':''; ?>" data-testid="side-dashboard"><i class="fa-solid fa-gauge-high"></i> Dashboard</a>

        <div class="side-section">Basis Pengetahuan</div>
        <a href="crud_gejala.php"    class="side-link <?php echo $active==='gejala'?'active':''; ?>"    data-testid="side-gejala"><i class="fa-solid fa-stethoscope"></i> Gejala</a>
        <a href="crud_penyakit.php"  class="side-link <?php echo $active==='penyakit'?'active':''; ?>"  data-testid="side-penyakit"><i class="fa-solid fa-virus"></i> Penyakit</a>
        <a href="crud_aturan.php"    class="side-link <?php echo $active==='aturan'?'active':''; ?>"    data-testid="side-aturan"><i class="fa-solid fa-diagram-project"></i> Aturan</a>

        <div class="side-section">Operasional</div>
        <a href="laporan.php"        class="side-link <?php echo $active==='laporan'?'active':''; ?>"   data-testid="side-laporan"><i class="fa-solid fa-clipboard-list"></i> Laporan Diagnosa</a>
        <a href="crud_users.php"     class="side-link <?php echo $active==='users'?'active':''; ?>"     data-testid="side-users"><i class="fa-solid fa-user-shield"></i> Manajemen User</a>

        <div class="side-section">Lainnya</div>
        <a href="../index.php"       class="side-link" data-testid="side-public"><i class="fa-solid fa-arrow-up-right-from-square"></i> Buka Situs</a>
        <a href="logout.php"         class="side-link" data-testid="side-logout"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
    </aside>

    <main class="admin-main">
        <div class="topbar">
            <div style="display:flex;align-items:center;gap:1rem;">
                <button class="menu-toggle" id="sideToggle" aria-label="Buka menu" data-testid="side-toggle"><i class="fa-solid fa-bars"></i></button>
                <div class="title" data-testid="topbar-title"><?php echo htmlspecialchars($page_title); ?></div>
            </div>
            <div class="user-chip" data-testid="user-chip">
                <span class="avatar"><?php echo htmlspecialchars($initial); ?></span>
                <span><?php echo htmlspecialchars($user); ?></span>
            </div>
        </div>
        <div class="admin-content">
<script>
document.getElementById('sideToggle')?.addEventListener('click', () => {
    document.getElementById('sidebar').classList.toggle('open');
});
</script>
