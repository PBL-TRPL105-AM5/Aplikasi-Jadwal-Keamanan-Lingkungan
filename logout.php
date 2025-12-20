<?php
session_start();

// Hapus semua session
session_unset();
session_destroy();

// Arahkan kembali ke halaman landing_page
header("Location: landing_page.php");
exit;
