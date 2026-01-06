<?php
// Memanggil file konfigurasi (koneksi database, session_start, dll)
include 'config/config.php';

// Cek apakah user sudah login
// Jika sudah ada session user, langsung arahkan ke dashboard
if (isset($_SESSION['user'])) {
    header("Location: dashboard/index.php");
    exit;
}

// Variabel untuk menyimpan pesan error / alert
$alert = "";

// Mengecek apakah form dikirim menggunakan metode POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Mengambil input email
    $email = $_POST['email'];

    // Mengambil input password
    $password = $_POST['password'];

    // Query untuk mengambil data user berdasarkan email
    $sql = "SELECT id_pengguna, nama_pengguna, email, password, role 
            FROM tb_pengguna 
            WHERE email = '$email'";

    // Menjalankan query ke database
    $result = mysqli_query($conn, $sql);

    // Cek apakah email ditemukan tepat 1 data
if (mysqli_num_rows($result) == 1) {

    // Ambil data user mengambil satu baris data hasil query MySQL dalam bentuk array asosiatif.
    $user = mysqli_fetch_assoc($result);

    // Verifikasi password
    if (password_verify($password, $user['password'])) {

        // Simpan data user ke session
        $_SESSION['user'] = [
            'id_pengguna'   => $user['id_pengguna'],
            'nama_pengguna' => $user['nama_pengguna'],
            'email'         => $user['email'],
            'role'          => $user['role']
        ];

        // Redirect ke dashboard
        header("Location: dashboard/index.php");
        exit;
    }
}

// Jika email tidak ditemukan ATAU password salah
$alert = "Email atau password salah!";

}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login - Siskamling</title>

<link rel="stylesheet" href="assets/css/bootstrap.min.css">
<link rel="stylesheet" href="assets/fontawesome/css/all.min.css">
<link rel="stylesheet" href="assets/css/login.css">

</head>
<body>

<div class="login-box">
    <div class="login-left">
        <img src="assets/img/icon_login.png" alt="Login Icon">
    </div>

    <div class="login-right">
        <h3>LOGIN</h3>

        <?php if ($alert != ""): ?>
            <div class="alert alert-danger py-2"><?= $alert ?></div>
        <?php endif; ?>

        <form method="POST">

            <!-- EMAIL -->
            <div class="input-group mb-3">
                <span class="input-group-text">
                    <i class="fas fa-envelope"></i>
                </span>
                <input type="email" name="email" class="form-control" placeholder="Email" required>
            </div>

            <!-- PASSWORD -->
            <div class="input-group mb-3">
                <span class="input-group-text">
                    <i class="fas fa-lock"></i>
                </span>

                <input type="password"
                    name="password"
                    id="password"
                    class="form-control"
                    placeholder="Password"
                    required>

                <span class="input-group-text" style="cursor:pointer;" onclick="toggleLoginPass()">
                    <i class="fas fa-eye" id="toggleIcon"></i>
                </span>
            </div>



            <button type="submit" class="btn-login">LOGIN</button>

            <div class="text-center mt-3">
                <a href="landing_page.php" class="text-light text-decoration-none" style="font-size:13px;opacity:.8">
                    ← Kembali ke beranda
                </a>
            </div>


        </form>
    </div>
</div>

<script src="assets/js/bootstrap.bundle.min.js"></script>

<script>
function toggleLoginPass() {
    const pass = document.getElementById("password");
    const icon = document.getElementById("toggleIcon");

    if (pass.type === "password") {
        pass.type = "text";
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
    } else {
        pass.type = "password";
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
    }
}
</script>


</body>
</html>
