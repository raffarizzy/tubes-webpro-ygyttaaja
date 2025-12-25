<?php

namespace App\Http\Controllers;

use App\Models\Keranjang;
use App\Models\BarangKeranjang;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class KeranjangController extends Controller
{
    /**
     * Tampilkan halaman keranjang
     */
    public function index()
    {
        $user = Auth::user();
        
        // Ambil atau buat keranjang untuk user
        $keranjang = Keranjang::firstOrCreate(
            ['user_id' => $user->id, 'status' => 'active']
        );

        // Eager load items dengan product
        $keranjang->load(['items.product']);

        // Format data untuk JavaScript
        $keranjangItems = $keranjang->items->map(function($item) {
            return [
                'id' => $item->id,
                'jumlah' => $item->jumlah,
                'harga' => $item->harga,
                'product' => [
                    'id' => $item->product->id,
                    'nama' => $item->product->nama,
                    'deskripsi' => $item->product->deskripsi,
                    'imagePath' => $item->product->imagePath,
                    'stok' => $item->product->stok
                ]
            ];
        });

        return view('keranjang', compact('keranjang', 'keranjangItems'));
    }

    /**
     * Tambah produk ke keranjang (via AJAX)
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'jumlah' => 'required|integer|min:1'
        ]);

        $user = Auth::user();
        $product = Product::findOrFail($request->product_id);

        // Cek stok
        if ($request->jumlah > $product->stok) {
            return response()->json([
                'success' => false,
                'message' => "Stok {$product->nama} hanya tersedia {$product->stok} item"
            ], 400);
        }

        DB::beginTransaction();
        try {
            // Ambil atau buat keranjang
            $keranjang = Keranjang::firstOrCreate(
                ['user_id' => $user->id, 'status' => 'active']
            );

            // Cek apakah produk sudah ada di keranjang
            $barangKeranjang = BarangKeranjang::where('keranjang_id', $keranjang->id)
                ->where('product_id', $product->id)
                ->first();

            if ($barangKeranjang) {
                // Update jumlah jika sudah ada
                $jumlahBaru = $barangKeranjang->jumlah + $request->jumlah;
                
                if ($jumlahBaru > $product->stok) {
                    return response()->json([
                        'success' => false,
                        'message' => "Total jumlah melebihi stok tersedia"
                    ], 400);
                }

                $barangKeranjang->update([
                    'jumlah' => $jumlahBaru
                ]);

                $message = "Jumlah {$product->nama} di keranjang diperbarui";
            } else {
                // Tambah item baru
                BarangKeranjang::create([
                    'keranjang_id' => $keranjang->id,
                    'product_id' => $product->id,
                    'jumlah' => $request->jumlah,
                    'harga' => $product->harga // Simpan harga saat ditambahkan
                ]);

                $message = "{$product->nama} berhasil ditambahkan ke keranjang";
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => $message,
                'total_items' => $keranjang->fresh()->total_items
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan ke keranjang'
            ], 500);
        }
    }

    /**
     * Update jumlah item di keranjang
     */
    public function update(Request $request, $itemId)
    {
        $request->validate([
            'jumlah' => 'required|integer|min:1'
        ]);

        $user = Auth::user();
        
        // Ambil item dan pastikan milik user yang login
        $barangKeranjang = BarangKeranjang::whereHas('keranjang', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->findOrFail($itemId);

        $product = $barangKeranjang->product;

        // Cek stok
        if ($request->jumlah > $product->stok) {
            return response()->json([
                'success' => false,
                'message' => "Stok {$product->nama} hanya tersedia {$product->stok} item"
            ], 400);
        }

        $barangKeranjang->update([
            'jumlah' => $request->jumlah
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Jumlah berhasil diperbarui',
            'subtotal' => $barangKeranjang->subtotal,
            'total_harga' => $barangKeranjang->keranjang->total_harga
        ]);
    }

    /**
     * Hapus item dari keranjang
     */
    public function destroy($itemId)
    {
        $user = Auth::user();
        
        // Ambil item dan pastikan milik user yang login
        $barangKeranjang = BarangKeranjang::whereHas('keranjang', function($query) use ($user) {
            $query->where('user_id', $user->id);
        })->findOrFail($itemId);

        $productName = $barangKeranjang->product->nama;
        $barangKeranjang->delete();

        return response()->json([
            'success' => true,
            'message' => "{$productName} berhasil dihapus dari keranjang",
            'total_items' => $barangKeranjang->keranjang->fresh()->total_items
        ]);
    }

    /**
     * Kosongkan keranjang
     */
    public function clear()
    {
        $user = Auth::user();
        
        $keranjang = Keranjang::where('user_id', $user->id)
            ->where('status', 'active')
            ->first();

        if ($keranjang) {
            $keranjang->items()->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Keranjang berhasil dikosongkan'
        ]);
    }

    /**
     * Get total items untuk badge (via AJAX)
     */
    public function getCartCount()
    {
        $user = Auth::user();
        
        $keranjang = Keranjang::where('user_id', $user->id)
            ->where('status', 'active')
            ->first();

        $totalItems = $keranjang ? $keranjang->total_items : 0;

        return response()->json([
            'total_items' => $totalItems
        ]);
    }
}