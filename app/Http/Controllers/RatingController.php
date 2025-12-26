<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use App\Models\Product;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ratings = Rating::with('product')
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        $ratedProductIds = $ratings->pluck('product_id');

        $products = Product::whereNotIn('id', $ratedProductIds)->get();

        return view('ratings.index', compact('ratings', 'products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'rating'     => 'required|integer|min:1|max:5',
            'review'     => 'required|string|max:1000',
        ]);

        // Cegah user rating produk yang sama 2x
        $alreadyRated = Rating::where('user_id', auth()->id())
            ->where('product_id', $validated['product_id'])
            ->exists();

        if ($alreadyRated) {
            return back()
                ->withErrors([
                    'product_id' => 'Produk ini sudah kamu beri rating.'
                ])
                ->withInput();
        }

        // Simpan rating
        Rating::create([
            'user_id'    => auth()->id(),
            'product_id' => $validated['product_id'],
            'rating'     => $validated['rating'],
            'review'     => $validated['review'],
        ]);

        return redirect()
            ->route('ratings.index')
            ->with('success', 'Rating berhasil ditambahkan.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Rating $rating)
    {
        if ($rating->user_id !== auth()->id()) {
            abort(403);
        }

        $rating->delete();

        return back()->with('success', 'Rating berhasil dihapus');
    }
}