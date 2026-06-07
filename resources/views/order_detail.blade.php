@extends('layouts.main')

@section('title', 'Detail Pesanan - SpareHub')

@section('content')
<main class="container my-4">
    <div class="mb-4">
        <h2 class="fw-bold">Detail Order #{{ $order->id }}</h2>
        <p class="text-muted">Status: <span class="badge bg-primary">{{ $order->status }}</span></p>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <h5>Items</h5>
            <ul class="list-group list-group-flush">
                @foreach($order->items as $item)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        {{ $item->nama_produk ?? ($item->product->nama ?? 'Produk') }}
                        <span>{{ $item->qty }} x Rp {{ number_format($item->harga, 0, ',', '.') }}</span>
                    </li>
                @endforeach
            </ul>
            <hr>
            <div class="d-flex justify-content-between">
                <span class="fw-bold">Total Harga:</span>
                <span class="fw-bold text-primary">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <a href="{{ route('riwayat.pesanan') }}" class="btn btn-outline-secondary">Kembali ke Riwayat</a>
    </div>
</main>
@endsection
