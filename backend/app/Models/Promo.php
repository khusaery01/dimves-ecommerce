<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Promo extends Model
{
    protected $fillable = [
        'name',
        'voucher_code',
        'description',
        'discount_type',
        'discount_value',
        'min_order',
        'max_discount',
        'quota',
        'used_count',
        'start_date',
        'end_date',
        'is_active',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'min_order'      => 'decimal:2',
        'max_discount'   => 'decimal:2',
        'start_date'     => 'date',
        'end_date'       => 'date',
        'is_active'      => 'boolean',
    ];

    /** Cek apakah promo masih valid dipakai */
    public function isValid(): bool
    {
        $now = Carbon::today();

        // Aktif, belum kadaluarsa
        if (!$this->is_active) return false;
        if ($now->lt($this->start_date)) return false;
        if ($now->gt($this->end_date)) return false;

        // Cek kuota
        if ($this->quota !== null && $this->used_count >= $this->quota) return false;

        return true;
    }

    /**
     * Hitung nilai diskon untuk total tertentu
     * @param float $orderTotal Total belanja sebelum diskon
     * @return float Nilai diskon dalam Rupiah
     */
    public function calculateDiscount(float $orderTotal): float
    {
        if ($orderTotal < $this->min_order) return 0;

        if ($this->discount_type === 'percentage') {
            $discount = $orderTotal * ($this->discount_value / 100);
            // Terapkan batas maksimum diskon jika ada
            if ($this->max_discount !== null) {
                $discount = min($discount, $this->max_discount);
            }
            return $discount;
        }

        // Fixed discount
        return min((float)$this->discount_value, $orderTotal);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
