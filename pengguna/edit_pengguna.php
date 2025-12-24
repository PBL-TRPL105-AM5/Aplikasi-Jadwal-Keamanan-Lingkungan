<?php
// Memulai session
session_start();

// Memanggil konfigurasi database
include '../config/config.php';

// Proses hanya dijalankan jika request menggunakan metode POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Mengambil dan mengamankan data input dari form
    $id        = mysqli_real_escape_string($conn, $_POST['id_pengguna']);
    $nama      = mysqli_real_escape_string($conn, $_POST['nama_pengguna']);
    $alamat    = mysqli_real_escape_string($conn, $_POST['alamat']);
    $jk        = mysqli_real_escape_string($conn, $_POST['jenis_kelamin']);
    $telp      = mysqli_real_escape_string($conn, $_POST['no_telp']);
    $email     = mysqli_real_escape_string($conn, $_POST['email']);
    $role      = mysqli_real_escape_string($conn, $_POST['role']);

    // Password diambil apa adanya karena akan di-hash jika diisi
    $password  = $_POST['password'];

    // Jika password kosong, data password tidak diperbarui
    if ($password == "" || $password == null) {

        // Query update tanpa mengubah password
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

        // Hash password baru sebelum disimpan ke database
        $hashed = password_hash($password, PASSWORD_DEFAULT);

        // Query update termasuk perubahan password
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

    // Eksekusi query dan set pesan notifikasi
    if (mysqli_query($conn, $query)) {
        // Pesan sukses jika data berhasil diperbarui
        $_SESSION['success'] = "Data pengguna berhasil diperbarui!";
    } else {
        // Pesan error jika proses update gagal
        $_SESSION['error'] = "Gagal memperbarui data pengguna.";
    }

    // Redirect kembali ke halaman daftar pengguna
    header("Location: index.php");
    exit;
}
?>
