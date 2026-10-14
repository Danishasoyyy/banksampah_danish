<!doctype html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Bank Sampah Danish</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body{background:linear-gradient(135deg,#eff6ff,#ecfeff);min-height:100vh}
.card{border:0;border-radius:20px;box-shadow:0 8px 24px #0001}
.hero{margin-bottom:24px}
.saldo{display:inline-block;background:#dcfce7;color:#166534;font-weight:700;font-size:22px;padding:10px 20px;border-radius:14px}
.menu-card{transition:.2s}.menu-card:hover{transform:translateY(-4px);box-shadow:0 14px 30px #0002}
</style>
</head>
<body>
<nav class="navbar navbar-dark mb-4" style="background:#075985">
  <div class="container">
    <a class="navbar-brand fw-bold" href="<?=BASE_URL?>dashboard.php">♻️ Bank Sampah Danish</a>
    <div class="text-white">
      <?=htmlspecialchars($_SESSION['nama'] ?? '')?> (<?=htmlspecialchars($_SESSION['role'] ?? '')?>)
      <a class="btn btn-sm btn-light ms-2" href="<?=BASE_URL?>logout.php">Keluar</a>
    </div>
  </div>
</nav>
<div class="container pb-5">
