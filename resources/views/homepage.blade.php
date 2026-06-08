@extends('layouts.main')

@section('title', 'Home Page - SpareHub')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/homepage.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700;800&family=Roboto:wght@400;500;600;700&display=swap">
    <style>
        :root {
            --navy: #122C4F;
            --navy-2: #1a3a66;
            --navy-3: #0D1F38;
            --blue: #0066CC;
            --gold: #FFC107;
            --star: #FFA500;
            --cream: #F4E9DC;
            --cream-2: #EFE2D1;
            --cream-3: #E9D9C2;
            --ink: #202124;
            --body: #3C4043;
            --muted: #5F6368;
            --hint: #999;
            --line: #E5DFD3;
            --line-2: #DADCE0;
            --ok: #1E8E3E;
        }

        body {
            background: var(--cream);
            font-family: 'Roboto', sans-serif;
        }

        .sh-frame {
            max-width: 1180px;
            margin: 0 auto 48px;
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 4px 32px rgba(18,44,79,.10), 0 1px 6px rgba(18,44,79,.06);
            overflow: hidden;
        }

        /* ── HERO ── */
        .sh-hero {
            background: linear-gradient(135deg, var(--navy) 0%, var(--navy-2) 60%, #1e4d8a 100%);
            padding: 56px 56px 48px;
            display: grid;
            grid-template-columns: 1fr 340px;
            gap: 40px;
            align-items: center;
            position: relative;
            overflow: hidden;
        }

        .sh-hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            pointer-events: none;
        }

        .sh-hero-left {
            position: relative;
            z-index: 1;
        }

        .sh-eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(255,193,7,.15);
            border: 1px solid rgba(255,193,7,.35);
            color: var(--gold);
            font-family: 'Poppins', sans-serif;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .1em;
            text-transform: uppercase;
            padding: 5px 12px;
            border-radius: 100px;
            margin-bottom: 20px;
        }

        .sh-eyebrow i { font-size: 13px; }

        .sh-hero h1 {
            font-family: 'Poppins', sans-serif;
            font-size: clamp(28px, 3.2vw, 46px);
            font-weight: 800;
            color: #fff;
            line-height: 1.18;
            margin: 0 0 18px;
        }

        .sh-hero h1 em {
            font-style: normal;
            color: var(--gold);
        }

        .sh-hero-desc {
            color: rgba(255,255,255,.72);
            font-size: 15px;
            line-height: 1.65;
            max-width: 460px;
            margin: 0 0 28px;
        }

        .sh-cta-row {
            display: flex;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
        }

        #scroll-produk {
            background: var(--gold);
            color: var(--navy-3);
            border: none;
            padding: 13px 28px;
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: background .18s, transform .15s, box-shadow .18s;
            box-shadow: 0 4px 16px rgba(255,193,7,.35);
        }

        #scroll-produk:hover {
            background: #e6ac00;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255,193,7,.45);
        }

        .sh-hero-stats {
            display: flex;
            gap: 28px;
            margin-top: 32px;
            padding-top: 28px;
            border-top: 1px solid rgba(255,255,255,.12);
            flex-wrap: wrap;
        }

        .sh-stat {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .sh-stat-val {
            font-family: 'Poppins', sans-serif;
            font-size: 20px;
            font-weight: 800;
            color: var(--gold);
            line-height: 1;
        }

        .sh-stat-lbl {
            font-size: 11px;
            color: rgba(255,255,255,.55);
            font-weight: 500;
            letter-spacing: .03em;
        }

        /* Hero right — decorative circle */
        .sh-hero-right {
            position: relative;
            z-index: 1;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .sh-circle-art {
            width: 240px;
            height: 240px;
            border-radius: 50%;
            background: radial-gradient(circle at 35% 35%, #1e5aaa, var(--navy-3));
            border: 3px solid rgba(255,255,255,.12);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            box-shadow: 0 12px 48px rgba(0,0,0,.35), inset 0 2px 8px rgba(255,255,255,.08);
        }

        .sh-circle-art i {
            font-size: 88px;
            color: rgba(255,255,255,.18);
        }

        .sh-badge-float {
            position: absolute;
            bottom: 12px;
            right: -10px;
            background: var(--ok);
            color: #fff;
            font-family: 'Poppins', sans-serif;
            font-size: 11px;
            font-weight: 700;
            padding: 7px 14px;
            border-radius: 100px;
            display: flex;
            align-items: center;
            gap: 5px;
            box-shadow: 0 4px 16px rgba(30,142,62,.45);
            white-space: nowrap;
        }

        /* ── CATEGORY STRIP ── */
        .sh-cat-strip {
            padding: 32px 40px 28px;
            border-bottom: 1px solid var(--line);
        }

        .sh-cat-strip-title {
            font-family: 'Poppins', sans-serif;
            font-size: 13px;
            font-weight: 700;
            color: var(--muted);
            letter-spacing: .08em;
            text-transform: uppercase;
            margin-bottom: 18px;
        }

        .sh-cat-grid {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            gap: 12px;
        }

        .sh-cat-card {
            background: var(--cream);
            border: 1px solid var(--line);
            border-radius: 12px;
            padding: 18px 10px 14px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            transition: background .16s, border-color .16s, transform .15s, box-shadow .15s;
            text-decoration: none;
        }

        .sh-cat-card:hover {
            background: var(--cream-2);
            border-color: var(--blue);
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0,102,204,.12);
        }

        .sh-cat-icon {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .sh-cat-card span.sh-cat-name {
            font-family: 'Poppins', sans-serif;
            font-size: 11px;
            font-weight: 600;
            color: var(--ink);
            text-align: center;
            line-height: 1.3;
        }

        /* ── FILTER BAR ── */
        .sh-filter-bar {
            padding: 20px 40px 16px;
            border-bottom: 1px solid var(--line);
            background: #fafafa;
        }

        .sh-filter-row-1 {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 14px;
        }

        .sh-search-wrap {
            flex: 1;
            min-width: 220px;
            position: relative;
        }

        .sh-search-wrap i {
            position: absolute;
            left: 13px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--muted);
            font-size: 15px;
            pointer-events: none;
        }

        #search-input {
            width: 100%;
            padding: 10px 14px 10px 38px;
            border: 1.5px solid var(--line-2);
            border-radius: 100px;
            font-size: 14px;
            font-family: 'Roboto', sans-serif;
            color: var(--ink);
            background: #fff;
            transition: border-color .18s, box-shadow .18s;
            box-sizing: border-box;
        }

        #search-input:focus {
            outline: none;
            border-color: var(--blue);
            box-shadow: 0 0 0 3px rgba(0,102,204,.1);
        }

        .sh-filter-pills {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            align-items: center;
        }

        .sh-pill {
            padding: 8px 16px;
            border: 1.5px solid var(--line-2);
            border-radius: 100px;
            background: #fff;
            font-size: 13px;
            font-family: 'Roboto', sans-serif;
            font-weight: 500;
            color: var(--body);
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: background .16s, border-color .16s, color .16s;
            white-space: nowrap;
        }

        .sh-pill:hover {
            background: var(--cream);
            border-color: var(--blue);
            color: var(--blue);
        }

        .sh-pill-active {
            background: var(--navy);
            border-color: var(--navy);
            color: #fff;
        }

        .sh-pill-active:hover {
            background: var(--navy-2);
            border-color: var(--navy-2);
            color: #fff;
        }

        .sh-sort-select {
            margin-left: auto;
            padding: 8px 14px;
            border: 1.5px solid var(--line-2);
            border-radius: 8px;
            font-size: 13px;
            font-family: 'Roboto', sans-serif;
            color: var(--body);
            background: #fff;
            cursor: pointer;
            outline: none;
        }

        .sh-filter-row-2 {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .sh-price-group {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .sh-price-label {
            font-size: 12px;
            color: var(--muted);
            font-weight: 500;
            white-space: nowrap;
        }

        #price-min,
        #price-max {
            width: 130px;
            padding: 7px 12px;
            border: 1.5px solid var(--line-2);
            border-radius: 8px;
            font-size: 13px;
            font-family: 'Roboto', sans-serif;
            color: var(--ink);
            background: #fff;
            transition: border-color .18s;
        }

        #price-min:focus,
        #price-max:focus {
            outline: none;
            border-color: var(--blue);
        }

        .sh-price-sep {
            color: var(--hint);
            font-size: 13px;
        }

        #reset-filter {
            padding: 7px 18px;
            background: transparent;
            border: 1.5px solid var(--line-2);
            border-radius: 8px;
            font-size: 13px;
            font-family: 'Roboto', sans-serif;
            font-weight: 500;
            color: var(--muted);
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: background .16s, border-color .16s, color .16s;
        }

        #reset-filter:hover {
            background: #fce4e4;
            border-color: #e57373;
            color: #c0392b;
        }

        #results-info {
            margin-left: auto;
            font-size: 12px;
            color: var(--hint);
            font-style: italic;
        }

        /* ── PRODUCTS SECTION ── */
        .sh-products {
            padding: 32px 40px 40px;
        }

        .sh-section-header {
            display: flex;
            align-items: baseline;
            justify-content: space-between;
            margin-bottom: 24px;
        }

        .sh-section-title {
            font-family: 'Poppins', sans-serif;
            font-size: 20px;
            font-weight: 700;
            color: var(--ink);
        }

        .sh-section-title span {
            color: var(--blue);
        }

        .sh-see-all {
            font-size: 13px;
            font-weight: 600;
            color: var(--blue);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            transition: gap .15s;
        }

        .sh-see-all:hover { gap: 8px; }

        #produk-container {
            /* populated by homepage.js */
        }

        #pagination {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 30px;
            flex-wrap: wrap;
        }

        /* ── RESPONSIVE ── */
        @media (max-width: 900px) {
            .sh-hero { grid-template-columns: 1fr; padding: 40px 28px 36px; }
            .sh-hero-right { display: none; }
            .sh-cat-grid { grid-template-columns: repeat(3, 1fr); }
            .sh-filter-bar, .sh-cat-strip, .sh-products { padding-left: 20px; padding-right: 20px; }
        }

        @media (max-width: 560px) {
            .sh-cat-grid { grid-template-columns: repeat(2, 1fr); }
            .sh-hero { padding: 32px 20px 28px; }
            #price-min, #price-max { width: 100px; }
        }
    </style>
@endpush

@section('content')

<div class="sh-frame">

    {{-- ══════════════════════════════ HERO ══════════════════════════════ --}}
    <section class="sh-hero">
        <div class="sh-hero-left">
            <div class="sh-eyebrow">
                <i class="bi bi-cloud-rain-fill"></i>
                PROMO MUSIM HUJAN
            </div>

            <h1>Diskon hingga <em>50%</em><br>Sparepart Original</h1>

            <p class="sh-hero-desc">
                Ribuan suku cadang berkualitas, harga transparan, pengiriman cepat ke seluruh Indonesia.
                Temukan produk yang tepat untuk kendaraan Anda hari ini.
            </p>

            <div class="sh-cta-row">
                <button id="scroll-produk">
                    <i class="bi bi-search"></i>
                    Jelajahi Produk
                </button>
            </div>

            <div class="sh-hero-stats">
                <div class="sh-stat">
                    <span class="sh-stat-val">10K+</span>
                    <span class="sh-stat-lbl">Produk tersedia</span>
                </div>
                <div class="sh-stat">
                    <span class="sh-stat-val">500+</span>
                    <span class="sh-stat-lbl">Bengkel mitra</span>
                </div>
                <div class="sh-stat">
                    <span class="sh-stat-val">4.9 ★</span>
                    <span class="sh-stat-lbl">Rating pelanggan</span>
                </div>
            </div>
        </div>

        <div class="sh-hero-right">
            <div class="sh-circle-art">
                <i class="bi bi-shield-fill-check"></i>
            </div>
        </div>
    </section>

    {{-- ══════════════════════════════ CATEGORY STRIP ══════════════════════════════ --}}
    <div class="sh-cat-strip">
        <div class="sh-cat-strip-title">Kategori Populer</div>
        <div class="sh-cat-grid">
            <a class="sh-cat-card" href="#">
                <div class="sh-cat-icon" style="background:rgba(0,102,204,.1);color:var(--blue);">
                    <i class="bi bi-shield-fill"></i>
                </div>
                <span class="sh-cat-name">Helm</span>
            </a>
            <a class="sh-cat-card" href="#">
                <div class="sh-cat-icon" style="background:rgba(255,193,7,.15);color:#c68a00;">
                    <i class="bi bi-droplet-fill"></i>
                </div>
                <span class="sh-cat-name">Oli &amp; Cairan</span>
            </a>
            <a class="sh-cat-card" href="#">
                <div class="sh-cat-icon" style="background:rgba(220,53,69,.12);color:#c0392b;">
                    <i class="bi bi-circle-fill"></i>
                </div>
                <span class="sh-cat-name">Rem</span>
            </a>
            <a class="sh-cat-card" href="#">
                <div class="sh-cat-icon" style="background:rgba(30,142,62,.12);color:var(--ok);">
                    <i class="bi bi-disc-fill"></i>
                </div>
                <span class="sh-cat-name">Ban</span>
            </a>
            <a class="sh-cat-card" href="#">
                <div class="sh-cat-icon" style="background:rgba(100,100,200,.12);color:#4a4aaa;">
                    <i class="bi bi-battery-charging"></i>
                </div>
                <span class="sh-cat-name">Aki</span>
            </a>
            <a class="sh-cat-card" href="#">
                <div class="sh-cat-icon" style="background:rgba(18,44,79,.10);color:var(--navy);">
                    <i class="bi bi-gear-fill"></i>
                </div>
                <span class="sh-cat-name">Mesin</span>
            </a>
        </div>
    </div>

    {{-- ══════════════════════════════ FILTER BAR ══════════════════════════════ --}}
    <div class="sh-filter-bar" id="produk">
        {{-- Row 1: search + pill filters + sort --}}
        <div class="sh-filter-row-1">
            <div class="sh-search-wrap">
                <i class="bi bi-search"></i>
                <input
                    type="text"
                    id="search-input"
                    placeholder="Cari nama produk..."
                />
            </div>

            <div class="sh-filter-pills">
                <button class="sh-pill sh-pill-active" type="button">
                    <i class="bi bi-bicycle"></i> Tipe Motor
                </button>
                <button class="sh-pill" type="button">
                    <i class="bi bi-tag"></i> Harga
                </button>
                <button class="sh-pill" type="button">
                    <i class="bi bi-star"></i> Rating
                </button>
                <button class="sh-pill" type="button">
                    <i class="bi bi-shop"></i> Brand
                </button>
                <button class="sh-pill" type="button">
                    <i class="bi bi-layers"></i> Bahan
                </button>
                <button class="sh-pill" type="button" style="color:var(--ok);border-color:rgba(30,142,62,.35);">
                    <i class="bi bi-percent"></i> Promo
                </button>
                <button class="sh-pill" type="button">
                    <i class="bi bi-sliders"></i> Semua Filter
                </button>
            </div>

            <select class="sh-sort-select" aria-label="Urutkan">
                <option>Relevansi</option>
                <option>Harga: Rendah ke Tinggi</option>
                <option>Harga: Tinggi ke Rendah</option>
                <option>Terbaru</option>
                <option>Terpopuler</option>
            </select>
        </div>

        {{-- Row 2: price range + reset + results info --}}
        <div class="sh-filter-row-2">
            <span class="sh-price-label">Rentang Harga:</span>
            <div class="sh-price-group">
                <input
                    type="number"
                    id="price-min"
                    placeholder="Rp min"
                />
                <span class="sh-price-sep">—</span>
                <input
                    type="number"
                    id="price-max"
                    placeholder="Rp max"
                />
            </div>

            <button id="reset-filter" type="button">
                <i class="bi bi-x-circle"></i> Reset
            </button>

            <div id="results-info">Menampilkan semua produk</div>
        </div>
    </div>

    {{-- ══════════════════════════════ PRODUCT GRID ══════════════════════════════ --}}
    <div class="sh-products">
        <div class="sh-section-header">
            <div class="sh-section-title">
                Pilihan <span>Untuk Anda</span>
            </div>
            <a href="#" class="sh-see-all">Lihat semua <i class="bi bi-arrow-right"></i></a>
        </div>

        {{-- Populated by homepage.js --}}
        <div id="produk-container"></div>

        {{-- Pagination populated by homepage.js --}}
        <div id="pagination"></div>
    </div>

</div>

@endsection

@push('scripts')
    <script src="{{ asset('js/homepage.js') }}"></script>
@endpush