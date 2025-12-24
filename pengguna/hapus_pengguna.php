<?php
// Memanggil konfigurasi database dan session
include '../config/config.php';

// Memastikan user sudah login
if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit;
}

// Membatasi akses: hanya Admin yang boleh menghapus pengguna
if ($_SESSION['user']['role'] !== 'Admin') {
    $_SESSION['error'] = "Anda tidak memiliki hak untuk menghapus pengguna!";
    header("Location: index.php");
    exit;
}

// Validasi parameter ID pengguna
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "ID pengguna tidak valid.";
    header("Location: index.php");
    exit;
}

// Mengamankan ID pengguna yang akan dihapus
$id = intval($_GET['id']);

// Mencegah admin menghapus akun miliknya sendiri
if ($id == $_SESSION['user']['id_pengguna']) {
    $_SESSION['error'] = "Anda tidak dapat menghapus akun Anda sendiri!";
    header("Location: index.php");
    exit;
}

// Query untuk menghapus data pengguna berdasarkan ID
$sql = "DELETE FROM tb_pengguna WHERE id_pengguna = $id";

// Eksekusi query dan set pesan notifikasi
if (mysqli_query($conn, $sql)) {
    $_SESSION['success'] = "Pengguna berhasil dihapus.";
} else {
    $_SESSION['error'] = "Gagal menghapus pengguna: " . mysqli_error($conn);
}

// Redirect kembali ke halaman daftar pengguna
header("Location: index.php");
exit;
