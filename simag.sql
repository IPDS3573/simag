-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 17 Jan 2023 pada 01.28
-- Versi server: 10.4.25-MariaDB
-- Versi PHP: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `simag`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `absen`
--

CREATE TABLE `absen` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `datang` time NOT NULL,
  `pulang` time NOT NULL,
  `latitude` float NOT NULL,
  `longitude` float NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `absen`
--

INSERT INTO `absen` (`id`, `user_id`, `date`, `datang`, `pulang`, `latitude`, `longitude`, `created_at`, `updated_at`) VALUES
(124, 66, '2022-06-20', '10:41:06', '00:00:00', -8.00161, 112.621, '2022-06-20 10:41:06', '2022-06-20 10:41:35'),
(125, 63, '2022-06-20', '10:44:46', '00:00:00', 0, 0, '2022-06-20 10:44:46', '2022-06-20 10:44:46'),
(128, 94, '2022-11-11', '00:00:00', '14:18:47', -8.17331, 113.701, '2022-11-11 14:18:41', '2022-11-11 14:18:47');

-- --------------------------------------------------------

--
-- Struktur dari tabel `aktivitas`
--

CREATE TABLE `aktivitas` (
  `id` int(11) NOT NULL,
  `userId` int(11) NOT NULL,
  `date` date NOT NULL,
  `mulai` time NOT NULL,
  `selesai` time NOT NULL,
  `keterangan` varchar(500) NOT NULL,
  `status` int(1) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `Nilai` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `aktivitas`
--

INSERT INTO `aktivitas` (`id`, `userId`, `date`, `mulai`, `selesai`, `keterangan`, `status`, `created_at`, `updated_at`, `Nilai`) VALUES
(39, 94, '2022-11-11', '14:19:00', '15:19:00', 'lzm', 2, '2022-11-11 01:19:14', '2022-11-11 02:01:01', ''),
(46, 105, '2022-11-29', '01:26:00', '03:26:00', 'ygwqghqd', 2, '2022-11-29 08:26:41', '2022-11-29 08:27:01', ''),
(47, 105, '2022-11-29', '21:32:00', '01:32:00', 'PERTAMA', 0, '2022-11-29 08:33:11', '2022-11-29 08:33:11', '');

-- --------------------------------------------------------

--
-- Struktur dari tabel `divisi`
--

CREATE TABLE `divisi` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `quota` int(11) NOT NULL,
  `pembimbing` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `divisi`
--

INSERT INTO `divisi` (`id`, `name`, `quota`, `pembimbing`, `created_at`, `updated_at`) VALUES
(18, 'Divisi Distribusi', 11, 64, '2023-01-11 12:21:33', '2023-01-11 12:21:33'),
(19, 'Test 2', 22, 113, '2023-01-11 12:31:32', '2023-01-11 12:31:32'),
(21, 'Divisi Neraca', 5, 0, '2023-01-12 11:14:42', '2023-01-12 11:14:42'),
(22, 'Divisi Neraca', 88, 94, '2023-01-12 13:45:37', '2023-01-12 13:45:37');

-- --------------------------------------------------------

--
-- Struktur dari tabel `info_peserta`
--

CREATE TABLE `info_peserta` (
  `id` int(11) NOT NULL,
  `instansi` varchar(100) NOT NULL,
  `foto` varchar(255) NOT NULL DEFAULT '/assets/img/peserta.png',
  `startDate` date NOT NULL,
  `endDate` date NOT NULL,
  `pengantar` varchar(256) NOT NULL,
  `proposal` varchar(256) NOT NULL,
  `ket` text DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `userId` int(11) NOT NULL,
  `statuspgj` enum('diterima','ditolak','diproses') NOT NULL DEFAULT 'diproses'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `info_peserta`
--

INSERT INTO `info_peserta` (`id`, `instansi`, `foto`, `startDate`, `endDate`, `pengantar`, `proposal`, `ket`, `created_at`, `updated_at`, `userId`, `statuspgj`) VALUES
(86, 'polije2', '/assets/img/peserta.png', '2023-01-19', '2023-03-15', '/assets/dokumen pengantar/116/Lembar Pengesahan-5-1.pdf', '/assets/dokumen proposal/116/Lembar Pengesahan-5-1.pdf', 'Diterima yaaaa', '2023-01-12 00:47:52', '2023-01-12 00:48:45', 116, 'diterima');

-- --------------------------------------------------------

--
-- Struktur dari tabel `report`
--

CREATE TABLE `report` (
  `id` int(11) NOT NULL,
  `idduser` int(10) NOT NULL,
  `pengetahuan` int(10) NOT NULL,
  `keterampilan` int(10) NOT NULL,
  `kemampuan` int(10) NOT NULL,
  `disiplin` int(10) NOT NULL,
  `total` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `report`
--

INSERT INTO `report` (`id`, `idduser`, `pengetahuan`, `keterampilan`, `kemampuan`, `disiplin`, `total`) VALUES
(4, 93, 9, 9, 9, 9, 9),
(5, 94, 10, 8, 9, 8, 8),
(6, 97, 9, 9, 9, 9, 9),
(7, 105, 9, 7, 7, 8, 3),
(8, 115, 10, 10, 10, 10, 10),
(9, 116, 9, 9, 99, 9, 9);

-- --------------------------------------------------------

--
-- Struktur dari tabel `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `jenisKelamin` int(1) NOT NULL,
  `tglLahir` date NOT NULL,
  `role` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  `status` int(1) NOT NULL DEFAULT 0,
  `divisi` varchar(255) NOT NULL DEFAULT 'NO DIVISION'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data untuk tabel `user`
--

INSERT INTO `user` (`id`, `nama`, `email`, `jenisKelamin`, `tglLahir`, `role`, `created_at`, `updated_at`, `status`, `divisi`) VALUES
(33, 'Admin Viona ', 'vionafebriana@gmail.com', 2, '1999-02-20', 1, '2022-02-24 09:09:23', '2022-12-04 09:05:54', 2, 'NO DIVISION'),
(63, 'Peserta 1 ', 'peserta1stis@gmail.com', 2, '2002-01-11', 3, '2022-06-19 08:46:44', '2023-01-11 00:16:16', 2, '19'),
(64, 'Pembimbing 1', 'pembimbing1stis@gmail.com', 1, '1999-06-10', 2, '2022-06-19 08:55:07', '2023-01-10 23:21:33', 2, 'Test'),
(66, 'Bima Sakti Krisdianto', 'bimasakti.kr@gmail.com', 1, '1995-11-06', 3, '2022-06-19 22:37:40', '2023-01-11 00:19:18', 2, '19'),
(94, 'iug', 'e31201536@student.polije.ac.id', 2, '2022-11-11', 2, '2022-11-11 01:17:33', '2023-01-12 00:45:37', 2, 'Divisi Neraca'),
(105, 'icak', 'vrizchaulia182@gmail.com', 1, '2022-11-29', 1, '2022-11-29 08:04:42', '2022-11-29 08:24:49', 2, 'NO DIVISION'),
(107, 'rizal', 'khoirurrizal07@gmail.com', 1, '2022-12-14', 4, '2022-12-04 12:20:12', '2022-12-04 12:20:12', 2, 'NO DIVISION'),
(110, 'coba 1', 'vrizchaaulia182@gmail.com', 1, '2022-12-13', 3, '2022-12-13 02:41:17', '2023-01-11 00:28:50', 1, '20'),
(116, 'khoirur', 'khoirurrizall15@gmail.com', 1, '2023-01-26', 3, '2023-01-12 00:47:03', '2023-01-12 00:49:42', 2, '21');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `absen`
--
ALTER TABLE `absen`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indeks untuk tabel `aktivitas`
--
ALTER TABLE `aktivitas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `userId` (`userId`);

--
-- Indeks untuk tabel `divisi`
--
ALTER TABLE `divisi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pembimbing` (`pembimbing`);

--
-- Indeks untuk tabel `info_peserta`
--
ALTER TABLE `info_peserta`
  ADD PRIMARY KEY (`id`),
  ADD KEY `userId` (`userId`);

--
-- Indeks untuk tabel `report`
--
ALTER TABLE `report`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user(id)` (`idduser`);

--
-- Indeks untuk tabel `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD KEY `divisi` (`divisi`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `absen`
--
ALTER TABLE `absen`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=129;

--
-- AUTO_INCREMENT untuk tabel `aktivitas`
--
ALTER TABLE `aktivitas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT untuk tabel `divisi`
--
ALTER TABLE `divisi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT untuk tabel `info_peserta`
--
ALTER TABLE `info_peserta`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=87;

--
-- AUTO_INCREMENT untuk tabel `report`
--
ALTER TABLE `report`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=117;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `absen`
--
ALTER TABLE `absen`
  ADD CONSTRAINT `absen_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `aktivitas`
--
ALTER TABLE `aktivitas`
  ADD CONSTRAINT `aktivitas_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `info_peserta`
--
ALTER TABLE `info_peserta`
  ADD CONSTRAINT `info_peserta_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
