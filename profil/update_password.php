<?php
session_start();
include '../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id_pengguna    = $_POST['id_pengguna'];
    $password_lama  = $_POST['password_lama'];
    $password_baru  = $_POST['password_baru'];
    $konfirmasi     = $_POST['konfirmasi'];

    // Ambil password hash dari database
    $q = mysqli_query($conn, "SELECT password FROM tb_pengguna WHERE id_pengguna='$id_pengguna'");
    $data = mysqli_fetch_assoc($q);
    $password_db = $data['password'];

    // Cek password lama
    if (!password_verify($password_lama, $password_db)) {
        $_SESSION['error'] = "Password lama salah!";
        header("Location: index.php");
        exit;
    }

    // Cek konfirmasi password
    if ($password_baru !== $konfirmasi) {
        $_SESSION['error'] = "Konfirmasi password baru tidak cocok!";
        header("Location: index.php");
        exit;
    }

    // Hash password baru
    $hashed_password = password_hash($password_baru, PASSWORD_DEFAULT);

    // Update password
    mysqli_query($conn, "
        UPDATE tb_pengguna 
        SET password='$hashed_password'
        WHERE id_pengguna='$id_pengguna'
    ");

    $_SESSION['success'] = "Password berhasil diperbarui!";
    header("Location: index.php");
    exit;
}
?>
