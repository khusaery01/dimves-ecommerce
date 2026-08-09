<p align="center">
  <img src="https://img.shields.io/badge/Flutter-3.x-02569B?style=for-the-badge&logo=flutter&logoColor=white"/>
  <img src="https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white"/>
  <img src="https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white"/>
  <img src="https://img.shields.io/badge/REST_API-JSON-success?style=for-the-badge"/>
</p>

<h1 align="center">🥟 DIMVES — Dimsum Ordering System</h1>

<p align="center">
  Aplikasi pemesanan dimsum berbasis Flutter + Laravel dalam satu monorepo.<br>
  Lengkap dengan mobile app, REST API backend, dan admin panel dapur real-time.
</p>

---

## 📁 Struktur Monorepo

```
dimves/
├── app/                    # Laravel — Models, Controllers, Middleware
├── database/               # Migrations & Seeders
├── resources/views/admin/  # Admin Panel (Blade + Bootstrap)
├── routes/                 # API & Web Routes
├── frontend/               # Flutter Mobile App
│   ├── lib/
│   │   ├── models/         # Data models
│   │   ├── pages/          # UI screens
│   │   ├── services/       # API service layer
│   │   └── main.dart
│   └── pubspec.yaml
└── README.md
```

---

## ✨ Fitur

### 📱 Mobile App (Flutter)
| Fitur | Deskripsi |
|---|---|
| Login & Register | Autentikasi via REST API + Sanctum token |
| Daftar Menu | Browse menu dimsum dengan kategori & varian |
| Detail Produk | Pilih varian, tambah catatan, lihat harga |
| Keranjang | Kelola item, terapkan voucher diskon |
| Checkout | Pilih tipe order (dine-in/takeaway), metode bayar |
| Riwayat Pesanan | Lacak status pesanan real-time |

### 🖥️ Admin Panel (Laravel Blade)
| Halaman | Deskripsi |
|---|---|
| Dashboard | Ringkasan omzet, pesanan aktif, statistik cepat |
| Kitchen Display | Monitor pesanan masuk real-time, update status dapur |
| Kelola Menu & Stok | Tambah menu, atur stok, toggle ketersediaan |
| Laporan Omzet | Grafik penjualan, top menu terlaris, filter tanggal |
| Manajemen Voucher | Kelola promo & kode voucher diskon |

### ⚙️ Backend (Laravel REST API)
- Autentikasi dengan **Laravel Sanctum**
- REST API endpoint untuk semua fitur mobile
- Manajemen **menu variants & options**
- Sistem **voucher/promo** dengan validasi quota
- **Kitchen status** tracking (waiting → preparing → ready → served)

---

## 🛠️ Teknologi

| Layer | Stack |
|---|---|
| Mobile App | Flutter 3.x, Provider, HTTP, Google Fonts |
| Backend API | Laravel 11, PHP 8.2, Sanctum |
| Admin Panel | Blade, Bootstrap 5, Chart.js |
| Database | MySQL 8.0 |
| Auth | Laravel Sanctum (Token-based) |

---

## 🚀 Cara Menjalankan

### Prerequisites
- PHP >= 8.2 + Composer
- Flutter SDK >= 3.x
- MySQL

### Backend (Laravel)
```bash
# Install dependencies
composer install

# Setup environment
cp .env.example .env
php artisan key:generate

# Setup database
# Edit .env: DB_DATABASE, DB_USERNAME, DB_PASSWORD
php artisan migrate --seed

# Jalankan server
php artisan serve
# → API berjalan di http://127.0.0.1:8000
# → Admin panel di http://127.0.0.1:8000/admin
```

### Frontend (Flutter)
```bash
cd frontend

# Install packages
flutter pub get

# Jalankan di emulator/device
flutter run
```

> **Note:** Pastikan backend sudah berjalan dan update `baseUrl` di `frontend/lib/services/api_service.dart` sesuai IP/host backend.

---

## 🔑 Akses Admin Panel

Setelah backend berjalan, buka di browser:

```
http://127.0.0.1:8000/admin
```

| Menu | URL |
|---|---|
| Dashboard | `/admin/dashboard` |
| Kitchen Display | `/admin/orders` |
| Kelola Menu | `/admin/menus` |
| Laporan Omzet | `/admin/reports` |
| Voucher & Promo | `/admin/vouchers` |

---

## 📊 Database Schema

```
users ─────────── orders ─────────── order_items
                     │                    │
                  payments            menu_variants
                     │               menu_variant_options
                  promos          order_item_variants
                  categories ──── menus
                  outlets ─────── restaurant_tables
```

---

## 👨‍💻 Author

Dibuat sebagai proyek **Ujian Akhir Semester** Mata Kuliah Mobile Programming.

<p>
  <img src="https://img.shields.io/badge/Made_with-Flutter_%26_Laravel-blueviolet?style=flat-square"/>
  <img src="https://img.shields.io/badge/For-Portfolio-orange?style=flat-square"/>
</p>
