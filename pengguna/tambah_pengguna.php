<?php
// Memulai session untuk menyimpan pesan notifikasi
session_start();

// Memanggil konfigurasi database
include '../config/config.php';

// Proses hanya dijalankan jika form dikirim menggunakan metode POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Mengambil dan mengamankan data input dari form
    $nama      = mysqli_real_escape_string($conn, $_POST['nama_pengguna']);
    $alamat    = mysqli_real_escape_string($conn, $_POST['alamat']);
    $jk        = mysqli_real_escape_string($conn, $_POST['jenis_kelamin']);
    $telp      = mysqli_real_escape_string($conn, $_POST['no_telp']);
    $email     = mysqli_real_escape_string($conn, $_POST['email']);
    $role      = mysqli_real_escape_string($conn, $_POST['role']);

    // Password Difault
    $password_default = 'Ronda@2026';

    // Melakukan hash password sebelum disimpan ke database
    $hashed_password  = password_hash($password_default, PASSWORD_DEFAULT);

    // Query untuk menambahkan data pengguna baru
    $query = "
        INSERT INTO tb_pengguna 
            (nama_pengguna, alamat, jenis_kelamin, no_telp, email, password, role)
        VALUES 
            ('$nama', '$alamat', '$jk', '$telp', '$email', '$hashed_password', '$role')
    ";

    // Eksekusi query dan set pesan notifikasi
    if (mysqli_query($conn, $query)) {
        // Pesan sukses jika data berhasil ditambahkan
        $_SESSION['success'] = "Pengguna baru berhasil ditambahkan!";
    } else {
        // Pesan error jika proses insert gagal
        $_SESSION['error'] = "Gagal menambah pengguna.";
    }

    // Redirect kembali ke halaman daftar pengguna
    header("Location: index.php");
    exit;
}
?>
