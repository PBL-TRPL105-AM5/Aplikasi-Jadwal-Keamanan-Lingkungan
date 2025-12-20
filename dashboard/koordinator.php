<?php
include '../config/config.php';

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit;
}

$user = $_SESSION['user'];
$id_pengguna = $user['id_pengguna'];

$page_title = "Dashboard Koordinator | Siskamling";
include '../templates/header.php';
include '../templates/sidebar.php';


// ===============================
// AMBIL DATA DARI DATABASE
// ===============================

// total petugas
$total_petugas = mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT COUNT(*) AS total FROM tb_pengguna WHERE role='Petugas'
"))['total'];

// presensi hari ini
$tgl_hari_ini = date('Y-m-d');
$presensi_hari_ini = mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT COUNT(*) AS total FROM tb_presensi pr
    JOIN tb_jadwal j ON j.id_jadwal = pr.id_jadwal
    WHERE j.tanggal_tugas = '$tgl_hari_ini'
    AND pr.status = 'hadir'
"))['total'];

// total insiden
$total_insiden = mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT COUNT(*) AS total FROM tb_insiden
"))['total'];

// jadwal hari ini
$jadwal_hari_ini = mysqli_query($conn,"
    SELECT j.*, p.nama_pengguna
    FROM tb_jadwal j
    JOIN tb_pengguna p ON j.id_pengguna = p.id_pengguna
    WHERE j.tanggal_tugas = '$tgl_hari_ini'
    ORDER BY j.jam_mulai ASC
");
?>

<div class="container-fluid py-3">

    <h1 class="h3 mb-4">Dashboard Koordinator</h1>

    <!-- ===================== -->
    <!-- STATISTIK -->
    <!-- ===================== -->
    <div class="row g-3 mb-4">

        <!-- TOTAL PETUGAS -->
        <div class="col-md-4">
            <div class="p-3 bg-white shadow-sm stat-card">
                <div class="d-flex align-items-center gap-3">
                    <i class="fa fa-users text-primary" style="font-size:32px;"></i>
                    <div>
                        <div class="text-primary fs-3 fw-bold">
                            <?= $total_petugas ?>
                        </div>
                        <div class="text-muted small">
                            Total Petugas
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- PRESENSI HARI INI -->
        <div class="col-md-4">
            <div class="p-3 bg-white shadow-sm stat-card">
                <div class="d-flex align-items-center gap-3">
                    <i class="fa fa-check-circle text-success" style="font-size:32px;"></i>
                    <div>
                        <div class="text-success fs-3 fw-bold">
                            <?= $presensi_hari_ini ?>/<?= $total_petugas ?>
                        </div>
                        <div class="text-muted small">
                            Presensi Hari Ini
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- LAPORAN INSIDEN -->
        <div class="col-md-4">
            <div class="p-3 bg-white shadow-sm stat-card">
                <div class="d-flex align-items-center gap-3">
                    <i class="fa fa-exclamation-triangle text-warning" style="font-size:32px;"></i>
                    <div>
                        <div class="text-warning fs-3 fw-bold">
                            <?= $total_insiden ?>
                        </div>
                        <div class="text-muted small">
                            Laporan Insiden
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>



    <div class="row g-4">

        <!-- ===================== -->
        <!-- JADWAL HARI INI -->
        <!-- ===================== -->
        <div class="col-lg-8">
            <div class="p-4 bg-white shadow-sm rounded">
                <h5><i class="bi bi-calendar-check"></i> Jadwal Ronda Hari Ini</h5>

                <?php if (mysqli_num_rows($jadwal_hari_ini) > 0): ?>

                    <?php while ($j = mysqli_fetch_assoc($jadwal_hari_ini)): ?>

                        <div class="schedule-item mb-3">
                            <div class="schedule-title">
                                <?= htmlspecialchars($j['nama_pengguna']) ?>
                            </div>

                            <div class="schedule-info">
                                <div class="info-item">
                                    <i class="bi bi-clock"></i>
                                    <?= substr($j['jam_mulai'], 0, 5) ?> - <?= substr($j['jam_selesai'], 0, 5) ?> WIB
                                </div>

                                <div class="info-item">
                                    <i class="bi bi-geo-alt"></i>
                                    Lokasi Ronda
                                </div>
                            </div>
                        </div>

                    <?php endwhile; ?>

                <?php else: ?>
                    <p class="text-muted">Tidak ada jadwal ronda hari ini.</p>
                <?php endif; ?>

                <div class="text-center mt-3">
                    <a href="../presensi/index.php" class="btn btn-primary">
                        <i class="bi bi-check-square"></i> Kelola Presensi
                    </a>
                </div>

            </div>
        </div>


        <!-- ===================== -->
        <!-- AKSI CEPAT -->
        <!-- ===================== -->
        <div class="col-lg-4">

            <div class="p-4 bg-white shadow-sm rounded mb-3">
                <h5><i class="bi bi-lightning"></i> Aksi Cepat</h5>

                <a href="../presensi/index.php" class="btn btn-primary w-100 mb-3">
                    <i class="bi bi-check-square"></i> Kelola Presensi
                </a>

                <a href="../insiden/index.php" class="btn btn-danger w-100 mb-3">
                    <i class="bi bi-exclamation-triangle"></i> Lapor Insiden
                </a>

                <a href="../profil/index.php" class="btn btn-primary w-100">
                    <i class="bi bi-person"></i> Lihat Profil
                </a>
            </div>
        </div>

        <!-- INFO BOX -->
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i>
            Pastikan seluruh petugas melakukan presensi sesuai jadwal.
        </div>

    </div>

</div>

<?php include '../templates/footer.php'; ?>
