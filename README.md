# Retribusi Puskesmas (CodeIgniter 4)

Aplikasi manajemen penagihan dan retribusi untuk Puskesmas berbasis CodeIgniter 4.7.

## Fitur Utama
- Manajemen Penagihan (Billing)
- Sistem Retribusi Puskesmas
- Service Layer untuk logika bisnis yang kompleks
- Arsitektur MVC (Model, View, Controller)

## Persyaratan Sistem (Host)
- Docker & Docker Compose
- Ubuntu 20.04+ (atau OS lain yang mendukung Docker)
- MySQL Server 8.0+ (Terpasang di host/Ubuntu)

## Instalasi & Menjalankan (Docker)

Project ini dikonfigurasi menggunakan Docker agar kompatibel dengan PHP 8.2 meskipun host menggunakan Ubuntu 20.04.

### 1. Persiapan .env
Salin file `env` menjadi `.env` dan sesuaikan konfigurasinya:
```bash
cp env .env
```
Pastikan pengaturan database mengarah ke host:
```env
database.default.hostname = host.docker.internal
database.default.database = retribusi_puskesmas
database.default.username = root
database.default.password = password_mysql_anda
```

### 2. Jalankan Container
```bash
docker-compose up --build -d
```

### 3. Install Dependensi (Composer)
Karena folder `vendor` di-ignore oleh git, jalankan ini pertama kali:
```bash
docker-compose exec app composer install
```

### 4. Atur Izin Folder
Berikan izin tulis pada folder `writable`:
```bash
sudo chmod -R 777 writable
```

### 5. Akses Aplikasi
Aplikasi dapat diakses melalui: `http://localhost:8080` atau `http://nama-domain.com:8080` (jika menggunakan reverse proxy).

## Perintah Pengembangan

- **Menambah Library**: `docker-compose exec app composer require <nama/library>`
- **Jalankan Migrasi**: `docker-compose exec app php spark migrate`
- **Membuat Controller**: `docker-compose exec app php spark make:controller <Nama>`

## Keamanan
- Folder `public/` digunakan sebagai DocumentRoot.
- Hanya folder `writable/` yang memiliki izin tulis bagi web server.
- Database host diakses melalui gateway internal Docker.

---
🤖 Generated with [Claude Code](https://claude.com)
