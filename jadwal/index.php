<?php
include '../config/config.php';

/* ================= FUNGSI TANGGAL INDONESIA ================= */
function tanggal_indonesia($tanggal)
{
    $hari = [
        'Minggu',
        'Senin',
        'Selasa',
        'Rabu',
        'Kamis',
        'Jumat',
        'Sabtu'
    ];

    $timestamp = strtotime($tanggal);
    $nama_hari = $hari[date('w', $timestamp)];
    $tgl = date('d-m-Y', $timestamp);

    return $nama_hari . ', ' . $tgl;
}

/* ================= CEK LOGIN ================= */
if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit;
}

/* ================= AJAX DETAIL ================= */
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
        <p><strong>Tanggal:</strong> <?= tanggal_indonesia($jadwal['tanggal_tugas']) ?></p>
        <p><strong>Petugas:</strong> <?= $jadwal['nama_pengguna'] ?></p>
        <p><strong>Jam:</strong> <?= substr($jadwal['jam_mulai'],0,5) ?> - <?= substr($jadwal['jam_selesai'],0,5) ?></p>

        <hr>
        <h6>Presensi</h6>

        <?php if (mysqli_num_rows($presensiQ) == 0): ?>
            <p class="text-muted">Belum ada presensi.</p>
        <?php else: ?>
            <table class="table table-bordered table-sm">
                <thead>
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
<?php
    exit;
}

/* ================= USER LOGIN ================= */
$user = $_SESSION['user'];
$role = $user['role'];
$id_pengguna = $user['id_pengguna'];

/* ================= PAGINATION ================= */
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = ($page < 1) ? 1 : $page;
$offset = ($page - 1) * $limit;

/* ================= HITUNG TOTAL DATA ================= */
$countSql = "
    SELECT COUNT(*) AS total
    FROM tb_jadwal
    WHERE tanggal_tugas >= CURDATE()
";

if ($role === 'Petugas') {
    $countSql .= " AND id_pengguna = $id_pengguna";
}

$countResult = mysqli_query($conn, $countSql);
$totalData = mysqli_fetch_assoc($countResult)['total'];
$totalPage = ceil($totalData / $limit);

/* ================= LIST JADWAL ================= */
$sql = "
    SELECT j.id_jadwal, j.tanggal_tugas, j.jam_mulai, j.jam_selesai,
           p.nama_pengguna, pr.status AS status_presensi
    FROM tb_jadwal j
    LEFT JOIN tb_pengguna p ON j.id_pengguna = p.id_pengguna
    LEFT JOIN tb_presensi pr ON j.id_jadwal = pr.id_jadwal
    WHERE j.tanggal_tugas >= CURDATE()
";

if ($role === 'Petugas') {
    $sql .= " AND j.id_pengguna = $id_pengguna ";
}

$sql .= "
    ORDER BY j.tanggal_tugas ASC
    LIMIT $limit OFFSET $offset
";

$result = mysqli_query($conn, $sql);

$page_title = "Jadwal Ronda";
include '../templates/header.php';
include '../templates/sidebar.php';
?>

<style>
.jadwal-box {
    background: white;
    padding: 20px 25px;
    border-radius: 12px;
    border: 1px solid #ddd;
    margin-bottom: 25px;
}
</style>

<div class="container-fluid">

    <h1 class="h3 mb-4 text-gray-800">Daftar Jadwal Ronda</h1>

    <?php if ($role === 'Admin'): ?>
        <a href="buat.php" class="btn btn-primary mb-3">
            <i class="bi bi-plus-lg"></i> Buat Jadwal
        </a>
    <?php endif; ?>

    <div class="jadwal-box">
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-dark text-center">
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Petugas</th>
                        <th>Jam</th>
                        <th>Status</th>
                        <th width="120">Aksi</th>
                    </tr>
                </thead>
                <tbody>

                <?php $no = ($page - 1) * $limit + 1; ?>

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
                        <td><?= $no++ ?></td>
                        <td><?= tanggal_indonesia($row['tanggal_tugas']) ?></td>
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

        <!-- PAGINATION -->
        <nav>
            <ul class="pagination justify-content-center mt-3">

                <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= $page - 1 ?>">Previous</a>
                </li>

                <?php for ($i = 1; $i <= $totalPage; $i++): ?>
                    <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>

                <li class="page-item <?= ($page >= $totalPage) ? 'disabled' : '' ?>">
                    <a class="page-link" href="?page=<?= $page + 1 ?>">Next</a>
                </li>

            </ul>
        </nav>

    </div>
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
