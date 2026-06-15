@extends('layouts.main')

@section('title', 'Riwayat Pesanan - Medcom')
@section('body-class', 'class="bg-light"')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" />
    <style>
        /* Medcom Custom Buttons using Bootstrap Variable System */
        .btn-medcom-blue {
            --bs-btn-color: #ffffff;
            --bs-btn-bg: #122c4f;
            --bs-btn-border-color: #122c4f;
            --bs-btn-hover-color: #ffffff;
            --bs-btn-hover-bg: #0d2033; /* Darker blue on hover */
            --bs-btn-hover-border-color: #0d2033;
            --bs-btn-active-bg: #0a1829;
            --bs-btn-active-border-color: #0a1829;
        }

        .btn-medcom-outline-blue {
            --bs-btn-color: #122c4f;
            --bs-btn-border-color: #122c4f;
            --bs-btn-hover-color: #122c4f;
            --bs-btn-hover-bg: #F7F7F7;
            --bs-btn-hover-border-color: #122c4f;
            --bs-btn-active-bg: #122c4f;
            --bs-btn-active-border-color: #122c4f;
        }
    </style>
@endpush

@section('footer-class', 'class="bg-dark text-white text-center py-2 mt-4"')
@section('footer-text-class', 'class="mb-0 small"')

@section('content')
<main class="container my-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1 fw-bold">Riwayat Pesanan</h2>
            <p class="text-muted mb-0">Lihat semua pesanan yang pernah Anda buat</p>
        </div>
        <a href="{{ url('/') }}" class="btn" 
        style = "
            --bs-btn-color: #ffffff;
            --bs-btn-bg: #122c4f;
            --bs-btn-border-color: #122c4f;
            --bs-btn-hover-color: #ffffff;
            --bs-btn-hover-bg: #0d2033; /* Darker blue on hover */
            --bs-btn-hover-border-color: #0d2033;
            --bs-btn-active-bg: #0a1829;
            --bs-btn-active-border-color: #0a1829;
        "
        > Kembali ke Beranda </a>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Orders Container -->
    <div id="pesananContainer">
        @if($orders->isEmpty())
            <div class="card shadow-sm">
                <div class="card-body text-center py-5">
                    <h5 class="text-muted">Belum Ada Pesanan</h5>
                    <a href="{{ url('/') }}" class="btn btn-primary mt-3"> Mulai Belanja </a>
                </div>
            </div>
        @else
            @foreach($orders as $pesanan)
                @php
                    $totalItems = $pesanan->items->sum('qty');
                    $statusConfig = [
                        'pending' => ['class' => 'bg-warning text-dark', 'text' => 'Menunggu Pembayaran'],
                        'paid' => ['class' => 'bg-success', 'text' => 'Sudah Dibayar'],
                        'processing' => ['class' => 'bg-info text-dark', 'text' => 'Diproses'],
                        'shipped' => ['class' => 'bg-info text-white', 'text' => 'Dikirim'],
                        'finished' => ['class' => 'bg-success', 'text' => 'Pesanan Selesai'],
                        'cancelled' => ['class' => 'bg-danger', 'text' => 'Dibatalkan']
                    ];
                    $status = $statusConfig[$pesanan->status] ?? ['class' => 'bg-secondary', 'text' => ucfirst($pesanan->status)];
                @endphp

                <div class="card shadow-sm mb-3">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-0 fw-bold">Order #{{ $pesanan->id }}</h6>
                            <small class="text-muted">
                                {{ $pesanan->created_at ? \Carbon\Carbon::parse($pesanan->created_at)->timezone('Asia/Jakarta')->locale('id')->isoFormat('D MMMM YYYY, HH:mm') : 'N/A' }}
                            </small>
                        </div>
                        <span class="badge {{ $status['class'] }} fs-6">{{ $status['text'] }}</span>
                    </div>
                    
                    <div class="card-body">
                        <div class="mb-3">
                            @foreach($pesanan->items as $item)
                                <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                                    @php
                                        $imagePath = ($item->product && $item->product->image_path) ? $item->product->image_path : null;
                                    @endphp
                                    <img src="{{ $imagePath ? asset('storage/' . $imagePath) : asset('img/no-image.png') }}" class="rounded me-3" style="width: 60px; height: 60px; object-fit: cover;" onerror="this.src='{{ asset('img/no-image.png') }}'">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0 fw-semibold">{{ $item->nama_produk }}</h6>
                                        <div class="d-flex justify-content-between">
                                            <small class="text-muted">{{ $item->qty }} pcs × Rp {{ number_format($item->harga, 0, ',', '.') }}</small>
                                            <span class="fw-bold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="border rounded p-2 bg-light h-100">
                                    <small class="text-muted d-block small">Alamat Pengiriman</small>
                                    <p class="mb-0 small fw-semibold">{{ $pesanan->alamat->nama_penerima ?? 'N/A' }}</p>
                                    <p class="mb-0 small text-muted">{{ $pesanan->alamat->alamat ?? 'N/A' }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="border rounded p-2 bg-light h-100">
                                    <div class="d-flex justify-content-between small">
                                        <span class="text-muted">Ongkir ({{ $pesanan->courier_name ?? 'Kurir' }}):</span>
                                        <span>Rp {{ number_format($pesanan->shipping_cost ?? 0, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="d-flex justify-content-between fw-bold mt-1">
                                        <span>Total:</span>
                                        <span class="text-primary">Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3 d-flex justify-content-end gap-2">
                            @if(!in_array($pesanan->status, ['pending', 'cancelled']))
                                <button type="button" class="btn btn-medcom-outline-blue btn-sm btn-report" data-order-id="{{ $pesanan->id }}"> Laporkan Masalah </button>
                            @endif

                            @if($pesanan->status === 'shipped')
                                <button type="button" class="btn btn-medcom-blue btn-sm btn-lacak" data-resi="{{ $pesanan->nomor_resi }}" data-courier="{{ $pesanan->courier_code }}"> Lacak Pesanan </button>
                                <form action="{{ route('orders.finish', $pesanan->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Konfirmasi pesanan diterima?')">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm"> Pesanan Selesai </button>
                                </form>
                            @endif
                            
                            @if($pesanan->status === 'finished')
                                @php
                                    $firstItem = $pesanan->items->first();
                                    $productId = $firstItem ? $firstItem->product_id : null;
                                    $hasReviewedAll = $pesanan->items->every(fn($item) => isset($item->rating_id) && $item->rating_id);
                                @endphp
                                @if($productId)
                                    <a href="{{ route('ratings.create', $productId) }}" class="btn btn-warning btn-sm"> 
                                        {{ $hasReviewedAll ? 'Lihat Review' : 'Review' }} 
                                    </a>
                                @endif
                            @endif
                            
                            @if($pesanan->status === 'pending')
                                <button type="button" class="btn btn-medcom-blue btn-sm btn-pay-now" 
                                        data-order-id="{{ $pesanan->id }}"
                                        data-reference="{{ $pesanan->payment_reference ?? '' }}"
                                        data-total="{{ $pesanan->total_harga }}"> 
                                    <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                    <span class="btn-text">Bayar Sekarang</span>
                                </button>
                                <form action="{{ route('orders.cancel', $pesanan->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Batalkan pesanan?')">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-danger btn-sm"> Batalkan </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</main>

<!-- Modal Tracking -->
<div class="modal fade" id="trackingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Pelacakan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="trackingContent"></div>
        </div>
    </div>
</div>

<!-- Modal Report -->
<div class="modal fade" id="reportModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title">Laporkan Masalah</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <p>Ada kendala? Hubungi tim support Medcom via WhatsApp:</p>
                <a href="#" target="_blank" class="btn btn-success" id="waSupportLink"> Hubungi WhatsApp Support </a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

{{-- Duitku POP JS --}}
@if(config('services.duitku.mode') === 'production')
    <script src="https://app-prod.duitku.com/lib/js/duitku.js"></script>
@else
    <script src="https://app-sandbox.duitku.com/lib/js/duitku.js"></script>
@endif

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const trackingModal = new bootstrap.Modal(document.getElementById('trackingModal'));
        const reportModal = new bootstrap.Modal(document.getElementById('reportModal'));
        const waSupportLink = document.getElementById('waSupportLink');

        // Logic "Bayar Sekarang" via Duitku POP (Resilient Mode)
        document.querySelectorAll('.btn-pay-now').forEach(btn => {
            btn.addEventListener('click', async function() {
                const orderId = this.dataset.orderId;
                const reference = this.dataset.reference;
                const total = this.dataset.total;
                const btnText = this.querySelector('.btn-text');
                const spinner = this.querySelector('.spinner-border');

                // 1. Jika sudah punya reference, langsung buka POP
                if (reference && reference !== 'null') {
                    openDuitkuPop(reference);
                    return;
                }

                // 2. Jika belum punya reference (pesanan lama atau error), minta ke server
                try {
                    this.disabled = true;
                    btnText.textContent = "Menyiapkan...";
                    spinner.classList.remove('d-none');

                    const response = await fetch("/checkout/pay", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body: JSON.stringify({
                            order_id: orderId,
                            alamat_id: 0, // Alamat sudah ada di database, kita kirim 0 saja
                            total: total,
                            payment_method: 'VC' // Default ke Credit Card atau user bisa pilih nanti di POP
                        }),
                    });

                    const result = await response.json();
                    if (result.success && result.reference) {
                        openDuitkuPop(result.reference);
                    } else {
                        throw new Error(result.message || "Gagal mendapatkan referensi pembayaran");
                    }
                } catch (err) {
                    alert("Error: " + err.message);
                } finally {
                    this.disabled = false;
                    btnText.textContent = "Bayar Sekarang";
                    spinner.classList.add('d-none');
                }
            });
        });

        function openDuitkuPop(reference) {
            checkout.process(reference, {
                defaultLanguage: "id",
                successEvent: function(result) {
                    alert("Pembayaran Berhasil!");
                    location.reload();
                },
                pendingEvent: function(result) {
                    alert("Pesanan sedang menunggu pembayaran.");
                    location.reload();
                },
                errorEvent: function(result) {
                    alert("Gagal memproses pembayaran: " + (result.statusMessage || "Terjadi kesalahan"));
                },
                closeEvent: function(result) {
                    console.log('Duitku Popup Closed');
                    // Tidak perlu reload jika hanya menutup, agar user tetap di halaman riwayat
                }
            });
        }

        document.querySelectorAll('.btn-report').forEach(btn => {
            btn.addEventListener('click', function() {
                waSupportLink.href = "https://wa.me/6281234567890?text=Masalah Order #" + this.dataset.orderId;
                reportModal.show();
            });
        });

        document.querySelectorAll('.btn-lacak').forEach(btn => {
            btn.addEventListener('click', async function() {
                const resi = this.dataset.resi;
                const courier = this.dataset.courier;
                const content = document.getElementById('trackingContent');
                trackingModal.show();
                content.innerHTML = '<div class="text-center py-3"><div class="spinner-border text-primary"></div><p class="mt-2">Melacak...</p></div>';
                try {
                    const response = await fetch(`/api/shipping/track/${resi}/${courier}`);
                    const result = await response.json();
                    const data = result.data.data;
                    let historyHtml = (data.histories || []).map(h => `<div class="mb-3 border-start border-primary ps-3"><small class="text-muted d-block">${h.date}</small><div>${h.message}</div></div>`).join('');
                    content.innerHTML = `
                        <div class="row mb-3"><div class="col-6"><h6>Resi: ${resi}</h6></div><div class="col-6 text-end"><span class="badge bg-success">${data.status}</span></div></div>
                        <h6>Riwayat Perjalanan:</h6><hr>${historyHtml || 'Tidak ada riwayat.'}`;
                } catch (e) { content.innerHTML = '<div class="alert alert-danger">Gagal melacak pesanan.</div>'; }
            });
        });
    });
</script>
@endpush
