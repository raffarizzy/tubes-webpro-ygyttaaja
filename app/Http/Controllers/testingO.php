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