@extends('layouts.main')

@section('title', 'Detail Produk - SpareHub')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <style>
        :root {
            --navy: #122C4F;
            --navy-2: #1a3a66;
            --blue: #0066CC;
            --gold: #FFC107;
            --star: #FFA500;
            --cream: #F4E9DC;
            --cream-2: #EFE2D1;
            --ink: #202124;
            --body: #3C4043;
            --muted: #5F6368;
            --line: #E5DFD3;
            --line-2: #DADCE0;
            --ok: #1E8E3E;
            --danger: #EA4335;
        }

        body {
            font-family: 'Roboto', sans-serif;
            background-color: var(--cream);
            color: var(--body);
        }

        .pdp-frame {
            max-width: 1180px;
            margin: 0 auto;
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 4px 32px rgba(18, 44, 79, 0.10);
            padding: 36px 40px 48px;
        }

        /* Breadcrumb */
        .pdp-breadcrumb {
            font-size: 13px;
            color: var(--muted);
            margin-bottom: 24px;
        }
        .pdp-breadcrumb a {
            color: var(--blue);
            text-decoration: none;
        }
        .pdp-breadcrumb a:hover { text-decoration: underline; }
        .pdp-breadcrumb .sep { margin: 0 6px; color: var(--line-2); }

        /* 2-column layout */
        .pdp-cols {
            display: grid;
            grid-template-columns: 45% 55%;
            gap: 40px;
            align-items: start;
        }

        /* Left column */
        .pdp-img-main {
            min-height: 380px;
            border: 1.5px solid var(--line);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--cream);
            overflow: hidden;
            padding: 16px;
        }
        .pdp-img-main img {
            max-height: 380px;
            width: 100%;
            object-fit: contain;
        }
        .pdp-thumbs {
            display: flex;
            gap: 10px;
            margin-top: 12px;
        }
        .pdp-thumb {
            flex: 1;
            height: 72px;
            border: 1.5px solid var(--line);
            border-radius: 8px;
            overflow: hidden;
            background: var(--cream-2);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: border-color .2s;
        }
        .pdp-thumb:hover { border-color: var(--blue); }

        /* Thumbnail image + fallback logic */
        .pdp-thumb-img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            padding: 4px;
            display: block;
        }
        /* Hidden fallback by default */
        .pdp-thumb-fallback {
            display: none;
            width: 100%;
            height: 100%;
            align-items: center;
            justify-content: center;
        }
        .pdp-thumb-fallback i { font-size: 22px; color: rgba(18,44,79,.2); }
        /* When img fails, parent gets this class → show fallback, hide img */
        .pdp-thumb-broken .pdp-thumb-img { display: none; }
        .pdp-thumb-broken .pdp-thumb-fallback { display: flex; }
        /* For no-image case */
        .pdp-thumb-broken-show { display: flex !important; }

        /* Right column */
        .pdp-title {
            font-family: 'Poppins', sans-serif;
            font-weight: 800;
            font-size: 1.7rem;
            color: var(--navy);
            margin-bottom: 8px;
            line-height: 1.3;
        }
        .pdp-rating-line {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 14px;
            padding-bottom: 14px;
            border-bottom: 1px solid var(--line);
        }
        .pdp-stars { color: var(--star); font-size: 1rem; letter-spacing: 1px; }
        .pdp-avg { font-family: 'Poppins', sans-serif; font-weight: 700; color: var(--star); }
        .pdp-rcount { font-size: 13px; color: var(--muted); }

        /* Price block */
        .pdp-price-block { margin-bottom: 8px; }
        .pdp-price {
            font-family: 'Poppins', sans-serif;
            font-weight: 800;
            font-size: 2rem;
            color: var(--blue);
        }
        .pdp-original-price {
            font-size: 1rem;
            color: var(--muted);
            text-decoration: line-through;
            margin-left: 8px;
        }
        .pdp-discount-badge {
            display: inline-block;
            background: var(--danger);
            color: #fff;
            font-size: 12px;
            font-weight: 700;
            border-radius: 6px;
            padding: 2px 8px;
            margin-left: 8px;
            vertical-align: middle;
        }

        /* Stock */
        .pdp-stock-text {
            font-size: 14px;
            color: var(--muted);
            margin-bottom: 16px;
        }
        .pdp-stock-text strong { color: var(--ink); }

        /* Qty counter */
        .pdp-qty-row {
            display: flex;
            align-items: center;
            gap: 0;
            margin-bottom: 8px;
        }
        .pdp-qty-btn {
            width: 38px;
            height: 38px;
            border: 1.5px solid var(--line-2);
            background: #fff;
            color: var(--navy);
            font-size: 1.2rem;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background .15s, color .15s;
        }
        .pdp-qty-btn:first-child { border-radius: 20px 0 0 20px; }
        .pdp-qty-btn:last-child  { border-radius: 0 20px 20px 0; }
        .pdp-qty-btn:hover { background: var(--navy); color: #fff; }
        .pdp-qty-display {
            min-width: 48px;
            height: 38px;
            border-top: 1.5px solid var(--line-2);
            border-bottom: 1.5px solid var(--line-2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            font-size: 1rem;
            color: var(--ink);
            background: #fff;
        }
        .pdp-stock-warning {
            font-size: 12px;
            color: var(--danger);
            margin-bottom: 14px;
        }

        /* Total price */
        .pdp-total-row {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
        }
        .pdp-total-label { font-size: 14px; color: var(--muted); }
        .pdp-total-value {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            font-size: 1.2rem;
            color: var(--navy);
        }

        /* CTA buttons */
        .pdp-cta { display: flex; gap: 12px; margin-bottom: 20px; }
        .pdp-btn {
            flex: 1;
            padding: 13px 0;
            border-radius: 10px;
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            font-size: 1rem;
            border: none;
            cursor: pointer;
            transition: opacity .15s, transform .1s;
        }
        .pdp-btn:hover { opacity: .88; transform: translateY(-1px); }
        .pdp-btn-cart { background: var(--navy); color: #fff; }
        .pdp-btn-buy  { background: var(--blue); color: #fff; }

        /* Color swatches */
        .pdp-swatches-section { margin-bottom: 16px; }
        .pdp-swatches-label { font-size: 13px; font-weight: 600; color: var(--ink); margin: 0 0 8px; }
        .pdp-swatches { display: flex; gap: 8px; }
        .pdp-swatch {
            width: 28px; height: 28px; border-radius: 50%; cursor: pointer;
            border: 2px solid transparent; transition: transform .15s, border-color .15s;
        }
        .pdp-swatch:hover { transform: scale(1.15); }
        .pdp-swatch-active { border-color: var(--navy) !important; box-shadow: 0 0 0 2px #fff, 0 0 0 4px var(--navy); }

        /* Qty section */
        .pdp-qty-section { display: flex; align-items: center; gap: 16px; margin-bottom: 16px; }
        .pdp-stock-inline { font-size: 13px; color: var(--body); }
        .pdp-stock-warn-inline { color: var(--danger); font-size: 13px; line-height: 1.4; }
        .pdp-stock-warn-inline i { margin-right: 4px; }

        /* Info cards */
        .pdp-info-cards { display: flex; gap: 12px; margin-bottom: 20px; flex-direction: column; }
        .pdp-info-card {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 10px;
            padding: 14px 16px;
            display: flex;
            align-items: flex-start;
            gap: 12px;
            font-size: 13px;
            color: var(--body);
        }
        .pdp-info-card i { font-size: 1.4rem; flex-shrink: 0; margin-top: 2px; }
        .pdp-info-card strong { display: block; font-weight: 600; color: var(--ink); margin-bottom: 2px; }

        /* Toko */
        .pdp-toko {
            display: flex;
            align-items: center;
            gap: 14px;
            background: var(--cream);
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 14px 16px;
        }
        .pdp-toko-logo {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--blue);
            flex-shrink: 0;
        }
        .pdp-toko-name {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            color: var(--navy);
            margin: 0 0 2px;
        }
        .pdp-toko-loc {
            font-size: 13px;
            color: var(--muted);
            margin: 0;
        }

        /* Description section */
        .pdp-desc-section {
            margin-top: 40px;
            background: var(--cream);
            border-radius: 12px;
            padding: 24px 28px;
            border: 1px solid var(--line);
        }
        .pdp-desc-section h5 {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            color: var(--navy);
            margin-bottom: 12px;
        }
        .pdp-desc-section p {
            color: var(--body);
            line-height: 1.75;
            margin: 0;
        }

        /* Reviews section */
        .pdp-reviews-section { margin-top: 40px; }
        .pdp-reviews-section h4 {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            color: var(--navy);
            margin-bottom: 20px;
        }
        .pdp-reviews-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 18px;
        }
        .pdp-review-card {
            background: var(--cream);
            border: 1px solid var(--line);
            border-radius: 14px;
            padding: 18px 18px 14px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .pdp-review-header {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .pdp-review-avatar {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--navy) 0%, var(--blue) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            font-size: 1rem;
            color: #fff;
            flex-shrink: 0;
            overflow: hidden;
        }
        .pdp-review-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }
        .pdp-review-meta { flex: 1; min-width: 0; }
        .pdp-review-name {
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            color: var(--ink);
            font-size: 14px;
            margin: 0 0 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .pdp-review-date { font-size: 12px; color: var(--muted); margin: 0; }
        .pdp-review-stars { color: var(--star); font-size: 14px; letter-spacing: 1px; }
        .pdp-review-text { font-size: 14px; color: var(--body); line-height: 1.6; margin: 0; }
        .pdp-no-reviews { text-align: center; color: var(--muted); padding: 32px 0; }

        @media (max-width: 900px) {
            .pdp-cols { grid-template-columns: 1fr; }
            .pdp-reviews-grid { grid-template-columns: repeat(2, 1fr); }
            .pdp-frame { padding: 20px 16px 32px; }
        }
        @media (max-width: 600px) {
            .pdp-reviews-grid { grid-template-columns: 1fr; }
            .pdp-info-cards { flex-direction: column; }
            .pdp-cta { flex-direction: column; }
            .pdp-title { font-size: 1.3rem; }
        }
    </style>
@endpush

@section('content')
    @php
        $imagePath = $product->imagePath ?? '/img/iconOli.png';
        if (!str_starts_with($imagePath, 'http')) {
            if (str_starts_with($imagePath, '/storage/')) {
            } elseif (str_starts_with($imagePath, 'storage/')) {
                $imagePath = '/' . $imagePath;
            } elseif (str_starts_with($imagePath, 'produk/') || str_starts_with($imagePath, 'images/')) {
                $imagePath = '/storage/' . $imagePath;
            } elseif (str_starts_with($imagePath, '/img/') || str_starts_with($imagePath, 'img/')) {
                if (!str_starts_with($imagePath, '/')) {
                    $imagePath = '/' . $imagePath;
                }
            } else {
                $imagePath = '/storage/' . $imagePath;
            }
        }

        $tokoLogo = $product->toko->logo_path ?? '/img/iconPengguna.png';
        if (!str_starts_with($tokoLogo, 'http') && !str_starts_with($tokoLogo, '/img/')) {
            if (str_starts_with($tokoLogo, 'toko/')) {
                $tokoLogo = '/storage/' . $tokoLogo;
            } elseif (!str_starts_with($tokoLogo, '/storage/')) {
                $tokoLogo = '/storage/' . $tokoLogo;
            }
        }
    @endphp

    <div class="container-fluid py-4 px-3">
        <div class="pdp-frame">

            {{-- Breadcrumb --}}
            <nav class="pdp-breadcrumb" aria-label="breadcrumb">
                <a href="/">Beranda</a>
                <span class="sep">&rsaquo;</span>
                <a href="/produk">Produk</a>
                <span class="sep">&rsaquo;</span>
                <span>{{ $product->nama }}</span>
            </nav>

            {{-- 2-column PDP layout --}}
            <div class="pdp-cols">

                {{-- Left column: image --}}
                <div>
                    <div class="pdp-img-main" id="main-img-wrap">
                        <img
                            id="product-image"
                            src="{{ $imagePath }}"
                            alt="{{ $product->nama }}"
                            style="max-height:360px;width:100%;object-fit:contain;"
                            onerror="this.style.display='none';document.getElementById('main-img-placeholder').style.display='flex';"
                        />
                        <div id="main-img-placeholder" style="display:none;width:100%;height:100%;min-height:280px;align-items:center;justify-content:center;">
                            <i class="bi bi-image" style="font-size:80px;color:rgba(18,44,79,.18);"></i>
                        </div>
                    </div>
                    <div class="pdp-thumbs">
                        @for($t = 0; $t < 4; $t++)
                            <div class="pdp-thumb pdp-thumb-item" onclick="document.getElementById('product-image').style.display='block';document.getElementById('main-img-placeholder').style.display='none';">
                                @if(!empty($imagePath))
                                    <img src="{{ $imagePath }}" alt=""
                                        class="pdp-thumb-img"
                                        onerror="this.closest('.pdp-thumb-item').classList.add('pdp-thumb-broken');" />
                                    <div class="pdp-thumb-fallback"><i class="bi bi-image"></i></div>
                                @else
                                    <div class="pdp-thumb-fallback pdp-thumb-broken-show"><i class="bi bi-image"></i></div>
                                @endif
                            </div>
                        @endfor
                    </div>
                </div>

                {{-- Right column: product info --}}
                <div>
                    {{-- Product name --}}
                    <h1 id="product-name" class="pdp-title">{{ $product->nama }}</h1>

                    {{-- Rating line --}}
                    <div class="pdp-rating-line">
                        <span class="pdp-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</span>
                        <span id="avg-rating" class="pdp-avg">{{ number_format($avgRating, 1) }}</span>
                        <span id="rating-count" class="pdp-rcount">({{ $ratingCount }} ulasan)</span>
                    </div>

                    {{-- Price block --}}
                    <div class="pdp-price-block">
                        <span id="product-price" class="pdp-price">Rp {{ number_format($product->harga, 0, ',', '.') }}</span>
                        @if($product->diskon && $product->diskon > 0)
                            @php $hargaAsli = round($product->harga / (1 - $product->diskon / 100)); @endphp
                            <span id="product-original-price" class="pdp-original-price">Rp {{ number_format($hargaAsli, 0, ',', '.') }}</span>
                            <span id="product-discount" class="pdp-discount-badge">-{{ $product->diskon }}%</span>
                        @else
                            <span id="product-original-price" style="display:none;"></span>
                            <span id="product-discount" style="display:none;"></span>
                        @endif
                        @php $cicilan = round($product->harga / 6); @endphp
                        <p style="font-size:13px;color:#5F6368;margin:6px 0 0;">
                            atau <strong style="color:#122C4F;">Rp {{ number_format($cicilan, 0, ',', '.') }}/bulan</strong>
                            &nbsp;<span style="font-size:12px;">Cicilan 0% hingga 6&times; untuk pembayaran kartu kredit <strong>BCA &amp; Mandiri</strong></span>
                        </p>
                    </div>

                    {{-- Color swatches (visual only) --}}
                    <div class="pdp-swatches-section">
                        <p class="pdp-swatches-label">Pilih Warna</p>
                        <div class="pdp-swatches">
                            <button class="pdp-swatch pdp-swatch-active" style="background:#1a1a1a;" title="Hitam"></button>
                            <button class="pdp-swatch" style="background:#0066CC;" title="Biru"></button>
                            <button class="pdp-swatch" style="background:#EA4335;" title="Merah"></button>
                            <button class="pdp-swatch" style="background:#BDBDBD;" title="Abu"></button>
                            <button class="pdp-swatch" style="background:#FFC107;" title="Kuning"></button>
                        </div>
                    </div>

                    {{-- Qty + stock --}}
                    <div class="pdp-qty-section">
                        <div class="pdp-qty-row">
                            <button id="btn-decrease" class="pdp-qty-btn" type="button">&#8722;</button>
                            <div id="quantity-display" class="pdp-qty-display">1</div>
                            <button id="btn-increase" class="pdp-qty-btn" type="button">&#43;</button>
                        </div>
                        <div class="pdp-stock-inline">
                            <span id="product-stok" style="display:none;">{{ $product->stok }}</span>
                            @if($product->stok > 0 && $product->stok <= 20)
                                <span class="pdp-stock-warn-inline">
                                    <i class="bi bi-exclamation-circle"></i>
                                    Hanya tersisa <strong>{{ $product->stok }} item</strong><br>
                                    <span style="font-size:11px;color:#5F6368;">Jangan sampai kehabisan</span>
                                </span>
                            @else
                                <span style="font-size:13px;color:#1E8E3E;"><i class="bi bi-check-circle"></i> Stok tersedia (<span>{{ $product->stok }}</span>)</span>
                            @endif
                        </div>
                    </div>

                    {{-- Total price --}}
                    <div class="pdp-total-row">
                        <span class="pdp-total-label">Total Harga:</span>
                        <span id="total-price" class="pdp-total-value" data-price="{{ $product->harga }}">Rp {{ number_format($product->harga, 0, ',', '.') }}</span>
                    </div>

                    {{-- CTA buttons --}}
                    <div class="pdp-cta">
                        <button id="btn-Beli" class="pdp-btn pdp-btn-buy" type="button">
                            Beli Sekarang
                        </button>
                        <button id="btn-Keranjang" class="pdp-btn pdp-btn-cart" type="button">
                            + Tambah ke Keranjang
                        </button>
                    </div>

                    {{-- Info cards --}}
                    <div class="pdp-info-cards">
                        <div class="pdp-info-card">
                            <i class="bi bi-truck" style="color:#0066CC;"></i>
                            <div>
                                <strong>Gratis Ongkir</strong><br>
                                <a href="#" style="font-size:12px;color:#0066CC;text-decoration:none;">Cek tarif untuk kode pos Anda</a>
                            </div>
                        </div>
                        <div class="pdp-info-card">
                            <i class="bi bi-arrow-repeat" style="color:#E37400;"></i>
                            <div>
                                <strong>Garansi Tukar</strong><br>
                                <a href="#" style="font-size:12px;color:#0066CC;text-decoration:none;">Tukar gratis 30 hari &middot; Detail</a>
                            </div>
                        </div>
                    </div>

                    {{-- Toko info --}}
                    <div class="pdp-toko">
                        <img
                            src="{{ $tokoLogo }}"
                            alt="{{ $product->toko->nama_toko ?? 'Toko' }}"
                            class="pdp-toko-logo"
                            onerror="this.src='/img/iconPengguna.png'"
                        />
                        <div>
                            <p id="toko-nama" class="pdp-toko-name">{{ $product->toko->nama_toko ?? '-' }}</p>
                            <p id="toko-lokasi" class="pdp-toko-loc">
                                <i class="bi bi-geo-alt me-1"></i>{{ $product->toko->lokasi ?? '-' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Description section --}}
            <div class="pdp-desc-section">
                <h5><i class="bi bi-file-text me-2"></i>Deskripsi Produk</h5>
                <p id="product-description">{{ $product->deskripsi }}</p>
            </div>

            {{-- Reviews section --}}
            <div class="pdp-reviews-section">
                <h4><i class="bi bi-chat-square-text me-2"></i>Ulasan Pengguna</h4>
                <div id="reviews-list" class="pdp-reviews-grid">
                    @forelse($ratings as $rating)
                        <div class="pdp-review-card">
                            <div class="pdp-review-header">
                                <div class="pdp-review-avatar">
                                    @if(!empty($rating->user->pfpPath))
                                        <img src="{{ $rating->user->pfpPath }}" alt="{{ $rating->user->name ?? 'User' }}" onerror="this.style.display='none'" />
                                    @else
                                        {{ strtoupper(substr($rating->user->name ?? 'U', 0, 1)) }}
                                    @endif
                                </div>
                                <div class="pdp-review-meta">
                                    <p class="pdp-review-name">{{ $rating->user->name ?? 'Anonymous' }}</p>
                                    <p class="pdp-review-date">{{ $rating->created_at->format('d M Y') }}</p>
                                </div>
                            </div>
                            <div class="pdp-review-stars">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $rating->rating)&#9733;@else&#9734;@endif
                                @endfor
                            </div>
                            <p class="pdp-review-text">{{ $rating->review ?? '-' }}</p>
                        </div>
                    @empty
                        <div class="pdp-no-reviews" style="grid-column: 1 / -1;">
                            <i class="bi bi-chat-square" style="font-size:2rem; opacity:.4; display:block; margin-bottom:8px;"></i>
                            Belum ada ulasan untuk produk ini.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>{{-- /pdp-frame --}}
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Data produk untuk JavaScript
        window.PRODUCT_DATA = {
            id: {{ $product->id }},
            nama: "{{ $product->nama }}",
            harga: {{ $product->harga }},
            stok: {{ $product->stok }},
            imagePath: "{{ $imagePath }}",
            deskripsi: "{{ $product->deskripsi }}"
        };
        window.USER_ID = {{ auth()->check() ? auth()->id() : 'null' }};
    </script>
    <script src="{{ asset('js/detail-produk.js') }}"></script>
@endpush
