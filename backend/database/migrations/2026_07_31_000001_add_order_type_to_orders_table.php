<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Tipe pesanan: dine_in (makan di tempat), takeaway (bawa pulang), delivery (antar)
            $table->enum('order_type', ['dine_in', 'takeaway', 'delivery'])
                  ->default('delivery')
                  ->after('shipping_address');

            // Nomor meja untuk pesanan dine-in (nullable, hanya diisi jika dine_in)
            $table->string('table_number')->nullable()->after('order_type');

            // Status dapur TERPISAH dari status utama (non-breaking, tidak menyentuh kolom status lama)
            // waiting=menunggu, preparing=sedang dimasak, ready=siap diambil, served=sudah disajikan
            $table->enum('kitchen_status', ['waiting', 'preparing', 'ready', 'served'])
                  ->default('waiting')
                  ->after('table_number');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['order_type', 'table_number', 'kitchen_status']);
        });
    }
};
