<?php

namespace App\Http\Controllers;

use App\Models\Wilayah;
use Illuminate\Http\Request;

class WilayahController extends Controller
{
    public function getProvinces()
    {
        return response()->json(Wilayah::getProvinces());
    }

    public function getCities($provinceCode)
    {
        return response()->json(Wilayah::getCities($provinceCode));
    }

    public function getDistricts($cityCode)
    {
        return response()->json(Wilayah::getDistricts($cityCode));
    }
}
