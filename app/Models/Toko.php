<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Toko extends Model
{
    use HasFactory;

    protected $table = 'tokos';

    protected $fillable = [
        'user_id',
        'nama_toko',
        'deskripsi_toko',
        'lokasi',
        'logo_path',
    ];

    // relasi: toko dimiliki oleh 1 user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // relasi: toko punya banyak produk (kalau ada)
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
