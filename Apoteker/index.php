<?php 
$path_prefix = '../'; 
include $path_prefix . 'layout/header.php'; 
include $path_prefix . 'auth_check.php';

if ($_SESSION['role'] !== 'apoteker' && $_SESSION['role'] !== 'admin') {
    echo "<script>window.location='../auth/login.php';</script>"; exit;
}
?>

<div class="container mb-5">
    <!-- Header -->
    <div class="glass-panel p-4 mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h2 class="fw-bold mb-1 text-primary">Instalasi Farmasi</h2>
            <p class="mb-0 opacity-75">Selamat bertugas, <strong>Apt. <?= $_SESSION['nama'] ?></strong></p>
        </div>
        <div class="d-flex align-items-center gap-3">
            <a href="../index.php?page=home" class="btn btn-outline-primary rounded-pill btn-back">
                <i class="fas fa-arrow-left me-2"></i>Ke Halaman Depan
            </a>
            <i class="fas fa-prescription-bottle-alt fa-4x opacity-25 text-primary"></i>
        </div>
    </div>

    <div class="row g-4 mb-5">
        <!-- Stok Kritis -->
        <div class="col-md-4">
            <div class="card glass-card h-100 p-3 border-start border-5 border-danger">
                <div class="d-flex align-items-center">
                    <div class="icon-shape text-danger p-3">
                        <i class="fas fa-exclamation-triangle fa-3x"></i>
                    </div>
                    <div class="ms-3">
                        <p class="text-muted mb-0 small text-uppercase fw-bold">Stok Menipis</p>
                        <?php 
                        // Menghitung obat dengan stok kurang dari 10
                        $d = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM obat WHERE stok < 10")); 
                        ?>
                        <h2 class="fw-bold mb-0 text-danger"><?= $d['t'] ?> <span class="fs-6 text-muted fw-normal">Item</span></h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Jenis Obat -->
        <div class="col-md-4">
            <div class="card glass-card h-100 p-3 border-start border-5 border-success">
                <div class="d-flex align-items-center">
                    <div class="icon-shape text-success p-3">
                        <i class="fas fa-pills fa-3x"></i>
                    </div>
                    <div class="ms-3">
                        <p class="text-muted mb-0 small text-uppercase fw-bold">Total Jenis Obat</p>
                        <?php $d = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as t FROM obat")); ?>
                        <h2 class="fw-bold mb-0 text-success"><?= $d['t'] ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Menu Cepat -->
        <div class="col-md-4">
            <a href="obat.php" class="card glass-card h-100 text-primary text-decoration-none">
                <div class="card-body d-flex align-items-center justify-content-center flex-column text-center">
                    <i class="fas fa-edit fa-3x mb-2"></i>
                    <h5 class="fw-bold">Kelola Stok Obat</h5>
                    <small>Update stok atau tambah obat baru</small>
                </div>
            </a>
        </div>
    </div>

    <!-- Tabel Peringatan Stok -->
    <div class="card glass-card">
        <div class="card-header bg-transparent py-3 border-bottom fw-bold text-danger">
            <i class="fas fa-bell me-2"></i>Peringatan Stok Kritis (< 10 Unit)
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-transparent">
                    <tr>
                        <th class="ps-4">Nama Obat</th>
                        <th>Jenis</th>
                        <th>Sisa Stok</th>
                        <th>Harga Satuan</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $q_low = mysqli_query($conn, "SELECT * FROM obat WHERE stok < 10 ORDER BY stok ASC");
                    
                    if(mysqli_num_rows($q_low) > 0):
                        while($r = mysqli_fetch_assoc($q_low)):
                    ?>
                    <tr>
                        <td class="ps-4 fw-bold"><?= $r['nama_obat'] ?></td>
                        <td><?= $r['jenis'] ?></td>
                        <td><span class="badge bg-danger rounded-pill px-3"><?= $r['stok'] ?> <?= $r['satuan'] ?></span></td>
                        <td>Rp <?= number_format($r['harga']) ?></td>
                        <td class="text-end pe-4">
                            <a href="obat.php?edit=<?= $r['id_obat'] ?>" class="btn btn-sm btn-outline-danger">
                                <i class="fas fa-plus-circle me-1"></i> Restock
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; else: ?>
                    <tr><td colspan="5" class="text-center py-4 text-muted"><i class="fas fa-check-circle text-success me-2"></i>Semua stok obat aman.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include $path_prefix . 'layout/footer.php'; ?>