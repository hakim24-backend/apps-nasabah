-- phpMyAdmin SQL Dump
-- version 4.8.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 19, 2019 at 01:12 PM
-- Server version: 10.1.37-MariaDB
-- PHP Version: 7.3.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pinjaman_online`
--

-- --------------------------------------------------------

--
-- Table structure for table `akun`
--

CREATE TABLE `akun` (
  `id` int(11) NOT NULL,
  `password_hash` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `access_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `tanggal_waktu_pembuatan` datetime DEFAULT NULL,
  `id_status_akun` int(11) DEFAULT NULL,
  `id_jenis_akun` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `akun`
--

INSERT INTO `akun` (`id`, `password_hash`, `access_token`, `tanggal_waktu_pembuatan`, `id_status_akun`, `id_jenis_akun`) VALUES
(11, '$2y$13$qvswMkfQEe1LGiJYka/Kl.jLuPCVpa96XRSRN97ht9FLecm5l63O.', 'HQszq0_M6S6bm0cMW3JSjznvHxjzVbML', '2019-05-29 15:23:42', 1, 1),
(15, '$2y$13$DM989eVy/hKIURMN.BY8Yu9B0yPomRU2O3bywWq3ata50CQBHawNu', '4_Y9X1jeh95WRBKPB4RcaAtmN29xpr43', '2019-06-19 14:52:56', 1, 3),
(16, '$2y$13$NWcSKXWoONWiZ436kb3jtuMV315xJ/KJQfUunj/G90HjqtVlXrV1u', '1VP6OsD2rmQLeySG7rc3Oxs_9cZ5_z2m', '2019-06-19 14:54:35', 1, 4),
(17, '$2y$13$REtWyu96xQh8ICmnCBpXReXJSLklmMd4cbD1ce2s8tVs8DL2ltUou', 'BYivSmc9wGLvKcVR6O4oWgGKT9qB29BR', '2019-06-19 17:20:41', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `akun_jenis`
--

CREATE TABLE `akun_jenis` (
  `id` int(11) NOT NULL,
  `jenis_akun` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `akun_jenis`
--

INSERT INTO `akun_jenis` (`id`, `jenis_akun`) VALUES
(1, 'pengguna'),
(2, 'nasabah'),
(3, 'owner'),
(4, 'developer');

-- --------------------------------------------------------

--
-- Table structure for table `akun_status`
--

CREATE TABLE `akun_status` (
  `id` int(11) NOT NULL,
  `status_akun` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `akun_status`
--

INSERT INTO `akun_status` (`id`, `status_akun`) VALUES
(1, 'aktif'),
(2, 'tidak aktif'),
(3, 'belum terverifi');

-- --------------------------------------------------------

--
-- Table structure for table `migration`
--

CREATE TABLE `migration` (
  `version` varchar(180) COLLATE utf8_unicode_ci NOT NULL,
  `apply_time` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `migration`
--

INSERT INTO `migration` (`version`, `apply_time`) VALUES
('m000000_000000_base', 1557826793);

-- --------------------------------------------------------

--
-- Table structure for table `nasabah`
--

CREATE TABLE `nasabah` (
  `id` int(11) NOT NULL,
  `id_akun` int(11) DEFAULT NULL,
  `nama` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `alamat` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `tempat_lahir` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `jenis_kelamin` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `nomor_telepon` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `foto_ktp` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `foto_bersama_ktp` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `latitude` double DEFAULT NULL,
  `longitude` double DEFAULT NULL,
  `tanggal_waktu_posisi` datetime DEFAULT NULL,
  `nomor_kartu_sim` varchar(100) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `nasabah_buku_telepon`
--

CREATE TABLE `nasabah_buku_telepon` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `nomor_telepon` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `id_nasabah` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `nasabah_riwayat_nomor_telepon`
--

CREATE TABLE `nasabah_riwayat_nomor_telepon` (
  `id` int(11) NOT NULL,
  `id_nasabah` int(11) DEFAULT NULL,
  `nomor_telepon` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `tanggal_waktu_pembuatan` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `peminjaman`
--

CREATE TABLE `peminjaman` (
  `id` int(11) NOT NULL,
  `id_nasabah` int(11) DEFAULT NULL,
  `id_jenis_peminjaman` int(11) DEFAULT NULL,
  `nomor_kontrak` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `nama` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `alamat` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `nik_ktp` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `nominal_peminjaman` double DEFAULT NULL,
  `id_jenis_durasi` int(11) DEFAULT NULL,
  `durasi` int(11) DEFAULT NULL,
  `jaminan` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `nominal_pencicilan` double DEFAULT NULL,
  `nominal_admin` double DEFAULT NULL,
  `nominal_tabungan_ditahan` double DEFAULT NULL,
  `foto_ktp` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `foto_bersama_ktp` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `foto_optional` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `tanggal_waktu_pembuatan` datetime DEFAULT NULL COMMENT 'waktu_pembuatan_data',
  `id_status_peminjaman` int(11) DEFAULT NULL,
  `id_pengguna` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `peminjaman_durasi_jenis`
--

CREATE TABLE `peminjaman_durasi_jenis` (
  `id` int(11) NOT NULL,
  `durasi_peminjaman` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `peminjaman_durasi_jenis`
--

INSERT INTO `peminjaman_durasi_jenis` (`id`, `durasi_peminjaman`) VALUES
(1, 'mingguan'),
(2, 'bulanan');

-- --------------------------------------------------------

--
-- Table structure for table `peminjaman_jenis`
--

CREATE TABLE `peminjaman_jenis` (
  `id` int(11) NOT NULL,
  `jenis_peminjaman` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `besar_bunga` double DEFAULT NULL,
  `besar_admin` double DEFAULT NULL,
  `besar_tabungan_ditahan` double DEFAULT NULL,
  `besar_denda` double DEFAULT NULL,
  `besar_pinalti_langsung_lunas` double DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `peminjaman_jenis`
--

INSERT INTO `peminjaman_jenis` (`id`, `jenis_peminjaman`, `besar_bunga`, `besar_admin`, `besar_tabungan_ditahan`, `besar_denda`, `besar_pinalti_langsung_lunas`) VALUES
(1, 'dengan jaminan', 3, 5, 0, 8, 5),
(2, 'non jaminan', 5, 5, 5, 8, 0);

-- --------------------------------------------------------

--
-- Table structure for table `peminjaman_status`
--

CREATE TABLE `peminjaman_status` (
  `id` int(11) NOT NULL,
  `status_peminjaman` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `peminjaman_status`
--

INSERT INTO `peminjaman_status` (`id`, `status_peminjaman`) VALUES
(1, 'belum lunas'),
(2, 'lunas');

-- --------------------------------------------------------

--
-- Table structure for table `pencicilan`
--

CREATE TABLE `pencicilan` (
  `id` int(11) NOT NULL,
  `id_peminjaman` int(11) DEFAULT NULL,
  `id_pengguna` int(11) DEFAULT NULL,
  `id_jenis_pencicilan` int(11) DEFAULT NULL,
  `tanggal_jatuh_tempo` date DEFAULT NULL,
  `nominal_cicilan` double DEFAULT NULL COMMENT 'nominal cicilan terbayar (termasuk denda dan lain2)',
  `tanggal_waktu_cicilan` datetime DEFAULT NULL,
  `id_status_bayar` int(11) DEFAULT NULL,
  `periode` int(11) DEFAULT NULL,
  `nominal_denda_dibayar` double DEFAULT NULL,
  `nominal_denda_berhenti` double DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pencicilan_jenis`
--

CREATE TABLE `pencicilan_jenis` (
  `id` int(11) NOT NULL,
  `jenis_pencicilan` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `pencicilan_jenis`
--

INSERT INTO `pencicilan_jenis` (`id`, `jenis_pencicilan`) VALUES
(1, 'sesuai durasi'),
(2, 'langsung lunas');

-- --------------------------------------------------------

--
-- Table structure for table `pencicilan_status_bayar`
--

CREATE TABLE `pencicilan_status_bayar` (
  `id` int(11) NOT NULL,
  `status_bayar` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `pencicilan_status_bayar`
--

INSERT INTO `pencicilan_status_bayar` (`id`, `status_bayar`) VALUES
(1, 'belum dibayar'),
(2, 'sudah dibayar');

-- --------------------------------------------------------

--
-- Table structure for table `pengguna`
--

CREATE TABLE `pengguna` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `alamat` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `jenis_kelamin` varchar(15) COLLATE utf8_unicode_ci DEFAULT NULL,
  `tempat_lahir` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `email` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `id_akun` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `pengguna`
--

INSERT INTO `pengguna` (`id`, `nama`, `alamat`, `jenis_kelamin`, `tempat_lahir`, `tanggal_lahir`, `email`, `id_akun`) VALUES
(1, 'Hakim', 'Kenjeran', 'Pria', 'surabaya', '2019-05-12', 'hakim@gmail.com', 11),
(2, 'Henry', 'Rungkut', 'Pria', 'Surabaya', '1967-09-21', 'henry@gmail.com', 15),
(3, 'Hilmy', 'Gebang', 'Pria', 'Surabaya', '1994-12-12', 'hilmy@gmail.com', 16);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `akun`
--
ALTER TABLE `akun`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_access_token` (`access_token`),
  ADD KEY `id_status_akun` (`id_status_akun`),
  ADD KEY `id_jenis_akun` (`id_jenis_akun`);

--
-- Indexes for table `akun_jenis`
--
ALTER TABLE `akun_jenis`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `akun_status`
--
ALTER TABLE `akun_status`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migration`
--
ALTER TABLE `migration`
  ADD PRIMARY KEY (`version`);

--
-- Indexes for table `nasabah`
--
ALTER TABLE `nasabah`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_email` (`email`),
  ADD KEY `id_akun` (`id_akun`);

--
-- Indexes for table `nasabah_buku_telepon`
--
ALTER TABLE `nasabah_buku_telepon`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_nasabah` (`id_nasabah`);

--
-- Indexes for table `nasabah_riwayat_nomor_telepon`
--
ALTER TABLE `nasabah_riwayat_nomor_telepon`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_nasabah` (`id_nasabah`);

--
-- Indexes for table `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_nomor_kontrak` (`nomor_kontrak`),
  ADD KEY `id_nasabah` (`id_nasabah`),
  ADD KEY `id_jenis_peminjaman` (`id_jenis_peminjaman`),
  ADD KEY `id_jenis_durasi` (`id_jenis_durasi`),
  ADD KEY `id_status_peminjaman` (`id_status_peminjaman`),
  ADD KEY `id_pengguna` (`id_pengguna`);

--
-- Indexes for table `peminjaman_durasi_jenis`
--
ALTER TABLE `peminjaman_durasi_jenis`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `peminjaman_jenis`
--
ALTER TABLE `peminjaman_jenis`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `peminjaman_status`
--
ALTER TABLE `peminjaman_status`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pencicilan`
--
ALTER TABLE `pencicilan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_peminjaman` (`id_peminjaman`),
  ADD KEY `id_pengguna` (`id_pengguna`),
  ADD KEY `id_jenis_pencicilan` (`id_jenis_pencicilan`),
  ADD KEY `id_status_bayar` (`id_status_bayar`);

--
-- Indexes for table `pencicilan_jenis`
--
ALTER TABLE `pencicilan_jenis`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pencicilan_status_bayar`
--
ALTER TABLE `pencicilan_status_bayar`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pengguna`
--
ALTER TABLE `pengguna`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_email_pengguna` (`email`),
  ADD KEY `id_akun` (`id_akun`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `akun`
--
ALTER TABLE `akun`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `akun_jenis`
--
ALTER TABLE `akun_jenis`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `akun_status`
--
ALTER TABLE `akun_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `nasabah`
--
ALTER TABLE `nasabah`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nasabah_buku_telepon`
--
ALTER TABLE `nasabah_buku_telepon`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nasabah_riwayat_nomor_telepon`
--
ALTER TABLE `nasabah_riwayat_nomor_telepon`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `peminjaman`
--
ALTER TABLE `peminjaman`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `peminjaman_durasi_jenis`
--
ALTER TABLE `peminjaman_durasi_jenis`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `peminjaman_jenis`
--
ALTER TABLE `peminjaman_jenis`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `peminjaman_status`
--
ALTER TABLE `peminjaman_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `pencicilan`
--
ALTER TABLE `pencicilan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pencicilan_jenis`
--
ALTER TABLE `pencicilan_jenis`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `pencicilan_status_bayar`
--
ALTER TABLE `pencicilan_status_bayar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `pengguna`
--
ALTER TABLE `pengguna`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `akun`
--
ALTER TABLE `akun`
  ADD CONSTRAINT `akun_ibfk_1` FOREIGN KEY (`id_status_akun`) REFERENCES `akun_status` (`id`),
  ADD CONSTRAINT `akun_ibfk_2` FOREIGN KEY (`id_jenis_akun`) REFERENCES `akun_jenis` (`id`);

--
-- Constraints for table `nasabah`
--
ALTER TABLE `nasabah`
  ADD CONSTRAINT `nasabah_ibfk_1` FOREIGN KEY (`id_akun`) REFERENCES `akun` (`id`);

--
-- Constraints for table `nasabah_buku_telepon`
--
ALTER TABLE `nasabah_buku_telepon`
  ADD CONSTRAINT `nasabah_buku_telepon_ibfk_1` FOREIGN KEY (`id_nasabah`) REFERENCES `nasabah` (`id`);

--
-- Constraints for table `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD CONSTRAINT `peminjaman_ibfk_1` FOREIGN KEY (`id_nasabah`) REFERENCES `nasabah` (`id`),
  ADD CONSTRAINT `peminjaman_ibfk_2` FOREIGN KEY (`id_jenis_peminjaman`) REFERENCES `peminjaman_jenis` (`id`),
  ADD CONSTRAINT `peminjaman_ibfk_3` FOREIGN KEY (`id_jenis_durasi`) REFERENCES `peminjaman_durasi_jenis` (`id`),
  ADD CONSTRAINT `peminjaman_ibfk_4` FOREIGN KEY (`id_status_peminjaman`) REFERENCES `peminjaman_status` (`id`),
  ADD CONSTRAINT `peminjaman_ibfk_5` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna` (`id`);

--
-- Constraints for table `pencicilan`
--
ALTER TABLE `pencicilan`
  ADD CONSTRAINT `pencicilan_ibfk_1` FOREIGN KEY (`id_peminjaman`) REFERENCES `peminjaman` (`id`),
  ADD CONSTRAINT `pencicilan_ibfk_2` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna` (`id`),
  ADD CONSTRAINT `pencicilan_ibfk_3` FOREIGN KEY (`id_jenis_pencicilan`) REFERENCES `pencicilan_jenis` (`id`),
  ADD CONSTRAINT `pencicilan_ibfk_4` FOREIGN KEY (`id_status_bayar`) REFERENCES `pencicilan_status_bayar` (`id`);

--
-- Constraints for table `pengguna`
--
ALTER TABLE `pengguna`
  ADD CONSTRAINT `pengguna_ibfk_1` FOREIGN KEY (`id_akun`) REFERENCES `akun` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
