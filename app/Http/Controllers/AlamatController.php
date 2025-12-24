<?php

namespace App\Http\Controllers;

use App\Models\Alamat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AlamatController extends Controller
{
    // GET /alamat
    public function index()
    {
        return Alamat::where('user_id', Auth::id())->get();
    }

    // POST /alamat
    public function store(Request $request)
    {
        $data = $request->validate([
            'alamat' => 'required',
            'nama_penerima' => 'required',
            'nomor_penerima' => 'required',
        ]);

        // Set default value untuk isDefault jika tidak ada
        $data['isDefault'] = $request->isDefault ?? false;

        // kalau alamat baru dijadikan default
        if ($data['isDefault']) {
            Alamat::where('user_id', Auth::id())
                ->update(['isDefault' => false]);
        }

        $data['user_id'] = Auth::id();

        return Alamat::create($data);
    }

    // PUT /alamat/{id}
    public function update(Request $request, $id)
    {
        $alamat = Alamat::where('user_id', Auth::id())->findOrFail($id);

        $data = $request->only([
            'alamat',
            'nama_penerima',
            'nomor_penerima'
        ]);

        // Set default value
        $data['isDefault'] = $request->isDefault ?? false;

        if ($data['isDefault']) {
            Alamat::where('user_id', Auth::id())
                ->where('id', '!=', $id)
                ->update(['isDefault' => false]);
        }

        $alamat->update($data);

        return $alamat;
    }

    // DELETE /alamat/{id}
    public function destroy($id)
    {
        Alamat::where('user_id', Auth::id())
            ->findOrFail($id)
            ->delete();

        return response()->json(['success' => true]);
    }
}