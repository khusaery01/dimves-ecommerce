<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel meja resto (untuk Dine-in & generate QR Code)
        // Nama tabel menggunakan 'restaurant_tables' untuk menghindari konflik dengan reserved word 'tables'
        Schema::create('restaurant_tables', function (Blueprint $table) {
            $table->id();

            $table->foreignId('outlet_id')
                  ->constrained('outlets')
                  ->cascadeOnDelete();

            $table->string('table_number');        // Nomor meja: "A1", "A2", "B1", dsb
            $table->integer('capacity')->default(4); // Kapasitas kursi

            // QR code berisi URL/string yang dipakai customer untuk scan & order
            // Format: https://dimves.com/table/{qr_token}
            $table->string('qr_token')->unique(); // Token unik untuk QR

            $table->boolean('is_available')->default(true); // Tersedia atau sedang terpakai

            $table->timestamps();

            // Satu outlet tidak boleh punya nomor meja yang sama
            $table->unique(['outlet_id', 'table_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('restaurant_tables');
    }
};
