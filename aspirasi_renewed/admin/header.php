<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $judul_halaman ?? 'Admin' ?> | Aspirasi Siswa</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

    <header>
        <div class="logo">Aspirasi Siswa — Admin</div>
        <nav>
            <a href="dashboard.php" class="<?= ($aktif ?? '') == 'dashboard' ? 'aktif' : '' ?>">Dashboard</a>
            <a href="status.php" class="<?= ($aktif ?? '') == 'status'    ? 'aktif' : '' ?>">Status</a>
            <a href="feedback.php" class="<?= ($aktif ?? '') == 'feedback'  ? 'aktif' : '' ?>">Feedback</a>
            <a href="histori.php" class="<?= ($aktif ?? '') == 'histori'   ? 'aktif' : '' ?>">Histori</a>
            <a href="kategori.php" class="<?= ($aktif ?? '') == 'kategori'  ? 'aktif' : '' ?>">Kategori</a>
            <a href="logout.php" class="logout">Keluar</a>
        </nav>
    </header>