<?php
include '../config/config.php';

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit;
}

$user = $_SESSION['user'];
$id_pengguna = $user['id_pengguna'];

$page_title = "Dashboard Petugas | Siskamling";
include '../templates/header.php';
include '../templates/sidebar.php';

/* ======================================================
   === AMBIL DATA STATISTIK ===
   ====================================================== */

// total jadwal bulan ini
$bulan = date('Y-m');
$jadwal_bulan = mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT COUNT(*) AS total FROM tb_jadwal
    WHERE id_pengguna = '$id_pengguna'
    AND DATE_FORMAT(tanggal_tugas,'%Y-%m') = '$bulan'
"))['total'];

// total hadir
$hadir = mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT COUNT(*) AS total 
    FROM tb_presensi pr
    JOIN tb_jadwal j ON j.id_jadwal = pr.id_jadwal
    WHERE j.id_pengguna = '$id_pengguna'
      AND pr.status = 'hadir'
"))['total'];

// total insiden dicatat
$insiden = mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT COUNT(*) AS total FROM tb_insiden
    WHERE id_pengguna = '$id_pengguna'
"))['total'];

/* ======================================================
   === JADWAL TERDEKAT (1 JADWAL SAJA)
   ====================================================== */

$next_one = mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT *, CONCAT(tanggal_tugas,' ',jam_mulai) AS mulai 
    FROM tb_jadwal
    WHERE id_pengguna='$id_pengguna'
      AND CONCAT(tanggal_tugas, ' ', jam_mulai) >= NOW()
    ORDER BY tanggal_tugas ASC, jam_mulai ASC
    LIMIT 1
"));

/* ======================================================
   === RIWAYAT PRESENSI
   ====================================================== */

$riwayat = mysqli_query($conn,"
    SELECT j.tanggal_tugas, pr.status 
    FROM tb_presensi pr
    JOIN tb_jadwal j ON pr.id_jadwal = j.id_jadwal
    WHERE j.id_pengguna = '$id_pengguna'
    ORDER BY pr.waktu_absen DESC
    LIMIT 4
");
?>

<style>
.stat-icon {
    width: 55px;
    height: 55px;
    border-radius: 50%;
    font-size: 26px;
    display:flex;
    align-items:center;
    justify-content:center;
    margin:0 auto 10px;
}
.schedule-item {
    padding: 12px;
    border-left: 4px solid #3498db;
    background:#f8f9fa;
    border-radius:6px;
}
.schedule-title {
    font-weight: bold;
    font-size: 18px;
}
.countdown-box {
    font-size: 20px;
    font-weight: bold;
}
</style>

<div class="container-fluid py-3">

    <h1 class="h3 mb-4">Dashboard Petugas</h1>

    <!-- ===================== -->
    <!-- STATISTIK -->
    <!-- ===================== -->
    <div class="row g-3 mb-4">

        <!-- JADWAL BULAN INI -->
        <div class="col-md-4">
            <div class="p-3 bg-white shadow-sm rounded">
                <div class="d-flex align-items-center gap-3">
                    <i class="fa fa-calendar text-primary" style="font-size:26px;"></i>
                    <div>
                        <div class="fw-bold fs-5 text-primary">
                            <?= $jadwal_bulan ?>
                        </div>
                        <div class="text-muted small">
                            Jadwal Bulan Ini
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- KEHADIRAN -->
        <div class="col-md-4">
            <div class="p-3 bg-white shadow-sm rounded">
                <div class="d-flex align-items-center gap-3">
                    <i class="fa fa-check-circle text-success" style="font-size:26px;"></i>
                    <div>
                        <div class="fw-bold fs-5 text-success">
                            <?= $hadir ?>/<?= $jadwal_bulan ?>
                        </div>
                        <div class="text-muted small">
                            Kehadiran
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- INSIDEN DICATAT -->
        <div class="col-md-4">
            <div class="p-3 bg-white shadow-sm rounded">
                <div class="d-flex align-items-center gap-3">
                    <i class="fa fa-flag text-warning" style="font-size:26px;"></i>
                    <div>
                        <div class="fw-bold fs-5 text-warning">
                            <?= $insiden ?>
                        </div>
                        <div class="text-muted small">
                            Insiden Dicatat
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>


    <!-- ===================== -->
    <!-- JADWAL TERDEKAT + TIMER -->
    <!-- ===================== -->
    <div class="row g-4">

        <div class="col-lg-8">
            <div class="p-4 bg-white shadow-sm rounded">

                <h5 class="mb-3"><i class="bi bi-clock-history"></i> Jadwal Ronda Terdekat</h5>

                <?php if ($next_one): ?>

                    <div class="schedule-item mb-3">
                        <div class="schedule-title">Ronda Malam Berikutnya</div>
                        <div class="schedule-info mt-2">
                            <div><i class="bi bi-calendar3"></i> 
                                <?= date("d M Y", strtotime($next_one['tanggal_tugas'])) ?>
                            </div>
                            <div><i class="bi bi-clock"></i> 
                                <?= substr($next_one['jam_mulai'],0,5) ?> WIB
                            </div>
                        </div>
                    </div>

                    <?php 
                        $target = $next_one['mulai']; 
                    ?>

                    <div class="countdown-box p-3 mt-3 text-center bg-light border rounded">
                        <h6 class="mb-1 text-muted">Hitung Mundur</h6>
                        <div id="countdown" class="text-primary">Memuat...</div>
                    </div>

                    <script>
                        const target = new Date("<?= $target ?>").getTime();

                        setInterval(function() {
                            const now = new Date().getTime();
                            const distance = target - now;

                            if (distance <= 0) {
                                document.getElementById("countdown").innerHTML =
                                    "<span class='text-success fw-bold'>Sedang Berlangsung ⚡</span>";
                                return;
                            }

                            const d = Math.floor(distance / (1000*60*60*24));
                            const h = Math.floor((distance%(1000*60*60*24))/(1000*60*60));
                            const m = Math.floor((distance%(1000*60*60))/(1000*60));
                            const s = Math.floor((distance%(1000*60))/1000);

                            document.getElementById("countdown").innerHTML =
                                (d>0 ? d+"h " : "") + h+"j "+m+"m "+s+"d";

                        },1000);
                    </script>

                <?php else: ?>
                    <p class="text-muted">Tidak ada jadwal terdekat.</p>
                <?php endif; ?>

                <div class="text-center mt-3">
                    <a href="../jadwal/index.php" class="btn btn-primary">
                        <i class="bi bi-calendar3"></i> Lihat Semua Jadwal
                    </a>
                </div>

            </div>
        </div>

        <!-- ===================== -->
        <!-- AKSI CEPAT -->
        <!-- ===================== -->
        <div class="col-lg-4">

            <div class="p-4 bg-white shadow-sm rounded mb-3">
                <h5 class="mb-3"><i class="bi bi-lightning"></i> Aksi Cepat</h5>

                <a href="../insiden/index.php" class="btn btn-danger w-100 mb-3">
                    <i class="bi bi-exclamation-triangle"></i> Lapor Insiden
                </a>
                <a href="../jadwal/index.php" class="btn btn-primary w-100 mb-3">
                    <i class="bi bi-calendar-check"></i> Lihat Jadwal
                </a>
                <a href="../profil/index.php" class="btn btn-primary w-100">
                    <i class="bi bi-person"></i> Lihat Profil
                </a>
            </div>

            <div class="p-4 bg-white shadow-sm rounded">
                <h5 class="mb-3"><i class="bi bi-check2-square"></i> Riwayat Kehadiran</h5>

                <?php if (mysqli_num_rows($riwayat) > 0): ?>
                    <?php while ($r = mysqli_fetch_assoc($riwayat)): ?>
                        <div class="d-flex justify-content-between py-2 border-bottom">
                            <span><b><?= date('d M', strtotime($r['tanggal_tugas'])) ?></b></span>

                            <?php if ($r['status'] == 'hadir'): ?>
                                <span class="text-success"><i class="bi bi-check-circle-fill"></i> Hadir</span>
                            <?php else: ?>
                                <span class="text-danger"><i class="bi bi-x-circle-fill"></i> Tidak Hadir</span>
                            <?php endif; ?>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <p class="text-muted">Belum ada presensi.</p>
                <?php endif; ?>
            </div>

        </div>

    </div>

    <div class="alert alert-info mt-4">
        <i class="bi bi-info-circle"></i>
        Jangan lupa melakukan presensi saat jadwal Anda dimulai!
    </div>

</div>

<?php include '../templates/footer.php'; ?>
