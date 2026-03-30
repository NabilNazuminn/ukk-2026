<?php
// ==========================================
// FEEDBACK ADMIN - Balas pengaduan siswa
// ==========================================
include 'akses.php';
include '../config/koneksi.php';

$aktif         = 'feedback';
$judul_halaman = 'Feedback';
$pesan         = "";

// --- PROSES SIMPAN FEEDBACK ---
if (isset($_POST['simpan'])) {
    $id_aspirasi = mysqli_real_escape_string($koneksi, $_POST['id_aspirasi']);
    $status      = mysqli_real_escape_string($koneksi, $_POST['status']);
    $feedback    = mysqli_real_escape_string($koneksi, $_POST['feedback']);

    mysqli_query($koneksi,
        "UPDATE aspirasi SET status='$status', feedback='$feedback' WHERE id_aspirasi='$id_aspirasi'"
    );
    $pesan = "Feedback berhasil disimpan!";
}

// --- AMBIL DETAIL YANG DIPILIH ---
$id_dipilih = $_GET['id'] ?? $_POST['id_pelaporan'] ?? 0;
$dipilih    = null;

if ($id_dipilih > 0) {
    $picked = mysqli_query($koneksi,
        "SELECT aspirasi.id_aspirasi, input_aspirasi.id_pelaporan, input_aspirasi.nis, siswa.Kelas, kategori.ket_kategori,
                input_aspirasi.lokasi, input_aspirasi.ket, input_aspirasi.tgl_input, aspirasi.status, aspirasi.feedback
         FROM aspirasi
         JOIN input_aspirasi ON aspirasi.id_pelaporan = input_aspirasi.id_pelaporan
         LEFT JOIN siswa ON input_aspirasi.nis = siswa.nis
         JOIN kategori ON input_aspirasi.id_kategori = kategori.id_kategori
         WHERE input_aspirasi.id_pelaporan = '$id_dipilih'
         LIMIT 1"
    );
    $dipilih = mysqli_fetch_assoc($picked);
}

// --- DAFTAR SEMUA PENGADUAN (sidebar) ---
$semua = mysqli_query($koneksi,
    "SELECT aspirasi.id_aspirasi, input_aspirasi.id_pelaporan, input_aspirasi.nis, kategori.ket_kategori, input_aspirasi.tgl_input, aspirasi.status
     FROM aspirasi
     JOIN input_aspirasi ON aspirasi.id_pelaporan = input_aspirasi.id_pelaporan
     JOIN kategori ON input_aspirasi.id_kategori = kategori.id_kategori
     ORDER BY input_aspirasi.id_pelaporan DESC"
);
$list = mysqli_fetch_all($semua, MYSQLI_ASSOC);

include 'header.php';
?>

<div class="wrap">
<div class="section">

    <p style="font-size:1.1rem;font-weight:700;margin-bottom:14px">Feedback Pengaduan</p>

    <?php if ($pesan != ""): ?>
        <div class="alert alert-ok">✅ <?= $pesan ?></div>
    <?php endif; ?>

    <div class="feedback-grid">

        <!-- DAFTAR PENGADUAN (kiri) -->
        <div class="feedback-list">
            <div class="fb-header">Pilih Pengaduan</div>
            <?php foreach ($list as $item): ?>
                <a href="feedback.php?id=<?= $item['id_pelaporan'] ?>"
                   class="fb-item <?= $item['id_pelaporan'] == $id_dipilih ? 'aktif' : '' ?>">
                    <div class="fb-nis">NIS <?= $item['nis'] ?> — #<?= $item['id_pelaporan'] ?></div>
                    <div class="fb-sub"><?= $item['ket_kategori'] ?></div>
                    <div class="fb-sub"><?= $item['tgl_input'] ?></div>
                    <div style="margin-top:4px">
                        <?php $cls = strtolower($item['status']); ?>
                        <span class="badge badge-<?= $cls ?>"><?= $item['status'] ?></span>
                    </div>
                </a>
            <?php endforeach; ?>
            <?php if (count($list) == 0): ?>
                <div style="padding:16px;color:var(--abu);font-size:0.85rem">Belum ada pengaduan.</div>
            <?php endif; ?>
        </div>

        <!-- FORM FEEDBACK (kanan) -->
        <div>
            <?php if ($dipilih): ?>
                <div class="box">
                    <div class="box-judul">Detail Pengaduan #<?= $dipilih['id_pelaporan'] ?></div>

                    <!-- Info siswa dan pengaduan -->
                    <table style="margin-bottom:16px;font-size:0.85rem">
                        <tr>
                            <td style="color:var(--abu);padding:3px 16px 3px 0;white-space:nowrap">NIS</td>
                            <td><strong><?= $dipilih['nis'] ?></strong> (<?= $dipilih['Kelas'] ?? '-' ?>)</td>
                        </tr>
                        <tr>
                            <td style="color:var(--abu);padding:3px 16px 3px 0">Kategori</td>
                            <td><?= $dipilih['ket_kategori'] ?></td>
                        </tr>
                        <tr>
                            <td style="color:var(--abu);padding:3px 16px 3px 0">Lokasi</td>
                            <td><?= $dipilih['lokasi'] ?></td>
                        </tr>
                        <tr>
                            <td style="color:var(--abu);padding:3px 16px 3px 0">Keterangan</td>
                            <td><?= $dipilih['ket'] ?></td>
                        </tr>
                        <tr>
                            <td style="color:var(--abu);padding:3px 16px 3px 0">Tanggal</td>
                            <td><?= $dipilih['tgl_input'] ?></td>
                        </tr>
                    </table>

                    <!-- Form feedback -->
                    <form method="POST">
                        <input type="hidden" name="id_aspirasi"  value="<?= $dipilih['id_aspirasi'] ?>">
                        <input type="hidden" name="id_pelaporan" value="<?= $dipilih['id_pelaporan'] ?>">

                        <label>Ubah Status</label>
                        <select name="status">
                            <option value="Menunggu" <?= $dipilih['status']=='Menunggu'?'selected':'' ?>>Menunggu</option>
                            <option value="Proses"   <?= $dipilih['status']=='Proses'  ?'selected':'' ?>>Proses</option>
                            <option value="Selesai"  <?= $dipilih['status']=='Selesai' ?'selected':'' ?>>Selesai</option>
                        </select>

                        <label>Umpan Balik untuk Siswa</label>
                        <textarea name="feedback" placeholder="Tulis balasan di sini..."><?= $dipilih['feedback'] ?></textarea>

                        <button type="submit" name="simpan" class="btn">Simpan Feedback</button>
                    </form>
                </div>
            <?php else: ?>
                <div class="box" style="text-align:center;padding:40px;color:var(--abu)">
                    Pilih pengaduan dari daftar di sebelah kiri.
                </div>
            <?php endif; ?>
        </div>

    </div><!-- /feedback-grid -->

</div>
</div>

<footer>Copyright &copy; 2026 Aspirasi Siswa</footer>
</body>
</html>
