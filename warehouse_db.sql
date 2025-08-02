-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 02 Agu 2025 pada 17.01
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
-- Database: `warehouse_db`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `sku` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `category` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `unit` varchar(20) DEFAULT NULL,
  `stock` int(11) DEFAULT 0,
  `min_stock` int(11) DEFAULT 5,
  `location` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `products`
--

INSERT INTO `products` (`id`, `sku`, `name`, `category`, `description`, `unit`, `stock`, `min_stock`, `location`) VALUES
(110, 'WP20250801A1B2', 'Sepatu Nike Air Max', 'Olahraga', 'Sepatu olahraga ringan dan nyaman', 'pcs', 50, 10, 'GUDANG 3'),
(111, 'WP20250801C3D4', 'Sepatu Adidas Ultraboost', 'Olahraga', 'Desain modern dan empuk', 'pcs', 50, 10, 'GUDANG 1'),
(112, 'WP20250801E5F6', 'Sepatu Converse Chuck Taylor', 'Kasual', 'Sepatu klasik berbahan canvas', 'pcs', 40, 30, 'GUDANG 2'),
(113, 'WP20250801G7H8', 'Sepatu Vans Old Skool', 'Kasual', 'Sepatu dengan desain retro', 'pcs', 40, 5, 'GUDANG 4'),
(114, 'WP20250801I9J0', 'Sepatu Compass Gazelle', 'Kasual', 'Brand lokal dengan gaya kekinian', 'pcs', 5, 5, 'GUDANG 2'),
(115, 'WP20250801K1L2', 'Sepatu Puma RS-X', 'Olahraga', 'Teknologi bantalan modern', 'pcs', 52, 5, 'GUDANG 4'),
(116, 'WP20250801M3N4', 'Sepatu Reebok Classic', 'Kasual', 'Model retro klasik', 'pcs', 35, 5, 'GUDANG 4'),
(117, 'WP20250801O5P6', 'Sepatu New Balance 574', 'Kasual', 'Kenyamanan maksimal', 'pcs', 28, 5, 'GUDANG 3'),
(118, 'WP20250801Q7R8', 'Sepatu Asics Gel Kayano', 'Olahraga', 'Didesain untuk pelari profesional', 'pcs', 22, 5, 'GUDANG 1'),
(119, 'WP20250801S9T0', 'Sepatu Fila Disruptor', 'Fashion', 'Desain chunky dan trendi', 'pcs', 4, 5, 'GUDANG 2'),
(120, 'WP20250801U1V2', 'Sepatu Ardiles Sport', 'Olahraga', 'Brand lokal ekonomis', 'pcs', 50, 10, 'GUDANG 1'),
(121, 'WP20250801W3X4', 'Sepatu Eagle Running', 'Olahraga', 'Ringan dan fleksibel untuk lari', 'pcs', 10, 13, 'GUDANG 2'),
(122, 'WP20250801Y5Z6', 'Sepatu League Volt', 'Olahraga', 'Pilihan pelajar dan atlet sekolah', 'pcs', 43, 8, 'GUDANG 3'),
(123, 'WP20250801A7B8', 'Sepatu Mizuno Wave', 'Olahraga', 'Sepatu profesional pelari maraton', 'pcs', 7, 7, 'GUDANG 3'),
(124, 'WP20250801C9D0', 'Sepatu Nike Jordan 1', 'Fashion', 'Sepatu ikonik dan collectible', 'pcs', 20, 2, 'GUDANG 4'),
(125, 'WP20250801E1F2', 'Sepatu Bata Formal', 'Formal', 'Untuk ke kantor atau acara resmi', 'pcs', 22, 5, 'GUDANG 1'),
(126, 'WP20250801G3H4', 'Sepatu Kickers Kulit', 'Formal', 'Cocok untuk sekolah atau kerja', 'pcs', 35, 5, 'GUDANG 2'),
(127, 'WP20250801I5J6', 'Sepatu Tomkins Anak', 'Anak-anak', 'Sepatu sekolah anak SD', 'pcs', 35, 8, 'GUDANG 4'),
(128, 'WP20250801K7L8', 'Sepatu Homyped Comfort', 'Kasual', 'Nyaman untuk lansia dan dewasa', 'pcs', 18, 4, 'GUDANG 2'),
(129, 'WP20250801M9N0', 'Sepatu Brodo Derby', 'Formal', 'Sepatu kulit pria buatan lokal', 'pcs', 0, 4, 'GUDANG 1');

-- --------------------------------------------------------

--
-- Struktur dari tabel `suppliers`
--

CREATE TABLE `suppliers` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `contact` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `suppliers`
--

INSERT INTO `suppliers` (`id`, `name`, `contact`, `phone`, `email`, `address`) VALUES
(22, 'PT. Sepatu Nusantara', 'Budi Santoso', '081234567890', 'budi@sepatusnusantara.co.id', 'Jl. Raya Industri No. 88, Bekasi, Jawa Barat'),
(23, 'CV. Karya Mandiri', 'Siti Aminah', '085612345678', 'siti@karyamandiri.co.id', 'Jl. Cempaka Putih No. 10, Jakarta Pusat'),
(24, 'PT. Sportindo Jaya', 'Andre Wijaya', '087812345678', 'andre@sportindo.id', 'Jl. Sudirman No. 45, Bandung, Jawa Barat'),
(25, 'CV. Prima Olahraga', 'Dewi Lestari', '082112345678', 'dewi@primaolahraga.id', 'Jl. Merdeka No. 23, Semarang, Jawa Tengah'),
(26, 'PT. Sol Sepatu Indonesia', 'Agus Prabowo', '081298765432', 'agus@solsepatu.co.id', 'Jl. Dr. Sutomo No. 11, Surabaya, Jawa Timur'),
(27, 'UD. Aneka Sepatu', 'Rina Marlina', '089912345678', 'rina@anekasepatu.com', 'Jl. Gatot Subroto No. 56, Medan, Sumatera Utara'),
(28, 'PT. Footwear Global', 'Hendra Saputra', '081355566677', 'hendra@footwearglobal.com', 'Jl. Asia Afrika No. 100, Jakarta Selatan'),
(29, 'CV. Indo Sepatu Mandiri', 'Yusuf Maulana', '082233344455', 'yusuf@indosepatu.id', 'Jl. Kaliurang KM 7, Sleman, Yogyakarta'),
(30, 'PT. Global Kicks', 'Melati Ayu', '087744556699', 'melati@globalkicks.com', 'Jl. Imam Bonjol No. 9, Denpasar, Bali'),
(31, 'CV. Sepatu Kita', 'Doni Setiawan', '085733322211', 'doni@sepatukita.id', 'Jl. Ahmad Yani No. 22, Malang, Jawa Timur');

-- --------------------------------------------------------

--
-- Struktur dari tabel `transactions`
--

CREATE TABLE `transactions` (
  `id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `type` enum('in','out') NOT NULL,
  `quantity` int(11) NOT NULL,
  `date` datetime DEFAULT current_timestamp(),
  `user_id` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `supplier_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `transactions`
--

INSERT INTO `transactions` (`id`, `product_id`, `type`, `quantity`, `date`, `user_id`, `notes`, `supplier_id`) VALUES
(80, 113, 'in', 20, '2025-08-01 09:00:00', 1, 'Pengadaan sepatu Nike dari supplier', 27),
(81, 126, 'in', 15, '2025-08-01 09:30:00', 1, 'Stok Adidas Ultraboost masuk', 26),
(82, 124, 'in', 10, '2025-08-01 10:00:00', 1, 'Barang Jordan diterima', 24),
(83, 115, 'in', 25, '2025-08-01 10:30:00', 1, 'Stok Vans masuk', 24),
(84, 119, 'in', 30, '2025-08-01 11:00:00', 1, 'Barang Compass Gazelle restock', 22),
(85, 122, 'in', 18, '2025-08-01 11:30:00', 1, 'Stok Puma RS-X diterima', 30),
(86, 115, 'in', 12, '2025-08-01 12:00:00', 1, 'Barang Reebok Classic masuk', 31),
(87, 112, 'in', 20, '2025-08-01 12:30:00', 1, 'Pengiriman New Balance 574 diterima', 31),
(88, 118, 'in', 10, '2025-08-01 13:00:00', 1, 'Stok Asics Gel Kayano tiba', 23),
(89, 111, 'in', 15, '2025-08-01 13:30:00', 1, 'Barang Fila Disruptor masuk gudang', 29),
(90, 123, 'out', 5, '2025-08-02 09:00:00', 1, 'Penjualan sepatu Nike ke toko A', NULL),
(91, 122, 'out', 8, '2025-08-02 09:30:00', 1, 'Penjualan Adidas ke cabang Jakarta', NULL),
(92, 121, 'out', 6, '2025-08-02 10:00:00', 1, 'Pengiriman sepatu Converse untuk reseller', NULL),
(93, 112, 'out', 10, '2025-08-02 10:30:00', 1, 'Distribusi Vans Old Skool', NULL),
(94, 114, 'out', 20, '2025-08-02 11:00:00', 1, 'Sepatu Compass dikirim ke mitra', NULL),
(95, 129, 'out', 16, '2025-08-02 11:30:00', 1, 'Pengeluaran sepatu Puma untuk promosi', NULL),
(96, 125, 'out', 3, '2025-08-02 12:00:00', 1, 'Penjualan Reebok Classic offline', NULL),
(97, 118, 'out', 6, '2025-08-02 12:30:00', 1, 'Order New Balance dari marketplace', NULL),
(98, 120, 'out', 10, '2025-08-02 13:00:00', 1, 'Penggunaan internal sepatu Asics', NULL),
(99, 111, 'out', 5, '2025-08-02 13:30:00', 1, 'Pengiriman Fila Disruptor ke toko B', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','staff','viewer') NOT NULL,
  `fullname` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `fullname`) VALUES
(1, 'admin', '123', 'admin', 'Nanang'),
(2, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'Administrator'),
(3, 'admin2', '$2y$10$LeAAuHF4VYoHP7ViWngSSO4Gzt3AKbxx/mJ0aXjfwKp8WeMZmFZUq', 'staff', 'Nanang');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sku` (`sku`);

--
-- Indeks untuk tabel `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `supplier_id` (`supplier_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=130;

--
-- AUTO_INCREMENT untuk tabel `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT untuk tabel `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `transactions_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `transactions_ibfk_3` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
