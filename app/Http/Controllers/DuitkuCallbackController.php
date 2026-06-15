<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DuitkuCallbackController extends Controller
{
    private $nodeApiUrl;

    public function __construct()
    {
        $this->nodeApiUrl = config('services.node_api.url') . '/api';
    }

    /**
     * Duitku Callback Handler
     * URL: domain.com/api/duitku/callback
     */
    public function handle(Request $request)
    {
        try {
            Log::info('Duitku Callback Received', $request->all());

            $apiKey = config('services.duitku.api_key');
            $merchantCode = $request->merchantCode;
            $amount = $request->amount;
            $merchantOrderId = $request->merchantOrderId;
            $signature = $request->signature;
            $resultCode = $request->resultCode; // 00 = success

            // 1. Verifikasi Signature untuk keamanan
            // Rumus: md5(merchantCode + amount + merchantOrderId + apiKey)
            $params = $merchantCode . $amount . $merchantOrderId . $apiKey;
            $calcSignature = md5($params);

            if ($signature !== $calcSignature) {
                Log::error('Duitku Callback: Invalid Signature', [
                    'received' => $signature,
                    'calculated' => $calcSignature
                ]);
                return response()->json(['message' => 'Invalid Signature'], 400);
            }

            // 2. Jika Pembayaran Berhasil (00)
            if ($resultCode == '00') {
                // Ekstrak Order ID dari merchantOrderId (biasanya format: ORDER-123-xxxx)
                $orderId = str_replace('ORDER-', '', explode('-', $merchantOrderId)[1]);

                Log::info("Duitku Callback: Payment Success for Order #$orderId");

                // 3. Update Status di Node.js API
                $apiUrl = "{$this->nodeApiUrl}/orders/{$orderId}/status";
                $response = Http::timeout(10)->put($apiUrl, [
                    'status' => 'paid'
                ]);

                if ($response->successful()) {
                    return response('OK', 200); // Duitku butuh respon 'OK'
                } else {
                    Log::error("Duitku Callback: Failed to sync with Node API for Order #$orderId", [
                        'status' => $response->status(),
                        'body' => $response->body()
                    ]);
                    return response('Internal Server Error', 500);
                }
            }

            return response('OK', 200);

        } catch (\Exception $e) {
            Log::error('Duitku Callback Error: ' . $e->getMessage());
            return response('Internal Server Error', 500);
        }
    }
}
