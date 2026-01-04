<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    private $nodeApiUrl = 'http://localhost:3001/api';

    /**
     * SIMPAN ORDER (CHECKOUT) - Consume Node.js API
     */
    public function store(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        Log::info('Order store called', [
            'user_id' => Auth::id(),
            'request_data' => $request->all()
        ]);

        $request->validate([
            'alamat_id' => 'required|integer',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer',
            'items.*.jumlah' => 'required|integer|min:1',
        ]);

        try {
            /** @var User $user */
            $user = Auth::user();

            // Call Node.js API to create order
            $response = Http::timeout(30)->post("{$this->nodeApiUrl}/orders", [
                'user_id' => $user->id,
                'alamat_id' => $request->alamat_id,
                'items' => $request->items,
            ]);

            if ($response->successful()) {
                $result = $response->json();
                
                Log::info('Order created successfully via Node.js API', [
                    'order_id' => $result['data']['id'] ?? null
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Order berhasil dibuat',
                    'data' => $result['data'],
                ], 201);
            }

            throw new \Exception($response->json('message') ?? 'Failed to create order');

        } catch (\Exception $e) {
            Log::error('Order creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat order: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * RIWAYAT PESANAN USER (API) - Consume Node.js API
     */
    public function history()
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        try {
            /** @var User $user */
            $user = Auth::user();
            
            $response = Http::timeout(30)
                ->get("{$this->nodeApiUrl}/history/{$user->id}");

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'data' => $response->json('data')
                ]);
            }

            throw new \Exception('Failed to fetch order history');

        } catch (\Exception $e) {
            Log::error('Failed to fetch order history', [
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil riwayat pesanan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * CANCEL ORDER - VERSI FORM (REDIRECT) - Consume Node.js API
     */
    public function cancelForm($id)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
        }

        try {
            /** @var User $user */
            $user = Auth::user();

            // Call Node.js API to cancel order
            $response = Http::timeout(30)
                ->post("{$this->nodeApiUrl}/history/cancel/{$id}");

            if ($response->successful()) {
                Log::info('Order cancelled successfully via Node.js API', [
                    'order_id' => $id,
                    'user_id' => $user->id
                ]);

                return redirect()->route('riwayat.pesanan')
                    ->with('success', 'Pesanan berhasil dibatalkan!');
            }

            throw new \Exception($response->json('message') ?? 'Failed to cancel order');

        } catch (\Exception $e) {
            Log::error('Order cancellation failed', [
                'order_id' => $id,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->back()
                ->with('error', 'Gagal membatalkan pesanan: ' . $e->getMessage());
        }
    }

    /**
     * CANCEL ORDER - VERSI API - Consume Node.js API
     */
    public function cancel($id)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        try {
            /** @var User $user */
            $user = Auth::user();

            // Call Node.js API to cancel order
            $response = Http::timeout(30)
                ->post("{$this->nodeApiUrl}/history/cancel/{$id}");

            if ($response->successful()) {
                $result = $response->json();
                
                Log::info('Order cancelled successfully via Node.js API', [
                    'order_id' => $id,
                    'user_id' => $user->id
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Pesanan berhasil dibatalkan',
                    'data' => $result['data']
                ], 200);
            }

            throw new \Exception($response->json('message') ?? 'Failed to cancel order');

        } catch (\Exception $e) {
            Log::error('Order cancellation failed', [
                'order_id' => $id,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal membatalkan pesanan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * DETAIL ORDER - Consume Node.js API
     */
    public function show($id)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
        }

        try {
            /** @var User $user */
            $user = Auth::user();

            // Fetch order detail from Node.js API
            $response = Http::timeout(30)
                ->get("{$this->nodeApiUrl}/history/order/{$id}");

            if ($response->successful()) {
                $orderData = $response->json('data');

                // Convert to object for Blade compatibility
                $order = $this->convertToObject($orderData);

                // Verify ownership
                if ($order->user_id != $user->id) {
                    return redirect()->route('riwayat.pesanan')
                        ->with('error', 'Anda tidak memiliki akses ke pesanan ini');
                }

                return view('order_detail', compact('order'));
            }

            throw new \Exception('Order tidak ditemukan');

        } catch (\Exception $e) {
            Log::error('Failed to fetch order detail', [
                'order_id' => $id,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->route('riwayat.pesanan')
                ->with('error', 'Gagal mengambil detail pesanan: ' . $e->getMessage());
        }
    }

    /**
     * TAMPILKAN HALAMAN RIWAYAT PESANAN - Consume Node.js API
     */
    public function riwayatPesanan()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
        }

        try {
            /** @var User $user */
            $user = Auth::user();

            // Fetch cart data from Node.js API
            $response = Http::timeout(30)
                ->get("{$this->nodeApiUrl}/history/{$user->id}");

            Log::info('Fetched orders from Node.js API', [
                'user_id' => $user->id,
                'status' => $response->status()
            ]);

            if ($response->successful()) {
                $ordersData = $response->json('data') ?? [];

                // Convert to objects for Blade
                $orders = collect($ordersData)->map(function ($order) {
                    return $this->convertToObject($order);
                });

                Log::info('Loaded orders for user', [
                    'user_id' => $user->id,
                    'order_count' => $orders->count()
                ]);

                return view('riwayat_pesanan', [
                    'orders' => $orders
                ]);
            }

            throw new \Exception('Failed to fetch orders');

        } catch (\Exception $e) {
            Log::error('Failed to load order history page', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return view('riwayat_pesanan', [
                'orders' => collect([]),
                'error' => 'Gagal memuat riwayat pesanan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get order data as JSON - Consume Node.js API
     */
    public function getOrderData($id)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        try {
            /** @var User $user */
            $user = Auth::user();

            // Fetch from Node.js API
            $response = Http::timeout(30)
                ->get("{$this->nodeApiUrl}/history/order/{$id}");

            if ($response->successful()) {
                $orderData = $response->json('data');

                // Verify ownership
                if ($orderData['user_id'] != $user->id) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Anda tidak memiliki akses ke pesanan ini'
                    ], 403);
                }

                return response()->json([
                    'success' => true,
                    'data' => $orderData
                ]);
            }

            throw new \Exception('Order tidak ditemukan');

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper: Convert array to object recursively
     */
    private function convertToObject($data)
    {
        if (is_array($data)) {
            $obj = (object) $data;

            // Convert items
            if (isset($obj->items) && is_array($obj->items)) {
                $obj->items = collect($obj->items)->map(function ($item) {
                    $itemObj = (object) $item;
                    if (isset($item['product']) && is_array($item['product'])) {
                        $itemObj->product = (object) $item['product'];
                    }
                    return $itemObj;
                });
            }

            // Convert alamat
            if (isset($obj->alamat) && is_array($obj->alamat)) {
                $obj->alamat = (object) $obj->alamat;
            }

            // Convert user if exists
            if (isset($obj->user) && is_array($obj->user)) {
                $obj->user = (object) $obj->user;
            }

            return $obj;
        }

        return $data;
    }
}