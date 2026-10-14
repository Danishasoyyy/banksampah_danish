<?php
require_once '../config/auth.php';
wajib_role(['admin']);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_cek();
    $id = (int)($_POST['id'] ?? 0);
    if ($id === (int)$_SESSION['id_user']) {
        flash('Anda tidak bisa menghapus akun sendiri.', 'danger');
    } else {
        try {
            q($koneksi, "DELETE FROM user WHERE id_user=?", 'i', $id);
            flash('User dihapus.');
        } catch (Throwable $e) { flash('Gagal menghapus: ' . $e->getMessage(), 'danger'); }
    }
}
header('Location: index.php'); exit;
