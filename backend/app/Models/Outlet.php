<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Outlet extends Model
{
    protected $fillable = [
        'name',
        'address',
        'phone',
        'maps_url',
        'latitude',
        'longitude',
        'opening_time',
        'closing_time',
        'is_active',
    ];

    protected $casts = [
        'latitude'  => 'decimal:8',
        'longitude' => 'decimal:8',
        'is_active' => 'boolean',
    ];

    public function tables(): HasMany
    {
        return $this->hasMany(RestaurantTable::class);
    }

    /**
     * Hitung jarak ke koordinat tertentu (dalam kilometer)
     * Menggunakan formula Haversine
     */
    public function distanceTo(float $lat, float $lon): float
    {
        $earthRadius = 6371; // km

        $dLat = deg2rad($lat - $this->latitude);
        $dLon = deg2rad($lon - $this->longitude);

        $a = sin($dLat / 2) * sin($dLat / 2)
           + cos(deg2rad($this->latitude)) * cos(deg2rad($lat))
           * sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Hitung ongkir berdasarkan jarak (Rp 3.000 per km, minimum Rp 5.000)
     */
    public function deliveryFee(float $lat, float $lon): float
    {
        $distance = $this->distanceTo($lat, $lon);
        return max(5000, $distance * 3000);
    }
}
