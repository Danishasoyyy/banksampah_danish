<?php
require_once '../config/auth.php';
wajib_role(['admin', 'pengepul']);
$id = (int)$_SESSION['id_user'];
$admin = $_SESSION['role'] === 'admin';

if ($admin && $_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_cek();
    $idp = (int)($_POST['id_pengepul'] ?? 0);
    $ids = (int)($_POST['id_sampah'] ?? 0);
    $berat = round(angka($_POST['berat'] ?? 0), 2);
    mysqli_begin_transaction($koneksi);
    try {
        if ($idp <= 0 || $ids <= 0 || $berat <= 0) throw new Exception('Lengkapi data penjualan.');
        if (!row(q($koneksi, "SELECT id_pengepul FROM pengepul WHERE id_pengepul=?", 'i', $idp))) throw new Exception('Pengepul tidak ditemukan.');
        $s = row(q($koneksi, "SELECT harga_per_kg, stok, nama_sampah FROM sampah WHERE id_sampah=? FOR UPDATE", 'i', $ids));
        if (!$s) throw new Exception('Sampah tidak ditemukan.');
        if ($berat > (float)$s['stok']) throw new Exception('Stok ' . $s['nama_sampah'] . ' tidak cukup (tersedia ' . kg($s['stok']) . ').');
        $harga = (float)$s['harga_per_kg'];
        $total = round($berat * $harga, 2);
        q($koneksi, "INSERT INTO setor_pengepul (id_pengepul,id_sampah,berat,harga_per_kg,total) VALUES (?,?,?,?,?)", 'iiddd', $idp, $ids, $berat, $harga, $total);
        q($koneksi, "UPDATE sampah SET stok = stok - ? WHERE id_sampah=?", 'di', $berat, $ids);
        mysqli_commit($koneksi);
        flash('Penjualan dicatat: ' . rupiah($total) . '.');
    } catch (Throwable $e) {
        mysqli_rollback($koneksi);
        flash($e->getMessage(), 'danger');
    }
    header('Location: index.php'); exit;
}

$base = "SELECT sp.*, s.nama_sampah, p.nama_pengepul FROM setor_pengepul sp
         JOIN sampah s ON s.id_sampah=sp.id_sampah JOIN pengepul p ON p.id_pengepul=sp.id_pengepul";
if ($admin) {
    $data = rows(q($koneksi, "$base ORDER BY sp.tanggal DESC"));
    $pengepul = rows(q($koneksi, "SELECT * FROM pengepul ORDER BY nama_pengepul"));
    $sampah = rows(q($koneksi, "SELECT * FROM sampah WHERE stok>0 ORDER BY nama_sampah"));
} else {
    $data = rows(q($koneksi, "$base WHERE p.id_user=? ORDER BY sp.tanggal DESC", 'i', $id));
}
$total = array_sum(array_column($data, 'total'));
include '../config/header.php'; ?>
<?php page_head($admin ? '🚚 Jual Sampah ke Pengepul' : '📦 Riwayat Pembelian', '../dashboard.php',
  '<div class="saldo">Total: ' . rupiah($total) . '</div>'); ?>
<?= flash() ?>
<?php if ($admin): ?>
<div class="card p-4 mb-4">
  <h5 class="card-h">Catat Penjualan</h5>
  <form method="post">
    <?= csrf_field() ?>
    <div class="row g-3">
      <div class="col-md-4"><label class="form-label">Pengepul</label><select name="id_pengepul" class="form-select" required>
        <option value="">-- pilih pengepul --</option>
        <?php foreach ($pengepul as $p): ?><option value="<?= $p['id_pengepul'] ?>"><?= h($p['nama_pengepul']) ?></option><?php endforeach; ?>
      </select></div>
      <div class="col-md-5"><label class="form-label">Sampah</label><select name="id_sampah" class="form-select" required>
        <option value="">-- pilih sampah --</option>
        <?php foreach ($sampah as $s): ?><option value="<?= $s['id_sampah'] ?>"><?= h($s['nama_sampah']) ?> - stok <?= kg($s['stok']) ?> - <?= rupiah($s['harga_per_kg']) ?>/kg</option><?php endforeach; ?>
      </select></div>
      <div class="col-md-3"><label class="form-label">Berat (kg)</label><input type="number" name="berat" min="0.01" step="0.01" class="form-control" required></div>
    </div>
    <?php if (!$pengepul): ?><div class="text-danger mt-3">Belum ada pengepul. Buat user dengan role Pengepul di menu Kelola User.</div><?php endif; ?>
    <div class="form-actions"><button class="btn btn-primary">Catat Penjualan</button></div>
  </form>
</div>
<?php endif; ?>
<div class="card p-4">
  <h5 class="card-h">Riwayat</h5>
  <div class="table-responsive">
  <table class="table table-striped align-middle mb-0">
    <thead><tr><th>#</th><th>Tanggal</th><th>Pengepul</th><th>Sampah</th><th class="text-end">Berat</th><th class="text-end">Harga/kg</th><th class="text-end">Total</th></tr></thead>
    <tbody>
    <?php foreach ($data as $i => $r): ?>
      <tr><td><?= $i + 1 ?></td><td><?= tgl($r['tanggal']) ?></td><td><?= h($r['nama_pengepul']) ?></td><td><?= h($r['nama_sampah']) ?></td>
        <td class="text-end"><?= kg($r['berat']) ?></td><td class="text-end"><?= rupiah($r['harga_per_kg']) ?></td><td class="text-end"><b><?= rupiah($r['total']) ?></b></td></tr>
    <?php endforeach; if (!$data): ?><tr><td colspan="7" class="text-center text-secondary py-4">Belum ada data.</td></tr><?php endif; ?>
    </tbody>
  </table>
  </div>
</div>
<?php include '../config/footer.php';
