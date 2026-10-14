<?php
require_once '../config/auth.php';
wajib_role(['admin']);
$data = rows(q($koneksi, "SELECT id_user, nama, username, role FROM user ORDER BY id_user"));
$warna = ['admin' => 'dark', 'guru' => 'primary', 'siswa' => 'success', 'pengepul' => 'warning text-dark'];
include '../config/header.php'; ?>
<?php page_head('👥 Kelola User', '../dashboard.php', '<a href="tambah.php" class="btn btn-primary">＋ Tambah User</a>'); ?>
<?= flash() ?>
<div class="card p-4">
  <div class="table-responsive">
  <table class="table table-striped align-middle mb-0">
    <thead><tr><th>#</th><th>Nama</th><th>Username</th><th>Role</th><th class="text-end">Aksi</th></tr></thead>
    <tbody>
    <?php foreach ($data as $r): ?>
      <tr><td><?= $r['id_user'] ?></td><td><b><?= h($r['nama']) ?></b></td><td><?= h($r['username']) ?></td>
        <td><span class="badge bg-<?= $warna[$r['role']] ?>"><?= h($r['role']) ?></span></td>
        <td><div class="aksi">
        <?php if ((int)$r['id_user'] !== (int)$_SESSION['id_user']): ?>
          <form method="post" action="hapus.php" onsubmit="return confirm('Hapus user ini beserta riwayat setor & penarikannya?')">
            <?= csrf_field() ?><input type="hidden" name="id" value="<?= $r['id_user'] ?>"><button class="btn btn-sm btn-danger">🗑️ Hapus</button></form>
        <?php endif; ?>
          <a class="btn btn-sm btn-warning" href="edit.php?id=<?= $r['id_user'] ?>">✏️ Edit</a>
        </div></td></tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  </div>
</div>
<?php include '../config/footer.php';
