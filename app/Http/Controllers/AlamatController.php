<?php

namespace App\Http\Controllers;

use App\Models\Alamat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AlamatController extends Controller
{
    public function index()
    {
        return Alamat::where('user_id', Auth::id())->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'alamat' => 'required',
            'nama_penerima' => 'required',
            'nomor_penerima' => 'required',
            'is_default' => 'nullable|boolean', // TAMBAHKAN VALIDASI
        ]);

        $data['user_id'] = Auth::id();
        
        // Konversi is_default ke boolean
        $data['is_default'] = $request->input('is_default', false) ? true : false;
        
        Log::info('Store alamat', ['data' => $data]);

        // Jika alamat baru dijadikan default, set yang lain jadi false
        if ($data['is_default']) {
            Alamat::where('user_id', Auth::id())
                ->update(['is_default' => false]);
        }

        return Alamat::create($data);
    }

    public function update(Request $request, $id)
    {
        $alamat = Alamat::where('user_id', Auth::id())->findOrFail($id);

        $data = $request->validate([
            'alamat' => 'required',
            'nama_penerima' => 'required',
            'nomor_penerima' => 'required',
            'is_default' => 'nullable|boolean', // TAMBAHKAN VALIDASI
        ]);

        // Konversi is_default ke boolean
        $data['is_default'] = $request->input('is_default', false) ? true : false;
        
        Log::info('Update alamat', [
            'id' => $id,
            'data' => $data,
            'request_is_default' => $request->input('is_default')
        ]);

        // Jika alamat ini dijadikan default, set yang lain jadi false
        if ($data['is_default']) {
            Alamat::where('user_id', Auth::id())
                ->where('id', '!=', $id)
                ->update(['is_default' => false]);
        }

        $alamat->update($data);
        
        // Reload untuk memastikan data terbaru
        $alamat->refresh();
        
        Log::info('After update', ['alamat' => $alamat->toArray()]);

        return $alamat;
    }

    public function destroy($id)
    {
        Alamat::where('user_id', Auth::id())
            ->findOrFail($id)
            ->delete();

        return response()->json(['success' => true]);
    }
}