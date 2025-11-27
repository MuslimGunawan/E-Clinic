<?php 
$path_prefix = '../'; 
include $path_prefix . 'layout/header.php'; 
include $path_prefix . 'auth_check.php';

if ($_SESSION['role'] !== 'dokter') exit;
?>

<div class="container mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-primary mb-0"><i class="fas fa-file-medical-alt me-2"></i>Laporan Riwayat Pasien</h3>
        <a href="index.php" class="btn btn-secondary rounded-pill btn-back">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>

    <!-- Alert Info View -->
    <div class="alert alert-info d-flex align-items-center shadow-sm border-0 glass-panel">
        <i class="fas fa-info-circle fa-2x me-3"></i>
        <div>
            <strong>Implementasi SQL VIEW & JOIN</strong><br>
            Data di bawah ini diambil dari Virtual Table (<code>v_riwayat_pasien</code>) yang menggabungkan 5 tabel secara otomatis.
        </div>
    </div>

    <div class="card glass-card mt-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-primary text-white">
                    <tr>
                        <th class="ps-4 py-3">Tgl Periksa</th>
                        <th class="py-3">Nama Pasien</th>
                        <th class="py-3">Dokter</th>
                        <th class="py-3">Poli</th>
                        <th class="py-3">Diagnosa</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Query ke VIEW, bukan tabel fisik
                    // Filter berdasarkan dokter yang login jika perlu, tapi VIEW ini global.
                    // Jika ingin filter per dokter:
                    $id_user = $_SESSION['id_user'];
                    $q_doc = mysqli_query($conn, "SELECT id_dokter FROM dokter WHERE id_user='$id_user'");
                    $d_doc = mysqli_fetch_assoc($q_doc);
                    $id_dokter = $d_doc['id_dokter'];

                    // Kita filter manual di WHERE clause query view
                    $q = mysqli_query($conn, "SELECT * FROM v_riwayat_pasien 
                                              WHERE nama_dokter = (SELECT nama_dokter FROM dokter WHERE id_dokter='$id_dokter') 
                                              ORDER BY tgl_periksa DESC");
                    
                    if(mysqli_num_rows($q) > 0):
                        while($r = mysqli_fetch_assoc($q)):
                    ?>
                    <tr>
                        <td class="ps-4"><?= date('d/m/Y H:i', strtotime($r['tgl_periksa'])) ?></td>
                        <td class="fw-bold"><?= $r['nama_pasien'] ?></td>
                        <td><?= $r['nama_dokter'] ?></td>
                        <td><span class="badge bg-info text-dark"><?= $r['nama_poli'] ?></span></td>
                        <td class="text-truncate" style="max-width: 250px;"><?= $r['diagnosa'] ?></td>
                    </tr>
                    <?php endwhile; else: ?>
                    <tr><td colspan="5" class="text-center py-4 text-muted">Belum ada riwayat pemeriksaan.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include $path_prefix . 'layout/footer.php'; ?>