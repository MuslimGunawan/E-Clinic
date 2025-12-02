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

    // Upload Foto
    $foto = 'default.png';
    if(!empty($_FILES['foto']['name'])){
        $foto = time() . '_' . $_FILES['foto']['name'];
        move_uploaded_file($_FILES['foto']['tmp_name'], '../assets/img/obat/' . $foto);
    }

    $sql = "INSERT INTO obat (nama_obat, jenis, stok, harga, satuan, foto) VALUES ('$nama', '$jenis', '$stok', '$harga', '$satuan', '$foto')";
    if(mysqli_query($conn, $sql)) echo "<script>alert('Obat berhasil ditambahkan!'); window.location='obat.php';</script>";
}

// 2. Update Stok/Data
if (isset($_POST['update'])) {
    $id     = $_POST['id_obat'];
    $nama   = htmlspecialchars($_POST['nama']);
    $jenis  = $_POST['jenis'];
    $satuan = $_POST['satuan'];
    $stok   = $_POST['stok'];
    $harga  = $_POST['harga'];
    
    // Cek jika ada upload foto baru
    if(!empty($_FILES['foto']['name'])){
        $foto = time() . '_' . $_FILES['foto']['name'];
        move_uploaded_file($_FILES['foto']['tmp_name'], '../assets/img/obat/' . $foto);
        $sql = "UPDATE obat SET nama_obat='$nama', jenis='$jenis', satuan='$satuan', stok='$stok', harga='$harga', foto='$foto' WHERE id_obat='$id'";
    } else {
        $sql = "UPDATE obat SET nama_obat='$nama', jenis='$jenis', satuan='$satuan', stok='$stok', harga='$harga' WHERE id_obat='$id'";
    }

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
        <div class="d-flex gap-2">
            <a href="index.php" class="btn btn-secondary rounded-pill px-4 shadow-sm">
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
                        <th>Foto</th>
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
                        $foto_path = !empty($r['foto']) ? '../assets/img/obat/'.$r['foto'] : '../assets/img/obat/default.png';
                    ?>
                    <tr>
                        <td class="ps-4" data-label="No"><?= $no++ ?></td>
                        <td data-label="Foto">
                            <img src="<?= $foto_path ?>" class="rounded-3 shadow-sm" width="50" height="50" style="object-fit: cover;" onerror="this.onerror=null; this.src='https://via.placeholder.com/50?text=No+Img'">
                        </td>
                        <td class="fw-bold text-primary" data-label="Nama Obat"><?= $r['nama_obat'] ?></td>
                        <td data-label="Jenis"><?= $r['jenis'] ?></td>
                        <td data-label="Stok">
                            <span class="badge <?= $stok_class ?> rounded-pill px-3">
                                <?= $r['stok'] ?> <?= $r['satuan'] ?>
                            </span>
                        </td>
                        <td data-label="Harga">Rp <?= number_format($r['harga']) ?></td>
                        <td class="text-end pe-4" data-label="Aksi">
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
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="id_obat" value="<?= $r['id_obat'] ?>">
                    <div class="mb-3 text-center">
                        <?php $foto_path = !empty($r['foto']) ? '../assets/img/obat/'.$r['foto'] : '../assets/img/obat/default.png'; ?>
                        <img src="<?= $foto_path ?>" class="rounded-3 shadow-sm mb-2" width="100" height="100" style="object-fit: cover;" onerror="this.onerror=null; this.src='https://via.placeholder.com/100?text=No+Img'">
                        <br>
                        <small class="text-muted">Foto Saat Ini</small>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Nama Obat</label>
                        <input type="text" name="nama" class="form-control" value="<?= $r['nama_obat'] ?>" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Jenis</label>
                            <input type="text" name="jenis" class="form-control" list="jenisOptions" value="<?= $r['jenis'] ?>" placeholder="Pilih atau ketik..." required>
                            <datalist id="jenisOptions">
                                <option value="Tablet">
                                <option value="Kapsul">
                                <option value="Sirup">
                                <option value="Salep">
                                <option value="Injeksi">
                                <option value="Puyer">
                                <option value="Tetes Mata">
                                <option value="Tetes Telinga">
                                <option value="Inhaler">
                                <option value="Suppositoria">
                            </datalist>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Satuan</label>
                            <input type="text" name="satuan" class="form-control" value="<?= $r['satuan'] ?>" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Ganti Foto (Opsional)</label>
                        <input type="file" name="foto" class="form-control">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Stok Saat Ini</label>
                            <input type="number" name="stok" class="form-control" value="<?= $r['stok'] ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="fw-bold">Harga Jual (Rp)</label>
                            <input type="number" name="harga" class="form-control" value="<?= $r['harga'] ?>" required>
                        </div>
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
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nama Obat</label>
                        <input type="text" name="nama" class="form-control" placeholder="Contoh: Paracetamol 500mg" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Foto Obat</label>
                        <input type="file" name="foto" class="form-control">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Jenis</label>
                            <input type="text" name="jenis" class="form-control" list="jenisOptions" placeholder="Pilih atau ketik jenis baru..." required>
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