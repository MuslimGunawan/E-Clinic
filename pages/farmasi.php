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
</div>

<?php include $path_prefix . 'layout/footer.php'; ?>