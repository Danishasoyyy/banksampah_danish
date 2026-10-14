<?php
session_start();
require_once 'config/koneksi.php';
if (!empty($_SESSION['id_user'])) { header('Location: dashboard.php'); exit; }
$error = '';
if (isset($_POST['login'])) {
    $u = trim($_POST['username'] ?? '');
    $p = $_POST['password'] ?? '';
    $st = mysqli_prepare($koneksi, "SELECT * FROM user WHERE username=? LIMIT 1");
    mysqli_stmt_bind_param($st, 's', $u);
    mysqli_stmt_execute($st);
    $data = mysqli_fetch_assoc(mysqli_stmt_get_result($st));
    $ok = false;
    if ($data) {
        if (password_verify($p, $data['password'])) {
            $ok = true;
        } elseif (hash_equals($data['password'], $p)) {
            // password lama masih teks biasa -> otomatis diubah jadi hash
            $ok = true;
            $hash = password_hash($p, PASSWORD_DEFAULT);
            $up = mysqli_prepare($koneksi, "UPDATE user SET password=? WHERE id_user=?");
            mysqli_stmt_bind_param($up, 'si', $hash, $data['id_user']);
            mysqli_stmt_execute($up);
        }
    }
    if ($ok) {
        session_regenerate_id(true);
        $_SESSION['id_user'] = $data['id_user'];
        $_SESSION['nama']    = $data['nama'];
        $_SESSION['role']    = $data['role'];
        header('Location: dashboard.php'); exit;
    }
    $error = 'Username atau password salah.';
}
?><!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Masuk - Bank Sampah Danish</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link href="<?=BASE_URL?>assets/tema-api.css?v=<?=@filemtime(__DIR__ . '/assets/tema-api.css') ?: 1?>" rel="stylesheet">
</head>
<body class="login-page">
<div class="login-wrap">
  <section class="login-hero">
    <div class="big-fire"><span class="fire-icon">🔥</span></div>
    <h1>Bank Sampah Danish</h1>
    <p>Setor sampah, kumpulkan saldo, dan bantu lingkungan sekolah tetap bersih.</p>
    <?php for ($i = 0; $i < 1; $i++) { echo '<div class="flame-bar" aria-hidden="true">' . str_repeat('<i></i>', 30) . '</div>'; } ?>
  </section>
  <section class="login-form">
    <div class="login-card">
      <h2>Selamat datang 👋</h2>
      <p class="text-secondary mb-4">Silakan masuk untuk melanjutkan.</p>
      <?php if ($error): ?><div class="alert alert-danger"><?=htmlspecialchars($error)?></div><?php endif; ?>
      <form method="post">
        <label class="form-label" for="username">Username</label>
        <input class="form-control form-control-lg mb-3" id="username" name="username" required autofocus autocomplete="username">
        <label class="form-label" for="password">Password</label>
        <div class="pw mb-4">
          <input class="form-control form-control-lg" id="password" type="password" name="password" required autocomplete="current-password">
          <button type="button" class="pw-toggle" id="pwToggle" aria-label="Tampilkan password">👁️</button>
        </div>
        <button class="btn btn-primary btn-lg w-100" name="login">Masuk &rarr;</button>
      </form>
      <div class="demo-label">Akun demo (klik untuk mengisi otomatis)</div>
      <div class="chips">
        <button type="button" class="chip" data-u="admin" data-p="admin123">Admin</button>
        <button type="button" class="chip" data-u="guru" data-p="guru123">Guru</button>
        <button type="button" class="chip" data-u="siswa" data-p="siswa123">Siswa</button>
        <button type="button" class="chip" data-u="pengepul" data-p="pengepul123">Pengepul</button>
      </div>
    </div>
  </section>
</div>
<script>
document.querySelectorAll('.chip').forEach(function (c) {
  c.addEventListener('click', function () {
    document.getElementById('username').value = c.dataset.u;
    document.getElementById('password').value = c.dataset.p;
  });
});
document.getElementById('pwToggle').addEventListener('click', function () {
  var p = document.getElementById('password');
  p.type = p.type === 'password' ? 'text' : 'password';
});
</script>
</body>
</html>
