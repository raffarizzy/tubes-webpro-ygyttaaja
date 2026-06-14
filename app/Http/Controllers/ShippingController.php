<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ShippingController extends Controller
{
    private $apiKey;
    private $originId;
    private $nodeApiUrl;

    public function __construct()
    {
        $this->apiKey = config('services.klikresi.key');
        $this->originId = config('services.klikresi.origin_id');
        $this->nodeApiUrl = config('services.node_api.url') . '/api';
    }

    /**
     * Get shipping rates from KlikResi
     */
    public function getRates(Request $request)
    {
        $request->validate([
            'destination_id' => 'required|string',
            'items' => 'required|array|min:1',
        ]);

        try {
            $items = $request->items;

            // 1. Calculate total weight (in grams, default 1kg per item if weight not set)
            $totalWeightGrams = 0;
            foreach ($items as $item) {
                // Fetch actual weight from DB to be safe, or use default from request
                $productId = $item['product_id'] ?? $item['id'];
                $product = \App\Models\Product::find($productId);
                $productWeight = $product ? $product->berat : 1000; 
                
                $qty = $item['jumlah'] ?? $item['qty'] ?? 1;
                $totalWeightGrams += ($productWeight * $qty);
            }

            // 2. Convert to Kg and apply rounding logic (+0.3kg tolerance)
            // Rule: 1.30 -> 1kg, 1.31 -> 2kg
            $weightKg = $totalWeightGrams / 1000;
            $integerPart = floor($weightKg);
            $decimalPart = $weightKg - $integerPart;

            if ($decimalPart > 0.3) {
                $finalWeight = $integerPart + 1;
            } else {
                $finalWeight = max(1, $integerPart); // Minimum 1kg
            }

            Log::info('Calculating shipping rates', [
                'origin' => $this->originId,
                'destination' => $request->destination_id,
                'total_weight_grams' => $totalWeightGrams,
                'calculated_weight_kg' => $finalWeight
            ]);

            // 3. Call KlikResi API
            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey
            ])->post('https://klikresi.com/api/rates', [
                'origin_id' => $this->originId,
                'destination_id' => $request->destination_id,
                'weight' => $finalWeight
            ]);

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'weight' => $finalWeight,
                    'data' => $response->json()
                ]);
            }

            throw new \Exception($response->json('message') ?? 'Gagal mengambil tarif pengiriman dari KlikResi');

        } catch (\Exception $e) {
            Log::error('KlikResi Rates Error', [
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghitung ongkir: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Track shipment status
     */
    public function trackOrder($tracking_number, $courier_code)
    {
        try {
            $response = Http::withHeaders([
                'x-api-key' => $this->apiKey
            ])->get("https://klikresi.com/api/trackings/{$tracking_number}/couriers/{$courier_code}");

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'data' => $response->json()
                ]);
            }

            throw new \Exception($response->json('message') ?? 'Gagal mengambil data tracking');

        } catch (\Exception $e) {
            Log::error('KlikResi Tracking Error', [
                'message' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal melacak pesanan: ' . $e->getMessage()
            ], 500);
        }
    }
}
