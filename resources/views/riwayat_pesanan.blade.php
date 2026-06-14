@extends('layouts.main')

@section('title', 'Riwayat Pesanan - Medcom')
@section('body-class', 'class="bg-light"')

@push('styles')
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
            <h2 class="mb-1 fw-bold">Riwayat Pesanan</h2>
            <p class="text-muted mb-0">Lihat semua pesanan yang pernah Anda buat</p>
        </div>
        <a href="{{ url('/') }}" class="btn btn-outline-primary"> Kembali ke Beranda </a>
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
            <!-- Tidak Ada Pesanan -->
            <div class="card shadow-sm">
                <div class="card-body text-center py-5">
                    <h5 class="text-muted">Belum Ada Pesanan</h5>
                    <a href="{{ url('/') }}" class="btn btn-primary mt-3"> Mulai Belanja </a>
                </div>
            </div>
        @else
            <!-- Loop Orders -->
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
                        <span class="badge {{ $status['class'] }}">{{ $status['text'] }}</span>
                    </div>
                    
                    <div class="card-body">
                        <!-- Items -->
                        <div class="mb-3">
                            @foreach($pesanan->items as $item)
                                <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                                    <img src="{{ $item->product && $item->product->image_path ? Storage::url($item->product->image_path) : '' }}" class="rounded me-3" style="width: 60px; height: 60px; object-fit: cover;">
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

                        <!-- Summary -->
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="border rounded p-2 bg-light h-100">
                                    <small class="text-muted d-block">Alamat Pengiriman</small>
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

                        <!-- Action Buttons -->
                        <div class="mt-3 d-flex justify-content-end gap-2">
                            @if(!in_array($pesanan->status, ['pending', 'cancelled']))
                                <button type="button" class="btn btn-outline-warning btn-sm btn-report" data-order-id="{{ $pesanan->id }}">
                                    Laporkan Masalah
                                </button>
                            @endif

                            @if($pesanan->status === 'shipped')
                                <button type="button" class="btn btn-info btn-sm text-white btn-lacak" 
                                        data-resi="{{ $pesanan->nomor_resi }}" 
                                        data-courier="{{ $pesanan->courier_code }}">
                                    Lacak Pesanan
                                </button>

                                <form action="{{ route('orders.finish', $pesanan->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Konfirmasi bahwa Anda telah menerima pesanan ini?')">
                                    @csrf
                                    <button type="submit" class="btn btn-success btn-sm"> Pesanan Selesai </button>
                                </form>
                            @endif
                            
                            @if($pesanan->status === 'finished')
                                <a href="{{ route('ratings.index') }}" class="btn btn-warning btn-sm"> Review </a>
                            @endif
                            
                            @if($pesanan->status === 'pending')
                                @if($pesanan->payment_url)
                                    <a href="{{ $pesanan->payment_url }}" target="_blank" class="btn btn-primary btn-sm"> Bayar Sekarang </a>
                                @endif
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
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const trackingModal = new bootstrap.Modal(document.getElementById('trackingModal'));
        const reportModal = new bootstrap.Modal(document.getElementById('reportModal'));
        const trackingContent = document.getElementById('trackingContent');
        const waSupportLink = document.getElementById('waSupportLink');

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
                trackingModal.show();
                trackingContent.innerHTML = 'Melacak...';
                try {
                    const response = await fetch(`/api/shipping/track/${resi}/${courier}`);
                    const result = await response.json();
                    const data = result.data.data;
                    let historyHtml = (data.histories || []).map(h => `<div class="mb-2 small"><b>${h.date}</b>: ${h.message}</div>`).join('');
                    trackingContent.innerHTML = `<p>Resi: ${resi} (${courier.toUpperCase()})</p><p>Status: ${data.status}</p><hr>${historyHtml}`;
                } catch (e) { trackingContent.innerHTML = 'Gagal melacak.'; }
            });
        });
    });
</script>
@endpush
