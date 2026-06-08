@extends('layouts.main')

@section('title', 'Riwayat Pesanan - SpareHub')
@section('body-class', 'class="bg-light"')

@push('bootstrap')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
@endpush

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<style>
:root {
    --navy: #122C4F;
    --navy-2: #1a3a66;
    --blue: #0066CC;
    --gold: #FFC107;
    --cream: #F4E9DC;
    --ink: #202124;
    --body: #3C4043;
    --muted: #5F6368;
    --line: #E5DFD3;
    --ok: #1E8E3E;
    --warn: #E37400;
    --danger: #EA4335;
}

body {
    background: var(--cream);
    font-family: 'Roboto', sans-serif;
    color: var(--body);
}

h1, h2, h3, h4, h5, h6 {
    font-family: 'Poppins', sans-serif;
    color: var(--ink);
}

/* Page frame */
.page-frame {
    max-width: 1180px;
    margin: 32px auto;
    padding: 0 16px;
}

/* Layout */
.rh-layout {
    display: grid;
    grid-template-columns: 260px 1fr;
    gap: 24px;
    align-items: start;
}

/* ── Sidebar ── */
.sidebar-panel {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 2px 12px rgba(18,44,79,.08);
    overflow: hidden;
    position: sticky;
    top: 24px;
}

.sidebar-profile {
    background: linear-gradient(135deg, var(--blue) 0%, var(--navy) 100%);
    padding: 28px 20px 24px;
    text-align: center;
}

.avatar-circle {
    width: 72px;
    height: 72px;
    border-radius: 50%;
    background: linear-gradient(135deg, #3b9eff 0%, var(--navy) 100%);
    border: 3px solid rgba(255,255,255,.35);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 12px;
    font-family: 'Poppins', sans-serif;
    font-size: 28px;
    font-weight: 700;
    color: #fff;
}

.sidebar-name {
    font-family: 'Poppins', sans-serif;
    font-weight: 600;
    font-size: 15px;
    color: #fff;
    margin: 0 0 4px;
}

.sidebar-email {
    font-size: 12px;
    color: rgba(255,255,255,.75);
    margin: 0;
    word-break: break-all;
}

.sidebar-nav {
    padding: 12px 0;
}

.sidebar-nav-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 11px 20px;
    font-size: 14px;
    font-weight: 500;
    color: var(--body);
    text-decoration: none;
    border-left: 3px solid transparent;
    transition: background .15s, color .15s, border-color .15s;
}

.sidebar-nav-item:hover {
    background: #f0f4ff;
    color: var(--blue);
}

.sidebar-nav-item.active {
    background: #eef3ff;
    color: var(--blue);
    border-left-color: var(--blue);
    font-weight: 600;
}

.sidebar-nav-item .bi {
    font-size: 16px;
    flex-shrink: 0;
}

.sidebar-sep {
    height: 1px;
    background: var(--line);
    margin: 8px 20px;
}

.sidebar-logout-form {
    padding: 4px 0 12px;
}

.sidebar-logout-btn {
    display: flex;
    align-items: center;
    gap: 10px;
    width: 100%;
    padding: 11px 20px;
    font-size: 14px;
    font-weight: 500;
    color: var(--danger);
    background: none;
    border: none;
    border-left: 3px solid transparent;
    cursor: pointer;
    text-align: left;
    transition: background .15s, border-color .15s;
}

.sidebar-logout-btn:hover {
    background: #fff1f0;
    border-left-color: var(--danger);
}

/* ── Main content ── */
.main-content {
    min-width: 0;
}

/* Page header */
.page-header {
    background: #fff;
    border-radius: 16px;
    padding: 24px 28px;
    margin-bottom: 20px;
    box-shadow: 0 2px 12px rgba(18,44,79,.07);
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
}

.page-header h1 {
    font-size: 22px;
    font-weight: 700;
    margin: 0 0 4px;
    color: var(--navy);
}

.page-header p {
    font-size: 13px;
    color: var(--muted);
    margin: 0;
}

.btn-home {
    background: var(--navy);
    color: #fff;
    border: none;
    border-radius: 10px;
    padding: 9px 18px;
    font-size: 13px;
    font-weight: 500;
    font-family: 'Poppins', sans-serif;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: background .15s;
}

.btn-home:hover {
    background: var(--navy-2);
    color: #fff;
}

/* Stat cards */
.stat-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 14px;
    margin-bottom: 20px;
}

.stat-card {
    background: #fff;
    border-radius: 14px;
    padding: 18px 16px;
    box-shadow: 0 2px 10px rgba(18,44,79,.07);
    display: flex;
    align-items: center;
    gap: 14px;
}

.stat-icon {
    width: 46px;
    height: 46px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    flex-shrink: 0;
}

.stat-icon-all   { background: #eef3ff; color: var(--blue); }
.stat-icon-pending { background: #fff8e1; color: var(--warn); }
.stat-icon-paid   { background: #e8f5e9; color: var(--ok); }
.stat-icon-cancel { background: #fce8e6; color: var(--danger); }

.stat-label {
    font-size: 12px;
    color: var(--muted);
    margin: 0 0 2px;
}

.stat-value {
    font-family: 'Poppins', sans-serif;
    font-size: 24px;
    font-weight: 700;
    color: var(--ink);
    line-height: 1;
}

/* Alert overrides */
.alert {
    border-radius: 12px;
    font-size: 14px;
    margin-bottom: 16px;
}

/* Empty state */
.empty-card {
    background: #fff;
    border-radius: 16px;
    padding: 60px 32px;
    text-align: center;
    box-shadow: 0 2px 12px rgba(18,44,79,.07);
}

.empty-icon {
    font-size: 52px;
    color: var(--muted);
    margin-bottom: 12px;
}

/* Order card */
.order-card {
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 2px 12px rgba(18,44,79,.07);
    margin-bottom: 18px;
    overflow: hidden;
}

.order-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 10px;
    padding: 16px 22px;
    border-bottom: 1px solid var(--line);
    background: #fafafa;
}

.order-number {
    font-family: 'Poppins', sans-serif;
    font-size: 14px;
    font-weight: 700;
    color: var(--navy);
    letter-spacing: .3px;
}

.order-date {
    font-size: 12px;
    color: var(--muted);
    margin: 2px 0 0;
}

.order-badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 5px 14px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    font-family: 'Poppins', sans-serif;
}

.badge-pending  { background: #fff3cd; color: var(--warn); }
.badge-paid     { background: #d4edda; color: var(--ok); }
.badge-cancelled{ background: #fce8e6; color: var(--danger); }
.badge-default  { background: #e9ecef; color: var(--muted); }

.order-card-body {
    padding: 20px 22px;
}

/* Item row */
.item-row {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 12px 0;
    border-bottom: 1px solid var(--line);
}

.item-row:last-child {
    border-bottom: none;
}

.item-img {
    width: 64px;
    height: 64px;
    border-radius: 10px;
    object-fit: cover;
    flex-shrink: 0;
    background: #f1f3f4;
}

.item-name {
    font-family: 'Poppins', sans-serif;
    font-size: 14px;
    font-weight: 600;
    color: var(--ink);
    margin: 0 0 3px;
}

.item-meta {
    font-size: 12px;
    color: var(--muted);
}

.item-price {
    font-family: 'Poppins', sans-serif;
    font-size: 14px;
    font-weight: 700;
    color: var(--blue);
    margin-left: auto;
    white-space: nowrap;
}

/* Status tracker */
.status-tracker {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0;
    padding: 16px 0 12px;
    overflow-x: auto;
}

.tracker-step {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 4px;
    min-width: 70px;
}

.tracker-dot {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: #e9ecef;
    color: #aaa;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    border: 2px solid #e9ecef;
}

.tracker-dot.done {
    background: var(--ok);
    color: #fff;
    border-color: var(--ok);
}

.tracker-dot.active {
    background: var(--blue);
    color: #fff;
    border-color: var(--blue);
}

.tracker-label {
    font-size: 10px;
    font-weight: 500;
    color: var(--muted);
    text-align: center;
    white-space: nowrap;
}

.tracker-label.done   { color: var(--ok); }
.tracker-label.active { color: var(--blue); }

.tracker-line {
    flex: 1;
    height: 2px;
    background: #e9ecef;
    min-width: 24px;
    max-width: 48px;
    margin-bottom: 22px;
}

.tracker-line.done { background: var(--ok); }

/* Order footer */
.order-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
    padding-top: 16px;
    border-top: 1px solid var(--line);
    margin-top: 12px;
}

.order-total-label {
    font-size: 13px;
    color: var(--muted);
}

.order-total-value {
    font-family: 'Poppins', sans-serif;
    font-size: 18px;
    font-weight: 700;
    color: var(--navy);
}

.btn-cancel {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 7px 16px;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 500;
    border: 1.5px solid var(--danger);
    color: var(--danger);
    background: transparent;
    cursor: pointer;
    transition: background .15s, color .15s;
    font-family: 'Poppins', sans-serif;
}

.btn-cancel:hover {
    background: var(--danger);
    color: #fff;
}

.btn-review {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 7px 16px;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 500;
    background: var(--gold);
    color: var(--ink);
    border: none;
    text-decoration: none;
    font-family: 'Poppins', sans-serif;
    transition: opacity .15s;
}

.btn-review:hover {
    opacity: .85;
    color: var(--ink);
}

@media (max-width: 900px) {
    .rh-layout {
        grid-template-columns: 1fr;
    }
    .sidebar-panel {
        position: static;
    }
    .stat-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 480px) {
    .stat-grid {
        grid-template-columns: 1fr 1fr;
    }
}
</style>
@endpush

@section('footer-class', 'class="bg-dark text-white text-center py-2 mt-4"')
@section('footer-text-class', 'class="mb-0 small"')

@section('content')
<div class="page-frame">
    <div class="rh-layout">

        {{-- ─────────────── LEFT SIDEBAR ─────────────── --}}
        <aside class="sidebar-panel">
            <div class="sidebar-profile">
                <div class="avatar-circle">
                    {{ strtoupper(substr(auth()->user()->name ?? 'P', 0, 1)) }}
                </div>
                <p class="sidebar-name">{{ auth()->user()->name ?? 'Pengguna' }}</p>
                <p class="sidebar-email">{{ auth()->user()->email ?? '' }}</p>
            </div>

            <nav class="sidebar-nav">
                <a href="#" class="sidebar-nav-item active">
                    <i class="bi bi-bag-check"></i>
                    Pesanan Saya
                </a>
                @if(Route::has('profile.edit'))
                <a href="{{ route('profile.edit') }}" class="sidebar-nav-item">
                    <i class="bi bi-person"></i>
                    Profil
                </a>
                @endif
            </nav>

            <div class="sidebar-sep"></div>

            <div class="sidebar-logout-form">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="sidebar-logout-btn">
                        <i class="bi bi-box-arrow-right"></i>
                        Logout
                    </button>
                </form>
            </div>
        </aside>

        {{-- ─────────────── MAIN CONTENT ─────────────── --}}
        <main class="main-content">

            {{-- Page Header --}}
            <div class="page-header">
                <div>
                    <h1><i class="bi bi-clock-history" style="color:var(--blue)"></i> Riwayat Pesanan</h1>
                    <p>Lihat semua pesanan Anda</p>
                </div>
                <a href="{{ url('/') }}" class="btn-home">
                    <i class="bi bi-house"></i> Kembali ke Beranda
                </a>
            </div>

            {{-- Session Alerts --}}
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

            @if(isset($error))
                <div class="alert alert-danger" role="alert">
                    <h6 class="alert-heading mb-1"><i class="bi bi-exclamation-triangle-fill"></i> Error</h6>
                    <p class="mb-0">{{ $error }}</p>
                </div>
            @endif

            {{-- Stat Cards --}}
            <div class="stat-grid">
                <div class="stat-card">
                    <div class="stat-icon stat-icon-all"><i class="bi bi-bag"></i></div>
                    <div>
                        <p class="stat-label">Total Pesanan</p>
                        <div class="stat-value">{{ $orders->count() }}</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon stat-icon-pending"><i class="bi bi-hourglass-split"></i></div>
                    <div>
                        <p class="stat-label">Perlu Dibayar</p>
                        <div class="stat-value">{{ $orders->where('status','pending')->count() }}</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon stat-icon-paid"><i class="bi bi-check-circle"></i></div>
                    <div>
                        <p class="stat-label">Selesai</p>
                        <div class="stat-value">{{ $orders->where('status','paid')->count() }}</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon stat-icon-cancel"><i class="bi bi-x-circle"></i></div>
                    <div>
                        <p class="stat-label">Dibatalkan</p>
                        <div class="stat-value">{{ $orders->where('status','cancelled')->count() }}</div>
                    </div>
                </div>
            </div>

            {{-- Orders --}}
            @if($orders->isEmpty())
                <div class="empty-card">
                    <div class="empty-icon"><i class="bi bi-inbox"></i></div>
                    <h5 style="color:var(--muted)">Belum Ada Pesanan</h5>
                    <p style="color:var(--muted);font-size:14px">Anda belum pernah melakukan pemesanan</p>
                    <a href="{{ url('/') }}" class="btn-home" style="margin-top:12px;display:inline-flex">
                        <i class="bi bi-shop"></i> Mulai Belanja
                    </a>
                </div>
            @else
                @foreach($orders as $pesanan)
                    @php
                        $totalItems = $pesanan->items->sum('qty');

                        $statusConfig = [
                            'pending'   => ['badge' => 'badge-pending',   'icon' => 'hourglass-split', 'text' => 'Menunggu Pembayaran'],
                            'paid'      => ['badge' => 'badge-paid',      'icon' => 'check-circle',    'text' => 'Selesai'],
                            'cancelled' => ['badge' => 'badge-cancelled', 'icon' => 'x-circle',        'text' => 'Dibatalkan'],
                        ];
                        $st = $statusConfig[$pesanan->status] ?? ['badge' => 'badge-default', 'icon' => 'question-circle', 'text' => ucfirst($pesanan->status)];

                        // Tracker state
                        $isPaid      = $pesanan->status === 'paid';
                        $isCancelled = $pesanan->status === 'cancelled';
                    @endphp

                    <div class="order-card">
                        {{-- Header --}}
                        <div class="order-card-header">
                            <div>
                                <div class="order-number">
                                    <i class="bi bi-receipt" style="color:var(--blue)"></i>
                                    SH-{{ str_pad($pesanan->id, 6, '0', STR_PAD_LEFT) }}
                                </div>
                                <div class="order-date">
                                    <i class="bi bi-calendar3"></i>
                                    {{ $pesanan->created_at ? \Carbon\Carbon::parse($pesanan->created_at)->timezone('Asia/Jakarta')->locale('id')->isoFormat('D MMMM YYYY, HH:mm') : 'N/A' }}
                                </div>
                            </div>
                            <span class="order-badge {{ $st['badge'] }}">
                                <i class="bi bi-{{ $st['icon'] }}"></i> {{ $st['text'] }}
                            </span>
                        </div>

                        {{-- Body --}}
                        <div class="order-card-body">

                            {{-- Items --}}
                            <div>
                                @foreach($pesanan->items as $item)
                                    <div class="item-row">
                                        <img src="{{ $item->product && $item->product->image_path
                                            ? Storage::url($item->product->image_path)
                                            : 'data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%2264%22 height=%2264%22 viewBox=%220 0 64 64%22%3E%3Crect width=%2264%22 height=%2264%22 fill=%22%23f1f3f4%22/%3E%3Ctext x=%2250%25%22 y=%2254%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 font-size=%2210%22 fill=%22%235f6368%22%3ENo Img%3C/text%3E%3C/svg%3E' }}"
                                             alt="{{ $item->nama_produk }}"
                                             class="item-img"
                                             onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%2264%22 height=%2264%22 viewBox=%220 0 64 64%22%3E%3Crect width=%2264%22 height=%2264%22 fill=%22%23f1f3f4%22/%3E%3Ctext x=%2250%25%22 y=%2254%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 font-size=%2210%22 fill=%22%235f6368%22%3ENo Img%3C/text%3E%3C/svg%3E'">
                                        <div style="flex:1;min-width:0">
                                            <p class="item-name">{{ $item->nama_produk }}</p>
                                            <p class="item-meta">{{ $item->qty }} pcs × Rp {{ number_format($item->harga, 0, ',', '.') }}</p>
                                        </div>
                                        <div class="item-price">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</div>
                                    </div>
                                @endforeach
                            </div>

                            {{-- Status Tracker --}}
                            @if(!$isCancelled)
                            <div class="status-tracker">
                                <div class="tracker-step">
                                    <div class="tracker-dot {{ $isPaid ? 'done' : 'active' }}">
                                        <i class="bi bi-credit-card"></i>
                                    </div>
                                    <span class="tracker-label {{ $isPaid ? 'done' : 'active' }}">Dibayar</span>
                                </div>
                                <div class="tracker-line {{ $isPaid ? 'done' : '' }}"></div>
                                <div class="tracker-step">
                                    <div class="tracker-dot {{ $isPaid ? 'done' : '' }}">
                                        <i class="bi bi-box-seam"></i>
                                    </div>
                                    <span class="tracker-label {{ $isPaid ? 'done' : '' }}">Dikemas</span>
                                </div>
                                <div class="tracker-line {{ $isPaid ? 'done' : '' }}"></div>
                                <div class="tracker-step">
                                    <div class="tracker-dot {{ $isPaid ? 'done' : '' }}">
                                        <i class="bi bi-truck"></i>
                                    </div>
                                    <span class="tracker-label {{ $isPaid ? 'done' : '' }}">Dikirim</span>
                                </div>
                                <div class="tracker-line {{ $isPaid ? 'done' : '' }}"></div>
                                <div class="tracker-step">
                                    <div class="tracker-dot {{ $isPaid ? 'done' : '' }}">
                                        <i class="bi bi-house-check"></i>
                                    </div>
                                    <span class="tracker-label {{ $isPaid ? 'done' : '' }}">Tiba</span>
                                </div>
                            </div>
                            @endif

                            {{-- Order Footer --}}
                            <div class="order-footer">
                                <div>
                                    <div class="order-total-label">Total Pembayaran</div>
                                    <div class="order-total-value">Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</div>
                                </div>
                                <div style="display:flex;gap:8px;flex-wrap:wrap;align-items:center">
                                    @if($pesanan->status === 'paid')
                                        <a href="{{ route('ratings.index') }}" class="btn-review">
                                            <i class="bi bi-star"></i> Review
                                        </a>
                                    @endif

                                    @if($pesanan->status === 'pending')
                                        <form action="{{ route('orders.cancel', $pesanan->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan pesanan ini?')">
                                            @csrf
                                            <button type="submit" class="btn-cancel">
                                                <i class="bi bi-x-circle"></i> Batalkan
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>

                        </div>{{-- end card body --}}
                    </div>{{-- end order-card --}}
                @endforeach
            @endif

        </main>
    </div>{{-- end rh-layout --}}
</div>{{-- end page-frame --}}
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endpush