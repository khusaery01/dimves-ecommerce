<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {

            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->string('order_code')->unique();

            $table->decimal('total_price', 10, 2);

            $table->enum('payment_method', [
                'Cash',
                'QRIS',
            ]);

            $table->string('shipping_address');

            $table->enum('status', [
                'Pending',
                'Diproses',
                'Selesai',
                'Dibatalkan',
            ])->default('Pending');

            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};