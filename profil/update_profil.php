<?php
// Memanggil konfigurasi database dan session
include '../config/config.php';

// Memastikan request berasal dari metode POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Jika bukan POST, kembalikan ke halaman profil
    header("Location: index.php");
    exit;
}

// Mengambil data dari form profil
$id      = $_POST['id_pengguna'];
$nama    = mysqli_real_escape_string($conn, $_POST['nama_pengguna']);
$email   = mysqli_real_escape_string($conn, $_POST['email']);
$alamat  = mysqli_real_escape_string($conn, $_POST['alamat']);
$no_telp = mysqli_real_escape_string($conn, $_POST['no_telp']);

// Query untuk memperbarui data profil pengguna
$q = "
    UPDATE tb_pengguna 
    SET 
        nama_pengguna='$nama',
        email='$email',
        alamat='$alamat',
        no_telp='$no_telp'
    WHERE id_pengguna='$id'
";

// Mengeksekusi query dan menyimpan status ke session
if (mysqli_query($conn, $q)) {
    // Jika berhasil, simpan pesan sukses
    $_SESSION['success'] = "Profil berhasil diperbarui!";
} else {
    // Jika gagal, simpan pesan error
    $_SESSION['error'] = "Gagal memperbarui profil!";
}

// Kembali ke halaman profil
header("Location: index.php");
exit;
