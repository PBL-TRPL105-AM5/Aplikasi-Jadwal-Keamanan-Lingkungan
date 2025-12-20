<?php
session_start();
include '../config/config.php';

// Hanya Admin & Koordinator yang boleh mengubah status insiden
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['Admin', 'Koordinator'])) {
    $_SESSION['error'] = "Akses ditolak!";
    header("Location: index.php");
    exit;
}

// Jika form dikirim
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id_insiden     = $_POST['id_insiden'];
    $status         = $_POST['status'];
    $catatan_admin  = mysqli_real_escape_string($conn, $_POST['catatan_admin']);

    // Validasi status
    $status_valid = ['pending', 'diterima', 'ditolak'];
    if (!in_array($status, $status_valid)) {
        $_SESSION['error'] = "Status tidak valid!";
        header("Location: index.php");
        exit;
    }

    // Update data
    $query = "
        UPDATE tb_insiden 
        SET 
            status = '$status',
            catatan_admin = '$catatan_admin'
        WHERE id_insiden = '$id_insiden'
    ";

    if (mysqli_query($conn, $query)) {
        $_SESSION['success'] = "Status insiden berhasil diperbarui!";
    } else {
        $_SESSION['error'] = "Gagal memperbarui data!";
    }

    header("Location: index.php");
    exit;
}
?>
