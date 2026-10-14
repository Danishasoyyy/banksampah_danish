<?php
require_once '../config/auth.php';
wajib_role(['siswa', 'guru', 'admin']);
$id = (int)$_SESSION['id_user'];
$admin = $_SESSION['role'] === 'admin';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_cek();
    if ($admin) {
        // Verifikasi oleh admin
        $idt = (int)($_POST['id_tarik'] ?? 0);
        $aksi = $_POST['aksi'] ?? '';
        mysqli_begin_transaction($koneksi);
        try {
            $t = row(q($koneksi, "SELECT * FROM tarik_saldo WHERE id_tarik=? FOR UPDATE", 'i', $idt));
            if (!$t || $t['status'] !== 'menunggu') throw new Exception('Permintaan tidak ditemukan / sudah diproses.');
            if ($aksi === 'setuju') {
                if (saldo_user($koneksi, (int)$t['id_user']) < (float)$t['jumlah']) throw new Exception('Saldo pengguna tidak cukup.');
                q($koneksi, "UPDATE tarik_saldo SET status='disetujui' WHERE id_tarik=?", 'i', $idt);
                flash('Penarikan disetujui.');
            } else {
                q($koneksi, "UPDATE tarik_saldo SET status='ditolak' WHERE id_tarik=?", 'i', $idt);
                flash('Penarikan ditolak.');
            }
            mysqli_commit($koneksi);
        } catch (Throwable $e) {
            mysqli_rollback($koneksi);
            flash($e->getMessage(), 'danger');
        }
    } else {
        // Pengajuan oleh siswa/guru
        $jml = round(angka($_POST['jumlah'] ?? 0), 2);
        try {
            $tersedia = saldo_user($koneksi, $id, true);
            if ($jml <= 0) throw new Exception('Jumlah harus lebih dari 0.');
            if ($jml > $tersedia) throw new Exception('Saldo tidak cukup. Saldo tersedia: ' . rupiah($tersedia));
            q($koneksi, "INSERT INTO tarik_saldo (id_user,jumlah) VALUES (?,?)", 'id', $id, $jml);
            flash('Pengajuan penarikan ' . rupiah($jml) . ' dikirim, menunggu verifikasi admin.');
        } catch (Throwable $e) {
            flash($e->getMessage(), 'danger');
        }
    }
    header('Location: index.php'); exit;
}

if ($admin) {
    $data = rows(q($koneksi, "SELECT t.*, u.nama, u.role FROM tarik_saldo t JOIN user u ON u.id_user=t.id_user ORDER BY (t.status='menunggu') DESC, t.tanggal DESC"));
} else {
    $data = rows(q($koneksi, "SELECT * FROM tarik_saldo WHERE id_user=? ORDER BY tanggal DESC", 'i', $id));
    $tersedia = saldo_user($koneksi, $id, true);
}
$badge = ['menunggu' => 'warning text-dark', 'disetujui' => 'success', 'ditolak' => 'danger'];
include '../config/header.php'; ?>
<?php page_head($admin ? '✅ Verifikasi Tarik Saldo' : '💰 Tarik Saldo', '../dashboard.php',
  $admin ? '' : '<div class="saldo">Saldo tersedia: ' . rupiah(max(0, $tersedia)) . '</div>'); ?>
<?= flash() ?>
<?php if (!$admin): ?>
<div class="card p-4 mb-4">
  <h5 class="card-h">Ajukan Penarikan</h5>
  <form method="post">
    <?= csrf_field() ?>
    <div class="input-group input-group-lg">
      <span class="input-group-text">Rp</span>
      <input type="number" name="jumlah" min="1" step="any" class="form-control" placeholder="Jumlah penarikan" required>
      <button class="btn btn-primary">Ajukan Penarikan</button>
    </div>
  </form>
</div>
<?php endif; ?>
<div class="card p-4">
  <h5 class="card-h">Riwayat Penarikan</h5>
  <div class="table-responsive">
  <table class="table table-striped align-middle mb-0">
    <thead><tr><th>#</th><th>Tanggal</th><?php if ($admin): ?><th>Pengguna</th><?php endif; ?><th class="text-end">Jumlah</th><th>Status</th><?php if ($admin): ?><th class="text-end">Aksi</th><?php endif; ?></tr></thead>
    <tbody>
    <?php foreach ($data as $i => $r): ?>
      <tr><td><?= $i + 1 ?></td><td><?= tgl($r['tanggal']) ?></td>
        <?php if ($admin): ?><td><?= h($r['nama']) ?> <small class="text-secondary">(<?= h($r['role']) ?>)</small></td><?php endif; ?>
        <td class="text-end"><b><?= rupiah($r['jumlah']) ?></b></td>
        <td><span class="badge bg-<?= $badge[$r['status']] ?>"><?= h($r['status']) ?></span></td>
        <?php if ($admin): ?><td>
          <?php if ($r['status'] === 'menunggu'): ?>
          <form method="post" class="aksi"><?= csrf_field() ?><input type="hidden" name="id_tarik" value="<?= $r['id_tarik'] ?>">
            <button name="aksi" value="tolak" class="btn btn-sm btn-danger">✖ Tolak</button>
            <button name="aksi" value="setuju" class="btn btn-sm btn-success">✔ Setujui</button></form>
          <?php else: ?><div class="text-end text-secondary">-</div><?php endif; ?></td><?php endif; ?></tr>
    <?php endforeach; if (!$data): ?><tr><td colspan="6" class="text-center text-secondary py-4">Belum ada data.</td></tr><?php endif; ?>
    </tbody>
  </table>
  </div>
</div>
<?php include '../config/footer.php';
