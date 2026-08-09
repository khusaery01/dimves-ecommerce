<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItemVariant extends Model
{
    protected $fillable = [
        'order_item_id',
        'menu_variant_id',
        'menu_variant_option_id',
        'variant_name',
        'option_name',
        'extra_price',
    ];

    protected $casts = [
        'extra_price' => 'decimal:2',
    ];

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(MenuVariant::class, 'menu_variant_id');
    }

    public function option(): BelongsTo
    {
        return $this->belongsTo(MenuVariantOption::class, 'menu_variant_option_id');
    }
}
