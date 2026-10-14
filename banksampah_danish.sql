-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 23, 2026 at 09:10 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

CREATE DATABASE IF NOT EXISTS `banksampah_danish` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `banksampah_danish`;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `banksampah_danish`
--

-- --------------------------------------------------------

--
-- Table structure for table `pengepul`
--

CREATE TABLE `pengepul` (
  `id_pengepul` int(11) NOT NULL,
  `nama_pengepul` varchar(100) NOT NULL,
  `alamat` text DEFAULT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `id_user` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pengepul`
--

INSERT INTO `pengepul` (`id_pengepul`, `nama_pengepul`, `alamat`, `no_hp`, `id_user`) VALUES
(1, 'Pengepul Contoh', 'Jl. Contoh No. 1', '081234567890', 4);

-- --------------------------------------------------------

--
-- Table structure for table `sampah`
--

CREATE TABLE `sampah` (
  `id_sampah` int(11) NOT NULL,
  `nama_sampah` varchar(100) NOT NULL,
  `jenis` varchar(50) NOT NULL,
  `harga_per_kg` decimal(12,2) NOT NULL DEFAULT 0.00,
  `stok` decimal(12,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sampah`
--

INSERT INTO `sampah` (`id_sampah`, `nama_sampah`, `jenis`, `harga_per_kg`, `stok`) VALUES
(1, 'Botol Plastik', 'Plastik', 5000.00, 110.00),
(2, 'Kardus', 'Kertas', 3000.00, 6.00),
(3, 'Kertas', 'Kertas', 2500.00, 0.00),
(4, 'Kaleng', 'Logam', 7000.00, 116.00),
(5, 'Besi', 'Logam', 5000.00, 67.01);

-- --------------------------------------------------------

--
-- Table structure for table `setor_pengepul`
--

CREATE TABLE `setor_pengepul` (
  `id_setor` int(11) NOT NULL,
  `id_pengepul` int(11) NOT NULL,
  `id_sampah` int(11) NOT NULL,
  `berat` decimal(12,2) NOT NULL,
  `harga_per_kg` decimal(12,2) NOT NULL,
  `total` decimal(12,2) NOT NULL,
  `tanggal` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `setor_pengepul`
--

INSERT INTO `setor_pengepul` (`id_setor`, `id_pengepul`, `id_sampah`, `berat`, `harga_per_kg`, `total`, `tanggal`) VALUES
(1, 1, 1, 10.00, 5000.00, 50000.00, '2026-09-23 13:19:38'),
(2, 1, 4, 4.00, 7000.00, 28000.00, '2026-09-23 13:23:03'),
(3, 1, 2, 10.00, 3000.00, 30000.00, '2026-09-23 13:36:24');

-- --------------------------------------------------------

--
-- Table structure for table `tarik_saldo`
--

CREATE TABLE `tarik_saldo` (
  `id_tarik` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `jumlah` decimal(12,2) NOT NULL,
  `tanggal` datetime NOT NULL DEFAULT current_timestamp(),
  `status` enum('menunggu','disetujui','ditolak') NOT NULL DEFAULT 'menunggu'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transaksi`
--

CREATE TABLE `transaksi` (
  `id_transaksi` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_sampah` int(11) NOT NULL,
  `berat` decimal(12,2) NOT NULL,
  `harga_per_kg` decimal(12,2) NOT NULL,
  `total` decimal(12,2) NOT NULL,
  `tanggal` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaksi`
--

INSERT INTO `transaksi` (`id_transaksi`, `id_user`, `id_sampah`, `berat`, `harga_per_kg`, `total`, `tanggal`) VALUES
(1, 2, 1, 20.00, 5000.00, 100000.00, '2026-09-23 13:05:07'),
(2, 3, 2, 16.00, 3000.00, 48000.00, '2026-09-23 13:15:09'),
(3, 3, 5, 50.00, 5000.00, 250000.00, '2026-09-23 13:15:22'),
(4, 3, 5, 0.01, 5000.00, 50.00, '2026-09-23 13:15:28'),
(5, 3, 4, 15.00, 7000.00, 105000.00, '2026-09-23 13:20:59'),
(6, 3, 4, 5.00, 7000.00, 35000.00, '2026-09-23 13:23:40'),
(7, 2, 5, 17.00, 5000.00, 85000.00, '2026-09-23 13:24:54'),
(8, 3, 4, 100.00, 7000.00, 700000.00, '2026-09-23 13:27:11'),
(9, 3, 1, 100.00, 5000.00, 500000.00, '2026-09-23 13:35:25');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id_user` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','guru','siswa','pengepul') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id_user`, `nama`, `username`, `password`, `role`) VALUES
(1, 'Administrator', 'admin', 'admin123', 'admin'),
(2, 'Guru Bank Sampah', 'guru', 'guru123', 'guru'),
(3, 'Siswa Contoh', 'siswa', 'siswa123', 'siswa'),
(4, 'Pengepul Contoh', 'pengepul', 'pengepul123', 'pengepul');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `pengepul`
--
ALTER TABLE `pengepul`
  ADD PRIMARY KEY (`id_pengepul`),
  ADD KEY `fk_pengepul_user` (`id_user`);

--
-- Indexes for table `sampah`
--
ALTER TABLE `sampah`
  ADD PRIMARY KEY (`id_sampah`);

--
-- Indexes for table `setor_pengepul`
--
ALTER TABLE `setor_pengepul`
  ADD PRIMARY KEY (`id_setor`),
  ADD KEY `fk_setor_pengepul` (`id_pengepul`),
  ADD KEY `fk_setor_sampah` (`id_sampah`);

--
-- Indexes for table `tarik_saldo`
--
ALTER TABLE `tarik_saldo`
  ADD PRIMARY KEY (`id_tarik`),
  ADD KEY `fk_tarik_user` (`id_user`);

--
-- Indexes for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id_transaksi`),
  ADD KEY `fk_transaksi_user` (`id_user`),
  ADD KEY `fk_transaksi_sampah` (`id_sampah`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `pengepul`
--
ALTER TABLE `pengepul`
  MODIFY `id_pengepul` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `sampah`
--
ALTER TABLE `sampah`
  MODIFY `id_sampah` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `setor_pengepul`
--
ALTER TABLE `setor_pengepul`
  MODIFY `id_setor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tarik_saldo`
--
ALTER TABLE `tarik_saldo`
  MODIFY `id_tarik` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id_transaksi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `pengepul`
--
ALTER TABLE `pengepul`
  ADD CONSTRAINT `fk_pengepul_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `setor_pengepul`
--
ALTER TABLE `setor_pengepul`
  ADD CONSTRAINT `fk_setor_pengepul` FOREIGN KEY (`id_pengepul`) REFERENCES `pengepul` (`id_pengepul`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_setor_sampah` FOREIGN KEY (`id_sampah`) REFERENCES `sampah` (`id_sampah`) ON UPDATE CASCADE;

--
-- Constraints for table `tarik_saldo`
--
ALTER TABLE `tarik_saldo`
  ADD CONSTRAINT `fk_tarik_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD CONSTRAINT `fk_transaksi_sampah` FOREIGN KEY (`id_sampah`) REFERENCES `sampah` (`id_sampah`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_transaksi_user` FOREIGN KEY (`id_user`) REFERENCES `user` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
