<?php
mysqli_report(MYSQLI_REPORT_OFF);
$koneksi = mysqli_connect('localhost', 'root', '', 'banksampah_danish');
if (!$koneksi) {
    die('Koneksi database gagal: ' . mysqli_connect_error() . '. Pastikan MySQL di XAMPP menyala dan database banksampah_danish sudah diimport.');
}
mysqli_set_charset($koneksi, 'utf8mb4');

// URL dasar aplikasi (otomatis, sehingga link dari subfolder tetap benar)
if (!defined('BASE_URL')) {
    $root = str_replace('\\', '/', rtrim($_SERVER['DOCUMENT_ROOT'] ?? '', '/\\'));
    $app  = str_replace('\\', '/', dirname(__DIR__));
    $path = (stripos($app, $root) === 0) ? substr($app, strlen($root)) : '/banksampah_danish';
    define('BASE_URL', rtrim($path, '/') . '/');
}
