<?php

namespace App\Http\Controllers;

use App\Models\Toko;
use Illuminate\Http\Request;

class TokoController extends Controller
{
    // tampilkan toko milik user
    public function index()
    {
        $toko = Toko::where('user_id', auth()->id())->first();
        return view('toko.index', compact('toko'));
    }

    // form buat toko (kalau belum punya)
    public function create()
    {
        return view('toko.create');
    }

    // simpan toko
    public function store(Request $request)
    {
        $request->validate([
            'nama_toko' => 'required|string|max:255',
            'deskripsi_toko' => 'required|string',
            'lokasi' => 'required|string|max:255',
            'logo_path' => 'nullable|image|max:2048',
        ]);

        Toko::create([
            'user_id' => auth()->id(),
            'nama_toko' => $request->nama_toko,
            'deskripsi_toko' => $request->deskripsi_toko,
            'lokasi' => $request->lokasi,
            'logo_path' => $request->file('logo_path')
                ? $request->file('logo_path')->store('logo_toko', 'public')
                : null,
        ]);

        return redirect()->route('toko.index');
    }

    // profil toko
    public function show(Toko $toko)
    {
        $jumlahProduk = $toko->products()->count();
        return view('toko.show', compact('toko', 'jumlahProduk'));
    }

    // edit toko
    public function edit(Toko $toko)
    {
        return view('toko.edit', compact('toko'));
    }

    // update toko
    public function update(Request $request, Toko $toko)
    {
        $toko->update($request->only([
            'nama_toko',
            'deskripsi_toko',
            'lokasi'
        ]));

        return redirect()->route('toko.index');
    }

    // hapus toko
    public function destroy(Toko $toko)
    {
        $toko->delete();
        return redirect()->route('toko.index');
    }
}
