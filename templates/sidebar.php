<?php
include __DIR__ . '/../config/config.php';

if (!isset($_SESSION['user'])) {
    header("Location: " . $base_url . "login.php");
    exit;
}

$user = $_SESSION['user'];
$nama = htmlspecialchars($user['nama_pengguna']);
$role = $user['role'];

// Deteksi URL aktif
$current_uri = $_SERVER['REQUEST_URI'];

// Helper untuk active menu
function isActive($path) {
    global $current_uri;
    return strpos($current_uri, $path) !== false ? 'active' : '';
}
?>

<!-- ================= NAVBAR ATAS ================= -->
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">

        <a class="navbar-brand fw-bold" href="<?= $base_url ?>dashboard/index.php">
            <i class="bi bi-shield-check"></i> Siskamling
        </a>

        <button class="navbar-toggler" type="button"
                data-bs-toggle="collapse"
                data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">

            <!-- MENU MOBILE -->
            <ul class="navbar-nav me-auto mobile-nav-menu">

                <li class="nav-item">
                    <a class="nav-link <?= isActive('/dashboard/') ?>"
                       href="<?= $base_url ?>dashboard/index.php">
                        <i class="bi bi-house-door"></i> Dashboard
                    </a>
                </li>

                <?php if ($role == 'Admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= isActive('/jadwal/') ?>"
                           href="<?= $base_url ?>jadwal/index.php">
                            <i class="bi bi-calendar-event"></i> Kelola Jadwal
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= isActive('/pengguna/') ?>"
                           href="<?= $base_url ?>pengguna/index.php">
                            <i class="bi bi-people"></i> Kelola Pengguna
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= isActive('/insiden/') ?>"
                           href="<?= $base_url ?>insiden/index.php">
                            <i class="bi bi-exclamation-circle"></i> Catatan Insiden
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= isActive('/presensi/') ?>"
                           href="<?= $base_url ?>presensi/index.php">
                            <i class="bi bi-check2-square"></i> Presensi Ronda
                        </a>
                    </li>
                <?php endif; ?>

                <?php if ($role == 'Koordinator'): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= isActive('/jadwal/') ?>"
                           href="<?= $base_url ?>jadwal/index.php">
                            <i class="bi bi-calendar-event"></i> Lihat Jadwal
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= isActive('/insiden/') ?>"
                           href="<?= $base_url ?>insiden/index.php">
                            <i class="bi bi-exclamation-circle"></i> Catatan Insiden
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= isActive('/presensi/') ?>"
                           href="<?= $base_url ?>presensi/index.php">
                            <i class="bi bi-check2-square"></i> Presensi Ronda
                        </a>
                    </li>
                <?php endif; ?>

                <?php if ($role == 'Petugas'): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= isActive('/jadwal/') ?>"
                           href="<?= $base_url ?>jadwal/index.php">
                            <i class="bi bi-calendar-event"></i> Jadwal Saya
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= isActive('/insiden/') ?>"
                           href="<?= $base_url ?>insiden/index.php">
                            <i class="bi bi-exclamation-triangle"></i> Catat Insiden
                        </a>
                    </li>
                <?php endif; ?>
            </ul>

            <!-- USER -->
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle"></i>
                        <?= $nama ?> (<?= $role ?>)
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="<?= $base_url ?>profil/index.php">
                                <i class="bi bi-person"></i> Profil
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="<?= $base_url ?>logout.php">
                                <i class="bi bi-box-arrow-right"></i> Logout
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>

        </div>
    </div>
</nav>

<!-- ================= SIDEBAR DESKTOP ================= -->
<div class="container-fluid">
    <div class="row">

        <div class="col-md-3 col-lg-2 px-0 sidebar d-none d-md-block">
            <nav class="nav flex-column">

                <a class="nav-link <?= isActive('/dashboard/') ?>"
                   href="<?= $base_url ?>dashboard/index.php">
                    <i class="bi bi-house-door"></i> Dashboard
                </a>

                <?php if ($role == 'Admin'): ?>
                    <a class="nav-link <?= isActive('/jadwal/') ?>"
                       href="<?= $base_url ?>jadwal/index.php">
                        <i class="bi bi-calendar-event"></i> Kelola Jadwal
                    </a>
                    <a class="nav-link <?= isActive('/pengguna/') ?>"
                       href="<?= $base_url ?>pengguna/index.php">
                        <i class="bi bi-people"></i> Kelola Pengguna
                    </a>
                    <a class="nav-link <?= isActive('/insiden/') ?>"
                       href="<?= $base_url ?>insiden/index.php">
                        <i class="bi bi-exclamation-circle"></i> Catatan Insiden
                    </a>
                    <a class="nav-link <?= isActive('/presensi/') ?>"
                       href="<?= $base_url ?>presensi/index.php">
                        <i class="bi bi-check2-square"></i> Presensi Ronda
                    </a>
                <?php endif; ?>

                <?php if ($role == 'Koordinator'): ?>
                    <a class="nav-link <?= isActive('/jadwal/') ?>"
                       href="<?= $base_url ?>jadwal/index.php">
                        <i class="bi bi-calendar-event"></i> Lihat Jadwal
                    </a>
                    <a class="nav-link <?= isActive('/insiden/') ?>"
                       href="<?= $base_url ?>insiden/index.php">
                        <i class="bi bi-exclamation-circle"></i> Catatan Insiden
                    </a>
                    <a class="nav-link <?= isActive('/presensi/') ?>"
                       href="<?= $base_url ?>presensi/index.php">
                        <i class="bi bi-check2-square"></i> Presensi Ronda
                    </a>
                <?php endif; ?>

                <?php if ($role == 'Petugas'): ?>
                    <a class="nav-link <?= isActive('/jadwal/') ?>"
                       href="<?= $base_url ?>jadwal/index.php">
                        <i class="bi bi-calendar-event"></i> Jadwal Saya
                    </a>
                    <a class="nav-link <?= isActive('/insiden/') ?>"
                       href="<?= $base_url ?>insiden/index.php">
                        <i class="bi bi-exclamation-triangle"></i> Catat Insiden
                    </a>
                <?php endif; ?>

            </nav>
        </div>

        <div class="col-md-9 col-lg-10 p-4" id="mainContent">
