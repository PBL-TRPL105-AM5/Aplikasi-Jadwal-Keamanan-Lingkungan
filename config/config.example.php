<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host = "localhost";
$user = "root";
$pass = "";
$db   = "db_ronda";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi gagal");
}

/**
 * BASE URL OTOMATIS
 */
$base_url = ""; //masukkan nama folder projek http://localhost/nama_app/
