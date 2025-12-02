<?php 
$path_prefix = '../'; 
include $path_prefix . 'layout/header.php'; 
include $path_prefix . 'auth_check.php';

if ($_SESSION['role'] !== 'dokter') exit;

// Ambil ID Dokter berdasarkan ID User yang login
$id_user = $_SESSION['id_user'];
$q_doc = mysqli_query($conn, "SELECT id_dokter FROM dokter WHERE id_user='$id_user'");
$d_doc = mysqli_fetch_assoc($q_doc);
$id_dokter = $d_doc['id_dokter'];

// Handle parameter ID dari dashboard (Auto select pasien)
$selected_id = isset($_GET['id']) ? $_GET['id'] : '';

$pesan = "";

// --- LOGIKA TRANSAKSI REKAM MEDIS & RESEP (INTI NILAI UAS) ---
if (isset($_POST['simpan_periksa'])) {
    $id_daftar = $_POST['id_daftar'];
    $diagnosa  = htmlspecialchars($_POST['diagnosa']);
    $tindakan  = htmlspecialchars($_POST['tindakan']);
    
    // Array Obat & Jumlah (Multi Input)
    $id_obat_arr = $_POST['id_obat']; // Array
    $jumlah_arr  = $_POST['jumlah_obat']; // Array
    $aturan_arr  = $_POST['aturan_pakai']; // Array

    // 1. Insert ke Rekam Medis dulu
    $sql_rm = "INSERT INTO rekam_medis (id_daftar, diagnosa, tindakan) VALUES ('$id_daftar', '$diagnosa', '$tindakan')";
    
    if (mysqli_query($conn, $sql_rm)) {
        $last_id_rm = mysqli_insert_id($conn); // Ambil ID Rekam Medis yang baru dibuat
        
        $success_count = 0;
        $fail_count = 0;
        $error_msg = "";

        // Loop untuk setiap obat yang diinput
        for ($i = 0; $i < count($id_obat_arr); $i++) {
            $id_obat = $id_obat_arr[$i];
            $jumlah  = $jumlah_arr[$i];
            $aturan  = htmlspecialchars($aturan_arr[$i]);

            if (!empty($id_obat) && $jumlah > 0) {
                // 2. Panggil Stored Procedure Transaksi Obat (ACID)
                // "CALL sp_input_resep(id_rm, id_obat, jumlah, aturan_pakai)"
                $sql_sp = "CALL sp_input_resep($last_id_rm, $id_obat, $jumlah, '$aturan')";
                
                try {
                    // Clear previous results before calling SP again in loop
                    while(mysqli_more_results($conn) && mysqli_next_result($conn));

                    $res_sp = mysqli_query($conn, $sql_sp);
                    $row_sp = mysqli_fetch_assoc($res_sp);
                    $hasil  = $row_sp['Pesan'];

                    if (strpos($hasil, 'Berhasil') !== false) {
                        $success_count++;
                    } else {
                        $fail_count++;
                        $error_msg .= "Obat ID $id_obat: $hasil <br>";
                    }
                } catch (Exception $e) {
                    $fail_count++;
                    $error_msg .= "Error SP Obat ID $id_obat: ".$e->getMessage()."<br>";
                }
            }
        }

        // Update Status Pendaftaran jadi 'Selesai' jika setidaknya ada 1 obat berhasil atau hanya diagnosa tanpa obat
        // Note: Jika tidak ada obat, tetap dianggap sukses pemeriksaan
        if ($fail_count == 0) {
            // Clear result agar bisa query update
            while(mysqli_more_results($conn) && mysqli_next_result($conn));
            
            mysqli_query($conn, "UPDATE pendaftaran SET status='Selesai' WHERE id_daftar='$id_daftar'");
            $pesan = "<div class='alert alert-success shadow-sm'><i class='fas fa-check-circle me-2'></i>Pemeriksaan Selesai. $success_count Resep Diproses.</div>";
        } else {
            $pesan = "<div class='alert alert-warning shadow-sm'><i class='fas fa-exclamation-triangle me-2'></i>Pemeriksaan Selesai sebagian. <br>Sukses: $success_count <br>Gagal: $fail_count <br>Detail: $error_msg</div>";
        }

    } else {
        $pesan = "<div class='alert alert-danger'>Gagal Simpan RM: ".mysqli_error($conn)."</div>";
    }
}
?>

<div class="container mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-primary"><i class="fas fa-stethoscope me-2"></i>Pemeriksaan Medis</h3>
        <a href="index.php" class="btn btn-secondary rounded-pill px-4 shadow-sm">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>

    <?= $pesan ?>

    <div class="row g-4">
        <!-- Kolom Kiri: Form Pemeriksaan -->
        <div class="col-md-8">
            <div class="card glass-card">
                <div class="card-header bg-transparent fw-bold py-3 border-bottom">Formulir Dokter</div>
                <div class="card-body p-4">
                    
                    <?php if($selected_id): 
                        // Tampilkan Detail Pasien Terpilih
                        $q_detail = mysqli_query($conn, "SELECT p.*, pd.keluhan FROM pendaftaran pd JOIN pasien p ON pd.id_pasien=p.id_pasien WHERE pd.id_daftar='$selected_id'");
                        $d_detail = mysqli_fetch_assoc($q_detail);
                        $id_pasien_selected = $d_detail['id_pasien'];
                    ?>
                    <div class="alert alert-info d-flex align-items-center shadow-sm mb-4">
                        <i class="fas fa-user-injured fa-2x me-3"></i>
                        <div>
                            <h5 class="fw-bold mb-0"><?= $d_detail['nama_pasien'] ?></h5>
                            <small>Keluhan: "<?= $d_detail['keluhan'] ?>"</small>
                        </div>
                    </div>

                    <!-- Riwayat Medis Accordion -->
                    <div class="accordion mb-4" id="accordionRiwayat">
                        <div class="accordion-item border-0 shadow-sm rounded overflow-hidden">
                            <h2 class="accordion-header" id="headingOne">
                                <button class="accordion-button collapsed bg-light text-primary fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseRiwayat">
                                    <i class="fas fa-history me-2"></i> Riwayat Medis Pasien Sebelumnya
                                </button>
                            </h2>
                            <div id="collapseRiwayat" class="accordion-collapse collapse" data-bs-parent="#accordionRiwayat">
                                <div class="accordion-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-striped mb-0 small">
                                            <thead class="bg-primary text-white">
                                                <tr>
                                                    <th>Tanggal</th>
                                                    <th>Dokter</th>
                                                    <th>Diagnosa</th>
                                                    <th>Tindakan</th>
                                                    <th>Resep Obat</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                // Clear result set agar query jalan
                                                while(mysqli_more_results($conn) && mysqli_next_result($conn));

                                                $q_riwayat = mysqli_query($conn, "SELECT rm.*, pd.tgl_kunjungan, d.nama_dokter 
                                                                                  FROM rekam_medis rm 
                                                                                  JOIN pendaftaran pd ON rm.id_daftar = pd.id_daftar 
                                                                                  JOIN dokter d ON pd.id_dokter = d.id_dokter 
                                                                                  WHERE pd.id_pasien = '$id_pasien_selected' 
                                                                                  ORDER BY pd.tgl_kunjungan DESC LIMIT 5");
                                                
                                                if(mysqli_num_rows($q_riwayat) > 0){
                                                    while($rh = mysqli_fetch_assoc($q_riwayat)){
                                                        // Ambil obat untuk RM ini
                                                        $list_obat = [];
                                                        // Perlu query terpisah atau subquery. Kita pakai query simple di dalam loop (hati-hati performance, tapi ok untuk skala kecil)
                                                        $q_resep = mysqli_query($conn, "SELECT o.nama_obat, dr.jumlah, dr.aturan_pakai 
                                                                                        FROM detail_resep dr 
                                                                                        JOIN obat o ON dr.id_obat = o.id_obat 
                                                                                        WHERE dr.id_rm = '{$rh['id_rm']}'");
                                                        while($ro = mysqli_fetch_assoc($q_resep)){
                                                            $list_obat[] = "- {$ro['nama_obat']} ({$ro['jumlah']}) <br><small class='text-muted'>{$ro['aturan_pakai']}</small>";
                                                        }
                                                        $obat_str = implode("<br>", $list_obat);
                                                        
                                                        echo "<tr>
                                                            <td>".date('d/m/Y', strtotime($rh['tgl_kunjungan']))."</td>
                                                            <td>{$rh['nama_dokter']}</td>
                                                            <td>{$rh['diagnosa']}</td>
                                                            <td>{$rh['tindakan']}</td>
                                                            <td>".($obat_str ?: '-')."</td>
                                                        </tr>";
                                                    }
                                                } else {
                                                    echo "<tr><td colspan='5' class='text-center py-3 text-muted'>Belum ada riwayat medis sebelumnya.</td></tr>";
                                                }
                                                ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Pasien (Antrian)</label>
                            <select name="id_daftar" class="form-select bg-light" required onchange="window.location.href='?id='+this.value">
                                <option value="">-- Pilih Pasien --</option>
                                <?php
                                // Ambil pasien status Menunggu KHUSUS DOKTER INI
                                $q_antri = mysqli_query($conn, "SELECT pd.id_daftar, p.nama_pasien, pd.keluhan 
                                                                FROM pendaftaran pd JOIN pasien p ON pd.id_pasien=p.id_pasien 
                                                                WHERE pd.status='Menunggu' AND pd.id_dokter='$id_dokter'");
                                while($r = mysqli_fetch_assoc($q_antri)){
                                    $sel = ($selected_id == $r['id_daftar']) ? 'selected' : '';
                                    echo "<option value='{$r['id_daftar']}' $sel>{$r['nama_pasien']} - \"{$r['keluhan']}\"</option>";
                                }
                                ?>
                            </select>
                            <div class="form-text">Pilih pasien untuk memunculkan detail di atas.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Diagnosa Dokter</label>
                            <textarea name="diagnosa" class="form-control" rows="2" required placeholder="Hasil diagnosa penyakit..."></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Tindakan Medis</label>
                            <textarea name="tindakan" class="form-control" rows="2" required placeholder="Tindakan yang dilakukan..."></textarea>
                        </div>

                        <hr>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold text-success mb-0"><i class="fas fa-pills me-2"></i>Resep Obat</h5>
                            <button type="button" class="btn btn-sm btn-outline-success rounded-pill" onclick="addObatRow()">
                                <i class="fas fa-plus me-1"></i> Tambah Obat
                            </button>
                        </div>
                        
                        <div id="resep-container">
                            <!-- Baris Obat Pertama -->
                            <div class="row g-2 align-items-end bg-light p-3 rounded-3 mb-2 resep-row">
                                <div class="col-md-5">
                                    <label class="small text-muted fw-bold">Nama Obat</label>
                                    <select name="id_obat[]" class="form-select" required>
                                        <option value="">-- Pilih Obat --</option>
                                        <?php
                                        // Clear result set agar query obat jalan
                                        while(mysqli_more_results($conn) && mysqli_next_result($conn)); 
                                        
                                        $q_obat = mysqli_query($conn, "SELECT * FROM obat WHERE stok > 0");
                                        $obat_options = ""; // Simpan opsi untuk JS nanti
                                        while($o = mysqli_fetch_assoc($q_obat)){
                                            $opt = "<option value='{$o['id_obat']}'>{$o['nama_obat']} (Sisa: {$o['stok']} {$o['satuan']})</option>";
                                            echo $opt;
                                            $obat_options .= $opt; // Append string
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="small text-muted fw-bold">Jumlah</label>
                                    <input type="number" name="jumlah_obat[]" class="form-control" min="1" value="1" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="small text-muted fw-bold">Aturan Pakai</label>
                                    <input type="text" name="aturan_pakai[]" class="form-control" placeholder="Cth: 3x1 Sesudah Makan" required>
                                </div>
                                <div class="col-md-1">
                                    <button type="button" class="btn btn-danger w-100" onclick="removeRow(this)"><i class="fas fa-trash"></i></button>
                                </div>
                            </div>
                        </div>

                        <script>
                        function addObatRow() {
                            var container = document.getElementById('resep-container');
                            var row = document.createElement('div');
                            row.className = 'row g-2 align-items-end bg-light p-3 rounded-3 mb-2 resep-row';
                            row.innerHTML = `
                                <div class="col-md-5">
                                    <label class="small text-muted fw-bold">Nama Obat</label>
                                    <select name="id_obat[]" class="form-select" required>
                                        <option value="">-- Pilih Obat --</option>
                                        <?= $obat_options ?>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="small text-muted fw-bold">Jumlah</label>
                                    <input type="number" name="jumlah_obat[]" class="form-control" min="1" value="1" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="small text-muted fw-bold">Aturan Pakai</label>
                                    <input type="text" name="aturan_pakai[]" class="form-control" placeholder="Cth: 3x1 Sesudah Makan" required>
                                </div>
                                <div class="col-md-1">
                                    <button type="button" class="btn btn-danger w-100" onclick="removeRow(this)"><i class="fas fa-trash"></i></button>
                                </div>
                            `;
                            container.appendChild(row);
                        }

                        function removeRow(btn) {
                            var row = btn.closest('.resep-row');
                            // Sisakan minimal 1 baris
                            if(document.querySelectorAll('.resep-row').length > 1) {
                                row.remove();
                            } else {
                                alert("Minimal satu obat harus diisi (atau kosongkan jika tidak ada resep).");
                            }
                        }
                        </script>

                        <button type="submit" name="simpan_periksa" class="btn btn-primary w-100 fw-bold py-2 rounded-pill shadow-sm mt-3">
                            <i class="fas fa-save me-2"></i>Simpan Rekam Medis & Resep
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Info Stok -->
        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white fw-bold py-3 border-bottom text-danger">
                    <i class="fas fa-exclamation-circle me-2"></i>Monitor Stok Obat
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped mb-0 table-sm small">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Obat</th>
                                <th>Stok</th>
                                <th class="pe-3">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            mysqli_data_seek($q_obat, 0); // Reset pointer
                            while($o = mysqli_fetch_assoc($q_obat)):
                                $color = ($o['stok'] < 10) ? 'text-danger fw-bold' : 'text-success';
                            ?>
                            <tr>
                                <td class="ps-3"><?= $o['nama_obat'] ?></td>
                                <td class="<?= $color ?>"><?= $o['stok'] ?></td>
                                <td class="pe-3"><?= ($o['stok']<10)?'Kritis':'Aman' ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-light small text-muted">
                    *Jika stok obat tidak cukup, sistem otomatis <strong>Rollback</strong> (Batalkan Transaksi).
                </div>
            </div>
        </div>
    </div>
</div>

<?php include $path_prefix . 'layout/footer.php'; ?>