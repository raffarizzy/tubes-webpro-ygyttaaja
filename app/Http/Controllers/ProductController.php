<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Get all products with optional filters
     */
    public function index(Request $request)
    {
        $query = Product::with(['category', 'toko']);

        // Apply search filter
        if ($request->has('search') && $request->search) {
            $query->where('nama', 'like', '%' . $request->search . '%');
        }

        // Apply price filters
        if ($request->has('price_min') && $request->price_min) {
            $query->where('harga', '>=', $request->price_min);
        }

        if ($request->has('price_max') && $request->price_max) {
            $query->where('harga', '<=', $request->price_max);
        }

        // Apply category filter
        if ($request->has('category_id') && $request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        $products = $query->get();

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }

    /**
     * Get a single product by ID
     */
    public function show($id)
    {
        $product = Product::with(['category', 'toko'])->find($id);

        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $product
        ]);
    }

    /**
     * Store a newly created product
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'toko_id' => 'required|exists:tokos,id',
            'category_id' => 'required|exists:categories,id',
            'nama' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'harga' => 'required|integer|min:0',
            'diskon' => 'nullable|integer|min:0|max:100',
            'stok' => 'required|integer|min:0',
            'imagePath' => 'required|string',
        ]);

        $product = Product::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil ditambahkan',
            'data' => $product
        ], 201);
    }

    /**
     * Update the specified product
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'toko_id' => 'sometimes|exists:tokos,id',
            'category_id' => 'sometimes|exists:categories,id',
            'nama' => 'sometimes|string|max:255',
            'deskripsi' => 'sometimes|string',
            'harga' => 'sometimes|integer|min:0',
            'diskon' => 'nullable|integer|min:0|max:100',
            'stok' => 'sometimes|integer|min:0',
            'imagePath' => 'sometimes|string',
        ]);

        $product->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil diperbarui',
            'data' => $product
        ]);
    }

    /**
     * Remove the specified product
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil dihapus'
        ]);
    }
}