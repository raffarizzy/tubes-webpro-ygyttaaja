@extends('layouts.main')

@section('title', 'Manajemen Toko - Medcom')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
    :root {
        --primary-dark: #122c4f;
        --accent-amber: #ffc107;
        --bg-light: #f8fafc;
    }

    body {
        background-color: var(--bg-light);
    }

    .shop-banner {
        height: 200px;
        background: linear-gradient(135deg, var(--primary-dark) 0%, #1e4b8a 100%);
        border-radius: 0 0 24px 24px;
        position: relative;
        margin-bottom: 80px;
    }

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

    .text-medcom-blue {
        color: #122c4f !important;
    }

    .bg-medcom-blue {
        background-color: #122c4f !important;
    }

    /* Bleed background to prevent white gap on scroll up */
    .shop-banner::before {
        content: "";
        position: absolute;
        top: -1000px;
        left: 0;
        right: 0;
        bottom: 0;
        background: var(--primary-dark);
        z-index: -1;
    }

    .shop-profile-card {
        position: absolute;
        bottom: -60px;
        left: 50%;
        transform: translateX(-50%);
        width: 90%;
        max-width: 1000px;
        background: white;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        display: flex;
        align-items: center;
        gap: 24px;
    }

    .shop-logo-wrapper {
        position: relative;
        flex-shrink: 0;
    }

    .shop-logo {
        width: 120px;
        height: 120px;
        border-radius: 12px;
        object-fit: cover;
        border: 4px solid white;
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .shop-details {
        flex-grow: 1;
    }

    .shop-details h1 {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--primary-dark);
        margin: 0;
    }

    .shop-details p {
        color: #64748b;
        margin: 4px 0;
    }

    .shop-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 12px;
        background: #f1f5f9;
        border-radius: 20px;
        font-size: 0.85rem;
        color: #475569;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-top: 40px;
    }

    .stat-card {
        background: white;
        padding: 20px;
        border-radius: 16px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.02);
        display: flex;
        align-items: center;
        gap: 16px;
        transition: transform 0.2s;
    }

    .stat-card:hover {
        transform: translateY(-5px);
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: rgba(18, 44, 79, 0.05);
        color: var(--primary-dark);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }

    .stat-info h3 {
        font-size: 1.25rem;
        font-weight: 700;
        margin: 0;
        color: var(--primary-dark);
    }

    .stat-info p {
        margin: 0;
        font-size: 0.85rem;
        color: #64748b;
    }

    .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 24px;
        margin-top: 30px;
    }

    .product-card {
        background: white;
        border-radius: 16px;
        overflow: hidden;
        border: 1px solid #f1f5f9;
        transition: all 0.3s ease;
    }

    .product-card:hover {
        box-shadow: 0 12px 24px rgba(0,0,0,0.08);
        transform: translateY(-8px);
    }

    .product-image-wrapper {
        position: relative;
        height: 180px;
    }

    .product-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .product-content {
        padding: 16px;
    }

    .product-category {
        font-size: 0.75rem;
        text-transform: uppercase;
        color: #94a3b8;
        font-weight: 600;
        letter-spacing: 0.5px;
    }

    .product-title {
        font-size: 1rem;
        font-weight: 600;
        color: var(--primary-dark);
        margin: 8px 0;
        height: 2.5rem;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }

    .product-price {
        font-size: 1.125rem;
        font-weight: 700;
        color: var(--primary-dark);
    }

    .product-stock {
        font-size: 0.85rem;
        color: #64748b;
        margin-top: 8px;
    }

    .product-actions {
        display: flex;
        gap: 8px;
        margin-top: 16px;
    }

    .btn-action {
        flex: 1;
        padding: 8px;
        border-radius: 8px;
        font-size: 0.85rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        transition: all 0.2s;
    }

    .btn-edit {
        background: #f1f5f9;
        color: #475569;
        border: none;
    }

    .btn-edit:hover {
        background: #e2e8f0;
        color: var(--primary-dark);
    }

    .btn-delete {
        background: #fef2f2;
        color: #ef4444;
        border: none;
    }

    .btn-delete:hover {
        background: #fee2e2;
    }

    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 60px;
        margin-bottom: 24px;
    }

    .btn-add-product {
        background: var(--primary-dark);
        color: white;
        padding: 10px 20px;
        border-radius: 12px;
        font-weight: 600;
        border: none;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
    }

    .btn-add-product:hover {
        background: #1e4b8a;
        transform: scale(1.05);
    }

    /* Modal Styling */
    .modal-content {
        border-radius: 20px;
        border: none;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }

    .modal-header {
        border-bottom: 1px solid #f1f5f9;
        padding: 24px;
    }

    .modal-body {
        padding: 24px;
    }

    .form-control, .form-select {
        border-radius: 10px;
        padding: 10px 14px;
        border: 1px solid #e2e8f0;
    }

    .form-control:focus {
        box-shadow: 0 0 0 4px rgba(18, 44, 79, 0.1);
        border-color: var(--primary-dark);
    }

    .preview-image {
        width: 100%;
        max-height: 200px;
        object-fit: contain;
        border-radius: 12px;
        margin-top: 10px;
        background: #f8fafc;
        border: 1px dashed #cbd5e1;
    }

    .fw-500 { font-weight: 500; }
    .fw-600 { font-weight: 600; }

    @media (max-width: 768px) {
        .shop-profile-card {
            flex-direction: column;
            text-align: center;
            bottom: -120px;
        }
        .shop-banner {
            margin-bottom: 140px;
        }
        .product-actions {
            flex-direction: column;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid p-0">
    {{-- Banner & Profile --}}
    <div class="shop-banner">
        {{-- Alerts di dalam Banner agar tidak mendorong layout ke bawah --}}
        <div class="container pt-3">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show rounded-3 border-0 shadow-sm" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show rounded-3 border-0 shadow-sm" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
        </div>

        <div class="shop-profile-card">
            <div class="shop-logo-wrapper">
                <img src="{{ $toko->logo_path ? asset('storage/'.$toko->logo_path) : asset('img/iconPengguna.png') }}" 
                     class="shop-logo" 
                     alt="Logo {{ $toko->nama_toko }}">
            </div>
            <div class="shop-details">
                <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                    <div>
                        <h1 class="d-flex align-items-center gap-2">
                            {{ $toko->nama_toko }}
                            @if($toko->is_verified_seller)
                                <i class="bi bi-patch-check-fill text-primary" style="font-size: 1.2rem;" title="Verified Seller"></i>
                            @endif
                        </h1>
                        <p class="mb-2">
                            @if(strlen($toko->deskripsi_toko) > 160)
                                <span id="short-desc">{{ Str::limit($toko->deskripsi_toko, 160) }}</span>
                                <a href="javascript:void(0)" class="text-primary fw-600 text-decoration-none ms-1" onclick="openFullDescModal()">Baca Selengkapnya</a>
                            @else
                                {{ $toko->deskripsi_toko }}
                            @endif
                        </p>
                        <div class="d-flex gap-2 flex-wrap">
                            <span class="shop-badge">
                                <i class="bi bi-geo-alt"></i> {{ $toko->lokasi }}
                            </span>
                            <span class="shop-badge">
                                <i class="bi bi-calendar3"></i> Tergabung {{ \Carbon\Carbon::parse($toko->created_at)->format('M Y') }}
                            </span>
                        </div>
                    </div>
                    @if($isOwner)
                    <button class="btn btn-outline-dark rounded-pill px-4" onclick="openEditTokoModal()">
                        <i class="bi bi-pencil-square me-2"></i> Edit Profil
                    </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        {{-- Stats Summary --}}
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="bi bi-box-seam"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $toko->products->count() }}</h3>
                    <p>Total Produk</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="bi bi-cart-check"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ $successOrdersCount }}</h3>
                    <p>Pesanan Berhasil</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="bi bi-star"></i>
                </div>
                <div class="stat-info">
                    <h3>{{ number_format($averageRating, 1) }}</h3>
                    <p>Rating Toko</p>
                </div>
            </div>
        </div>

        {{-- Tab Navigation --}}
        <ul class="nav nav-pills gap-2 mb-4 mt-5" id="shopTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active rounded-pill px-4 fw-600" id="products-tab" data-bs-toggle="pill" data-bs-target="#products" type="button" role="tab">
                    <i class="bi bi-grid me-2"></i> {{ $isOwner ? 'Produk Anda' : 'Semua Produk' }}
                </button>
            </li>
            @if($isOwner)
            <li class="nav-item" role="presentation">
                <button class="nav-link rounded-pill px-4 fw-600" id="orders-tab" data-bs-toggle="pill" data-bs-target="#orders" type="button" role="tab">
                    <i class="bi bi-cart-check me-2"></i> Pesanan Masuk
                </button>
            </li>
            @endif
        </ul>

        <div class="tab-content" id="shopTabsContent">
            {{-- Products Tab --}}
            <div class="tab-pane fade show active" id="products" role="tabpanel">
                <div class="section-header mt-0">
                    <div>
                        <h2 class="fw-bold mb-0" style="color: var(--primary-dark)">{{ $isOwner ? 'Daftar Produk' : 'Katalog Produk' }}</h2>
                        <p class="text-muted mb-0">{{ $isOwner ? 'Kelola stok dan informasi produk Anda' : 'Temukan berbagai produk berkualitas dari toko kami' }}</p>
                    </div>
                    <div class="d-flex gap-3 align-items-center">
                        <div class="input-group" style="width: 300px;">
                            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search"></i></span>
                            <input type="text" id="search-produk-toko" class="form-control border-start-0" placeholder="Cari produk Anda...">
                        </div>
                        <select id="filter-kategori-toko" class="form-select" style="width: 200px;">
                            <option value="">Semua Kategori</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->judulKategori }}">{{ $cat->judulKategori }}</option>
                            @endforeach
                        </select>
                        @if($isOwner)
                        <div class="d-flex gap-2">
                            <button class="btn btn-outline-success rounded-pill px-4 fw-600" onclick="document.getElementById('csvInput').click()">
                                <i class="bi bi-file-earmark-spreadsheet me-2"></i> Import CSV
                            </button>
                            <input type="file" id="csvInput" style="display: none" accept=".csv" onchange="importCSV(this)">
                            <button class="btn-add-product" onclick="openTambahModal()">
                                <i class="bi bi-plus-lg"></i> Tambah Produk Baru
                            </button>
                        </div>
                        @endif
                    </div>
                </div>

                @if($toko->products->count() == 0)
                    <div class="text-center py-5 bg-white rounded-4 shadow-sm">
                        <i class="bi bi-inbox text-muted" style="font-size: 4rem;"></i>
                        <h4 class="mt-3 text-muted">Belum ada produk @if($isOwner) di toko Anda @endif</h4>
                        @if($isOwner)
                        <p class="text-muted">Mulai berjualan dengan menambahkan produk pertama Anda.</p>
                        <button class="btn btn-primary rounded-pill px-4" onclick="openTambahModal()">
                            Tambah Produk Sekarang
                        </button>
                        @endif
                    </div>
                @else
                    <div class="product-grid" id="produk-list">
                        @foreach($toko->products as $p)
                        <div class="product-card" id="produk-{{ $p->id }}" data-category="{{ $p->category->judulKategori ?? '' }}" onclick="!event.target.closest('.product-actions') && (window.location.href='{{ route('produk.detail', $p->id) }}')" style="cursor: pointer;">
                            <div class="product-image-wrapper">
                                @php
                                    $pImg = $p->imagePath ?: 'produk/default.png';
                                    $pImg = asset('storage/'.$pImg);
                                @endphp
                                <img src="{{ $pImg }}" 
                                     class="product-image" 
                                     alt="{{ $p->nama }}">
                            </div>
                            <div class="product-content">
                                <span class="product-category">{{ $p->category->judulKategori ?? 'Sparepart' }}</span>
                                <h3 class="product-title">{{ $p->nama }}</h3>
                                <div class="d-flex justify-content-between align-items-end">
                                    <span class="product-price">Rp {{ number_format($p->harga, 0, ',', '.') }}</span>
                                </div>
                                <p class="product-stock">Stok: <span class="fw-bold text-dark">{{ $p->stok }}</span></p>
                                
                                @if($isOwner)
                                <div class="product-actions">
                                    <button class="btn-action btn-edit" 
                                        onclick="openEditModal(
                                            {{ $p->id }},
                                            {{ json_encode($p->nama) }},
                                            {{ $p->harga }},
                                            {{ $p->stok }},
                                            '{{ $p->imagePath }}',
                                            {{ json_encode($p->deskripsi) }},
                                            {{ $p->berat ?? 1000 }},
                                            {{ $p->category_id }}
                                        )">
                                        <i class="bi bi-pencil"></i> Edit
                                    </button>
                                    <button class="btn-action btn-delete" onclick="openHapusModal({{ $p->id }})">
                                        <i class="bi bi-trash"></i> Hapus
                                    </button>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Orders Tab --}}
            @if($isOwner)
            <div class="tab-pane fade" id="orders" role="tabpanel">
                <div class="section-header mt-0">
                    <div>
                        <h2 class="fw-bold mb-0" style="color: var(--primary-dark)">Pesanan Masuk</h2>
                        <p class="text-muted mb-0">Daftar transaksi produk dari pembeli</p>
                    </div>
                </div>

                @if($incomingOrders->isEmpty())
                    <div class="text-center py-5 bg-white rounded-4 shadow-sm">
                        <i class="bi bi-receipt text-muted" style="font-size: 4rem;"></i>
                        <h4 class="mt-3 text-muted">Belum ada pesanan masuk</h4>
                        <p class="text-muted">Tetap semangat! Pesanan pertama akan segera datang.</p>
                    </div>
                @else
                    <div class="bg-white rounded-4 shadow-sm overflow-hidden">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4 py-3">ID Pesanan</th>
                                        <th class="py-3">Pembeli</th>
                                        <th class="py-3">Produk</th>
                                        <th class="py-3">Jumlah</th>
                                        <th class="py-3">Total</th>
                                        <th class="py-3 text-center">Status</th>
                                        <th class="pe-4 py-3 text-end">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($incomingOrders as $item)
                                    <tr>
                                        <td class="ps-4 fw-bold text-primary">#{{ $item->order->id }}</td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="avatar-sm bg-info text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                                    {{ strtoupper(substr($item->order->user->name, 0, 1)) }}
                                                </div>
                                                <div>
                                                    <div class="fw-bold">{{ $item->order->user->name }}</div>
                                                    <div class="text-muted small">{{ $item->order->alamat->kota ?? 'Lokasi tdk diketahui' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="fw-600">{{ $item->nama_produk }}</div>
                                            <span class="badge bg-light text-dark border fw-normal" style="font-size: 0.7rem;">
                                                <i class="bi bi-truck text-primary"></i> {{ $item->order->courier_name ?? 'Kurir' }} ({{ $item->order->service_name ?? '-' }})
                                            </span>
                                            <div class="text-muted small">Harga: Rp {{ number_format($item->subtotal, 0, ',', '.') }}</div>
                                            <div class="text-muted small">Ongkir: Rp {{ number_format($item->order->shipping_cost, 0, ',', '.') }}</div>
                                        </td>
                                        <td>
                                            <div class="fw-600">{{ $item->qty }}x</div>
                                        </td>
                                        <td>
                                            <div class="fw-600">Rp {{ number_format($item->subtotal + ($item->order->shipping_cost ?? 0), 0, ',', '.') }}</div>
                                        </td>
                                        <td class="text-center">
                                            @php
                                                $statusClass = match($item->order->status) {
                                                    'pending' => 'bg-warning-subtle text-warning',
                                                    'paid' => 'bg-success-subtle text-success border border-success',
                                                    'processing' => 'bg-primary-subtle text-primary border border-primary',
                                                    'shipped' => 'bg-info-subtle text-info border border-info',
                                                    'finished' => 'bg-success text-white',
                                                    'cancelled' => 'bg-danger-subtle text-danger',
                                                    default => 'bg-secondary-subtle text-secondary'
                                                };
                                            @endphp
                                            <span class="badge {{ $statusClass }} rounded-pill px-3 py-2 text-capitalize">
                                                {{ $item->order->status === 'finished' ? 'Selesai' : $item->order->status }}
                                            </span>
                                        </td>
                                        <td class="pe-4 text-end">
                                            <div class="d-flex justify-content-end gap-2">
                                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" 
                                                        onclick="openDetailOrderModal({{ json_encode([
                                                            'id' => $item->order->id,
                                                            'user_name' => $item->order->user->name,
                                                            'user_email' => $item->order->user->email,
                                                            'nama_penerima' => $item->order->alamat->nama_penerima ?? $item->order->user->name,
                                                            'nomor_penerima' => $item->order->alamat->nomor_penerima ?? '-',
                                                            'alamat_lengkap' => $item->order->alamat->alamat ?? '-',
                                                            'wilayah' => ($item->order->alamat->kecamatan ?? '').', '.($item->order->alamat->kota ?? '').', '.($item->order->alamat->provinsi ?? ''),
                                                            'produk' => $item->nama_produk,
                                                            'qty' => $item->qty,
                                                            'harga' => $item->harga,
                                                            'subtotal' => $item->subtotal,
                                                            'ongkir' => $item->order->shipping_cost ?? 0,
                                                            'total' => $item->subtotal + ($item->order->shipping_cost ?? 0),
                                                            'kurir_kode' => $item->order->courier_code ?? '-',
                                                            'kurir' => ($item->order->courier_name ?? 'Kurir').' ('.($item->order->service_name ?? '-').')',
                                                            'status' => $item->order->status,
                                                            'resi' => $item->order->nomor_resi ?? '-'
                                                        ]) }})">
                                                    Detail
                                                </button>
                                                @if($item->order->status === 'paid')
                                                    <form action="{{ route('toko.order.accept', $item->order->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-success rounded-pill px-3">
                                                            Terima
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('toko.order.reject', $item->order->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menolak pesanan ini?')">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3">
                                                            Tolak
                                                        </button>
                                                    </form>
                                                @elseif($item->order->status === 'processing')
                                                    <button class="btn btn-sm btn-primary rounded-pill px-3" onclick="openShipModal({{ $item->order->id }})">
                                                        Kirim
                                                    </button>
                                                    <form action="{{ route('toko.order.reject', $item->order->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin membatalkan pesanan ini?')">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3">
                                                            Batal
                                                        </button>
                                                    </form>
                                                @elseif($item->order->status === 'shipped')
                                                    <div class="text-muted small">
                                                        Resi: <span class="fw-bold text-dark">{{ $item->order->nomor_resi }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Modal Full Description --}}
<div class="modal fade" id="modalFullDesc" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Deskripsi Toko</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <p class="text-muted" style="white-space: pre-line;">{{ $toko->deskripsi_toko }}</p>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-medcom-blue rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

@if($isOwner)
{{-- MODALS --}}

{{-- Modal Edit Toko --}}
<div class="modal fade" id="modalEditToko" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Pengaturan Toko</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formEditToko">
                    <div class="mb-3">
                        <label class="form-label fw-600">Nama Toko</label>
                        <input type="text" name="nama_toko" class="form-control" value="{{ $toko->nama_toko }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="deskripsi_toko" class="form-control" rows="3" required>{{ $toko->deskripsi_toko }}</textarea>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-md-4">
                            <label class="form-label small mb-1">Provinsi</label>
                            <select class="form-select form-select-sm" id="tokoProvinsi" required>
                                <option value="">Pilih Provinsi</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small mb-1">Kota</label>
                            <select class="form-select form-select-sm" id="tokoKota" required disabled>
                                <option value="">Pilih Kota</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small mb-1">Kecamatan</label>
                            <select class="form-select form-select-sm" id="tokoKecamatan" required disabled>
                                <option value="">Pilih Kecamatan</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small mb-1">Kode Pos</label>
                        <input type="text" name="kode_pos" id="tokoKodePos" class="form-control" value="{{ $toko->kode_pos ?? '' }}" placeholder="Contoh: 12345" required>
                    </div>

                    <input type="hidden" name="kode_wilayah" id="tokoKodeWilayah">
                    <input type="hidden" name="provinsi" id="hiddenProvinsi">
                    <input type="hidden" name="kota" id="hiddenKota">
                    <input type="hidden" name="kecamatan" id="hiddenKecamatan">

                    <div class="mb-3">
                        <label class="form-label">Alamat Lengkap (Jalan/No)</label>
                        <input type="text" name="lokasi" class="form-control" value="{{ $toko->lokasi }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Logo Baru (Opsional)</label>
                        <input type="file" name="logo" class="form-control" accept="image/*">
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary rounded-pill px-4" onclick="updateToko()">Simpan Perubahan</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Tambah Produk --}}
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Tambah Produk Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formTambah" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nama Produk</label>
                                <input type="text" name="nama" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Kategori</label>
                                <select name="category_id" class="form-select" required>
                                    <option value="">Pilih Kategori</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->judulKategori }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Harga (Rp)</label>
                                    <input type="number" name="harga" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Stok</label>
                                    <input type="number" name="stok" class="form-control" required>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Berat (Gram)</label>
                                    <input type="number" name="berat" class="form-control" value="1000" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Deskripsi</label>
                                <textarea name="deskripsi" class="form-control" rows="4" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Gambar</label>
                                <input type="file" name="image" class="form-control" accept="image/*" onchange="previewImage(this, 'previewTambah')">
                                <img id="previewTambah" class="preview-image" style="display:none">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary rounded-pill px-4" onclick="simpanProduk()">Terbitkan</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Edit Produk --}}
<div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Edit Produk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="editId">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Nama</label>
                            <input type="text" id="editNama" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kategori</label>
                            <select id="editKategori" class="form-select" required>
                                <option value="">Pilih Kategori</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->judulKategori }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-6 mb-3">
                                <label class="form-label">Harga</label>
                                <input type="number" id="editHarga" class="form-control" required>
                            </div>
                            <div class="col-6 mb-3">
                                <label class="form-label">Stok</label>
                                <input type="number" id="editStok" class="form-control" required>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">Berat (Gram)</label>
                                <input type="number" id="editBerat" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea id="editDeskripsi" class="form-control" rows="6" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Gambar</label>
                            <input type="file" id="editGambar" class="form-control" accept="image/*">
                            <img id="previewEditGambar" class="preview-image">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary rounded-pill px-4" onclick="updateProduk()">Simpan</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Detail Pesanan (STYLE DARI YANGBENER) --}}
<div class="modal fade" id="modalDetailOrder" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-medcom-blue text-white p-4">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <h5 class="modal-title fw-bold mb-0">Detail Pesanan #<span id="detailId"></span></h5>
                    <span id="detailStatusBadge" class="badge rounded-pill px-3 py-2 text-capitalize border border-white" style="font-size: 0.75rem;"></span>
                </div>
                <button type="button" class="btn-close btn-close-white ms-2" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3 mb-4">
                            <h6 class="fw-bold text-primary mb-3"><i class="bi bi-person-fill me-2"></i>Informasi Pembeli</h6>
                            <p class="mb-1 fw-bold" id="detailUserName"></p>
                            <p class="text-muted small mb-0" id="detailUserEmail"></p>
                        </div>
                        <div class="p-3 bg-light rounded-3">
                            <h6 class="fw-bold text-primary mb-3"><i class="bi bi-geo-alt-fill me-2"></i>Alamat Pengiriman</h6>
                            <p class="mb-1 fw-bold" id="detailNamaPenerima"></p>
                            <p class="mb-1 small text-muted" id="detailNomorPenerima"></p>
                            <p class="mb-2 small" id="detailAlamatLengkap"></p>
                            <p class="small text-muted" id="detailWilayah"></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 border rounded-3 h-100">
                            <h6 class="fw-bold text-medcom-blue mb-3"><i class="bi bi-bag-check-fill me-2"></i>Informasi Produk & Pembayaran</h6>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Produk</span>
                                <span class="fw-bold" id="detailProduk"></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Harga Satuan</span>
                                <span id="detailHarga"></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Jumlah</span>
                                <span id="detailQty"></span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Subtotal Produk</span>
                                <span class="fw-bold" id="detailSubtotal"></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Ongkos Kirim</span>
                                <span class="fw-bold" id="detailOngkir"></span>
                            </div>
                            <div class="d-flex justify-content-between mt-3 p-2 bg-primary-subtle rounded">
                                <span class="fw-bold">Total Pembayaran</span>
                                <span class="fw-bold text-primary" id="detailTotal"></span>
                            </div>
                            <hr>
                            <div class="mb-2">
                                <span class="text-muted small d-block">Metode Pengiriman</span>
                                <span class="fw-bold" id="detailKurir"></span>
                            </div>
                            <div class="mb-0">
                                <span class="text-muted small d-block">Nomor Resi</span>
                                <span class="badge bg-secondary" id="detailResi"></span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tracking Timeline Section --}}
                <div id="trackingSection" class="mt-4 d-none">
                    <h6 class="fw-bold small text-muted text-uppercase mb-3">Status Pengiriman (Live Tracking)</h6>
                    <div id="trackingTimeline" class="p-3 bg-white border rounded-3 overflow-auto" style="max-height: 250px;">
                        {{-- Timeline items will be injected here --}}
                        <div class="text-center py-3 text-muted small">
                            <div class="spinner-border spinner-border-sm me-2" role="status"></div> Memuat status pengiriman...
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn btn-medcom-blue rounded-pill px-5 w-100 py-2 fw-bold" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Hapus --}}
<div class="modal fade" id="modalHapus" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content text-center">
            <div class="modal-body p-4">
                <input type="hidden" id="hapusId">
                <div class="mb-3 text-danger" style="font-size: 3rem;"><i class="bi bi-exclamation-circle"></i></div>
                <h5 class="fw-bold">Hapus Produk?</h5>
                <p class="text-muted small">Tindakan ini tidak dapat dibatalkan.</p>
                <div class="d-flex gap-2 justify-content-center mt-4">
                    <button type="button" class="btn btn-light rounded-pill flex-fill" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger rounded-pill flex-fill" onclick="hapusProduk()">Hapus</button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Kirim --}}
<div class="modal fade" id="modalShip" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Input Nomor Resi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formShip" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small">Nomor Resi Pengiriman</label>
                        <input type="text" name="nomor_resi" class="form-control" placeholder="Contoh: JNE123456789" required>
                    </div>
                    <p class="small text-muted mb-0">Status pesanan akan berubah menjadi <span class="badge bg-info-subtle text-info">Shipped</span></p>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Kirim Pesanan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Preview CSV --}}
<div class="modal fade" id="modalPreviewCSV" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">Pratinjau Impor Produk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small mb-3">Silakan periksa kembali data produk Anda sebelum disimpan. Anda dapat mengedit atau menghapus baris langsung dari tabel ini.</p>
                <div class="table-responsive" style="max-height: 400px;">
                    <table class="table table-sm table-hover align-middle">
                        <thead class="bg-light sticky-top">
                            <tr>
                                <th>Nama Produk</th>
                                <th style="width: 150px;">Kategori</th>
                                <th style="width: 120px;">Harga (Rp)</th>
                                <th style="width: 80px;">Stok</th>
                                <th style="width: 100px;">Berat (g)</th>
                                <th>Deskripsi</th>
                                <th style="width: 50px;"></th>
                            </tr>
                        </thead>
                        <tbody id="previewCSVBody"></tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                <button type="button" id="btnSubmitImport" class="btn btn-primary rounded-pill px-4" onclick="submitImport()">Simpan Semua Produk</button>
            </div>
        </div>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/PapaParse/5.4.1/papaparse.min.js"></script>
<script>
    const TOKO_ID = {{ $toko->id }};
    const STORE_PRODUCT_URL = "{{ route('product.store') }}";
    let csvData = []; 

    @if($isOwner)
    function openDetailOrderModal(data) {
        document.getElementById('detailId').textContent = data.id;
        document.getElementById('detailUserName').textContent = data.user_name;
        document.getElementById('detailUserEmail').textContent = data.user_email;
        document.getElementById('detailNamaPenerima').textContent = data.nama_penerima;
        document.getElementById('detailNomorPenerima').textContent = data.nomor_penerima;
        document.getElementById('detailAlamatLengkap').textContent = data.alamat_lengkap;
        document.getElementById('detailWilayah').textContent = data.wilayah;
        document.getElementById('detailProduk').textContent = data.produk;
        document.getElementById('detailHarga').textContent = 'Rp ' + data.harga.toLocaleString('id-ID');
        document.getElementById('detailQty').textContent = data.qty + 'x';
        document.getElementById('detailSubtotal').textContent = 'Rp ' + data.subtotal.toLocaleString('id-ID');
        document.getElementById('detailOngkir').textContent = 'Rp ' + data.ongkir.toLocaleString('id-ID');
        document.getElementById('detailTotal').textContent = 'Rp ' + data.total.toLocaleString('id-ID');
        document.getElementById('detailKurir').textContent = data.kurir;
        document.getElementById('detailResi').textContent = data.resi;

        // --- Status Badge ---
        const badge = document.getElementById('detailStatusBadge');
        badge.textContent = data.status === 'finished' ? 'Selesai' : data.status;
        badge.className = 'badge rounded-pill px-3 py-2 text-capitalize border border-white';
        const s = data.status;
        if (s === 'pending') badge.classList.add('bg-warning');
        else if (s === 'paid') badge.classList.add('bg-success');
        else if (s === 'processing') badge.classList.add('bg-primary');
        else if (s === 'shipped') badge.classList.add('bg-info');
        else if (s === 'finished') badge.classList.add('bg-success');
        else if (s === 'cancelled') badge.classList.add('bg-danger');
        else badge.classList.add('bg-secondary');

        // --- Live Tracking Logic ---
        const trackingSection = document.getElementById('trackingSection');
        const trackingTimeline = document.getElementById('trackingTimeline');
        console.log("INI KENAPA")
        console.log(data.resi)
        console.log(data.kurir_kode)
        if (data.resi && data.resi !== '-' && data.kurir_kode) {
            trackingSection.classList.remove('d-none');
            trackingTimeline.innerHTML = '<div class="text-center py-3 text-muted small"><div class="spinner-border spinner-border-sm me-2" role="status"></div> Memuat status pengiriman...</div>';
            
            fetch(`/api/shipping/track/${data.resi}/${data.kurir_kode}`)
                .then(res => res.json())
                .then(res => {
                    console.log(res.data.data.histories)
                    if (res.data && res.data.data.histories) {
let html = '<div class="timeline-small">';
                        res.data.data.histories.forEach((h, index) => {
                            html += `
                                <div class="d-flex mb-3">
                                    <div class="me-3 text-center" style="width: 20px;">
                                        <i class="bi bi-circle-fill text-${index === 0 ? 'primary' : 'secondary'} small"></i>
                                        ${index !== res.data.data.histories.length - 1 ? '<div class="border-start mx-auto h-100" style="width: 1px; min-height: 20px;"></div>' : ''}
                                    </div>
                                    <div>
                                        <div class="fw-bold small">${h.status}</div>
                                        <div class="text-muted" style="font-size: 0.75rem;">
                                            ${new Date(h.date).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' })} - ${h.message || ''}
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                        html += '</div>';
                        trackingTimeline.innerHTML = html;
                    } else {
                        trackingTimeline.innerHTML =
                            '<div class="text-center py-3 text-muted small">Data pelacakan belum tersedia.</div>';
                    }
                })
                .catch(err => {
                    console.error('Tracking Error:', err);
                    trackingTimeline.innerHTML = '<div class="text-center py-3 text-danger small">Gagal memuat data pelacakan.</div>';
                });
        } else {
            trackingSection.classList.add('d-none');
        }

        new bootstrap.Modal(document.getElementById('modalDetailOrder')).show();
    }

    function openShipModal(orderId) {
        const modal = new bootstrap.Modal(document.getElementById('modalShip'));
        const form = document.getElementById('formShip');
        form.action = `/toko/orders/${orderId}/ship`;
        modal.show();
    }

    function importCSV(input) {
        if (!input.files || !input.files[0]) return;
        
        const file = input.files[0];
        Papa.parse(file, {
            header: true,
            skipEmptyLines: true,
            complete: function(results) {
                csvData = results.data.map((row, index) => ({
                    id: index,
                    nama: row.nama_produk || row[Object.keys(row)[0]],
                    category_id: row.category_id || row[Object.keys(row)[1]],
                    harga: row.harga || row[Object.keys(row)[2]],
                    stok: row.stok || row[Object.keys(row)[3]],
                    berat: row.berat || row[Object.keys(row)[4]],
                    deskripsi: row.deskripsi || row[Object.keys(row)[5]]
                }));
                renderPreviewTable();
                new bootstrap.Modal(document.getElementById('modalPreviewCSV')).show();
                input.value = '';
            }
        });
    }

    function renderPreviewTable() {
        const tbody = document.getElementById('previewCSVBody');
        tbody.innerHTML = '';
        csvData.forEach((row, index) => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td><input type="text" class="form-control form-control-sm" value="${row.nama}" onchange="updateCSVRow(${index}, 'nama', this.value)"></td>
                <td>
                    <select class="form-select form-select-sm" onchange="updateCSVRow(${index}, 'category_id', this.value)">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" ${row.category_id == {{ $cat->id }} ? 'selected' : ''}>{{ $cat->judulKategori }}</option>
                        @endforeach
                    </select>
                </td>
                <td><input type="number" class="form-control form-control-sm" value="${row.harga}" onchange="updateCSVRow(${index}, 'harga', this.value)"></td>
                <td><input type="number" class="form-control form-control-sm" value="${row.stok}" onchange="updateCSVRow(${index}, 'stok', this.value)"></td>
                <td><input type="number" class="form-control form-control-sm" value="${row.berat}" onchange="updateCSVRow(${index}, 'berat', this.value)"></td>
                <td><input type="text" class="form-control form-control-sm" value="${row.deskripsi}" onchange="updateCSVRow(${index}, 'deskripsi', this.value)"></td>
                <td>
                    <button class="btn btn-sm btn-danger" onclick="removeCSVRow(${index})"><i class="bi bi-trash"></i></button>
                </td>
            `;
            tbody.appendChild(tr);
        });
    }

    function updateCSVRow(index, field, value) {
        csvData[index][field] = value;
    }

    function removeCSVRow(index) {
        csvData.splice(index, 1);
        renderPreviewTable();
    }

    function submitImport() {
        if (csvData.length === 0) return;

        const btn = document.getElementById('btnSubmitImport');
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Memproses...';

        fetch("{{ route('product.import.bulk') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ products: csvData })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert(`Berhasil mengimpor ${data.count} produk.`);
                location.reload();
            } else {
                alert(data.message || 'Gagal mengimpor produk.');
            }
        })
        .catch(err => {
            console.error(err);
            alert('Terjadi kesalahan saat mengimpor data.');
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = originalText;
        });
    }

    @endif

    // Persistensi Tab setelah Refresh
    document.addEventListener('DOMContentLoaded', function() {
        @if($isOwner)
        prefillTokoLocation();
        const tokoProv = document.getElementById('tokoProvinsi');
        const tokoKota = document.getElementById('tokoKota');
        const tokoKec = document.getElementById('tokoKecamatan');
        const tokoKode = document.getElementById('tokoKodeWilayah');

        async function fetchWilayah(url) {
            const res = await fetch(url);
            return await res.json();
        }

        async function loadProvinces() {
            const data = await fetchWilayah('/api/wilayah/provinsi');
            tokoProv.innerHTML = '<option value="">Pilih Provinsi</option>';
            data.forEach(p => {
                const opt = document.createElement('option');
                opt.value = p.kode;
                opt.textContent = p.nama;
                tokoProv.appendChild(opt);
            });
        }

        async function handleProvChange() {
            tokoKota.innerHTML = '<option value="">Pilih Kota</option>';
            tokoKec.innerHTML = '<option value="">Pilih Kecamatan</option>';
            tokoKec.disabled = true;
            if (tokoProv.value) {
                const data = await fetchWilayah(`/api/wilayah/kota/${tokoProv.value}`);
                data.forEach(c => {
                    const opt = document.createElement('option');
                    opt.value = c.kode;
                    opt.textContent = c.nama;
                    tokoKota.appendChild(opt);
                });
                tokoKota.disabled = false;
            }
            updateHiddenFields();
        }

        async function handleKotaChange() {
            tokoKec.innerHTML = '<option value="">Pilih Kecamatan</option>';
            if (tokoKota.value) {
                const data = await fetchWilayah(`/api/wilayah/kecamatan/${tokoKota.value}`);
                data.forEach(d => {
                    const opt = document.createElement('option');
                    opt.value = d.kode;
                    opt.textContent = d.nama;
                    tokoKec.appendChild(opt);
                });
                tokoKec.disabled = false;
            }
            updateHiddenFields();
        }

        function updateHiddenFields() {
            tokoKode.value = tokoKec.value || tokoKota.value || tokoProv.value || "";
            document.getElementById('hiddenProvinsi').value = tokoProv.options[tokoProv.selectedIndex]?.text || '';
            document.getElementById('hiddenKota').value = tokoKota.options[tokoKota.selectedIndex]?.text || '';
            document.getElementById('hiddenKecamatan').value = tokoKec.options[tokoKec.selectedIndex]?.text || '';
        }

        tokoProv.addEventListener('change', handleProvChange);
        tokoKota.addEventListener('change', handleKotaChange);
        tokoKec.addEventListener('change', updateHiddenFields);

        async function prefillTokoLocation() {
            await loadProvinces();
            const currentKode = "{{ $toko->kode_wilayah ?? '' }}";
            if (currentKode) {
                const provCode = currentKode.substring(0, 2);
                const cityCode = currentKode.substring(0, 5);
                tokoProv.value = provCode;
                await handleProvChange();
                tokoKota.value = cityCode;
                await handleKotaChange();
                tokoKec.value = currentKode;
                updateHiddenFields();
            }
        }
        @endif

        const hash = window.location.hash;
        if (hash) {
            const tabEl = document.querySelector(`button[data-bs-target="${hash}"]`);
            if (tabEl) bootstrap.Tab.getOrCreateInstance(tabEl).show();
        }

        const tabButtons = document.querySelectorAll('button[data-bs-toggle="pill"]');
        tabButtons.forEach(btn => {
            btn.addEventListener('shown.bs.tab', function(e) {
                history.replaceState(null, null, e.target.getAttribute('data-bs-target'));
            });
        });

        const searchInputToko = document.getElementById('search-produk-toko');
        const filterKategoriToko = document.getElementById('filter-kategori-toko');

        function filterProdukToko() {
            const term = searchInputToko.value.toLowerCase().trim();
            const cat = filterKategoriToko.value.trim();
            const cards = document.querySelectorAll('.product-card');
            
            cards.forEach(card => {
                const title = card.querySelector('.product-title').textContent.toLowerCase();
                const cardCat = card.getAttribute('data-category').trim();
                
                const matchTitle = title.includes(term);
                const matchCat = cat === "" || cardCat === cat;
                
                if (matchTitle && matchCat) {
                    card.style.setProperty('display', 'block', 'important');
                } else {
                    card.style.setProperty('display', 'none', 'important');
                }
            });
        }

        if (searchInputToko) searchInputToko.addEventListener('input', filterProdukToko);
        if (filterKategoriToko) filterKategoriToko.addEventListener('change', filterProdukToko);

        window.openFullDescModal = function() {
            new bootstrap.Modal(document.getElementById('modalFullDesc')).show();
        };
    });
</script>
<script src="{{ asset('js/mengelolaProdukCRUD.js') }}"></script>
@endpush