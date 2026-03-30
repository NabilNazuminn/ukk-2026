<?php
// ==========================================
// STATUS ADMIN - Ubah status pengaduan
// ==========================================
include 'akses.php';
include '../config/koneksi.php';

$aktif         = 'status';
$judul_halaman = 'Status Pengaduan';
$pesan         = "";

// --- PROSES UPDATE STATUS ---
if (isset($_POST['simpan'])) {
    $id_aspirasi = mysqli_real_escape_string($koneksi, $_POST['id_aspirasi']);
    $status      = mysqli_real_escape_string($koneksi, $_POST['status']);

    mysqli_query($koneksi, "UPDATE aspirasi SET status='$status' WHERE id_aspirasi='$id_aspirasi'");
    $pesan = "Status berhasil diperbarui!";
}

// --- AMBIL SEMUA PENGADUAN ---
$result = mysqli_query($koneksi,
    "SELECT a.id_aspirasi, ia.id_pelaporan, ia.nis, k.ket_kategori,
            ia.lokasi, ia.tgl_input, a.status
     FROM aspirasi a
     JOIN input_aspirasi ia ON a.id_pelaporan = ia.id_pelaporan
     JOIN kategori k ON ia.id_kategori = k.id_kategori
     ORDER BY ia.id_pelaporan DESC"
);
$data = mysqli_fetch_all($result, MYSQLI_ASSOC);

include 'header.php';
?>

<div class="wrap">
<div class="section">

    <p style="font-size:1.1rem;font-weight:700;margin-bottom:14px">Ubah Status Pengaduan</p>

    <?php if ($pesan != ""): ?>
        <div class="alert alert-ok">✅ <?= $pesan ?></div>
    <?php endif; ?>

    <div class="box" style="padding:0;overflow:auto">
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>NIS</th>
                    <th>Kategori</th>
                    <th>Lokasi</th>
                    <th>Tanggal</th>
                    <th>Status Sekarang</th>
                    <th>Ganti Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($data) == 0): ?>
                    <tr>
                        <td colspan="7" style="text-align:center;padding:24px;color:var(--abu)">
                            Belum ada pengaduan.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($data as $no => $row): ?>
                    <tr>
                        <td><?= $no + 1 ?></td>
                        <td><?= $row['nis'] ?></td>
                        <td><?= $row['ket_kategori'] ?></td>
                        <td><?= $row['lokasi'] ?></td>
                        <td style="font-size:0.78rem;white-space:nowrap"><?= $row['tgl_input'] ?></td>
                        <td>
                            <?php $cls = strtolower($row['status']); ?>
                            <span class="badge badge-<?= $cls ?>"><?= $row['status'] ?></span>
                        </td>
                        <td>
                            <form method="POST" style="display:flex;gap:6px;align-items:center">
                                <input type="hidden" name="id_aspirasi" value="<?= $row['id_aspirasi'] ?>">
                                <select name="status" style="margin:0;padding:5px 8px;font-size:0.8rem;width:auto">
                                    <option value="Menunggu" <?= $row['status']=='Menunggu'?'selected':'' ?>>Menunggu</option>
                                    <option value="Proses"   <?= $row['status']=='Proses'  ?'selected':'' ?>>Proses</option>
                                    <option value="Selesai"  <?= $row['status']=='Selesai' ?'selected':'' ?>>Selesai</option>
                                </select>
                                <button type="submit" name="simpan" class="btn btn-kecil">Simpan</button>
                            </form>
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
