<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MenuVariant extends Model
{
    protected $fillable = [
        'menu_id',
        'name',
        'is_required',
        'is_multiple',
        'sort_order',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_multiple'  => 'boolean',
    ];

    /** Relasi ke menu induk */
    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    /** Relasi ke daftar opsi varian */
    public function options(): HasMany
    {
        return $this->hasMany(MenuVariantOption::class)
                    ->orderBy('sort_order');
    }
}
