        </div> <!-- end mainContent -->
    </div> <!-- end row -->
</div> <!-- end container-fluid -->

<!-- ================= SCRIPT ================= -->
<script src="../assets/js/bootstrap.bundle.min.js"></script>

<script>
// ================= TOGGLE PASSWORD =================
function togglePass(id, el) {
    const input = document.getElementById(id);
    if (!input || !el) return;

    const icon = el.querySelector('i');
    if (!icon) return;

    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('bi-eye-fill');
        icon.classList.add('bi-eye-slash-fill');
    } else {
        input.type = 'password';
        icon.classList.remove('bi-eye-slash-fill');
        icon.classList.add('bi-eye-fill');
    }
}

// ================= JAM REALTIME =================
function updateJam() {
    const jamEl = document.getElementById('tanggalJam');
    if (!jamEl) return; // aman kalau elemen tidak ada

    const now = new Date();

    const hari = now.toLocaleDateString('id-ID', { weekday: 'long' });
    const tanggal = now.toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric'
    });

    const jam = now.toLocaleTimeString('id-ID', {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    });

    jamEl.innerText = `${hari}, ${tanggal} | ${jam} WIB`;
}

// Jalankan jam
updateJam();
setInterval(updateJam, 1000);
</script>

</body>
</html>
