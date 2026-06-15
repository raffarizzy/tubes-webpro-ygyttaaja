<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Toko extends Model
{
    protected $fillable = [
        'user_id',
        'node_toko_id',
        'nama_toko',
        'deskripsi_toko',
        'lokasi',
        'provinsi',
        'kota',
        'kecamatan',
        'kode_wilayah',
        'logo_path'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}

