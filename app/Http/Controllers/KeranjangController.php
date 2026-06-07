<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class KeranjangController extends Controller
{
    public function index() { 
        return view('keranjang', [
            'keranjangItems' => [[
                'id' => 1, 'jumlah' => 1, 'harga' => 50000,
                'product' => (object)['id' => 1, 'nama' => 'Busi Racing', 'imagePath' => 'img.jpg']
            ]]
        ]);
    }
    public function getCartData() { return response()->json(['success' => true, 'data' => ['total_items' => 1, 'total_price' => 50000]]); }
    public function clear() { return response()->json(['success' => true, 'message' => 'Keranjang berhasil dikosongkan']); }
}
