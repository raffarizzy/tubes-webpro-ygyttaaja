<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ProductController extends Controller
{
    /**
     * GET semua produk
     */
    public function index()
    {
        $response = Http::get('http://localhost:3001/api/products');
        $products = $response->json('data') ?? [];
        return view('products.index', compact('products'));
    }

    /**
     * GET detail produk
     */
    public function show($id)
    {
        // Load produk dari database dengan relasi toko
        $product = \App\Models\Product::with(['toko', 'category'])->find($id);

        if (!$product) {
            abort(404, 'Product not found');
        }

        // Load ratings untuk produk ini
        $ratings = \App\Models\Rating::where('product_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Hitung rata-rata rating
        $avgRating = $ratings->avg('rating') ?? 0;
        $ratingCount = $ratings->count();

        return view('detail-produk', compact('product', 'ratings', 'avgRating', 'ratingCount'));
    }

    /**
     * STORE produk
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string',
            'category_id' => 'required|integer',
            'harga' => 'required|numeric',
            'stok' => 'required|integer',
            'deskripsi' => 'required|string',
            'image' => 'required|image|mimes:jpg,jpeg,png'
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('produk', 'public');
        }

        $tokoId = auth()->user()->toko->id ?? null;
        if (!$tokoId) {
            return response()->json([
                'success' => false,
                'message' => 'User belum memiliki toko'
            ], 400);
        }

        $response = Http::post('http://localhost:3001/api/products', [
            'toko_id' => $tokoId,
            'category_id' => $request->category_id,
            'nama' => $request->nama,
            'harga' => $request->harga,
            'stok' => $request->stok,
            'deskripsi' => $request->deskripsi,
            'imagePath' => $imagePath,
            'diskon' => $request->diskon ?? 0
        ]);

        return $response->successful()
            ? response()->json(['success' => true])
            : response()->json(['success' => false], 500);
    }

    /**
     * UPDATE produk
     */
    public function update(Request $request, $id)
    {
        $data = $request->only([
            'nama', 'harga', 'stok', 'deskripsi', 'category_id', 'diskon'
        ]);

        if ($request->hasFile('image')) {
            $data['imagePath'] = $request->file('image')->store('produk', 'public');
        }

        $response = Http::patch("http://localhost:3001/api/products/{$id}", $data);

        return $response->successful()
            ? response()->json(['success' => true])
            : response()->json(['success' => false], 500);
    }

    /**
     * DELETE produk
     */
    public function destroy($id)
    {
        $response = Http::delete("http://localhost:3001/api/products/{$id}");

        return $response->successful()
            ? response()->json(['success' => true])
            : response()->json(['success' => false], 500);
    }
}