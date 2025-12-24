<?php
// Memulai session
session_start();

// Memanggil konfigurasi database
include '../config/config.php';

// Mengecek apakah user sudah login dan memiliki role Admin atau Koordinator
// Jika tidak, akses ditolak
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['Admin', 'Koordinator'])) {
    $_SESSION['error'] = "Akses ditolak!";
    header("Location: index.php");
    exit;
}

// Proses hanya dijalankan jika request menggunakan metode POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Mengambil data dari form
    $id_insiden     = $_POST['id_insiden'];
    $status         = $_POST['status'];

    // Mengamankan input catatan admin
    $catatan_admin  = mysqli_real_escape_string($conn, $_POST['catatan_admin']);

    // Daftar status yang diperbolehkan
    $status_valid = ['pending', 'diterima', 'ditolak'];

    // Validasi status agar tidak diisi sembarangan
    if (!in_array($status, $status_valid)) {
        $_SESSION['error'] = "Status tidak valid!";
        header("Location: index.php");
        exit;
    }

    // Query untuk memperbarui status dan catatan admin pada insiden
    $query = "
        UPDATE tb_insiden 
        SET 
            status = '$status',
            catatan_admin = '$catatan_admin'
        WHERE id_insiden = '$id_insiden'
    ";

    // Eksekusi query dan set pesan notifikasi
    if (mysqli_query($conn, $query)) {
        $_SESSION['success'] = "Status insiden berhasil diperbarui!";
    } else {
        $_SESSION['error'] = "Gagal memperbarui data!";
    }

    // Kembali ke halaman daftar insiden
    header("Location: index.php");
    exit;
}
?>
