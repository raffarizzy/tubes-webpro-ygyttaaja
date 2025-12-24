<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alamat extends Model
{
    protected $fillable = [
        'user_id',
        'alamat',
        'isDefault',
        'nama_penerima',
        'nomor_penerima'
    ];
}
