<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CheckoutController extends Controller
{
    private $nodeApiUrl;

    public function __construct()
    {
        $this->nodeApiUrl = config('services.node_api.url') . '/api';
    }

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
            'payment_method' => 'nullable|string',
        ]);

        try {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            Log::info('Local Checkout Pay: Started', ['order_id' => $request->order_id, 'user_id' => $user->id]);

            // 1. Verify order exists via Node.js API
            $apiUrl = "{$this->nodeApiUrl}/orders/{$request->order_id}";
            Log::info('Local Checkout Pay: Contacting Node API', ['url' => $apiUrl]);
            
            $orderResponse = Http::timeout(10)->get($apiUrl);

            if (!$orderResponse->successful()) {
                Log::error('Local Checkout Pay: Order Not Found in API', ['status' => $orderResponse->status(), 'body' => $orderResponse->body()]);
                throw new \Exception('Order tidak ditemukan di database API');
            }

            $orderData = $orderResponse->json('data');

            // 2. Verify order belongs to user
            if ($orderData['user_id'] != $user->id) {
                Log::warning('Local Checkout Pay: Access Denied', ['owner' => $orderData['user_id'], 'current' => $user->id]);
                return response()->json(['success' => false, 'message' => 'Akses ditolak'], 403);
            }

            // 3. Duitku POP Inquiry
            $merchantCode = config('services.duitku.merchant_code');
            $apiKey = config('services.duitku.api_key');
            
            if (!$merchantCode || !$apiKey) {
                Log::error('Local Checkout Pay: Missing Duitku Credentials in .env');
                throw new \Exception('Konfigurasi Duitku (Merchant Code/API Key) belum diatur di .env');
            }

            $merchantOrderId = 'ORDER-' . $request->order_id . '-' . time();
            $paymentAmount = (int) $request->total;
            $signature = md5($merchantCode . $merchantOrderId . $paymentAmount . $apiKey);

            $duitkuUrl = config('services.duitku.mode') === 'production' 
                ? 'https://passport.duitku.com/webapi/api/merchant/v2/inquiry'
                : 'https://sandbox.duitku.com/webapi/api/merchant/v2/inquiry';

            Log::info('Local Checkout Pay: Contacting Duitku', [
                'url' => $duitkuUrl, 
                'order' => $merchantOrderId,
                'method' => $request->payment_method ?? 'ALL'
            ]);

            $params = [
                'merchantCode' => $merchantCode,
                'paymentAmount' => $paymentAmount,
                'merchantOrderId' => $merchantOrderId,
                'productDetails' => 'Pembayaran Medcom Order #' . $request->order_id,
                'email' => $user->email,
                'callbackUrl' => route('duitku.callback'),
                'returnUrl' => route('riwayat.pesanan'),
                'signature' => $signature,
                'expiryPeriod' => 60
            ];

            if ($request->filled('payment_method')) {
                $params['paymentMethod'] = $request->payment_method;
            }

            $inquiryResponse = Http::timeout(15)->post($duitkuUrl, $params);

            if (!$inquiryResponse->successful()) {
                Log::error('Local Checkout Pay: Duitku Server Error', ['status' => $inquiryResponse->status(), 'body' => $inquiryResponse->body()]);
                throw new \Exception('Gagal menghubungi server Duitku (HTTP Error)');
            }

            $duitkuData = $inquiryResponse->json();

            if (($duitkuData['statusCode'] ?? '') !== '00') {
                Log::error('Local Checkout Pay: Duitku Business Error', $duitkuData);
                throw new \Exception('Duitku: ' . ($duitkuData['statusMessage'] ?? 'Unknown error'));
            }

            $reference = $duitkuData['reference'];

            // 4. Update order status and save reference via Node.js API
            Log::info('Local Checkout Pay: Updating Order Status in API');
            $statusResponse = Http::timeout(10)
                ->put("{$this->nodeApiUrl}/orders/{$request->order_id}/status", [
                    'status' => 'pending',
                    'payment_url' => null,
                    'payment_reference' => $reference
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Duitku Inquiry Berhasil',
                'reference' => $reference,
                'order_id' => $request->order_id,
            ]);

        } catch (\Exception $e) {
            Log::error('Local Checkout Pay: FATAL ERROR', ['msg' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
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