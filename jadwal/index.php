<?php
include '../config/config.php';

// Cek login
if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit;
}

// ==========================
// AJAX DETAIL
// ==========================
if (isset($_GET['detail'])) {
    $id = intval($_GET['detail']);

    $q = mysqli_query($conn, "
        SELECT j.*, p.nama_pengguna
        FROM tb_jadwal j
        LEFT JOIN tb_pengguna p ON j.id_pengguna = p.id_pengguna
        WHERE j.id_jadwal = $id
    ");
    $jadwal = mysqli_fetch_assoc($q);

    $presensiQ = mysqli_query($conn, "
        SELECT pr.*, u.nama_pengguna AS dicatat_nama
        FROM tb_presensi pr
        LEFT JOIN tb_pengguna u ON pr.dicatat_oleh = u.id_pengguna
        WHERE pr.id_jadwal = $id
    ");
    ?>

    <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">Detail Jadwal</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>

    <div class="modal-body">
        <p><strong>Tanggal Tugas:</strong> <?= $jadwal['tanggal_tugas'] ?></p>
        <p><strong>Petugas:</strong> <?= $jadwal['nama_pengguna'] ?></p>
        <p><strong>Jam:</strong> <?= substr($jadwal['jam_mulai'],0,5) ?> - <?= substr($jadwal['jam_selesai'],0,5) ?></p>

        <hr>
        <h6>Presensi</h6>

        <?php if (mysqli_num_rows($presensiQ) == 0): ?>
            <p class="text-muted">Belum ada presensi.</p>
        <?php else: ?>
            <table class="table table-bordered table-sm">
                <thead class="table-light">
                    <tr>
                        <th>Status</th>
                        <th>Keterangan</th>
                        <th>Dicatat Oleh</th>
                        <th>Waktu</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($pr = mysqli_fetch_assoc($presensiQ)): ?>
                    <tr>
                        <td><?= $pr['status'] ?></td>
                        <td><?= $pr['keterangan'] ?></td>
                        <td><?= $pr['dicatat_nama'] ?></td>
                        <td><?= $pr['waktu_absen'] ?></td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
    </div>

    <?php exit;
}


// ==========================
// LIST JADWAL
// ==========================
$user = $_SESSION['user'];
$role = $user['role'];
$id_pengguna = $user['id_pengguna'];

$sql = "
    SELECT j.id_jadwal, j.tanggal_tugas, j.jam_mulai, j.jam_selesai,
           p.nama_pengguna, pr.status AS status_presensi
    FROM tb_jadwal j
    LEFT JOIN tb_pengguna p ON j.id_pengguna = p.id_pengguna
    LEFT JOIN tb_presensi pr ON j.id_jadwal = pr.id_jadwal
";

if ($role === 'Petugas') {
    $sql .= " WHERE j.id_pengguna = $id_pengguna ";
}

$sql .= " ORDER BY j.tanggal_tugas ASC, p.nama_pengguna ASC";

$result = mysqli_query($conn, $sql);


// ==========================
// TEMPLATE
// ==========================
$page_title = "Jadwal Ronda | Siskamling";
include __DIR__ . '/../templates/header.php';
include __DIR__ . '/../templates/sidebar.php';
?>

<!-- STYLE: jadwal-box sama seperti buat.php -->
<style>
.jadwal-box {
    background: white;
    padding: 20px 25px;
    border-radius: 12px;
    border: 1px solid #ddd;
    margin-bottom: 25px;
}

/* MATIKAN HOVER */
.table-hover tbody tr:hover {
    background: transparent !important;
}
</style>

<div class="container-fluid">

    <h1 class="h3 mb-4 text-gray-800">Daftar Jadwal Ronda</h1>

    <?php if ($role === 'Admin'): ?>
        <a href="buat.php" class="btn btn-primary mb-3">
            <i class="bi bi-plus-lg"></i> Buat Jadwal
        </a>
    <?php endif; ?>

    <!-- BOX ALIH-ALIH CARD -->
    <div class="jadwal-box">

        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-dark text-center">
                    <tr>
                        <th>Tanggal</th>
                        <th>Petugas</th>
                        <th>Jam</th>
                        <th>Status</th>
                        <th width="120">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>

                        <?php
                        if ($row['status_presensi'] == 'hadir') {
                            $badge = '<span class="badge bg-success">Hadir</span>';
                        } elseif ($row['status_presensi'] == 'tidak hadir') {
                            $badge = '<span class="badge bg-danger">Tidak Hadir</span>';
                        } else {
                            $badge = '<span class="badge bg-secondary">Belum Absen</span>';
                        }
                        ?>

                        <tr>
                            <td><?= $row['tanggal_tugas'] ?></td>
                            <td><?= $row['nama_pengguna'] ?></td>
                            <td><?= substr($row['jam_mulai'],0,5) ?> - <?= substr($row['jam_selesai'],0,5) ?></td>
                            <td class="text-center"><?= $badge ?></td>

                            <td class="text-center">
                                <button class="btn btn-info btn-sm btn-detail" data-id="<?= $row['id_jadwal'] ?>">
                                    Detail
                                </button>
                            </td>
                        </tr>

                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

    </div> <!-- END jadwal-box -->

</div>


<!-- MODAL DETAIL -->
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" id="detailContent"></div>
    </div>
</div>


<?php include '../templates/footer.php'; ?>

<script>
document.querySelectorAll('.btn-detail').forEach(btn => {
    btn.addEventListener('click', function () {
        let id = this.dataset.id;

        fetch('index.php?detail=' + id)
            .then(res => res.text())
            .then(html => {
                document.getElementById('detailContent').innerHTML = html;
                new bootstrap.Modal(document.getElementById('detailModal')).show();
            });
    });
});
</script>
