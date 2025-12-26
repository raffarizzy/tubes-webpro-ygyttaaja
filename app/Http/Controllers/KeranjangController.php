<?php

namespace App\Http\Controllers;

use App\Models\Keranjang;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KeranjangController extends Controller
{
    /**
     * Display the shopping cart page
     */
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
        }

        /** @var User $user */
        $user = Auth::user();
        $keranjang = $user->keranjang()->with(['items.product'])->first();

        $cartItems = [];
        if ($keranjang) {
            $cartItems = $keranjang->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'jumlah' => $item->jumlah,
                    'harga' => $item->harga,
                    'product' => [
                        'id' => $item->product->id,
                        'nama' => $item->product->nama,
                        'deskripsi' => $item->product->deskripsi,
                        'imagePath' => $item->product->imagePath,
                        'stok' => $item->product->stok,
                    ]
                ];
            });
        }

        return view('keranjang', [
            'keranjangItems' => $cartItems
        ]);
    }

    /**
     * Get cart data as JSON
     */
    public function getCartData()
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        /** @var User $user */
        $user = Auth::user();
        $keranjang = $user->keranjang()->with(['items.product'])->first();

        $cartItems = [];
        $totalItems = 0;
        $totalPrice = 0;

        if ($keranjang) {
            $cartItems = $keranjang->items->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'jumlah' => $item->jumlah,
                    'harga' => $item->harga,
                    'subtotal' => $item->subtotal,
                    'product' => [
                        'id' => $item->product->id,
                        'nama' => $item->product->nama,
                        'deskripsi' => $item->product->deskripsi,
                        'imagePath' => $item->product->imagePath,
                        'stok' => $item->product->stok,
                    ]
                ];
            });

            $totalItems = $keranjang->total_items;
            $totalPrice = $keranjang->total_price;
        }

        return response()->json([
            'success' => true,
            'data' => [
                'items' => $cartItems,
                'total_items' => $totalItems,
                'total_price' => $totalPrice,
            ]
        ]);
    }

    /**
     * Clear all items from cart
     */
    public function clear()
    {
        try {
            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }

            /** @var User $user */
            $user = Auth::user();
            $keranjang = $user->keranjang;

            if ($keranjang) {
                $keranjang->items()->delete();
            }

            return response()->json([
                'success' => true,
                'message' => 'Keranjang berhasil dikosongkan'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengosongkan keranjang: ' . $e->getMessage()
            ], 500);
        }
    }
}