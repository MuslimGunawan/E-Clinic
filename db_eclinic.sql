-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 25, 2025 at 05:31 AM
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
-- Database: `db_eclinic`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`ramell`@`%` PROCEDURE `sp_input_resep` (IN `p_id_rm` INT, IN `p_id_obat` INT, IN `p_jumlah` INT)   BEGIN
    DECLARE v_stok INT;
    DECLARE v_harga DECIMAL(10,2);
    DECLARE EXIT HANDLER FOR SQLEXCEPTION
    BEGIN
        -- Jika error, batalkan semua
        ROLLBACK;
        SELECT 'Transaksi Gagal: Error SQL' AS Pesan;
    END;

    -- Mulai Transaksi
    START TRANSACTION;

    -- Cek stok saat ini (Read)
    SELECT stok, harga INTO v_stok, v_harga FROM obat WHERE id_obat = p_id_obat FOR UPDATE;

    IF v_stok >= p_jumlah THEN
        -- 1. Kurangi Stok (Update)
        UPDATE obat SET stok = stok - p_jumlah WHERE id_obat = p_id_obat;

        -- 2. Masukkan ke Detail Resep (Insert)
        INSERT INTO detail_resep (id_rm, id_obat, jumlah, subtotal)
        VALUES (p_id_rm, p_id_obat, p_jumlah, (v_harga * p_jumlah));

        -- Simpan permanen
        COMMIT;
        SELECT 'Resep Berhasil Disimpan' AS Pesan;
    ELSE
        -- Stok tidak cukup, batalkan
        ROLLBACK;
        SELECT 'Gagal: Stok Obat Tidak Cukup' AS Pesan;
    END IF;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `detail_resep`
--

CREATE TABLE `detail_resep` (
  `id_resep` int(11) NOT NULL,
  `id_rm` int(11) NOT NULL,
  `id_obat` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `subtotal` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `detail_resep`
--

INSERT INTO `detail_resep` (`id_resep`, `id_rm`, `id_obat`, `jumlah`, `subtotal`) VALUES
(1, 1, 1, 2, 10000.00),
(2, 2, 4, 2, 91600.00);

-- --------------------------------------------------------

--
-- Table structure for table `dokter`
--

CREATE TABLE `dokter` (
  `id_dokter` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `nama_dokter` varchar(100) NOT NULL,
  `spesialisasi` varchar(50) DEFAULT NULL,
  `no_hp` varchar(15) DEFAULT NULL,
  `id_poli` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `obat`
--

CREATE TABLE `obat` (
  `id_obat` int(11) NOT NULL,
  `nama_obat` varchar(100) NOT NULL,
  `jenis` varchar(50) DEFAULT NULL,
  `stok` int(11) DEFAULT 0,
  `harga` decimal(10,2) DEFAULT NULL,
  `satuan` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `obat`
--

INSERT INTO `obat` (`id_obat`, `nama_obat`, `jenis`, `stok`, `harga`, `satuan`) VALUES
(1, 'Paracetamol 500mg', 'Tablet', 98, 5000.00, 'Strip'),
(2, 'Amoxicillin', 'Kapsul', 50, 12000.00, 'Strip'),
(3, 'Vitamin C', 'Tablet', 200, 2000.00, 'Strip'),
(4, 'Cataflam 25mg', 'Tablet', 12, 45800.00, 'Strip');

-- --------------------------------------------------------

--
-- Table structure for table `pasien`
--

CREATE TABLE `pasien` (
  `id_pasien` int(11) NOT NULL,
  `nik` varchar(16) NOT NULL,
  `nama_pasien` varchar(100) NOT NULL,
  `tgl_lahir` date DEFAULT NULL,
  `jenis_kelamin` enum('L','P') DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `no_hp` varchar(15) DEFAULT NULL,
  `tgl_daftar` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pasien`
--

INSERT INTO `pasien` (`id_pasien`, `nik`, `nama_pasien`, `tgl_lahir`, `jenis_kelamin`, `alamat`, `no_hp`, `tgl_daftar`) VALUES
(1, '1101010101010001', 'Andi Pratama', '1995-05-20', 'L', 'Lhokseumawe', '085200001111', '2025-11-25 05:14:26'),
(2, '1101010101010002', 'Rina Wati', '1998-08-17', 'P', 'Bireuen', '085200002222', '2025-11-25 05:14:26');

-- --------------------------------------------------------

--
-- Table structure for table `pendaftaran`
--

CREATE TABLE `pendaftaran` (
  `id_daftar` int(11) NOT NULL,
  `id_pasien` int(11) NOT NULL,
  `id_dokter` int(11) NOT NULL,
  `tgl_kunjungan` datetime DEFAULT current_timestamp(),
  `status` enum('Menunggu','Diperiksa','Selesai') DEFAULT 'Menunggu',
  `keluhan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pendaftaran`
--

INSERT INTO `pendaftaran` (`id_daftar`, `id_pasien`, `id_dokter`, `tgl_kunjungan`, `status`, `keluhan`) VALUES
(1, 1, 1, '2025-11-25 05:14:26', 'Selesai', 'Demam dan Sakit Kepala'),
(2, 2, 2, '2025-11-25 05:14:26', 'Menunggu', 'Sakit Gigi Geraham'),
(3, 1, 1, '2025-11-25 05:15:53', 'Menunggu', 'Demam mau tewas'),
(4, 2, 2, '2025-11-25 05:31:25', 'Selesai', 'asd'),
(5, 1, 1, '2025-11-25 05:39:16', 'Menunggu', 'alamak'),
(6, 1, 3, '2025-11-25 11:08:43', 'Menunggu', 'Gigi goyang'),
(7, 2, 3, '2025-11-25 11:10:40', 'Menunggu', 'Gigi bagoyang');

-- --------------------------------------------------------

--
-- Table structure for table `poli`
--

CREATE TABLE `poli` (
  `id_poli` int(11) NOT NULL,
  `nama_poli` varchar(50) NOT NULL,
  `lokasi_ruangan` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `poli`
--

INSERT INTO `poli` (`id_poli`, `nama_poli`, `lokasi_ruangan`) VALUES
(1, 'Poli Umum', 'Lt 1 R.A'),
(2, 'Poli Gigi', 'Lt 1 R.B'),
(3, 'Poli Anak', 'Lt 2 R.A'),
(4, 'Poli Kandungan', 'Lt 2 R.B'),
(5, 'Poli Penyakit Dalam', 'Lt 3 R.A');

-- --------------------------------------------------------

--
-- Table structure for table `rekam_medis`
--

CREATE TABLE `rekam_medis` (
  `id_rm` int(11) NOT NULL,
  `id_daftar` int(11) NOT NULL,
  `tgl_periksa` datetime DEFAULT current_timestamp(),
  `diagnosa` text DEFAULT NULL,
  `tindakan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rekam_medis`
--

INSERT INTO `rekam_medis` (`id_rm`, `id_daftar`, `tgl_periksa`, `diagnosa`, `tindakan`) VALUES
(1, 1, '2025-11-25 05:14:26', 'Febris (Demam)', 'Diberikan obat penurun panas dan istirahat'),
(2, 4, '2025-11-25 06:26:22', 'Sakit gigi Berlubang', 'pengobatan');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','dokter','apoteker','resepsionis') NOT NULL,
  `nama_lengkap` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `username`, `password`, `role`, `nama_lengkap`) VALUES
(1, 'admin', '21232f297a57a5a743894a0e4a801fc3', 'admin', 'Muslim Gunawan (Admin)'),
(8, 'apt_rahmi', '5f08aa0b709b40d938f57fcd7d25ead1', 'apoteker', 'Rahmi Sahara'),
(9, 'fo_rahmi', '5f08aa0b709b40d938f57fcd7d25ead1', 'resepsionis', 'Rahmi Sahara');

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_info_dokter`
-- (See below for the actual view)
--
CREATE TABLE `v_info_dokter` (
`id_dokter` int(11)
,`nama_dokter` varchar(100)
,`nama_poli` varchar(50)
,`lokasi_ruangan` varchar(20)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_riwayat_pasien`
-- (See below for the actual view)
--
CREATE TABLE `v_riwayat_pasien` (
`tgl_periksa` datetime
,`nama_pasien` varchar(100)
,`nama_dokter` varchar(100)
,`nama_poli` varchar(50)
,`diagnosa` text
);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `detail_resep`
--
ALTER TABLE `detail_resep`
  ADD PRIMARY KEY (`id_resep`),
  ADD KEY `id_rm` (`id_rm`),
  ADD KEY `id_obat` (`id_obat`);

--
-- Indexes for table `dokter`
--
ALTER TABLE `dokter`
  ADD PRIMARY KEY (`id_dokter`),
  ADD KEY `id_poli` (`id_poli`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `obat`
--
ALTER TABLE `obat`
  ADD PRIMARY KEY (`id_obat`);

--
-- Indexes for table `pasien`
--
ALTER TABLE `pasien`
  ADD PRIMARY KEY (`id_pasien`),
  ADD UNIQUE KEY `nik` (`nik`),
  ADD KEY `idx_nama_pasien` (`nama_pasien`);

--
-- Indexes for table `pendaftaran`
--
ALTER TABLE `pendaftaran`
  ADD PRIMARY KEY (`id_daftar`),
  ADD KEY `id_pasien` (`id_pasien`),
  ADD KEY `id_dokter` (`id_dokter`);

--
-- Indexes for table `poli`
--
ALTER TABLE `poli`
  ADD PRIMARY KEY (`id_poli`);

--
-- Indexes for table `rekam_medis`
--
ALTER TABLE `rekam_medis`
  ADD PRIMARY KEY (`id_rm`),
  ADD UNIQUE KEY `id_daftar` (`id_daftar`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `detail_resep`
--
ALTER TABLE `detail_resep`
  MODIFY `id_resep` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `dokter`
--
ALTER TABLE `dokter`
  MODIFY `id_dokter` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `obat`
--
ALTER TABLE `obat`
  MODIFY `id_obat` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `pasien`
--
ALTER TABLE `pasien`
  MODIFY `id_pasien` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `pendaftaran`
--
ALTER TABLE `pendaftaran`
  MODIFY `id_daftar` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `poli`
--
ALTER TABLE `poli`
  MODIFY `id_poli` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `rekam_medis`
--
ALTER TABLE `rekam_medis`
  MODIFY `id_rm` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

-- --------------------------------------------------------

--
-- Structure for view `v_info_dokter`
--
DROP TABLE IF EXISTS `v_info_dokter`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_info_dokter`  AS SELECT `d`.`id_dokter` AS `id_dokter`, `d`.`nama_dokter` AS `nama_dokter`, `p`.`nama_poli` AS `nama_poli`, `p`.`lokasi_ruangan` AS `lokasi_ruangan` FROM (`dokter` `d` join `poli` `p` on(`d`.`id_poli` = `p`.`id_poli`)) ;

-- --------------------------------------------------------

--
-- Structure for view `v_riwayat_pasien`
--
DROP TABLE IF EXISTS `v_riwayat_pasien`;

CREATE ALGORITHM=UNDEFINED DEFINER=`ramell`@`%` SQL SECURITY DEFINER VIEW `v_riwayat_pasien`  AS SELECT `rm`.`tgl_periksa` AS `tgl_periksa`, `ps`.`nama_pasien` AS `nama_pasien`, `d`.`nama_dokter` AS `nama_dokter`, `p`.`nama_poli` AS `nama_poli`, `rm`.`diagnosa` AS `diagnosa` FROM ((((`rekam_medis` `rm` join `pendaftaran` `pd` on(`rm`.`id_daftar` = `pd`.`id_daftar`)) join `pasien` `ps` on(`pd`.`id_pasien` = `ps`.`id_pasien`)) join `dokter` `d` on(`pd`.`id_dokter` = `d`.`id_dokter`)) join `poli` `p` on(`d`.`id_poli` = `p`.`id_poli`)) ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `detail_resep`
--
ALTER TABLE `detail_resep`
  ADD CONSTRAINT `detail_resep_ibfk_1` FOREIGN KEY (`id_rm`) REFERENCES `rekam_medis` (`id_rm`),
  ADD CONSTRAINT `detail_resep_ibfk_2` FOREIGN KEY (`id_obat`) REFERENCES `obat` (`id_obat`);

--
-- Constraints for table `dokter`
--
ALTER TABLE `dokter`
  ADD CONSTRAINT `dokter_ibfk_1` FOREIGN KEY (`id_poli`) REFERENCES `poli` (`id_poli`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `pendaftaran`
--
ALTER TABLE `pendaftaran`
  ADD CONSTRAINT `pendaftaran_ibfk_1` FOREIGN KEY (`id_pasien`) REFERENCES `pasien` (`id_pasien`),
  ADD CONSTRAINT `pendaftaran_ibfk_2` FOREIGN KEY (`id_dokter`) REFERENCES `dokter` (`id_dokter`);

--
-- Constraints for table `rekam_medis`
--
ALTER TABLE `rekam_medis`
  ADD CONSTRAINT `rekam_medis_ibfk_1` FOREIGN KEY (`id_daftar`) REFERENCES `pendaftaran` (`id_daftar`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
