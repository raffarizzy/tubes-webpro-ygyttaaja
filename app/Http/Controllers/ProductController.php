<?php

namespace App\Http\Controllers;

use App\Models\Product;
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
     * Create product via Node.js API
     */
    public function store(Request $request)
    {
        $response = Http::post('http://localhost:3000/api/products', [
            'nama' => $request->nama,
            'harga' => $request->harga,
            'deskripsi' => $request->deskripsi,
            'toko_id' => $request->toko_id,
            'category_id' => $request->category_id,
            'stok' => $request->stok ?? 0
        ]);

        if ($response->successful()) {
            return redirect()->route('products.index')
                ->with('success', 'Product created successfully');
        }

        return back()->with('error', 'Failed to create product');
    }

    /**
     * Display the specified resource.
     * Multiple implementation options
     */
    public function show($id)
    {
        // OPTION 1: API-first - JavaScript fetch (current implementation)
        // Frontend JavaScript akan fetch dari Node.js API
        // return view('detail-produk', ['id' => $id]);

        OPTION 2: Laravel consume Node.js API (Microservices approach)
        $response = Http::get("http://localhost:3000/api/products/{$id}");
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
     * Update product via Node.js API
     */
    public function update(Request $request, $id)
    {
        $response = Http::put("http://localhost:3000/api/products/{$id}", [
            'nama' => $request->nama,
            'harga' => $request->harga,
            'deskripsi' => $request->deskripsi,
            'toko_id' => $request->toko_id,
            'category_id' => $request->category_id,
            'stok' => $request->stok
        ]);

        if ($response->successful()) {
            return redirect()->route('products.index')
                ->with('success', 'Product updated successfully');
        }

        return back()->with('error', 'Failed to update product');
    }

    /**
     * Remove the specified resource from storage.
     * Delete product via Node.js API
     */
    public function destroy($id)
    {
        $response = Http::delete("http://localhost:3000/api/products/{$id}");

        if ($response->successful()) {
            return redirect()->route('products.index')
                ->with('success', 'Product deleted successfully');
        }

        return back()->with('error', 'Failed to delete product');
    }
}