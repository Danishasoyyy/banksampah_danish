<?php
$role = $_SESSION['role'] ?? '';
$menu = [['dashboard.php', '🏠', 'Dashboard', 'dashboard']];
if (in_array($role, ['siswa', 'guru'], true)) {
    $menu[] = ['setor/index.php', '♻️', 'Setor Sampah', 'setor'];
    $menu[] = ['tarik_saldo/index.php', '💰', 'Tarik Saldo', 'tarik_saldo'];
    $menu[] = ['transaksi/index.php', '📋', 'Riwayat Setor', 'transaksi'];
} elseif ($role === 'admin') {
    $menu[] = ['user/index.php', '👥', 'Kelola User', 'user'];
    $menu[] = ['sampah/index.php', '🗑️', 'Data Sampah', 'sampah'];
    $menu[] = ['jual_berasampah/index.php', '🚚', 'Jual ke Pengepul', 'jual_berasampah'];
    $menu[] = ['tarik_saldo/index.php', '✅', 'Verifikasi Tarik', 'tarik_saldo'];
    $menu[] = ['transaksi/index.php', '📋', 'Riwayat Setor', 'transaksi'];
} elseif ($role === 'pengepul') {
    $menu[] = ['pengepul/index.php', '🚛', 'Stok Sampah', 'pengepul'];
    $menu[] = ['jual_berasampah/index.php', '📦', 'Riwayat Pembelian', 'jual_berasampah'];
}
$script = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
$aktif = function ($key) use ($script) {
    return basename($script) === 'dashboard.php' ? $key === 'dashboard' : strpos($script, '/' . $key . '/') !== false;
};
$judulBar = 'Bank Sampah Danish';
foreach ($menu as $m) if ($aktif($m[3])) $judulBar = $m[2];
$cssv = @filemtime(dirname(__DIR__) . '/assets/tema-api.css') ?: 1;
?><!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?=h($judulBar)?> - Bank Sampah Danish</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link href="<?=BASE_URL?>assets/tema-api.css?v=<?=$cssv?>" rel="stylesheet">
</head>
<body>
<div class="side-overlay" id="overlay"></div>
<div class="app">
  <aside class="sidebar" id="sidebar">
    <div class="brand"><span class="fire-icon">🔥</span><div><b>Bank Sampah</b><small>Danish</small></div></div>
    <nav class="side-menu">
      <?php foreach ($menu as $m): ?>
        <a href="<?=BASE_URL . $m[0]?>" class="<?=$aktif($m[3]) ? 'active' : ''?>"><span class="ico"><?=$m[1]?></span><?=h($m[2])?></a>
      <?php endforeach; ?>
    </nav>
    <div class="side-user">
      <div class="nama"><?=h($_SESSION['nama'] ?? '')?></div>
      <div class="role"><?=h($role)?></div>
      <a class="btn-logout" href="<?=BASE_URL?>logout.php">⏻ Keluar</a>
    </div>
    <?=api(15)?>
  </aside>
  <div class="main">
    <header class="topbar">
      <button class="menu-toggle" id="menuToggle" type="button" aria-label="Menu">☰</button>
      <span class="topbar-title"><?=h($judulBar)?></span>
      <span class="user-chip">👤 <?=h($_SESSION['nama'] ?? '')?></span>
      </header>
    <main class="content">
