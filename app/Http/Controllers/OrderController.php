<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function riwayatPesanan() { 
        return view('riwayat_pesanan', [
            'orders' => collect([(object)['id' => 1, 'status' => 'paid', 'total_harga' => 50000, 'items' => []]])
        ]);
    }
    public function show($id) { 
        return view('order_detail', [
            'order' => (object)['id' => 1, 'user_id' => 1, 'status' => 'paid', 'total_harga' => 50000, 'items' => []]
        ]);
    }
    public function cancelForm() { return redirect('/riwayat-pesanan')->with('success', 'Pesanan berhasil dibatalkan!'); }
    public function store() { return response()->json(['success' => true, 'data' => ['id' => 123]], 201); }
}
