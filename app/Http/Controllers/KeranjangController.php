<?php

namespace App\Http\Controllers;

use App\Models\Keranjang;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

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

        // Fetch cart data from Node.js API
        try {
            $response = Http::get("http://localhost:3001/api/cart/{$user->id}");
            $cartItems = $response->successful() ? $response->json('data') : [];
        } catch (\Exception $e) {
            $cartItems = [];
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

        try {
            // Fetch from Node.js API
            $response = Http::get("http://localhost:3001/api/cart/{$user->id}");

            if ($response->successful()) {
                $cartItems = $response->json('data');

                $totalItems = array_sum(array_column($cartItems, 'jumlah'));
                $totalPrice = array_sum(array_map(function($item) {
                    return $item['harga'] * $item['jumlah'];
                }, $cartItems));

                return response()->json([
                    'success' => true,
                    'data' => [
                        'items' => $cartItems,
                        'total_items' => $totalItems,
                        'total_price' => $totalPrice,
                    ]
                ]);
            }

            throw new \Exception('Failed to fetch cart data');

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data keranjang: ' . $e->getMessage()
            ], 500);
        }
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

            // Call Node.js API to clear cart
            $response = Http::delete("http://localhost:3001/api/cart/{$user->id}/clear");

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Keranjang berhasil dikosongkan'
                ]);
            }

            throw new \Exception('Failed to clear cart');

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengosongkan keranjang: ' . $e->getMessage()
            ], 500);
        }
    }
}