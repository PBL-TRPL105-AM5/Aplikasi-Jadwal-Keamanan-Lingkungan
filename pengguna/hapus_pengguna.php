<?php
include '../config/config.php';

// Pastikan user login
if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit;
}

// Hanya Admin yang boleh menghapus pengguna
if ($_SESSION['user']['role'] !== 'Admin') {
    $_SESSION['error'] = "Anda tidak memiliki hak untuk menghapus pengguna!";
    header("Location: index.php");
    exit;
}

// Validasi ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $_SESSION['error'] = "ID pengguna tidak valid.";
    header("Location: index.php");
    exit;
}

$id = intval($_GET['id']);

// Cegah admin menghapus akun dirinya sendiri
if ($id == $_SESSION['user']['id_pengguna']) {
    $_SESSION['error'] = "Anda tidak dapat menghapus akun Anda sendiri!";
    header("Location: index.php");
    exit;
}

// Jalankan query hapus
$sql = "DELETE FROM tb_pengguna WHERE id_pengguna = $id";

if (mysqli_query($conn, $sql)) {
    $_SESSION['success'] = "Pengguna berhasil dihapus.";
} else {
    $_SESSION['error'] = "Gagal menghapus pengguna: " . mysqli_error($conn);
}

header("Location: index.php");
exit;
