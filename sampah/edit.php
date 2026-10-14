<?php
require_once '../config/auth.php';
wajib_role(['admin']);
$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$r = row(q($koneksi, "SELECT * FROM sampah WHERE id_sampah=?", 'i', $id));
if (!$r) { flash('Data tidak ditemukan.', 'danger'); header('Location: index.php'); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_cek();
    $nama = trim($_POST['nama_sampah'] ?? '');
    $jenis = trim($_POST['jenis'] ?? '');
    $harga = round(angka($_POST['harga_per_kg'] ?? 0), 2);
    $stok = round(angka($_POST['stok'] ?? 0), 2);
    if ($nama === '' || $jenis === '' || $harga < 0 || $stok < 0) {
        flash('Lengkapi data dengan benar.', 'danger');
    } else {
        try {
            q($koneksi, "UPDATE sampah SET nama_sampah=?, jenis=?, harga_per_kg=?, stok=? WHERE id_sampah=?", 'ssddi', $nama, $jenis, $harga, $stok, $id);
            flash('Data sampah diperbarui.');
            header('Location: index.php'); exit;
        } catch (Throwable $e) { flash($e->getMessage(), 'danger'); }
    }
    header('Location: edit.php?id=' . $id); exit;
}
include '../config/header.php'; ?>
<?php page_head('✏️ Edit Data Sampah', 'index.php'); ?>
<?= flash() ?>
<div class="card p-4" style="max-width:620px">
  <form method="post">
    <?= csrf_field() ?><input type="hidden" name="id" value="<?= $id ?>">
    <label class="form-label">Nama Sampah</label><input name="nama_sampah" class="form-control mb-3" value="<?= h($r['nama_sampah']) ?>" required>
    <label class="form-label">Jenis</label><input name="jenis" class="form-control mb-3" value="<?= h($r['jenis']) ?>" required>
    <label class="form-label">Harga per kg (Rp)</label><input type="number" name="harga_per_kg" min="0" step="any" class="form-control mb-3" value="<?= h($r['harga_per_kg']) ?>" required>
    <label class="form-label">Stok (kg)</label><input type="number" name="stok" min="0" step="0.01" class="form-control" value="<?= h($r['stok']) ?>" required>
    <div class="form-actions"><a href="index.php" class="btn btn-secondary">Batal</a><button class="btn btn-primary">Simpan Perubahan</button></div>
  </form>
</div>
<?php include '../config/footer.php';
