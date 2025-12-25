<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItems;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * SIMPAN ORDER (CHECKOUT)
     */
    public function store(Request $request)
    {
        \Log::info('Order store called', [
            'user_id' => Auth::id(),
            'request_data' => $request->all()
        ]);

        $request->validate([
            'alamat_id' => 'required|integer|exists:alamats,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.jumlah' => 'required|integer|min:1',
        ]);

        DB::beginTransaction();

        try {
            \Log::info('Validation passed, checking stock...');
            
            // 1️⃣ Validasi stok
            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);
                
                if ($product->stok < $item['jumlah']) {
                    return response()->json([
                        'message' => "Stok {$product->nama} tidak mencukupi"
                    ], 400);
                }
            }

            \Log::info('Stock validation passed, creating order...');

            // 2️⃣ Buat order dengan status pending dulu
            $order = Order::create([
                'user_id' => Auth::id(),
                'alamat_id' => $request->alamat_id,
                'total_harga' => 0,
                'status' => 'pending', // Nanti diubah setelah payment
            ]);

            \Log::info('Order created', ['order_id' => $order->id]);

            $total = 0;

            // 3️⃣ Simpan order items & kurangi stok
            foreach ($request->items as $item) {
                $product = Product::findOrFail($item['product_id']);
                $subtotal = $product->harga * $item['jumlah'];
                $total += $subtotal;

                OrderItems::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'nama_produk' => $product->nama,
                    'harga' => $product->harga,
                    'qty' => $item['jumlah'],
                    'subtotal' => $subtotal,
                ]);

                // Kurangi stok
                $product->decrement('stok', $item['jumlah']);
            }

            \Log::info('All items processed, updating total...', ['total' => $total]);

            // 4️⃣ Update total
            $order->update(['total_harga' => $total]);

            \Log::info('Order total updated, committing transaction...');

            DB::commit();

            \Log::info('Order completed successfully', ['order_id' => $order->id]);

            return response()->json([
                'message' => 'Order berhasil dibuat',
                'order' => $order->load('items.product'),
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Order creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'message' => 'Gagal membuat order',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * RIWAYAT PESANAN USER
     */
    public function history()
    {
        $orders = Order::with(['items.product', 'alamat'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($orders);
    }

    /**
     * CANCEL ORDER
     */
    public function cancel($id)
    {
        try {
            $order = Order::where('id', $id)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            // Hanya bisa cancel jika status pending
            if ($order->status !== 'pending') {
                return response()->json([
                    'message' => 'Hanya pesanan dengan status pending yang bisa dibatalkan'
                ], 400);
            }

            DB::beginTransaction();

            // Kembalikan stok produk
            foreach ($order->items as $item) {
                $product = Product::find($item->product_id);
                if ($product) {
                    $product->increment('stok', $item->qty);
                }
            }

            // Update status order menjadi cancelled
            $order->update(['status' => 'cancelled']);

            DB::commit();

            \Log::info('Order cancelled successfully', [
                'order_id' => $order->id,
                'user_id' => Auth::id()
            ]);

            return response()->json([
                'message' => 'Pesanan berhasil dibatalkan',
                'order' => $order->load(['items.product', 'alamat'])
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Pesanan tidak ditemukan'
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Order cancellation failed', [
                'order_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'message' => 'Gagal membatalkan pesanan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * DETAIL ORDER
     */
    public function show($id)
    {
        try {
            $order = Order::with(['items.product', 'alamat', 'user'])
                ->where('id', $id)
                ->where('user_id', Auth::id())
                ->firstOrFail();

            return response()->json($order, 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Pesanan tidak ditemukan'
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Failed to fetch order detail', [
                'order_id' => $id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'message' => 'Gagal mengambil detail pesanan',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}