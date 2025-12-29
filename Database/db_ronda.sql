-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 28, 2025 at 03:26 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_ronda`
--
CREATE DATABASE IF NOT EXISTS `db_ronda` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `db_ronda`;

-- --------------------------------------------------------

--
-- Table structure for table `tb_insiden`
--

CREATE TABLE `tb_insiden` (
  `id_insiden` int(11) NOT NULL,
  `id_pengguna` int(11) DEFAULT NULL,
  `timestamp` datetime DEFAULT current_timestamp(),
  `nama_insiden` varchar(255) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `status` enum('pending','diterima','ditolak') NOT NULL DEFAULT 'pending',
  `catatan_admin` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_insiden`
--

INSERT INTO `tb_insiden` (`id_insiden`, `id_pengguna`, `timestamp`, `nama_insiden`, `deskripsi`, `status`, `catatan_admin`) VALUES
(7, 1, '2025-12-22 19:19:46', 'Kehilangan ', 'test', 'pending', NULL),
(8, 9, '2025-12-28 21:19:50', 'Pencurian', 'Kejadian di blok A pukul 02:00', 'diterima', 'Sudah ditangani                            ');

-- --------------------------------------------------------

--
-- Table structure for table `tb_jadwal`
--

CREATE TABLE `tb_jadwal` (
  `id_jadwal` int(11) NOT NULL,
  `id_pengguna` int(11) DEFAULT NULL,
  `tanggal_tugas` date NOT NULL,
  `jam_mulai` time DEFAULT '21:00:00',
  `jam_selesai` time DEFAULT '05:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_jadwal`
--

INSERT INTO `tb_jadwal` (`id_jadwal`, `id_pengguna`, `tanggal_tugas`, `jam_mulai`, `jam_selesai`) VALUES
(56, 4, '2025-12-24', '21:00:00', '05:00:00'),
(57, 4, '2025-12-26', '21:00:00', '05:00:00'),
(58, 4, '2025-12-28', '21:00:00', '05:00:00'),
(59, 4, '2025-12-30', '21:00:00', '05:00:00'),
(60, 4, '2025-12-25', '21:00:00', '05:00:00'),
(61, 4, '2025-12-27', '21:00:00', '05:00:00'),
(62, 4, '2025-12-29', '21:00:00', '05:00:00'),
(63, 4, '2025-12-31', '21:00:00', '05:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `tb_pengguna`
--

CREATE TABLE `tb_pengguna` (
  `id_pengguna` int(11) NOT NULL,
  `nama_pengguna` varchar(100) NOT NULL,
  `alamat` varchar(255) DEFAULT NULL,
  `jenis_kelamin` enum('L','P') NOT NULL,
  `no_telp` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(200) NOT NULL,
  `role` enum('Admin','Koordinator','Petugas') DEFAULT 'Petugas'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tb_pengguna`
--

INSERT INTO `tb_pengguna` (`id_pengguna`, `nama_pengguna`, `alamat`, `jenis_kelamin`, `no_telp`, `email`, `password`, `role`) VALUES
(1, 'Admin', 'Jl. Merdeka No.112', 'L', '0811111111', 'admin@ronda.com', '$2y$10$o6eNYtGPmfumNw/9Do1iHePTW1.C5z4YoCnEmElY/WVi7D3Fn4Cqa', 'Admin'),
(3, 'Koordinator', 'Jl. Merdeka No.2', 'L', '0812222222', 'koordinator@ronda.com', '$2y$10$5Edqm6ZjxkspYouTYEvnwuKCoHLrwtHy97iHjeoEMvxnKOiDHwEAq', 'Koordinator'),
(4, 'Petugas Utama', 'Jl. Merdeka No.5', 'P', '0813333333', 'petugasutama@ronda.com', '$2y$10$57boGenh.15ygf2somM4VeKOCYsf8GswZTuRkdUh4G56EOx36ImAa', 'Petugas'),
(9, 'Petugas 1', 'Jl. Merdeka No.10', 'L', '0813333331', 'petugas1@ronda.com', '$2y$10$50T6glzoJpraKMXkqPeGHunGOh.7717ZXmDVO8AAYnjmRPiY4lvBu', 'Petugas'),
(10, 'Petugas 2', 'Jl. Merdeka No.11', 'P', '0813333332', 'petugas2@ronda.com', '$2y$10$IbZCDi/SyjXeO740Vb5K1OwGbyVW7PZ4lpbaO/0t./RvAdp5Gow6y', 'Petugas'),
(11, 'Petugas 3', 'Jl. Merdeka No.12', 'L', '0813333333', 'petugas3@ronda.com', '$2y$10$mbdsGtm09V7v3GxWOFueDe7EVjcMp/ssyXDkbGAP42s9NjBVb4fr.', 'Petugas'),
(12, 'Petugas 4', 'Jl. Merdeka No.13', 'P', '0813333334', 'petugas4@ronda.com', '$2y$10$14E/0vMMYUw0NTUBk0TvqOcvAULBIGOA5KvCyz9QAydo24YxLAD7u', 'Petugas');

-- --------------------------------------------------------

--
-- Table structure for table `tb_presensi`
--

CREATE TABLE `tb_presensi` (
  `id_absen` int(11) NOT NULL,
  `id_jadwal` int(11) DEFAULT NULL,
  `status` enum('hadir','tidak hadir') NOT NULL,
  `dicatat_oleh` int(11) DEFAULT NULL,
  `keterangan` varchar(255) DEFAULT NULL,
  `waktu_absen` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tb_insiden`
--
ALTER TABLE `tb_insiden`
  ADD PRIMARY KEY (`id_insiden`),
  ADD KEY `id_pengguna` (`id_pengguna`);

--
-- Indexes for table `tb_jadwal`
--
ALTER TABLE `tb_jadwal`
  ADD PRIMARY KEY (`id_jadwal`),
  ADD KEY `id_pengguna` (`id_pengguna`);

--
-- Indexes for table `tb_pengguna`
--
ALTER TABLE `tb_pengguna`
  ADD PRIMARY KEY (`id_pengguna`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `tb_presensi`
--
ALTER TABLE `tb_presensi`
  ADD PRIMARY KEY (`id_absen`),
  ADD KEY `id_jadwal` (`id_jadwal`),
  ADD KEY `dicatat_oleh` (`dicatat_oleh`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tb_insiden`
--
ALTER TABLE `tb_insiden`
  MODIFY `id_insiden` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `tb_jadwal`
--
ALTER TABLE `tb_jadwal`
  MODIFY `id_jadwal` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT for table `tb_pengguna`
--
ALTER TABLE `tb_pengguna`
  MODIFY `id_pengguna` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `tb_presensi`
--
ALTER TABLE `tb_presensi`
  MODIFY `id_absen` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tb_insiden`
--
ALTER TABLE `tb_insiden`
  ADD CONSTRAINT `tb_insiden_ibfk_1` FOREIGN KEY (`id_pengguna`) REFERENCES `tb_pengguna` (`id_pengguna`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tb_jadwal`
--
ALTER TABLE `tb_jadwal`
  ADD CONSTRAINT `tb_jadwal_ibfk_1` FOREIGN KEY (`id_pengguna`) REFERENCES `tb_pengguna` (`id_pengguna`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tb_presensi`
--
ALTER TABLE `tb_presensi`
  ADD CONSTRAINT `tb_presensi_ibfk_1` FOREIGN KEY (`id_jadwal`) REFERENCES `tb_jadwal` (`id_jadwal`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tb_presensi_ibfk_2` FOREIGN KEY (`dicatat_oleh`) REFERENCES `tb_pengguna` (`id_pengguna`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
