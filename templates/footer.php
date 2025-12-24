        </div> <!-- end mainContent -->
    </div> <!-- end row -->
</div> <!-- end container-fluid -->
<footer class="fixed-bottom bg-light text-center py-2 small text-muted">
    © <?= date('Y') ?> PBL-TRPL105-AM5 — Aplikasi Jadwal Keamanan Lingkungan
</footer>




<!-- Load JavaScript Bootstrap -->
<script src="../assets/js/bootstrap.bundle.min.js"></script>

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
function updateJam() {

    // Ambil elemen jam
    const jamEl = document.getElementById('tanggalJam');
    if (!jamEl) return;

    // Ambil waktu saat ini
    const now = new Date();

    // Format hari (Indonesia)
    const hari = now.toLocaleDateString('id-ID', { weekday: 'long' });

    // Format tanggal
    const tanggal = now.toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric'
    });

    // Format jam
    const jam = now.toLocaleTimeString('id-ID', {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    });

    // Tampilkan ke halaman
    jamEl.innerText = `${hari}, ${tanggal} | ${jam} WIB`;
}

// Jalankan jam pertama kali
updateJam();

// Update jam setiap 1 detik
setInterval(updateJam, 1000);
</script>

</body>
</html>
