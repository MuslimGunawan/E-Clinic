<?php
session_start();

// Menghapus semua session yang tersimpan
session_unset();
session_destroy();

// Mengarahkan kembali ke halaman login
header("Location: login.php");
exit;
?>