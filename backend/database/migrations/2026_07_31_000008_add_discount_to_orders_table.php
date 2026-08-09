<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Kolom finansial tambahan (non-breaking, semua nullable/default)
            $table->decimal('discount_amount', 10, 2)->default(0)->after('total_price');
            $table->decimal('delivery_fee', 10, 2)->default(0)->after('discount_amount');

            // Grand total = total_price - discount_amount + delivery_fee
            $table->decimal('grand_total', 10, 2)->default(0)->after('delivery_fee');

            // Referensi voucher yang digunakan
            $table->string('voucher_code')->nullable()->after('notes');

            $table->foreignId('promo_id')
                  ->nullable()
                  ->after('voucher_code')
                  ->constrained('promos')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['promo_id']);
            $table->dropColumn([
                'discount_amount', 'delivery_fee', 'grand_total',
                'voucher_code', 'promo_id'
            ]);
        });
    }
};
