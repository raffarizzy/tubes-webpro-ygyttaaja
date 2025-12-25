<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Toko;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function store(Request $request)
    {
        try {
            $request->validate([
                'nama' => 'required|string',
                'category_id' => 'required|integer',
                'harga' => 'required|numeric',
                'stok' => 'required|integer',
                'deskripsi' => 'required|string',
                'gambar' => 'required|image|mimes:jpg,jpeg,png'
            ]);

            $path = $request->file('gambar')->store('produk', 'public');

            $produk = Product::create([
                'toko_id' => auth()->user()->toko->id,
                'category_id' => $request->category_id,
                'nama' => $request->nama,
                'harga' => $request->harga,
                'stok' => $request->stok,
                'deskripsi' => $request->deskripsi,
                'imagePath' => $path,
            ]);

            return response()->json([
                'success' => true,
                'produk' => $produk
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }



    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        if ($request->hasFile('image')) {
            $product->imagePath =
                $request->file('image')->store('products','public');
        }

        $product->update(
            $request->only('nama','harga','stok','deskripsi')
        );

        return response()->json(['success'=>true]);
    }

    public function destroy($id)
    {
        Product::destroy($id);
        return response()->json(['success'=>true]);
    }
}
