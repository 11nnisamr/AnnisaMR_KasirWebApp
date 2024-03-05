-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 05, 2024 at 04:31 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kasir`
--

-- --------------------------------------------------------

--
-- Table structure for table `pelanggan`
--

CREATE TABLE `pelanggan` (
  `pelanggan_id` int(11) NOT NULL,
  `toko_id` int(11) NOT NULL,
  `nama_pelanggan` varchar(50) NOT NULL,
  `alamat` varchar(100) NOT NULL,
  `no_hp` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pelanggan`
--

INSERT INTO `pelanggan` (`pelanggan_id`, `toko_id`, `nama_pelanggan`, `alamat`, `no_hp`, `created_at`) VALUES
(1, 1, 'Budi Supriatna', 'Banjar', '2345678901', '2024-02-11 10:30:22'),
(2, 1, 'Dede sudirman', 'Banjar', '09876543456', '2024-02-14 14:50:53'),
(5, 1, 'Tedi', 'Tasik', '12345678', '2024-02-15 23:21:33'),
(6, 1, 'Bima Sakti', 'Jakarta', '123456789', '2024-03-04 12:33:52'),
(7, 1, 'Rina', 'Banjar', '09876567895', '2024-03-04 15:15:21');

-- --------------------------------------------------------

--
-- Table structure for table `pembelian`
--

CREATE TABLE `pembelian` (
  `pembelian_id` int(11) NOT NULL,
  `toko_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `no_faktur` varchar(50) NOT NULL,
  `tanggal_pembelian` date NOT NULL,
  `suplier_id` int(11) NOT NULL,
  `total` int(11) NOT NULL,
  `bayar` int(11) NOT NULL,
  `sisa` int(11) NOT NULL,
  `keterangan` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pembelian`
--

INSERT INTO `pembelian` (`pembelian_id`, `toko_id`, `user_id`, `no_faktur`, `tanggal_pembelian`, `suplier_id`, `total`, `bayar`, `sisa`, `keterangan`, `created_at`) VALUES
(35, 1, 3, 'PO-NM-20240301-0001', '2024-03-04', 7, 6000, 6000, 0, 'Testing', '2024-03-04 12:12:55'),
(37, 1, 3, 'PO-NM-20240301-0002', '2024-03-04', 8, 350, 350, 0, 'Testing', '2024-03-04 13:37:05'),
(38, 1, 1, '8765434567', '2024-03-04', 11, 1, 1, 0, 'Testing', '2024-03-05 11:21:37'),
(39, 1, 1, '987654345', '2024-03-07', 10, 15, 15, 0, 'Testing', '2024-03-05 14:16:04');

-- --------------------------------------------------------

--
-- Table structure for table `pembelian_detail`
--

CREATE TABLE `pembelian_detail` (
  `beli_detail_id` int(11) NOT NULL,
  `pembelian_id` int(11) NOT NULL,
  `produk_id` int(11) NOT NULL,
  `qty` int(11) NOT NULL,
  `harga_beli` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pembelian_detail`
--

INSERT INTO `pembelian_detail` (`beli_detail_id`, `pembelian_id`, `produk_id`, `qty`, `harga_beli`, `created_at`) VALUES
(28, 35, 21, 100, 2000, '2024-03-04 12:12:55'),
(29, 35, 22, 200, 4000, '2024-03-04 12:12:55'),
(30, 37, 23, 200, 3000, '2024-03-04 13:37:05'),
(31, 37, 24, 150, 6000, '2024-03-04 13:37:05'),
(32, 38, 26, 1, 20000, '2024-03-05 11:21:37'),
(33, 39, 29, 15, 20000, '2024-03-05 14:16:05');

-- --------------------------------------------------------

--
-- Table structure for table `penjualan`
--

CREATE TABLE `penjualan` (
  `penjualan_id` int(11) NOT NULL,
  `toko_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `tanggal_penjualan` date NOT NULL,
  `pelanggan_id` int(11) NOT NULL,
  `total` int(11) NOT NULL,
  `bayar` int(11) NOT NULL,
  `sisa` int(11) NOT NULL,
  `keterangan` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `penjualan`
--

INSERT INTO `penjualan` (`penjualan_id`, `toko_id`, `user_id`, `tanggal_penjualan`, `pelanggan_id`, `total`, `bayar`, `sisa`, `keterangan`, `created_at`) VALUES
(47, 1, 3, '2024-03-04', 0, 29000, 29000, 0, '', '2024-03-04 12:14:45'),
(48, 1, 2, '2024-03-04', 0, 22000, 22000, 0, '', '2024-03-04 13:56:19'),
(49, 1, 2, '2024-03-04', 0, 82500, 82500, 0, '', '2024-03-04 14:05:50'),
(50, 1, 12, '2024-03-04', 0, 30500, 30500, 0, '', '2024-03-04 14:24:30'),
(51, 1, 1, '2024-03-04', 0, 8000, 8000, 0, '', '2024-03-04 14:46:13'),
(52, 1, 1, '2024-03-04', 0, 17000, 17000, 0, '', '2024-03-04 14:48:20'),
(53, 1, 1, '2024-03-04', 0, 6000, 6000, 0, '', '2024-03-04 14:49:40'),
(54, 1, 1, '2024-03-04', 0, 35000, 35000, 0, '', '2024-03-04 15:29:42'),
(55, 1, 1, '2024-03-04', 0, 7000, 7000, 0, '', '2024-03-04 15:31:25'),
(56, 1, 1, '2024-03-04', 0, 6000, 6000, 0, '', '2024-03-04 15:39:37'),
(57, 1, 1, '2024-03-04', 7, 42000, 42000, 0, '', '2024-03-04 15:49:58'),
(58, 1, 1, '2024-03-04', 6, 15000, 15000, 0, '', '2024-03-04 16:12:10'),
(59, 1, 12, '2024-03-04', 0, 12000, 12000, 0, '', '2024-03-04 16:18:08'),
(60, 1, 12, '2024-03-04', 0, 8500, 8500, 0, '', '2024-03-04 16:21:47'),
(61, 1, 15, '2024-03-05', 0, 10500, 10500, 0, '', '2024-03-05 11:31:56');

-- --------------------------------------------------------

--
-- Table structure for table `penjualan_detail`
--

CREATE TABLE `penjualan_detail` (
  `penjualan_detail_id` int(11) NOT NULL,
  `penjualan_id` int(11) NOT NULL,
  `produk_id` int(11) NOT NULL,
  `qty` int(11) NOT NULL,
  `harga_beli` int(11) NOT NULL,
  `harga_jual` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `penjualan_detail`
--

INSERT INTO `penjualan_detail` (`penjualan_detail_id`, `penjualan_id`, `produk_id`, `qty`, `harga_beli`, `harga_jual`, `created_at`) VALUES
(68, 47, 21, 3, 0, 3000, '2024-03-04 12:14:45'),
(69, 47, 22, 4, 0, 5000, '2024-03-04 12:14:45'),
(70, 48, 21, 4, 0, 3000, '2024-03-04 13:56:19'),
(71, 48, 22, 2, 0, 5000, '2024-03-04 13:56:19'),
(72, 49, 21, 4, 0, 3000, '2024-03-04 14:05:50'),
(73, 49, 22, 5, 0, 5000, '2024-03-04 14:05:50'),
(74, 49, 23, 5, 0, 3500, '2024-03-04 14:05:50'),
(75, 49, 24, 4, 0, 7000, '2024-03-04 14:05:50'),
(76, 50, 21, 4, 0, 3000, '2024-03-04 14:24:30'),
(77, 50, 22, 3, 0, 5000, '2024-03-04 14:24:30'),
(78, 50, 23, 1, 0, 3500, '2024-03-04 14:24:30'),
(79, 51, 21, 1, 0, 3000, '2024-03-04 14:46:13'),
(80, 51, 22, 1, 0, 5000, '2024-03-04 14:46:13'),
(81, 52, 22, 2, 0, 5000, '2024-03-04 14:48:20'),
(82, 52, 23, 2, 0, 3500, '2024-03-04 14:48:20'),
(83, 53, 21, 2, 0, 3000, '2024-03-04 14:49:40'),
(84, 54, 23, 10, 0, 3500, '2024-03-04 15:29:43'),
(85, 55, 23, 2, 0, 3500, '2024-03-04 15:31:25'),
(86, 56, 21, 2, 0, 3000, '2024-03-04 15:39:37'),
(87, 57, 24, 6, 0, 7000, '2024-03-04 15:49:59'),
(88, 58, 22, 3, 0, 5000, '2024-03-04 16:12:10'),
(89, 59, 22, 1, 0, 5000, '2024-03-04 16:18:08'),
(90, 59, 24, 1, 0, 7000, '2024-03-04 16:18:08'),
(91, 60, 22, 1, 0, 5000, '2024-03-04 16:21:47'),
(92, 60, 23, 1, 0, 3500, '2024-03-04 16:21:47'),
(93, 61, 23, 1, 0, 3500, '2024-03-05 11:31:56'),
(94, 61, 24, 1, 0, 7000, '2024-03-05 11:31:57');

-- --------------------------------------------------------

--
-- Table structure for table `produk`
--

CREATE TABLE `produk` (
  `produk_id` int(11) NOT NULL,
  `toko_id` int(11) NOT NULL,
  `suplier_id` int(11) NOT NULL,
  `nama_produk` varchar(100) NOT NULL,
  `kategori_id` int(11) NOT NULL,
  `satuan` varchar(50) NOT NULL,
  `harga_beli` int(11) NOT NULL,
  `harga_jual` int(11) NOT NULL,
  `jumlah_produk` int(11) NOT NULL,
  `created_at` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `produk`
--

INSERT INTO `produk` (`produk_id`, `toko_id`, `suplier_id`, `nama_produk`, `kategori_id`, `satuan`, `harga_beli`, `harga_jual`, `jumlah_produk`, `created_at`) VALUES
(21, 1, 7, 'Indomie Kari Ayam', 1, 'PCS', 2000, 3000, 80, '2024-03-04'),
(22, 1, 7, 'Mie Sedap Rendang', 1, 'PCS', 4000, 5000, 178, '2024-03-04'),
(23, 1, 8, 'kopi kapal api', 2, 'PCS', 3000, 3500, 178, '2024-03-04'),
(24, 1, 8, 'Sabun Sunlight', 4, 'PCS', 6000, 7000, 138, '2024-03-04'),
(29, 1, 10, 'Attack Deterjen', 4, 'PCS', 20000, 25000, 15, '2024-03-05');

-- --------------------------------------------------------

--
-- Table structure for table `produk_kategori`
--

CREATE TABLE `produk_kategori` (
  `kategori_id` int(11) NOT NULL,
  `nama_kategori` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `produk_kategori`
--

INSERT INTO `produk_kategori` (`kategori_id`, `nama_kategori`, `created_at`) VALUES
(1, 'Mie', '2024-03-04 15:03:13'),
(2, 'Kopi', '2024-02-09 03:04:19'),
(3, 'Roti', '2024-02-11 08:04:34'),
(4, 'Sabun Cuci', '2024-02-15 23:31:50'),
(6, 'Minuman Kaleng', '2024-03-04 15:03:51');

-- --------------------------------------------------------

--
-- Table structure for table `suplier`
--

CREATE TABLE `suplier` (
  `suplier_id` int(11) NOT NULL,
  `toko_id` int(11) NOT NULL,
  `produk_id` int(11) NOT NULL,
  `nama_suplier` varchar(50) NOT NULL,
  `tlp_hp` varchar(50) NOT NULL,
  `alamat_suplier` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `suplier`
--

INSERT INTO `suplier` (`suplier_id`, `toko_id`, `produk_id`, `nama_suplier`, `tlp_hp`, `alamat_suplier`, `created_at`) VALUES
(7, 1, 0, 'PT Zahra Sejahtera', '123456789', 'Banjar', '2024-03-04 14:59:37'),
(8, 1, 0, 'PT ABC', '9876543', 'Bandung', '2024-03-04 12:11:02'),
(9, 1, 0, 'PT BCD', '123456782', 'Banjar', '2024-03-04 15:02:34'),
(10, 1, 0, 'PT Galaksi Satu', '7654345678', 'Garut', '2024-03-05 11:08:30'),
(14, 1, 0, 'PT Galaksi ', '7654345678', 'Garut', '2024-03-05 14:08:59');

-- --------------------------------------------------------

--
-- Table structure for table `toko`
--

CREATE TABLE `toko` (
  `toko_id` int(11) NOT NULL,
  `nama_toko` varchar(50) NOT NULL,
  `alamat` varchar(100) NOT NULL,
  `tlp_hp` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `toko`
--

INSERT INTO `toko` (`toko_id`, `nama_toko`, `alamat`, `tlp_hp`, `created_at`) VALUES
(1, 'Serba Ada', 'Banjar', '089656677665', '2024-02-09 03:02:20');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `user_id` int(11) NOT NULL,
  `toko_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(50) NOT NULL,
  `nama_lengkap` varchar(50) NOT NULL,
  `alamat` varchar(100) NOT NULL,
  `access_level` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`user_id`, `toko_id`, `username`, `password`, `email`, `nama_lengkap`, `alamat`, `access_level`, `created_at`) VALUES
(1, 1, 'nisa', '12345', 'annisa@gmail.com', 'annisa mr', 'banjar', 'admin', '2024-02-09 03:00:07'),
(2, 1, 'inosuke', '12345', 'rian@gmail.com', 'laisa', 'banjar', 'cashier', '2024-03-05 10:55:15'),
(3, 1, 'super_admin', '54321', '', 'Super Admin', 'banjar', 'admin', '2024-03-05 14:33:26'),
(12, 0, 'erwan', '123', '', 'Erwan Abdullah', '', 'cashier', '2024-03-05 14:48:50'),
(15, 0, 'fara', '12345', '', 'faraaa', '', 'cashier', '2024-03-04 14:58:58');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `pelanggan`
--
ALTER TABLE `pelanggan`
  ADD PRIMARY KEY (`pelanggan_id`),
  ADD KEY `toko_id` (`toko_id`);

--
-- Indexes for table `pembelian`
--
ALTER TABLE `pembelian`
  ADD PRIMARY KEY (`pembelian_id`),
  ADD KEY `toko_id` (`toko_id`,`user_id`,`suplier_id`),
  ADD KEY `suplier_id` (`suplier_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `pembelian_detail`
--
ALTER TABLE `pembelian_detail`
  ADD PRIMARY KEY (`beli_detail_id`),
  ADD KEY `pembelian_id` (`pembelian_id`,`produk_id`),
  ADD KEY `produk_id` (`produk_id`);

--
-- Indexes for table `penjualan`
--
ALTER TABLE `penjualan`
  ADD PRIMARY KEY (`penjualan_id`),
  ADD KEY `toko_id` (`toko_id`,`user_id`,`pelanggan_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `pelanggan_id` (`pelanggan_id`);

--
-- Indexes for table `penjualan_detail`
--
ALTER TABLE `penjualan_detail`
  ADD PRIMARY KEY (`penjualan_detail_id`),
  ADD KEY `penjualan_id` (`penjualan_id`,`produk_id`),
  ADD KEY `produk_id` (`produk_id`);

--
-- Indexes for table `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`produk_id`),
  ADD KEY `toko_id` (`toko_id`,`kategori_id`),
  ADD KEY `kategori_id` (`kategori_id`);

--
-- Indexes for table `produk_kategori`
--
ALTER TABLE `produk_kategori`
  ADD PRIMARY KEY (`kategori_id`);

--
-- Indexes for table `suplier`
--
ALTER TABLE `suplier`
  ADD PRIMARY KEY (`suplier_id`),
  ADD KEY `toko_id` (`toko_id`);

--
-- Indexes for table `toko`
--
ALTER TABLE `toko`
  ADD PRIMARY KEY (`toko_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `toko_id` (`toko_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `pelanggan`
--
ALTER TABLE `pelanggan`
  MODIFY `pelanggan_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `pembelian`
--
ALTER TABLE `pembelian`
  MODIFY `pembelian_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `pembelian_detail`
--
ALTER TABLE `pembelian_detail`
  MODIFY `beli_detail_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `penjualan`
--
ALTER TABLE `penjualan`
  MODIFY `penjualan_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `penjualan_detail`
--
ALTER TABLE `penjualan_detail`
  MODIFY `penjualan_detail_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=95;

--
-- AUTO_INCREMENT for table `produk`
--
ALTER TABLE `produk`
  MODIFY `produk_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `produk_kategori`
--
ALTER TABLE `produk_kategori`
  MODIFY `kategori_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `suplier`
--
ALTER TABLE `suplier`
  MODIFY `suplier_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `toko`
--
ALTER TABLE `toko`
  MODIFY `toko_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
