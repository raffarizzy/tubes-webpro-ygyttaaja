<?php

namespace App\Http\Controllers;

use App\Models\Toko;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TokoController extends Controller
{
    // Base URL Node.js API
    private $nodeApiUrl = 'http://localhost:3001/api/toko';

    /**
     * Display toko profile or redirect to create
     */
    public function index()
    {
        try {
            // Panggil Node.js API untuk cek apakah user punya toko
            $response = Http::withHeaders([
                'x-user-id' => auth()->id()
            ])->get($this->nodeApiUrl . '/check');

            if (!$response->successful()) {
                throw new \Exception('Gagal mengecek status toko');
            }

            $result = $response->json();

            // Jika belum punya toko, redirect ke create
            if (!$result['data']['hasToko']) {
                return redirect()->route('toko.create');
            }

            // Jika sudah punya toko, ambil data dari API
            $tokoResponse = Http::withHeaders([
                'x-user-id' => auth()->id()
            ])->get($this->nodeApiUrl . '/my-toko');

            if (!$tokoResponse->successful()) {
                throw new \Exception('Gagal mengambil data toko');
            }

            $toko = $tokoResponse->json()['data'];

            $toko = (object) $tokoData;

            return view('profil_toko', compact('toko'));

        } catch (\Exception $e) {
            Log::error('Error in TokoController@index: ' . $e->getMessage());
            
            // Fallback ke database Laravel jika API error
            $toko = Toko::where('user_id', auth()->id())->first();

            if (!$toko) {
                return redirect()->route('toko.create');
            }

            return view('profil_toko', compact('toko'));
        }
    }

    /**
     * Show create toko form
     */
    public function create()
    {
        // Cek dulu apakah user sudah punya toko (dari API)
        try {
            $response = Http::withHeaders([
                'x-user-id' => auth()->id()
            ])->get($this->nodeApiUrl . '/check');

            if ($response->successful()) {
                $result = $response->json();
                
                if ($result['data']['hasToko']) {
                    return redirect()->route('toko.index')
                        ->with('error', 'Anda sudah memiliki toko');
                }
            }
        } catch (\Exception $e) {
            Log::error('Error checking toko: ' . $e->getMessage());
        }

        return view('toko.create');
    }

    /**
     * Store new toko
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_toko' => 'required|string|max:255',
            'deskripsi_toko' => 'required|string',
            'lokasi' => 'required|string|max:255',
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        try {
            // 1. Upload logo ke storage Laravel
            $logoPath = $request->file('logo')->store('toko', 'public');
            $logoUrl = Storage::url($logoPath);

            // 2. Simpan ke database Laravel (sebagai backup)
            $tokoLocal = Toko::create([
                'user_id' => auth()->id(),
                'nama_toko' => $request->nama_toko,
                'deskripsi_toko' => $request->deskripsi_toko,
                'lokasi' => $request->lokasi,
                'logo_path' => $logoPath
            ]);

            // 3. Kirim ke Node.js API
            $response = Http::withHeaders([
                'x-user-id' => auth()->id()
            ])->post($this->nodeApiUrl, [
                'nama_toko' => $request->nama_toko,
                'deskripsi_toko' => $request->deskripsi_toko,
                'lokasi' => $request->lokasi,
                'logo_path' => $logoPath
            ]);

            if (!$response->successful()) {
                // Jika API gagal, rollback database Laravel
                $tokoLocal->delete();
                Storage::disk('public')->delete($logoPath);
                
                throw new \Exception('Gagal menyimpan toko ke API');
            }

            return redirect()->route('toko.index')
                ->with('success', 'Toko berhasil dibuat!');

        } catch (\Exception $e) {
            Log::error('Error creating toko: ' . $e->getMessage());
            
            // Hapus file jika ada error
            if (isset($logoPath)) {
                Storage::disk('public')->delete($logoPath);
            }
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gagal membuat toko: ' . $e->getMessage());
        }
    }

    /**
     * Update toko
     */
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'nama_toko' => 'required|string|max:255',
                'deskripsi_toko' => 'required|string',
                'lokasi' => 'required|string|max:255',
                'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            // 1. Update di database Laravel
            $toko = Toko::findOrFail($id);

            // Cek ownership
            if ($toko->user_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk mengupdate toko ini'
                ], 403);
            }

            $logoPath = $toko->logo_path;

            // Handle upload logo baru
            if ($request->hasFile('logo')) {
                // Hapus logo lama
                if ($toko->logo_path) {
                    Storage::disk('public')->delete($toko->logo_path);
                }

                // Upload logo baru
                $logoPath = $request->file('logo')->store('toko', 'public');
            }

            // Update database Laravel
            $toko->update([
                'nama_toko' => $request->nama_toko,
                'deskripsi_toko' => $request->deskripsi_toko,
                'lokasi' => $request->lokasi,
                'logo_path' => $logoPath
            ]);

            // 2. Update ke Node.js API
            $apiData = [
                'nama_toko' => $request->nama_toko,
                'deskripsi_toko' => $request->deskripsi_toko,
                'lokasi' => $request->lokasi,
                'logo_path' => $logoPath
            ];

            $response = Http::withHeaders([
                'x-user-id' => auth()->id()
            ])->patch($this->nodeApiUrl . '/' . $id, $apiData);

            if (!$response->successful()) {
                Log::warning('Failed to update toko in Node.js API: ' . $response->body());
                // Tidak rollback karena minimal sudah tersimpan di Laravel
            }

            return response()->json([
                'success' => true,
                'message' => 'Toko berhasil diperbarui',
                'logo_path' => $logoPath,
                'logo_url' => Storage::url($logoPath)
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            Log::error('Error updating toko: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui toko: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete toko
     */
    public function destroy($id)
    {
        try {
            $toko = Toko::findOrFail($id);

            // Cek ownership
            if ($toko->user_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak memiliki akses untuk menghapus toko ini'
                ], 403);
            }

            // 1. Hapus dari Node.js API
            $response = Http::withHeaders([
                'x-user-id' => auth()->id()
            ])->delete($this->nodeApiUrl . '/' . $id);

            if (!$response->successful()) {
                Log::warning('Failed to delete toko in Node.js API: ' . $response->body());
            }

            // 2. Hapus logo dari storage
            if ($toko->logo_path) {
                Storage::disk('public')->delete($toko->logo_path);
            }

            // 3. Hapus dari database Laravel
            $toko->delete();

            return response()->json([
                'success' => true,
                'message' => 'Toko berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            Log::error('Error deleting toko: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus toko: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get toko data via API (helper method)
     */
    public function getTokoFromApi($tokoId = null)
    {
        try {
            $url = $tokoId 
                ? $this->nodeApiUrl . '/' . $tokoId 
                : $this->nodeApiUrl . '/my-toko';

            $response = Http::withHeaders([
                'x-user-id' => auth()->id()
            ])->get($url);

            if ($response->successful()) {
                return $response->json()['data'];
            }

            return null;

        } catch (\Exception $e) {
            Log::error('Error getting toko from API: ' . $e->getMessage());
            return null;
        }
    }
}