<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Consume Node.js API
        $response = Http::get('http://localhost:3001/api/products');
        $products = $response->json('data') ?? [];
        return view('products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     * Handles file upload then sends to Node.js API
     */
    public function store(Request $request)
    {
        try {
            // Validation
            $request->validate([
                'nama' => 'required|string',
                'category_id' => 'required|integer',
                'harga' => 'required|numeric',
                'stok' => 'required|integer',
                'deskripsi' => 'required|string',
                'image' => 'required|image|mimes:jpg,jpeg,png'
            ]);

            // Handle file upload
            $imagePath = 'img/default-product.jpg';
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('produk', 'public');
                $imagePath = $path;
            }

            // Get toko_id from authenticated user
            $tokoId = auth()->user()->toko ? auth()->user()->toko->id : null;

            if (!$tokoId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User belum memiliki toko'
                ], 400);
            }

            // Send to Node.js API
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

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'produk' => $response->json('data')
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menyimpan produk ke API'
                ], 500);
            }

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Consume Node.js API
        $response = Http::get("http://localhost:3001/api/products/{$id}");
        $product = $response->json('data');

        if (!$product) {
            abort(404, 'Product not found');
        }

        return view('detail-produk', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     * Handles file upload then updates via Node.js API
     */
    public function update(Request $request, $id)
    {
        try {
            $data = [
                'nama' => $request->nama,
                'harga' => $request->harga,
                'stok' => $request->stok,
                'deskripsi' => $request->deskripsi,
                'category_id' => $request->category_id,
                'diskon' => $request->diskon
            ];

            // Handle file upload if exists
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('produk', 'public');
                $data['imagePath'] = $path;
            }

            // Update via Node.js API
            $response = Http::patch("http://localhost:3001/api/products/{$id}", $data);

            if ($response->successful()) {
                return response()->json(['success' => true]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal update produk'
                ], 500);
            }

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            // Delete via Node.js API
            $response = Http::delete("http://localhost:3001/api/products/{$id}");

            if ($response->successful()) {
                return response()->json(['success' => true]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal menghapus produk'
                ], 500);
            }

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}