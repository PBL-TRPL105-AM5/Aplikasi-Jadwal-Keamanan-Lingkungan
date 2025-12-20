<?php
session_start();
include "config/config.php";

/* Jika sudah login, langsung ke dashboard */
if (isset($_SESSION['user'])) {
    header("Location: dashboard/index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Siskamling</title>

  <!-- Bootstrap & Font (TETAP) -->
  <link href="assets/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

  <!-- Custom CSS -->
  <link rel="stylesheet" href="assets/css/landing_page.css">

</head>
<body>

<!-- NAVBAR -->
<nav class="main-nav">
  <div class="container">
    <div class="nav-glass d-flex justify-content-between align-items-center">
      <div class="nav-brand">SISKAMLING</div>
      <div class="d-flex align-items-center gap-4">
        <a href="#hero" class="nav-link-custom active">Home</a>
        <a href="#about" class="nav-link-custom">About Us</a>
        <a href="#schedule" class="nav-link-custom">Schedule</a>
        <a href="#incident" class="nav-link-custom">Incident</a>
        <button class="nav-login" onclick="location.href='login.php'">Login</button>
      </div>
    </div>
  </div>
</nav>

<!-- HERO -->
<section id="hero" class="hero">
  <div class="container position-relative">
    <div class="row align-items-center g-5">
      <div class="col-lg-5">
        <div class="hero-left-wrap">
          <div class="hero-left">
            <small class="text-uppercase d-block mb-2">SISKAMLING</small>
            <h1>
              Gak perlu lagi ribet<br>
              catat <span class="accent">jadwal ronda</span> di kertas.
            </h1>
            <p class="lead-text mb-4">
              Buat jadwal otomatis, ingatkan semua anggota, dan hindari bentrokan jadwal.
            </p>
            <button class="btn btn-primary" onclick="location.href='login.php'">
              Atur Ronda Kamu Sekarang
            </button>
          </div>
        </div>
      </div>

      <div class="col-lg-7">
        <div class="tablet-wrapper">
          <div class="tablet-glass">
            <img src="assets/img/WhatsApp Image 2025-12-19 at 23.10.00.jpeg" alt="Dashboard Petugas">
          </div>
        </div>
      </div>
    </div>

    <img src="assets/img/Arrow-Filled-Head-Swirl-Medium--Streamline-Beveled-Scribbles.png"
         alt="Arrow" class="hero-arrow">
  </div>
</section>

<!-- ABOUT -->
<section id="about" class="about-section">
  <div class="container">
    <div class="row align-items-center g-5">
      <div class="col-lg-7">
        <p class="text-uppercase small text-muted mb-3">Sponsored By</p>
        <div class="d-flex flex-wrap align-items-center gap-4 mb-5">
          <img src="assets/img/LOGO_Jur_IF_Variant_03_Horizontal_Black-removebg-preview.png" style="height:44px;">
          <img src="assets/img/LOGO_Prod_TRPL_Variant_03_Horizontal_Black-removebg-preview.png" style="height:44px;">
        </div>

        <h2 class="fw-bold mb-3" style="font-size:1.8rem;">About Us</h2>
        <p class="mb-0 about-text">
          Siskamling adalah solusi revolusioner yang mengubah cara lingkungan Anda menjaga keamanan malam hari—bayangkan jadwal ronda yang adil dan otomatis, tanpa lagi ribut soal giliran, lengkap dengan pengingat langsung ke ponsel warga agar tak ada yang lupa tugas. Dengan fitur presensi instan, rekap kehadiran yang detail, dan catatan insiden yang mudah dilacak, pengurus bisa memantau semuanya dari satu dasbor, menghindari kekacauan dan memastikan keamanan maksimal. Plus, laporan bulanan dengan grafik visual yang tajam membuat analisis jadi lebih mudah daripada minum kopi pagi—bergabunglah sekarang dan buat ronda malam jadi lebih efisien, aman, dan bebas drama untuk komunitas Anda!
        </p>
      </div>

      <div class="col-lg-5 text-center">
        <img src="assets/img/21743663_6485985-removebg-preview.png"
             class="img-fluid" style="max-width:420px;">
      </div>
    </div>
  </div>
</section>

<!-- TODAY'S SCHEDULE -->
<section id="schedule" class="schedule-section">
  <div class="schedule-inner">
    <div class="container text-center">
      <h2 class="schedule-title">Today's Schedule</h2>

      <div class="row g-4 justify-content-center schedule-grid">
        <?php
        // tanggal hari ini
        $today = date('Y-m-d');

        // cari SENIN minggu ini
        $monday = date('Y-m-d', strtotime('monday this week'));

        // ambil semua jadwal dalam 1 minggu (Senin–Minggu)
        $jadwal = [];
        $qJadwal = mysqli_query($conn, "
          SELECT j.tanggal_tugas, p.nama_pengguna
          FROM tb_jadwal j
          JOIN tb_pengguna p ON j.id_pengguna = p.id_pengguna
          WHERE j.tanggal_tugas BETWEEN '$monday' AND DATE_ADD('$monday', INTERVAL 6 DAY)
        ");

        while ($row = mysqli_fetch_assoc($qJadwal)) {
          $jadwal[$row['tanggal_tugas']][] = $row['nama_pengguna'];
        }

        // loop 7 hari (Senin → Minggu)
        for ($i = 0; $i < 7; $i++):
          $date = date('Y-m-d', strtotime("$monday +$i day"));
          $isToday = ($date === $today);
        ?>
        <div class="col-md-4">
          <div class="schedule-glass <?= $isToday ? 'schedule-glass-dark' : '' ?>">

            <div class="schedule-top-lines">
              <?php if (!empty($jadwal[$date])): ?>
                <?php foreach ($jadwal[$date] as $nama): ?>
                  <p class="mb-1 fw-semibold small">
                    <?= htmlspecialchars($nama); ?>
                  </p>
                <?php endforeach; ?>
              <?php else: ?>
                <p class="mb-1 small text-muted fst-italic">
                  Belum ada jadwal
                </p>
              <?php endif; ?>
            </div>

            <div class="schedule-date-bar">
              <span class="icon">
                <i class="fa-regular fa-clock"></i>
              </span>
              <span style="white-space:nowrap;">
                <?= date('l | d F', strtotime($date)); ?>
                <small><?= date('Y', strtotime($date)); ?></small>
              </span>
            </div>

          </div>
        </div>
        <?php endfor; ?>
      </div>

    </div>
  </div>
</section>

<!-- INCIDENT REPORT -->
<section id="incident" class="incident-section">
  <div class="incident-inner">
    <div class="container">
      <h2 class="incident-title">Incident Report</h2>

      <?php
      $qInsiden = mysqli_query($conn,"
        SELECT 
          i.nama_insiden,
          i.deskripsi,
          i.timestamp,
          i.status,
          i.catatan_admin,
          p.nama_pengguna
        FROM tb_insiden i
        LEFT JOIN tb_pengguna p ON i.id_pengguna = p.id_pengguna
        ORDER BY i.timestamp DESC
        LIMIT 3
      ");

      if (mysqli_num_rows($qInsiden) > 0):
        while ($i = mysqli_fetch_assoc($qInsiden)):
      ?>
      <div class="incident-card">
        <div class="incident-left">

          <!-- Pelapor -->
          <small>
            <span class="profile-icon">
              <i class="fa-regular fa-user"></i>
            </span>
            <?= htmlspecialchars($i['nama_pengguna'] ?? 'Tidak diketahui'); ?>
          </small>

          <!-- Judul -->
          <div class="incident-title-text">
            <?= htmlspecialchars($i['nama_insiden']); ?>
          </div>

          <!-- Deskripsi -->
          <div class="incident-desc">
            <?= nl2br(htmlspecialchars($i['deskripsi'])); ?>
          </div>

          <!-- CATATAN ADMIN -->
          <div class="incident-desc catatan-admin">
            <strong>Catatan Admin:</strong><br>
            <?= $i['catatan_admin']
                ? nl2br(htmlspecialchars($i['catatan_admin']))
                : '<em>- Belum ada catatan</em>'; ?>
          </div>


          <!-- Meta -->
          <div class="incident-meta" style="margin-top:12px;">
            <span>
              <i class="fa-regular fa-calendar"></i>
              <?= date('d M Y, H:i', strtotime($i['timestamp'])); ?>
            </span>
            <span class="badge 
              <?= $i['status']=='diterima' ? 'bg-success' : 
                ($i['status']=='ditolak' ? 'bg-danger' : 'bg-secondary') ?>
              text-uppercase">
              <?= htmlspecialchars($i['status']); ?>
            </span>

          </div>

        </div>
      </div>
      <?php endwhile; else: ?>
      
      <!-- EMPTY STATE -->
      <div class="incident-empty text-center py-4">
        <i class="fa-regular fa-folder-open fa-2x mb-2"></i>
        <p class="mb-1">Belum ada laporan insiden</p>
        <small>Lingkungan dalam kondisi aman 👍</small>
      </div>

      <?php endif; ?>
    </div>
  </div>
</section>



</body>
</html>
