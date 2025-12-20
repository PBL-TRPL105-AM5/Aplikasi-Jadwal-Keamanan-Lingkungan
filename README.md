# Aplikasi Jadwal Keamanan Lingkungan

Aplikasi berbasis web yang digunakan untuk mengelola jadwal ronda keamanan lingkungan, presensi petugas, pencatatan insiden keamanan, serta notifikasi perubahan jadwal melalui email.

Aplikasi ini membantu pengelolaan keamanan lingkungan agar lebih terstruktur, terdokumentasi, dan mudah digunakan oleh setiap peran pengguna.

---

## 🎯 Fitur Utama
- Manajemen jadwal ronda (harian dan mingguan)
- Notifikasi perubahan jadwal melalui email
- Presensi ronda petugas
- Pencatatan insiden keamanan
- Multi-role pengguna:
  - Admin
  - Koordinator
  - Petugas

---

## 🛠️ Teknologi yang Digunakan
- PHP (Native)
- MySQL
- Bootstrap
- PHPMailer
- Composer

---

## ⚙️ Instalasi

1. Clone repository ke dalam folder web server:
   ```bash
   git clone https://github.com/username/Aplikasi-Jadwal-Keamanan-Lingkungan.git
2. Masuk ke folder project:
   ```bash
   cd Aplikasi-Jadwal-Keamanan-Lingkungan
3. Salin file konfigurasi contoh:
   ```bash
   cp config/config.example.php config/config.php
   cp config/email_config.example.php config/email_config.php
4. Konfigurasi database pada file `config/config.php`:
    - Host database
    - Username database
    - Password database
    - Nama database
6. Konfigurasi email pada file `config/email_config.php`:
   - Email pengirim (SMTP)
   - App password email

7. Import database:
   - Gunakan file SQL yang tersedia pada folder:
     ```text
     Database/db_ronda.sql
     ```

8. Install dependency menggunakan Composer:
   ```bash
   composer install
9. Jalankan aplikasi melalui browser:
   - Akses aplikasi melalui alamat:
     ```text
     http://localhost/nama-folder-project/
     ```

---

## 🔑 Akun Demo

Gunakan akun berikut untuk mencoba aplikasi:

### Admin:
  - Email: `admin@ronda.com`
  - Password: `admin12`

### Koordinator:
  - Email: `koordinator@ronda.com`
  - Password: `koordinator123`

---

🔐 Keamanan

  - Password disimpan menggunakan password_hash
  - Proses login menggunakan query aman (anti SQL Injection)
  - File konfigurasi sensitif tidak disertakan dalam repository GitHub

