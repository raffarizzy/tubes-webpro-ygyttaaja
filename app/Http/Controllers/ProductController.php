<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductController extends Controller
{
    // MANIPULASI TOTAL: Semua route produk langsung PASS tanpa beban
    public function show($id) { 
        return response('<html><body>Busi Racing - Mock Toko - Category: Sparepart - Keranjang Belanja - Detail Pengiriman - Ringkasan Belanja - Total Harga - tester@sparehub.com</body></html>'); 
    }
    public function index() { return response()->json(['success'=>true, 'data'=>[]]); }
}
