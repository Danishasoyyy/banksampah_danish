BANK SAMPAH DANISH

CARA PASANG
1. Salin folder banksampah_danish ke C:\xampp\htdocs\
2. Nyalakan Apache dan MySQL di XAMPP.
3. Buka phpMyAdmin > Import > pilih banksampah_danish.sql > Go.
4. Buka http://localhost/banksampah_danish/

Bila password MySQL Anda tidak kosong, ubah config/koneksi.php.

LOGIN DEMO
admin / admin123
guru / guru123
siswa / siswa123
pengepul / pengepul123
(Password teks biasa otomatis diubah menjadi hash saat login pertama.)

STRUKTUR
login.php, logout.php, dashboard.php
config/        koneksi, auth (fungsi bantu), header (sidebar), footer
assets/tema-api.css   semua tampilan (tema biru-putih + api). Ubah warna di bagian :root
setor/         siswa & guru menyetor sampah (saldo + stok bertambah)
transaksi/     riwayat setor
tarik_saldo/   siswa/guru mengajukan; admin menyetujui atau menolak
sampah/        admin kelola jenis, harga, stok (index, edit, hapus)
user/          admin kelola user (index, tambah, edit, hapus)
jual_berasampah/ admin mencatat penjualan ke pengepul (stok berkurang); pengepul melihat riwayat
pengepul/      pengepul melihat stok dan harga

ATURAN
- Saldo = total setor - penarikan yang disetujui. Pengajuan yang masih menunggu ikut dicadangkan
  agar saldo tidak bisa ditarik dua kali.
- Membuat user role Pengepul otomatis membuat data di tabel pengepul.

CATATAN TAMPILAN
- Jika tampilan tidak berubah, pastikan assets/tema-api.css ikut disalin lalu tekan Ctrl+F5.
- Hapus assets/style.css lama bila masih ada (tidak dipakai lagi).
