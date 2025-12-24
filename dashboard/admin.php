<?php
include '../config/config.php';

// Cek login dan role Admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'Admin') {
    header("Location: ../login.php");
    exit;
}

// Ambil data user dari session
$user = $_SESSION['user'];


// Hitung total petugas
$total_petugas = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM tb_pengguna WHERE role='Petugas'")
)['total'];

// Hitung total jadwal bulan ini
$total_jadwal = mysqli_fetch_assoc(
    mysqli_query($conn, "
        SELECT COUNT(*) AS total FROM tb_jadwal 
        WHERE MONTH(tanggal_tugas)=MONTH(CURRENT_DATE())
        AND YEAR(tanggal_tugas)=YEAR(CURRENT_DATE())
    ")
)['total'];

// Hitung total insiden
$total_insiden = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM tb_insiden")
)['total'];

// Hitung total presensi hadir
$total_hadir = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM tb_presensi WHERE status='hadir'")
)['total'];

// Hitung total seluruh presensi
$total_presensi = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM tb_presensi")
)['total'];

// Hitung persentase rata-rata kehadiran
$avg_kehadiran = ($total_presensi > 0) ? round(($total_hadir / $total_presensi) * 100) : 0;


// Nama bulan untuk grafik
$bulan_fix = ["Jan","Feb","Mar","Apr","Mei","Jun","Jul","Agu","Sep","Okt","Nov","Des"];

// Inisialisasi data grafik
$data_jadwal = array_fill(0, 12, 0);
$data_hadir = array_fill(0, 12, 0);

// Ambil data jadwal per bulan
$q_jadwal = mysqli_query($conn, "
    SELECT MONTH(tanggal_tugas) AS bulan, COUNT(*) AS total
    FROM tb_jadwal
    WHERE YEAR(tanggal_tugas)=YEAR(CURRENT_DATE())
    GROUP BY MONTH(tanggal_tugas)
");

while ($row = mysqli_fetch_assoc($q_jadwal)) {
    $data_jadwal[((int)$row['bulan']) - 1] = (int)$row['total'];
}

// Ambil data kehadiran per bulan
$q_hadir_bulan = mysqli_query($conn, "
    SELECT MONTH(j.tanggal_tugas) AS bulan, COUNT(p.id_absen) AS total
    FROM tb_presensi p
    JOIN tb_jadwal j ON p.id_jadwal = j.id_jadwal
    WHERE YEAR(j.tanggal_tugas)=YEAR(CURRENT_DATE())
      AND p.status='hadir'
    GROUP BY MONTH(j.tanggal_tugas)
");

while ($row = mysqli_fetch_assoc($q_hadir_bulan)) {
    $data_hadir[((int)$row['bulan']) - 1] = (int)$row['total'];
}

// Set judul halaman dan load template
$page_title = "Dashboard Admin | Siskamling";
include '../templates/header.php';
include '../templates/sidebar.php';
?>

<style>
.stat-card {
    transition: 0.25s;
    border-radius: 10px;
}
.stat-card:hover {
    transform: translateY(-6px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
}
</style>

<div class="container-fluid">

    <h1 class="h3 mb-4 fw-bold">Dashboard Admin</h1>

    <div class="row g-3">

        <div class="col-md-3">
            <div class="p-3 bg-white shadow-sm stat-card">
                <div class="d-flex align-items-center gap-3">
                    <i class="fa fa-users text-primary" style="font-size:32px;"></i>
                    <div>
                        <div class="text-primary fs-3 fw-bold"><?= $total_petugas ?></div>
                        <div class="text-muted small">Total Petugas</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="p-3 bg-white shadow-sm stat-card">
                <div class="d-flex align-items-center gap-3">
                    <i class="fa fa-calendar text-success" style="font-size:32px;"></i>
                    <div>
                        <div class="text-success fs-3 fw-bold"><?= $total_jadwal ?></div>
                        <div class="text-muted small">Jadwal Bulan Ini</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="p-3 bg-white shadow-sm stat-card">
                <div class="d-flex align-items-center gap-3">
                    <i class="fa fa-exclamation-triangle text-warning" style="font-size:32px;"></i>
                    <div>
                        <div class="text-warning fs-3 fw-bold"><?= $total_insiden ?></div>
                        <div class="text-muted small">Total Insiden</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="p-3 bg-white shadow-sm stat-card">
                <div class="d-flex align-items-center gap-3">
                    <i class="fa fa-chart-line text-danger" style="font-size:32px;"></i>
                    <div>
                        <div class="text-danger fs-3 fw-bold"><?= $avg_kehadiran ?>%</div>
                        <div class="text-muted small">Rata-rata Kehadiran</div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="row mt-4">
        <div class="col-lg-12">
            <div class="p-4 bg-white shadow-sm rounded">
                <h5 class="mb-3 fw-bold">
                    <i class="bi bi-bar-chart"></i> Grafik Jadwal & Kehadiran per Bulan
                </h5>
                <canvas id="chartBulan" height="130"></canvas>
            </div>
        </div>
    </div>

</div>

<?php include '../templates/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Membuat grafik bar jadwal dan kehadiran
new Chart(document.getElementById('chartBulan'), {
    type: 'bar',
    data: {
        labels: <?= json_encode($bulan_fix) ?>,
        datasets: [
            {
                label: "Total Jadwal",
                data: <?= json_encode($data_jadwal) ?>,
                backgroundColor: "rgba(52, 152, 219, 0.85)",
                borderColor: "#2980b9",
                borderWidth: 2,
                borderRadius: 8
            },
            {
                label: "Total Kehadiran",
                data: <?= json_encode($data_hadir) ?>,
                backgroundColor: "rgba(46, 204, 113, 0.85)",
                borderColor: "#27ae60",
                borderWidth: 2,
                borderRadius: 8
            }
        ]
    },
    options: {
        responsive: true,
        animation: {
            duration: 1800,
            easing: 'easeOutQuart',
            delay: (ctx) => ctx.dataIndex * 150
        },
        plugins: {
            legend: {
                labels: { font: { size: 14 } }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { stepSize: 5 }
            }
        }
    }
});
</script>
