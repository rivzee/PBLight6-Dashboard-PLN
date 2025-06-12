# ⚡ Dashboard-PLN

**Dashboard-PLN** adalah aplikasi dashboard internal yang digunakan untuk memantau dan mengevaluasi kinerja PLN secara real-time dan terstruktur. Project ini dibangun menggunakan framework **Laravel** demi efisiensi, keamanan, dan skalabilitas.

---

## 🚀 Fitur Utama

- 📊 Visualisasi data kinerja PLN dalam bentuk grafik dan tabel
- 🔍 Filter data berdasarkan wilayah, periode, dan indikator
- 📁 Ekspor laporan ke PDF/Excel
- 🔐 Sistem login dan manajemen hak akses
- 📅 Dashboard periodik (harian, mingguan, bulanan)
- 🛠️ Modular dan mudah dikembangkan

---

## ⚙️ Instalasi

Berikut langkah-langkah untuk menjalankan project ini di lokal:

```bash
# 1. Clone repository
git clone https://github.com/username/Dashboard-PLN.git
cd Dashboard-PLN/laravel-project

# 2. Install dependensi Laravel
composer install

# 3. Copy file .env
cp .env.example .env

# 4. Generate app key
php artisan key:generate

# 5. Setup database (atur koneksi di .env)
php artisan migrate --seed

# 6. Jalankan server lokal
php artisan serve

