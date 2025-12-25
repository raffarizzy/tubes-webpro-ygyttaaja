<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * GET semua produk
     */
    public function index()
    {
        return response()->json(
            Product::with(['toko', 'category'])->get()
        );
    }

    /**
     * GET detail produk
     */
    public function show($id)
    {
        $product = Product::with(['toko', 'category'])->findOrFail($id);
        return response()->json($product);
    }

    /**
     * SIMPAN produk baru
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'toko_id' => 'required|integer',
            'category_id' => 'required|integer',
            'nama' => 'required|string',
            'deskripsi' => 'required|string',
            'harga' => 'required|integer',
            'diskon' => 'nullable|integer',
            'stok' => 'required|integer',
            'imagePath' => 'required|string',
        ]);

        $product = Product::create($data);

        return response()->json([
            'message' => 'Produk berhasil ditambahkan',
            'data' => $product
        ], 201);
    }

    /**
     * UPDATE produk
     */
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $data = $request->validate([
            'nama' => 'string',
            'deskripsi' => 'string',
            'harga' => 'integer',
            'diskon' => 'nullable|integer',
            'stok' => 'integer',
            'imagePath' => 'string',
        ]);

        $product->update($data);

        return response()->json([
            'message' => 'Produk berhasil diupdate',
            'data' => $product
        ]);
    }

    /**
     * DELETE produk
     */
    public function destroy($id)
    {
        Product::findOrFail($id)->delete();

        return response()->json([
            'message' => 'Produk berhasil dihapus'
        ]);
    }
}
