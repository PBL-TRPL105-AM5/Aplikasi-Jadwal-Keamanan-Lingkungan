<?php
include '../config/config.php';

// Memastikan request berasal dari metode POST
// Jika bukan POST, kembalikan ke halaman index
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

// Mengambil dan membersihkan input nama insiden
$nama_insiden = mysqli_real_escape_string($conn, $_POST['nama_insiden']);

// Mengambil dan membersihkan input deskripsi insiden
$deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);

// Mengambil id pengguna dan memastikan bertipe integer
$id_pengguna = intval($_POST['id_pengguna']);

// Query untuk menyimpan data insiden ke database
$sql = "
    INSERT INTO tb_insiden (id_pengguna, nama_insiden, deskripsi, timestamp)
    VALUES ($id_pengguna, '$nama_insiden', '$deskripsi', NOW())
";

// Eksekusi query dan set pesan notifikasi
if (mysqli_query($conn, $sql)) {
    // Pesan sukses jika data berhasil disimpan
    $_SESSION['success'] = "Insiden berhasil dicatat.";
} else {
    // Pesan error jika terjadi kegagalan
    $_SESSION['error'] = "Gagal mencatat insiden.";
}

// Redirect kembali ke halaman index setelah proses selesai
header("Location: index.php");
exit;
