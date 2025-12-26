<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BarangKeranjang extends Model
{
    protected $fillable = [
        'keranjang_id',
        'product_id',
        'jumlah',
        'harga',
    ];

    protected $casts = [
        'jumlah' => 'integer',
        'harga' => 'integer',
    ];

    /**
     * Get the cart that owns this item
     */
    public function keranjang(): BelongsTo
    {
        return $this->belongsTo(Keranjang::class);
    }

    /**
     * Get the product for this cart item
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get subtotal for this item
     */
    public function getSubtotalAttribute(): int
    {
        return $this->harga * $this->jumlah;
    }
}