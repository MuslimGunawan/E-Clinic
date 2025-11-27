<?php
// File ini berfungsi sebagai "Satpam".
// Diletakkan di root folder, dan di-include oleh file di sub-folder (admin, dokter, dll).

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Jika User BELUM Login
if (!isset($_SESSION['login'])) {
    // Kita asumsikan file ini di-include dari sub-folder (misal: admin/index.php)
    // Maka path login relatif adalah ../auth/login.php
    // Jika ingin lebih robust, gunakan absolute path atau deteksi path
    
    $login_path = '../auth/login.php'; 
    
    // Redirect paksa ke halaman login
    header("Location: " . $login_path);
    exit;
}
?>