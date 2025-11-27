-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 25, 2025 at 10:30 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+07:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_eclinic`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id_user` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','dokter','resepsionis','apoteker') NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  PRIMARY KEY (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `username`, `password`, `role`, `nama_lengkap`) VALUES
(1, 'admin', '21232f297a57a5a743894a0e4a801fc3', 'admin', 'Administrator Sistem'),
(2, 'dr_budi', '21232f297a57a5a743894a0e4a801fc3', 'dokter', 'Dr. Budi Santoso'),
(3, 'dr_siti', '21232f297a57a5a743894a0e4a801fc3', 'dokter', 'Drg. Siti Aminah'),
(4, 'resepsionis', '21232f297a57a5a743894a0e4a801fc3', 'resepsionis', 'Resepsionis Utama');

-- Password default untuk semua user di atas adalah: admin

-- --------------------------------------------------------

--
-- Table structure for table `poli`
--

DROP TABLE IF EXISTS `poli`;
CREATE TABLE `poli` (
  `id_poli` int(11) NOT NULL AUTO_INCREMENT,
  `nama_poli` varchar(50) NOT NULL,
  `lokasi_ruangan` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id_poli`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `poli`
--

INSERT INTO `poli` (`id_poli`, `nama_poli`, `lokasi_ruangan`) VALUES
(1, 'Poli Umum', 'Lt 1 R.A'),
(2, 'Poli Gigi', 'Lt 1 R.B'),
(3, 'Poli Anak', 'Lt 2 R.C'),
(4, 'Poli Kandungan', 'Lt 2 R.D'),
(5, 'Poli Penyakit Dalam', 'Lt 3 R.E');

-- --------------------------------------------------------

--
-- Table structure for table `dokter`
--

DROP TABLE IF EXISTS `dokter`;
CREATE TABLE `dokter` (
  `id_dokter` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `nama_dokter` varchar(100) NOT NULL,
  `spesialisasi` varchar(50) DEFAULT NULL,
  `no_hp` varchar(15) DEFAULT NULL,
  `id_poli` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_dokter`),
  KEY `id_poli` (`id_poli`),
  KEY `id_user` (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `dokter`
--

INSERT INTO `dokter` (`id_dokter`, `id_user`, `nama_dokter`, `spesialisasi`, `no_hp`, `id_poli`) VALUES
(1, 2, 'Dr. Budi Santoso', 'Umum', '081234567890', 1),
(2, 3, 'Drg. Siti Aminah', 'Gigi', '081298765432', 2);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_info_dokter`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `v_info_dokter`;
CREATE TABLE `v_info_dokter` (
`nama_dokter` varchar(100)
,`nama_poli` varchar(50)
,`lokasi_ruangan` varchar(20)
);

-- --------------------------------------------------------

--
-- Table structure for table `pasien`
--

DROP TABLE IF EXISTS `pasien`;
CREATE TABLE `pasien` (
  `id_pasien` int(11) NOT NULL AUTO_INCREMENT,
  `nik` varchar(16) NOT NULL,
  `nama_pasien` varchar(100) NOT NULL,
  `tgl_lahir` date NOT NULL,
  `jenis_kelamin` enum('L','P') NOT NULL,
  `alamat` text DEFAULT NULL,
  `no_hp` varchar(15) DEFAULT NULL,
  `tgl_daftar` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id_pasien`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `pendaftaran`
--

DROP TABLE IF EXISTS `pendaftaran`;
CREATE TABLE `pendaftaran` (
  `id_daftar` int(11) NOT NULL AUTO_INCREMENT,
  `id_pasien` int(11) NOT NULL,
  `id_dokter` int(11) NOT NULL,
  `tgl_kunjungan` datetime DEFAULT current_timestamp(),
  `status` enum('Menunggu','Diperiksa','Selesai','Batal') DEFAULT 'Menunggu',
  `keluhan` text DEFAULT NULL,
  PRIMARY KEY (`id_daftar`),
  KEY `id_pasien` (`id_pasien`),
  KEY `id_dokter` (`id_dokter`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `obat`
--

DROP TABLE IF EXISTS `obat`;
CREATE TABLE `obat` (
  `id_obat` int(11) NOT NULL AUTO_INCREMENT,
  `nama_obat` varchar(100) NOT NULL,
  `jenis` varchar(50) DEFAULT NULL,
  `stok` int(11) DEFAULT 0,
  `harga` decimal(10,2) DEFAULT 0.00,
  `satuan` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id_obat`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `obat`
--

INSERT INTO `obat` (`id_obat`, `nama_obat`, `jenis`, `stok`, `harga`, `satuan`) VALUES
(1, 'Paracetamol 500mg', 'Tablet', 100, 5000.00, 'Strip'),
(2, 'Amoxicillin', 'Kapsul', 50, 12000.00, 'Strip'),
(3, 'Vitamin C', 'Tablet', 200, 2000.00, 'Strip');

-- --------------------------------------------------------

--
-- Table structure for table `rekam_medis`
--

DROP TABLE IF EXISTS `rekam_medis`;
CREATE TABLE `rekam_medis` (
  `id_rm` int(11) NOT NULL AUTO_INCREMENT,
  `id_daftar` int(11) NOT NULL,
  `tgl_periksa` datetime DEFAULT current_timestamp(),
  `diagnosa` text DEFAULT NULL,
  `tindakan` text DEFAULT NULL,
  PRIMARY KEY (`id_rm`),
  KEY `id_daftar` (`id_daftar`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `detail_resep`
--

DROP TABLE IF EXISTS `detail_resep`;
CREATE TABLE `detail_resep` (
  `id_resep` int(11) NOT NULL AUTO_INCREMENT,
  `id_rm` int(11) NOT NULL,
  `id_obat` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `subtotal` decimal(10,2) DEFAULT NULL,
  PRIMARY KEY (`id_resep`),
  KEY `id_rm` (`id_rm`),
  KEY `id_obat` (`id_obat`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Structure for view `v_info_dokter`
--
DROP TABLE IF EXISTS `v_info_dokter`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_info_dokter`  AS SELECT `d`.`nama_dokter` AS `nama_dokter`, `p`.`nama_poli` AS `nama_poli`, `p`.`lokasi_ruangan` AS `lokasi_ruangan` FROM (`dokter` `d` join `poli` `p` on(`d`.`id_poli` = `p`.`id_poli`))  ;

-- --------------------------------------------------------

--
-- Structure for view `v_riwayat_pasien`
--
DROP VIEW IF EXISTS `v_riwayat_pasien`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_riwayat_pasien` AS 
SELECT 
    rm.tgl_periksa,
    p.nama_pasien,
    d.nama_dokter,
    po.nama_poli,
    rm.diagnosa
FROM rekam_medis rm
JOIN pendaftaran pd ON rm.id_daftar = pd.id_daftar
JOIN pasien p ON pd.id_pasien = p.id_pasien
JOIN dokter d ON pd.id_dokter = d.id_dokter
JOIN poli po ON d.id_poli = po.id_poli;

-- --------------------------------------------------------

--
-- Procedures
--
DELIMITER $$
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_input_resep` (IN `p_id_rm` INT, IN `p_id_obat` INT, IN `p_jumlah` INT)   
BEGIN
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

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
