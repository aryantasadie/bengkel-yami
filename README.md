# Bengkel Yami - Sistem Informasi Manajemen Bengkel

Aplikasi berbasis web untuk manajemen operasional Bengkel Yami, mencakup fitur kasir, inventaris, manajemen karyawan, penggajian (payroll), dan laporan keuangan. Dibangun menggunakan framework Laravel.

## Persyaratan Sistem (System Requirements)
Sebelum menjalankan aplikasi ini, pastikan komputer/server Anda telah menginstal perangkat lunak berikut:
1. **PHP** (Minimal versi 8.1 atau terbaru)
2. **Composer** (Untuk manajemen dependensi PHP)
3. **Node.js & NPM** (Untuk kompilasi aset Frontend/Tailwind)
4. **MySQL / MariaDB** (Database server, bisa menggunakan XAMPP/Laragon)
5. **Git** (Opsional)

## Cara Eksekusi / Instalasi (Manual Penggunaan)

Silakan ikuti langkah-langkah di bawah ini secara berurutan untuk menjalankan aplikasi di komputer lokal (Localhost):

### 1. Ekstrak File Source Code
Jika Anda menerima file ini dalam bentuk `.zip` atau `.rar`, silakan ekstrak terlebih dahulu ke dalam folder lokal (misalnya di `C:\xampp\htdocs\bengkel-yami`).

### 2. Install Dependensi PHP (Composer)
Buka Terminal atau Command Prompt, arahkan direktori ke dalam folder project (`cd bengkel-yami`), lalu jalankan perintah berikut:
```bash
composer install
```
*(Catatan: Proses ini membutuhkan koneksi internet untuk mengunduh library Laravel).*

### 3. Install Dependensi Frontend (NPM)
Setelah composer selesai, jalankan perintah berikut untuk menginstal library tampilan (CSS/JS):
```bash
npm install
```

### 4. Konfigurasi Environment (.env)
1. Salin file `.env.example` dan ubah namanya menjadi `.env`.
   ```bash
   copy .env.example .env
   ```
2. Buka file `.env` menggunakan teks editor (Notepad/VS Code).
3. Sesuaikan konfigurasi database Anda pada baris berikut:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=bengkel_yami_database
   DB_USERNAME=root
   DB_PASSWORD=
   ```
   *(Biarkan `DB_PASSWORD` kosong jika Anda menggunakan XAMPP default).*

### 5. Generate Application Key
Jalankan perintah ini di terminal untuk menghasilkan kunci keamanan aplikasi:
```bash
php artisan key:generate
```

### 6. Migrasi Database dan Seeder
Pastikan Anda sudah membuat database kosong di phpMyAdmin (atau biarkan Laravel membuatnya). Lalu jalankan perintah ini untuk membangun struktur tabel beserta data awal (akun dummy):
```bash
php artisan migrate:fresh --seed
```
*(Jika muncul pertanyaan konfirmasi pembuatan database, ketik `yes`)*.

### 7. Build Aset Frontend
Kompilasi file CSS dan JavaScript agar tampilan website muncul dengan sempurna:
```bash
npm run build
```

### 8. Jalankan Server Lokal
Langkah terakhir, nyalakan server lokal Laravel dengan perintah:
```bash
php artisan serve
```
Aplikasi sekarang dapat diakses melalui browser di alamat: **http://127.0.0.1:8000**

---

## Akun Login Default (Dummy Data)
Gunakan akun berikut untuk masuk ke dalam sistem:

**1. Akun Admin (Kasir/CS)**
* Username: `admin`
* Password: `password`

**2. Akun Owner (Pemilik)**
* Username: `owner`
* Password: `password`

*(Catatan: Jika login menggunakan NIK, silakan cek tabel `karyawan` dan `users` di database untuk melihat daftar NIK yang digenerate oleh Seeder).*

---
*Dibuat untuk memenuhi Tugas Laporan Proyek Bengkel Yami.*
