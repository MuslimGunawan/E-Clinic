<?php 
session_start();

// Hubungkan ke database
include 'config/koneksi.php';

// --- LOGIKA ROUTER PINTAR ---
// Jika user sudah login, jangan kasih lihat halaman ini.
// Langsung lempar ke ruang kerja mereka masing-masing.
// KECUALI jika ada parameter ?page=home (untuk akses tombol "Ke Halaman Depan")
if (isset($_SESSION['login']) && !isset($_GET['page'])) {
    $role = $_SESSION['role'];
    if ($role == 'admin') {
        header("Location: admin/");
    } elseif ($role == 'dokter') {
        header("Location: dokter/");
    } elseif ($role == 'resepsionis') {
        header("Location: resepsionis/");
    } elseif ($role == 'apoteker') {
        header("Location: apoteker/");
    }
    exit;
}

// Jika belum login, tampilkan Landing Page untuk Pasien/Publik
$path_prefix = ''; // Karena file ini di root, path ke layout tidak perlu '../'
include 'layout/header.php';
?>

<!-- Hero Section -->
<header class="hero-section position-relative overflow-hidden text-white" style="background: linear-gradient(135deg, #0d6efd 0%, #0dcaf0 100%); padding-top: 120px; padding-bottom: 100px;">
    <!-- Decorative Shapes -->
    <div class="position-absolute top-0 start-0 translate-middle rounded-circle bg-white opacity-10" style="width: 400px; height: 400px; filter: blur(80px);"></div>
    <div class="position-absolute bottom-0 end-0 translate-middle rounded-circle bg-warning opacity-10" style="width: 300px; height: 300px; filter: blur(60px);"></div>
    
    <div class="container position-relative z-index-1 text-center">
        <span class="badge bg-white bg-opacity-25 rounded-pill px-4 py-2 mb-4 backdrop-blur border border-white border-opacity-25 shadow-sm">
            <i class="fas fa-star text-warning me-2"></i>Sistem Informasi Klinik Terpadu
        </span>
        <h1 class="display-3 fw-bold mb-3 text-shadow">Kesehatan Anda,<br>Prioritas Kami</h1>
        <p class="lead fs-5 mb-5 text-white-50 mx-auto" style="max-width: 700px;">
            Platform E-Clinic memudahkan Anda dalam mengakses jadwal dokter, informasi layanan, dan pendaftaran yang lebih efisien.
        </p>
        
        <div class="d-flex justify-content-center gap-3">
            <a href="#jadwal" class="btn btn-light btn-lg text-primary fw-bold px-5 shadow-lg rounded-pill hover-scale">
                <i class="far fa-calendar-alt me-2"></i>Cek Jadwal
            </a>
            <a href="auth/login.php" class="btn btn-outline-light btn-lg fw-bold px-5 rounded-pill backdrop-blur hover-scale border-2">
                <i class="fas fa-sign-in-alt me-2"></i>Login Petugas
            </a>
        </div>
    </div>
</header>

<!-- Stats Section (Floating Cards) -->
<div class="container position-relative z-index-2 mt-n5">
    <div class="row g-3 justify-content-center">
        <div class="col-md-3 col-12">
            <div class="glass-card p-3 text-center h-100 d-flex align-items-center justify-content-center">
                <i class="fas fa-user-md fa-2x text-primary me-3"></i>
                <div class="text-start">
                    <h3 class="fw-bold mb-0">15+</h3>
                    <small class="text-muted">Dokter Spesialis</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-12">
            <div class="glass-card p-3 text-center h-100 d-flex align-items-center justify-content-center">
                <i class="fas fa-users fa-2x text-success me-3"></i>
                <div class="text-start">
                    <h3 class="fw-bold mb-0">5k+</h3>
                    <small class="text-muted">Pasien Terlayani</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-12">
            <div class="glass-card p-3 text-center h-100 d-flex align-items-center justify-content-center">
                <i class="fas fa-clock fa-2x text-danger me-3"></i>
                <div class="text-start">
                    <h3 class="fw-bold mb-0">24/7</h3>
                    <small class="text-muted">Layanan IGD</small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Jadwal Dokter Section -->
<div class="container py-5 my-5" id="jadwal">
    <div class="row align-items-center mb-5">
        <div class="col-md-6">
            <h5 class="text-primary fw-bold text-uppercase ls-2">Jadwal Praktik</h5>
            <h2 class="fw-bold display-6">Temui Dokter Ahli Kami</h2>
        </div>
        <div class="col-md-6 text-md-end">
            <p class="text-muted">Informasi jadwal dokter diperbarui secara real-time dari sistem database klinik.</p>
        </div>
    </div>

    <div class="glass-panel overflow-hidden" style="background: rgba(255, 255, 255, 0.5);">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th class="py-4 ps-5">Nama Dokter</th>
                            <th class="py-4">Poliklinik</th>
                            <th class="py-4">Lokasi Ruangan</th>
                            <th class="py-4 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Mengambil data dari VIEW v_info_dokter
                        $query = "SELECT * FROM v_info_dokter";
                        $result = mysqli_query($conn, $query);
                        
                        if(mysqli_num_rows($result) > 0) {
                            while ($row = mysqli_fetch_assoc($result)) {
                                echo "<tr>
                                    <td class='fw-bold text-dark ps-5 py-3' data-label='Nama Dokter'>
                                        <div class='d-flex align-items-center'>
                                            <div class='icon-shape bg-primary bg-opacity-10 text-primary rounded-circle me-3 p-2'>
                                                <i class='fas fa-user-md'></i>
                                            </div>
                                            {$row['nama_dokter']}
                                        </div>
                                    </td>
                                    <td data-label='Poliklinik'>
                                        <span class='badge bg-info bg-opacity-10 text-info border border-info rounded-pill px-3'>{$row['nama_poli']}</span>
                                    </td>
                                    <td class='text-muted' data-label='Lokasi Ruangan'>
                                        <i class='fas fa-map-marker-alt text-danger me-2'></i>{$row['lokasi_ruangan']}
                                    </td>
                                    <td class='text-center' data-label='Status'>
                                        <span class='badge bg-success rounded-pill px-3'>Tersedia</span>
                                    </td>
                                </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4' class='text-center py-5 text-muted'>Belum ada jadwal dokter tersedia saat ini.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Layanan Section -->
<div class="py-5" id="layanan" style="background-color: #f8f9fa;">
    <div class="container">
        <div class="text-center mb-5">
            <h5 class="text-primary fw-bold text-uppercase ls-2">Fasilitas & Layanan</h5>
            <h2 class="fw-bold display-6">Solusi Kesehatan Lengkap</h2>
        </div>
        
        <div class="row g-4">
            <!-- Layanan 1: Poli Umum -->
            <div class="col-md-4">
                <div class="glass-card h-100 p-4 text-center">
                    <div class="mb-4 mt-3">
                        <div class="icon-shape bg-primary text-white rounded-circle mx-auto shadow-lg" style="width: 80px; height: 80px; font-size: 2rem; display:flex; align-items:center; justify-content:center;">
                            <i class="fas fa-stethoscope"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold">Poli Umum</h4>
                    <p class="text-muted mb-4">Pemeriksaan kesehatan menyeluruh untuk dewasa dan anak-anak dengan penanganan dokter umum.</p>
                    <a href="pages/poli_umum.php" class="btn btn-outline-primary rounded-pill px-4 stretched-link">Selengkapnya</a>
                </div>
            </div>

            <!-- Layanan 2: Poli Gigi -->
            <div class="col-md-4">
                <div class="glass-card h-100 p-4 text-center">
                    <div class="mb-4 mt-3">
                        <div class="icon-shape bg-warning text-white rounded-circle mx-auto shadow-lg" style="width: 80px; height: 80px; font-size: 2rem; display:flex; align-items:center; justify-content:center;">
                            <i class="fas fa-tooth"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold">Poli Gigi</h4>
                    <p class="text-muted mb-4">Layanan kesehatan gigi dan mulut profesional, mulai dari pembersihan karang hingga perawatan.</p>
                    <a href="pages/poli_gigi.php" class="btn btn-outline-warning text-dark rounded-pill px-4 stretched-link">Selengkapnya</a>
                </div>
            </div>

            <!-- Layanan 3: Poli Anak -->
            <div class="col-md-4">
                <div class="glass-card h-100 p-4 text-center">
                    <div class="mb-4 mt-3">
                        <div class="icon-shape bg-info text-white rounded-circle mx-auto shadow-lg" style="width: 80px; height: 80px; font-size: 2rem; display:flex; align-items:center; justify-content:center;">
                            <i class="fas fa-baby"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold">Poli Anak</h4>
                    <p class="text-muted mb-4">Layanan kesehatan khusus untuk tumbuh kembang anak dan imunisasi lengkap.</p>
                    <a href="pages/poli_anak.php" class="btn btn-outline-info text-dark rounded-pill px-4 stretched-link">Selengkapnya</a>
                </div>
            </div>

            <!-- Layanan 4: Poli Kandungan -->
            <div class="col-md-4">
                <div class="glass-card h-100 p-4 text-center">
                    <div class="mb-4 mt-3">
                        <div class="icon-shape bg-danger text-white rounded-circle mx-auto shadow-lg" style="width: 80px; height: 80px; font-size: 2rem; display:flex; align-items:center; justify-content:center;">
                            <i class="fas fa-female"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold">Poli Kandungan</h4>
                    <p class="text-muted mb-4">Pemeriksaan kehamilan (ANC), USG, dan kesehatan reproduksi wanita.</p>
                    <a href="pages/poli_kandungan.php" class="btn btn-outline-danger text-dark rounded-pill px-4 stretched-link">Selengkapnya</a>
                </div>
            </div>

            <!-- Layanan 5: Poli Penyakit Dalam -->
            <div class="col-md-4">
                <div class="glass-card h-100 p-4 text-center">
                    <div class="mb-4 mt-3">
                        <div class="icon-shape bg-primary bg-gradient text-white rounded-circle mx-auto shadow-lg" style="width: 80px; height: 80px; font-size: 2rem; display:flex; align-items:center; justify-content:center;">
                            <i class="fas fa-heartbeat"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold">Poli Penyakit Dalam</h4>
                    <p class="text-muted mb-4">Konsultasi dan penanganan penyakit organ dalam untuk dewasa dan lansia.</p>
                    <a href="pages/poli_penyakit_dalam.php" class="btn btn-outline-primary rounded-pill px-4 stretched-link">Selengkapnya</a>
                </div>
            </div>

            <!-- Layanan 6: Farmasi -->
            <div class="col-md-4">
                <div class="glass-card h-100 p-4 text-center">
                    <div class="mb-4 mt-3">
                        <div class="icon-shape bg-success text-white rounded-circle mx-auto shadow-lg" style="width: 80px; height: 80px; font-size: 2rem; display:flex; align-items:center; justify-content:center;">
                            <i class="fas fa-pills"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold">Instalasi Farmasi</h4>
                    <p class="text-muted mb-4">Apotek lengkap dengan apoteker bersertifikat. Obat berkualitas dan terjangkau.</p>
                    <a href="pages/farmasi.php" class="btn btn-outline-success rounded-pill px-4 stretched-link">Selengkapnya</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CTA Section Removed as requested -->

<?php include 'layout/footer.php'; ?>