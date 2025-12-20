<?php
include '../config/config.php';

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit;
}

$user   = $_SESSION['user'];
$id     = $user['id_pengguna'];

$q = mysqli_query($conn, "SELECT * FROM tb_pengguna WHERE id_pengguna='$id'");
$data = mysqli_fetch_assoc($q);

$page_title = "Profil Saya | Siskamling";

include '../templates/header.php';
include '../templates/sidebar.php';
?>

<div class="container-fluid">

    <h1 class="h3 mb-4 text-gray-800">Profil Saya</h1>

    <!-- ALERT -->
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

    <!-- TAMBAHKAN .no-hover -->
    <div class="d-flex gap-4 flex-wrap">

        <!-- BOX PROFIL -->
        <div class="p-4 bg-white shadow-sm rounded no-hover" style="flex: 1; min-width: 320px;">
            <h5 class="mb-3">Informasi Profil</h5>

            <form action="update_profil.php" method="POST">

                <input type="hidden" name="id_pengguna" value="<?= $data['id_pengguna'] ?>">

                <div class="mb-3">
                    <label class="form-label">Nama Pengguna</label>
                    <input type="text" name="nama_pengguna" class="form-control"
                        value="<?= htmlspecialchars($data['nama_pengguna']) ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control"
                        value="<?= htmlspecialchars($data['email']) ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Alamat</label>
                    <textarea name="alamat" rows="2" class="form-control"><?= htmlspecialchars($data['alamat']) ?></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">No. Telepon</label>
                    <input type="text" name="no_telp" class="form-control"
                        value="<?= htmlspecialchars($data['no_telp']) ?>">
                </div>

                <div class="mb-3">
                    <label class="form-label">Role</label>
                    <input type="text" class="form-control" value="<?= $data['role'] ?>" readonly>
                </div>

                <button class="btn btn-primary w-100">
                    <i class="bi bi-save"></i> Update Profil
                </button>

            </form>
        </div>


        <!-- BOX GANTI PASSWORD -->
        <div class="p-4 bg-white shadow-sm rounded no-hover" style="flex: 1; min-width: 320px;">
            <h5 class="mb-3">Ganti Password</h5>

            <form action="update_password.php" method="POST">

                <input type="hidden" name="id_pengguna" value="<?= $data['id_pengguna'] ?>">

                <div class="mb-3">
                    <label class="form-label">Password Lama</label>
                    <div class="input-group">
                        <input type="password" name="password_lama" id="password_lama" class="form-control" required>
                        <span class="input-group-text" onclick="togglePass('password_lama', this)" style="cursor:pointer;">
                            <i class="bi bi-eye-fill"></i>
                        </span>
                    </div>
                </div>


                <div class="mb-3">
                    <label class="form-label">Password Baru</label>
                    <div class="input-group">
                        <input type="password" name="password_baru" id="password_baru" class="form-control" required>
                        <span class="input-group-text" onclick="togglePass('password_baru', this)" style="cursor:pointer;">
                            <i class="bi bi-eye-fill"></i>
                        </span>
                    </div>
                </div>


                <div class="mb-3">
                    <label class="form-label">Konfirmasi Password Baru</label>
                    <div class="input-group">
                        <input type="password" name="konfirmasi" id="konfirmasi" class="form-control" required>
                        <span class="input-group-text" onclick="togglePass('konfirmasi', this)" style="cursor:pointer;">
                            <i class="bi bi-eye-fill"></i>
                        </span>
                    </div>
                </div>


                <button class="btn btn-primary w-100">Simpan Password</button>
            </form>
        </div>

    </div>


</div>

<?php include '../templates/footer.php'; ?>
