@extends('layouts.main')

@section('title', 'Riwayat Pesanan - SpareHub')
@section('body-class', 'class="bg-light"')

@push('bootstrap')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" />
@endpush

@section('footer-class', 'class="bg-dark text-white text-center py-2 mt-4"')
@section('footer-text-class', 'class="mb-0 small"')

@section('content')
<main class="container my-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1 fw-bold">
                <i class="bi bi-clock-history text-primary"></i> Riwayat Pesanan
            </h2>
            <p class="text-muted mb-0">Lihat semua pesanan yang pernah Anda buat</p>
        </div>
        <a href="{{ url('/') }}" class="btn btn-outline-primary">
            <i class="bi bi-house"></i> Kembali ke Beranda
        </a>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Error Message dari Controller -->
    @if(isset($error))
        <div class="alert alert-danger shadow-sm" role="alert">
            <h5 class="alert-heading">
                <i class="bi bi-exclamation-triangle-fill"></i> Error
            </h5>
            <p class="mb-0">{{ $error }}</p>
        </div>
    @endif

    <!-- Orders Container -->
    <div id="pesananContainer">
        @if($orders->isEmpty())
            <!-- Tidak Ada Pesanan -->
            <div class="card shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="bi bi-inbox fs-1 text-muted mb-3"></i>
                    <h5 class="text-muted mt-3">Belum Ada Pesanan</h5>
                    <p class="text-muted">Anda belum pernah melakukan pemesanan</p>
                    <a href="{{ url('/') }}" class="btn btn-primary mt-3">
                        <i class="bi bi-shop"></i> Mulai Belanja
                    </a>
                </div>
            </div>
        @else
            <!-- Loop Orders -->
            @foreach($orders as $pesanan)
                @php
                    $totalItems = $pesanan->items->sum('qty');
                    
                    // Status badge
                    $statusConfig = [
                        'pending' => [
                            'class' => 'bg-warning text-dark',
                            'icon' => 'hourglass-split',
                            'text' => 'Menunggu Pembayaran'
                        ],
                        'paid' => [
                            'class' => 'bg-success',
                            'icon' => 'check-circle',
                            'text' => 'Lunas'
                        ],
                        'cancelled' => [
                            'class' => 'bg-danger',
                            'icon' => 'x-circle',
                            'text' => 'Dibatalkan'
                        ]
                    ];
                    
                    $status = $statusConfig[$pesanan->status] ?? [
                        'class' => 'bg-secondary',
                        'icon' => 'question-circle',
                        'text' => ucfirst($pesanan->status)
                    ];
                @endphp

                <div class="card shadow-sm mb-3">
                    <!-- Card Header -->
                    <div class="card-header bg-light">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <h6 class="mb-0 fw-bold">
                                    <i class="bi bi-receipt text-primary"></i>
                                    Order #{{ $pesanan->id }}
                                </h6>
                                <small class="text-muted">
                                    <i class="bi bi-calendar3"></i>
                                    {{ $pesanan->created_at->locale('id')->isoFormat('D MMMM YYYY, HH:mm') }}
                                </small>
                            </div>
                            <div class="col-md-6 text-md-end mt-2 mt-md-0">
                                <span class="badge {{ $status['class'] }} fs-6">
                                    <i class="bi bi-{{ $status['icon'] }}"></i> {{ $status['text'] }}
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Card Body -->
                    <div class="card-body">
                        <!-- Order Items -->
                        <div class="mb-3">
                            @foreach($pesanan->items as $item)
                                <div class="d-flex align-items-start mb-3 pb-3 border-bottom">
                                    <img src="{{ $item->product && $item->product->image_path ? asset($item->product->image_path) : 'data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22110%22 height=%22110%22 viewBox=%220 0 110 110%22%3E%3Crect width=%22110%22 height=%22110%22 fill=%22%23f1f3f4%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 font-family=%22Arial, sans-serif%22 font-size=%2214%22 fill=%22%235f6368%22%3ENo Image%3C/text%3E%3Cpath d=%22M35 40h40v5H35z M40 50h30v5H40z M45 60h20v5H45z%22 fill=%22%23dadce0%22/%3E%3C/svg%3E' }}" 
                                         alt="{{ $item->nama_produk }}"
                                         class="rounded me-3"
                                         style="width: 80px; height: 80px; object-fit: cover;"
                                         onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22110%22 height=%22110%22 viewBox=%220 0 110 110%22%3E%3Crect width=%22110%22 height=%22110%22 fill=%22%23f1f3f4%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 font-family=%22Arial, sans-serif%22 font-size=%2214%22 fill=%22%235f6368%22%3ENo Image%3C/text%3E%3Cpath d=%22M35 40h40v5H35z M40 50h30v5H40z M45 60h20v5H45z%22 fill=%22%23dadce0%22/%3E%3C/svg%3E'">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1 fw-semibold">{{ $item->nama_produk }}</h6>
                                        <p class="text-muted small mb-1">
                                            {{ $item->product && $item->product->deskripsi ? $item->product->deskripsi : 'Produk berkualitas tinggi' }}
                                        </p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="text-muted small">
                                                {{ $item->qty }} pcs × Rp {{ number_format($item->harga, 0, ',', '.') }}
                                            </span>
                                            <span class="fw-bold text-primary">
                                                Rp {{ number_format($item->subtotal, 0, ',', '.') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Order Summary -->
                        <div class="row g-3">
                            <!-- Alamat Pengiriman -->
                            <div class="col-md-6">
                                <div class="border rounded p-3 bg-light h-100">
                                    <h6 class="mb-3 fw-semibold">
                                        <i class="bi bi-geo-alt text-danger"></i>
                                        Alamat Pengiriman
                                    </h6>
                                    <p class="mb-1 fw-semibold">{{ $pesanan->alamat ? $pesanan->alamat->nama_penerima : 'Tidak ada nama' }}</p>
                                    <p class="small mb-1 text-muted">{{ $pesanan->alamat ? $pesanan->alamat->alamat : 'Alamat tidak tersedia' }}</p>
                                    <p class="small mb-0 text-muted">
                                        <i class="bi bi-telephone"></i>
                                        {{ $pesanan->alamat ? $pesanan->alamat->nomor_penerima : '-' }}
                                    </p>
                                </div>
                            </div>
                            
                            <!-- Ringkasan Pembayaran -->
                            <div class="col-md-6">
                                <div class="border rounded p-3 bg-light h-100">
                                    <h6 class="mb-3 fw-semibold">
                                        <i class="bi bi-receipt-cutoff text-success"></i>
                                        Ringkasan Pembayaran
                                    </h6>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">Total Item:</span>
                                        <span class="fw-semibold">{{ $totalItems }} pcs</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">Ongkir:</span>
                                        <span class="text-success fw-semibold">Gratis</span>
                                    </div>
                                    <hr class="my-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="fw-bold">Total:</span>
                                        <span class="fw-bold text-primary fs-5">
                                            Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-3 d-flex justify-content-end gap-2">
                            @if($pesanan->status === 'paid')
                                <a href="{{ route('ratings.index') }}" class="btn btn-success btn-sm">
                                    <i class="bi bi-star"></i> Review
                                </a>
                            @endif
                            
                            @if($pesanan->status === 'pending')
                                <!-- Form untuk Cancel Order -->
                                <form action="{{ route('orders.cancel', $pesanan->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini?')">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-danger btn-sm">
                                        <i class="bi bi-x-circle"></i> Batalkan
                                    </button>
                                </form>
                            @endif
                            
                            <a href="{{ route('orders.detail', $pesanan->id) }}" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-eye"></i> Detail
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</main>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
{{-- Tidak ada JavaScript untuk fetch data lagi! Data sudah dari Controller --}}
@endpush