-- phpMyAdmin SQL Dump
-- Database: db_ronda
-- Versi bersih untuk repository GitHub

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET NAMES utf8mb4 */;

-- --------------------------------------------------------
-- Database
-- --------------------------------------------------------
CREATE DATABASE IF NOT EXISTS `db_ronda`
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_general_ci;
USE `db_ronda`;

-- --------------------------------------------------------
-- Table: tb_pengguna
-- --------------------------------------------------------
CREATE TABLE `tb_pengguna` (
  `id_pengguna` int(11) NOT NULL AUTO_INCREMENT,
  `nama_pengguna` varchar(100) NOT NULL,
  `alamat` varchar(255) DEFAULT NULL,
  `jenis_kelamin` enum('L','P') NOT NULL,
  `no_telp` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(200) NOT NULL,
  `role` enum('Admin','Koordinator','Petugas') DEFAULT 'Petugas',
  PRIMARY KEY (`id_pengguna`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Data: tb_pengguna (AKUN DEMO)
-- --------------------------------------------------------
INSERT INTO `tb_pengguna`
(`nama_pengguna`, `alamat`, `jenis_kelamin`, `no_telp`, `email`, `password`, `role`)
VALUES
('Admin', 'Jl. Merdeka No.112', 'L', '0811111111', 'admin@ronda.com',
'$2y$10$o6eNYtGPmfumNw/9Do1iHePTW1.C5z4YoCnEmElY/WVi7D3Fn4Cqa', 'Admin'),

('Koordinator', 'Jl. Merdeka No.2', 'L', '0812222222', 'koordinator@ronda.com',
'$2y$10$5Edqm6ZjxkspYouTYEvnwuKCoHLrwtHy97iHjeoEMvxnKOiDHwEAq', 'Koordinator');

-- --------------------------------------------------------
-- Table: tb_jadwal
-- --------------------------------------------------------
CREATE TABLE `tb_jadwal` (
  `id_jadwal` int(11) NOT NULL AUTO_INCREMENT,
  `id_pengguna` int(11) DEFAULT NULL,
  `tanggal_tugas` date NOT NULL,
  `jam_mulai` time DEFAULT '21:00:00',
  `jam_selesai` time DEFAULT '05:00:00',
  PRIMARY KEY (`id_jadwal`),
  KEY `id_pengguna` (`id_pengguna`),
  CONSTRAINT `tb_jadwal_ibfk_1`
    FOREIGN KEY (`id_pengguna`)
    REFERENCES `tb_pengguna` (`id_pengguna`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table: tb_insiden
-- --------------------------------------------------------
CREATE TABLE `tb_insiden` (
  `id_insiden` int(11) NOT NULL AUTO_INCREMENT,
  `id_pengguna` int(11) DEFAULT NULL,
  `timestamp` datetime DEFAULT current_timestamp(),
  `nama_insiden` varchar(255) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  PRIMARY KEY (`id_insiden`),
  KEY `id_pengguna` (`id_pengguna`),
  CONSTRAINT `tb_insiden_ibfk_1`
    FOREIGN KEY (`id_pengguna`)
    REFERENCES `tb_pengguna` (`id_pengguna`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table: tb_presensi
-- --------------------------------------------------------
CREATE TABLE `tb_presensi` (
  `id_absen` int(11) NOT NULL AUTO_INCREMENT,
  `id_jadwal` int(11) DEFAULT NULL,
  `status` enum('hadir','tidak hadir') NOT NULL,
  `dicatat_oleh` int(11) DEFAULT NULL,
  `keterangan` varchar(255) DEFAULT NULL,
  `waktu_absen` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id_absen`),
  KEY `id_jadwal` (`id_jadwal`),
  KEY `dicatat_oleh` (`dicatat_oleh`),
  CONSTRAINT `tb_presensi_ibfk_1`
    FOREIGN KEY (`id_jadwal`)
    REFERENCES `tb_jadwal` (`id_jadwal`)
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `tb_presensi_ibfk_2`
    FOREIGN KEY (`dicatat_oleh`)
    REFERENCES `tb_pengguna` (`id_pengguna`)
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

COMMIT;
