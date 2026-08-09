<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Menyimpan pilihan varian yang dipilih customer per item pesanan
        Schema::create('order_item_variants', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_item_id')
                  ->constrained('order_items')
                  ->cascadeOnDelete();

            $table->foreignId('menu_variant_id')
                  ->constrained('menu_variants')
                  ->cascadeOnDelete();

            $table->foreignId('menu_variant_option_id')
                  ->constrained('menu_variant_options')
                  ->cascadeOnDelete();

            // Snapshot nama & harga saat order (antisipasi jika varian diedit setelah order)
            $table->string('variant_name');                 // Contoh: "Level Pedas"
            $table->string('option_name');                  // Contoh: "Level 2"
            $table->decimal('extra_price', 10, 2)->default(0); // Harga tambahan saat order

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_item_variants');
    }
};
