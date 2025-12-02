<?php
session_start();
include '../config/koneksi.php';

// Jika sudah login, lempar langsung ke dashboard sesuai role
if (isset($_SESSION['login'])) {
    header("Location: ../" . $_SESSION['role'] . "/");
    exit;
}

$error = "";
if (isset($_POST['login'])) {
    $user = mysqli_real_escape_string($conn, $_POST['username']);
    $pass = md5($_POST['password']); // Sesuai database kita pakai MD5

    $q = mysqli_query($conn, "SELECT * FROM users WHERE username='$user' AND password='$pass'");
    
    if (mysqli_num_rows($q) > 0) {
        $data = mysqli_fetch_assoc($q);
        $_SESSION['login'] = true;
        $_SESSION['id_user'] = $data['id_user'];
        $_SESSION['role'] = $data['role'];
        $_SESSION['nama'] = $data['nama_lengkap'];
        
        // Redirect Dinamis ke folder role (admin/, dokter/, dll)
        header("Location: ../" . $data['role'] . "/");
        exit;
    } else {
        $error = "Username atau Password Salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login E-Clinic</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../assets/css/style.css" rel="stylesheet">
</head>
<body class="d-flex justify-content-center align-items-center vh-100 login-body">
    <div class="glass-panel p-4 login-card">
        <div class="text-center mb-4">
            <h3 class="text-primary fw-bold">LOGIN SYSTEM</h3>
            <p class="text-muted">Silakan masuk untuk memulai</p>
        </div>
        
        <?php if($error): ?>
            <div class="alert alert-danger p-2 small text-center"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label>Username</label>
                <input type="text" name="username" class="form-control" required autofocus>
            </div>
            <div class="mb-3">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" name="login" class="btn btn-primary w-100 fw-bold">MASUK</button>
        </form>
        <div class="text-center mt-3">
            <a href="../index.php" class="small text-decoration-none">Kembali ke Halaman Depan</a>
        </div>
    </div>
</body>
</html>