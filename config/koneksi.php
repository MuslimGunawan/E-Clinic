<?php
// File: eclinic/config/koneksi.php

// Konfigurasi Database
$host = "localhost";
$user = "root";     // User default XAMPP
$pass = "";         // Password default XAMPP (kosong)
$db   = "db_eclinic";

// Membuat koneksi
$conn = mysqli_connect($host, $user, $pass, $db);

// Cek koneksi
if (!$conn) {
    die("Koneksi Database Gagal: " . mysqli_connect_error());
}
?>