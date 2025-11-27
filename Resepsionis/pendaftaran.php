<?php 
$path_prefix = '../'; 
include $path_prefix . 'layout/header.php'; 
include $path_prefix . 'auth_check.php';

if ($_SESSION['role'] !== 'resepsionis' && $_SESSION['role'] !== 'admin') exit;

// LOGIKA PENDAFTARAN KUNJUNGAN
if (isset($_POST['daftar'])) {
    $id_pasien = $_POST['pasien'];
    $id_dokter = $_POST['dokter'];
    $keluhan   = htmlspecialchars($_POST['keluhan']);

    // Status Default = Menunggu
    $sql = "INSERT INTO pendaftaran (id_pasien, id_dokter, keluhan, status) VALUES ('$id_pasien', '$id_dokter', '$keluhan', 'Menunggu')";
    
    if(mysqli_query($conn, $sql)) {
        echo "<script>alert('Kunjungan berhasil didaftarkan!'); window.location='index.php';</script>";
    } else {
        echo "<script>alert('Error: ".mysqli_error($conn)."');</script>";
    }
}
?>

<div class="container mb-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card glass-card mt-4">
                <div class="card-header bg-success text-white py-3 fw-bold">
                    <div class="d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-notes-medical me-2"></i>Form Pendaftaran Kunjungan</span>
                        <a href="index.php" class="btn btn-sm btn-outline-light btn-back">Kembali</a>
                    </div>
                </div>
                <div class="card-body p-4">
                    <form method="POST">
                        <!-- Pilih Pasien -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Cari Pasien</label>
                            <select name="pasien" class="form-select" required>
                                <option value="">-- Pilih Pasien --</option>
                                <?php
                                $qp = mysqli_query($conn, "SELECT * FROM pasien ORDER BY nama_pasien ASC");
                                while($rp = mysqli_fetch_assoc($qp)){
                                    echo "<option value='{$rp['id_pasien']}'>{$rp['nama_pasien']} (NIK: {$rp['nik']})</option>";
                                }
                                ?>
                            </select>
                            <div class="form-text">Jika pasien belum ada, daftarkan dulu di menu <a href="pasien.php">Data Pasien</a>.</div>
                        </div>

                        <!-- Pilih Dokter -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Pilih Dokter Tujuan</label>
                            <div class="row g-2">
                                <?php
                                // Join dengan Poli agar jelas dokternya spesialis apa
                                $qd = mysqli_query($conn, "SELECT d.id_dokter, d.nama_dokter, p.nama_poli 
                                                           FROM dokter d JOIN poli p ON d.id_poli = p.id_poli");
                                while($rd = mysqli_fetch_assoc($qd)):
                                ?>
                                <div class="col-md-6">
                                    <input type="radio" class="btn-check" name="dokter" id="doc_<?= $rd['id_dokter'] ?>" value="<?= $rd['id_dokter'] ?>" required>
                                    <label class="btn btn-outline-light text-dark border w-100 text-start p-3 shadow-sm h-100" for="doc_<?= $rd['id_dokter'] ?>">
                                        <div class="fw-bold text-success"><?= $rd['nama_dokter'] ?></div>
                                        <small class="text-muted"><?= $rd['nama_poli'] ?></small>
                                    </label>
                                </div>
                                <?php endwhile; ?>
                            </div>
                        </div>

                        <!-- Keluhan -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Keluhan Utama</label>
                            <textarea name="keluhan" class="form-control" rows="3" placeholder="Contoh: Demam tinggi sudah 3 hari..." required></textarea>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" name="daftar" class="btn btn-success fw-bold py-2 rounded-pill">DAFTARKAN KUNJUNGAN</button>
                            <a href="index.php" class="btn btn-link text-secondary text-decoration-none">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include $path_prefix . 'layout/footer.php'; ?>