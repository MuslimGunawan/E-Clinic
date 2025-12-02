<?php 
$path_prefix = '../'; 
include $path_prefix . 'layout/header.php'; 
include $path_prefix . 'auth_check.php';

if ($_SESSION['role'] !== 'resepsionis' && $_SESSION['role'] !== 'admin') exit;
?>

<?php
// INSERT DATA PASIEN (Group 2: DML)
if (isset($_POST['save'])) {
    $nik = htmlspecialchars($_POST['nik']);
    $nama = htmlspecialchars($_POST['nama']);
    $tgl = $_POST['tgl'];
    $jk = $_POST['jk'];
    $hp = htmlspecialchars($_POST['hp']);
    $alamat = htmlspecialchars($_POST['alamat']);

    // Cek apakah NIK sudah ada
    $cek_nik = mysqli_query($conn, "SELECT nik FROM pasien WHERE nik = '$nik'");
    if (mysqli_num_rows($cek_nik) > 0) {
        echo "<script>alert('Gagal: NIK $nik sudah terdaftar atas nama pasien lain!'); window.history.back();</script>";
    } else {
        $sql = "INSERT INTO pasien (nik, nama_pasien, tgl_lahir, jenis_kelamin, no_hp, alamat) 
                VALUES ('$nik', '$nama', '$tgl', '$jk', '$hp', '$alamat')";

        if (mysqli_query($conn, $sql)) {
            echo "<script>alert('Pasien berhasil didaftarkan!'); window.location='pasien.php';</script>";
        } else {
            echo "<script>alert('Gagal: ".mysqli_error($conn)."');</script>";
        }
    }
}
// UPDATE DATA PASIEN
if (isset($_POST['update'])) {
    $id = $_POST['id_pasien'];
    $nik = htmlspecialchars($_POST['nik']);
    $nama = htmlspecialchars($_POST['nama']);
    $tgl = $_POST['tgl'];
    $jk = $_POST['jk'];
    $hp = htmlspecialchars($_POST['hp']);
    $alamat = htmlspecialchars($_POST['alamat']);

    $sql = "UPDATE pasien SET nik='$nik', nama_pasien='$nama', tgl_lahir='$tgl', jenis_kelamin='$jk', no_hp='$hp', alamat='$alamat' WHERE id_pasien='$id'";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Data pasien berhasil diperbarui!'); window.location='pasien.php';</script>";
    } else {
        echo "<script>alert('Gagal update: ".mysqli_error($conn)."');</script>";
    }
}

// DELETE DATA PASIEN
if (isset($_GET['del'])) {
    $id = $_GET['del'];
    if (mysqli_query($conn, "DELETE FROM pasien WHERE id_pasien='$id'")) {
        echo "<script>alert('Data pasien berhasil dihapus!'); window.location='pasien.php';</script>";
    } else {
        echo "<script>alert('Gagal hapus: ".mysqli_error($conn)."'); window.location='pasien.php';</script>";
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

    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-transparent">
                    <tr>
                        <th class="ps-4">NIK</th>
                        <th>Nama Lengkap</th>
                        <th>L/P</th>
                        <th>Usia</th>
                        <th>No HP</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Ambil semua data dulu ke array untuk menghindari masalah pointer mysqli
                    $q = mysqli_query($conn, "SELECT *, TIMESTAMPDIFF(YEAR, tgl_lahir, CURDATE()) AS usia FROM pasien ORDER BY id_pasien DESC");
                    $data_pasien = [];
                    while($row = mysqli_fetch_assoc($q)){
                        $data_pasien[] = $row;
                    }

                    // Loop untuk Tabel
                    foreach($data_pasien as $r):
                        $id_clean = intval($r['id_pasien']); // Pastikan ID integer bersih
                    ?>
                    <tr style="position: relative; z-index: 1;">
                        <td class="ps-4 font-monospace" data-label="NIK"><?= $r['nik'] ?></td>
                        <td class="fw-bold" data-label="Nama Lengkap"><?= $r['nama_pasien'] ?></td>
                        <td data-label="L/P"><?= $r['jenis_kelamin'] ?></td>
                        <td data-label="Usia"><?= $r['usia'] ?> Thn</td>
                        <td data-label="No HP"><?= $r['no_hp'] ?></td>
                        <td class="text-end pe-4" data-label="Aksi">
                            <button type="button" class="btn btn-sm btn-outline-info rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalHistory<?= $id_clean ?>">
                                <i class="fas fa-history me-1"></i> Riwayat
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalDetail<?= $id_clean ?>">
                                <i class="fas fa-info-circle me-1"></i> Detail
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-warning rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $id_clean ?>">
                                <i class="fas fa-edit"></i>
                            </button>
                            <a href="?del=<?= $id_clean ?>" class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="return confirm('Yakin hapus data pasien ini?')">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Detail Pasien (Looping dari Array) -->
<?php
foreach($data_pasien as $r):
    $id_clean = intval($r['id_pasien']);
?>
<!-- Modal History Pasien -->
<div class="modal fade" id="modalHistory<?= $id_clean ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title fw-bold"><i class="fas fa-history me-2"></i>Riwayat Kunjungan: <?= $r['nama_pasien'] ?></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Tanggal</th>
                                <th>Poli</th>
                                <th>Dokter</th>
                                <th>Keluhan</th>
                                <th class="pe-4">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Query History Kunjungan (FIXED: Join ke Poli via Dokter)
                            $q_hist = mysqli_query($conn, "SELECT pd.*, p.nama_poli, d.nama_dokter 
                                                           FROM pendaftaran pd 
                                                           JOIN dokter d ON pd.id_dokter = d.id_dokter 
                                                           JOIN poli p ON d.id_poli = p.id_poli 
                                                           WHERE pd.id_pasien = '{$id_clean}' 
                                                           ORDER BY pd.tgl_kunjungan DESC");
                            
                            if($q_hist && mysqli_num_rows($q_hist) > 0){
                                while($rh = mysqli_fetch_assoc($q_hist)){
                                    $badge = ($rh['status']=='Selesai') ? 'bg-success' : 'bg-warning text-dark';
                                    echo "<tr>
                                        <td class='ps-4'>".date('d/m/Y H:i', strtotime($rh['tgl_kunjungan']))."</td>
                                        <td>{$rh['nama_poli']}</td>
                                        <td>{$rh['nama_dokter']}</td>
                                        <td>{$rh['keluhan']}</td>
                                        <td class='pe-4'><span class='badge rounded-pill $badge'>{$rh['status']}</span></td>
                                    </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='5' class='text-center py-4 text-muted'>Belum ada riwayat kunjungan.</td></tr>";
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

<div class="modal fade" id="modalDetail<?= $id_clean ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold"><i class="fas fa-user me-2"></i>Detail Pasien</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-4">
                    <div class="bg-light rounded-circle d-inline-flex p-3 mb-2">
                        <i class="fas fa-user-circle fa-4x text-success"></i>
                    </div>
                    <h4 class="fw-bold mb-0"><?= $r['nama_pasien'] ?></h4>
                    <span class="badge bg-secondary rounded-pill"><?= $r['nik'] ?></span>
                </div>
                
                <div class="row g-3">
                    <div class="col-6">
                        <small class="text-muted text-uppercase fw-bold">Jenis Kelamin</small>
                        <p class="mb-0 fw-bold"><?= ($r['jenis_kelamin'] == 'L') ? 'Laki-laki' : 'Perempuan' ?></p>
                    </div>
                    <div class="col-6">
                        <small class="text-muted text-uppercase fw-bold">Usia</small>
                        <p class="mb-0 fw-bold"><?= $r['usia'] ?> Tahun</p>
                    </div>
                    <div class="col-6">
                        <small class="text-muted text-uppercase fw-bold">Tanggal Lahir</small>
                        <p class="mb-0 fw-bold"><?= date('d F Y', strtotime($r['tgl_lahir'])) ?></p>
                    </div>
                    <div class="col-6">
                        <small class="text-muted text-uppercase fw-bold">No. HP</small>
                        <p class="mb-0 fw-bold"><?= $r['no_hp'] ?></p>
                    </div>
                    <div class="col-12">
                        <div class="p-3 bg-light rounded border">
                            <small class="text-muted text-uppercase fw-bold d-block mb-1">Alamat Lengkap</small>
                            <p class="mb-0 text-dark"><?= $r['alamat'] ?></p>
                        </div>
                    </div>
                    <div class="col-12 mt-3">
                        <small class="text-muted text-uppercase fw-bold">Terdaftar Sejak</small>
                        <p class="mb-0 text-secondary small"><i class="far fa-clock me-1"></i> <?= date('d F Y H:i', strtotime($r['tgl_daftar'])) ?></p>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Pasien -->
<div class="modal fade" id="modalEdit<?= $id_clean ?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title fw-bold"><i class="fas fa-user-edit me-2"></i>Edit Data Pasien</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST">
                <div class="modal-body p-4">
                    <input type="hidden" name="id_pasien" value="<?= $id_clean ?>">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">NIK (KTP)</label>
                            <input type="text" name="nik" class="form-control" value="<?= $r['nik'] ?>" required maxlength="16">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="nama" class="form-control" value="<?= $r['nama_pasien'] ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tanggal Lahir</label>
                            <input type="date" name="tgl" class="form-control" value="<?= $r['tgl_lahir'] ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Jenis Kelamin</label>
                            <select name="jk" class="form-select">
                                <option value="L" <?= ($r['jenis_kelamin'] == 'L') ? 'selected' : '' ?>>Laki-laki</option>
                                <option value="P" <?= ($r['jenis_kelamin'] == 'P') ? 'selected' : '' ?>>Perempuan</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Nomor HP</label>
                            <input type="text" name="hp" class="form-control" value="<?= $r['no_hp'] ?>">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Alamat</label>
                            <textarea name="alamat" class="form-control" rows="2"><?= $r['alamat'] ?></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-link text-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="update" class="btn btn-warning px-4 rounded-pill fw-bold">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php 
    endforeach; 
?>
<div class="modal fade" id="modalTest" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">Test Modal</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Jika Anda melihat ini, berarti Bootstrap Modal berfungsi dengan baik.</p>
            </div>
        </div>
    </div>
</div>

<!-- Modal Pasien Baru -->
<div class="modal fade" id="modalAdd" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold">Registrasi Pasien Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
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