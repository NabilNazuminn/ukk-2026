<?php
// ==========================================
// HISTORI ADMIN - Riwayat semua pengaduan
// ==========================================
include 'akses.php';
include '../config/koneksi.php';

$aktif         = 'histori';
$judul_halaman = 'Histori';
$pesan         = "";

// --- HAPUS SATU PENGADUAN ---
if (isset($_GET['hapus'])) {
    $id = mysqli_real_escape_string($koneksi, $_GET['hapus']);
    mysqli_query($koneksi, "DELETE FROM aspirasi       WHERE id_pelaporan = '$id'");
    mysqli_query($koneksi, "DELETE FROM input_aspirasi WHERE id_pelaporan = '$id'");
    $pesan = "Pengaduan berhasil dihapus.";
}

// --- HAPUS SEMUA ---
if (isset($_POST['hapus_semua'])) {
    mysqli_query($koneksi, "DELETE FROM aspirasi");
    mysqli_query($koneksi, "DELETE FROM input_aspirasi");
    $pesan = "Semua histori berhasil dihapus.";
}

// --- FILTER ---
$f_nis  = $_GET['nis']  ?? "";
$f_stat = $_GET['stat'] ?? "";

$where = "WHERE 1=1";
if ($f_nis  != "") $where .= " AND ia.nis = '"    . mysqli_real_escape_string($koneksi, $f_nis)  . "'";
if ($f_stat != "") $where .= " AND a.status = '" . mysqli_real_escape_string($koneksi, $f_stat) . "'";

$result = mysqli_query($koneksi,
    "SELECT input_aspirasi.id_pelaporan, input_aspirasi.nis, siswa.Kelas, kategori.ket_kategori,
               input_aspirasi.lokasi, input_aspirasi.ket, input_aspirasi.tgl_input,
               aspirasi.status, aspirasi.feedback
        FROM input_aspirasi
        LEFT JOIN siswa ON input_aspirasi.nis = siswa.nis
        JOIN kategori ON input_aspirasi.id_kategori = kategori.id_kategori
        JOIN aspirasi ON input_aspirasi.id_pelaporan = aspirasi.id_pelaporan
        $where
        GROUP BY input_aspirasi.id_pelaporan
        ORDER BY input_aspirasi.id_pelaporan DESC"
);
$data = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Daftar NIS untuk filter
$nis_list = mysqli_fetch_all(mysqli_query($koneksi, "SELECT DISTINCT nis FROM input_aspirasi ORDER BY nis"), MYSQLI_ASSOC);

include 'header.php';
?>

<div class="wrap">
<div class="section">

    <p style="font-size:1.2rem;font-weight:500;margin-bottom:14px;font-family: 'Times New Roman', Times, serif">Histori Semua Pengaduan</p>

    <?php if ($pesan != ""): ?>
        <div class="alert alert-ok">✅ <?= $pesan ?></div>
    <?php endif; ?>

    <!-- TOMBOL HAPUS SEMUA -->
    <div style="display:flex;justify-content:flex-end;margin-bottom:12px">
        <form method="POST" onsubmit="return confirm('Yakin hapus SEMUA histori? Tidak bisa dibatalkan!')">
            <button type="submit" name="hapus_semua" class="btn btn-merah btn-kecil">Hapus Semua Histori</button>
        </form>
    </div>

    <!-- FILTER -->
    <form method="GET">
        <div class="filter-bar">
            <div class="filter-item">
                <label>Filter NIS</label>
                <select name="nis">
                    <option value="">Semua Siswa</option>
                    <?php foreach ($nis_list as $n): ?>
                        <option value="<?= $n['nis'] ?>" <?= $f_nis == $n['nis'] ? 'selected' : '' ?>>
                            NIS: <?= $n['nis'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="filter-item">
                <label>Filter Status</label>
                <select name="stat">
                    <option value="">Semua Status</option>
                    <option value="Menunggu" <?= $f_stat=='Menunggu'?'selected':'' ?>>Menunggu</option>
                    <option value="Proses"   <?= $f_stat=='Proses'  ?'selected':'' ?>>Proses</option>
                    <option value="Selesai"  <?= $f_stat=='Selesai' ?'selected':'' ?>>Selesai</option>
                </select>
            </div>
            <div style="display:flex;gap:8px;align-items:flex-end">
                <button type="submit" class="btn">Filter</button>
                <a href="histori.php" class="btn btn-outline">Reset</a>
            </div>
        </div>
    </form>

    <!-- TABEL -->
    <div class="box" style="padding:0;overflow:auto">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>NIS</th>
                    <th>Kelas</th>
                    <th>Kategori</th>
                    <th>Lokasi</th>
                    <th>Keterangan</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                    <th>Feedback</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($data) == 0): ?>
                    <tr>
                        <td colspan="10" style="text-align:center;padding:24px;color:var(--abu)">
                            Tidak ada data.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($data as $no => $row): ?>
                    <tr>
                        <td><?= $no + 1 ?></td>
                        <td><?= $row['nis'] ?></td>
                        <td><?= $row['Kelas'] ?? '-' ?></td>
                        <td><?= $row['ket_kategori'] ?></td>
                        <td><?= $row['lokasi'] ?></td>
                        <td><?= $row['ket'] ?></td>
                        <td style="font-size:0.76rem;white-space:nowrap"><?= $row['tgl_input'] ?></td>
                        <td>
                            <?php $cls = strtolower($row['status']); ?>
                            <span class="badge badge-<?= $cls ?>"><?= $row['status'] ?></span>
                        </td>
                        <td style="font-size:0.8rem;color:<?= $row['feedback']!=''?'var(--teks)':'var(--abu)' ?>">
                            <?= $row['feedback'] != '' ? $row['feedback'] : '-' ?>
                        </td>
                        <td>
                            <a href="histori.php?hapus=<?= $row['id_pelaporan'] ?>"
                               class="btn btn-merah btn-kecil"
                               onclick="return confirm('Yakin hapus pengaduan ini?')">Hapus</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>
</div>

<footer>Copyright &copy; 2026 Aspirasi Siswa</footer>
</body>
</html>
