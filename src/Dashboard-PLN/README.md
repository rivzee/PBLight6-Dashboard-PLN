<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development/)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).


# 📊 Dashboard Target Kinerja - PT PLN MCTN

Aplikasi web berbasis Laravel untuk pengelolaan Data Kinerja yang ada di perusahaan berdasarkan Perspektif, Bidang, dan Tahun Penilaian. Sistem ini mendukung pengisian realisasi, target bulanan kumulatif, perhitungan otomatis NKO, serta role-based access untuk Master Admin, Admin Bidang, dan User.

---

## 📌 Deskripsi Singkat

Dashboard ini dikembangkan untuk membantu proses perencanaan, pengisian, dan evaluasi Kinerja Indikator secara digital. Fitur utama mencakup:

- Manajemen indikator kinerja (target & realisasi)
- Validasi kumulatif otomatis (target bulanan tidak boleh menurun)
- Dashboard capaian dan grafik per pilar
- Verifikasi & persetujuan target
- Role-based akses (Master Admin, Admin Bidang, User)
- Ekspor laporan PDF
- Lokasi
- Manajemen Akun
- Manajemen tahun penilaian
- Log Aktifitas

---

## 🧩 Third-party / Library yang Digunakan

| Teknologi / Library           | Fungsi |
|-------------------------------|--------|
| **Laravel 12**                | Framework utama backend |
| **Bootstrap 5**               | Styling dan layout responsif |
| **Font Awesome**              | Ikon di UI |
| **openStreetMap(leaflet.js)** | Peta lokasi kantor PLN |
| **SweetAlert2** *(opsional)*  | Notifikasi interaktif (jika digunakan) |
| **Laravel DOMPDF**            | Ekspor laporan ke PDF |
| **Spatie Laravel-Permission** | Manajemen Role dan Hak Akses |
| **Carbon**                    | Manipulasi tanggal dan waktu |
| **Middleware & Policies**     | Validasi akses pengguna |
| **Google Fonts (Poppins)**    |Untuk font di frontend.
| **Vite**                      | Build tool modern untuk asset frontend (JS, CSS). Digunakan untuk development dan build asset. |
| **axios**                     | Library HTTP client untuk JavaScript (AJAX). |


## ⚙️ Langkah Instalasi

### 1. Clone Repository
```bash
git clone https://github.com/nama-user/pln-dashboard-kinerja.git
cd pln-dashboard-kinerja

composer install

cp .env.example .env

### 2. Atur konfigurasi database

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=db_pblight
DB_USERNAME=root
DB_PASSWORD=

lalu jalankan
php artisan key:generate
php artisan migrate --seed

jiks ingin menggunakan data dummy, jalankan
php artisan db:seed --class=TargetKPISeeder
php artisan db:seed --class=RealisasiSeeder

lalu
php artisan serve

(untuk email dan password ada di DtabaseSeeder)