<?php
include 'config/config.php';

if (isset($_SESSION['user'])) {
    header("Location: dashboard/index.php");
    exit;
}

$alert = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Ambil input & trim
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    // Prepared Statement
    $stmt = mysqli_prepare(
        $conn,
        "SELECT id_pengguna, nama_pengguna, email, password, role, is_first_login
        FROM tb_pengguna 
        WHERE email = ? LIMIT 1
        "
    );

    // Bind parameter
    mysqli_stmt_bind_param($stmt, "s", $email);

    // Eksekusi
    mysqli_stmt_execute($stmt);

    // Ambil hasil
    $result = mysqli_stmt_get_result($stmt);

    if ($result && mysqli_num_rows($result) === 1) {

        $user = mysqli_fetch_assoc($result);

        // Verifikasi password hash
        if (password_verify($password, $user['password'])) {

            $_SESSION['user'] = [
                'id_pengguna'     => $user['id_pengguna'],
                'nama_pengguna'   => $user['nama_pengguna'],
                'email'           => $user['email'],
                'role'            => $user['role'],
                'is_first_login'  => $user['is_first_login']
            ];


            header("Location: dashboard/index.php");
            exit;
        }
    }

    // Jika gagal login
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
