<?php
include '../config/config.php';

// Pastikan user login
if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit;
}

$user = $_SESSION['user'];
$role = $user['role'];

// Hanya Admin yang boleh buat jadwal
if ($role !== 'Admin') {
    die("<div style='margin:20px;'>Anda tidak memiliki akses ke halaman ini.</div>");
}

// Ambil semua petugas
$petugas = mysqli_query($conn, "SELECT * FROM tb_pengguna WHERE role='Petugas'");

$tanggal_mulai = isset($_GET['mulai']) ? $_GET['mulai'] : '';
$hariNama = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
?>

<?php include '../templates/header.php'; ?>
<?php include '../templates/sidebar.php'; ?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Buat Jadwal Ronda</h1>

    <!-- KONTENER NON-CARD -->
    <div class="p-4 mb-4" 
         style="background:white; border-radius:12px; border:1px solid #e0e0e0;">

        <!-- FORM PILIH TANGGAL -->
        <form method="get" class="mb-3">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <label class="form-label">Pilih Tanggal Mulai:</label>
                    <input type="date" name="mulai" class="form-control"
                        value="<?= htmlspecialchars($tanggal_mulai) ?>" required>
                </div>

                <div class="col-md-3 mt-3 mt-md-0">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-calendar-alt"></i> Tampilkan
                    </button>
                    <a href="index.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
        </form>

        <?php if (!empty($tanggal_mulai)): ?>
            <form method="post" action="simpan.php">
                <input type="hidden" name="mulai" value="<?= htmlspecialchars($tanggal_mulai) ?>">

                <div class="table-responsive">
                    <table class="table table-bordered text-center align-middle table-hover">
                        <thead class="table-primary">
                            <tr>
                                <th>Nama Petugas</th>

                                <?php
                                for ($i = 0; $i < 7; $i++) {
                                    $tgl = date('Y-m-d', strtotime("$tanggal_mulai +$i day"));
                                    $hari = $hariNama[date('w', strtotime($tgl))];

                                    echo "<th>$hari<br><small>($tgl)</small></th>";
                                }
                                ?>

                                <th>Total</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php while ($p = mysqli_fetch_assoc($petugas)): ?>
                                <tr class="row-<?= $p['id_pengguna'] ?>">
                                    <td class="text-start"><?= htmlspecialchars($p['nama_pengguna']) ?></td>

                                    <?php
                                    for ($i = 0; $i < 7; $i++):
                                        $tgl = date('Y-m-d', strtotime("$tanggal_mulai +$i day"));
                                        $cek = mysqli_query($conn, "
                                            SELECT 1 FROM tb_jadwal
                                            WHERE id_pengguna='{$p['id_pengguna']}'
                                              AND tanggal_tugas='$tgl'
                                        ");
                                        $checked = mysqli_num_rows($cek) > 0 ? 'checked' : '';
                                    ?>
                                        <td>
                                            <input type="checkbox"
                                                name="jadwal[<?= $p['id_pengguna'] ?>][]"
                                                value="<?= $tgl ?>"
                                                <?= $checked ?>
                                                onchange="hitungTotal(<?= $p['id_pengguna'] ?>)">
                                        </td>
                                    <?php endfor; ?>

                                    <td id="total-<?= $p['id_pengguna'] ?>">0</td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

                <div class="text-end mt-3">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Simpan Jadwal
                    </button>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>

<script>
function hitungTotal(id) {
    const cb = document.querySelectorAll('.row-' + id + ' input[type=checkbox]');
    let total = 0;
    cb.forEach(c => { if (c.checked) total++; });
    document.getElementById('total-' + id).innerText = total;
}

function hitungSemua() {
    document.querySelectorAll('[id^="total-"]').forEach(td => {
        const id = td.id.replace('total-', '');
        hitungTotal(id);
    });
}

window.onload = hitungSemua;
</script>

<?php include '../templates/footer.php'; ?>
