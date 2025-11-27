<?php 
$path_prefix = '../';
include $path_prefix . 'layout/header.php'; 
?>

<div class="container py-5" style="margin-top: 80px;">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="../index.php">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Poli Anak</li>
        </ol>
    </nav>

    <div class="row align-items-center mb-5">
        <div class="col-md-6">
            <img src="../assets/img/Poli_Anak.png" class="img-fluid rounded-4 shadow" alt="Poli Anak">
        </div>
        <div class="col-md-6">
            <h1 class="fw-bold text-info mb-3">Poli Anak</h1>
            <p class="lead text-muted">Layanan kesehatan khusus untuk bayi, balita, dan anak-anak dengan pendekatan yang ramah anak.</p>
            <hr>
            <h5 class="fw-bold"><i class="fas fa-check-circle text-success me-2"></i>Layanan Kami:</h5>
            <ul class="list-unstyled">
                <li class="mb-2"><i class="fas fa-dot-circle text-info me-2 small"></i>Imunisasi dasar dan lanjutan</li>
                <li class="mb-2"><i class="fas fa-dot-circle text-info me-2 small"></i>Pemantauan tumbuh kembang</li>
                <li class="mb-2"><i class="fas fa-dot-circle text-info me-2 small"></i>Konsultasi gizi anak</li>
                <li class="mb-2"><i class="fas fa-dot-circle text-info me-2 small"></i>Penanganan penyakit anak</li>
            </ul>
            
            <div class="mt-4 p-3 bg-light rounded border-start border-4 border-info shadow-sm">
                <h6 class="fw-bold text-info mb-1"><i class="fas fa-info-circle me-2"></i>Cara Mendaftar</h6>
                <p class="mb-0 text-muted small">Silakan datang langsung ke meja <strong>Resepsionis</strong> untuk melakukan pendaftaran layanan ini.</p>
            </div>
        </div>
    </div>
</div>

<?php include $path_prefix . 'layout/footer.php'; ?>