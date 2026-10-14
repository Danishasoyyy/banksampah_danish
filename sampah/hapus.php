<?php
require_once '../config/auth.php';
wajib_role(['admin']);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_cek();
    try {
        q($koneksi, "DELETE FROM sampah WHERE id_sampah=?", 'i', (int)($_POST['id'] ?? 0));
        flash('Data sampah dihapus.');
    } catch (Throwable $e) {
        flash('Tidak bisa dihapus karena sudah dipakai di transaksi. Ubah stok/harga saja bila perlu.', 'danger');
    }
}
header('Location: index.php'); exit;
