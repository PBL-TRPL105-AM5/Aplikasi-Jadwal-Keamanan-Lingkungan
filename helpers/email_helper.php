<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Load PHPMailer secara aman (tidak akan fatal error)
 */
function loadMailerSafe()
{
    try {
        require_once __DIR__ . '/../assets/vendor/autoload.php';
        return true;
    } catch (Throwable $e) {
        error_log("PHPMailer gagal diload: " . $e->getMessage());
        return false;
    }
}

/**
 * Format tanggal menjadi HTML list
 * $strike = true → jadwal dicoret (untuk penghapusan)
 */
function formatTanggalList($tanggalList, $withTime = true, $strike = false)
{
    if (empty($tanggalList)) return "";

    $out = "";
    foreach ($tanggalList as $tgl) {
        $dt = new DateTime($tgl);
        $hari = $dt->format('l');
        $tanggal = $dt->format('d F Y');

        $jam = $withTime ? " (21:00 - 05:00)" : "";
        $text = "{$hari}, {$tanggal}{$jam}";

        if ($strike) {
            $text = "<span style='text-decoration: line-through;'>{$text}</span>";
        }

        $out .= "• {$text}<br>";
    }
    return $out;
}

/**
 * Kirim email perubahan jadwal ronda:
 * - Penambahan jadwal
 * - Penghapusan jadwal
 */
function kirimEmailPerubahanJadwal($nama, $email, $penambahan, $penghapusan)
{
    require __DIR__ . '/../config/email_config.php';

    // Tidak ada perubahan → tidak kirim email
    if (empty($penambahan) && empty($penghapusan)) {
        return false;
    }

    // Pastikan PHPMailer siap
    if (!loadMailerSafe()) {
        return false;
    }

    // Format daftar tanggal
    $htmlAdd = formatTanggalList($penambahan, true, false); // tidak dicoret
    $htmlDel = formatTanggalList($penghapusan, false, true); // dicoret & tanpa jam

    // Template email formal
    $body = "
        <p>Yth. Bapak/Ibu <b>{$nama}</b>,</p>
        <p>Terdapat perubahan pada jadwal ronda Anda. Berikut adalah rinciannya:</p>
    ";

    if (!empty($htmlAdd)) {
        $body .= "
            <p><b>Penambahan Jadwal:</b><br>
            {$htmlAdd}</p>
        ";
    }

    if (!empty($htmlDel)) {
        $body .= "
            <p><b>Penghapusan Jadwal:</b><br>
            {$htmlDel}</p>
        ";
    }

    $body .= "
        <p>Mohon menyesuaikan dengan jadwal terbaru tersebut.</p>
        <br>
        <p>Hormat kami,<br>
        <b>Sistem Informasi Ronda</b></p>
    ";

    // === Kirim email ===
    try {
        $mail = new PHPMailer(true);

        $mail->isSMTP();
        $mail->Host       = $MAIL_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = $MAIL_USERNAME;
        $mail->Password   = $MAIL_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom($MAIL_USERNAME, 'Sistem Ronda');
        $mail->addAddress($email, $nama);

        $mail->isHTML(true);

        // Tentukan judul email
        if (!empty($penambahan) && empty($penghapusan)) {
            $subject = "Penambahan Jadwal Ronda";
        } elseif (empty($penambahan) && !empty($penghapusan)) {
            $subject = "Penghapusan Jadwal Ronda";
        } else {
            $subject = "Perubahan Jadwal Ronda";
        }

        $mail->Subject = $subject;
        $mail->Body    = $body;

        $mail->send();

        return true;

    } catch (Exception $e) {
        error_log("Email gagal ke $email : " . $e->getMessage());
        return false;
    }
}
