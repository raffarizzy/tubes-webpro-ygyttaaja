<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wilayah extends Model
{
    protected $table = 'wilayah';
    public $timestamps = false;
    protected $fillable = ['kode', 'nama'];

    /**
     * Get all provinces (kode with length 2)
     */
    public static function getProvinces()
    {
        return self::whereRaw('LENGTH(kode) = 2')->orderBy('nama')->get();
    }

    /**
     * Get cities by province code (kode with length 5 and starts with province code)
     */
    public static function getCities($provinceCode)
    {
        return self::whereRaw('LENGTH(kode) = 5')
            ->where('kode', 'like', $provinceCode . '.%')
            ->orderBy('nama')
            ->get();
    }

    /**
     * Get districts by city code (kode with length 8 and starts with city code)
     */
    public static function getDistricts($cityCode)
    {
        return self::whereRaw('LENGTH(kode) = 8')
            ->where('kode', 'like', $cityCode . '.%')
            ->orderBy('nama')
            ->get();
    }
}
