<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

}

