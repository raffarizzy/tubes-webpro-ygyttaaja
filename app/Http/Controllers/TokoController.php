<?php

namespace App\Http\Controllers;

use App\Models\Toko;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TokoController extends Controller
{
    public function index()
    {
        $toko = Toko::where('user_id', auth()->id())->first();

        if (!$toko) {
            // BELUM PUNYA TOKO
            return redirect()->route('toko.create');
        }

        // SUDAH PUNYA TOKO
        return view('profil_toko', compact('toko'));
    }


    public function create()
    {
        return view('toko.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_toko' => 'required',
            'deskripsi_toko' => 'required',
            'lokasi' => 'required',
            'logo' => 'required|image'
        ]);

        $path = $request->file('logo')->store('toko', 'public');

        Toko::create([
            'user_id' => auth()->id(),
            'nama_toko' => $request->nama_toko,
            'deskripsi_toko' => $request->deskripsi_toko,
            'lokasi' => $request->lokasi,
            'logo_path' => $path
        ]);

        return redirect('/toko');
    }

    public function update(Request $request, $id)
    {
        try {
            $toko = Toko::findOrFail($id);

            $request->validate([
                'nama_toko' => 'required',
                'deskripsi_toko' => 'required',
                'lokasi' => 'required',
                'logo' => 'nullable|image|max:2048'
            ]);

            if ($request->hasFile('logo')) {
                if ($toko->logo_path) {
                    Storage::disk('public')->delete($toko->logo_path);
                }

                $toko->logo_path = $request
                    ->file('logo')
                    ->store('logo_toko', 'public');
            }

            $toko->update([
                'nama_toko' => $request->nama_toko,
                'deskripsi_toko' => $request->deskripsi_toko,
                'lokasi' => $request->lokasi
            ]);

            return response()->json([
                'success' => true,
                'logo_path' => $toko->logo_path
            ]);

        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }


}
