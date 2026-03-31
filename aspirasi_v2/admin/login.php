<?php
// ==========================================
// LOGIN ADMIN
// ==========================================
session_start();
include '../config/koneksi.php';

$error = "";

if (isset($_POST['login'])) {
    $user = mysqli_real_escape_string($koneksi, $_POST['username']);
    $pass = mysqli_real_escape_string($koneksi, $_POST['password']);

    $cek = mysqli_query($koneksi, "SELECT * FROM admin WHERE username='$user' AND password='$pass'");

    if (mysqli_num_rows($cek) > 0) {
        $_SESSION['login']    = true;
        $_SESSION['username'] = $user;
        header('Location: dashboard.php');
        exit;
    } else {
        $error = "Username atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans:ital,opsz,wght@0,17..18,400..700;1,17..18,400..700&display=swap" rel="stylesheet">
</head>
<body>
<div class="login-wrap">
    <div class="login-box">
    <img src="../Logo_SMKN5_Banda_Aceh_Large.png" style="width:50px;
        margin-bottom:8px;
        display:block;
        margin-left:auto;
        margin-right:auto;">
        <h2>ASPIRASI SISWA</h2>
        <p class="sub">Login sebagai Admin</p>

        <?php if ($error != ""): ?>
            <div class="alert alert-err"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <label>Username</label>
            <input type="text" name="username" placeholder="Masukkan username" required>

            <label>Password</label>
            <input type="password" name="password" placeholder="Masukkan password" required>

            <button type="submit" name="login" class="btn btn-block" style="margin-top:4px">
                Masuk sebagai Admin
            </button>
        </form>

        <p style="text-align:center;margin-top:14px;font-size:0.82rem;color:var(--abu)">
            <a href="../index.php" style="color:var(--hijau)">← Kembali ke halaman siswa</a>
        </p>
    </div>
</div>
</body>
</html>
