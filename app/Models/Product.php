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
        'imagePath'
    ];

    public function toko()
    {
        return $this->belongsTo(Toko::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

}

