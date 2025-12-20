<?php
include '../config/config.php';

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit;
}

$user = $_SESSION['user'];
$role = $user['role'];
$id_pengguna = $user['id_pengguna'];

// ===============================
// FILTER & SEARCH
// ===============================
$where = " WHERE 1=1 ";

$search      = $_GET['search']      ?? '';
$start_date  = $_GET['start_date']  ?? '';
$end_date    = $_GET['end_date']    ?? '';

if ($search !== '') {
    $s = mysqli_real_escape_string($conn, $search);
    $where .= " AND (i.nama_insiden LIKE '%$s%' 
                OR p.nama_pengguna LIKE '%$s%')";
}

if ($start_date !== '') {
    $s = mysqli_real_escape_string($conn, $start_date);
    $where .= " AND DATE(i.timestamp) >= '$s'";
}

if ($end_date !== '') {
    $e = mysqli_real_escape_string($conn, $end_date);
    $where .= " AND DATE(i.timestamp) <= '$e'";
}

// ===============================
// QUERY INSIDEN
// ===============================
$sql = "
    SELECT i.*, p.nama_pengguna 
    FROM tb_insiden i
    LEFT JOIN tb_pengguna p ON i.id_pengguna = p.id_pengguna
    $where
    ORDER BY i.timestamp DESC
";
$result = mysqli_query($conn, $sql);

$page_title = "Catatan Insiden | Siskamling";
include __DIR__ . '/../templates/header.php';
include __DIR__ . '/../templates/sidebar.php';
?>

<div class="container-fluid">

    <h1 class="h3 mb-4 text-gray-800">Catatan Insiden</h1>

    <?php if (!empty($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= $_SESSION['success']; unset($_SESSION['success']); ?>
            <button class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (!empty($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            <button class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>


    <div class="p-4 bg-white shadow-sm rounded mb-4">

        <?php if (in_array($role, ['Petugas','Koordinator','Admin'])): ?>
            <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalTambah">
                <i class="bi bi-plus-circle"></i> Catat Insiden
            </button>
        <?php endif; ?>

        <!-- FORM FILTER -->
        <form class="row mb-3">

            <div class="col-md-3 mb-2">
                <label>Cari Insiden</label>
                <input type="text" name="search" class="form-control"
                       value="<?= htmlspecialchars($search) ?>">
            </div>

            <div class="col-md-3 mb-2">
                <label>Dari Tanggal</label>
                <input type="date" name="start_date" class="form-control"
                       value="<?= htmlspecialchars($start_date) ?>">
            </div>

            <div class="col-md-3 mb-2">
                <label>Sampai Tanggal</label>
                <input type="date" name="end_date" class="form-control"
                       value="<?= htmlspecialchars($end_date) ?>">
            </div>

            <div class="col-md-3 mb-2 d-flex align-items-end">
                <button class="btn btn-secondary w-100"><i class="bi bi-search"></i> Tampilkan</button>
            </div>

        </form>

        <!-- TABEL INSIDEN -->
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-dark text-center">
                    <tr>
                        <th>Waktu</th>
                        <th>Nama Insiden</th>
                        <th>Pelapor</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>

                            <?php 
                                $status = $row['status'];
                                $boleh_edit = ($status === 'pending');

                                $badge = [
                                    'pending'  => 'secondary',
                                    'diterima' => 'success',
                                    'ditolak'  => 'danger'
                                ];
                            ?>

                            <tr>
                                <td><?= date('d-m-Y H:i', strtotime($row['timestamp'])) ?></td>
                                <td><?= htmlspecialchars($row['nama_insiden']) ?></td>
                                <td><?= htmlspecialchars($row['nama_pengguna']) ?></td>

                                <td class="text-center">
                                    <span class="badge bg-<?= $badge[$status] ?> text-uppercase">
                                        <?= $status ?>
                                    </span>
                                </td>

                                <td class="text-center">
                                    <button class="btn btn-info btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#detail<?= $row['id_insiden'] ?>">
                                        Detail
                                    </button>
                                </td>
                            </tr>

                            <!-- MODAL DETAIL -->
                            <div class="modal fade" id="detail<?= $row['id_insiden'] ?>">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">

                                        <div class="modal-header bg-info text-white">
                                            <h5 class="modal-title">Detail Insiden</h5>
                                            <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body">

                                            <div class="mb-2"><strong>Waktu:</strong> <?= date('d-m-Y H:i', strtotime($row['timestamp'])) ?></div>
                                            <div class="mb-2"><strong>Nama Insiden:</strong> <?= htmlspecialchars($row['nama_insiden']) ?></div>
                                            <div class="mb-2"><strong>Pelapor:</strong> <?= htmlspecialchars($row['nama_pengguna']) ?></div>
                                            <div class="mb-2"><strong>Deskripsi:</strong><br><?= nl2br(htmlspecialchars($row['deskripsi'])) ?></div>

                                            <div class="mb-2"><strong>Status:</strong> 
                                                <span class="badge bg-<?= $badge[$status] ?> text-uppercase">
                                                    <?= $status ?>
                                                </span>
                                            </div>

                                            <div class="mb-3"><strong>Catatan Admin:</strong><br>
                                                <?= nl2br(htmlspecialchars($row['catatan_admin'] ?? '-')) ?>
                                            </div>

                                            <?php if (in_array($role, ['Admin','Koordinator'])): ?>

                                                <form method="POST" action="update_insiden.php">

                                                    <input type="hidden" name="id_insiden"
                                                           value="<?= $row['id_insiden'] ?>">

                                                    <label>Status</label>
                                                    <select name="status" 
                                                            class="form-control mb-3"
                                                            <?= !$boleh_edit ? "disabled" : "" ?>>
                                                        <option value="pending"  <?= $status=='pending'?'selected':'' ?>>Pending</option>
                                                        <option value="diterima" <?= $status=='diterima'?'selected':'' ?>>Diterima</option>
                                                        <option value="ditolak"  <?= $status=='ditolak'?'selected':'' ?>>Ditolak</option>
                                                    </select>

                                                    <label>Catatan Admin</label>
                                                    <textarea name="catatan_admin"
                                                        class="form-control mb-3"
                                                        rows="3"
                                                        <?= !$boleh_edit ? "readonly" : "" ?>><?= htmlspecialchars(trim($row['catatan_admin'] ?? '')) ?>
                                                    </textarea>


                                                    <?php if ($boleh_edit): ?>
                                                        <button class="btn btn-success">Simpan Perubahan</button>
                                                    <?php else: ?>
                                                        <div class="alert alert-info mt-2">
                                                            Status sudah final — tidak dapat diubah.
                                                        </div>
                                                    <?php endif; ?>

                                                </form>

                                            <?php endif; ?>

                                        </div>
                                    </div>
                                </div>
                            </div>

                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center">Tidak ada data.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<!-- MODAL TAMBAH -->
<div class="modal fade" id="modalTambah">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Catat Insiden Baru</h5>
                <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form action="catat_insiden.php" method="POST">
                <div class="modal-body">

                    <div class="mb-3">
                        <label>Nama Insiden</label>
                        <input type="text" name="nama_insiden" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label>Deskripsi</label>
                        <textarea name="deskripsi" rows="4" class="form-control" required></textarea>
                    </div>

                    <input type="hidden" name="id_pengguna" value="<?= $id_pengguna ?>">

                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button class="btn btn-primary">Simpan</button>
                </div>

            </form>

        </div>
    </div>
</div>

<?php include '../templates/footer.php'; ?>
