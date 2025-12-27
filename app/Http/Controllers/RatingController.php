<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class RatingController extends Controller
{
    private string $nodeApiUrl = 'http://localhost:3001';

    /**
     * Display ratings page
     */
    public function index()
    {
        try {
            // Ambil ratings user dari Node.js API
            $response = Http::timeout(10)->get($this->nodeApiUrl . '/api/ratings', [
                'user_id' => auth()->id()
            ]);

            // Parse response jadi collection
            $ratings = collect();
            if ($response->successful()) {
                $ratingsData = $response->json('data') ?? [];
                $ratings = collect($ratingsData);
            }

            // Ambil ID produk yang sudah dirating
            $ratedProductIds = $ratings->pluck('product_id')->toArray();

            // Ambil produk yang BELUM dirating
            $products = Product::whereNotIn('id', $ratedProductIds)->get();

            return view('ratings.index', compact('ratings', 'products'));

        } catch (\Exception $e) {
            return view('ratings.index', [
                'ratings' => collect(),
                'products' => Product::all(),
            ])->with('error', 'Koneksi ke server rating gagal');
        }
    }

    /**
     * Store rating via Node.js API
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'rating'     => 'required|integer|min:1|max:5',
            'review'     => 'required|string|max:1000',
        ]);

        try {
            $response = Http::timeout(10)->post(
                $this->nodeApiUrl . '/api/ratings',
                [
                    'user_id'    => auth()->id(),
                    'product_id' => $validated['product_id'],
                    'rating'     => $validated['rating'],
                    'review'     => $validated['review'],
                ]
            );

            if ($response->failed()) {
                $errorMessage = $response->json('message') ?? 'Gagal menambahkan rating';
                return back()
                    ->withErrors(['product_id' => $errorMessage])
                    ->withInput();
            }

            return redirect()
                ->route('ratings.index')
                ->with('success', 'Rating berhasil ditambahkan');

        } catch (\Exception $e) {
            return back()
                ->withErrors(['product_id' => 'Koneksi ke server rating gagal'])
                ->withInput();
        }
    }

    /**
     * Delete rating via Node.js API
     */
    public function destroy($id)
    {
        try {
            $response = Http::timeout(10)->delete(
                $this->nodeApiUrl . "/api/ratings/{$id}",
                ['user_id' => auth()->id()]
            );

            if ($response->successful()) {
                return redirect()
                    ->route('ratings.index')
                    ->with('success', 'Rating berhasil dihapus');
            }

            return back()->with('error', 'Gagal menghapus rating');

        } catch (\Exception $e) {
            return back()->with('error', 'Koneksi ke server rating gagal');
        }
    }
}