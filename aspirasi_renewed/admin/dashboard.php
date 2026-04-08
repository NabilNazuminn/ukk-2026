<?php
include 'akses.php';
include '../config/koneksi.php';

$aktif          = 'dashboard';
$judul_halaman  = 'Dashboard';
include 'header.php';

// --- AMBIL DATA UNTUK FILTER ---
$kategori_query = mysqli_query($koneksi, "SELECT * FROM kategori ORDER BY ket_kategori");

// --- TERIMA NILAI FILTER ---
$f_tgl_dari  = $_GET['tgl_dari']    ?? "";
$f_tgl_sampai = $_GET['tgl_sampai']  ?? "";
$f_kategori  = $_GET['id_kategori'] ?? "";
$f_nis       = $_GET['nis']         ?? "";
$f_kelas     = $_GET['kelas']       ?? "";

// --- BANGUN QUERY DENGAN KONDISI FILTER ---
$where = "WHERE 1=1";
if ($f_nis != "")      $where .= " AND input_aspirasi.nis = '" . mysqli_real_escape_string($koneksi, $f_nis) . "'";
if ($f_kelas != "")    $where .= " AND siswa.Kelas = '" . mysqli_real_escape_string($koneksi, $f_kelas) . "'";
if ($f_kategori != "") $where .= " AND input_aspirasi.id_kategori = '" . mysqli_real_escape_string($koneksi, $f_kategori) . "'";
if ($f_tgl_dari != "") $where .= " AND STR_TO_DATE(input_aspirasi.tgl_input, '%d-%m-%Y') >= '" . mysqli_real_escape_string($koneksi, $f_tgl_dari) . "'";
if ($f_tgl_sampai != "") $where .= " AND STR_TO_DATE(input_aspirasi.tgl_input, '%d-%m-%Y') <= '" . mysqli_real_escape_string($koneksi, $f_tgl_sampai) . "'";

$sql = "SELECT input_aspirasi.id_pelaporan, input_aspirasi.nis, siswa.Kelas, kategori.ket_kategori,
               input_aspirasi.lokasi, input_aspirasi.ket, input_aspirasi.tgl_input,
               aspirasi.status, aspirasi.feedback
        FROM input_aspirasi
        LEFT JOIN siswa ON input_aspirasi.nis = siswa.nis
        JOIN kategori ON input_aspirasi.id_kategori = kategori.id_kategori
        JOIN aspirasi ON input_aspirasi.id_pelaporan = aspirasi.id_pelaporan
        $where
        GROUP BY input_aspirasi.id_pelaporan
        ORDER BY input_aspirasi.id_pelaporan DESC";

$result = mysqli_query($koneksi, $sql);
$data   = mysqli_fetch_all($result, MYSQLI_ASSOC);

// --- STATISTIK ---
$total    = count($data);
$menunggu = count(array_filter($data, fn($r) => $r['status'] == 'Menunggu'));
$proses   = count(array_filter($data, fn($r) => $r['status'] == 'Proses'));
$selesai  = count(array_filter($data, fn($r) => $r['status'] == 'Selesai'));
?>

<div class="wrap">
    <div class="section">

        <p style="font-size:1.3rem;font-weight:500;margin-bottom:16px;letter-spacing:-0.01em;font-family: 'Times New Roman', Times, serif">Dashboard — Semua Pengaduan</p>

        <!-- STATISTIK -->
        <div class="stat-grid">
            <div class="stat-card">
                <div class="s-label">Total</div>
                <div class="s-val"><?= $total ?></div>
            </div>
            <div class="stat-card">
                <div class="s-label">Menunggu</div>
                <div class="s-val" style="color:var(--kuning)"><?= $menunggu ?></div>
            </div>
            <div class="stat-card">
                <div class="s-label">Diproses</div>
                <div class="s-val" style="color:var(--biru)"><?= $proses ?></div>
            </div>
            <div class="stat-card">
                <div class="s-label">Selesai</div>
                <div class="s-val" style="color:var(--hijau)"><?= $selesai ?></div>
            </div>
        </div>

        <!-- FILTER -->
        <form method="GET">
            <div class="filter-bar">
                <div class="filter-item">
                    <label>NIS</label>
                    <input type="text" name="nis" placeholder="Contoh: 43235..." value="<?= htmlspecialchars($f_nis) ?>">
                </div>
                <div class="filter-item">
                    <label>Kelas</label>
                    <select name="kelas">
                        <option value="">Semua Kelas</option>
                        <?php
                        $kelas_list = ['X RPL 1', 'X RPL 2', 'XI RPL 1', 'XI RPL 2', 'XI RPL 3', 'XII RPL 1', 'XII RPL 2', 'XII RPL 3'];
                        foreach ($kelas_list as $kl):
                        ?>
                            <option value="<?= $kl ?>" <?= $f_kelas == $kl ? 'selected' : '' ?>><?= $kl ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="filter-item">
                    <label>Kategori</label>
                    <select name="id_kategori">
                        <option value="">Semua Kategori</option>
                        <?php
                        mysqli_data_seek($kategori_query, 0);
                        while ($k = mysqli_fetch_assoc($kategori_query)):
                        ?>
                            <option value="<?= $k['id_kategori'] ?>" <?= $f_kategori == $k['id_kategori'] ? 'selected' : '' ?>>
                                <?= $k['ket_kategori'] ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="filter-item">
                    <label>Tanggal Dari</label>
                    <input type="date" name="tgl_dari" value="<?= $f_tgl_dari ?>">
                </div>
                <div class="filter-item">
                    <label>Tanggal Sampai</label>
                    <input type="date" name="tgl_sampai" value="<?= $f_tgl_sampai ?>">
                </div>
                <div style="display:flex;gap:8px;align-items:flex-end">
                    <button type="submit" class="btn">Filter</button>
                    <a href="dashboard.php" class="btn btn-outline">Reset</a>
                </div>
            </div>
        </form>

        <!-- TABEL PENGADUAN -->
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
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($data) == 0): ?>
                        <tr>
                            <td colspan="9" style="text-align:center;padding:24px;color:var(--abu)">
                                Tidak ada data pengaduan.
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
                                <td style="white-space:nowrap;font-size:0.78rem"><?= $row['tgl_input'] ?></td>
                                <td>
                                    <?php $cls = strtolower($row['status']); ?>
                                    <span class="badge badge-<?= $cls ?>"><?= $row['status'] ?></span>
                                </td>
                                <td>
                                    <a href="feedback.php?id=<?= $row['id_pelaporan'] ?>" class="btn btn-kecil">Balas</a>
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