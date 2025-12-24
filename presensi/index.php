<?php
// Memanggil konfigurasi database dan session
include __DIR__ . '/../config/config.php';

// Mengecek apakah user sudah login dan memiliki role Admin atau Koordinator
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['Admin', 'Koordinator'])) {
    die("Akses hanya untuk Admin atau Koordinator");
}

// Menyimpan data user yang sedang login
$user = $_SESSION['user'];

// Menyimpan tanggal yang dipilih dari parameter GET
$tanggal = $_GET['tanggal'] ?? '';

// Variabel untuk menampilkan pesan setelah simpan presensi
$message = '';

// Proses penyimpanan presensi ketika form disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status'])) {

    // Loop setiap data presensi berdasarkan id_jadwal
    foreach ($_POST['status'] as $id_jadwal => $status) {

        // Mengamankan ID jadwal dan pencatat
        $id_jadwal = (int)$id_jadwal;
        $dicatat_oleh = (int)$_SESSION['user']['id_pengguna'];

        // Menentukan status kehadiran
        if ($status === '') {
            $status_sql = "NULL";
        } else {
            $status = strtolower($status) === 'hadir' ? 'hadir' : 'tidak hadir';
            $status_sql = "'" . mysqli_real_escape_string($conn, $status) . "'";
        }

        // Mengambil dan memproses keterangan presensi
        $keterangan_input = $_POST['keterangan'][$id_jadwal] ?? '';
        if (trim($keterangan_input) === '') {
            $keterangan_sql = "NULL";
        } else {
            $keterangan_sql = "'" . mysqli_real_escape_string($conn, $keterangan_input) . "'";
        }

        // Mengecek apakah data presensi untuk jadwal ini sudah ada
        $cek = mysqli_query(
            $conn,
            "SELECT id_absen FROM tb_presensi WHERE id_jadwal = $id_jadwal"
        );

        if (mysqli_num_rows($cek) > 0) {
            // Jika sudah ada, lakukan update data presensi
            $sql = "
                UPDATE tb_presensi SET
                    status = $status_sql,
                    keterangan = $keterangan_sql,
                    waktu_absen = NOW(),
                    dicatat_oleh = $dicatat_oleh
                WHERE id_jadwal = $id_jadwal
            ";
        } else {
            // Jika belum ada, tambahkan data presensi baru
            $sql = "
                INSERT INTO tb_presensi (id_jadwal, status, keterangan, waktu_absen, dicatat_oleh)
                VALUES ($id_jadwal, $status_sql, $keterangan_sql, NOW(), $dicatat_oleh)
            ";
        }

        // Menjalankan query simpan/update presensi
        mysqli_query($conn, $sql);
    }

    // Pesan sukses setelah semua presensi diproses
    $message = "Presensi berhasil disimpan!";
}
?>

<?php
// Menentukan judul halaman dan memanggil template
$page_title = "Presensi Ronda | Siskamling";
include __DIR__ . '/../templates/header.php';
include __DIR__ . '/../templates/sidebar.php';
?>

<div class="container-fluid">

    <h3 class="mb-4">Presensi Ronda</h3>

    <!-- Menampilkan pesan sukses -->
    <?php if (!empty($message)): ?>
        <div class="alert alert-success"><?= $message ?></div>
    <?php endif; ?>

    <div class="p-4 bg-white shadow-sm rounded" style="min-height: 120px;">

        <!-- Form untuk memilih tanggal presensi -->
        <form method="get" class="mb-3">
            <label for="tanggal" class="form-label">Pilih Tanggal:</label>
            <div class="d-flex gap-2 flex-wrap">
                <input type="date"
                       id="tanggal"
                       name="tanggal"
                       class="form-control w-auto"
                       value="<?= htmlspecialchars($tanggal) ?>"
                       required>
                <button type="submit" class="btn btn-primary">Tampilkan</button>
            </div>
        </form>

        <?php if (!empty($tanggal)): ?>
            <?php
            // Mengamankan tanggal untuk query
            $tanggal_safe = mysqli_real_escape_string($conn, $tanggal);

            // Mengambil data jadwal dan presensi berdasarkan tanggal
            $result = mysqli_query($conn, "
                SELECT 
                    j.id_jadwal,
                    j.tanggal_tugas,
                    p.nama_pengguna,
                    pr.status,
                    pr.keterangan
                FROM tb_jadwal j
                JOIN tb_pengguna p ON j.id_pengguna = p.id_pengguna
                LEFT JOIN tb_presensi pr ON j.id_jadwal = pr.id_jadwal
                WHERE j.tanggal_tugas = '$tanggal_safe'
            ");
            ?>

            <?php if (mysqli_num_rows($result) > 0): ?>
                <form method="POST">

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-dark text-center">
                                <tr>
                                    <th>Nama Petugas</th>
                                    <th>Status Kehadiran</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($row['nama_pengguna']) ?></td>

                                        <td class="text-center">
                                            <select name="status[<?= $row['id_jadwal'] ?>]" class="form-select">
                                                <option value="">- Pilih -</option>
                                                <option value="hadir" <?= ($row['status'] === 'hadir') ? 'selected' : '' ?>>
                                                    Hadir
                                                </option>
                                                <option value="tidak hadir" <?= ($row['status'] === 'tidak hadir') ? 'selected' : '' ?>>
                                                    Tidak Hadir
                                                </option>
                                            </select>
                                        </td>

                                        <td>
                                            <input type="text"
                                                   name="keterangan[<?= $row['id_jadwal'] ?>]"
                                                   value="<?= htmlspecialchars($row['keterangan'] ?? '') ?>"
                                                   class="form-control"
                                                   placeholder="Tambahkan keterangan (opsional)">
                                        </td>
                                    </tr>
                                <?php endwhile; ?>

                            </tbody>
                        </table>
                    </div>

                    <!-- Tombol simpan presensi -->
                    <button type="submit" class="btn btn-success mt-2">
                        Simpan Presensi
                    </button>

                </form>
            <?php else: ?>
                <div class="alert alert-warning mt-3">
                    Tidak ada jadwal pada tanggal <?= htmlspecialchars($tanggal) ?>.
                </div>
            <?php endif; ?>
        <?php endif; ?>

    </div>
</div>

<?php include __DIR__ . '/../templates/footer.php'; ?>
