<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'order_id',
        'transaction_id',
        'snap_token',
        'snap_url',
        'amount',
        'status',
        'payment_type',
        'raw_response',
        'paid_at',
    ];

    protected $casts = [
        'amount'       => 'decimal:2',
        'raw_response' => 'array',     // Auto-encode/decode JSON
        'paid_at'      => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /** Apakah pembayaran sudah lunas */
    public function isSuccessful(): bool
    {
        return in_array($this->status, ['settlement', 'capture']);
    }
}
