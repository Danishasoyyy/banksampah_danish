<?php
require_once '../config/auth.php';
wajib_role(['siswa', 'guru']);
$id = (int)$_SESSION['id_user'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_cek();
    $ids   = (int)($_POST['id_sampah'] ?? 0);
    $berat = round(angka($_POST['berat'] ?? 0), 2);
    if ($ids <= 0 || $berat <= 0) {
        flash('Pilih jenis sampah dan isi berat lebih dari 0.', 'danger');
    } else {
        mysqli_begin_transaction($koneksi);
        try {
            $s = row(q($koneksi, "SELECT harga_per_kg FROM sampah WHERE id_sampah=? FOR UPDATE", 'i', $ids));
            if (!$s) throw new Exception('Jenis sampah tidak ditemukan.');
            $harga = (float)$s['harga_per_kg'];
            $total = round($berat * $harga, 2);
            q($koneksi, "INSERT INTO transaksi (id_user,id_sampah,berat,harga_per_kg,total) VALUES (?,?,?,?,?)", 'iiddd', $id, $ids, $berat, $harga, $total);
            q($koneksi, "UPDATE sampah SET stok = stok + ? WHERE id_sampah=?", 'di', $berat, $ids);
            mysqli_commit($koneksi);
            flash('Setoran berhasil! Saldo bertambah ' . rupiah($total) . '.');
        } catch (Throwable $e) {
            mysqli_rollback($koneksi);
            flash('Gagal menyimpan: ' . $e->getMessage(), 'danger');
        }
    }
    header('Location: index.php'); exit;
}

$sampah = rows(q($koneksi, "SELECT * FROM sampah ORDER BY nama_sampah"));
$saldo  = saldo_user($koneksi, $id);
include '../config/header.php'; ?>
<?php page_head('♻️ Setor Sampah', '../dashboard.php',
  '<div class="saldo">Saldo: ' . rupiah(max(0, $saldo)) . '</div><a class="btn btn-outline-primary" href="../transaksi/index.php">📋 Riwayat Setor</a>'); ?>
<?= flash() ?>
<div class="row g-4">
  <div class="col-lg-5">
    <div class="card p-4">
      <h5 class="card-h">Form Setoran</h5>
      <form method="post">
        <?= csrf_field() ?>
        <label class="form-label">Jenis Sampah</label>
        <select name="id_sampah" id="sampah" class="form-select mb-3" required>
          <option value="">-- pilih --</option>
          <?php foreach ($sampah as $s): ?>
            <option value="<?= $s['id_sampah'] ?>" data-harga="<?= $s['harga_per_kg'] ?>"><?= h($s['nama_sampah']) ?> (<?= rupiah($s['harga_per_kg']) ?>/kg)</option>
          <?php endforeach; ?>
        </select>
        <label class="form-label">Berat (kg)</label>
        <input type="number" step="0.01" min="0.01" name="berat" id="berat" class="form-control mb-3" required>
        <div>Perkiraan pendapatan: <b id="est">Rp 0</b></div>
        <div class="form-actions"><button class="btn btn-primary btn-lg">Setor Sekarang</button></div>
      </form>
    </div>
  </div>
  <div class="col-lg-7">
    <div class="card p-4">
      <h5 class="card-h">Daftar Harga</h5>
      <div class="table-responsive">
      <table class="table table-striped mb-0">
        <thead><tr><th>Sampah</th><th>Jenis</th><th class="text-end">Harga/kg</th></tr></thead>
        <tbody>
        <?php foreach ($sampah as $s): ?>
          <tr><td><?= h($s['nama_sampah']) ?></td><td><?= h($s['jenis']) ?></td><td class="text-end"><?= rupiah($s['harga_per_kg']) ?></td></tr>
        <?php endforeach; ?>
        </tbody>
      </table>
      </div>
    </div>
  </div>
</div>
<script>
const sel = document.getElementById('sampah'), b = document.getElementById('berat'), est = document.getElementById('est');
function hitung() {
  const o = sel.options[sel.selectedIndex];
  const harga = parseFloat(o && o.dataset.harga || 0), berat = parseFloat(b.value || 0);
  est.textContent = 'Rp ' + Math.round(harga * berat).toLocaleString('id-ID');
}
sel.addEventListener('change', hitung); b.addEventListener('input', hitung);
</script>
<?php include '../config/footer.php';
