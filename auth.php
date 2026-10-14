<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/koneksi.php';

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
