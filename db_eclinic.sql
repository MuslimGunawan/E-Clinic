-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 02, 2025 at 04:15 AM
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
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_input_resep` (IN `p_id_rm` INT, IN `p_id_obat` INT, IN `p_jumlah` INT)   BEGIN
    DECLARE v_stok INT;
    DECLARE v_harga DECIMAL(10,2);
    DECLARE v_subtotal DECIMAL(10,2);
    DECLARE v_pesan VARCHAR(100);

    -- 1. Cek Stok Obat
    SELECT stok, harga INTO v_stok, v_harga FROM obat WHERE id_obat = p_id_obat;

    IF v_stok >= p_jumlah THEN
        -- 2. Kurangi Stok (Update)
        UPDATE obat SET stok = stok - p_jumlah WHERE id_obat = p_id_obat;

        -- 3. Hitung Subtotal
        SET v_subtotal = v_harga * p_jumlah;

        -- 4. Insert ke Detail Resep
        INSERT INTO detail_resep (id_rm, id_obat, jumlah, subtotal) 
        VALUES (p_id_rm, p_id_obat, p_jumlah, v_subtotal);

        SET v_pesan = 'Berhasil: Resep diinput dan stok dikurangi.';
        
        -- COMMIT (Implisit di MySQL kecuali dalam blok transaction eksplisit, tapi SP ini mensimulasikan logika bisnisnya)
    ELSE
        -- Stok Kurang
        SET v_pesan = 'Gagal: Stok obat tidak mencukupi.';
        -- ROLLBACK (Tidak bisa rollback insert RM dari sini jika RM ada di luar SP, tapi kita kirim sinyal error)
    END IF;

    -- Kembalikan Pesan Status
    SELECT v_pesan AS Pesan;
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

-- --------------------------------------------------------

--
-- Table structure for table `dokter`
--

CREATE TABLE `dokter` (
  `id_dokter` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `nama_dokter` varchar(100) NOT NULL,
  `spesialisasi` varchar(50) DEFAULT NULL,
  `no_hp` varchar(15) DEFAULT NULL,
  `id_poli` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dokter`
--

INSERT INTO `dokter` (`id_dokter`, `id_user`, `nama_dokter`, `spesialisasi`, `no_hp`, `id_poli`) VALUES
(3, 5, 'Dr. Rahmi Sahara', 'Umum', '0822-5056-5463', 1),
(4, 6, 'Dr. Muslim Gunawan', 'Gigi', '0813-6148-4242', 2);

-- --------------------------------------------------------

--
-- Table structure for table `obat`
--

CREATE TABLE `obat` (
  `id_obat` int(11) NOT NULL,
  `nama_obat` varchar(100) NOT NULL,
  `jenis` varchar(50) DEFAULT NULL,
  `stok` int(11) DEFAULT 0,
  `harga` decimal(10,2) DEFAULT 0.00,
  `satuan` varchar(20) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `obat`
--

INSERT INTO `obat` (`id_obat`, `nama_obat`, `jenis`, `stok`, `harga`, `satuan`, `foto`) VALUES
(1, 'Paracetamol 500mg', 'Tablet', 120, 5000.00, 'Strip', '1764636358_Paracetamol_500mg.png'),
(2, 'Amoxicillin 500mg Hexpharm', 'Tablet', 45, 12000.00, 'Strip', '1764635451_Amoxicillin_500mg.jpg'),
(3, 'Vitacimin Lemon 500mg', 'Tablet Hisap', 200, 2000.00, 'Strip', '1764638460_Vitacimin_lemon_500mg.png'),
(4, 'Cataflam 25mg', 'Kaplet', 8, 45800.00, 'Strip', '1764631267_Cataflam_25mg.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `pasien`
--

CREATE TABLE `pasien` (
  `id_pasien` int(11) NOT NULL,
  `nik` varchar(16) NOT NULL,
  `nama_pasien` varchar(100) NOT NULL,
  `tgl_lahir` date NOT NULL,
  `jenis_kelamin` enum('L','P') NOT NULL,
  `alamat` text DEFAULT NULL,
  `no_hp` varchar(15) DEFAULT NULL,
  `tgl_daftar` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pendaftaran`
--

CREATE TABLE `pendaftaran` (
  `id_daftar` int(11) NOT NULL,
  `id_pasien` int(11) NOT NULL,
  `id_dokter` int(11) NOT NULL,
  `tgl_kunjungan` datetime DEFAULT current_timestamp(),
  `status` enum('Menunggu','Diperiksa','Selesai','Batal') DEFAULT 'Menunggu',
  `keluhan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(3, 'Poli Anak', 'Lt 2 R.C'),
(4, 'Poli Kandungan', 'Lt 2 R.D'),
(5, 'Poli Penyakit Dalam', 'Lt 3 R.E'),
(6, 'Poli Saraf', 'Lt 3 R.F');

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
(1, 1, '2025-11-25 11:47:11', 'Gigi beneran bagoyang', 'Cabut gigi'),
(2, 3, '2025-11-25 12:14:45', 'Bagoyang sendiri dah geser', 'Kretek + obat');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','dokter','resepsionis','apoteker') NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `username`, `password`, `role`, `nama_lengkap`) VALUES
(1, 'admin', '21232f297a57a5a743894a0e4a801fc3', 'admin', 'Adm. S. Muslim Gunawan'),
(5, 'dr_rahmi', '5f08aa0b709b40d938f57fcd7d25ead1', 'dokter', 'Dr. Rahmi Sahara'),
(6, 'dr_muslim', '965fece9c320b86f2bf304fb17e2b35a', 'dokter', 'Dr. Muslim Gunawan'),
(9, 'apt_rahmi', '5f08aa0b709b40d938f57fcd7d25ead1', 'apoteker', 'Apt. Rahmi Sahara'),
(10, 'fo_rahmi', '5f08aa0b709b40d938f57fcd7d25ead1', 'resepsionis', 'Fo. Rahmi Sahara');

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_info_dokter`
-- (See below for the actual view)
--
CREATE TABLE `v_info_dokter` (
`nama_dokter` varchar(100)
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
  ADD PRIMARY KEY (`id_pasien`);

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
  ADD KEY `id_daftar` (`id_daftar`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`);

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
  MODIFY `id_dokter` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
  MODIFY `id_daftar` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `poli`
--
ALTER TABLE `poli`
  MODIFY `id_poli` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

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

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_info_dokter`  AS SELECT `d`.`nama_dokter` AS `nama_dokter`, `p`.`nama_poli` AS `nama_poli`, `p`.`lokasi_ruangan` AS `lokasi_ruangan` FROM (`dokter` `d` join `poli` `p` on(`d`.`id_poli` = `p`.`id_poli`)) ;

-- --------------------------------------------------------

--
-- Structure for view `v_riwayat_pasien`
--
DROP TABLE IF EXISTS `v_riwayat_pasien`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_riwayat_pasien`  AS SELECT `rm`.`tgl_periksa` AS `tgl_periksa`, `p`.`nama_pasien` AS `nama_pasien`, `d`.`nama_dokter` AS `nama_dokter`, `po`.`nama_poli` AS `nama_poli`, `rm`.`diagnosa` AS `diagnosa` FROM ((((`rekam_medis` `rm` join `pendaftaran` `pd` on(`rm`.`id_daftar` = `pd`.`id_daftar`)) join `pasien` `p` on(`pd`.`id_pasien` = `p`.`id_pasien`)) join `dokter` `d` on(`pd`.`id_dokter` = `d`.`id_dokter`)) join `poli` `po` on(`d`.`id_poli` = `po`.`id_poli`)) ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
