<?php
require_once '../config/auth.php';
wajib_role(['admin']);
$roles = ['admin', 'guru', 'siswa', 'pengepul'];
$d = ['nama' => '', 'username' => '', 'role' => 'siswa', 'alamat' => '', 'no_hp' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_cek();
    foreach ($d as $k => $v) $d[$k] = trim($_POST[$k] ?? '');
    $pass = $_POST['password'] ?? '';
    mysqli_begin_transaction($koneksi);
    try {
        if ($d['nama'] === '' || $d['username'] === '') throw new Exception('Nama dan username wajib diisi.');
        if (!in_array($d['role'], $roles, true)) throw new Exception('Role tidak valid.');
        if (strlen($pass) < 6) throw new Exception('Password minimal 6 karakter.');
        if (row(q($koneksi, "SELECT id_user FROM user WHERE username=?", 's', $d['username']))) throw new Exception('Username sudah dipakai.');
        $hash = password_hash($pass, PASSWORD_DEFAULT);
        q($koneksi, "INSERT INTO user (nama,username,password,role) VALUES (?,?,?,?)", 'ssss', $d['nama'], $d['username'], $hash, $d['role']);
        $idu = mysqli_insert_id($koneksi);
        if ($d['role'] === 'pengepul') {
            q($koneksi, "INSERT INTO pengepul (nama_pengepul,alamat,no_hp,id_user) VALUES (?,?,?,?)", 'sssi', $d['nama'], $d['alamat'], $d['no_hp'], $idu);
        }
        mysqli_commit($koneksi);
        flash('User berhasil ditambahkan.');
        header('Location: index.php'); exit;
    } catch (Throwable $e) {
        mysqli_rollback($koneksi);
        flash($e->getMessage(), 'danger');
    }
}
include '../config/header.php'; ?>
<?php page_head('＋ Tambah User', 'index.php'); ?>
<?= flash() ?>
<div class="card p-4" style="max-width:620px">
  <form method="post">
    <?= csrf_field() ?>
    <label class="form-label">Nama</label><input name="nama" class="form-control mb-3" value="<?= h($d['nama']) ?>" required>
    <label class="form-label">Username</label><input name="username" class="form-control mb-3" value="<?= h($d['username']) ?>" required>
    <label class="form-label">Password</label><input type="password" name="password" class="form-control mb-3" minlength="6" required>
    <label class="form-label">Role</label>
    <select name="role" id="role" class="form-select mb-3">
      <?php foreach ($roles as $r): ?><option value="<?= $r ?>" <?= $d['role'] === $r ? 'selected' : '' ?>><?= ucfirst($r) ?></option><?php endforeach; ?>
    </select>
    <div id="boxPengepul" style="display:none">
      <label class="form-label">Alamat Pengepul</label><textarea name="alamat" class="form-control mb-3"><?= h($d['alamat']) ?></textarea>
      <label class="form-label">No. HP</label><input name="no_hp" class="form-control mb-3" value="<?= h($d['no_hp']) ?>">
    </div>
    <div class="form-actions"><a href="index.php" class="btn btn-secondary">Batal</a><button class="btn btn-primary">Simpan User</button></div>
  </form>
</div>
<script>
const r = document.getElementById('role'), b = document.getElementById('boxPengepul');
function t() { b.style.display = r.value === 'pengepul' ? 'block' : 'none'; }
r.addEventListener('change', t); t();
</script>
<?php include '../config/footer.php';
