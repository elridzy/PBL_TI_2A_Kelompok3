# ⚡ sekilasrv - Website Promosi Jasa Editing

**sekilasrv** adalah platform web promosi dan penghubung antara **Customer** dan **Editor** profesional (Project-Based Learning / PBL Semester 3). Platform ini memudahkan pengguna mencari layanan editing video, desain grafis, animasi, retouching foto, dan langsung terhubung dengan editor via WhatsApp tanpa perantara.

---

## 🚀 Fitur Utama

### 1. 👥 Pemilihan Peran (Beranda)
- Tampilan landing page interaktif bergaya *modern dark theme*.
- Navigasi peran: **Customer** (eksplorasi jasa) dan **Editor** (manajemen jasa).

### 2. 🛍️ Modul Customer
- **Katalog Layanan Jasa**: Tampilan grid kartu jasa yang responsif dan modern.
- **Filter Kategori**: Menyaring jasa berdasarkan kategori (Video Editing, Desain Grafis, Animasi, dll.).
- **Pencarian Real-Time**: Cari jasa berdasarkan kata kunci nama atau deskripsi.
- **Detail Jasa & Galeri Portofolio**: Tampilan showcase karya/portofolio, rincian benefit, estimasi harga, dan tanggal upload.
- **Direct WhatsApp Order**: Tombol order langsung membuka chat WhatsApp ke editor bersangkutan dengan format pesan otomatis mencantumkan nama jasa dan harga.

### 3. ✍️ Modul Editor
- **Login PIN Editor**: Sistem autentikasi berbasis PIN yang cepat dan aman.
- **Dashboard Statistik**: Memantau jumlah total jasa, kategori, dan portofolio yang aktif.
- **Kelola Layanan Jasa (CRUD)**:
  - Tambah jasa baru lengkap dengan kategori, harga, deskripsi, dan upload gambar portofolio.
  - Edit informasi jasa dan ganti gambar portofolio.
  - Hapus layanan jasa (otomatis menghapus file portofolio terkait).
- **Kelola Kategori Jasa (CRUD)**: Tambah, edit, dan hapus kategori layanan editing.
- **Keamanan Sesi & Logout**: Proteksi halaman dashboard jika belum login.

---

## 🛠️ Teknologi yang Digunakan

- **Backend**: PHP 8.3+ (Native dengan PDO PostgreSQL)
- **Database**: PostgreSQL 15+
- **Frontend**: HTML5, Vanilla CSS3 (Custom Dark Theme, Glassmorphism, Google Font Poppins)
- **Web Server**: PHP Built-in Server atau Laragon / Apache

---

## 📁 Struktur Direktori

```text
sekilasrv/
├── index.php                          # Entry point utama (redirect ke beranda)
├── README.md                          # Dokumentasi panduan project
├── beranda/
│   ├── beranda.php                    # Halaman pemilihan peran (Customer / Editor)
│   └── beranda.css                    # Styling halaman beranda
├── customer/
│   ├── informasi/
│   │   ├── kategori_jasa.php          # Katalog layanan & filter kategori
│   │   └── detail_jasa.php            # Rincian jasa, portofolio & CTA WhatsApp
│   └── tampilan/
│       ├── kategori_jasa.css          # Styling halaman katalog
│       └── detail_jasa.css            # Styling halaman detail jasa
├── database/
│   ├── koneksi_psql.php               # Konfigurasi koneksi PDO PostgreSQL
│   ├── schema.sql                     # Skema tabel lengkap + data awal (seed)
│   ├── detail_jasa.sql                # Tabel detail_jasa
│   ├── editor.sql                     # Tabel editor
│   ├── kategori_jasa.sql              # Tabel kategori_jasa
│   └── portofolio.sql                 # Tabel portofolio
├── editor/
│   ├── dashboard.php                  # Dashboard admin editor
│   ├── auth/
│   │   ├── login.php                  # Form input PIN editor
│   │   ├── proses_login.php           # Verifikasi PIN & set session
│   │   ├── auth_check.php             # Middleware pengaman sesi login
│   │   └── logout.php                 # Hapus sesi & keluar
│   ├── detail_jasa/
│   │   ├── tambah.php                 # Form tambah jasa baru
│   │   ├── proses_tambah.php          # Simpan jasa baru & upload gambar
│   │   ├── edit.php                   # Form edit jasa
│   │   ├── proses_edit.php            # Simpan perubahan jasa
│   │   └── proses_hapus.php           # Hapus jasa & file fisik portofolio
│   ├── kategori_jasa/
│   │   ├── tambah.php                 # Form tambah kategori
│   │   ├── proses_tambah.php          # Simpan kategori baru
│   │   ├── edit.php                   # Form edit kategori
│   │   ├── proses_edit.php            # Simpan perubahan kategori
│   │   └── proses_hapus.php           # Hapus kategori
│   └── tampilan/
│       ├── dashboard.css              # Styling dashboard editor
│       ├── login.css                  # Styling halaman login
│       ├── detail_jasa.css            # Styling form detail jasa
│       └── kategori_jasa.css          # Styling form kategori jasa
└── uploads/                           # Direktori penyimpanan media portofolio
```

---

## ⚙️ Cara Menjalankan Project

### Langkah 1: Persiapan Database PostgreSQL

1. Buka **pgAdmin**, **DBeaver**, atau terminal **psql**.
2. Buat database baru bernama:
   ```sql
   CREATE DATABASE db_sekilasrv;
   ```
3. Import file skema dan data awal yang telah disediakan di `database/schema.sql`:
   - Jika via psql:
     ```bash
     psql -U postgres -d db_sekilasrv -f "database/schema.sql"
     ```
   - Atau buka file `database/schema.sql` di Query Tool pgAdmin / DBeaver, lalu jalankan (**Execute/F5**).

4. **Konfigurasi Password Database:**
   Buka file `database/koneksi_psql.php`, lalu sesuaikan password user PostgreSQL Anda:
   ```php
   $host = "localhost";
   $port = "5432";
   $dbname = "db_sekilasrv";
   $user = "postgres";
   $password = "password_postgres_anda"; // Sesuaikan di sini
   ```

---

### Langkah 2: Menjalankan Server

Pilih salah satu metode berikut:

#### Opsi A: Menggunakan PHP Built-in Server (Direkomendasikan)
Buka terminal (PowerShell / Command Prompt) di folder project `sekilasrv`:

```powershell
# Jalankan PHP bawaan Laragon atau PHP sistem Anda
& "D:\laragon\bin\php\php-8.3.33-Win32-vs16-x64\php.exe" -S localhost:8000
```
*(Atau jika `php` sudah terdaftar di PATH: cukup ketik `php -S localhost:8000`)*.

Buka browser dan kunjungi:
👉 **http://localhost:8000/**

---

#### Opsi B: Menggunakan Laragon (Apache)
1. Buka aplikasi **Laragon**.
2. Pastikan project berada di `D:\laragon\www\sekilasrv` atau gunakan Symbolic Link:
   ```powershell
   New-Item -ItemType SymbolicLink -Path "D:\laragon\www\sekilasrv" -Target "D:\SEMESTER 3\PBL\sekilasrv"
   ```
3. Klik **Start All** di Laragon.
4. Buka browser dan akses:
   👉 **http://localhost/sekilasrv/** atau **http://sekilasrv.test/**

---

## 🔑 Kredensial Pengujian (Demo Akun)

Untuk masuk ke Area Editor (`editor/auth/login.php`), gunakan salah satu PIN default yang sudah terdaftar di `database/schema.sql`:

| Peran | PIN Akses | Nomor WhatsApp Terdaftar |
|---|---|---|
| **Editor 1** | `123456` | 6281234567890 |
| **Editor 2** | `080421` | 6289876543210 |

---

## 📌 Catatan Tambahan

- Upload gambar portofolio tersimpan di folder `uploads/`. Pastikan folder tersebut memiliki izin tulis (*write permission*).
- Jika ada kendala koneksi database, sistem akan menampilkan halaman error diagnostik yang memandu letak konfigurasi yang perlu disesuaikan.
