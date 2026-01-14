        </div> <!-- end mainContent -->
    </div> <!-- end row -->
</div> <!-- end container-fluid -->
<footer class="fixed-bottom bg-light text-center py-2 small text-muted">
    © <?= date('Y') ?> PBL-TRPL105-AM5 — Aplikasi Jadwal Keamanan Lingkungan
</footer>




<!-- Load JavaScript Bootstrap -->
<script src="../assets/js/bootstrap.bundle.min.js"></script>

<?php
if (
    isset($_SESSION['user']) &&
    $_SESSION['user']['is_first_login'] == 1 &&
    empty($_SESSION['first_login_modal_shown'])
):
?>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const modalEl = document.getElementById('modalFirstLogin');
    if (modalEl) {
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
    }
});
</script>
<?php
$_SESSION['first_login_modal_shown'] = true;
endif;
?>


<script>
// Fungsi untuk menampilkan / menyembunyikan password
// Digunakan pada form ganti password
function togglePass(id, el) {

    // Ambil input password berdasarkan id
    const input = document.getElementById(id);
    if (!input || !el) return;

    // Ambil icon di dalam tombol
    const icon = el.querySelector('i');
    if (!icon) return;

    // Jika password masih tersembunyi, tampilkan
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('bi-eye-fill');
        icon.classList.add('bi-eye-slash-fill');
    } 
    // Jika sudah terlihat, sembunyikan kembali
    else {
        input.type = 'password';
        icon.classList.remove('bi-eye-slash-fill');
        icon.classList.add('bi-eye-fill');
    }
}

// Fungsi untuk menampilkan jam dan tanggal realtime di navbar
function updateTanggalJamNavbar() {
    const now = new Date();

    const options = {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    };

    const tanggal = now.toLocaleDateString('id-ID', options);
    const jam = now.toLocaleTimeString('id-ID');

    const el = document.getElementById('tanggalJamNavbar');
    if (el) {
        el.innerHTML = ` ${tanggal}, ${jam}`;
    }
}

// Set awal
updateTanggalJamNavbar();

// Update tiap 1 detik
setInterval(updateTanggalJamNavbar, 1000);
</script>

</body>
</html>
