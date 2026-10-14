<?php
require_once 'config/auth.php';
wajib_login();
$id = (int)$_SESSION['id_user'];
$role = $_SESSION['role'];
$punyaSaldo = in_array($role, ['siswa', 'guru'], true);
$saldo = $punyaSaldo ? saldo_user($koneksi, $id) : 0;

$stat = [];
if ($role === 'admin') {
    $stat[] = ['👥', row(q($koneksi, "SELECT COUNT(*) n FROM user"))['n'], 'Pengguna'];
    $stat[] = ['⏳', row(q($koneksi, "SELECT COUNT(*) n FROM tarik_saldo WHERE status='menunggu'"))['n'], 'Penarikan menunggu'];
    $stat[] = ['⚖️', kg(row(q($koneksi, "SELECT COALESCE(SUM(stok),0) n FROM sampah"))['n']), 'Total stok sampah'];
}

if ($punyaSaldo) {
    $menus = [['setor', '♻️', 'Setor Sampah', 'Setorkan sampah dan saldo otomatis bertambah.'],
              ['tarik_saldo', '💰', 'Tarik Saldo', 'Ajukan penarikan saldo.'],
              ['transaksi', '📋', 'Riwayat Setor', 'Lihat riwayat setor sampah.']];
} elseif ($role === 'admin') {
    $menus = [['user', '👥', 'Kelola User', 'Tambah, ubah, dan hapus pengguna.'],
              ['tarik_saldo', '✅', 'Verifikasi Tarik Saldo', 'Setujui atau tolak permintaan penarikan.'],
              ['jual_berasampah', '🚚', 'Jual Sampah ke Pengepul', 'Catat penjualan sampah ke pengepul.'],
              ['sampah', '🗑️', 'Data Sampah', 'Kelola jenis, harga, dan stok sampah.']];
} else {
    $menus = [['pengepul', '🚛', 'Stok Sampah', 'Lihat stok dan harga sampah yang tersedia.'],
              ['jual_berasampah', '📦', 'Riwayat Pembelian', 'Lihat sampah yang sudah dibeli.']];
}
include 'config/header.php'; ?>
<div class="card hero-card mb-4">
  <div>
    <h2>Halo, <?= h($_SESSION['nama']) ?> 👋</h2>
    <p class="text-secondary mb-0">Selamat datang di sistem Bank Sampah Danish.</p>
  </div>
  <?php if ($punyaSaldo): ?><div class="saldo">Saldo: <?= rupiah(max(0, $saldo)) ?></div><?php endif; ?>
</div>

<?php if ($stat): ?>
<div class="row g-3 mb-4">
  <?php foreach ($stat as $s): ?>
  <div class="col-md-4"><div class="card stat"><div class="ico"><?= $s[0] ?></div><div><div class="n"><?= h($s[1]) ?></div><div class="l"><?= h($s[2]) ?></div></div></div></div>
  <?php endforeach; ?>
</div>
<?php endif; ?>

<div class="row g-3">
  <?php foreach ($menus as $m): ?>
  <div class="col-md-6 col-xl-4">
    <a class="card menu-card" href="<?= BASE_URL . $m[0] ?>/index.php">
      <div class="ico"><?= $m[1] ?></div>
      <h4><?= h($m[2]) ?></h4>
      <p><?= h($m[3]) ?></p>
      <span class="buka">Buka &rarr;</span>
    </a>
  </div>
  <?php endforeach; ?>
</div>
<?php include 'config/footer.php';
