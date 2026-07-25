<div align="center">

<img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="300" alt="Laravel Logo">

# 🗓️ Leave Management System

![Laravel](https://img.shields.io/badge/Laravel_12-%23FF2D20.svg?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP_8.2+-%23777BB4.svg?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1.svg?style=for-the-badge&logo=mysql&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap_5.3-%238511FA.svg?style=for-the-badge&logo=bootstrap&logoColor=white)

**Sistem Manajemen Cuti Karyawan berbasis web dengan 4 role:**
**Employee · Manager · HRD · Admin**

</div>

---

## 📋 Daftar Isi
1. [Fitur Utama](#-fitur-utama)
2. [Alur Kerja Sistem](#-alur-kerja-sistem)
3. [Persyaratan Sistem](#-persyaratan-sistem)
4. [Instalasi](#-instalasi)
5. [Akun Demo](#-akun-demo)
6. [Struktur Folder](#-struktur-folder)
7. [Penjelasan Role & Fitur](#-penjelasan-role--fitur)
8. [Validasi Bisnis](#-validasi-bisnis)
9. [Command Tambahan](#-command-tambahan)
10. [Teknologi](#-teknologi-yang-digunakan)

---

## ✨ Fitur Utama

| Fitur | Deskripsi |
|-------|-----------|
| 🔐 Multi-Role Auth | Employee, Manager, HRD, Admin |
| 📅 Kuota Cuti Tahunan | 12 hari/tahun per karyawan (configurable) |
| 🏥 Cuti Sakit | Tidak terbatas, bisa upload surat dokter |
| ✅ Alur Persetujuan 2-tahap | Manager → HRD (karyawan) / HRD langsung (manager) |
| 📆 Kalkulasi Hari Kerja | Otomatis, tidak menghitung Sabtu & Minggu |
| 🚫 Cek Tumpang Tindih | Tidak bisa ajukan cuti jika ada yang aktif di tanggal sama |
| 📄 Cetak Laporan PDF | HRD bisa download laporan cuti per tahun (landscape A4) |
| 👥 Manajemen Pengguna | Admin dapat CRUD pengguna dan atur kuota |
| 📊 Dashboard Real-time | Statistik cuti per role |

---

## 🖥️ Persyaratan Sistem

- **PHP** >= 8.2
- **Composer** >= 2.x
- **MySQL** >= 8.0 (atau MariaDB >= 10.6)
- **Web Server**: Apache (XAMPP/Laragon) atau Nginx
- **Extension PHP**: pdo_mysql, mbstring, openssl, tokenizer, xml, fileinfo

---

## 🚀 Instalasi

**1. Clone repository**
```bash
git clone https://github.com/DausssWeb/cuti_app.git
cd cuti_app
```

**2. Install dependencies**
```bash
composer install
```

**3. Setup environment**
```bash
cp .env.example .env
php artisan key:generate
```

**4. Konfigurasi database** — edit file `.env`:
```env
APP_NAME="Leave Management System"
APP_URL=http://localhost/cuti_app/public

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=leave_management
DB_USERNAME=root
DB_PASSWORD=
```

**5. Buat database**

Buka **phpMyAdmin** → buat database baru bernama `leave_management`

**6. Migrasi & Seeder**
```bash
php artisan migrate --seed
```

**7. Storage link**
```bash
php artisan storage:link
```

**8. Jalankan aplikasi**
```bash
# Dengan artisan
php artisan serve
# Akses: http://127.0.0.1:8000

# Dengan XAMPP/Laragon
# Akses: http://localhost/cuti_app/public
```

---

## 👤 Akun Demo

| Role | Email | Password |
|------|-------|----------|
| 🔧 Admin | admin@leavemgmt.com | password |
| 👩‍💼 HRD | hrd@leavemgmt.com | password |
| 🧑‍💼 Manager | manager@leavemgmt.com | password |
| 👷 Employee 1 | employee@leavemgmt.com | password |
| 👷 Employee 2 | dewi@leavemgmt.com | password |
| 👷 Employee 3 | raka@leavemgmt.com | password |

---

## 🎯 Penjelasan Role & Fitur

### 👷 Employee
- ✅ Dashboard dengan info kuota cuti real-time
- ✅ Ajukan Cuti Tahunan (maks 12 hari/tahun)
- ✅ Ajukan Cuti Sakit (tidak terbatas, opsional surat dokter)
- ✅ Lihat riwayat & timeline approval
- ❌ Tidak bisa melihat data karyawan lain

### 🧑‍💼 Manager
- ✅ Melihat & approve/reject cuti semua karyawan
- ✅ Wajib isi catatan saat menolak
- ✅ Mengajukan cuti sendiri (langsung ke HRD)
- ❌ Tidak bisa approve cuti sesama manager

### 👩‍💼 HRD
- ✅ Melihat semua pengajuan dengan filter
- ✅ Approve final cuti karyawan (setelah manager)
- ✅ Approve cuti manager langsung
- ✅ Generate laporan PDF per tahun

### 🔧 Admin
- ✅ CRUD pengguna (employee, manager, HRD)
- ✅ Atur kuota cuti tahunan per pengguna
- ✅ Aktifkan / nonaktifkan akun
- ✅ Reset password pengguna

---

## ✅ Validasi Bisnis

| Validasi | Keterangan |
|----------|------------|
| Kuota tahunan | Tidak bisa ajukan jika sisa kuota kurang |
| Tanggal mulai | Tidak boleh sebelum hari ini |
| Hari kerja | Sabtu & Minggu tidak dihitung, min. 1 hari kerja |
| Tumpang tindih | Cek cuti aktif di rentang tanggal sama |
| Alasan | Min. 10 karakter, maks. 500 karakter |
| Surat dokter | Format PDF/JPG/PNG, maks. 2MB |
| Role access | Setiap role hanya akses menu yang sesuai |
| Catatan tolak | Wajib isi catatan ketika menolak |

---

## 🔧 Command Tambahan

```bash
# Buat kuota cuti tahunan untuk semua user
php artisan app:create-annual-quotas
php artisan app:create-annual-quotas 2026

# Bersihkan cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Reset database & seed ulang
php artisan migrate:fresh --seed
```

---

## 🎨 Teknologi yang Digunakan

- **Laravel 12** — PHP Framework
- **Bootstrap 5.3** — CSS Framework
- **Bootstrap Icons 1.11** — Icon library
- **barryvdh/laravel-dompdf** — Generate PDF
- **Inter Font** — Typography
- **MySQL** — Database

---

## 👤 Author

<div align="center">

**Faisal Rahmat Firdaus**

[![GitHub](https://img.shields.io/badge/GitHub-%23121011.svg?style=for-the-badge&logo=github&logoColor=white)](https://github.com/DausssWeb)
[![Instagram](https://img.shields.io/badge/Instagram-%23E4405F.svg?style=for-the-badge&logo=Instagram&logoColor=white)](https://instagram.com/rhmtfrdus._)
[![LinkedIn](https://img.shields.io/badge/LinkedIn-%230077B5.svg?style=for-the-badge&logo=linkedin&logoColor=white)](https://linkedin.com/in/faisal-rahmat-firdaus-453959207)

</div>

---

<div align="center">

*Dibuat untuk keperluan pembelajaran Laravel 12 — STMIK Mardira Indonesia* 🎓

⭐ Jangan lupa kasih star kalau project ini bermanfaat!

</div>
