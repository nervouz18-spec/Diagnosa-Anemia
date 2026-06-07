<?php
session_start();
include '../config.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $username = $conn->real_escape_string($_POST["username"]);
    $password = $_POST["password"];
    $user = $conn->query("SELECT * FROM users WHERE username='$username'");
    if ($user && $user->num_rows > 0) {
        $row = $user->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['admin'] = $row['username'];
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "Username tidak ditemukan!";
    }
}
?>
<link rel="stylesheet" href="../assets/style.css">
<div class="container">
    <h2>Login Admin</h2>
    <?php if($error) echo "<div class='info'>$error</div>"; ?>
    <form method="post">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>
</div>