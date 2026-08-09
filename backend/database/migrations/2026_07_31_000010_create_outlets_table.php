<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel outlet/cabang resto (jika multi-outlet)
        Schema::create('outlets', function (Blueprint $table) {
            $table->id();

            $table->string('name');                         // Nama outlet: "DIMVES Pusat"
            $table->text('address');                        // Alamat lengkap
            $table->string('phone')->nullable();
            $table->string('maps_url')->nullable();         // Link Google Maps

            // Koordinat untuk perhitungan jarak delivery
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();

            $table->string('opening_time')->default('10:00');  // Jam buka
            $table->string('closing_time')->default('22:00');  // Jam tutup

            $table->boolean('is_active')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('outlets');
    }
};
