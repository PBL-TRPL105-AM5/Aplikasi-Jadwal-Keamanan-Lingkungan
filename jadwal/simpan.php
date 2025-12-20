<?php
include '../config/config.php';
include '../helpers/email_helper.php'; // gunakan helper email

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $mulai  = $_POST['mulai'];
    $jadwal = $_POST['jadwal'] ?? [];

    // Array untuk email perubahan jadwal
    $penambahan = [];
    $penghapusan = [];

    // Generate jadwal 7 hari ke depan
    for ($i = 0; $i < 7; $i++) {

        $tgl = date('Y-m-d', strtotime("$mulai +$i day"));

        // Petugas baru dari form (yang diceklis admin)
        $petugasBaru = [];
        foreach ($jadwal as $id_pengguna => $tanggalArray) {
            if (in_array($tgl, $tanggalArray)) {
                $petugasBaru[] = $id_pengguna;
            }
        }

        // Petugas lama (data yang sudah ada)
        $res = mysqli_query($conn,
            "SELECT id_pengguna FROM tb_jadwal WHERE tanggal_tugas='$tgl'"
        );

        $petugasLama = [];
        while ($r = mysqli_fetch_assoc($res)) {
            $petugasLama[] = $r['id_pengguna'];
        }

        // Hitung perubahan
        $toAdd    = array_diff($petugasBaru, $petugasLama); // jadwal baru
        $toDelete = array_diff($petugasLama, $petugasBaru); // jadwal dihapus

        // Hapus jadwal lama
        foreach ($toDelete as $id_pengguna) {

            mysqli_query($conn,
                "DELETE FROM tb_jadwal 
                 WHERE tanggal_tugas='$tgl' AND id_pengguna='$id_pengguna'"
            );

            // simpan untuk email penghapusan
            $penghapusan[$id_pengguna][] = $tgl;
        }

        // Tambah jadwal baru
        foreach ($toAdd as $id_pengguna) {

            mysqli_query($conn, "
                INSERT INTO tb_jadwal (id_pengguna, tanggal_tugas, jam_mulai, jam_selesai)
                VALUES ('$id_pengguna', '$tgl', '21:00:00', '05:00:00')
            ");

            // simpan untuk email penambahan
            $penambahan[$id_pengguna][] = $tgl;
        }
    }

    // === KIRIM EMAIL PERUBAHAN JADWAL ===
    foreach ($penambahan as $id_pengguna => $listTambah) {

        // pastikan array penghapusan ada, jika tidak buat array kosong
        $listHapus = $penghapusan[$id_pengguna] ?? [];

        // Ambil data pengguna
        $q = mysqli_query($conn,
            "SELECT nama_pengguna AS nama, email 
             FROM tb_pengguna 
             WHERE id_pengguna='$id_pengguna'"
        );

        if ($p = mysqli_fetch_assoc($q)) {

            if (!empty($p['email'])) {

                // Hanya kirim email jika ADA penambahan atau penghapusan
                kirimEmailPerubahanJadwal(
                    $p['nama'],
                    $p['email'],
                    $listTambah,   // daftar penambahan
                    $listHapus     // daftar penghapusan
                );
            }
        }
    }

    // === Kirim email untuk pengguna yang hanya DIHAPUS jadwalnya ===
    foreach ($penghapusan as $id_pengguna => $listHapus) {

        // Jika sudah dikirim pada loop penambahan, skip
        if (isset($penambahan[$id_pengguna])) continue;

        $q = mysqli_query($conn,
            "SELECT nama_pengguna AS nama, email 
             FROM tb_pengguna 
             WHERE id_pengguna='$id_pengguna'"
        );

        if ($p = mysqli_fetch_assoc($q)) {

            if (!empty($p['email'])) {
                kirimEmailPerubahanJadwal(
                    $p['nama'],
                    $p['email'],
                    [],            // tidak ada penambahan
                    $listHapus     // hanya penghapusan
                );
            }
        }
    }

    header("Location: index.php?mulai=$mulai&msg=tersimpan");
    exit;
}
?>
