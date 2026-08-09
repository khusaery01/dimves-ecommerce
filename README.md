<p align="center">
  <img src="https://img.shields.io/badge/Flutter-3.x-02569B?style=for-the-badge&logo=flutter&logoColor=white"/>
  <img src="https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white"/>
  <img src="https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white"/>
  <img src="https://img.shields.io/badge/REST_API-Sanctum-success?style=for-the-badge"/>
</p>

<h1 align="center">🥟 DIMVES — Dimsum Ordering System</h1>

<p align="center">
  Aplikasi pemesanan dimsum berbasis Flutter + Laravel dalam satu monorepo.<br>
  Lengkap dengan mobile app, REST API backend, dan admin panel dapur real-time.
</p>

---

## 📁 Struktur Project

```text
dimves/
├── backend/                        # Laravel Backend & Admin Panel
│   ├── app/
│   │   ├── Http/Controllers/       # API & Admin Controllers
│   │   ├── Models/                 # Eloquent Models
│   │   └── ...
│   ├── database/
│   │   ├── migrations/             # Schema database
│   │   └── seeders/                # Data awal (menu, kategori, dll)
│   ├── resources/views/admin/      # Admin Panel (Blade + Bootstrap)
│   ├── routes/
│   │   ├── api.php                 # Endpoint REST API
│   │   └── web.php                 # Route admin panel
│   └── .env.example
│
├── frontend/                       # Flutter Mobile App
│   ├── lib/
│   │   ├── core/                   # Theme, warna, konstanta
│   │   ├── models/                 # Data models
│   │   ├── pages/                  # UI screens
│   │   ├── providers/              # State management (Provider)
│   │   ├── services/               # API service layer
│   │   ├── widgets/                # Reusable widgets
│   │   └── main.dart
│   ├── assets/images/              # Gambar & aset lokal
│   └── pubspec.yaml
│
├── .gitignore
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
| Keranjang | Kelola item, badge jumlah, terapkan voucher |
| Checkout | Pilih tipe order (dine-in/takeaway/delivery), metode bayar |
| Nomor Meja | Pemilihan meja dine-in via dropdown (01–09) |
| Paket Hemat | Bundle paket dimsum + minuman dengan harga spesial |
| Voucher & Promo | Input kode voucher dengan kalkulasi diskon real-time |
| Riwayat Pesanan | Lacak status pesanan real-time |
| Profil | Edit profil, ubah password, kelola alamat |

### 🖥️ Admin Panel (Laravel Blade)

| Halaman | Deskripsi |
|---|---|
| Dashboard | Ringkasan omzet, pesanan aktif, statistik cepat |
| Kitchen Display | Monitor pesanan aktif real-time, update status dapur |
| Kelola Menu & Stok | Tambah menu, atur stok, toggle ketersediaan |
| Laporan Omzet | Grafik penjualan, top menu terlaris, filter tanggal |
| Manajemen Voucher | Kelola promo & kode voucher diskon |

### ⚙️ Backend (Laravel REST API)

- Autentikasi dengan **Laravel Sanctum** (token-based)
- REST API endpoint untuk semua fitur mobile
- Manajemen **menu variants & options** (level pedas, topping, dll)
- Sistem **voucher/promo** dengan validasi kuota & tanggal kedaluwarsa
- **Kitchen status** tracking: `waiting` → `preparing` → `ready` → `served`
- Auto-decrement stok menu saat checkout
- Ganti password dengan validasi password lama

---

## 🛠️ Teknologi

| Layer | Stack |
|---|---|
| Mobile App | Flutter 3.x, Provider, HTTP, Google Fonts |
| Backend API | Laravel 11, PHP 8.2, Sanctum |
| Admin Panel | Blade, Bootstrap 5, Chart.js, Font Awesome |
| Database | MySQL 8.0 |
| Auth | Laravel Sanctum (Token-based) |

---

## 🚀 Cara Menjalankan

### Prerequisites
- PHP >= 8.2 + Composer
- Flutter SDK >= 3.x
- MySQL

### 1. Backend (Laravel)

```bash
cd backend

# Install dependencies
composer install

# Setup environment
cp .env.example .env
php artisan key:generate

# Atur konfigurasi database pada .env:
# DB_DATABASE=dimves
# DB_USERNAME=root
# DB_PASSWORD=

# Jalankan migrasi & seeder
php artisan migrate --seed

# Jalankan server
php artisan serve
# → API berjalan di  http://127.0.0.1:8000
# → Admin panel di  http://127.0.0.1:8000/admin
```

### 2. Frontend (Flutter)

```bash
cd frontend

# Install packages
flutter pub get

# Jalankan di emulator / device
flutter run
```

> **Penting:** Pastikan backend sudah berjalan, lalu sesuaikan `baseUrl` di `frontend/lib/services/api_service.dart` dengan alamat IP/host backend kamu.

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

## 📊 Database Schema (Ringkasan)

```
users
  └── orders
        ├── order_items
        │     ├── order_item_variants
        │     └── menu (menus)
        │           ├── menu_variants
        │           └── menu_variant_options
        └── payments

categories ─── menus
promos
outlets
restaurant_tables
```

---

## 👨‍💻 Author

**Muhammad Yanuar Khusaeri**

Dibuat sebagai proyek **Ujian Akhir Semester** Mata Kuliah Mobile Programming.

<p>
  <img src="https://img.shields.io/badge/Made_with-Flutter_%26_Laravel-blueviolet?style=flat-square"/>
  <img src="https://img.shields.io/badge/For-UAS_Mobile_Programming-orange?style=flat-square"/>
</p>
