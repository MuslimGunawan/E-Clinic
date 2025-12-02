<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$path = isset($path_prefix) ? $path_prefix : ''; 
include $path . 'config/koneksi.php'; 
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Clinic System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= $path ?>assets/css/style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<!-- Navbar Fixed Top agar melayang -->
<nav class="navbar navbar-expand-lg navbar-dark fixed-top" id="mainNavbar">
  <div class="container">
    <a class="navbar-brand fw-bold d-flex align-items-center" href="<?= $path ?>index.php?page=home">
        <img src="<?= $path ?>assets/img/E-Clinic_Logo.png" alt="Logo" height="40" class="me-2">
        E-CLINIC
        <?php if(isset($_SESSION['role'])): 
            $role_badge = 'bg-sky-solid text-white';
            if($_SESSION['role'] == 'admin') $role_badge = 'bg-danger text-white';
            if($_SESSION['role'] == 'dokter') $role_badge = 'bg-sky-solid text-white';
            if($_SESSION['role'] == 'resepsionis') $role_badge = 'bg-success text-white';
            if($_SESSION['role'] == 'apoteker') $role_badge = 'bg-warning text-dark';
        ?>
            <span class="badge <?= $role_badge ?> ms-3 small text-uppercase" style="font-size: 0.7rem;">
                <i class="fas fa-user-circle me-1"></i> <?= $_SESSION['role'] ?>
            </span>
        <?php endif; ?>
    </a>
    
    <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto align-items-center">
        <?php if (isset($_SESSION['login'])): ?>
            <li class="nav-item me-3">
                <span class="nav-link text-white">
                    Selamat Datang, <strong><?= $_SESSION['nama'] ?></strong>
                </span>
            </li>
            <li class="nav-item me-2">
                <a class="btn btn-light btn-sm fw-bold px-3 rounded-pill shadow-sm text-primary" href="<?= $path . $_SESSION['role'] ?>/">
                    <i class="fas fa-tachometer-alt me-1"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="btn btn-warning btn-sm fw-bold px-4 rounded-pill shadow-sm" href="<?= $path ?>auth/logout.php">
                    <i class="fas fa-sign-out-alt me-1"></i> Logout
                </a>
            </li>
        <?php else: ?>
            <li class="nav-item">
                <a class="btn btn-light text-primary fw-bold px-4 rounded-pill shadow-sm" href="<?= $path ?>auth/login.php">
                    <i class="fas fa-sign-in-alt me-1"></i> Login Petugas
                </a>
            </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>