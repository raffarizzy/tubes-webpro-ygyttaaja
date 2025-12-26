<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OrderProxyController extends Controller
{
    private $nodeApiUrl = 'http://localhost:3001/api/orders';

    /**
     * Create order via Node.js API
     */
    public function createOrder(Request $request)
    {
        try {
            Log::info('OrderProxy: Creating order', ['data' => $request->all()]);

            // Forward request to Node.js API
            $response = Http::timeout(30)->post($this->nodeApiUrl, [
                'user_id' => $request->user_id,
                'alamat_id' => $request->alamat_id,
                'items' => $request->items,
            ]);

            Log::info('OrderProxy: Node.js API response', [
                'status' => $response->status(),
                'body' => $response->json()
            ]);

            // Return Node.js API response to frontend
            return response()->json(
                $response->json(),
                $response->status()
            );

        } catch (\Exception $e) {
            Log::error('OrderProxy: Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat order',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get order by ID via Node.js API
     */
    public function getOrder($orderId)
    {
        try {
            $response = Http::timeout(30)->get("{$this->nodeApiUrl}/{$orderId}");

            return response()->json(
                $response->json(),
                $response->status()
            );

        } catch (\Exception $e) {
            Log::error('OrderProxy: Get order error', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil order',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user orders via Node.js API
     */
    public function getUserOrders($userId)
    {
        try {
            $response = Http::timeout(30)->get("{$this->nodeApiUrl}/user/{$userId}");

            return response()->json(
                $response->json(),
                $response->status()
            );

        } catch (\Exception $e) {
            Log::error('OrderProxy: Get user orders error', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil daftar order',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}