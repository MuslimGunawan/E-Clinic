<?php 
$path_prefix = '../'; 
include $path_prefix . 'layout/header.php'; 
include $path_prefix . 'auth_check.php';

if ($_SESSION['role'] !== 'resepsionis' && $_SESSION['role'] !== 'admin') exit;

// INSERT DATA PASIEN (Group 2: DML)
if (isset($_POST['save'])) {
    $nik = htmlspecialchars($_POST['nik']);
    $nama = htmlspecialchars($_POST['nama']);
    $tgl = $_POST['tgl'];
    $jk = $_POST['jk'];
    $hp = htmlspecialchars($_POST['hp']);
    $alamat = htmlspecialchars($_POST['alamat']);

    $sql = "INSERT INTO pasien (nik, nama_pasien, tgl_lahir, jenis_kelamin, no_hp, alamat) 
            VALUES ('$nik', '$nama', '$tgl', '$jk', '$hp', '$alamat')";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Pasien berhasil didaftarkan!'); window.location='pasien.php';</script>";
    } else {
        echo "<script>alert('Gagal: ".mysqli_error($conn)."');</script>";
    }
}
?>

<div class="container mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-success"><i class="fas fa-id-card me-2"></i>Data Pasien</h3>
        <div>
            <a href="index.php" class="btn btn-secondary rounded-pill btn-back me-2">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
            <button class="btn btn-success rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalAdd">
                <i class="fas fa-user-plus me-2"></i>Pasien Baru
            </button>
        </div>
    </div>

    <div class="card glass-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-transparent">
                    <tr>
                        <th class="ps-4">NIK</th>
                        <th>Nama Lengkap</th>
                        <th>L/P</th>
                        <th>Usia</th>
                        <th>No HP</th>
                        <th>Alamat</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $q = mysqli_query($conn, "SELECT *, TIMESTAMPDIFF(YEAR, tgl_lahir, CURDATE()) AS usia FROM pasien ORDER BY id_pasien DESC");
                    while($r = mysqli_fetch_assoc($q)):
                    ?>
                    <tr>
                        <td class="ps-4 font-monospace"><?= $r['nik'] ?></td>
                        <td class="fw-bold"><?= $r['nama_pasien'] ?></td>
                        <td><?= $r['jenis_kelamin'] ?></td>
                        <td><?= $r['usia'] ?> Thn</td>
                        <td><?= $r['no_hp'] ?></td>
                        <td class="text-truncate" style="max-width: 150px;"><?= $r['alamat'] ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Pasien Baru -->
<div class="modal fade" id="modalAdd" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold">Registrasi Pasien Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">NIK (KTP)</label>
                            <input type="text" name="nik" class="form-control" required maxlength="16">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="nama" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Lahir</label>
                            <input type="date" name="tgl" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Jenis Kelamin</label>
                            <select name="jk" class="form-select">
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Nomor HP</label>
                            <input type="text" name="hp" class="form-control">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Alamat</label>
                            <textarea name="alamat" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-link text-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="save" class="btn btn-success px-4 rounded-pill">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include $path_prefix . 'layout/footer.php'; ?>