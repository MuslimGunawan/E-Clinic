<?php 
$path_prefix = ''; 
include 'layout/header.php'; 
?>

<div class="container py-5" style="margin-top: 80px;">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="card-header bg-primary text-white p-4 text-center">
                    <h4 class="fw-bold mb-0"><i class="fas fa-history me-2"></i>Cek Riwayat Berobat & Antrian</h4>
                    <p class="mb-0 opacity-75">Masukkan data diri Anda untuk melihat riwayat medis</p>
                </div>
                <div class="card-body p-4 p-md-5">
                    
                    <?php if(!isset($_POST['cek_riwayat'])): ?>
                    <!-- FORM PENCARIAN -->
                    <form method="POST" action="">
                        <div class="mb-4">
                            <label for="nik" class="form-label fw-bold">Nomor Induk Kependudukan (NIK)</label>
                            <input type="text" class="form-control form-control-lg" id="nik" name="nik" placeholder="Contoh: 110101..." required>
                        </div>
                        <div class="mb-4">
                            <label for="tgl_lahir" class="form-label fw-bold">Tanggal Lahir</label>
                            <input type="date" class="form-control form-control-lg" id="tgl_lahir" name="tgl_lahir" required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" name="cek_riwayat" class="btn btn-primary btn-lg rounded-pill fw-bold">
                                <i class="fas fa-search me-2"></i>Cek Data Saya
                            </button>
                            <a href="index.php" class="btn btn-link text-muted mt-3 text-decoration-none">
                                <i class="fas fa-arrow-left me-1"></i> Kembali ke Beranda
                            </a>
                        </div>
                    </form>

                    <?php else: ?>
                    <!-- HASIL PENCARIAN -->
                    <?php
                        $nik = mysqli_real_escape_string($conn, $_POST['nik']);
                        $tgl_lahir = mysqli_real_escape_string($conn, $_POST['tgl_lahir']);

                        // 1. Cari Pasien
                        $query_pasien = "SELECT * FROM pasien WHERE nik = '$nik' AND tgl_lahir = '$tgl_lahir'";
                        $result_pasien = mysqli_query($conn, $query_pasien);
                        $pasien = mysqli_fetch_assoc($result_pasien);

                        if ($pasien):
                    ?>
                        <div class="alert alert-success d-flex align-items-center rounded-3 mb-4">
                            <i class="fas fa-check-circle fa-2x me-3"></i>
                            <div>
                                <h5 class="alert-heading fw-bold mb-0">Data Ditemukan!</h5>
                                <p class="mb-0">Halo, <strong><?= $pasien['nama_pasien'] ?></strong>. Berikut adalah riwayat kunjungan Anda.</p>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="py-3">Tanggal</th>
                                        <th class="py-3">Poli & Dokter</th>
                                        <th class="py-3">Keluhan & Diagnosa</th>
                                        <th class="py-3">Resep Obat</th>
                                        <th class="py-3 text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // 2. Ambil Riwayat Pendaftaran + Rekam Medis
                                    $id_pasien = $pasien['id_pasien'];
                                    $query_riwayat = "
                                        SELECT 
                                            p.tgl_kunjungan, 
                                            p.status, 
                                            p.keluhan,
                                            d.nama_dokter, 
                                            po.nama_poli,
                                            rm.diagnosa,
                                            rm.tindakan,
                                            rm.id_rm
                                        FROM pendaftaran p
                                        JOIN dokter d ON p.id_dokter = d.id_dokter
                                        JOIN poli po ON d.id_poli = po.id_poli
                                        LEFT JOIN rekam_medis rm ON p.id_daftar = rm.id_daftar
                                        WHERE p.id_pasien = '$id_pasien'
                                        ORDER BY p.tgl_kunjungan DESC
                                    ";
                                    $result_riwayat = mysqli_query($conn, $query_riwayat);

                                    if(mysqli_num_rows($result_riwayat) > 0):
                                        while($row = mysqli_fetch_assoc($result_riwayat)):
                                            // 3. Ambil Obat jika ada rekam medis
                                            $list_obat = [];
                                            if($row['id_rm']) {
                                                $id_rm = $row['id_rm'];
                                                $query_obat = "
                                                    SELECT o.nama_obat, dr.jumlah 
                                                    FROM detail_resep dr
                                                    JOIN obat o ON dr.id_obat = o.id_obat
                                                    WHERE dr.id_rm = '$id_rm'
                                                ";
                                                $res_obat = mysqli_query($conn, $query_obat);
                                                while($obat = mysqli_fetch_assoc($res_obat)){
                                                    $list_obat[] = $obat['nama_obat'] . " (" . $obat['jumlah'] . ")";
                                                }
                                            }
                                    ?>
                                    <tr>
                                        <td class="text-muted" data-label="Tanggal">
                                            <i class="far fa-calendar-alt me-1"></i> <?= date('d M Y', strtotime($row['tgl_kunjungan'])) ?><br>
                                            <small><?= date('H:i', strtotime($row['tgl_kunjungan'])) ?> WIB</small>
                                        </td>
                                        <td data-label="Poli & Dokter">
                                            <span class="badge bg-primary bg-opacity-10 text-primary mb-1"><?= $row['nama_poli'] ?></span><br>
                                            <small class="fw-bold"><?= $row['nama_dokter'] ?></small>
                                        </td>
                                        <td data-label="Diagnosa">
                                            <div class="mb-1"><span class="text-muted small">Keluhan:</span> <br><?= $row['keluhan'] ?></div>
                                            <?php if($row['diagnosa']): ?>
                                                <div class="mt-2 p-2 bg-light rounded border-start border-4 border-success">
                                                    <span class="text-success small fw-bold">Diagnosa:</span><br>
                                                    <?= $row['diagnosa'] ?>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                        <td data-label="Obat">
                                            <?php if(!empty($list_obat)): ?>
                                                <ul class="mb-0 ps-3 small text-muted">
                                                    <?php foreach($list_obat as $o): ?>
                                                        <li><?= $o ?></li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            <?php else: ?>
                                                <span class="text-muted small">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center" data-label="Status">
                                            <?php 
                                                $statusClass = 'bg-secondary';
                                                if($row['status'] == 'Selesai') $statusClass = 'bg-success';
                                                elseif($row['status'] == 'Diperiksa') $statusClass = 'bg-warning text-dark';
                                                elseif($row['status'] == 'Menunggu') $statusClass = 'bg-info text-dark';
                                            ?>
                                            <span class="badge <?= $statusClass ?> rounded-pill"><?= $row['status'] ?></span>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                    <?php else: ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            <img src="assets/img/no-data.svg" alt="" style="width: 100px; opacity: 0.5;" class="mb-3 d-block mx-auto">
                                            Belum ada riwayat kunjungan.
                                        </td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="text-center mt-4">
                            <a href="cek_riwayat.php" class="btn btn-outline-primary rounded-pill px-4 me-2">
                                <i class="fas fa-search me-2"></i>Cari Data Lain
                            </a>
                            <a href="index.php" class="btn btn-outline-secondary rounded-pill px-4">
                                <i class="fas fa-home me-2"></i>Beranda
                            </a>
                        </div>

                    <?php else: ?>
                        <div class="text-center py-5">
                            <div class="text-danger mb-3"><i class="fas fa-times-circle fa-4x"></i></div>
                            <h4 class="fw-bold text-danger">Data Tidak Ditemukan</h4>
                            <p class="text-muted">Kombinasi NIK dan Tanggal Lahir tidak cocok dengan database kami.<br>Silakan periksa kembali atau hubungi resepsionis.</p>
                            <div class="mt-3">
                                <a href="cek_riwayat.php" class="btn btn-light border rounded-pill px-4 me-2">Coba Lagi</a>
                                <a href="index.php" class="btn btn-link text-muted text-decoration-none">Kembali</a>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'layout/footer.php'; ?>