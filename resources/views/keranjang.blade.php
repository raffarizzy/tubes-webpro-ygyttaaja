@extends('layouts.main')

@section('title', 'Keranjang - SpareHub')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/keranjang.css') }}" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet" />
    <style>
        :root {
            --navy: #122C4F;
            --navy-2: #1a3a66;
            --blue: #0066CC;
            --gold: #FFC107;
            --cream: #F4E9DC;
            --cream-2: #EFE2D1;
            --ink: #202124;
            --body: #3C4043;
            --muted: #5F6368;
            --line: #E5DFD3;
            --line-2: #DADCE0;
            --ok: #1E8E3E;
        }

        body {
            font-family: 'Roboto', sans-serif;
            color: var(--body);
        }

        .kj-page {
            background: var(--cream);
            min-height: 100vh;
            padding: 2.5rem 1rem 4rem;
        }

        .kj-frame {
            max-width: 1180px;
            margin: 0 auto;
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 4px 32px rgba(18, 44, 79, 0.10);
            overflow: hidden;
        }

        /* ── GRID ── */
        .kj-grid {
            display: grid;
            grid-template-columns: 1.4fr 1fr;
            align-items: start;
            gap: 0;
        }

        /* ── LEFT ── */
        .kj-left {
            padding: 2.5rem 2rem 2.5rem 2.5rem;
            border-right: 1px solid var(--line-2);
        }

        .kj-header-card {
            background: var(--navy);
            border-radius: 10px;
            padding: 1.5rem 1.75rem;
            margin-bottom: 1.75rem;
            color: #fff;
        }

        .kj-header-card h1 {
            font-family: 'Poppins', sans-serif;
            font-size: 1.45rem;
            font-weight: 700;
            margin: 0 0 .35rem;
            color: #fff;
        }

        .kj-header-card .kj-lead {
            font-size: .93rem;
            color: rgba(255, 255, 255, .72);
            margin: 0;
        }

        /* keranjang-container: JS renders items here */
        #keranjang-container {
            min-height: 80px;
        }

        /* ── CART ACTIONS ── */
        .kj-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-top: 1.5rem;
            padding-top: 1.25rem;
            border-top: 1px solid var(--line-2);
            flex-wrap: wrap;
        }

        .kj-actions a {
            font-size: .875rem;
            font-weight: 500;
            color: var(--blue);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            transition: color .2s;
        }

        .kj-actions a:hover {
            color: var(--navy);
        }

        .kj-actions a.kj-update {
            margin-left: auto;
            color: var(--muted);
        }

        .kj-actions a.kj-update:hover {
            color: var(--ink);
        }

        /* ── COUPON CARD ── */
        .kj-coupon {
            margin-top: 1.5rem;
            background: var(--cream);
            border: 2px dashed var(--cream-2);
            border-radius: 10px;
            padding: 1.25rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .kj-coupon-icon {
            font-size: 1.6rem;
            color: var(--gold);
            flex-shrink: 0;
        }

        .kj-coupon-text {
            flex: 1;
        }

        .kj-coupon-text strong {
            display: block;
            font-family: 'Poppins', sans-serif;
            font-size: .95rem;
            font-weight: 600;
            color: var(--ink);
        }

        .kj-coupon-text span {
            font-size: .82rem;
            color: var(--muted);
        }

        .kj-coupon-btn {
            background: var(--navy);
            color: #fff;
            border: none;
            border-radius: 7px;
            padding: .5rem 1.1rem;
            font-size: .85rem;
            font-weight: 500;
            cursor: pointer;
            transition: background .2s;
            white-space: nowrap;
        }

        .kj-coupon-btn:hover {
            background: var(--navy-2);
        }

        /* ── RIGHT SIDEBAR ── */
        .kj-right {
            padding: 2.5rem 2.5rem 2.5rem 2rem;
            position: sticky;
            top: 1.5rem;
        }

        .kj-sidebar-title {
            font-family: 'Poppins', sans-serif;
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--navy);
            margin: 0 0 1.5rem;
            padding-bottom: .75rem;
            border-bottom: 2px solid var(--navy);
        }

        .kj-summary-rows {
            display: flex;
            flex-direction: column;
            gap: .7rem;
            margin-bottom: 1.25rem;
        }

        .kj-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: .9rem;
        }

        .kj-row .kj-label {
            color: var(--muted);
        }

        .kj-row .kj-value {
            font-weight: 500;
            color: var(--ink);
        }

        .kj-row .kj-free {
            color: var(--ok);
            font-weight: 600;
        }

        .kj-divider {
            border: none;
            border-top: 1px solid var(--line-2);
            margin: 1rem 0;
        }

        .kj-total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .kj-total-row .kj-total-label {
            font-family: 'Poppins', sans-serif;
            font-size: 1rem;
            font-weight: 700;
            color: var(--navy);
        }

        .kj-total-row .kj-total-value {
            font-family: 'Poppins', sans-serif;
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--navy);
        }

        /* btn-checkout: class kept as-is so JS works */
        .btn-checkout {
            display: block;
            width: 100%;
            background: var(--navy);
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: .9rem 1rem;
            font-family: 'Poppins', sans-serif;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background .2s, transform .1s;
            text-align: center;
        }

        .btn-checkout:hover {
            background: var(--navy-2);
            transform: translateY(-1px);
        }

        .btn-checkout:active {
            transform: translateY(0);
        }

        /* ── SECURITY NOTE ── */
        .kj-security {
            display: flex;
            align-items: center;
            gap: .45rem;
            justify-content: center;
            margin-top: .9rem;
            font-size: .78rem;
            color: var(--muted);
        }

        .kj-security i {
            color: var(--ok);
            font-size: 1rem;
        }

        /* ── PAYMENT LOGOS ── */
        .kj-payment-strip {
            margin-top: 1.25rem;
            padding-top: 1rem;
            border-top: 1px solid var(--line-2);
        }

        .kj-payment-strip p {
            font-size: .75rem;
            color: var(--muted);
            margin: 0 0 .55rem;
            text-align: center;
        }

        .kj-logos {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .5rem;
            flex-wrap: wrap;
        }

        .kj-logo-badge {
            background: var(--cream);
            border: 1px solid var(--line);
            border-radius: 5px;
            padding: .22rem .55rem;
            font-size: .72rem;
            font-weight: 700;
            color: var(--navy);
            letter-spacing: .03em;
        }

        .kj-logo-badge.visa { color: #1A1F71; }
        .kj-logo-badge.mc { color: #EB001B; }
        .kj-logo-badge.bca { color: #003f88; }
        .kj-logo-badge.mandiri { color: #003D79; }
        .kj-logo-badge.ovo { color: #4C2A86; }

        /* ── RESPONSIVE ── */
        @media (max-width: 820px) {
            .kj-grid {
                grid-template-columns: 1fr;
            }

            .kj-left {
                padding: 1.75rem 1.25rem;
                border-right: none;
                border-bottom: 1px solid var(--line-2);
            }

            .kj-right {
                padding: 1.75rem 1.25rem;
                position: static;
            }
        }
    </style>
@endpush

@section('content')
<div class="kj-page">
    <div class="kj-frame">
        <div class="kj-grid">

            {{-- ══ LEFT COLUMN ══ --}}
            <div class="kj-left">

                {{-- Header card --}}
                <div class="kj-header-card">
                    <h1><i class="bi bi-cart3 me-2"></i>Keranjang Belanja Anda</h1>
                    <p class="kj-lead">Periksa kembali pesanan Anda sebelum melanjutkan ke pembayaran.</p>
                </div>

                {{-- JS renders cart items here — keep id exactly --}}
                <div id="keranjang-container"></div>

                {{-- Cart actions footer --}}
                <div class="kj-actions">
                    <a href="{{ url('/') }}"><i class="bi bi-arrow-left"></i> Lanjut Belanja</a>
                    <a href="{{ url('/keranjang') }}" class="kj-update"><i class="bi bi-arrow-clockwise"></i> Perbarui Keranjang</a>
                </div>

                {{-- Coupon card --}}
                <div class="kj-coupon">
                    <span class="kj-coupon-icon"><i class="bi bi-ticket-perforated-fill"></i></span>
                    <div class="kj-coupon-text">
                        <strong>Punya kode kupon?</strong>
                        <span>Masukkan kode dan hemat lebih banyak</span>
                    </div>
                    <button class="kj-coupon-btn">Pakai Sekarang</button>
                </div>

            </div>{{-- /kj-left --}}

            {{-- ══ RIGHT COLUMN (sticky sidebar) ══ --}}
            <div class="kj-right">
                <h2 class="kj-sidebar-title">Ringkasan Belanja</h2>

                <div class="kj-summary-rows">
                    <div class="kj-row">
                        <span class="kj-label">Total Item</span>
                        <span class="kj-value" id="total-item">0</span>
                    </div>
                    <div class="kj-row">
                        <span class="kj-label">Subtotal</span>
                        <span class="kj-value" id="total-harga">Rp 0</span>
                    </div>
                    <div class="kj-row">
                        <span class="kj-label">Ongkos kirim</span>
                        <span class="kj-value kj-free">Gratis</span>
                    </div>
                    <div class="kj-row">
                        <span class="kj-label">PPN (11%)</span>
                        <span class="kj-value" id="ppn-value">Rp 0</span>
                    </div>
                </div>

                <hr class="kj-divider" />

                <div class="kj-total-row">
                    <span class="kj-total-label">Total Pembayaran</span>
                    <span class="kj-total-value" id="grand-total">Rp 0</span>
                </div>

                <button class="btn-checkout">
                    <i class="bi bi-lock-fill me-1"></i> Lanjut ke Pembayaran
                </button>

                <div class="kj-security">
                    <i class="bi bi-shield-check-fill"></i>
                    <span>Transaksi dijamin aman &amp; terenkripsi</span>
                </div>

                <div class="kj-payment-strip">
                    <p>Metode pembayaran yang diterima</p>
                    <div class="kj-logos">
                        <span class="kj-logo-badge visa">VISA</span>
                        <span class="kj-logo-badge mc">MC</span>
                        <span class="kj-logo-badge bca">BCA</span>
                        <span class="kj-logo-badge mandiri">Mandiri</span>
                        <span class="kj-logo-badge ovo">OVO</span>
                    </div>
                </div>
            </div>{{-- /kj-right --}}

        </div>{{-- /kj-grid --}}
    </div>{{-- /kj-frame --}}
</div>{{-- /kj-page --}}
@endsection

@push('scripts')
    <script>
        // Pass cart data from Laravel to JavaScript
        window.keranjangItems = @json($keranjangItems ?? []);
    </script>
    <script src="{{ asset('js/keranjang.js') }}"></script>
    <script>
        // Compute PPN + grand total whenever total-harga updates
        (function () {
            function parseRp(str) {
                if (!str) return 0;
                return parseInt(str.replace(/[^0-9]/g, ''), 10) || 0;
            }
            function formatRp(n) {
                return 'Rp ' + n.toLocaleString('id-ID');
            }
            function updateDerived() {
                var subtotal = parseRp(document.getElementById('total-harga').textContent);
                var ppn = Math.round(subtotal * 0.11);
                var grand = subtotal + ppn;
                document.getElementById('ppn-value').textContent = formatRp(ppn);
                document.getElementById('grand-total').textContent = formatRp(grand);
            }
            // Observe changes to total-harga (keranjang.js updates it)
            var el = document.getElementById('total-harga');
            if (el) {
                new MutationObserver(updateDerived).observe(el, { childList: true, subtree: true, characterData: true });
            }
            // Also run once on load
            document.addEventListener('DOMContentLoaded', updateDerived);
        })();
    </script>
@endpush