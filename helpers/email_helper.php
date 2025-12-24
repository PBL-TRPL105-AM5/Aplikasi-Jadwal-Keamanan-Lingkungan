<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Memuat library PHPMailer secara aman
 * Jika gagal diload, sistem tidak akan error fatal
 */
function loadMailerSafe()
{
    try {
        // Autoload PHPMailer dari folder vendor
        require_once __DIR__ . '/../assets/vendor/autoload.php';
        return true;
    } catch (Throwable $e) {
        // Simpan error ke log server
        error_log("PHPMailer gagal diload: " . $e->getMessage());
        return false;
    }
}

/**
 * Mengubah daftar tanggal menjadi format HTML list
 * $withTime  : menampilkan jam ronda
 * $strike    : mencoret teks (untuk jadwal yang dihapus)
 */
function formatTanggalList($tanggalList, $withTime = true, $strike = false)
{
    // Jika daftar tanggal kosong, tidak perlu diproses
    if (empty($tanggalList)) return "";

    $out = "";

    // Loop setiap tanggal
    foreach ($tanggalList as $tgl) {
        $dt = new DateTime($tgl);

        // Format hari dan tanggal
        $hari = $dt->format('l');
        $tanggal = $dt->format('d F Y');

        // Jam ronda opsional
        $jam = $withTime ? " (21:00 - 05:00)" : "";

        $text = "{$hari}, {$tanggal}{$jam}";

        // Jika jadwal dihapus, beri coretan
        if ($strike) {
            $text = "<span style='text-decoration: line-through;'>{$text}</span>";
        }

        // Tambahkan ke output HTML
        $out .= "• {$text}<br>";
    }

    return $out;
}

/**
 * Mengirim email notifikasi perubahan jadwal ronda
 * Email dikirim jika ada:
 * - Penambahan jadwal
 * - Penghapusan jadwal
 */
function kirimEmailPerubahanJadwal($nama, $email, $penambahan, $penghapusan)
{
    // Memuat konfigurasi email
    require __DIR__ . '/../config/email_config.php';

    // Jika tidak ada perubahan, email tidak dikirim
    if (empty($penambahan) && empty($penghapusan)) {
        return false;
    }

    // Pastikan PHPMailer berhasil dimuat
    if (!loadMailerSafe()) {
        return false;
    }

    // Format daftar tanggal penambahan dan penghapusan
    $htmlAdd = formatTanggalList($penambahan, true, false);
    $htmlDel = formatTanggalList($penghapusan, false, true);

    // Template isi email
    $body = "
        <p>Yth. Bapak/Ibu <b>{$nama}</b>,</p>
        <p>Terdapat perubahan pada jadwal ronda Anda. Berikut adalah rinciannya:</p>
    ";

    // Tambahkan bagian penambahan jadwal jika ada
    if (!empty($htmlAdd)) {
        $body .= "
            <p><b>Penambahan Jadwal:</b><br>
            {$htmlAdd}</p>
        ";
    }

    // Tambahkan bagian penghapusan jadwal jika ada
    if (!empty($htmlDel)) {
        $body .= "
            <p><b>Penghapusan Jadwal:</b><br>
            {$htmlDel}</p>
        ";
    }

    // Penutup email
    $body .= "
        <p>Mohon menyesuaikan dengan jadwal terbaru tersebut.</p>
        <br>
        <p>Hormat kami,<br>
        <b>Sistem Informasi Ronda</b></p>
    ";

    // Proses pengiriman email
    try {
        $mail = new PHPMailer(true);

        // Konfigurasi SMTP
        $mail->isSMTP();
        $mail->Host       = $MAIL_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = $MAIL_USERNAME;
        $mail->Password   = $MAIL_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Pengirim dan penerima email
        $mail->setFrom($MAIL_USERNAME, 'Sistem Ronda');
        $mail->addAddress($email, $nama);

        // Email dalam format HTML
        $mail->isHTML(true);

        // Menentukan subjek email berdasarkan jenis perubahan
        if (!empty($penambahan) && empty($penghapusan)) {
            $subject = "Penambahan Jadwal Ronda";
        } elseif (empty($penambahan) && !empty($penghapusan)) {
            $subject = "Penghapusan Jadwal Ronda";
        } else {
            $subject = "Perubahan Jadwal Ronda";
        }

        $mail->Subject = $subject;
        $mail->Body    = $body;

        // Kirim email
        $mail->send();

        return true;

    } catch (Exception $e) {
        // Catat error jika pengiriman gagal
        error_log("Email gagal ke $email : " . $e->getMessage());
        return false;
    }
}
