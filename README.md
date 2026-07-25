# 🗓️ Leave Management System — Laravel 12

Sistem Manajemen Cuti Karyawan berbasis web menggunakan **Laravel 12** dengan 4 role:
**Employee**, **Manager**, **HRD**, dan **Admin**.

---

## 📋 Daftar Isi
1. [Fitur Utama](#fitur-utama)
2. [Alur Kerja Sistem](#alur-kerja-sistem)
3. [Persyaratan Sistem](#persyaratan-sistem)
4. [Instalasi](#instalasi)
5. [Akun Demo](#akun-demo)
6. [Struktur Folder](#struktur-folder)
7. [Penjelasan Role & Fitur](#penjelasan-role--fitur)
8. [Validasi Bisnis](#validasi-bisnis)

---

## ✨ Fitur Utama

| Fitur | Deskripsi |
|-------|-----------|
| Multi-Role Auth | Employee, Manager, HRD, Admin |
| Kuota Cuti Tahunan | 12 hari/tahun per karyawan (configurable) |
| Cuti Sakit | Tidak terbatas, dapat upload surat dokter |
| Alur Persetujuan 2-tahap | Manager → HRD (untuk karyawan) / HRD langsung (untuk manager) |
| Kalkulasi Hari Kerja | Otomatis, tidak menghitung Sabtu & Minggu |
| Cek Tumpang Tindih | Tidak bisa ajukan cuti jika ada yang aktif di tanggal sama |
| Cetak Laporan PDF | HRD bisa download laporan cuti per tahun (landscape A4) |
| Manajemen Pengguna | Admin dapat CRUD pengguna dan atur kuota |
| Dashboard Real-time | Statistik cuti per role |

---

## 🔄 Alur Kerja Sistem

```
EMPLOYEE:
  Ajukan Cuti → Pending → [Manager: Setuju/Tolak]
                              ↓ Setuju
                          Manager Approved → [HRD: Setuju/Tolak]
                                                 ↓ Setuju
                                             HRD Approved ✅

MANAGER:
  Ajukan Cuti → Pending → [HRD: Setuju/Tolak] → HRD Approved ✅
  (Tanpa perlu persetujuan manager lain)

HRD:
  - Menyetujui final cuti karyawan (setelah manager)
  - Menyetujui cuti manager (langsung)
  - Mencetak laporan PDF tahunan

ADMIN:
  - Mengelola pengguna (CRUD)
  - Mengatur kuota cuti tahunan
  - Aktif/nonaktifkan akun
```

---

## 🖥️ Persyaratan Sistem

- **PHP** >= 8.2
- **Composer** >= 2.x
- **MySQL** >= 8.0 (atau MariaDB >= 10.6)
- **Web Server**: Apache (XAMPP/Laragon) atau Nginx
- **Extension PHP**: pdo_mysql, mbstring, openssl, tokenizer, xml, fileinfo

---

## 🚀 Instalasi

### Langkah 1 – Clone / Copy Proyek

```bash
# Salin folder app_project ke direktori web Anda
# XAMPP  : C:/xampp/htdocs/app_project
# Laragon: C:/laragon/www/app_project
```

### Langkah 2 – Install Dependensi

```bash
cd app_project
composer install
```

### Langkah 3 – Konfigurasi Environment

```bash
cp .env.example .env
php artisan key:generate
```

Edit file `.env`:

```env
APP_NAME="Leave Management System"
APP_URL=http://localhost/app_project/public

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=leave_management
DB_USERNAME=root
DB_PASSWORD=          # kosongkan jika XAMPP default
```

### Langkah 4 – Buat Database

Buka **phpMyAdmin** → buat database baru bernama `leave_management`

### Langkah 5 – Migrasi & Seeder

```bash
php artisan migrate --seed
```

Perintah ini akan:
- Membuat semua tabel database
- Mengisi data akun demo (admin, HRD, manager, 5 karyawan)
- Membuat kuota cuti awal

### Langkah 6 – Storage Link

```bash
php artisan storage:link
```

### Langkah 7 – Jalankan Server

**Dengan `php artisan serve`:**
```bash
php artisan serve
# Akses: http://127.0.0.1:8000
```

**Dengan XAMPP/Laragon:**
```
http://localhost/app_project/public
```

---

## 👤 Akun Demo

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@leavemgmt.com | password |
| HRD | hrd@leavemgmt.com | password |
| Manager | manager@leavemgmt.com | password |
| Employee | employee@leavemgmt.com | password |
| Employee 2 | dewi@leavemgmt.com | password |
| Employee 3 | raka@leavemgmt.com | password |

---

## 📁 Struktur Folder

```
app_project/
├── app/
│   ├── Console/Commands/
│   │   └── CreateAnnualQuotas.php       # Command auto-buat kuota tahunan
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AuthController.php       # Login & logout
│   │   │   ├── EmployeeController.php   # Dashboard, ajukan & lihat cuti
│   │   │   ├── ManagerController.php    # Approve karyawan + cuti sendiri
│   │   │   ├── HrdController.php        # Approve final + laporan PDF
│   │   │   └── AdminController.php      # CRUD pengguna & kuota
│   │   └── Middleware/
│   │       └── RoleMiddleware.php        # Guard akses per role
│   ├── Models/
│   │   ├── User.php                     # Model pengguna + helper role & kuota
│   │   ├── LeaveApplication.php         # Model pengajuan cuti
│   │   └── LeaveQuota.php               # Model kuota tahunan per user
│   └── Providers/
│       └── AppServiceProvider.php
├── bootstrap/
│   └── app.php                          # Registrasi middleware Laravel 12
├── config/
│   ├── app.php
│   ├── auth.php
│   ├── database.php
│   ├── dompdf.php                       # Konfigurasi PDF
│   ├── filesystems.php
│   └── session.php
├── database/
│   ├── migrations/
│   │   ├── ..._create_users_table.php
│   │   ├── ..._create_leave_quotas_table.php
│   │   └── ..._create_leave_applications_table.php
│   └── seeders/
│       └── DatabaseSeeder.php           # Data demo lengkap
├── public/
│   ├── index.php
│   └── .htaccess
├── resources/views/
│   ├── auth/
│   │   └── login.blade.php              # Halaman login
│   ├── layouts/
│   │   ├── app.blade.php                # Layout utama + sidebar
│   │   └── partials/
│   │       └── sidebar-menu.blade.php   # Menu sidebar per role
│   ├── employee/
│   │   ├── dashboard.blade.php
│   │   ├── index.blade.php              # Riwayat pengajuan
│   │   ├── create.blade.php             # Form ajukan cuti
│   │   └── show.blade.php               # Detail + timeline approval
│   ├── manager/
│   │   ├── dashboard.blade.php
│   │   ├── employee-leaves.blade.php    # Daftar cuti karyawan
│   │   ├── show-employee-leave.blade.php # Review + approve/reject
│   │   ├── my-leaves.blade.php          # Riwayat cuti manager
│   │   └── create-leave.blade.php       # Form cuti manager
│   ├── hrd/
│   │   ├── dashboard.blade.php
│   │   ├── all-leaves.blade.php         # Semua pengajuan + filter
│   │   ├── show-leave.blade.php         # Review + approve/reject HRD
│   │   ├── report-form.blade.php        # Form generate PDF
│   │   └── report-pdf.blade.php         # Template PDF laporan
│   ├── admin/
│   │   ├── dashboard.blade.php
│   │   ├── users.blade.php              # List pengguna
│   │   ├── create-user.blade.php
│   │   └── edit-user.blade.php
│   └── errors/
│       └── 403.blade.php
└── routes/
    ├── web.php                          # Semua route
    └── console.php
```

---

## 🎯 Penjelasan Role & Fitur

### 👷 Employee (Karyawan)
- ✅ Dashboard dengan info kuota cuti real-time
- ✅ Ajukan **Cuti Tahunan** (maks 12 hari/tahun)
- ✅ Ajukan **Cuti Sakit** (tidak terbatas, opsional surat dokter)
- ✅ Lihat riwayat semua pengajuan beserta status
- ✅ Lihat timeline approval (Manager → HRD)
- ❌ Tidak bisa melihat data karyawan lain
- ❌ Tidak bisa approve cuti siapapun

### 🧑‍💼 Manager
- ✅ Dashboard statistik pengajuan karyawan
- ✅ Melihat & menyetujui/menolak pengajuan cuti **semua karyawan (employee)**
- ✅ Wajib isi catatan saat menolak
- ✅ Mengajukan cuti sendiri (langsung ke HRD)
- ✅ Melihat riwayat cuti sendiri
- ❌ Tidak melihat pengajuan sesama manager atau HRD
- ❌ Tidak bisa approve cuti manager lain

### 👩‍💼 HRD
- ✅ Dashboard ringkasan seluruh cuti
- ✅ Melihat **semua** pengajuan (employee + manager) dengan filter
- ✅ Approve/reject cuti karyawan **setelah manager approve**
- ✅ Approve/reject cuti manager **langsung**
- ✅ Saat approve → kuota cuti otomatis dikurangi
- ✅ **Generate laporan PDF** (per tahun, filter jenis & status)
- ✅ PDF berisi: ringkasan per karyawan + detail semua pengajuan

### 🔧 Admin
- ✅ Dashboard statistik pengguna & cuti
- ✅ Tambah/edit pengguna (employee, manager, HRD)
- ✅ Atur **kuota cuti tahunan** per pengguna
- ✅ Aktifkan / nonaktifkan akun
- ✅ Reset password pengguna

---

## ✅ Validasi Bisnis

| Validasi | Keterangan |
|----------|------------|
| Kuota tahunan | Tidak bisa ajukan jika sisa kuota < hari yang diminta |
| Tanggal mulai | Tidak boleh sebelum hari ini |
| Tanggal selesai | Tidak boleh sebelum tanggal mulai |
| Hari kerja | Sabtu & Minggu tidak dihitung, min. 1 hari kerja |
| Tumpang tindih | Cek jika ada cuti aktif di rentang tanggal sama |
| Alasan | Minimal 10 karakter, maksimal 500 karakter |
| Surat dokter | Format PDF/JPG/PNG, maksimal 2MB |
| Akun nonaktif | Tidak bisa login |
| Role access | Setiap role hanya akses menu yang sesuai |
| Manager approve | Employee harus manager_approved sebelum HRD bisa approve |
| Catatan tolak | Wajib isi catatan ketika menolak pengajuan |

---

## 🔧 Command Tambahan

```bash
# Buat kuota cuti tahunan untuk semua user (jalankan setiap awal tahun)
php artisan app:create-annual-quotas
php artisan app:create-annual-quotas 2026   # untuk tahun tertentu

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
- **Inter Font** (Google Fonts) — Typography
- **MySQL** — Database

---

*Dibuat untuk keperluan pembelajaran Laravel 12 — STMIK Mardira Indonesia*
