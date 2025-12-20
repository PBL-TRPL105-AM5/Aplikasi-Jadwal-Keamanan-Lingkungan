<?php
include 'config/config.php';

if (isset($_SESSION['user'])) {
    header("Location: dashboard/index.php");
    exit;
}

$alert = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // === PREPARED STATEMENT ANTI SQL INJECTION ===
    $stmt = $conn->prepare("SELECT * FROM tb_pengguna WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();

    // cek apakah email ditemukan
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // verifikasi password hash
        if (password_verify($password, $user['password'])) {

            $_SESSION['user'] = [
                'id_pengguna'   => $user['id_pengguna'],
                'nama_pengguna' => $user['nama_pengguna'],
                'email'         => $user['email'],
                'role'          => $user['role']
            ];

            header("Location: dashboard/index.php");
            exit;

        } else {
            $alert = "Password salah!";
        }
    } else {
        $alert = "Email tidak ditemukan!";
    }

    $stmt->close();
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

<style>
    body {
        margin: 0;
        padding: 0;
        height: 100vh;
        background: url('assets/img/bg_login.png') no-repeat center center/cover;
        display: flex;
        justify-content: center;
        align-items: center;
        font-family: "Segoe UI", sans-serif;
    }

    .login-box {
        width: 780px;
        min-height: 380px;
        display: flex;
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(14px);
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0px 8px 35px rgba(0,0,0,0.35);
    }


    .login-left {
        width: 45%;
        background: #eef0ff;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .login-left img {
        width: 85%;
    }

    .login-right {
        width: 55%;
        padding: 38px;
        color: white;
    }

    .login-right h3 {
        font-weight: 700;
        margin-bottom: 28px;
    }

    .input-group {
        position: relative;   
    }

    /* FIX TOTAL INPUT TRANSPARAN */
    .input-group-text {
        background: rgba(255,255,255,0.20) !important;
        border: 1px solid rgba(255,255,255,0.35) !important;
        border-right: none !important;
        color: #fff !important;
        width: 45px;
        display: flex;
        justify-content: center;
    }

    .form-control {
        background: rgba(255,255,255,0.20) !important;
        border: 1px solid rgba(255,255,255,0.35) !important;
        border-left: none !important;
        color: white !important;
        height: 42px !important;
        box-shadow: none !important;
    }

    .form-control::placeholder {
        color: #e4e4e4 !important;
        opacity: .9 !important;
    }

    .form-control:focus {
        background: rgba(255,255,255,0.27) !important;
        border-color: rgba(255,255,255,0.60) !important;
        color: #fff !important;
        box-shadow: none !important;
    }

    /* EYE TOGGLE */
    .toggle-password {
        position: absolute;
        right: 12px;
        top: 12.5px;
        z-index: 20;
        cursor: pointer;
        color: #fff;
    }

    .form-control {
        position: relative;
        z-index: 10; 
    }

    .toggle-password:hover {
        opacity: 1;
    }

    /* BUTTON */
    .btn-login {
        width: 100%;
        border: none;
        padding: 10px;
        border-radius: 6px;
        font-weight: bold;
        background: linear-gradient(90deg, #3152ff, #4e68ff);
        color: white;
        margin-top: 12px;
        transition: 0.2s;
    }
</style>
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

        <form method="POST" autocomplete="off">

            <!-- EMAIL -->
            <div class="input-group mb-3">
                <span class="input-group-text">
                    <i class="fas fa-envelope"></i>
                </span>
                <input type="email" name="email" class="form-control" placeholder="Email" required autocomplete="off">
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



            <button class="btn-login">LOGIN</button>
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
