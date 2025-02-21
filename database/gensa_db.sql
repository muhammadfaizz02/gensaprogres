-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 17 Feb 2025 pada 09.16
-- Versi server: 10.4.28-MariaDB
-- Versi PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gensa_db`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `absensi`
--

CREATE TABLE `absensi` (
  `id` int(11) NOT NULL,
  `siswa_id` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `status_absensi` enum('Hadir','Tidak Hadir','Izin','Sakit') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `absensi`
--

INSERT INTO `absensi` (`id`, `siswa_id`, `tanggal`, `status_absensi`) VALUES
(1, 11, '2025-02-12', 'Izin');

-- --------------------------------------------------------

--
-- Struktur dari tabel `daftar_hadir`
--

CREATE TABLE `daftar_hadir` (
  `id` int(11) NOT NULL,
  `guru_id` int(11) NOT NULL,
  `tanggal_waktu` datetime NOT NULL,
  `status_kehadiran` enum('Hadir','Izin','Sakit','Alpha') NOT NULL,
  `foto_absensi` varchar(255) DEFAULT NULL,
  `lokasi` varchar(255) DEFAULT NULL,
  `status_approval` enum('approved','rejected') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `daftar_hadir`
--

INSERT INTO `daftar_hadir` (`id`, `guru_id`, `tanggal_waktu`, `status_kehadiran`, `foto_absensi`, `lokasi`, `status_approval`) VALUES
(1, 8, '2025-02-12 17:33:29', 'Hadir', '../uploads/bg.jpg', 'Latitude: -7.0191616, Longitude: 107.657059', 'approved'),
(2, 8, '2025-02-12 18:21:36', 'Hadir', '../uploads/transkrip 1.jpg', 'Latitude: -7.0191616, Longitude: 107.657059', 'rejected'),
(3, 8, '2025-02-12 18:26:31', 'Hadir', '../uploads/WhatsApp Image 2025-01-24 at 15.24.11_0df3f5a3.jpg', 'Latitude: -7.0207807, Longitude: 107.6588993', 'approved'),
(4, 8, '2025-02-12 21:09:16', 'Hadir', '../uploads/transkrip 2.jpg', 'Latitude: -7.0191616, Longitude: 107.657059', 'approved');

-- --------------------------------------------------------

--
-- Struktur dari tabel `nilai_pelajaran`
--

CREATE TABLE `nilai_pelajaran` (
  `id` int(11) NOT NULL,
  `siswa_id` int(11) NOT NULL,
  `mata_pelajaran` varchar(255) NOT NULL,
  `nilai` int(11) NOT NULL,
  `jenjang_pendidikan` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `nilai_pelajaran`
--

INSERT INTO `nilai_pelajaran` (`id`, `siswa_id`, `mata_pelajaran`, `nilai`, `jenjang_pendidikan`) VALUES
(5, 5, 'arab', 87, 'SMA'),
(6, 5, 'inggris', 78, 'SMA'),
(7, 9, 'inggris', 55, 'SMA'),
(8, 11, 'arab', 80, 'SD'),
(9, 11, 'inggris', 90, 'SD');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nama_lengkap` varchar(255) NOT NULL,
  `jenis_kelamin` enum('Laki-laki','Perempuan') NOT NULL,
  `ttl` date NOT NULL,
  `no_telp` varchar(20) NOT NULL,
  `email` varchar(255) NOT NULL,
  `jenjang_pendidikan` enum('TK','SD','SMP','SMA') NOT NULL,
  `asal_sekolah` varchar(255) NOT NULL,
  `kelas` varchar(50) NOT NULL,
  `pilihan_bahasa` enum('Arab','Inggris','Keduanya') NOT NULL,
  `no_wali` varchar(20) NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','guru','pelajar') NOT NULL DEFAULT 'pelajar',
  `is_approved` tinyint(1) NOT NULL DEFAULT 0,
  `jumlah_mengajar` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `nama_lengkap`, `jenis_kelamin`, `ttl`, `no_telp`, `email`, `jenjang_pendidikan`, `asal_sekolah`, `kelas`, `pilihan_bahasa`, `no_wali`, `foto`, `password`, `role`, `is_approved`, `jumlah_mengajar`) VALUES
(1, 'Admin', 'Laki-laki', '0000-00-00', '', 'admin@example.com', 'TK', '', '', 'Arab', '', NULL, '$2y$10$rrSLLauWMJK9qcsTbRdc7uRs6cbrTZNfPhFCpKOoFjhcszxqGiqVi', 'admin', 1, 0),
(5, 'Muhammad Faiz Akbar Kamil ', 'Laki-laki', '2002-12-05', '081395163254', 'faiz@gmail.com', 'SMA', 'almuawanah', 'c', 'Arab', '08137248914', 'uploads/1739166564_Faiz.jpg', '$2y$10$hXwWVKJSWQypBGi4tjFulu/MxDRBLX.gNHoeYKdYdO0JH/gE3ujbK', 'pelajar', 1, 0),
(7, 'aldhy', 'Laki-laki', '0000-00-00', '', 'aldhy@gmail.com', 'TK', '', '', 'Arab', '', NULL, '$2y$10$hDFy1bxhJ8T7u8O80kNFkOZ3zax504WvfkQVz9FnAGVa.VYizG57K', 'guru', 1, 0),
(8, 'pais', 'Laki-laki', '0000-00-00', '', 'pais@gmail.com', 'TK', '', '', 'Arab', '', 'uploads/1739170151_faiz_.jpg', '$2y$10$Mj62g2DNo2m3aCkYXE/vTe2OPsIB20JkBxag1125c6jGDjjKqUAZO', 'guru', 1, 2),
(9, 'Shifa', 'Perempuan', '2003-07-20', '081312344321', 'shif@gmail.com', 'SMA', 'poltekpos', 'A', 'Inggris', '081395161234', 'uploads/1739190317_faiz_.png', '$2y$10$jMgKsomd4BHQT/HJ2ZzbJOG82fAV69/xfRV3Ndlk5Y4.h4rf7VbpO', 'pelajar', 1, 0),
(11, 'aripin nur kamil', 'Laki-laki', '2017-01-14', '081395163254', 'aripin@gmail.com', 'SD', 'bintang', 'A', 'Arab', '08137248914', 'uploads/1739390767_faiz_.png', '$2y$10$BD6lET1vYE22PhAKpQlUTOvuCPiEYBaeHMd.xinDcBB4U.JGQDfAq', 'pelajar', 1, 0);

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `absensi`
--
ALTER TABLE `absensi`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `siswa_id` (`siswa_id`,`tanggal`);

--
-- Indeks untuk tabel `daftar_hadir`
--
ALTER TABLE `daftar_hadir`
  ADD PRIMARY KEY (`id`),
  ADD KEY `guru_id` (`guru_id`);

--
-- Indeks untuk tabel `nilai_pelajaran`
--
ALTER TABLE `nilai_pelajaran`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_siswa` (`siswa_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `absensi`
--
ALTER TABLE `absensi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `daftar_hadir`
--
ALTER TABLE `daftar_hadir`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `nilai_pelajaran`
--
ALTER TABLE `nilai_pelajaran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `absensi`
--
ALTER TABLE `absensi`
  ADD CONSTRAINT `absensi_ibfk_1` FOREIGN KEY (`siswa_id`) REFERENCES `users` (`id`);

--
-- Ketidakleluasaan untuk tabel `daftar_hadir`
--
ALTER TABLE `daftar_hadir`
  ADD CONSTRAINT `daftar_hadir_ibfk_1` FOREIGN KEY (`guru_id`) REFERENCES `users` (`id`);

--
-- Ketidakleluasaan untuk tabel `nilai_pelajaran`
--
ALTER TABLE `nilai_pelajaran`
  ADD CONSTRAINT `fk_siswa` FOREIGN KEY (`siswa_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
