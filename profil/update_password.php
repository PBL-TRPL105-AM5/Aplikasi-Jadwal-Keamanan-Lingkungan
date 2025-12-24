<?php
// Memulai session untuk menyimpan pesan sukses atau error
session_start();

// Memanggil konfigurasi database
include '../config/config.php';

// Memastikan request berasal dari form POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Mengambil data dari form
    $id_pengguna    = $_POST['id_pengguna'];
    $password_lama  = $_POST['password_lama'];
    $password_baru  = $_POST['password_baru'];
    $konfirmasi     = $_POST['konfirmasi'];

    // Mengambil password lama (hash) dari database berdasarkan ID pengguna
    $q = mysqli_query($conn, "
        SELECT password 
        FROM tb_pengguna 
        WHERE id_pengguna='$id_pengguna'
    ");
    $data = mysqli_fetch_assoc($q);
    $password_db = $data['password'];

    // Mengecek apakah password lama sesuai dengan yang ada di database
    if (!password_verify($password_lama, $password_db)) {
        $_SESSION['error'] = "Password lama salah!";
        header("Location: index.php");
        exit;
    }

    // Mengecek kecocokan password baru dan konfirmasi password
    if ($password_baru !== $konfirmasi) {
        $_SESSION['error'] = "Konfirmasi password baru tidak cocok!";
        header("Location: index.php");
        exit;
    }

    // Melakukan hashing pada password baru sebelum disimpan
    $hashed_password = password_hash($password_baru, PASSWORD_DEFAULT);

    // Mengupdate password pengguna di database
    mysqli_query($conn, "
        UPDATE tb_pengguna 
        SET password='$hashed_password'
        WHERE id_pengguna='$id_pengguna'
    ");

    // Menyimpan pesan sukses ke session
    $_SESSION['success'] = "Password berhasil diperbarui!";

    // Kembali ke halaman profil
    header("Location: index.php");
    exit;
}
?>
