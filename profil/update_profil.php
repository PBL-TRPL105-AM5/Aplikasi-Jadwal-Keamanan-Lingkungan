<?php
include '../config/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

$id      = $_POST['id_pengguna'];
$nama    = mysqli_real_escape_string($conn, $_POST['nama_pengguna']);
$email   = mysqli_real_escape_string($conn, $_POST['email']);
$alamat  = mysqli_real_escape_string($conn, $_POST['alamat']);
$no_telp = mysqli_real_escape_string($conn, $_POST['no_telp']);

$q = "
    UPDATE tb_pengguna 
    SET nama_pengguna='$nama',
        email='$email',
        alamat='$alamat',
        no_telp='$no_telp'
    WHERE id_pengguna='$id'
";

if (mysqli_query($conn, $q)) {
    $_SESSION['success'] = "Profil berhasil diperbarui!";
} else {
    $_SESSION['error'] = "Gagal memperbarui profil!";
}

header("Location: index.php");
exit;
