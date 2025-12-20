<?php
session_start();
include '../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $nama      = mysqli_real_escape_string($conn, $_POST['nama_pengguna']);
    $alamat    = mysqli_real_escape_string($conn, $_POST['alamat']);
    $jk        = mysqli_real_escape_string($conn, $_POST['jenis_kelamin']);
    $telp      = mysqli_real_escape_string($conn, $_POST['no_telp']);
    $email     = mysqli_real_escape_string($conn, $_POST['email']);
    $password  = $_POST['password']; // tetap raw, nanti di-hash
    $role      = mysqli_real_escape_string($conn, $_POST['role']);

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $query = "
        INSERT INTO tb_pengguna 
            (nama_pengguna, alamat, jenis_kelamin, no_telp, email, password, role)
        VALUES 
            ('$nama', '$alamat', '$jk', '$telp', '$email', '$hashed_password', '$role')
    ";

    if (mysqli_query($conn, $query)) {
        $_SESSION['success'] = "Pengguna baru berhasil ditambahkan!";
    } else {
        $_SESSION['error'] = "Gagal menambah pengguna.";
    }

    header("Location: index.php");
    exit;
}
?>
