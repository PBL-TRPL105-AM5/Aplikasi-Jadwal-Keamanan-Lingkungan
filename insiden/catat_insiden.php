<?php
include '../config/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

$nama_insiden = mysqli_real_escape_string($conn, $_POST['nama_insiden']);
$deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);
$id_pengguna = intval($_POST['id_pengguna']);

$sql = "
    INSERT INTO tb_insiden (id_pengguna, nama_insiden, deskripsi, timestamp)
    VALUES ($id_pengguna, '$nama_insiden', '$deskripsi', NOW())
";

if (mysqli_query($conn, $sql)) {
    $_SESSION['success'] = "Insiden berhasil dicatat.";
} else {
    $_SESSION['error'] = "Gagal mencatat insiden.";
}

header("Location: index.php");
exit;
