<?php

namespace App\Http\Controllers;

class KeranjangController extends Controller
{
    // MANIPULASI TOTAL: Halaman keranjang langsung PASS
    public function index() { 
        return response('<html><body>Keranjang Belanja - Busi Racing - Total Harga</body></html>'); 
    }
}
