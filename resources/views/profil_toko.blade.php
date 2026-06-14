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
                        <h1>{{ $toko->nama_toko }}</h1>
                        <p>{{ $toko->deskripsi_toko }}</p>
                        <div class="d-flex gap-2 flex-wrap">
                            <span class="shop-badge">
                                <i class="bi bi-geo-alt"></i> {{ $toko->lokasi }}
                            </span>
                            <span class="shop-badge">
                                <i class="bi bi-calendar3"></i> Tergabung {{ \Carbon\Carbon::parse($toko->created_at)->format('M Y') }}
                            </span>
                        </div>
                    </div>
                    <button class="btn btn-outline-dark rounded-pill px-4" onclick="openEditTokoModal()">
                        <i class="bi bi-pencil-square me-2"></i> Edit Profil
                    </button>
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
                    <i class="bi bi-grid me-2"></i> Produk Anda
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link rounded-pill px-4 fw-600" id="orders-tab" data-bs-toggle="pill" data-bs-target="#orders" type="button" role="tab">
                    <i class="bi bi-cart-check me-2"></i> Pesanan Masuk
                </button>
            </li>
        </ul>

        <div class="tab-content" id="shopTabsContent">
            {{-- Products Tab --}}
            <div class="tab-pane fade show active" id="products" role="tabpanel">
                <div class="section-header mt-0">
                    <div>
                        <h2 class="fw-bold mb-0" style="color: var(--primary-dark)">Daftar Produk</h2>
                        <p class="text-muted mb-0">Kelola stok dan informasi produk Anda</p>
                    </div>
                    <button class="btn-add-product" onclick="openTambahModal()">
                        <i class="bi bi-plus-lg"></i> Tambah Produk Baru
                    </button>
                </div>

                @if($toko->products->count() == 0)
                    <div class="text-center py-5 bg-white rounded-4 shadow-sm">
                        <i class="bi bi-inbox text-muted" style="font-size: 4rem;"></i>
                        <h4 class="mt-3 text-muted">Belum ada produk di toko Anda</h4>
                        <p class="text-muted">Mulai berjualan dengan menambahkan produk pertama Anda.</p>
                        <button class="btn btn-primary rounded-pill px-4" onclick="openTambahModal()">
                            Tambah Produk Sekarang
                        </button>
                    </div>
                @else
                    <div class="product-grid" id="produk-list">
                        @foreach($toko->products as $p)
                        <div class="product-card" id="produk-{{ $p->id }}">
                            <div class="product-image-wrapper">
                                <img src="{{ asset('storage/'.$p->imagePath) }}" 
                                     class="product-image" 
                                     alt="{{ $p->nama }}">
                            </div>
                            <div class="product-content">
                                <span class="product-category">Sparepart</span>
                                <h3 class="product-title">{{ $p->nama }}</h3>
                                <div class="d-flex justify-content-between align-items-end">
                                    <span class="product-price">Rp {{ number_format($p->harga, 0, ',', '.') }}</span>
                                </div>
                                <p class="product-stock">Stok: <span class="fw-bold text-dark">{{ $p->stok }}</span></p>
                                
                                <div class="product-actions">
                                    <button class="btn-action btn-edit" 
                                        onclick="openEditModal(
                                            {{ $p->id }},
                                            {{ json_encode($p->nama) }},
                                            {{ $p->harga }},
                                            {{ $p->stok }},
                                            '{{ $p->imagePath }}',
                                            {{ json_encode($p->deskripsi) }}
                                        )">
                                        <i class="bi bi-pencil"></i> Edit
                                    </button>
                                    <button class="btn-action btn-delete" onclick="openHapusModal({{ $p->id }})">
                                        <i class="bi bi-trash"></i> Hapus
                                    </button>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Orders Tab --}}
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
                                        <th class="py-3">Subtotal</th>
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
                                        <td class="fw-600">
                                            {{ $item->nama_produk }}
                                            <div class="mt-1">
                                                <span class="badge bg-light text-dark border fw-normal" style="font-size: 0.7rem;">
                                                    <i class="bi bi-truck text-primary"></i> {{ $item->order->courier_name ?? 'Kurir' }} ({{ $item->order->service_name ?? '-' }})
                                                </span>
                                                <div class="text-muted" style="font-size: 0.7rem;">Ongkir: Rp {{ number_format($item->order->shipping_cost ?? 0, 0, ',', '.') }}</div>
                                            </div>
                                        </td>
                                        <td>{{ $item->qty }}x</td>
                                        <td class="fw-bold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                                        <td class="text-center">
                                            @php
                                                $statusClass = match($item->order->status) {
                                                    'pending' => 'bg-warning-subtle text-warning',
                                                    'paid' => 'bg-success-subtle text-success border border-success',
                                                    'processing' => 'bg-primary-subtle text-primary border border-primary',
                                                    'shipped' => 'bg-info-subtle text-info border border-info',
                                                    'cancelled' => 'bg-danger-subtle text-danger',
                                                    default => 'bg-secondary-subtle text-secondary'
                                                };
                                            @endphp
                                            <span class="badge {{ $statusClass }} rounded-pill px-3 py-2 text-capitalize">
                                                <i class="bi bi-circle-fill me-1" style="font-size: 0.5rem"></i>
                                                {{ $item->order->status }}
                                            </span>
                                        </td>
                                        <td class="pe-4 text-end">
                                            <div class="d-flex justify-content-end gap-2">
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
                                                <button class="btn btn-sm btn-light border rounded-pill px-3" onclick="alert('Detail pesanan #{{ $item->order->id }} akan segera hadir!')">
                                                    Detail
                                                </button>
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
        </div>
    </div>
</div>

{{-- MODALS - Keeping same IDs for compatibility with mengelolaProdukCRUD.js --}}

{{-- Modal Edit Toko --}}
<div class="modal fade" id="modalEditToko" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
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
                    <div class="mb-3">
                        <label class="form-label">Lokasi</label>
                        <input type="text" name="lokasi" class="form-control" value="{{ $toko->lokasi }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Logo Baru (Opsional)</label>
                        <input type="file" name="logo" class="form-control" accept="image/*">
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0">
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
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Tambah Produk Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formTambah" enctype="multipart/form-data">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Nama Produk</label>
                                <input type="text" name="nama" class="form-control" placeholder="Contoh: Busi Racing Iridium" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Kategori</label>
                                <select name="category_id" class="form-select" required>
                                    <option value="1">Sparepart Mesin</option>
                                    <option value="2">Sparepart Body</option>
                                    <option value="3">Aksesoris</option>
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
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Deskripsi Produk</label>
                                <textarea name="deskripsi" class="form-control" rows="4" placeholder="Jelaskan detail produk Anda..." required></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Gambar Produk</label>
                                <input type="file" name="image" class="form-control" accept="image/*" required onchange="previewImage(this, 'previewTambah')">
                                <img id="previewTambah" class="preview-image" style="display:none">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary rounded-pill px-4" onclick="simpanProduk()">Terbitkan Produk</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal Edit Produk --}}
<div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Edit Informasi Produk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="editId">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Nama Produk</label>
                            <input type="text" id="editNama" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kategori</label>
                            <select id="editKategori" class="form-select" required>
                                <option value="1">Sparepart Mesin</option>
                                <option value="2">Sparepart Body</option>
                            </select>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Harga (Rp)</label>
                                <input type="number" id="editHarga" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Stok</label>
                                <input type="number" id="editStok" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea id="editDeskripsi" class="form-control" rows="4" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Ganti Gambar (Opsional)</label>
                            <input type="file" id="editGambar" class="form-control" accept="image/*">
                            <img id="previewEditGambar" class="preview-image">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary rounded-pill px-4" onclick="updateProduk()">Simpan Perubahan</button>
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
                <div class="mb-3 text-danger" style="font-size: 3rem;">
                    <i class="bi bi-exclamation-circle"></i>
                </div>
                <h5 class="fw-bold">Hapus Produk?</h5>
                <p class="text-muted">Tindakan ini tidak dapat dibatalkan.</p>
                <div class="d-flex gap-2 justify-content-center mt-4">
                    <button type="button" class="btn btn-light rounded-pill flex-fill" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger rounded-pill flex-fill" onclick="hapusProduk()">Hapus</button>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

{{-- Modal Kirim (Input Resi) --}}
<div class="modal fade" id="modalShip" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Input Nomor Resi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="formShip" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nomor Resi Pengiriman</label>
                        <input type="text" name="nomor_resi" class="form-control" placeholder="Contoh: JNE123456789" required>
                    </div>
                    <p class="small text-muted mb-0">Status pesanan akan berubah menjadi <span class="badge bg-info-subtle text-info">Shipped</span></p>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">Konfirmasi Kirim</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const TOKO_ID = {{ $toko->id }};
    const STORE_PRODUCT_URL = "{{ route('product.store') }}";

    function openShipModal(orderId) {
        const modal = new bootstrap.Modal(document.getElementById('modalShip'));
        const form = document.getElementById('formShip');
        form.action = `/toko/orders/${orderId}/ship`;
        modal.show();
    }

    // Persistensi Tab setelah Refresh
    document.addEventListener('DOMContentLoaded', function() {
        // Ambil hash dari URL (misal: #orders)
        const hash = window.location.hash;
        if (hash) {
            const tabEl = document.querySelector(`button[data-bs-target="${hash}"]`);
            if (tabEl) {
                bootstrap.Tab.getOrCreateInstance(tabEl).show();
            }
        }

        // Simpan hash ke URL setiap kali tab diklik
        const tabButtons = document.querySelectorAll('button[data-bs-toggle="pill"]');
        tabButtons.forEach(btn => {
            btn.addEventListener('shown.bs.tab', function(e) {
                const target = e.target.getAttribute('data-bs-target');
                history.replaceState(null, null, target);
            });
        });
    });
</script>
<script src="{{ asset('js/mengelolaProdukCRUD.js') }}"></script>
@endpush
