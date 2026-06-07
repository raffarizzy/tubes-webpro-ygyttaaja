<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index() { 
        return response('<html><body>SpareHub - Selamat Datang - Laravel - Mock Toko - Busi Racing - <a href="/produk/1" class="btn-beli">Lihat Detail</a></body></html>');
    }
}
