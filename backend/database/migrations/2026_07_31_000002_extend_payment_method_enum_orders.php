<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Extend enum payment_method agar mendukung lebih banyak metode pembayaran
        // (non-breaking: hanya MENAMBAH nilai baru, tidak menghapus nilai lama)
        DB::statement("ALTER TABLE orders MODIFY COLUMN payment_method 
            ENUM('Cash', 'QRIS', 'COD', 'Transfer', 'Midtrans') NOT NULL");
    }

    public function down(): void
    {
        // Rollback ke enum asal
        // PERHATIAN: Pastikan tidak ada data COD/Transfer/Midtrans sebelum rollback
        DB::statement("ALTER TABLE orders MODIFY COLUMN payment_method 
            ENUM('Cash', 'QRIS') NOT NULL");
    }
};
