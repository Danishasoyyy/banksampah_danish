<?php
require_once '../config/auth.php';
wajib_role(['admin']);

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
            q($koneksi, "INSERT INTO sampah (nama_sampah,jenis,harga_per_kg,stok) VALUES (?,?,?,?)", 'ssdd', $nama, $jenis, $harga, $stok);
            flash('Data sampah ditambahkan.');
        } catch (Throwable $e) { flash($e->getMessage(), 'danger'); }
    }
    header('Location: index.php'); exit;
}
$data = rows(q($koneksi, "SELECT * FROM sampah ORDER BY id_sampah"));
include '../config/header.php'; ?>
<?php page_head('🗑️ Data Sampah'); ?>
<?= flash() ?>
<div class="card p-4 mb-4">
  <h5 class="card-h">Tambah Jenis Sampah</h5>
  <form method="post">
    <?= csrf_field() ?>
    <div class="row g-3">
      <div class="col-md-3"><label class="form-label">Nama sampah</label><input name="nama_sampah" class="form-control" required></div>
      <div class="col-md-3"><label class="form-label">Jenis</label><input name="jenis" class="form-control" placeholder="Plastik, Kertas, ..." required></div>
      <div class="col-md-3"><label class="form-label">Harga per kg (Rp)</label><input type="number" name="harga_per_kg" min="0" step="any" class="form-control" required></div>
      <div class="col-md-3"><label class="form-label">Stok awal (kg)</label><input type="number" name="stok" min="0" step="0.01" value="0" class="form-control"></div>
    </div>
    <div class="form-actions"><button class="btn btn-primary">＋ Tambah</button></div>
  </form>
</div>
<div class="card p-4">
  <h5 class="card-h">Daftar Sampah</h5>
  <div class="table-responsive">
  <table class="table table-striped align-middle mb-0">
    <thead><tr><th>#</th><th>Nama</th><th>Jenis</th><th class="text-end">Harga/kg</th><th class="text-end">Stok</th><th class="text-end">Aksi</th></tr></thead>
    <tbody>
    <?php foreach ($data as $r): ?>
      <tr><td><?= $r['id_sampah'] ?></td><td><b><?= h($r['nama_sampah']) ?></b></td><td><?= h($r['jenis']) ?></td>
        <td class="text-end"><?= rupiah($r['harga_per_kg']) ?></td><td class="text-end"><?= kg($r['stok']) ?></td>
        <td><div class="aksi">
          <form method="post" action="hapus.php" onsubmit="return confirm('Hapus data ini?')"><?= csrf_field() ?><input type="hidden" name="id" value="<?= $r['id_sampah'] ?>"><button class="btn btn-sm btn-danger">🗑️ Hapus</button></form>
          <a class="btn btn-sm btn-warning" href="edit.php?id=<?= $r['id_sampah'] ?>">✏️ Edit</a>
        </div></td></tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  </div>
</div>
<?php include '../config/footer.php';
