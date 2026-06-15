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

        // Fetch alamat dari Node.js API
        $alamatResponse = Http::timeout(30)
            ->get("{$this->nodeApiUrl}/alamat/{$user->id}");

        if (!$alamatResponse->successful()) {
            Log::warning('Failed to fetch alamat from Node.js', [
                'status' => $alamatResponse->status()
            ]);
            $alamats = collect([]);
        } else {
            $alamats = collect($alamatResponse->json('data'));
        }

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
     * Proses pembayaran dengan Duitku - Consume Node.js API
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

            // 1. Verify order exists via Node.js API
            $orderResponse = Http::timeout(30)
                ->get("{$this->nodeApiUrl}/orders/{$request->order_id}");

            if (!$orderResponse->successful()) {
                throw new \Exception('Order tidak ditemukan');
            }

            $orderData = $orderResponse->json('data');

            // 2. Verify order belongs to user
            if ($orderData['user_id'] != $user->id) {
                return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
            }

            // 3. Duitku POP Inquiry
            $merchantCode = config('services.duitku.merchant_code');
            $apiKey = config('services.duitku.api_key');
            $merchantOrderId = 'ORDER-' . $request->order_id . '-' . time();
            $paymentAmount = (int) $request->total;
            
            // Signature formula: md5(merchantCode + merchantOrderId + paymentAmount + apiKey)
            $signature = md5($merchantCode . $merchantOrderId . $paymentAmount . $apiKey);

            $duitkuUrl = config('services.duitku.mode') === 'production' 
                ? 'https://passport.duitku.com/webapi/api/merchant/v2/inquiry'
                : 'https://sandbox.duitku.com/webapi/api/merchant/v2/inquiry';

            $inquiryResponse = Http::timeout(30)->post($duitkuUrl, [
                'merchantCode' => $merchantCode,
                'paymentAmount' => $paymentAmount,
                'merchantOrderId' => $merchantOrderId,
                'productDetails' => 'Pembayaran Medcom Order #' . $request->order_id,
                'email' => $user->email,
                'callbackUrl' => route('duitku.callback'),
                'returnUrl' => route('riwayat.pesanan'),
                'signature' => $signature,
                'expiryPeriod' => 60 // 60 menit
            ]);

            if (!$inquiryResponse->successful()) {
                throw new \Exception('Gagal menghubungi server Duitku');
            }

            $duitkuData = $inquiryResponse->json();

            if (($duitkuData['statusCode'] ?? '') !== '00') {
                throw new \Exception('Duitku Error: ' . ($duitkuData['statusMessage'] ?? 'Unknown error'));
            }

            $reference = $duitkuData['reference'];

            // 4. Update order status and save reference via Node.js API
            $statusResponse = Http::timeout(30)
                ->put("{$this->nodeApiUrl}/orders/{$request->order_id}/status", [
                    'status' => 'pending',
                    'payment_url' => null, // Duitku POP uses reference
                    'payment_reference' => $reference // New field if needed, or repurposed
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Duitku Inquiry Berhasil',
                'reference' => $reference,
                'order_id' => $request->order_id,
            ]);

        } catch (\Exception $e) {
            Log::error('Duitku Payment Error', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
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