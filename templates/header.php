<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title><?= $page_title ?? "Siskamling" ?></title>

    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="../assets/css/custom.css" rel="stylesheet">
    <link href="../assets/fontawesome/css/all.min.css" rel="stylesheet">
</head>
<body>
<?php if (isset($_SESSION['user']) && $_SESSION['user']['is_first_login'] == 1): ?>
<div class="modal fade" id="modalFirstLogin" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-shield-lock"></i> Keamanan Akun
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <p class="mb-2">
                    Anda masih menggunakan password awal.
                </p>
                <p class="small text-muted mb-0">
                    Demi keamanan akun, silakan ganti password Anda.
                </p>
            </div>

            <div class="modal-footer">
                <button type="button"
                        class="btn btn-light btn-sm"
                        data-bs-dismiss="modal">
                    Nanti
                </button>

                <a href="<?= $base_url ?>profil/index.php"
                   class="btn btn-primary btn-sm">
                    Ganti Password
                </a>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
