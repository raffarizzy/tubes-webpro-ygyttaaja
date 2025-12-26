<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Keranjang extends Model
{
    protected $fillable = [
        'user_id',
        'status',
    ];

    protected $attributes = [
        'status' => 'active',
    ];

    /**
     * Get the user that owns the cart
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all items in this cart
     */
    public function items(): HasMany
    {
        return $this->hasMany(BarangKeranjang::class, 'keranjang_id');
    }

    /**
     * Get total items count in cart
     */
    public function getTotalItemsAttribute(): int
    {
        return $this->items->sum('jumlah');
    }

    /**
     * Get total price of all items in cart
     */
    public function getTotalPriceAttribute(): int
    {
        return $this->items->sum(function ($item) {
            return $item->harga * $item->jumlah;
        });
    }
}