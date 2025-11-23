-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 23 Nov 2025 pada 13.24
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_inventaris_telkom`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `barang`
--

CREATE TABLE `barang` (
  `id_barang` int(11) NOT NULL,
  `id_kategori` int(11) DEFAULT NULL,
  `nama_barang` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `stok_tersedia` int(11) DEFAULT 0,
  `stok_rusak` int(11) DEFAULT 0,
  `gambar` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `barang`
--

INSERT INTO `barang` (`id_barang`, `id_kategori`, `nama_barang`, `deskripsi`, `stok_tersedia`, `stok_rusak`, `gambar`) VALUES
(1, 1, 'Laptop ASUS ROG', 'Laptop spesifikasi tinggi untuk keperluan jurusan RPL dan Multimedia.', 5, 0, 'images.jpg'),
(2, 1, 'Kamera Canon DSLR', 'Kamera untuk dokumentasi kegiatan OSIS dan Ekskul.', 3, 0, ''),
(3, 1, 'Proyektor Epson', 'Infocus untuk kegiatan belajar mengajar di kelas.', 10, 0, ''),
(4, 2, 'Sapu Ijuk', 'Alat kebersihan kelas.', 17, 8, ''),
(6, 1, 'HDMI', '1234', 123, 0, 'download.jpg'),
(7, 2, 'adasd', 'dasda', 44, 0, 'Screenshot (1).png');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kategori`
--

CREATE TABLE `kategori` (
  `id_kategori` int(11) NOT NULL,
  `nama_kategori` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kategori`
--

INSERT INTO `kategori` (`id_kategori`, `nama_kategori`) VALUES
(1, 'Elektronik'),
(2, 'Alat Kebersihan');

-- --------------------------------------------------------

--
-- Struktur dari tabel `peminjaman`
--

CREATE TABLE `peminjaman` (
  `id_peminjaman` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `id_barang` int(11) DEFAULT NULL,
  `tanggal_pinjam` date DEFAULT NULL,
  `tanggal_kembali` date DEFAULT NULL,
  `tanggal_real_kembali` date DEFAULT NULL,
  `jumlah` int(11) DEFAULT 1,
  `status` enum('pending','disetujui','ditolak','dikembalikan') DEFAULT 'pending',
  `kondisi_kembali` enum('baik','rusak') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `peminjaman`
--

INSERT INTO `peminjaman` (`id_peminjaman`, `id_user`, `id_barang`, `tanggal_pinjam`, `tanggal_kembali`, `tanggal_real_kembali`, `jumlah`, `status`, `kondisi_kembali`) VALUES
(1, 3, 4, '2025-11-22', '2025-11-22', '2025-11-22', 3, 'dikembalikan', 'rusak'),
(2, 4, 2, '2025-11-22', '2025-11-28', '2025-11-22', 2, 'dikembalikan', 'baik'),
(3, 4, 2, '2025-11-22', '2025-12-04', '2025-11-22', 1, 'dikembalikan', 'baik'),
(4, 4, 7, '2025-11-23', '2025-11-23', NULL, 1, 'pending', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `nomor_induk` varchar(50) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin','superadmin') DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id_user`, `nomor_induk`, `nama_lengkap`, `password`, `role`, `created_at`) VALUES
(1, '999', 'Superadmin Telkom', '$2y$10$uEsDYTc5TYVKlq/iWAm7Q.t8JmV1Vo2p/f2ZYrTDGofJ2xDN0l5/K', 'superadmin', '2025-11-22 03:59:49'),
(2, '888', 'Admin Sarpras', '$2y$10$uEsDYTc5TYVKlq/iWAm7Q.t8JmV1Vo2p/f2ZYrTDGofJ2xDN0l5/K', 'admin', '2025-11-22 03:59:49'),
(3, '12345678', 'Ananta', '$2y$10$B1gbKRDDFp4n7KrIm6vmtuk/aPXSqFdExfi2H1Jq7ogUTvEUl1X/O', 'user', '2025-11-22 04:06:00'),
(4, '1212', 'Rere', '$2y$10$XqOSvvQZEVvKef6wN/zrD.VhcIuC5m5t0CTFNFS1m90Irl52Qy7U2', 'user', '2025-11-22 04:29:00');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `barang`
--
ALTER TABLE `barang`
  ADD PRIMARY KEY (`id_barang`),
  ADD KEY `id_kategori` (`id_kategori`);

--
-- Indeks untuk tabel `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id_kategori`);

--
-- Indeks untuk tabel `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD PRIMARY KEY (`id_peminjaman`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_barang` (`id_barang`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `nomor_induk` (`nomor_induk`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `barang`
--
ALTER TABLE `barang`
  MODIFY `id_barang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id_kategori` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `peminjaman`
--
ALTER TABLE `peminjaman`
  MODIFY `id_peminjaman` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `barang`
--
ALTER TABLE `barang`
  ADD CONSTRAINT `barang_ibfk_1` FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id_kategori`) ON DELETE SET NULL;

--
-- Ketidakleluasaan untuk tabel `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD CONSTRAINT `peminjaman_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`),
  ADD CONSTRAINT `peminjaman_ibfk_2` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id_barang`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
