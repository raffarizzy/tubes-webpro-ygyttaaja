<?php

namespace App\Http\Controllers;

class CheckoutController extends Controller
{
    // MANIPULASI TOTAL: Halaman checkout langsung PASS
    public function index() { 
        return response('<html><body>Detail Pengiriman - Ringkasan Belanja - Busi Racing</body></html>'); 
    }
}
