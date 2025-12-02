<?php 
$path_prefix = '../';
include $path_prefix . 'layout/header.php'; 
?>

<div class="container py-5" style="margin-top: 80px;">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Instalasi Farmasi</li>
        </ol>
    </nav>

    <div class="row align-items-center mb-5">
        <div class="col-md-6">
            <img src="../assets/img/Poli_InstalasiFarmasi.png" class="img-fluid rounded-4 shadow" alt="Farmasi">
        </div>
        <div class="col-md-6">
            <h1 class="fw-bold text-success mb-3">Instalasi Farmasi</h1>
            <p class="lead text-muted">Layanan penyediaan obat-obatan lengkap dengan jaminan kualitas dan harga terjangkau.</p>
            <hr>
            <h5 class="fw-bold"><i class="fas fa-check-circle text-success me-2"></i>Layanan Kami:</h5>
            <ul class="list-unstyled">
                <li class="mb-2"><i class="fas fa-dot-circle text-success me-2 small"></i>Resep Dokter</li>
                <li class="mb-2"><i class="fas fa-dot-circle text-success me-2 small"></i>Obat Bebas & Suplemen</li>
                <li class="mb-2"><i class="fas fa-dot-circle text-success me-2 small"></i>Konsultasi Obat (PIO)</li>
                <li class="mb-2"><i class="fas fa-dot-circle text-success me-2 small"></i>Alat Kesehatan Dasar</li>
            </ul>
            
            <div class="mt-4 p-3 bg-light rounded border-start border-4 border-success shadow-sm">
                <h6 class="fw-bold text-success mb-1"><i class="fas fa-info-circle me-2"></i>Layanan Obat</h6>
                <p class="mb-0 text-muted small">Silakan serahkan resep dokter Anda ke loket <strong>Farmasi</strong> untuk pengambilan obat.</p>
            </div>
        </div>
    </div>

    <!-- Katalog Obat Section -->
    <div class="mt-5">
        <h3 class="fw-bold text-success mb-4 border-bottom pb-2">Katalog Obat Tersedia</h3>
        <div class="row g-4">
            <?php
            $q_obat = mysqli_query($conn, "SELECT * FROM obat ORDER BY nama_obat ASC");
            if(mysqli_num_rows($q_obat) > 0){
                while($o = mysqli_fetch_assoc($q_obat)){
                    $foto = !empty($o['foto']) ? '../assets/img/obat/'.$o['foto'] : '../assets/img/obat/default.png';
            ?>
            <div class="col-md-3 col-6">
                <div class="card h-100 shadow-sm border-0 hover-scale">
                    <img src="<?= $foto ?>" class="card-img-top" alt="<?= $o['nama_obat'] ?>" style="height: 150px; object-fit: cover;" onerror="this.onerror=null; this.src='https://via.placeholder.com/150?text=No+Img'">
                    <div class="card-body text-center">
                        <h6 class="fw-bold text-dark mb-1"><?= $o['nama_obat'] ?></h6>
                        <span class="badge bg-light text-secondary border"><?= $o['jenis'] ?></span>
                        <p class="text-success fw-bold mt-2 mb-0">Rp <?= number_format($o['harga']) ?></p>
                    </div>
                </div>
            </div>
            <?php 
                }
            } else {
                echo "<div class='col-12 text-center text-muted py-5'>Belum ada data obat yang ditampilkan.</div>";
            }
            ?>
        </div>
    </div>
</div>

<?php include $path_prefix . 'layout/footer.php'; ?>