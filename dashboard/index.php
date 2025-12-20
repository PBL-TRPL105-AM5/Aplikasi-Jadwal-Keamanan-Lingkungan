<?php
// Arahkan user ke dashboard sesuai role
include '../config/config.php';

// Jika belum login, kembalikan ke login
if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit;
}

// Ambil role pengguna
$role = $_SESSION['user']['role'];

// Routing berdasarkan role
switch ($role) {
    case 'Admin':
        header("Location: admin.php");
        break;

    case 'Koordinator':
        header("Location: koordinator.php");
        break;

    case 'Petugas':
        header("Location: petugas.php");
        break;

    default:
        echo "<h3 style='margin:20px;'>Role tidak dikenali.</h3>";
}

exit;
?>
