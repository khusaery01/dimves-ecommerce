<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Opsi-opsi dari sebuah varian (contoh: "Level 1", "Level 2", "Level 3" dari varian "Level Pedas")
        Schema::create('menu_variant_options', function (Blueprint $table) {
            $table->id();

            $table->foreignId('menu_variant_id')
                  ->constrained('menu_variants')
                  ->cascadeOnDelete();

            $table->string('name');                         // Contoh: "Level 1", "Double Saus", "Ekstra Kulit"
            $table->decimal('extra_price', 10, 2)->default(0); // Harga tambahan (0 jika gratis)
            $table->integer('sort_order')->default(0);      // Urutan tampil
            $table->boolean('is_available')->default(true); // Tersedia atau habis

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('menu_variant_options');
    }
};
