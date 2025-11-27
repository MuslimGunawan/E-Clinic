<?php 
$path_prefix = '../'; 
include $path_prefix . 'layout/header.php'; 
include $path_prefix . 'auth_check.php';

if ($_SESSION['role'] !== 'admin') exit;

// --- LOGIKA TAMBAH USER ---
if (isset($_POST['add'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $user = mysqli_real_escape_string($conn, $_POST['user']);
    $pass = md5($_POST['pass']);
    $role = $_POST['role'];
    
    // Insert ke tabel users
    $sql = "INSERT INTO users (username, password, role, nama_lengkap) VALUES ('$user', '$pass', '$role', '$nama')";
    
    if(mysqli_query($conn, $sql)) {
        $new_user_id = mysqli_insert_id($conn); // Ambil ID User yang baru dibuat

        // Jika Role adalah Dokter, masukkan juga ke tabel 'dokter' dengan referensi id_user
        if ($role == 'dokter') {
            $no_hp = $_POST['no_hp'];
            $id_poli = $_POST['id_poli'];
            
            // Ambil nama poli untuk mengisi kolom spesialisasi
            $q_poli = mysqli_query($conn, "SELECT nama_poli FROM poli WHERE id_poli='$id_poli'");
            $d_poli = mysqli_fetch_assoc($q_poli);
            $spesialisasi = str_replace("Poli ", "", $d_poli['nama_poli']); 
            
            // Perbaikan: Pastikan id_user disertakan
            $sql_dokter = "INSERT INTO dokter (id_user, nama_dokter, spesialisasi, no_hp, id_poli) 
                           VALUES ('$new_user_id', '$nama', '$spesialisasi', '$no_hp', '$id_poli')";
            
            if (!mysqli_query($conn, $sql_dokter)) {
                // Jika gagal insert dokter, hapus user yang baru dibuat agar tidak jadi sampah
                mysqli_query($conn, "DELETE FROM users WHERE id_user='$new_user_id'");
                echo "<script>alert('Gagal menambahkan data dokter: " . mysqli_error($conn) . "'); window.location='users.php';</script>";
                exit;
            }
        }

        echo "<script>alert('User $nama berhasil ditambahkan!'); window.location='users.php';</script>";
    }
    else {
        echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
    }
}

// --- LOGIKA UPDATE USER ---
if (isset($_POST['update'])) {
    $id = $_POST['id_user'];
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $role = $_POST['role'];
    
    // Update tabel users
    $sql_update = "UPDATE users SET nama_lengkap='$nama', role='$role'";
    if (!empty($_POST['pass'])) {
        $pass = md5($_POST['pass']);
        $sql_update .= ", password='$pass'";
    }
    $sql_update .= " WHERE id_user='$id'";
    mysqli_query($conn, $sql_update);

    // Sinkronisasi Data Dokter
    if ($role == 'dokter') {
        $no_hp = $_POST['no_hp'];
        $id_poli = $_POST['id_poli'];
        
        // Ambil nama poli
        $q_poli = mysqli_query($conn, "SELECT nama_poli FROM poli WHERE id_poli='$id_poli'");
        $d_poli = mysqli_fetch_assoc($q_poli);
        $spesialisasi = str_replace("Poli ", "", $d_poli['nama_poli']);

        // Cek apakah sudah ada data di tabel dokter untuk user ini
        $cek_dokter = mysqli_query($conn, "SELECT id_dokter FROM dokter WHERE id_user='$id'");
        
        if (mysqli_num_rows($cek_dokter) > 0) {
            // Jika sudah ada, UPDATE
            $sql_dokter = "UPDATE dokter SET nama_dokter='$nama', spesialisasi='$spesialisasi', no_hp='$no_hp', id_poli='$id_poli' 
                           WHERE id_user='$id'";
        } else {
            // Jika belum ada (misal dulunya admin lalu diubah jadi dokter), INSERT
            $sql_dokter = "INSERT INTO dokter (id_user, nama_dokter, spesialisasi, no_hp, id_poli) 
                           VALUES ('$id', '$nama', '$spesialisasi', '$no_hp', '$id_poli')";
        }
        mysqli_query($conn, $sql_dokter);
    } else {
        // Jika role BUKAN dokter, hapus data dari tabel dokter jika ada (Cleanup)
        mysqli_query($conn, "DELETE FROM dokter WHERE id_user='$id'");
    }
    
    echo "<script>window.location='users.php';</script>";
}

if (isset($_GET['del'])) {
    $id = $_GET['del'];
    if ($id != $_SESSION['id_user']) {
        // Hapus dari dokter dulu (foreign key logic manual)
        mysqli_query($conn, "DELETE FROM dokter WHERE id_user='$id'");
        // Baru hapus user
        mysqli_query($conn, "DELETE FROM users WHERE id_user='$id'");
        echo "<script>window.location='users.php';</script>";
    }
}
?>

<div class="container mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-primary"><i class="fas fa-users-cog me-2"></i>Manajemen Users</h3>
        <div>
            <a href="index.php" class="btn btn-secondary rounded-pill btn-back me-2">
                <i class="fas fa-arrow-left me-2"></i>Kembali
            </a>
            <button class="btn btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalAdd">
                <i class="fas fa-plus me-2"></i>Tambah User
            </button>
        </div>
    </div>

    <div class="card glass-card overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-transparent">
                    <tr>
                        <th class="ps-4 py-3">No</th>
                        <th class="py-3">Nama Lengkap</th>
                        <th class="py-3">Username</th>
                        <th class="py-3">Role</th>
                        <th class="py-3 text-end pe-4">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    // Join dengan tabel dokter untuk mengambil data tambahan jika ada
                    $q = mysqli_query($conn, "SELECT u.*, d.id_poli, d.no_hp FROM users u LEFT JOIN dokter d ON u.id_user = d.id_user ORDER BY u.role ASC");
                    
                    // Pre-fetch poli list untuk digunakan di modal edit dan add
                    $q_poli_list = mysqli_query($conn, "SELECT * FROM poli");
                    
                    while($r = mysqli_fetch_assoc($q)):
                    ?>
                    <tr>
                        <td class="ps-4"><?= $no++ ?></td>
                        <td class="fw-bold">
                            <?= $r['nama_lengkap'] ?>
                            <?php if($r['role'] == 'dokter'): ?>
                                <br><small class="text-muted"><i class="fas fa-stethoscope me-1"></i><?= $r['no_hp'] ?? '-' ?></small>
                            <?php endif; ?>
                        </td>
                        <td class="text-muted">@<?= $r['username'] ?></td>
                        <td>
                            <?php 
                                $badge = 'bg-secondary';
                                if($r['role']=='admin') $badge='bg-danger';
                                if($r['role']=='dokter') $badge='bg-primary';
                                if($r['role']=='resepsionis') $badge='bg-success';
                                if($r['role']=='apoteker') $badge='bg-warning text-dark';
                            ?>
                            <span class="badge <?= $badge ?> rounded-pill px-3"><?= strtoupper($r['role']) ?></span>
                        </td>
                        <td class="text-end pe-4">
                            <button class="btn btn-sm btn-outline-warning me-1" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $r['id_user'] ?>">
                                <i class="fas fa-edit"></i>
                            </button>
                            <?php if($r['id_user'] != $_SESSION['id_user']): ?>
                            <a href="?del=<?= $r['id_user'] ?>" class="btn btn-sm btn-outline-danger rounded-circle" onclick="return confirm('Yakin hapus user ini?')" title="Hapus">
                                <i class="fas fa-trash"></i>
                            </a>
                            <?php else: ?>
                                <span class="text-muted small fst-italic">Akun Anda</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modals Edit User (Diluar Tabel) -->
<?php
mysqli_data_seek($q, 0);
while($r = mysqli_fetch_assoc($q)):
?>
<div class="modal fade" id="modalEdit<?= $r['id_user'] ?>" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit User: <?= $r['username'] ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" name="id_user" value="<?= $r['id_user'] ?>">
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control" value="<?= $r['nama_lengkap'] ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select name="role" id="roleEdit<?= $r['id_user'] ?>" class="form-select" onchange="toggleDokterEdit(<?= $r['id_user'] ?>)">
                            <option value="admin" <?= ($r['role']=='admin')?'selected':'' ?>>Admin</option>
                            <option value="dokter" <?= ($r['role']=='dokter')?'selected':'' ?>>Dokter</option>
                            <option value="resepsionis" <?= ($r['role']=='resepsionis')?'selected':'' ?>>Resepsionis</option>
                            <option value="apoteker" <?= ($r['role']=='apoteker')?'selected':'' ?>>Apoteker</option>
                        </select>
                    </div>

                    <!-- Field Khusus Dokter di Edit (Hidden by default unless role is dokter) -->
                    <div id="dokterEditFields<?= $r['id_user'] ?>" class="<?= ($r['role']=='dokter')?'':'d-none' ?> p-3 mb-3 bg-info bg-opacity-10 rounded border border-info">
                        <h6 class="text-info fw-bold mb-3"><i class="fas fa-user-md me-2"></i>Data Detail Dokter</h6>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-secondary">Spesialisasi / Poli</label>
                            <select name="id_poli" class="form-select">
                                <option value="">-- Pilih Poli --</option>
                                <?php
                                // Reset pointer poli list untuk setiap modal
                                mysqli_data_seek($q_poli_list, 0);
                                while($rp = mysqli_fetch_assoc($q_poli_list)){
                                    $selected = ($r['id_poli'] == $rp['id_poli']) ? 'selected' : '';
                                    echo "<option value='{$rp['id_poli']}' $selected>{$rp['nama_poli']}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-secondary">No. HP</label>
                            <input type="text" name="no_hp" class="form-control" value="<?= $r['no_hp'] ?? '' ?>" placeholder="0812...">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Password Baru <small class="text-muted">(Kosongkan jika tidak ubah)</small></label>
                        <input type="password" name="pass" class="form-control" placeholder="***">
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

<script>
function toggleDokterEdit(id) {
    var role = document.getElementById('roleEdit' + id).value;
    var fields = document.getElementById('dokterEditFields' + id);
    if(role === 'dokter') {
        fields.classList.remove('d-none');
    } else {
        fields.classList.add('d-none');
    }
}
</script>

<!-- Modal Add User -->
<div class="modal fade" id="modalAdd" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold"><i class="fas fa-user-plus me-2"></i>Tambah User Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control" placeholder="Cth: Dr. Budi Santoso" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-secondary">Username</label>
                            <input type="text" name="user" class="form-control" placeholder="Username Login" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold text-secondary">Password</label>
                            <input type="password" name="pass" class="form-control" placeholder="******" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold text-secondary">Role / Jabatan</label>
                        <select name="role" id="roleSelect" class="form-select bg-light" required onchange="toggleDokterFields()">
                            <option value="" disabled selected>-- Pilih Role --</option>
                            <option value="dokter">Dokter (Medis)</option>
                            <option value="resepsionis">Resepsionis (Pendaftaran)</option>
                            <option value="apoteker">Apoteker (Obat)</option>
                            <option value="admin">Admin (IT/Manager)</option>
                        </select>
                    </div>

                    <!-- Field Khusus Dokter (Hidden by default) -->
                    <div id="dokterFields" class="d-none p-3 mb-3 bg-info bg-opacity-10 rounded border border-info">
                        <h6 class="text-info fw-bold mb-3"><i class="fas fa-user-md me-2"></i>Data Detail Dokter</h6>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-secondary">Spesialisasi / Poli</label>
                            <select name="id_poli" class="form-select">
                                <option value="">-- Pilih Poli --</option>
                                <?php
                                $q_poli_list_add = mysqli_query($conn, "SELECT * FROM poli");
                                while($rp = mysqli_fetch_assoc($q_poli_list_add)){
                                    echo "<option value='{$rp['id_poli']}'>{$rp['nama_poli']}</option>";
                                }
                                ?>
                            </select>
                            <div class="form-text text-muted">Wajib dipilih jika role adalah Dokter.</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold text-secondary">No. HP</label>
                            <input type="text" name="no_hp" class="form-control" placeholder="0812...">
                        </div>
                    </div>

                    <script>
                    function toggleDokterFields() {
                        var role = document.getElementById('roleSelect').value;
                        var fields = document.getElementById('dokterFields');
                        if(role === 'dokter') {
                            fields.classList.remove('d-none');
                        } else {
                            fields.classList.add('d-none');
                        }
                    }
                    </script>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-link text-secondary text-decoration-none" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="add" class="btn btn-primary px-4 fw-bold rounded-pill">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include $path_prefix . 'layout/footer.php'; ?>