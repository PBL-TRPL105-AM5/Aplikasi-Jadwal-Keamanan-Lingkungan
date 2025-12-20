<?php
include '../config/config.php';

$id = intval($_GET['id']);

$q = mysqli_query($conn, "
    SELECT j.*, p.nama_pengguna
    FROM tb_jadwal j
    LEFT JOIN tb_pengguna p ON j.id_pengguna = p.id_pengguna
    WHERE j.id_jadwal = $id
");

$jadwal = mysqli_fetch_assoc($q);

// ambil presensi
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
    <p><strong>Tanggal Tugas:</strong> <?= htmlspecialchars($jadwal['tanggal_tugas']) ?></p>
    <p><strong>Petugas:</strong> <?= htmlspecialchars($jadwal['nama_pengguna']) ?></p>
    <p><strong>Jam:</strong>
        <?= substr($jadwal['jam_mulai'], 0, 5) ?> -
        <?= substr($jadwal['jam_selesai'], 0, 5) ?>
    </p>

    <hr>
    <h6>Presensi</h6>

    <?php if (mysqli_num_rows($presensiQ) === 0): ?>
        <p>-</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-sm table-bordered mt-2">
                <thead>
                    <tr>
                        <th>Status</th>
                        <th>Keterangan</th>
                        <th>Dicatat Oleh</th>
                        <th>Waktu Absen</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($pr = mysqli_fetch_assoc($presensiQ)): ?>
                        <tr>
                            <td><?= htmlspecialchars($pr['status']) ?></td>
                            <td><?= htmlspecialchars($pr['keterangan']) ?></td>
                            <td><?= htmlspecialchars($pr['dicatat_nama']) ?></td>
                            <td><?= htmlspecialchars($pr['waktu_absen']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<div class="modal-footer">
    <button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
</div>
