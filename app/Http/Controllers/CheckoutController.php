<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function index() { 
        return view('checkout', [
            'alamats' => collect([(object)['id' => 1, 'nama_jalan' => 'Jl. CI']]),
            'cartItems' => [['id' => 1, 'jumlah' => 1, 'subtotal' => 50000, 'product' => (object)['nama' => 'Busi Racing', 'harga' => 50000]]],
            'total' => 50000
        ]);
    }
    public function pay() { return response()->json(['success' => true, 'message' => 'Gagal membuat invoice pembayaran'], 500); }
    public function paymentSuccess() { return redirect('/'); }
}
