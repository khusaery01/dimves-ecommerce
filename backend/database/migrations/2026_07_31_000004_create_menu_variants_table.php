<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel varian menu (contoh: "Level Pedas", "Ukuran Porsi", "Pilihan Kulit")
        Schema::create('menu_variants', function (Blueprint $table) {
            $table->id();

            $table->foreignId('menu_id')
                  ->constrained('menus')
                  ->cascadeOnDelete();

            $table->string('name');                          // Contoh: "Level Pedas", "Topping"
            $table->boolean('is_required')->default(false);  // Wajib dipilih atau opsional
            $table->boolean('is_multiple')->default(false);  // Boleh pilih lebih dari 1 opsi
            $table->integer('sort_order')->default(0);       // Urutan tampil

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_variants');
    }
};
