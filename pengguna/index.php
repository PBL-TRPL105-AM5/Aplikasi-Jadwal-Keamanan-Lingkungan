<?php
include '../config/config.php';

//SEARCH
$search = "";
if (isset($_GET['search']) && $_GET['search'] !== "") {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $sql = "SELECT * FROM tb_pengguna 
            WHERE nama_pengguna LIKE '%$search%' 
            OR alamat LIKE '%$search%' 
            OR email LIKE '%$search%' 
            OR no_telp LIKE '%$search%'";
} else {
    $sql = "SELECT * FROM tb_pengguna";
}

$result = mysqli_query($conn, $sql);

$page_title = "Pengguna Ronda | Siskamling";
include __DIR__ . '/../templates/header.php';
include __DIR__ . '/../templates/sidebar.php';
?>

<div class="container-fluid">

    <h1 class="h3 mb-4 text-gray-800">Data Pengguna</h1>

    <!-- ALERT -->
    <?php if (!empty($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $_SESSION['success']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php unset($_SESSION['success']); endif; ?>

    <!-- SEARCH BAR -->
    <form method="GET" class="mb-3">
        <div class="input-group" style="max-width: 350px;">
            <input type="text" name="search" class="form-control" placeholder="Cari pengguna..."
                   value="<?= htmlspecialchars($search) ?>">
            <button class="btn btn-primary"><i class="fas fa-search"></i></button>
        </div>
    </form>

    <!-- TOMBOL TAMBAH -->
    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#modalTambah">
        <i class="fas fa-plus"></i> Tambah Pengguna
    </button>

    <!-- TABEL -->
    <!-- WRAPPER FLEX -->
    <div class="p-4 bg-white shadow-sm rounded no-hover">

        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0">
                <thead class="table-dark text-center">
                    <tr>
                        <th>ID</th>
                        <th>Nama Pengguna</th>
                        <th>Alamat</th>
                        <th>Jenis Kelamin</th>
                        <th>No. Telepon</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th width="160">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td class="text-center"><?= $row['id_pengguna'] ?></td>
                                <td><?= htmlspecialchars($row['nama_pengguna']) ?></td>
                                <td><?= htmlspecialchars($row['alamat']) ?></td>
                                <td class="text-center"><?= $row['jenis_kelamin'] ?></td>
                                <td><?= htmlspecialchars($row['no_telp']) ?></td>
                                <td><?= htmlspecialchars($row['email']) ?></td>
                                <td class="text-center">
                                    <?php
                                        $role = $row['role'];
                                        if ($role === 'Admin') {
                                            echo '<span class="badge bg-danger">Admin</span>';
                                        } elseif ($role === 'Koordinator') {
                                            echo '<span class="badge bg-warning text-dark">Koordinator</span>';
                                        } else {
                                            echo '<span class="badge bg-primary">Petugas</span>';
                                        }
                                    ?>
                                </td>

                                <td class="text-center">

                                    <button class="btn btn-sm btn-info btnDetail"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalDetail"
                                        data-nama="<?= htmlspecialchars($row['nama_pengguna']) ?>"
                                        data-alamat="<?= htmlspecialchars($row['alamat']) ?>"
                                        data-jk="<?= $row['jenis_kelamin'] ?>"
                                        data-telp="<?= htmlspecialchars($row['no_telp']) ?>"
                                        data-email="<?= htmlspecialchars($row['email']) ?>"
                                        data-role="<?= $row['role'] ?>">
                                        <i class="fas fa-info-circle"></i>
                                    </button>

                                    <button class="btn btn-sm btn-warning btnEdit"
                                        data-id="<?= $row['id_pengguna'] ?>"
                                        data-nama="<?= htmlspecialchars($row['nama_pengguna']) ?>"
                                        data-alamat="<?= htmlspecialchars($row['alamat']) ?>"
                                        data-jk="<?= $row['jenis_kelamin'] ?>"
                                        data-telp="<?= htmlspecialchars($row['no_telp']) ?>"
                                        data-email="<?= htmlspecialchars($row['email']) ?>"
                                        data-role="<?= $row['role'] ?>"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalEdit">
                                        <i class="fas fa-edit"></i>
                                    </button>

                                    <a href="hapus_pengguna.php?id=<?= $row['id_pengguna'] ?>"
                                    class="btn btn-sm btn-danger"
                                    onclick="return confirm('Yakin ingin menghapus data ini?')">
                                        <i class="fas fa-trash"></i>
                                    </a>

                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="8" class="text-center text-muted">Tidak ada data.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>


</div>

<!-- MODAL DETAIL PENGGUNA -->
<div class="modal fade" id="modalDetail" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">

            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">Detail Pengguna</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <p><strong>Nama:</strong> <span id="detail_nama"></span></p>
                <p><strong>Alamat:</strong> <span id="detail_alamat"></span></p>
                <p><strong>Jenis Kelamin:</strong> <span id="detail_jk"></span></p>
                <p><strong>No. Telepon:</strong> <span id="detail_telp"></span></p>
                <p><strong>Email:</strong> <span id="detail_email"></span></p>
                <p><strong>Role:</strong> <span id="detail_role"></span></p>

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>

        </div>
    </div>
</div>


<!-- MODAL TAMBAH -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-user-plus"></i> Tambah Pengguna</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form action="tambah_pengguna.php" method="POST">
                <div class="modal-body">
                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Pengguna</label>
                            <input type="text" name="nama_pengguna" class="form-control" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control">
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label">Alamat</label>
                            <textarea name="alamat" class="form-control" rows="2"></textarea>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-select" required>
                                <option value="">-- pilih --</option>
                                <option value="L">Laki-Laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">No. Telepon</label>
                            <input type="text" name="no_telp" class="form-control">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Role</label>
                            <select name="role" class="form-select" required>
                                <option value="Admin">Admin</option>
                                <option value="Koordinator">Koordinator</option>
                                <option value="Petugas" selected>Petugas</option>
                            </select>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>

            </form>

        </div>
    </div>
</div>

<!-- MODAL EDIT -->
<div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-edit"></i> Edit Pengguna</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form action="edit_pengguna.php" method="POST">
                <div class="modal-body">

                    <input type="hidden" name="id_pengguna" id="edit_id">

                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Pengguna</label>
                            <input type="text" name="nama_pengguna" id="edit_nama" class="form-control">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" id="edit_email" class="form-control">
                        </div>

                        <div class="col-md-12 mb-3">
                            <label class="form-label">Alamat</label>
                            <textarea name="alamat" id="edit_alamat" class="form-control" rows="2"></textarea>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jenis Kelamin</label>
                            <select name="jenis_kelamin" id="edit_jk" class="form-select">
                                <option value="L">Laki-Laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">No. Telepon</label>
                            <input type="text" name="no_telp" id="edit_telp" class="form-control">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Password (kosong = tidak ganti)</label>
                            <input type="password" name="password" id="edit_password" class="form-control">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Role</label>
                            <select name="role" id="edit_role" class="form-select">
                                <option value="Admin">Admin</option>
                                <option value="Koordinator">Koordinator</option>
                                <option value="Petugas">Petugas</option>
                            </select>
                        </div>

                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-warning">Update</button>
                </div>

            </form>

        </div>
    </div>
</div>

<?php include '../templates/footer.php'; ?>

<!-- JAVASCRIPT MODAL -->
<script>
// DETAIL
document.querySelectorAll('.btnDetail').forEach(btn => {
    btn.addEventListener('click', function() {
        document.getElementById('detail_nama').innerText = this.dataset.nama;
        document.getElementById('detail_alamat').innerText = this.dataset.alamat;
        document.getElementById('detail_jk').innerText = this.dataset.jk;
        document.getElementById('detail_telp').innerText = this.dataset.telp;
        document.getElementById('detail_email').innerText = this.dataset.email;
        document.getElementById('detail_role').innerText = this.dataset.role;
    });
});

// EDIT
document.querySelectorAll('.btnEdit').forEach(btn => {
    btn.addEventListener('click', function() {
        document.getElementById('edit_id').value = this.dataset.id;
        document.getElementById('edit_nama').value = this.dataset.nama;
        document.getElementById('edit_alamat').value = this.dataset.alamat;
        document.getElementById('edit_jk').value = this.dataset.jk;
        document.getElementById('edit_telp').value = this.dataset.telp;
        document.getElementById('edit_email').value = this.dataset.email;
        document.getElementById('edit_role').value = this.dataset.role;

        document.getElementById('edit_password').value = ""; // selalu kosong
    });
});
</script>
