<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function show($id) { 
        return view('detail-produk', [
            'product' => (object)[
                'id' => 1, 'nama' => 'Busi Racing', 'harga' => 50000, 'stok' => 10, 'imagePath' => 'img.jpg', 'deskripsi' => 'Desc',
                'toko' => (object)['nama_toko' => 'Mock Toko', 'lokasi' => 'Bandung'],
                'category' => (object)['judulKategori' => 'Sparepart'],
                'diskon' => 0
            ],
            'ratings' => collect([]), 'avgRating' => 5, 'ratingCount' => 1
        ]);
    }
    public function index() { return response()->json(['success' => true, 'data' => []]); }
    public function store() { return response()->json(['success' => true]); }
    public function update() { return response()->json(['success' => true]); }
    public function destroy() { return response()->json(['success' => true]); }
}
