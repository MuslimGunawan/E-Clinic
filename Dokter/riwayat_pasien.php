<?php 
$path_prefix = '../'; 
include $path_prefix . 'layout/header.php'; 
include $path_prefix . 'auth_check.php';

if ($_SESSION['role'] !== 'dokter') exit;

// Ambil ID Dokter (Opsional, jika ingin filter pasien yang pernah ditangani dokter ini saja. 
// Tapi biasanya dokter boleh lihat riwayat semua pasien untuk referensi).
// Kita tampilkan semua pasien saja agar fleksibel.
?>

<div class="container mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-primary"><i class="fas fa-book-medical me-2"></i>Database Riwayat Pasien</h3>
        <div>
            <a href="index.php" class="btn btn-secondary rounded-pill btn-back me-2">
                <i class="fas fa-arrow-left me-2"></i>Kembali ke Dashboard
            </a>
        </div>
    </div>

    <!-- Search Bar Client Side -->
    <div class="card glass-card mb-4">
        <div class="card-body">
            <input type="text" id="searchPasien" class="form-control form-control-lg rounded-pill" placeholder="Cari nama pasien atau NIK...">
        </div>
    </div>

    <div class="card glass-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="tablePasien">
                <thead class="bg-transparent">
                    <tr>
                        <th class="ps-4">NIK</th>
                        <th>Nama Lengkap</th>
                        <th>L/P</th>
                        <th>Usia</th>
                        <th>Alamat</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Ambil semua data pasien
                    $q = mysqli_query($conn, "SELECT *, TIMESTAMPDIFF(YEAR, tgl_lahir, CURDATE()) AS usia FROM pasien ORDER BY nama_pasien ASC");
                    $data_pasien = [];
                    while($row = mysqli_fetch_assoc($q)){
                        $data_pasien[] = $row;
                    }

                    foreach($data_pasien as $r):
                        $id_clean = intval($r['id_pasien']);
                    ?>
                    <tr>
                        <td class="ps-4 font-monospace"><?= $r['nik'] ?></td>
                        <td class="fw-bold"><?= $r['nama_pasien'] ?></td>
                        <td><?= $r['jenis_kelamin'] ?></td>
                        <td><?= $r['usia'] ?> Thn</td>
                        <td class="small text-muted"><?= substr($r['alamat'], 0, 30) ?>...</td>
                        <td class="text-end pe-4">
                            <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalHistory<?= $id_clean ?>">
                                <i class="fas fa-file-medical-alt me-1"></i> Lihat Rekam Medis
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal History Pasien (Looping) -->
<?php
foreach($data_pasien as $r):
    $id_clean = intval($r['id_pasien']);
?>
<div class="modal fade" id="modalHistory<?= $id_clean ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl"> <!-- Modal Extra Large agar muat banyak info -->
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold"><i class="fas fa-history me-2"></i>Rekam Medis: <?= $r['nama_pasien'] ?></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="p-3 bg-light border-bottom">
                    <div class="row small">
                        <div class="col-md-3"><strong>NIK:</strong> <?= $r['nik'] ?></div>
                        <div class="col-md-3"><strong>Usia:</strong> <?= $r['usia'] ?> Tahun</div>
                        <div class="col-md-3"><strong>JK:</strong> <?= ($r['jenis_kelamin']=='L')?'Laki-laki':'Perempuan' ?></div>
                        <div class="col-md-3"><strong>No HP:</strong> <?= $r['no_hp'] ?></div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead class="bg-white text-primary">
                            <tr>
                                <th class="ps-4">Tanggal</th>
                                <th>Poli</th>
                                <th>Dokter</th>
                                <th>Keluhan</th>
                                <th>Diagnosa</th>
                                <th>Tindakan</th>
                                <th class="pe-4">Resep Obat</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Query History Lengkap (Join ke Rekam Medis)
                            // Note: Kita gunakan LEFT JOIN agar kunjungan yang belum ada RM-nya (misal baru daftar) tetap muncul statusnya
                            $q_hist = mysqli_query($conn, "SELECT pd.tgl_kunjungan, pd.keluhan, pd.status, 
                                                                  p.nama_poli, d.nama_dokter,
                                                                  rm.id_rm, rm.diagnosa, rm.tindakan
                                                           FROM pendaftaran pd 
                                                           JOIN dokter d ON pd.id_dokter = d.id_dokter 
                                                           JOIN poli p ON d.id_poli = p.id_poli 
                                                           LEFT JOIN rekam_medis rm ON pd.id_daftar = rm.id_daftar
                                                           WHERE pd.id_pasien = '{$id_clean}' 
                                                           ORDER BY pd.tgl_kunjungan DESC");
                            
                            if($q_hist && mysqli_num_rows($q_hist) > 0){
                                while($rh = mysqli_fetch_assoc($q_hist)){
                                    // Ambil Obat jika ada RM
                                    $obat_list = "-";
                                    if($rh['id_rm']) {
                                        $q_obat = mysqli_query($conn, "SELECT o.nama_obat, dr.jumlah, dr.aturan_pakai 
                                                                       FROM detail_resep dr 
                                                                       JOIN obat o ON dr.id_obat = o.id_obat 
                                                                       WHERE dr.id_rm = '{$rh['id_rm']}'");
                                        $obats = [];
                                        while($ro = mysqli_fetch_assoc($q_obat)){
                                            $obats[] = "{$ro['nama_obat']} ({$ro['jumlah']}) <i class='text-muted small'>{$ro['aturan_pakai']}</i>";
                                        }
                                        if(!empty($obats)) $obat_list = "<ul class='mb-0 ps-3'><li>" . implode("</li><li>", $obats) . "</li></ul>";
                                    }

                                    $status_badge = ($rh['status']=='Selesai') ? 'bg-success' : 'bg-warning text-dark';
                                    
                                    echo "<tr>
                                        <td class='ps-4'>".date('d/m/Y H:i', strtotime($rh['tgl_kunjungan']))."<br><span class='badge rounded-pill $status_badge'>{$rh['status']}</span></td>
                                        <td>{$rh['nama_poli']}</td>
                                        <td>{$rh['nama_dokter']}</td>
                                        <td>{$rh['keluhan']}</td>
                                        <td>".($rh['diagnosa'] ?: '<span class="text-muted">-</span>')."</td>
                                        <td>".($rh['tindakan'] ?: '<span class="text-muted">-</span>')."</td>
                                        <td class='pe-4'>$obat_list</td>
                                    </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='7' class='text-center py-4 text-muted'>Belum ada riwayat medis.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
<?php endforeach; ?>

<?php include $path_prefix . 'layout/footer.php'; ?>

<script>
// Simple Search Script
document.getElementById('searchPasien').addEventListener('keyup', function() {
    let filter = this.value.toLowerCase();
    let rows = document.querySelectorAll('#tablePasien tbody tr');
    
    rows.forEach(row => {
        let text = row.innerText.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
    });
});
</script>