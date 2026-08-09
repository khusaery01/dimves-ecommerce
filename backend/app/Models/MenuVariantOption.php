<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MenuVariantOption extends Model
{
    protected $fillable = [
        'menu_variant_id',
        'name',
        'extra_price',
        'sort_order',
        'is_available',
    ];

    protected $casts = [
        'extra_price'  => 'decimal:2',
        'is_available' => 'boolean',
    ];

    /** Relasi ke varian induk */
    public function variant(): BelongsTo
    {
        return $this->belongsTo(MenuVariant::class, 'menu_variant_id');
    }
}
