<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    private $nodeApiUrl = 'http://localhost:3001/api';

    /**
     * Menampilkan halaman checkout - Consume Node.js API
     */
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
        }

        try {
            /** @var \App\Models\User $user */
            $user = Auth::user();

            // Fetch alamat dari Laravel (karena masih di Laravel DB)
            // Atau bisa juga dipindah ke Node.js jika mau
            $alamats = \App\Models\Alamat::where('user_id', $user->id)
                ->orderBy('is_default', 'desc')
                ->get();

            // Fetch cart data dari Node.js API
            $cartResponse = Http::timeout(30)
                ->get("{$this->nodeApiUrl}/cart/{$user->id}");

            if (!$cartResponse->successful()) {
                Log::warning('Failed to fetch cart from Node.js', [
                    'status' => $cartResponse->status()
                ]);
                
                return view('checkout', [
                    'alamats' => $alamats,
                    'cartItems' => collect([]),
                    'subtotal' => 0,
                    'deliveryFee' => 0,
                    'discount' => 0,
                    'total' => 0,
                    'error' => 'Gagal memuat data keranjang'
                ]);
            }

            $cartData = $cartResponse->json('data');
            $cartItems = collect($cartData['items'] ?? [])->map(function ($item) {
                return (object) [
                    'id' => $item['id'],
                    'product_id' => $item['product_id'],
                    'jumlah' => $item['jumlah'],
                    'subtotal' => $item['subtotal'],
                    'product' => (object) [
                        'id' => $item['product']['id'],
                        'nama' => $item['product']['nama'],
                        'harga' => $item['product']['harga'],
                        'imagePath' => $item['product']['imagePath'],
                        'deskripsi' => $item['product']['deskripsi'],
                    ]
                ];
            });

            // Hitung total
            $subtotal = $cartItems->sum('subtotal');
            $deliveryFee = 0;
            $discount = 0;
            $total = $subtotal + $deliveryFee - $discount;

            return view('checkout', compact('alamats', 'cartItems', 'subtotal', 'deliveryFee', 'discount', 'total'));

        } catch (\Exception $e) {
            Log::error('Failed to load checkout page', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage()
            ]);

            return view('checkout', [
                'alamats' => collect([]),
                'cartItems' => collect([]),
                'subtotal' => 0,
                'deliveryFee' => 0,
                'discount' => 0,
                'total' => 0,
                'error' => 'Gagal memuat halaman checkout: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Proses pembayaran dengan Xendit - Consume Node.js API
     */
    public function pay(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $request->validate([
            'order_id' => 'required|integer',
            'alamat_id' => 'required|integer',
            'total' => 'required|integer|min:1',
        ]);

        try {
            /** @var \App\Models\User $user */
            $user = Auth::user();

            Log::info('Processing payment', [
                'order_id' => $request->order_id,
                'user_id' => $user->id,
                'total' => $request->total
            ]);

            // Verify order exists via Node.js API
            $orderResponse = Http::timeout(30)
                ->get("{$this->nodeApiUrl}/orders/{$request->order_id}");

            if (!$orderResponse->successful()) {
                throw new \Exception('Order tidak ditemukan');
            }

            $orderData = $orderResponse->json('data');

            // Verify order belongs to user
            if ($orderData['user_id'] != $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses ke order ini'
                ], 403);
            }

            // Verify order status is pending
            if ($orderData['status'] !== 'pending') {
                throw new \Exception('Order sudah diproses sebelumnya');
            }

            // Update alamat if different (via Node.js API)
            if ($orderData['alamat_id'] != $request->alamat_id) {
                $updateResponse = Http::timeout(30)
                    ->put("{$this->nodeApiUrl}/orders/{$request->order_id}/alamat", [
                        'alamat_id' => $request->alamat_id
                    ]);

                if (!$updateResponse->successful()) {
                    Log::warning('Failed to update order alamat', [
                        'order_id' => $request->order_id,
                        'alamat_id' => $request->alamat_id
                    ]);
                }
            }

            // Create Xendit invoice
            \Xendit\Xendit::setApiKey(config('services.xendit.secret'));

            $invoice = \Xendit\Invoice::create([
                'external_id' => 'ORDER-' . $request->order_id . '-' . time(),
                'payer_email' => $user->email,
                'description' => 'Pembayaran Order #' . $request->order_id,
                'amount' => $request->total,
                'success_redirect_url' => route('payment.success'),
                'failure_redirect_url' => route('checkout'),
            ]);

            Log::info('Xendit invoice created', [
                'order_id' => $request->order_id,
                'invoice_id' => $invoice['id'],
                'invoice_url' => $invoice['invoice_url']
            ]);

            // Update order status to 'paid' via Node.js API
            $statusResponse = Http::timeout(30)
                ->put("{$this->nodeApiUrl}/orders/{$request->order_id}/status", [
                    'status' => 'paid'
                ]);

            if (!$statusResponse->successful()) {
                Log::error('Failed to update order status via Node.js API', [
                    'order_id' => $request->order_id,
                    'response' => $statusResponse->body()
                ]);
                throw new \Exception('Failed to update order status');
            }

            // Save invoice data to session for tracking
            session([
                'last_invoice_id' => $invoice['id'],
                'last_invoice_url' => $invoice['invoice_url'],
                'last_order_id' => $request->order_id
            ]);

            Log::info('Payment processed successfully', [
                'order_id' => $request->order_id,
                'invoice_id' => $invoice['id'],
                'status' => 'paid'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Payment berhasil diproses',
                'invoice_url' => $invoice['invoice_url'],
                'order_id' => $request->order_id,
            ]);

        } catch (\Xendit\Exceptions\ApiException $e) {
            Log::error('Xendit API error', [
                'message' => $e->getMessage(),
                'error_code' => $e->getErrorCode()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat invoice pembayaran: ' . $e->getMessage()
            ], 500);

        } catch (\Exception $e) {
            Log::error('Payment error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses pembayaran: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Callback setelah payment success
     */
    public function paymentSuccess()
    {
        $lastOrderId = session('last_order_id');
        
        if ($lastOrderId) {
            Log::info('Payment success callback', [
                'order_id' => $lastOrderId
            ]);
        }

        // Clear session data
        session()->forget(['last_invoice_id', 'last_invoice_url', 'last_order_id']);

        return redirect()->route('riwayat.pesanan')
            ->with('success', 'Pembayaran berhasil! Terima kasih atas pesanan Anda.');
    }

    /**
     * Callback untuk payment failure
     */
    public function paymentFailure()
    {
        Log::warning('Payment failure callback');

        return redirect()->route('checkout')
            ->with('error', 'Pembayaran gagal atau dibatalkan. Silakan coba lagi.');
    }
}