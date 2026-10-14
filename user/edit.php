<?php
require_once '../config/auth.php';
wajib_role(['admin']);
$roles = ['admin', 'guru', 'siswa', 'pengepul'];
$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$u = row(q($koneksi, "SELECT * FROM user WHERE id_user=?", 'i', $id));
if (!$u) { flash('User tidak ditemukan.', 'danger'); header('Location: index.php'); exit; }
$p = row(q($koneksi, "SELECT * FROM pengepul WHERE id_user=?", 'i', $id)) ?: ['alamat' => '', 'no_hp' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_cek();
    $nama = trim($_POST['nama'] ?? ''); $username = trim($_POST['username'] ?? '');
    $role = $_POST['role'] ?? ''; $pass = $_POST['password'] ?? '';
    $alamat = trim($_POST['alamat'] ?? ''); $hp = trim($_POST['no_hp'] ?? '');
    mysqli_begin_transaction($koneksi);
    try {
        if ($nama === '' || $username === '') throw new Exception('Nama dan username wajib diisi.');
        if (!in_array($role, $roles, true)) throw new Exception('Role tidak valid.');
        if ($id === (int)$_SESSION['id_user'] && $role !== 'admin') throw new Exception('Anda tidak bisa mengubah role akun Anda sendiri.');
        if ($pass !== '' && strlen($pass) < 6) throw new Exception('Password baru minimal 6 karakter.');
        if (row(q($koneksi, "SELECT id_user FROM user WHERE username=? AND id_user<>?", 'si', $username, $id))) throw new Exception('Username sudah dipakai.');
        if ($pass !== '') {
            q($koneksi, "UPDATE user SET nama=?, username=?, role=?, password=? WHERE id_user=?", 'ssssi', $nama, $username, $role, password_hash($pass, PASSWORD_DEFAULT), $id);
        } else {
            q($koneksi, "UPDATE user SET nama=?, username=?, role=? WHERE id_user=?", 'sssi', $nama, $username, $role, $id);
        }
        if ($role === 'pengepul') {
            if (row(q($koneksi, "SELECT id_pengepul FROM pengepul WHERE id_user=?", 'i', $id))) {
                q($koneksi, "UPDATE pengepul SET nama_pengepul=?, alamat=?, no_hp=? WHERE id_user=?", 'sssi', $nama, $alamat, $hp, $id);
            } else {
                q($koneksi, "INSERT INTO pengepul (nama_pengepul,alamat,no_hp,id_user) VALUES (?,?,?,?)", 'sssi', $nama, $alamat, $hp, $id);
            }
        }
        mysqli_commit($koneksi);
        if ($id === (int)$_SESSION['id_user']) $_SESSION['nama'] = $nama;
        flash('User diperbarui.');
        header('Location: index.php'); exit;
    } catch (Throwable $e) {
        mysqli_rollback($koneksi);
        flash($e->getMessage(), 'danger');
        $u['nama'] = $nama; $u['username'] = $username; $u['role'] = $role;
        $p = ['alamat' => $alamat, 'no_hp' => $hp];
    }
}
include '../config/header.php'; ?>
<?php page_head('✏️ Edit User', 'index.php'); ?>
<?= flash() ?>
<div class="card p-4" style="max-width:620px">
  <form method="post">
    <?= csrf_field() ?><input type="hidden" name="id" value="<?= $id ?>">
    <label class="form-label">Nama</label><input name="nama" class="form-control mb-3" value="<?= h($u['nama']) ?>" required>
    <label class="form-label">Username</label><input name="username" class="form-control mb-3" value="<?= h($u['username']) ?>" required>
    <label class="form-label">Password baru <small class="text-secondary">(kosongkan bila tidak diganti)</small></label><input type="password" name="password" class="form-control mb-3">
    <label class="form-label">Role</label>
    <select name="role" id="role" class="form-select mb-3">
      <?php foreach ($roles as $r): ?><option value="<?= $r ?>" <?= $u['role'] === $r ? 'selected' : '' ?>><?= ucfirst($r) ?></option><?php endforeach; ?>
    </select>
    <div id="boxPengepul" style="display:none">
      <label class="form-label">Alamat Pengepul</label><textarea name="alamat" class="form-control mb-3"><?= h($p['alamat']) ?></textarea>
      <label class="form-label">No. HP</label><input name="no_hp" class="form-control mb-3" value="<?= h($p['no_hp']) ?>">
    </div>
    <div class="form-actions"><a href="index.php" class="btn btn-secondary">Batal</a><button class="btn btn-primary">Simpan Perubahan</button></div>
  </form>
</div>
<script>
const r = document.getElementById('role'), b = document.getElementById('boxPengepul');
function t() { b.style.display = r.value === 'pengepul' ? 'block' : 'none'; }
r.addEventListener('change', t); t();
</script>
<?php include '../config/footer.php';
