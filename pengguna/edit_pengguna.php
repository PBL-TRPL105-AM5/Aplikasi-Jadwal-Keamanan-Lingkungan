<?php
session_start();
include '../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $id        = mysqli_real_escape_string($conn, $_POST['id_pengguna']);
    $nama      = mysqli_real_escape_string($conn, $_POST['nama_pengguna']);
    $alamat    = mysqli_real_escape_string($conn, $_POST['alamat']);
    $jk        = mysqli_real_escape_string($conn, $_POST['jenis_kelamin']);
    $telp      = mysqli_real_escape_string($conn, $_POST['no_telp']);
    $email     = mysqli_real_escape_string($conn, $_POST['email']);
    $role      = mysqli_real_escape_string($conn, $_POST['role']);
    $password  = $_POST['password']; // tidak sanitize dulu, hanya di-hash

    // Password kosong → tidak diupdate
    if ($password == "" || $password == null) {

        $query = "
            UPDATE tb_pengguna SET 
                nama_pengguna = '$nama',
                alamat = '$alamat',
                jenis_kelamin = '$jk',
                no_telp = '$telp',
                email = '$email',
                role = '$role'
            WHERE id_pengguna = '$id'
        ";

    } else {

        // Hash password baru
        $hashed = password_hash($password, PASSWORD_DEFAULT);

        $query = "
            UPDATE tb_pengguna SET 
                nama_pengguna = '$nama',
                alamat = '$alamat',
                jenis_kelamin = '$jk',
                no_telp = '$telp',
                email = '$email',
                password = '$hashed',
                role = '$role'
            WHERE id_pengguna = '$id'
        ";
    }

    if (mysqli_query($conn, $query)) {
        $_SESSION['success'] = "Data pengguna berhasil diperbarui!";
    } else {
        $_SESSION['error'] = "Gagal memperbarui data pengguna.";
    }

    header("Location: index.php");
    exit;
}
?>
