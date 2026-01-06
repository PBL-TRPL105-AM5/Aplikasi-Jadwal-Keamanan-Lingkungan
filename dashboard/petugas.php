<?php
include '../config/config.php';

/* ================= TIMEZONE WIB ================= */
date_default_timezone_set('Asia/Jakarta');

// ================= CEK LOGIN =================
if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit;
}

// ================= USER LOGIN =================
$user = $_SESSION['user'];
$id_pengguna = $user['id_pengguna'];

// ================= JUDUL HALAMAN =================
$page_title = "Dashboard Petugas | Siskamling";

include '../templates/header.php';
include '../templates/sidebar.php';

// ================= STATISTIK =================
$bulan = date('Y-m');

$jadwal_bulan = mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT COUNT(*) AS total 
    FROM tb_jadwal
    WHERE id_pengguna = '$id_pengguna'
      AND DATE_FORMAT(tanggal_tugas,'%Y-%m') = '$bulan'
"))['total'];

$hadir = mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT COUNT(*) AS total 
    FROM tb_presensi pr
    JOIN tb_jadwal j ON j.id_jadwal = pr.id_jadwal
    WHERE j.id_pengguna = '$id_pengguna'
      AND pr.status = 'hadir'
"))['total'];

$insiden = mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT COUNT(*) AS total 
    FROM tb_insiden
    WHERE id_pengguna = '$id_pengguna'
"))['total'];

// ================= JADWAL TERDEKAT =================
$next_one = mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT *, CONCAT(tanggal_tugas,' ',jam_mulai) AS mulai
    FROM tb_jadwal
    WHERE id_pengguna='$id_pengguna'
      AND CONCAT(tanggal_tugas,' ',jam_mulai) >= NOW()
    ORDER BY tanggal_tugas ASC, jam_mulai ASC
    LIMIT 1
"));

// ================= RIWAYAT PRESENSI =================
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
}
.schedule-item {
    padding: 14px;
    border-left: 4px solid #0d6efd;
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

    <!-- STATISTIK -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="p-3 bg-white shadow-sm rounded d-flex gap-3 align-items-center">
                <i class="fa fa-calendar text-primary fs-3"></i>
                <div>
                    <div class="fw-bold fs-5"><?= $jadwal_bulan ?></div>
                    <div class="text-muted small">Jadwal Bulan Ini</div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="p-3 bg-white shadow-sm rounded d-flex gap-3 align-items-center">
                <i class="fa fa-check-circle text-success fs-3"></i>
                <div>
                    <div class="fw-bold fs-5"><?= $hadir ?>/<?= $jadwal_bulan ?></div>
                    <div class="text-muted small">Kehadiran</div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="p-3 bg-white shadow-sm rounded d-flex gap-3 align-items-center">
                <i class="fa fa-flag text-warning fs-3"></i>
                <div>
                    <div class="fw-bold fs-5"><?= $insiden ?></div>
                    <div class="text-muted small">Insiden Dicatat</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">

        <!-- JADWAL TERDEKAT + COUNTDOWN -->
        <div class="col-lg-8">
            <div class="p-4 bg-white shadow-sm rounded">
                <h5 class="mb-3"><i class="bi bi-clock-history"></i> Jadwal Ronda Terdekat</h5>

                <?php if ($next_one): ?>

                    <div class="schedule-item mb-3">
                        <div class="schedule-title">Ronda Berikutnya</div>
                        <div class="mt-2">
                            <div><i class="bi bi-calendar3"></i>
                                <?= date('d M Y', strtotime($next_one['tanggal_tugas'])) ?>
                            </div>
                            <div><i class="bi bi-clock"></i>
                                <?= substr($next_one['jam_mulai'],0,5) ?> WIB
                            </div>
                        </div>
                    </div>

                    <?php
                    $target = date(
                        'Y-m-d H:i:s',
                        strtotime($next_one['tanggal_tugas'].' '.$next_one['jam_mulai'])
                    );
                    ?>

                    <div class="countdown-box p-3 text-center bg-light border rounded">
                        <h6 class="mb-1 text-muted">Hitung Mundur Mulai Bertugas</h6>
                        <div id="countdown" class="text-primary">Memuat...</div>
                    </div>

                    <script>
                        const targetTime = new Date("<?= $target ?>".replace(" ", "T")).getTime();
                        const countdownEl = document.getElementById("countdown");

                        const timer = setInterval(() => {
                            const now = new Date().getTime();
                            const distance = targetTime - now;

                            if (distance <= 0) {
                                clearInterval(timer);
                                countdownEl.innerHTML =
                                    "<span class='text-success'>Sedang Bertugas ⚡</span>";
                                return;
                            }

                            const hari  = Math.floor(distance / (1000*60*60*24));
                            const jam   = Math.floor((distance % (1000*60*60*24)) / (1000*60*60));
                            const menit = Math.floor((distance % (1000*60*60)) / (1000*60));
                            const detik = Math.floor((distance % (1000*60)) / 1000);

                            let text = "";
                            if (hari > 0) text += hari + " hari ";
                            text += jam + " jam " + menit + " menit " + detik + " detik";

                            countdownEl.innerHTML = text;
                        }, 1000);
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

        <!-- AKSI CEPAT & RIWAYAT -->
        <div class="col-lg-4">

            <div class="p-4 bg-white shadow-sm rounded mb-3">
                <h5 class="mb-3"><i class="bi bi-lightning"></i> Aksi Cepat</h5>

                <a href="../insiden/index.php" class="btn btn-danger w-100 mb-2">
                    <i class="bi bi-exclamation-triangle"></i> Lapor Insiden
                </a>
                <a href="../jadwal/index.php" class="btn btn-primary w-100 mb-2">
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
                            <b><?= date('d M', strtotime($r['tanggal_tugas'])) ?></b>
                            <?php if ($r['status'] == 'hadir'): ?>
                                <span class="text-success">Hadir</span>
                            <?php else: ?>
                                <span class="text-danger">Tidak Hadir</span>
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
