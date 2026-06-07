<?php
session_start();
include '../config.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $username = trim($_POST["username"] ?? '');
    $password = $_POST["password"] ?? '';

    $stmt = $conn->prepare("SELECT id, username, password, nama_lengkap FROM users WHERE username = ? LIMIT 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($row = $res->fetch_assoc()) {
        if (password_verify($password, $row['password'])) {
            $_SESSION['admin']    = $row['username'];
            $_SESSION['admin_id'] = $row['id'];
            $_SESSION['admin_nama'] = $row['nama_lengkap'];
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "Username tidak ditemukan!";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Admin · SiPaDiA</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Epilogue:wght@400;500;600;700&family=Work+Sans:wght@400;500;600&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
<div class="auth-shell">
    <div class="auth-card" data-testid="login-card">
        <a href="../index.php" class="brand" style="margin-bottom:1.5rem;">
            <span class="brand-mark"><i class="fa-solid fa-droplet"></i></span>
            <span>SiPaDiA<br><small style="font-weight:500;color:var(--text-muted);font-size:.72rem;letter-spacing:.05em;text-transform:uppercase;">Panel Admin</small></span>
        </a>
        <h2 style="margin-bottom:.25rem;">Login Admin</h2>
        <p class="text-muted text-sm" style="margin-bottom:1.5rem;">Masuk untuk mengelola basis pengetahuan dan data sistem.</p>

        <?php if ($error): ?>
            <div class="alert alert-danger" data-testid="login-error">
                <i class="fa-solid fa-circle-exclamation"></i> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form method="post" data-testid="login-form">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" placeholder="admin" required autofocus data-testid="input-username">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="••••••••" required data-testid="input-password">
            </div>
            <button type="submit" class="btn btn-primary btn-block" data-testid="btn-login"><i class="fa-solid fa-arrow-right-to-bracket"></i> Login</button>
        </form>

        <div class="text-center" style="margin-top:1.25rem;">
            <a href="../index.php" class="text-sm text-muted" data-testid="link-home"><i class="fa-solid fa-arrow-left"></i> Kembali ke beranda</a>
        </div>
    </div>
</div>
</body>
</html>
