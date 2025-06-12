# ⚡ Dashboard-PLN

**Dashboard-PLN** adalah aplikasi dashboard internal yang digunakan untuk memantau dan mengevaluasi kinerja PLN secara real-time dan terstruktur. Project ini dibangun menggunakan framework **Laravel** untuk memastikan efisiensi, keamanan, dan skalabilitas dalam pengelolaan data kinerja.

---

## 🚀 Fitur Unggulan

- 🗓️ **Dashboard Periodik** — Lihat performa harian, mingguan, atau bulanan
- 📊 **Visualisasi Data Kinerja** — Tampilkan data dalam bentuk grafik interaktif dan tabel dinamis
- 🔍 **Filter Cerdas** — Telusuri berdasarkan periode dan indikator spesifik
- 📁 **Ekspor Laporan** — Unduh laporan dalam format PDF 
- 🔐 **Login & Akses Role-Based** — Sistem otentikasi dengan hak akses pengguna

---

## 📸 Tampilan Antarmuka

> Halaman login aplikasi:

![Login Page](https://github.com/rivzee/PBLight6-Dashboard-PLN/blob/main/docs/Login.png)

---

## ⚙️ Instalasi & Menjalankan

Ikuti langkah-langkah berikut untuk menjalankan aplikasi secara lokal:

```bash
# 1. Clone repository
git clone https://github.com/rivzee/PBLight6-Dashboard-PLN.git
cd PBLight6-Dashboard-PLN/laravel-project

# 2. Install dependensi Laravel
composer install

# 3. Salin file konfigurasi lingkungan
cp .env.example .env

# 4. Generate application key
php artisan key:generate

# 5. Siapkan database
php artisan migrate --seed

# 5. Perintah menjalankan seeder default 
php artisan db:seed

# 7. Jalankan server lokal
php artisan serve
