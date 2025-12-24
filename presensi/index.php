<?php
// Include konfigurasi
include __DIR__ . '/../config/config.php';

// Cek login dan role
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['Admin', 'Koordinator'])) {
    die("Akses hanya untuk Admin atau Koordinator");
}

$user = $_SESSION['user'];
$tanggal = $_GET['tanggal'] ?? '';
$message = '';

// Jika form presensi disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status'])) {

    foreach ($_POST['status'] as $id_jadwal => $status) {

        $id_jadwal = (int)$id_jadwal;
        $dicatat_oleh = (int)$_SESSION['user']['id_pengguna'];

        // --- Status ---
        if ($status === '') {
            $status_sql = "NULL";
        } else {
            $status = strtolower($status) === 'hadir' ? 'hadir' : 'tidak hadir';
            $status_sql = "'" . mysqli_real_escape_string($conn, $status) . "'";
        }

        // --- Keterangan ---
        $keterangan_input = $_POST['keterangan'][$id_jadwal] ?? '';
        if (trim($keterangan_input) === '') {
            $keterangan_sql = "NULL";
        } else {
            $keterangan_sql = "'" . mysqli_real_escape_string($conn, $keterangan_input) . "'";
        }

        // Cek apakah sudah ada presensi sebelumnya
        $cek = mysqli_query($conn, "SELECT id_absen FROM tb_presensi WHERE id_jadwal = $id_jadwal"); //Sistem mengecek apakah jadwal tersebut sudah memiliki data presensi.

        if (mysqli_num_rows($cek) > 0) {
            // UPDATE
            $sql = "
                UPDATE tb_presensi SET
                    status = $status_sql,
                    keterangan = $keterangan_sql,
                    waktu_absen = NOW(),
                    dicatat_oleh = $dicatat_oleh
                WHERE id_jadwal = $id_jadwal
            "; // Jika data presensi sudah ada, sistem akan memperbarui data tersebut dengan status, keterangan, waktu absen, dan siapa yang mencatatnya.
        } else {
            $sql = "
                INSERT INTO tb_presensi (id_jadwal, status, keterangan, waktu_absen, dicatat_oleh)
                VALUES ($id_jadwal, $status_sql, $keterangan_sql, NOW(), $dicatat_oleh)
            "; // Jika belum ada, sistem akan menambahkan data presensi baru dengan informasi yang diberikan.
        }

        mysqli_query($conn, $sql);
    }

    $message = "Presensi berhasil disimpan!";
}

?>

<?php
$page_title = "Presensi Ronda | Siskamling";
include __DIR__ . '/../templates/header.php';
include __DIR__ . '/../templates/sidebar.php';
?>


<div class="container-fluid">

    <h3 class="mb-4">Presensi Ronda</h3>

    <?php if (!empty($message)): ?>
        <div class="alert alert-success"><?= $message ?></div>
    <?php endif; ?>


    <!-- WRAPPER FLEX PUTIH -->
    <div class="p-4 bg-white shadow-sm rounded" style="min-height: 120px;">

        <!-- Form pilih tanggal -->
        <form method="get" class="mb-3">
            <label for="tanggal" class="form-label">Pilih Tanggal:</label>
            <div class="d-flex gap-2 flex-wrap">
                <input type="date" id="tanggal" name="tanggal" class="form-control w-auto"
                       value="<?= htmlspecialchars($tanggal) ?>" required>
                <button type="submit" class="btn btn-primary">Tampilkan</button>
            </div>
        </form>


        <?php if (!empty($tanggal)): ?>
            <?php
            $tanggal_safe = mysqli_real_escape_string($conn, $tanggal);

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
            "); // Query ini menampilkan jadwal dan status presensi pada tanggal tertentu.
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

                    <button type="submit" class="btn btn-success mt-2">Simpan Presensi</button>

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
