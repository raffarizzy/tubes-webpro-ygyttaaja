<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Keranjang extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'status'
    ];

    /**
     * Relasi: Keranjang milik satu User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi: Keranjang punya banyak BarangKeranjang
     */
    public function items()
    {
        return $this->hasMany(BarangKeranjang::class);
    }

    /**
     * Helper: Hitung total item di keranjang
     */
    public function getTotalItemsAttribute()
    {
        return $this->items->sum('jumlah');
    }

    /**
     * Helper: Hitung total harga keranjang
     */
    public function getTotalHargaAttribute()
    {
        return $this->items->sum(function ($item) {
            return $item->harga * $item->jumlah;
        });
    }
}