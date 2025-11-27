<?php 
$path_prefix = '../';
include $path_prefix . 'layout/header.php'; 
?>

<div class="container py-5" style="margin-top: 80px;">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Poli Umum</li>
        </ol>
    </nav>

    <div class="row align-items-center mb-5">
        <div class="col-md-6">
            <img src="../assets/img/Poli_Umum.png" class="img-fluid rounded-4 shadow" alt="Poli Umum">
        </div>
        <div class="col-md-6">
            <h1 class="fw-bold text-primary mb-3">Poli Umum</h1>
            <p class="lead text-muted">Layanan kesehatan primer yang menangani berbagai keluhan kesehatan umum untuk dewasa dan anak-anak.</p>
            <hr>
            <h5 class="fw-bold"><i class="fas fa-check-circle text-success me-2"></i>Layanan Kami:</h5>
            <ul class="list-unstyled">
                <li class="mb-2"><i class="fas fa-dot-circle text-primary me-2 small"></i>Pemeriksaan kesehatan rutin (Check-up)</li>
                <li class="mb-2"><i class="fas fa-dot-circle text-primary me-2 small"></i>Pengobatan penyakit infeksi ringan</li>
                <li class="mb-2"><i class="fas fa-dot-circle text-primary me-2 small"></i>Konsultasi kesehatan umum</li>
                <li class="mb-2"><i class="fas fa-dot-circle text-primary me-2 small"></i>Surat keterangan sehat</li>
            </ul>
            
            <div class="mt-4 p-3 bg-light rounded border-start border-4 border-primary shadow-sm">
                <h6 class="fw-bold text-primary mb-1"><i class="fas fa-info-circle me-2"></i>Cara Mendaftar</h6>
                <p class="mb-0 text-muted small">Silakan datang langsung ke meja <strong>Resepsionis</strong> untuk melakukan pendaftaran layanan ini.</p>
            </div>
        </div>
    </div>
</div>

<?php include $path_prefix . 'layout/footer.php'; ?>