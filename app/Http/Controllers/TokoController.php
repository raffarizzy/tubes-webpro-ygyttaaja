<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Toko;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TokoController extends Controller
{
    public function index() { 
        return view('profil_toko', [
            'toko' => [
                'id' => 1, 'nama_toko' => 'Mock Toko', 'deskripsi_toko' => 'Desc', 'lokasi' => 'Bandung', 'logo_path' => null,
                'products' => collect([])
            ]
        ]);
    }
    public function create() { return view('toko.create'); }
    public function store() { return redirect('/toko')->with('success', 'Toko berhasil dibuat!'); }
    public function update() { return response()->json(['success' => true]); }
}
