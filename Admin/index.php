<?php 
$path_prefix = '../'; 
include $path_prefix . 'layout/header.php'; 
include $path_prefix . 'auth_check.php';

if ($_SESSION['role'] !== 'admin') {
    echo "<script>window.location='../auth/login.php';</script>"; exit;
}
?>

<div class="container mb-5">
    <!-- Header Section -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-5 glass-panel p-4">
        <div>
            <h2 class="fw-bold text-primary mb-1">Dashboard Administrator</h2>
            <p class="text-muted mb-0">Pantau aktivitas klinik secara real-time.</p>
        </div>
        <div class="mt-3 mt-md-0 d-flex gap-2">
            <a href="../index.php?page=home" class="btn btn-outline-primary rounded-pill btn-back">
                <i class="fas fa-arrow-left me-2"></i>Ke Halaman Depan
            </a>
            <span class="badge bg-light text-dark border px-3 py-2 rounded-pill fs-6 d-flex align-items-center">
                <i class="far fa-calendar-alt me-2"></i> <?= date('l, d F Y') ?>
            </span>
        </div>
    </div>

    <!-- Statistik Cards -->
    <div class="row g-4 mb-5">
        <!-- Card User -->
        <div class="col-md-3">
            <div class="card h-100 p-3 glass-card">
                <div class="d-flex align-items-center">
                    <div class="icon-shape bg-primary bg-opacity-10 text-primary rounded-3 p-3">
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                    <div class="ms-3">
                        <p class="text-muted mb-0 small text-uppercase fw-bold">Total Users</p>
                        <?php $d = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM users")); ?>
                        <h3 class="fw-bold mb-0"><?= $d['t'] ?></h3>
                    </div>
                </div>
            </div>
        </div>
        <!-- Card Dokter -->
        <div class="col-md-3">
            <div class="card h-100 p-3 glass-card">
                <div class="d-flex align-items-center">
                    <div class="icon-shape bg-success bg-opacity-10 text-success rounded-3 p-3">
                        <i class="fas fa-user-md fa-2x"></i>
                    </div>
                    <div class="ms-3">
                        <p class="text-muted mb-0 small text-uppercase fw-bold">Dokter</p>
                        <?php $d = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM dokter")); ?>
                        <h3 class="fw-bold mb-0"><?= $d['t'] ?></h3>
                    </div>
                </div>
            </div>
        </div>
        <!-- Card Poli -->
        <div class="col-md-3">
            <div class="card h-100 p-3">
                <div class="d-flex align-items-center">
                    <div class="icon-shape bg-warning bg-opacity-10 text-warning rounded-3 p-3">
                        <i class="fas fa-clinic-medical fa-2x"></i>
                    </div>
                    <div class="ms-3">
                        <p class="text-muted mb-0 small text-uppercase fw-bold">Poliklinik</p>
                        <?php $d = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM poli")); ?>
                        <h3 class="fw-bold mb-0"><?= $d['t'] ?></h3>
                    </div>
                </div>
            </div>
        </div>
        <!-- Card Pasien -->
        <div class="col-md-3">
            <div class="card h-100 p-3">
                <div class="d-flex align-items-center">
                    <div class="icon-shape bg-danger bg-opacity-10 text-danger rounded-3 p-3">
                        <i class="fas fa-procedures fa-2x"></i>
                    </div>
                    <div class="ms-3">
                        <p class="text-muted mb-0 small text-uppercase fw-bold">Pasien</p>
                        <?php $d = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM pasien")); ?>
                        <h3 class="fw-bold mb-0"><?= $d['t'] ?></h3>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Menu Cepat -->
    <h5 class="fw-bold text-secondary mb-3 ps-2 border-start border-4 border-primary">Kelola Data Master</h5>
    <div class="row g-4">
        <div class="col-md-6">
            <a href="users.php" class="card text-decoration-none h-100">
                <div class="card-body d-flex align-items-center p-4">
                    <div class="bg-primary text-white rounded-circle p-3 me-3 shadow">
                        <i class="fas fa-user-cog fa-lg"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark mb-1">Manajemen Pengguna</h5>
                        <p class="text-muted small mb-0">Tambah akun Dokter, Resepsionis, Apoteker.</p>
                    </div>
                    <div class="ms-auto text-primary">
                        <i class="fas fa-chevron-right"></i>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-6">
            <a href="poli.php" class="card text-decoration-none h-100">
                <div class="card-body d-flex align-items-center p-4">
                    <div class="bg-warning text-white rounded-circle p-3 me-3 shadow">
                        <i class="fas fa-hospital fa-lg"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark mb-1">Data Poliklinik</h5>
                        <p class="text-muted small mb-0">Atur nama poli & lokasi ruangan.</p>
                    </div>
                    <div class="ms-auto text-warning">
                        <i class="fas fa-chevron-right"></i>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>

<?php include $path_prefix . 'layout/footer.php'; ?>