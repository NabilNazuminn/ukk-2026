<?php
// ==========================================
// INDEX.PHP - Halaman Pengaduan Siswa
// Siswa bisa kirim pengaduan langsung
// dengan memasukkan NIS saja
// ==========================================
session_start();
include './config/koneksi.php';

$pesan = "";
$error = "";

// Ambil semua kategori untuk dropdown
$kategori_query = mysqli_query($koneksi, "SELECT * FROM kategori ORDER BY ket_kategori");

    if (isset($_SESSION['pesan'])) {
        $pesan = $_SESSION['pesan'];
        unset($_SESSION['pesan']);
    }

// --- PROSES KIRIM PENGADUAN ---
if (isset($_POST['kirim'])) {
    $nis         = mysqli_real_escape_string($koneksi, $_POST['nis']);
    $kelas       = mysqli_real_escape_string($koneksi, $_POST['kelas']);
    $id_kategori = mysqli_real_escape_string($koneksi, $_POST['id_kategori']);
    $lokasi      = mysqli_real_escape_string($koneksi, $_POST['lokasi']);
    $ket         = mysqli_real_escape_string($koneksi, $_POST['ket']);

    if (!$nis || !$kelas || !$id_kategori || !$lokasi || !$ket) {
        $error = "Semua field wajib diisi!";
    } else {
        date_default_timezone_set('Asia/Jakarta');
        $tgl = date('d-m-Y H:i:s');

        // Simpan ke tabel siswa
        mysqli_query($koneksi, "INSERT INTO siswa (nis, Kelas) VALUES ('$nis', '$kelas')");

        // Simpan ke tabel input_aspirasi
        mysqli_query($koneksi, "INSERT INTO input_aspirasi (nis, id_kategori, lokasi, ket, tgl_input)
                                VALUES ('$nis', '$id_kategori', '$lokasi', '$ket', '$tgl')");

        // Simpan ke tabel aspirasi 
        $id_pelaporan = mysqli_insert_id($koneksi);
        mysqli_query($koneksi, "INSERT INTO aspirasi (id_pelaporan, id_kategori, status, feedback)
                                VALUES ('$id_pelaporan', '$id_kategori', 'Menunggu', '')");

        $_SESSION['pesan'] = "Pengaduan berhasil dikirim!";
        header("Location: index.php?nis_cek=$nis");
        exit;

    }
}

// --- AMBIL PENGADUAN BERDASARKAN NIS (kalau NIS diisi) ---
$nis_cek     = $_GET['nis_cek'] ?? $_POST['nis'] ?? "";
$pengaduan   = [];
if ($nis_cek != "") {
    $nis_safe  = mysqli_real_escape_string($koneksi, $nis_cek);
    $q = mysqli_query(
        $koneksi,
        "SELECT input_aspirasi.id_pelaporan, input_aspirasi.tgl_input, kategori.ket_kategori,
                input_aspirasi.lokasi, input_aspirasi.ket,
                aspirasi.status, aspirasi.feedback
         FROM input_aspirasi 
         JOIN kategori ON input_aspirasi.id_kategori = kategori.id_kategori
         JOIN aspirasi ON input_aspirasi.id_pelaporan = aspirasi.id_pelaporan
         WHERE input_aspirasi.nis = '$nis_safe'
         ORDER BY input_aspirasi.id_pelaporan DESC"
    );
    $pengaduan = mysqli_fetch_all($q, MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Aspirasi Siswa | Pengaduan Sarana Sekolah</title>
    <link rel="stylesheet" href="./assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans:ital,opsz,wght@0,17..18,400..700;1,17..18,400..700&display=swap" rel="stylesheet">
</head>

<body>

    <!-- HEADER SISWA -->
    <div class="header-siswa">
        <div class="judul">📋 Aspirasi Siswa</div>
        <div class="sub">Aplikasi Pengaduan Sarana Sekolah
        </div>
    </div>

    <!-- KONTEN -->
    <div class="wrap">
        <div class="section">

            <?php if ($pesan != ""): ?>
                <div class="alert alert-ok">✅ <?= $pesan ?></div>
            <?php endif; ?>
            <?php if ($error != ""): ?>
                <div class="alert alert-err">⚠️ <?= $error ?></div>
            <?php endif; ?>

            <div class="grid-2">

                <!-- FORM KIRIM PENGADUAN (kiri) -->
                <div>
                    <div class="box">
                        <div class="box-judul">📝 Buat Pengaduan</div>
                        <form method="POST">
                            <label>NIS (Nomor Induk Siswa)</label>
                            <input type="number" name="nis" placeholder="Masukkan NIS kamu" required
                                value="<?= htmlspecialchars($_POST['nis'] ?? '') ?>">

                            <label>Kelas</label>
                            <select name="kelas" required>
                                <option value="">-- Pilih Kelas --</option>
                                <option value="X RPL 1">X RPL 1</option>
                                <option value="X RPL 2">X RPL 2</option>
                                <option value="XI RPL 1">XI RPL 1</option>
                                <option value="XI RPL 2">XI RPL 2</option>
                                <option value="XI RPL 3">XI RPL 3</option>
                                <option value="XII RPL 1">XII RPL 1</option>
                                <option value="XII RPL 2">XII RPL 2</option>
                                <option value="XII RPL 3">XII RPL 3</option>
                            </select>

                            <label>Kategori</label>
                            <select name="id_kategori" required>
                                <option value="">-- Pilih Kategori --</option>
                                <?php
                                mysqli_data_seek($kategori_query, 0);
                                while ($k = mysqli_fetch_assoc($kategori_query)):
                                ?>
                                    <option value="<?= $k['id_kategori'] ?>"><?= $k['ket_kategori'] ?></option>
                                <?php endwhile; ?>
                            </select>

                            <label>Lokasi</label>
                            <input type="text" name="lokasi" placeholder="Contoh: Toilet Lantai 2, Lab Komputer...">

                            <label>Deskripsi Pengaduan</label>
                            <textarea name="ket" placeholder="Jelaskan masalahnya..."></textarea>

                            <button type="submit" name="kirim" class="btn btn-block">Kirim Pengaduan</button>
                        </form>
                    </div>

                    <!-- FORM CEK PENGADUAN -->
                    <div class="box">
                        <div class="box-judul">🔍 Cek Pengaduan Saya</div>
                        <form method="GET">
                            <label>Masukkan NIS kamu</label>
                            <input type="number" name="nis_cek" placeholder="Contoh: 432352524"
                                value="<?= htmlspecialchars($nis_cek) ?>">
                            <button type="submit" class="btn btn-block">Cek Status</button>
                        </form>
                    </div>
                </div>

                <!-- DAFTAR PENGADUAN (kanan) -->
                <div>
                    <div class="box">
                        <div class="box-judul">
                            📬 Pengaduan Saya
                            <?php if ($nis_cek != ""): ?>
                                — NIS <?= htmlspecialchars($nis_cek) ?>
                            <?php endif; ?>
                        </div>

                        <?php if ($nis_cek == ""): ?>
                            <p style="color:var(--abu);font-size:0.88rem;text-align:center;padding:30px 0">
                                Masukkan NIS di form "Cek Pengaduan" untuk melihat pengaduanmu.
                            </p>

                        <?php elseif (count($pengaduan) == 0): ?>
                            <p style="color:var(--abu);font-size:0.88rem;text-align:center;padding:30px 0">
                                Belum ada pengaduan untuk NIS ini.
                            </p>

                        <?php else: ?>
                            <?php foreach ($pengaduan as $p): ?>
                                <?php
                                // Tentukan warna border berdasarkan status
                                $warna = ['Menunggu' => '#d97706', 'Proses' => '#2563eb', 'Selesai' => '#03c98a'];
                                $w = $warna[$p['status']] ?? '#d97706';
                                ?>
                                <div class="kartu" style="border-left-color:<?= $w ?>">
                                    <div class="k-atas">
                                        <div class="k-kat"><?= $p['ket_kategori'] ?></div>
                                        <?php
                                        $cls = strtolower($p['status']);
                                        echo "<span class='badge badge-$cls'>{$p['status']}</span>";
                                        ?>
                                    </div>
                                    <div class="k-info">
                                        📍 <?= $p['lokasi'] ?> &nbsp;·&nbsp; 🕒 <?= $p['tgl_input'] ?>
                                    </div>
                                    <div class="k-ket"><?= $p['ket'] ?></div>

                                    <?php if (!empty($p['feedback'])): ?>
                                        <div class="k-feedback">
                                            <strong style="color:var(--hijau-tua)">💬 Umpan Balik Admin:</strong>
                                            <?= $p['feedback'] ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

            </div><!-- /grid-2 -->
        </div>
    </div>

    <footer>Copyright &copy; 2026 Aspirasi Siswa — Aplikasi Pengaduan Sarana Sekolah</footer>

<script>
    setTimeout(function() {
        var alert = document.querySelector('.alert-ok');
        if (alert) alert.style.display = 'none';
    }, 2500);
</script>
</body>

</html>