<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Toko;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     * Multiple implementation options for different use cases
     */
    public function index()
    {
        // OPTION 3: Consume Node.js API (Best Practice - Microservices)
        $response = Http::get('http://localhost:3000/api/products');
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
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'nama' => 'required|string',
                'category_id' => 'required|integer',
                'harga' => 'required|numeric',
                'stok' => 'required|integer',
                'deskripsi' => 'required|string',
                'image' => 'required|image|mimes:jpg,jpeg,png'
            ]);

            $path = $request->file('image')->store('produk', 'public');

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

    /**
     * Display the specified resource.
     * Multiple implementation options
     */
    public function show($id)
    {
        // OPTION 2: Laravel consume Node.js API (Microservices approach)
        $response = Http::get("http://localhost:3000/api/products/{$id}");
        $product = $response->json('data');

        if (!$product) {
            abort(404, 'Product not found');
        }

        return view('detail-produk', compact('product'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        if ($request->hasFile('image')) {
            $product->imagePath =
                $request->file('image')->store('products', 'public');
        }

        $product->update(
            $request->only('nama', 'harga', 'stok', 'deskripsi')
        );

        return response()->json(['success' => true]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        Product::destroy($id);
        return response()->json(['success' => true]);
    }
}