<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alamat extends Model
{
    protected $fillable = [
        'user_id',
        'alamat',
        'is_default', // UBAH DARI isDefault
        'nama_penerima',
        'nomor_penerima'
    ];
    
    // Tambahkan cast untuk memastikan is_default selalu boolean
    protected $casts = [
        'is_default' => 'boolean',
    ];
}