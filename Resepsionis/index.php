<?php 
$path_prefix = '../'; 
include $path_prefix . 'layout/header.php'; 
include $path_prefix . 'auth_check.php';

if ($_SESSION['role'] !== 'resepsionis' && $_SESSION['role'] !== 'admin') {
    echo "<script>window.location='../auth/login.php';</script>"; exit;
}
?>

<div class="container mb-5">
    <!-- Header Glass -->
    <div class="glass-panel p-4 mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h2 class="fw-bold text-success mb-0">Dashboard Resepsionis</h2>
            <p class="text-muted mb-0">Selamat bertugas, <strong><?= $_SESSION['nama'] ?></strong></p>
        </div>
        <div class="d-flex gap-2">
            <a href="../index.php?page=home" class="btn btn-outline-success rounded-pill btn-back">
                <i class="fas fa-arrow-left me-2"></i>Ke Halaman Depan
            </a>
            <a href="pendaftaran.php" class="btn btn-success rounded-pill px-4 shadow-sm">
                <i class="fas fa-plus-circle me-2"></i>Daftar Kunjungan
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- Card Antrian -->
        <div class="col-md-4">
            <div class="card glass-card h-100 p-3">
                <div class="d-flex align-items-center">
                    <div class="icon-shape text-warning p-3">
                        <i class="fas fa-user-clock fa-3x"></i>
                    </div>
                    <div class="ms-3">
                        <p class="text-muted mb-0 small text-uppercase fw-bold">Antrian Menunggu</p>
                        <?php 
                        date_default_timezone_set('Asia/Jakarta'); // Set Timezone Indonesia
                        $today = date('Y-m-d');
                        $d = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM pendaftaran WHERE status='Menunggu' AND DATE(tgl_kunjungan)='$today'")); 
                        ?>
                        <h3 class="fw-bold mb-0"><?= $d['t'] ?></h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Total Pasien -->
        <div class="col-md-4">
            <div class="card glass-card h-100 p-3">
                <div class="d-flex align-items-center">
                    <div class="icon-shape text-primary p-3">
                        <i class="fas fa-users fa-3x"></i>
                    </div>
                    <div class="ms-3">
                        <p class="text-muted mb-0 small text-uppercase fw-bold">Database Pasien</p>
                        <?php $d = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM pasien")); ?>
                        <h3 class="fw-bold mb-0"><?= $d['t'] ?></h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card Selesai -->
        <div class="col-md-4">
            <div class="card glass-card h-100 p-3">
                <div class="d-flex align-items-center">
                    <div class="icon-shape text-success p-3">
                        <i class="fas fa-check-double fa-3x"></i>
                    </div>
                    <div class="ms-3">
                        <p class="text-muted mb-0 small text-uppercase fw-bold">Selesai Hari Ini</p>
                        <?php 
                        $d = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM pendaftaran WHERE status='Selesai' AND DATE(tgl_kunjungan)='$today'")); 
                        ?>
                        <h3 class="fw-bold mb-0"><?= $d['t'] ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Kunjungan Hari Ini -->
    <div class="card glass-card mt-4 overflow-hidden">
        <div class="card-header bg-transparent py-3 border-bottom fw-bold text-secondary">
            <i class="fas fa-list-alt me-2"></i>Daftar Kunjungan Hari Ini
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-transparent">
                    <tr>
                        <th class="ps-4">No</th>
                        <th>Nama Pasien</th>
                        <th>Dokter Tujuan</th>
                        <th>Keluhan</th>
                        <th>Status</th>
                    </tr>
                </thead>
                                <tbody>
                    <?php
                    $no = 1;
                    // Join ke tabel dokter untuk ambil nama dokter
                    $q = mysqli_query($conn, "SELECT pd.*, p.nama_pasien, d.nama_dokter 
                                              FROM pendaftaran pd 
                                              JOIN pasien p ON pd.id_pasien = p.id_pasien 
                                              JOIN dokter d ON pd.id_dokter = d.id_dokter 
                                              WHERE DATE(pd.tgl_kunjungan) = '$today' 
                                              ORDER BY pd.id_daftar DESC");
                    
                    if(mysqli_num_rows($q) > 0):
                        while($r = mysqli_fetch_assoc($q)):
                            $status_badge = 'bg-secondary';
                            if($r['status'] == 'Menunggu') $status_badge = 'bg-warning text-dark';
                            if($r['status'] == 'Diperiksa') $status_badge = 'bg-info text-dark';
                            if($r['status'] == 'Selesai') $status_badge = 'bg-success';
                    ?>
                    <tr>
                        <td class="ps-4"><?= $no++ ?></td>
                        <td class="fw-bold"><?= $r['nama_pasien'] ?></td>
                        <td><i class="fas fa-user-md me-1 text-primary"></i> <?= $r['nama_dokter'] ?></td>
                        <td class="text-muted small fst-italic">"<?= $r['keluhan'] ?>"</td>
                        <td><span class="badge <?= $status_badge ?> rounded-pill px-3"><?= $r['status'] ?></span></td>
                    </tr>
                    <?php endwhile; else: ?>
                    <tr><td colspan="5" class="text-center py-4 text-muted">Belum ada kunjungan hari ini.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include $path_prefix . 'layout/footer.php'; ?>