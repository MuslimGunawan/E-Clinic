<?php 
$path_prefix = '../'; 
include $path_prefix . 'layout/header.php'; 
include $path_prefix . 'auth_check.php';

if ($_SESSION['role'] !== 'admin') exit;

if (isset($_POST['save'])) {
    $nama = $_POST['nama'];
    $lokasi = $_POST['lokasi'];
    mysqli_query($conn, "INSERT INTO poli (nama_poli, lokasi_ruangan) VALUES ('$nama', '$lokasi')");
    echo "<script>window.location='poli.php';</script>";
}

if (isset($_POST['update'])) {
    $id = $_POST['id_poli'];
    $nama = $_POST['nama'];
    $lokasi = $_POST['lokasi'];
    mysqli_query($conn, "UPDATE poli SET nama_poli='$nama', lokasi_ruangan='$lokasi' WHERE id_poli='$id'");
    echo "<script>window.location='poli.php';</script>";
}

if (isset($_GET['del'])) {
    $id = $_GET['del'];
    mysqli_query($conn, "DELETE FROM poli WHERE id_poli='$id'");
    echo "<script>window.location='poli.php';</script>";
}
?>

<div class="container mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-primary mb-0"><i class="fas fa-clinic-medical me-2"></i>Data Poliklinik</h3>
        <a href="index.php" class="btn btn-secondary rounded-pill px-4 shadow-sm">
            <i class="fas fa-arrow-left me-2"></i>Kembali
        </a>
    </div>
    
    <div class="row g-4">
        <!-- Form -->
        <div class="col-md-4">
            <div class="card glass-card h-100">
                <div class="card-header bg-transparent py-3 border-bottom fw-bold text-primary">
                    <i class="fas fa-plus-circle me-2"></i>Tambah Poli Baru
                </div>
                <div class="card-body p-4">
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label small text-muted fw-bold text-uppercase">Nama Poli</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-hospital-alt"></i></span>
                                <input type="text" name="nama" class="form-control" placeholder="Cth: Poli Mata" required>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label small text-muted fw-bold text-uppercase">Lokasi Ruangan</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-map-marker-alt"></i></span>
                                <input type="text" name="lokasi" class="form-control" placeholder="Cth: Gedung B Lt.1" required>
                            </div>
                        </div>
                        <button type="submit" name="save" class="btn btn-primary w-100 fw-bold rounded-pill">Simpan Poli</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Tabel -->
        <div class="col-md-8">
            <div class="card glass-card h-100 overflow-hidden">
                <div class="card-header bg-transparent py-3 fw-bold border-bottom">
                    Daftar Poli Tersedia
                </div>
                <div class="table-responsive h-100">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="bg-transparent text-secondary">
                            <tr>
                                <th class="ps-4">No</th>
                                <th>Nama Poli</th>
                                <th>Lokasi</th>
                                <th>Dokter Bertugas</th>
                                <th class="text-end pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no=1;
                            $q = mysqli_query($conn, "SELECT * FROM poli");
                            while($r = mysqli_fetch_assoc($q)):
                                // Hitung jumlah dokter di poli ini
                                $id_poli = $r['id_poli'];
                                $q_doc = mysqli_query($conn, "SELECT nama_dokter FROM dokter WHERE id_poli='$id_poli'");
                                $doc_list = [];
                                while($d = mysqli_fetch_assoc($q_doc)) {
                                    $doc_list[] = $d['nama_dokter'];
                                }
                                $doc_count = count($doc_list);
                            ?>
                            <tr>
                                <td class="ps-4 text-muted" data-label="No"><?= $no++ ?></td>
                                <td class="fw-bold text-primary" data-label="Nama Poli"><?= $r['nama_poli'] ?></td>
                                <td data-label="Lokasi"><span class="badge bg-light text-dark border"><i class="fas fa-map-pin me-1 text-danger"></i> <?= $r['lokasi_ruangan'] ?></span></td>
                                <td data-label="Dokter Bertugas">
                                    <?php if($doc_count > 0): ?>
                                        <div class="d-flex flex-column">
                                            <?php foreach($doc_list as $doc_name): ?>
                                                <small class="text-muted"><i class="fas fa-user-md me-1 text-primary"></i><?= $doc_name ?></small>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary">Kosong</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end pe-4" data-label="Aksi">
                                    <button class="btn btn-sm btn-outline-warning me-1" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $r['id_poli'] ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <a href="?del=<?= $r['id_poli'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus poli ini? Data dokter di poli ini akan kehilangan referensi!')"><i class="fas fa-trash"></i> Hapus</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modals Edit (Diluar Tabel agar tidak tertutup overflow) -->
<?php
mysqli_data_seek($q, 0);
while($r = mysqli_fetch_assoc($q)):
?>
<div class="modal fade" id="modalEdit<?= $r['id_poli'] ?>" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Poli</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="id_poli" value="<?= $r['id_poli'] ?>">
                    <div class="mb-3">
                        <label class="form-label">Nama Poli</label>
                        <input type="text" name="nama" class="form-control" value="<?= $r['nama_poli'] ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Lokasi</label>
                        <input type="text" name="lokasi" class="form-control" value="<?= $r['lokasi_ruangan'] ?>" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="update" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endwhile; ?>

<?php include $path_prefix . 'layout/footer.php'; ?>