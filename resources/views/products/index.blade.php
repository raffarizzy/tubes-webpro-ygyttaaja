@extends('layouts.main')

@section('title', 'Katalog Produk - Medcom')

@section('content')
<div class="container my-5">
    <h2 class="fw-bold mb-4">Katalog Produk</h2>

    <div id="produk-list" class="row g-4">
        @forelse ($products as $produk)
            @php
                $nama  = is_array($produk) ? ($produk['nama'] ?? '-')  : ($produk->nama ?? '-');
                $harga = is_array($produk) ? ($produk['harga'] ?? 0)   : ($produk->harga ?? 0);
            @endphp
            <div class="col-md-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">{{ $nama }}</h5>
                        <p class="card-text fw-bold">Rp {{ number_format($harga, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-muted">Tidak ada produk tersedia.</p>
        @endforelse
    </div>
</div>
@endsection
