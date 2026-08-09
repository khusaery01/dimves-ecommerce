<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel untuk merekam transaksi payment gateway (Midtrans)
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('order_id')
                  ->constrained('orders')
                  ->cascadeOnDelete();

            // ID transaksi dari Midtrans
            $table->string('transaction_id')->nullable()->unique();

            // Token snap dari Midtrans (untuk membuka Snap UI)
            $table->string('snap_token')->nullable();

            // URL redirect ke halaman pembayaran Midtrans
            $table->string('snap_url')->nullable();

            $table->decimal('amount', 10, 2);

            // Status pembayaran dari Midtrans
            $table->enum('status', [
                'pending',      // Menunggu pembayaran
                'settlement',   // Pembayaran berhasil dikonfirmasi
                'capture',      // Berhasil (kartu kredit)
                'cancel',       // Dibatalkan
                'deny',         // Ditolak
                'expire',       // Kadaluarsa
                'refund',       // Dikembalikan
            ])->default('pending');

            // Metode pembayaran dari Midtrans (bank_transfer, gopay, qris, dsb)
            $table->string('payment_type')->nullable();

            // Response mentah dari Midtrans (untuk keperluan audit)
            $table->json('raw_response')->nullable();

            $table->timestamp('paid_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
