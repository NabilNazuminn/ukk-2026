<?php
// ==========================================
// KATEGORI ADMIN - Tambah, Edit & Hapus
// ==========================================
include 'akses.php';
include '../config/koneksi.php';

$aktif         = 'kategori';
$judul_halaman = 'Kategori';
$pesan         = "";
$error         = "";

// --- TAMBAH KATEGORI ---
if (isset($_POST['tambah'])) {
    $nama = mysqli_real_escape_string($koneksi, trim($_POST['ket_kategori']));
    if ($nama == "") {
        $error = "Nama kategori tidak boleh kosong.";
    } else {
        $cek = mysqli_query($koneksi, "SELECT id_kategori FROM kategori WHERE ket_kategori = '$nama'");
        if (mysqli_num_rows($cek) > 0) {
            $error = "Kategori '$nama' sudah ada.";
        } else {
            mysqli_query($koneksi, "INSERT INTO kategori (ket_kategori) VALUES ('$nama')");
            $pesan = "Kategori '$nama' berhasil ditambahkan!";
        }
    }
}

// --- EDIT KATEGORI ---
if (isset($_POST['edit'])) {
    $id   = mysqli_real_escape_string($koneksi, $_POST['id_kategori']);
    $nama = mysqli_real_escape_string($koneksi, trim($_POST['ket_kategori_edit']));
    if ($nama == "") {
        $error = "Nama kategori tidak boleh kosong.";
    } else {
        mysqli_query($koneksi, "UPDATE kategori SET ket_kategori='$nama' WHERE id_kategori='$id'");
        $pesan = "Kategori berhasil diperbarui!";
    }
}

// --- HAPUS KATEGORI ---
if (isset($_GET['hapus'])) {
    $id  = mysqli_real_escape_string($koneksi, $_GET['hapus']);
    $cek = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM input_aspirasi WHERE id_kategori = '$id'");
    $row = mysqli_fetch_assoc($cek);
    if ($row['total'] > 0) {
        $error = "Kategori tidak bisa dihapus karena masih dipakai oleh {$row['total']} pengaduan.";
    } else {
        mysqli_query($koneksi, "DELETE FROM kategori WHERE id_kategori = '$id'");
        $pesan = "Kategori berhasil dihapus.";
    }
}

// ID yang sedang diedit (kalau ada)
$edit_id = $_GET['edit'] ?? 0;
$edit_data = null;
if ($edit_id) {
    $q         = mysqli_query($koneksi, "SELECT * FROM kategori WHERE id_kategori = '$edit_id'");
    $edit_data = mysqli_fetch_assoc($q);
}

// Ambil semua kategori
$result   = mysqli_query($koneksi, "SELECT * FROM kategori ORDER BY ket_kategori");
$kategori = mysqli_fetch_all($result, MYSQLI_ASSOC);

include 'header.php';
?>

<div class="wrap">
<div class="section">

    <p style="font-size:1.2rem;font-weight:500;margin-bottom:16px;font-family: 'Times New Roman', Times, serif">Kelola Kategori</p>

    <?php if ($pesan != ""): ?>
        <div class="alert alert-ok">✅ <?= $pesan ?></div>
    <?php endif; ?>
    <?php if ($error != ""): ?>
        <div class="alert alert-err">⚠️ <?= $error ?></div>
    <?php endif; ?>

    <div style="display:grid;grid-template-columns:1fr 2fr;gap:16px;align-items:start">

        <!-- FORM KIRI: Tambah atau Edit -->
        <div class="box">
            <?php if ($edit_data): ?>
                <!-- MODE EDIT -->
                <div class="box-judul" style="color:var(--ungu)">✏️ Edit Kategori</div>
                <form method="POST">
                    <input type="hidden" name="id_kategori" value="<?= $edit_data['id_kategori'] ?>">
                    <label>Nama Kategori</label>
                    <input type="text" name="ket_kategori_edit"
                           value="<?= htmlspecialchars($edit_data['ket_kategori']) ?>" required>
                    <div style="display:flex;gap:8px">
                        <button type="submit" name="edit" class="btn btn-ungu" style="flex:1">Simpan</button>
                        <a href="kategori.php" class="btn btn-outline" style="flex:1;text-align:center">Batal</a>
                    </div>
                </form>
            <?php else: ?>
                <!-- MODE TAMBAH -->
                <div class="box-judul">➕ Tambah Kategori</div>
                <form method="POST">
                    <label>Nama Kategori</label>
                    <input type="text" name="ket_kategori" placeholder="Contoh: KEBERSIHAN, TOILET..." required>
                    <button type="submit" name="tambah" class="btn btn-block">Tambah</button>
                </form>
            <?php endif; ?>
        </div>

        <!-- TABEL KATEGORI -->
        <div class="box" style="padding:0;overflow:hidden">
            <div style="padding:14px 18px;font-weight:700;font-size:0.9rem;border-bottom:1px solid var(--border);background:linear-gradient(90deg,var(--cyan-muda),var(--ungu-muda))">
                Daftar Kategori (<?= count($kategori) ?>)
            </div>
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Kategori</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($kategori) == 0): ?>
                        <tr>
                            <td colspan="3" style="text-align:center;padding:24px;color:var(--abu)">
                                Belum ada kategori.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($kategori as $no => $k): ?>
                        <tr style="<?= $k['id_kategori'] == $edit_id ? 'background:var(--ungu-muda)' : '' ?>">
                            <td><?= $no + 1 ?></td>
                            <td style="font-weight:<?= $k['id_kategori'] == $edit_id ? '700' : 'normal' ?>">
                                <?= htmlspecialchars($k['ket_kategori']) ?>
                            </td>
                            <td style="display:flex;gap:6px;flex-wrap:wrap">
                                <a href="kategori.php?edit=<?= $k['id_kategori'] ?>"
                                   class="btn btn-ungu btn-kecil">Edit</a>
                                <a href="kategori.php?hapus=<?= $k['id_kategori'] ?>"
                                   class="btn btn-merah btn-kecil"
                                   onclick="return confirm('Yakin hapus kategori ini?')">Hapus</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>

</div>
</div>

<script>
    setTimeout(function() {
        var alert = document.querySelector('.alert-ok');
        if (alert) alert.style.display = 'none';
    }, 2500);
</script>

<footer>Copyright &copy; 2026 Aspirasi Siswa</footer>
</body>
</html>
