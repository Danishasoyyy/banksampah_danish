<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/koneksi.php';

/* ---------- Login & role ---------- */
function wajib_login() {
    if (empty($_SESSION['id_user'])) {
        header('Location: ' . BASE_URL . 'login.php');
        exit;
    }
}
function wajib_role(array $roles) {
    wajib_login();
    if (!in_array($_SESSION['role'], $roles, true)) {
        http_response_code(403);
        die('Akses ditolak. <a href="' . BASE_URL . 'dashboard.php">Kembali</a>');
    }
}

/* ---------- Helper tampilan ---------- */
function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function rupiah($n) { return 'Rp ' . number_format((float)$n, 0, ',', '.'); }
function kg($n) { return rtrim(rtrim(number_format((float)$n, 2, ',', '.'), '0'), ',') . ' kg'; }
function tgl($s) { return date('d-m-Y H:i', strtotime($s)); }
function angka($s) { return (float)str_replace(',', '.', str_replace(' ', '', (string)$s)); }

/* Pesan sementara (flash) */
function flash($msg = null, $type = 'success') {
    if ($msg !== null) { $_SESSION['flash'] = [$msg, $type]; return ''; }
    if (!empty($_SESSION['flash'])) {
        [$m, $t] = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return '<div class="alert alert-' . $t . '">' . h($m) . '</div>';
    }
    return '';
}

/* ---------- CSRF ---------- */
function csrf_token() {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(16));
    return $_SESSION['csrf'];
}
function csrf_field() { return '<input type="hidden" name="csrf" value="' . csrf_token() . '">'; }
function csrf_cek() {
    if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
        http_response_code(400);
        die('Token tidak valid. Kembali dan coba lagi.');
    }
}

/* ---------- Database ---------- */
// Jalankan prepared statement; melempar Exception bila gagal
function q($k, $sql, $types = '', ...$p) {
    $st = mysqli_prepare($k, $sql);
    if (!$st) throw new Exception(mysqli_error($k));
    if ($types !== '') mysqli_stmt_bind_param($st, $types, ...$p);
    if (!mysqli_stmt_execute($st)) throw new Exception(mysqli_stmt_error($st));
    return $st;
}
function rows($st) {
    $r = mysqli_stmt_get_result($st);
    return $r ? mysqli_fetch_all($r, MYSQLI_ASSOC) : [];
}
function row($st) { $r = rows($st); return $r ? $r[0] : null; }

/* Saldo = total setor - penarikan disetujui (- yang masih menunggu bila $sisihkan_menunggu) */
function saldo_user($k, $id, $sisihkan_menunggu = false) {
    $t = (float)row(q($k, "SELECT COALESCE(SUM(total),0) s FROM transaksi WHERE id_user=?", 'i', $id))['s'];
    $st = $sisihkan_menunggu ? "('disetujui','menunggu')" : "('disetujui')";
    $o = (float)row(q($k, "SELECT COALESCE(SUM(jumlah),0) s FROM tarik_saldo WHERE id_user=? AND status IN $st", 'i', $id))['s'];
    return $t - $o;
}

/* ---------- Komponen tampilan ---------- */
// Lidah api animasi (CSS murni)
function api($n = 40) { return '<div class="flame-bar" aria-hidden="true">' . str_repeat('<i></i>', $n) . '</div>'; }

// Kepala halaman: tombol Kembali di kiri atas, aksi utama di kanan atas
function page_head($judul, $back = '../dashboard.php', $aksi = '') {
    echo '<div class="page-head"><div>';
    if ($back) echo '<a class="btn-back" href="' . h($back) . '">&larr; Kembali</a>';
    echo '<h1 class="page-title">' . $judul . '</h1></div>';
    if ($aksi) echo '<div class="page-actions">' . $aksi . '</div>';
    echo '</div>';
}
