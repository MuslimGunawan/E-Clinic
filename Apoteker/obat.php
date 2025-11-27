<?php 
$path_prefix = '../'; 
include $path_prefix . 'layout/header.php'; 
include $path_prefix . 'auth_check.php';

if ($_SESSION['role'] !== 'apoteker' && $_SESSION['role'] !== 'admin') exit;

// --- LOGIKA CRUD OBAT ---

// 1. Tambah Obat
if (isset($_POST['tambah'])) {
    $nama   = htmlspecialchars($_POST['nama']);
    $jenis  = $_POST['jenis'];
    $stok   = $_POST['stok'];
    $harga  = $_POST['harga'];
    $satuan = $_POST['satuan'];

    $sql = "INSERT INTO obat (nama_obat, jenis, stok, harga, satuan) VALUES ('$nama', '$jenis', '$stok', '$harga', '$satuan')";
    if(mysqli_query($conn, $sql)) echo "<script>alert('Obat berhasil ditambahkan!'); window.location='obat.php';</script>";
}

// 2. Update Stok/Data
if (isset($_POST['update'])) {
    $id     = $_POST['id_obat'];
    $stok   = $_POST['stok'];
    $harga  = $_POST['harga'];
    // Logika update sederhana
    $sql = "UPDATE obat SET stok='$stok', harga='$harga' WHERE id_obat='$id'";
    if(mysqli_query($conn, $sql)) echo "<script>alert('Data obat diperbarui!'); window.location='obat.php';</script>";
}

// 3. Hapus Obat
if (isset($_GET['del'])) {
    $id = $_GET['del'];
    mysqli_query($conn, "DELETE FROM obat WHERE id_obat='$id'");
    echo "<script>window.location='obat.php';</script>";
}
?>

<div class="container mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-primary"><i class="fas fa-capsules me-2"></i>Manajemen Stok Obat</h3>
        <div>
            <a href="index.php" class="btn btn-secondary rounded-pill btn-back me-2">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
            <button class="btn btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalAdd">
                <i class="fas fa-plus me-2"></i>Obat Baru
            </button>
        </div>
    </div>

    <div class="card glass-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-transparent">
                    <tr>
                        <th class="ps-4">No</th>
                        <th>Nama Obat</th>
                        <th>Jenis</th>
                        <th>Stok</th>
                        <th>Harga</th>
                        <th class="text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no=1;
                    $q = mysqli_query($conn, "SELECT * FROM obat ORDER BY stok ASC"); // Urutkan dari stok paling sedikit
                    while($r = mysqli_fetch_assoc($q)):
                        // Visualisasi Stok
                        $stok_class = ($r['stok'] < 10) ? 'bg-danger text-white' : 'bg-light text-dark border';
                    ?>
                    <tr>
                        <td class="ps-4"><?= $no++ ?></td>
                        <td class="fw-bold text-primary"><?= $r['nama_obat'] ?></td>
                        <td><?= $r['jenis'] ?></td>
                        <td>
                            <span class="badge <?= $stok_class ?> rounded-pill px-3">
                                <?= $r['stok'] ?> <?= $r['satuan'] ?>
                            </span>
                        </td>
                        <td>Rp <?= number_format($r['harga']) ?></td>
                        <td class="text-end pe-4">
                            <button class="btn btn-sm btn-outline-warning me-1" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $r['id_obat'] ?>">
                                <i class="fas fa-edit"></i>
                            </button>
                            <a href="?del=<?= $r['id_obat'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus obat ini?')">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modals Edit Stok (Diluar Tabel) -->
<?php
mysqli_data_seek($q, 0);
while($r = mysqli_fetch_assoc($q)):
?>
<div class="modal fade" id="modalEdit<?= $r['id_obat'] ?>" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Update: <?= $r['nama_obat'] ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="id_obat" value="<?= $r['id_obat'] ?>">
                    <div class="mb-3">
                        <label>Stok Saat Ini</label>
                        <input type="number" name="stok" class="form-control" value="<?= $r['stok'] ?>" required>
                    </div>
                    <div class="mb-3">
                        <label>Harga Jual (Rp)</label>
                        <input type="number" name="harga" class="form-control" value="<?= $r['harga'] ?>" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="update" class="btn btn-success">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endwhile; ?>

<!-- Modal Tambah Obat -->
<div class="modal fade" id="modalAdd" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold">Tambah Obat Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Obat</label>
                        <input type="text" name="nama" class="form-control" placeholder="Contoh: Paracetamol 500mg" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Jenis</label>
                            <select name="jenis" class="form-select">
                                <option value="Tablet">Tablet</option>
                                <option value="Kapsul">Kapsul</option>
                                <option value="Sirup">Sirup</option>
                                <option value="Salep">Salep</option>
                                <option value="Injeksi">Injeksi</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Satuan</label>
                            <input type="text" name="satuan" class="form-control" placeholder="Strip/Botol" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Stok Awal</label>
                            <input type="number" name="stok" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Harga (Rp)</label>
                            <input type="number" name="harga" class="form-control" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-link text-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="tambah" class="btn btn-primary px-4 rounded-pill">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include $path_prefix . 'layout/footer.php'; ?>