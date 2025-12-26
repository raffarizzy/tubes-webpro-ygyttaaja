<?php

namespace App\Http\Controllers;

use App\Models\BarangKeranjang;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BarangKeranjangController extends Controller
{
    /**
     * Add item to cart
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'product_id' => 'required|exists:products,id',
                'jumlah' => 'required|integer|min:1',
            ]);

            $product = Product::findOrFail($validated['product_id']);

            if ($validated['jumlah'] > $product->stok) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stok tidak mencukupi'
                ], 400);
            }

            /** @var User $user */
            $user = Auth::user();
            $keranjang = $user->getOrCreateCart();

            $existingItem = BarangKeranjang::where('keranjang_id', $keranjang->id)
                ->where('product_id', $validated['product_id'])
                ->first();

            if ($existingItem) {
                $newJumlah = $existingItem->jumlah + $validated['jumlah'];

                if ($newJumlah > $product->stok) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Total jumlah melebihi stok tersedia'
                    ], 400);
                }

                $existingItem->update(['jumlah' => $newJumlah]);
                $item = $existingItem;
            } else {
                $item = BarangKeranjang::create([
                    'keranjang_id' => $keranjang->id,
                    'product_id' => $validated['product_id'],
                    'jumlah' => $validated['jumlah'],
                    'harga' => $product->harga,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil ditambahkan ke keranjang',
                'data' => $item->load('product')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan ke keranjang: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update cart item quantity
     */
    public function update(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'jumlah' => 'required|integer|min:1',
            ]);

            $item = BarangKeranjang::findOrFail($id);

            if ($item->keranjang->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            if ($validated['jumlah'] > $item->product->stok) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jumlah melebihi stok tersedia'
                ], 400);
            }

            $item->update(['jumlah' => $validated['jumlah']]);

            return response()->json([
                'success' => true,
                'message' => 'Jumlah berhasil diperbarui',
                'data' => $item->load('product')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui jumlah: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove item from cart
     */
    public function destroy($id)
    {
        try {
            $item = BarangKeranjang::findOrFail($id);

            if ($item->keranjang->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 403);
            }

            $productName = $item->product->nama;
            $item->delete();

            return response()->json([
                'success' => true,
                'message' => $productName . ' berhasil dihapus dari keranjang'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus item: ' . $e->getMessage()
            ], 500);
        }
    }
}