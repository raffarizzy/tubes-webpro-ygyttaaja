<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RatingController extends Controller
{
    private string $nodeApiUrl = 'http://localhost:3001';

    /**
     * Display ratings page (Riwayat Ulasan)
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

            return view('ratings.index', compact('ratings'));

        } catch (\Exception $e) {
            Log::error('Failed to fetch ratings', ['error' => $e->getMessage()]);
            return view('ratings.index', [
                'ratings' => collect(),
            ])->with('error', 'Koneksi ke server rating gagal');
        }
    }

    /**
     * Show form to create rating for a specific product
     */
    public function createRating($productId)
    {
        try {
            $product = Product::findOrFail($productId);

            // Cek apakah user sudah pernah memberikan rating untuk produk ini
            $response = Http::timeout(10)->get($this->nodeApiUrl . '/api/ratings', [
                'user_id' => auth()->id()
            ]);

            $existingRating = null;
            if ($response->successful()) {
                $ratings = collect($response->json('data'));
                $existingRating = $ratings->firstWhere('product_id', $productId);
            }

            return view('ratings.create', compact('product', 'existingRating'));

        } catch (\Exception $e) {
            return redirect()->route('riwayat.pesanan')->with('error', 'Gagal memuat form rating: ' . $e->getMessage());
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
            // Cek duplikasi sebelum post ke Node API
            $checkResponse = Http::timeout(10)->get($this->nodeApiUrl . '/api/ratings', [
                'user_id' => auth()->id()
            ]);

            if ($checkResponse->successful()) {
                $ratings = collect($checkResponse->json('data'));
                if ($ratings->contains('product_id', $validated['product_id'])) {
                    return back()
                        ->withErrors(['product_id' => 'Anda sudah memberikan ulasan untuk produk ini.'])
                        ->withInput();
                }
            }

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
