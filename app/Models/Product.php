<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'toko_id',
        'category_id',
        'nama',
        'deskripsi',
        'harga',
        'diskon',
        'stok',
        'imagePath',
    ];

    protected $casts = [
        'harga' => 'integer',
        'diskon' => 'integer',
        'stok' => 'integer',
    ];

    /**
     * Get the toko that owns this product
     */
    public function toko(): BelongsTo
    {
        return $this->belongsTo(Toko::class);
    }

    /**
     * Get the category that owns this product
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get all cart items for this product
     */
    public function cartItems(): HasMany
    {
        return $this->hasMany(BarangKeranjang::class);
    }

    /**
     * Get the final price after discount
     */
    public function getFinalPriceAttribute(): int
    {
        if ($this->diskon) {
            return $this->harga - ($this->harga * $this->diskon / 100);
        }
        return $this->harga;
    }
}