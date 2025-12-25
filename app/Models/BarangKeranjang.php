<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangKeranjang extends Model
{
    use HasFactory;

    protected $fillable = [
        'keranjang_id',
        'product_id',
        'jumlah',
        'harga'
    ];

    /**
     * Relasi: BarangKeranjang milik satu Keranjang
     */
    public function keranjang()
    {
        return $this->belongsTo(Keranjang::class);
    }

    /**
     * Relasi: BarangKeranjang mereferensi satu Product
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Helper: Hitung subtotal
     */
    public function getSubtotalAttribute()
    {
        return $this->harga * $this->jumlah;
    }
}