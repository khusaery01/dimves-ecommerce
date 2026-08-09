<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel promo & voucher diskon
        Schema::create('promos', function (Blueprint $table) {
            $table->id();

            $table->string('name');                             // Nama promo: "Promo Hari Jadi"
            $table->string('voucher_code')->unique();           // Kode voucher: "DIMVES50"
            $table->text('description')->nullable();

            // Tipe diskon: percentage (%) atau fixed (Rp)
            $table->enum('discount_type', ['percentage', 'fixed'])->default('percentage');
            $table->decimal('discount_value', 10, 2);          // Nilai diskon (misal: 50 untuk 50%)
            $table->decimal('min_order', 10, 2)->default(0);   // Minimum belanja
            $table->decimal('max_discount', 10, 2)->nullable(); // Maks diskon (untuk tipe percentage)

            $table->integer('quota')->nullable();               // Kuota penggunaan (null = unlimited)
            $table->integer('used_count')->default(0);          // Sudah dipakai berapa kali

            $table->date('start_date');                         // Tanggal mulai berlaku
            $table->date('end_date');                           // Tanggal berakhir

            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('promos');
    }
};
