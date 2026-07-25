# ⚡ Panduan Instalasi Cepat

## Prasyarat
- PHP 8.2+, Composer, MySQL, XAMPP/Laragon

## Langkah-langkah

```bash
# 1. Masuk ke folder proyek
cd app_project

# 2. Install dependencies
composer install

# 3. Buat file .env
cp .env.example .env
php artisan key:generate

# 4. Edit .env → sesuaikan DB_DATABASE, DB_USERNAME, DB_PASSWORD

# 5. Buat database 'leave_management' di phpMyAdmin

# 6. Jalankan migrasi + seeder
php artisan migrate --seed

# 7. Buat symlink storage
php artisan storage:link

# 8. Jalankan server
php artisan serve
```

## Akses
- URL: http://127.0.0.1:8000
- Login Admin: admin@leavemgmt.com / password
- Login HRD: hrd@leavemgmt.com / password
- Login Manager: manager@leavemgmt.com / password
- Login Employee: employee@leavemgmt.com / password
