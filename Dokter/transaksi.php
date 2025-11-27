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
    // Kita sederhanakan dulu untuk 1 jenis obat demi demo Stored Procedure ACID
    $id_obat = $_POST['id_obat'];
    $jumlah  = $_POST['jumlah_obat'];

    // 1. Insert ke Rekam Medis dulu
    $sql_rm = "INSERT INTO rekam_medis (id_daftar, diagnosa, tindakan) VALUES ('$id_daftar', '$diagnosa', '$tindakan')";
    
    if (mysqli_query($conn, $sql_rm)) {
        $last_id_rm = mysqli_insert_id($conn); // Ambil ID Rekam Medis yang baru dibuat
        
        // 2. Panggil Stored Procedure Transaksi Obat (ACID)
        // "CALL sp_input_resep(id_rm, id_obat, jumlah)"
        $sql_sp = "CALL sp_input_resep($last_id_rm, $id_obat, $jumlah)";
        
        try {
            $res_sp = mysqli_query($conn, $sql_sp);
            $row_sp = mysqli_fetch_assoc($res_sp);
            $hasil  = $row_sp['Pesan'];

            // Update Status Pendaftaran jadi 'Selesai' jika sukses
            if (strpos($hasil, 'Berhasil') !== false) {
                // Kita perlu koneksi baru atau clear result agar bisa query lagi setelah SP
                mysqli_next_result($conn); 
                mysqli_query($conn, "UPDATE pendaftaran SET status='Selesai' WHERE id_daftar='$id_daftar'");
                
                $pesan = "<div class='alert alert-success shadow-sm'><i class='fas fa-check-circle me-2'></i>Pemeriksaan Selesai & Resep Diproses (Commit).</div>";
            } else {
                // Jika Stok Kurang -> Rollback terjadi di DB
                $pesan = "<div class='alert alert-danger shadow-sm'><i class='fas fa-exclamation-circle me-2'></i>Gagal: $hasil (Rollback). Stok Obat Kurang!</div>";
            }
        } catch (Exception $e) {
            $pesan = "<div class='alert alert-danger'>Error SP: ".$e->getMessage()."</div>";
        }
    } else {
        $pesan = "<div class='alert alert-danger'>Gagal Simpan RM: ".mysqli_error($conn)."</div>";
    }
}
?>

<div class="container mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-primary"><i class="fas fa-stethoscope me-2"></i>Pemeriksaan Medis</h3>
        <a href="index.php" class="btn btn-secondary rounded-pill btn-back px-3">
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
                    ?>
                    <div class="alert alert-info d-flex align-items-center shadow-sm mb-4">
                        <i class="fas fa-user-injured fa-2x me-3"></i>
                        <div>
                            <h5 class="fw-bold mb-0"><?= $d_detail['nama_pasien'] ?></h5>
                            <small>Keluhan: "<?= $d_detail['keluhan'] ?>"</small>
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
                        <h5 class="fw-bold text-success mb-3"><i class="fas fa-pills me-2"></i>Resep Obat</h5>
                        <div class="row g-3 align-items-end bg-light p-3 rounded-3 mb-4">
                            <div class="col-md-8">
                                <label class="small text-muted fw-bold">Nama Obat</label>
                                <select name="id_obat" class="form-select" required>
                                    <option value="">-- Pilih Obat --</option>
                                    <?php
                                    // Clear result set agar query obat jalan
                                    while(mysqli_more_results($conn) && mysqli_next_result($conn)); 
                                    
                                    $q_obat = mysqli_query($conn, "SELECT * FROM obat WHERE stok > 0");
                                    while($o = mysqli_fetch_assoc($q_obat)){
                                        echo "<option value='{$o['id_obat']}'>{$o['nama_obat']} (Sisa: {$o['stok']} {$o['satuan']})</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="small text-muted fw-bold">Jumlah</label>
                                <input type="number" name="jumlah_obat" class="form-control" min="1" value="1" required>
                            </div>
                        </div>

                        <button type="submit" name="simpan_periksa" class="btn btn-primary w-100 fw-bold py-2 rounded-pill shadow-sm">
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