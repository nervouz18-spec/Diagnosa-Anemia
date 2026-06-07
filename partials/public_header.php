<?php
/**
 * Public layout partial — header.
 * Variables expected (optional):
 *   $page_title  : string  – Title for browser tab
 *   $active_nav  : string  – Slug of active nav item (diagnosa|informasi|riwayat|tentang)
 */
if (!isset($page_title)) $page_title = 'Sistem Pakar Diagnosa Anemia';
if (!isset($active_nav))  $active_nav  = '';
?><!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo htmlspecialchars($page_title); ?> · SiPaDiA</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Epilogue:wght@400;500;600;700&family=Work+Sans:wght@400;500;600&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<header class="navbar" data-testid="public-navbar">
    <div class="navbar-inner">
        <a href="index.php" class="brand" data-testid="brand-link">
            <span class="brand-mark"><i class="fa-solid fa-droplet"></i></span>
            <span>SiPaDiA<span style="color:var(--text-muted);font-weight:500;"> · Diagnosa Anemia</span></span>
        </a>
        <button class="menu-toggle" id="navToggle" aria-label="Menu" data-testid="nav-toggle">
            <i class="fa-solid fa-bars"></i>
        </button>
        <nav class="nav-links" id="navLinks">
            <a href="index.php"     class="<?php echo $active_nav==='diagnosa'?'active':''; ?>" data-testid="nav-diagnosa">Diagnosa</a>
            <a href="informasi.php" class="<?php echo $active_nav==='informasi'?'active':''; ?>" data-testid="nav-informasi">Informasi</a>
            <a href="riwayat.php"   class="<?php echo $active_nav==='riwayat'?'active':''; ?>" data-testid="nav-riwayat">Riwayat</a>
            <a href="tentang.php"   class="<?php echo $active_nav==='tentang'?'active':''; ?>" data-testid="nav-tentang">Tentang</a>
            <a href="admin/login.php" class="nav-cta" data-testid="nav-admin-login"><i class="fa-solid fa-lock"></i> Admin</a>
        </nav>
    </div>
</header>
<script>
document.getElementById('navToggle')?.addEventListener('click', () => {
    document.getElementById('navLinks').classList.toggle('open');
});
</script>
