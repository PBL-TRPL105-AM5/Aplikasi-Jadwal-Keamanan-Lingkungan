<?php
// Memanggil konfigurasi dan memulai session
include '../config/config.php';

// Mengecek apakah user sudah login
if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit;
}

// Mengambil role pengguna dari session
$role = $_SESSION['user']['role'];

// Mengarahkan user ke dashboard sesuai dengan role
switch ($role) {
    case 'Admin':
        // Jika role Admin, arahkan ke dashboard admin
        header("Location: admin.php");
        break;

    case 'Koordinator':
        // Jika role Koordinator, arahkan ke dashboard koordinator
        header("Location: koordinator.php");
        break;

    case 'Petugas':
        // Jika role Petugas, arahkan ke dashboard petugas
        header("Location: petugas.php");
        break;

    default:
        // Jika role tidak dikenali, tampilkan pesan error
        echo "<h3 style='margin:20px;'>Role tidak dikenali.</h3>";
}

// Menghentikan eksekusi script setelah redirect
exit;
?>
