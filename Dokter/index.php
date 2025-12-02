<?php 
$path_prefix = '../'; 
include $path_prefix . 'layout/header.php'; 
include $path_prefix . 'auth_check.php';

if ($_SESSION['role'] !== 'dokter') exit;

// Ambil ID Dokter berdasarkan ID User yang login
$id_user = $_SESSION['id_user'];
$q_doc = mysqli_query($conn, "SELECT id_dokter FROM dokter WHERE id_user='$id_user'");
$d_doc = mysqli_fetch_assoc($q_doc);
$id_dokter = $d_doc['id_dokter'];
?>

<div class="container mb-5">
    <!-- Welcome Header -->
    <div class="glass-panel p-4 mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h2 class="fw-bold mb-1 text-primary">Ruang Praktek Dokter</h2>
            <p class="mb-0 opacity-75">Selamat bekerja, <strong><?= $_SESSION['nama'] ?></strong></p>
        </div>
        <div class="d-flex align-items-center gap-3">
            <a href="riwayat_pasien.php" class="btn btn-info text-white rounded-pill shadow-sm">
                <i class="fas fa-book-medical me-2"></i>Database Pasien
            </a>
            <a href="../index.php?page=home" class="btn btn-outline-primary rounded-pill btn-back">
                <i class="fas fa-arrow-left me-2"></i>Ke Halaman Depan
            </a>
            <i class="fas fa-user-md fa-4x opacity-50 text-primary"></i>
        </div>
    </div>

    <!-- Statistik -->
    <div class="row g-4 mb-5">
        <div class="col-md-6">
            <div class="card glass-card h-100 p-3 border-start border-5 border-warning">
                <div class="d-flex align-items-center">
                    <div class="ms-3">
                        <h6 class="text-muted text-uppercase mb-1">Menunggu Pemeriksaan</h6>
                        <?php
                        $today = date('Y-m-d');
                        // Hitung pasien hari ini yg statusnya Menunggu KHUSUS DOKTER INI
                        $q = mysqli_query($conn, "SELECT COUNT(*) as t FROM pendaftaran WHERE status='Menunggu' AND DATE(tgl_kunjungan)='$today' AND id_dokter='$id_dokter'");
                        $d = mysqli_fetch_assoc($q);
                        ?>
                        <h2 class="fw-bold mb-0 text-warning"><?= $d['t'] ?> <span class="fs-6 text-muted fw-normal">Pasien</span></h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card glass-card h-100 p-3 border-start border-5 border-primary">
                <div class="d-flex align-items-center">
                    <div class="ms-3">
                        <h6 class="text-muted text-uppercase mb-1">Total Pasien Anda</h6>
                        <?php
                        // Hitung total rekam medis (pasien yg sudah diperiksa) OLEH DOKTER INI
                        // Join ke pendaftaran untuk filter by id_dokter
                        $q2 = mysqli_query($conn, "SELECT COUNT(*) as t FROM rekam_medis rm 
                                                   JOIN pendaftaran pd ON rm.id_daftar = pd.id_daftar 
                                                   WHERE pd.id_dokter='$id_dokter'");
                        $d2 = mysqli_fetch_assoc($q2);
                        ?>
                        <h2 class="fw-bold mb-0 text-primary"><?= $d2['t'] ?> <span class="fs-6 text-muted fw-normal">Orang</span></h2>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Pasien Menunggu (Real-time) -->
    <div class="card glass-card">
        <div class="card-header bg-transparent py-3 border-bottom d-flex justify-content-between align-items-center">
            <span class="fw-bold text-primary"><i class="fas fa-clipboard-list me-2"></i>Antrian Pasien Hari Ini</span>
            <a href="transaksi.php" class="btn btn-primary btn-sm rounded-pill px-3">Mulai Periksa</a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-transparent">
                    <tr>
                        <th class="ps-4">Jam Daftar</th>
                        <th>Nama Pasien</th>
                        <th>Keluhan</th>
                        <th>Status</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Menampilkan hanya pasien yang statusnya 'Menunggu' hari ini DAN KHUSUS DOKTER INI
                    $q_wait = mysqli_query($conn, "SELECT p.nama_pasien, pd.tgl_kunjungan, pd.keluhan, pd.status, pd.id_daftar 
                                                   FROM pendaftaran pd 
                                                   JOIN pasien p ON pd.id_pasien = p.id_pasien 
                                                   WHERE pd.status='Menunggu' 
                                                   AND pd.id_dokter='$id_dokter'
                                                   AND DATE(pd.tgl_kunjungan)='$today'
                                                   ORDER BY pd.tgl_kunjungan ASC");
                    
                    if(mysqli_num_rows($q_wait) > 0):
                        while($r = mysqli_fetch_assoc($q_wait)):
                    ?>
                    <tr>
                        <td class="ps-4 text-muted"><?= date('H:i', strtotime($r['tgl_kunjungan'])) ?></td>
                        <td class="fw-bold"><?= $r['nama_pasien'] ?></td>
                        <td><?= $r['keluhan'] ?></td>
                        <td><span class="badge bg-warning text-dark">Menunggu</span></td>
                        <td class="text-end pe-4">
                            <a href="transaksi.php?id=<?= $r['id_daftar'] ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                <i class="fas fa-stethoscope me-1"></i> Periksa
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; else: ?>
                    <tr><td colspan="5" class="text-center py-4 text-muted">Tidak ada antrian pasien saat ini.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include $path_prefix . 'layout/footer.php'; ?>