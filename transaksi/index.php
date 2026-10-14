<?php
require_once '../config/auth.php';
wajib_role(['siswa', 'guru', 'admin']);
$id = (int)$_SESSION['id_user'];
$admin = $_SESSION['role'] === 'admin';

if ($admin) {
    $data = rows(q($koneksi, "SELECT t.*, s.nama_sampah, u.nama FROM transaksi t JOIN sampah s ON s.id_sampah=t.id_sampah JOIN user u ON u.id_user=t.id_user ORDER BY t.tanggal DESC"));
} else {
    $data = rows(q($koneksi, "SELECT t.*, s.nama_sampah FROM transaksi t JOIN sampah s ON s.id_sampah=t.id_sampah WHERE t.id_user=? ORDER BY t.tanggal DESC", 'i', $id));
}
$jumlah = array_sum(array_column($data, 'total'));
$berat  = array_sum(array_column($data, 'berat'));
include '../config/header.php'; ?>
<?php page_head('📋 Riwayat Setor Sampah'); ?>
<div class="row g-3 mb-4">
  <div class="col-md-6"><div class="card stat"><div class="ico">⚖️</div><div><div class="n"><?= kg($berat) ?></div><div class="l">Total berat disetor</div></div></div></div>
  <div class="col-md-6"><div class="card stat"><div class="ico">💵</div><div><div class="n"><?= rupiah($jumlah) ?></div><div class="l">Total nilai</div></div></div></div>
</div>
<div class="card p-4">
  <div class="table-responsive">
  <table class="table table-striped align-middle mb-0">
    <thead><tr><th>#</th><th>Tanggal</th><?php if ($admin): ?><th>Penyetor</th><?php endif; ?><th>Sampah</th><th class="text-end">Berat</th><th class="text-end">Harga/kg</th><th class="text-end">Total</th></tr></thead>
    <tbody>
    <?php foreach ($data as $i => $r): ?>
      <tr><td><?= $i + 1 ?></td><td><?= tgl($r['tanggal']) ?></td><?php if ($admin): ?><td><?= h($r['nama']) ?></td><?php endif; ?>
        <td><?= h($r['nama_sampah']) ?></td><td class="text-end"><?= kg($r['berat']) ?></td>
        <td class="text-end"><?= rupiah($r['harga_per_kg']) ?></td><td class="text-end"><b><?= rupiah($r['total']) ?></b></td></tr>
    <?php endforeach; if (!$data): ?><tr><td colspan="7" class="text-center text-secondary py-4">Belum ada data.</td></tr><?php endif; ?>
    </tbody>
  </table>
  </div>
</div>
<?php include '../config/footer.php';
