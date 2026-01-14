<?php
session_start();
include '../config/config.php';

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: index.php");
    exit;
}

$id_pengguna    = $_SESSION['user']['id_pengguna'];
$is_first_login = $_SESSION['user']['is_first_login'];

$password_baru  = $_POST['password_baru'];
$konfirmasi     = $_POST['konfirmasi'];

if ($password_baru !== $konfirmasi) {
    $_SESSION['error'] = "Konfirmasi password baru tidak cocok!";
    header("Location: index.php");
    exit;
}

if (strlen($password_baru) < 6) {
    $_SESSION['error'] = "Password minimal 6 karakter!";
    header("Location: index.php");
    exit;
}

if ($is_first_login == 0) {

    if (empty($_POST['password_lama'])) {
        $_SESSION['error'] = "Password lama wajib diisi!";
        header("Location: index.php");
        exit;
    }

    $password_lama = $_POST['password_lama'];

    $q = mysqli_query($conn, "
        SELECT password 
        FROM tb_pengguna 
        WHERE id_pengguna='$id_pengguna'
    ");
    $data = mysqli_fetch_assoc($q);

    if (!password_verify($password_lama, $data['password'])) {
        $_SESSION['error'] = "Password lama salah!";
        header("Location: index.php");
        exit;
    }
}

$hashed_password = password_hash($password_baru, PASSWORD_DEFAULT);

mysqli_query($conn, "
    UPDATE tb_pengguna 
    SET password='$hashed_password',
        is_first_login=0
    WHERE id_pengguna='$id_pengguna'
");

$_SESSION['user']['is_first_login'] = 0;
$_SESSION['success'] = "Password berhasil diperbarui!";

header("Location: index.php");
exit;
