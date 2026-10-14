<?php
require_once '../config/auth.php';
wajib_role(['pengepul', 'admin']);
$id = (int)$_SESSION['id_user'];
$profil = row(q($koneksi, "SELECT * FROM pengepul WHERE id_user=?", 'i', $id));
$sampah = rows(q($koneksi, "SELECT * FROM sampah ORDER BY nama_sampah"));
include '../config/header.php'; ?>
<?php page_head('🚛 Stok Sampah Tersedia', '../dashboard.php', '<a class="btn btn-primary" href="../jual_berasampah/index.php">📦 Riwayat Pembelian</a>'); ?>
<?php if ($profil): ?>
<div class="card p-4 mb-4">
  <b class="fs-5"><?= h($profil['nama_pengepul']) ?></b>
  <div class="text-secondary"><?= h($profil['alamat']) ?> &bull; <?= h($profil['no_hp']) ?></div>
</div>
<?php endif; ?>
<div class="card p-4">
  <p class="text-secondary">Pembelian dicatat oleh admin Bank Sampah. Hubungi admin untuk membeli stok di bawah ini.</p>
  <div class="table-responsive">
  <table class="table table-striped align-middle mb-0">
    <thead><tr><th>Sampah</th><th>Jenis</th><th class="text-end">Harga/kg</th><th class="text-end">Stok</th></tr></thead>
    <tbody>
    <?php foreach ($sampah as $s): ?>
      <tr><td><b><?= h($s['nama_sampah']) ?></b></td><td><?= h($s['jenis']) ?></td><td class="text-end"><?= rupiah($s['harga_per_kg']) ?></td><td class="text-end"><?= kg($s['stok']) ?></td></tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  </div>
</div>
<?php include '../config/footer.php';
