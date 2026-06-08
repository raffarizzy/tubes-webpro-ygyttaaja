<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'SpareHub')</title>

    <!-- Warna Utama SpareHub -->
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">

    <!-- Boostrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Favicon -->
    <link rel="icon" href="https://i.ibb.co.com/VcGWcqFG/icon-Spare-Hub.png" />

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" />
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700;800&family=Roboto:wght@400;500;600;700&display=swap" rel="stylesheet">

    @stack('styles')
    <style>
        :root {
            --sh-navy: #122C4F;
            --sh-navy-2: #1a3a66;
            --sh-blue: #0066CC;
            --sh-gold: #FFC107;
            --sh-cream: #F4E9DC;
            --sh-ink: #202124;
            --sh-muted: #5F6368;
            --sh-line: #E5DFD3;
            --sh-danger: #EA4335;
        }

        /* ── TOPBAR ── */
        .sh-topbar {
            background: var(--sh-navy);
            color: rgba(255,255,255,.82);
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 8px 28px;
            letter-spacing: .02em;
            font-family: 'Roboto', sans-serif;
        }
        .sh-topbar .tb-phone { display: inline-flex; align-items: center; gap: 5px; }
        .sh-topbar .tb-promo { color: #fff; font-weight: 500; }
        .sh-topbar .tb-promo b { color: var(--sh-gold); }
        .sh-topbar .tb-promo a { color: var(--sh-gold); text-decoration: underline; }
        .sh-topbar .tb-right { display: flex; gap: 16px; align-items: center; }

        /* ── MAIN NAV (override navbar-unified.css) ── */
        nav.sh-nav {
            background: #fff !important;
            color: var(--sh-ink) !important;
            border-bottom: 1px solid var(--sh-line);
            height: auto !important;
            padding: 14px 28px !important;
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-shadow: 0 1px 4px rgba(18,44,79,.06) !important;
        }

        /* Brand */
        .sh-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            font-family: 'Poppins', sans-serif;
            font-weight: 800;
            font-size: 20px;
            color: var(--sh-navy);
            text-decoration: none;
            cursor: pointer;
        }
        .sh-brand .sh-mark {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--sh-blue), var(--sh-navy));
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 13px;
            font-weight: 800;
            box-shadow: 0 4px 10px -3px rgba(18,44,79,.4);
        }
        .sh-brand b span { color: var(--sh-blue); }

        /* Nav links */
        .sh-navlinks {
            display: flex;
            gap: 28px;
            align-items: center;
            list-style: none;
            margin: 0;
            padding: 0;
        }
        .sh-navlinks li { position: static; }
        .sh-navlinks a {
            font-size: 14px;
            font-weight: 500;
            color: var(--sh-ink) !important;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            transition: color .15s;
        }
        .sh-navlinks a:hover { color: var(--sh-navy) !important; }

        /* Search bar */
        .sh-search {
            display: flex;
            align-items: center;
            background: #F4F6FB;
            border-radius: 999px;
            padding: 8px 16px;
            width: 260px;
            border: 1px solid var(--sh-line);
            gap: 6px;
        }
        .sh-search i { color: var(--sh-muted); font-size: 14px; }
        .sh-search input {
            border: none;
            background: transparent;
            outline: none;
            font-family: 'Roboto', sans-serif;
            font-size: 13px;
            color: var(--sh-ink);
            width: 100%;
        }
        .sh-search input::placeholder { color: var(--sh-muted); }

        /* Nav right icons */
        .sh-nav-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .sh-ico-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            font-weight: 500;
            color: var(--sh-ink) !important;
            text-decoration: none;
            cursor: pointer;
            position: relative;
        }
        .sh-ico-link i { font-size: 18px; color: var(--sh-navy); }
        .sh-cart-pip {
            position: absolute;
            top: -6px;
            right: -10px;
            background: var(--sh-danger);
            color: #fff;
            font-size: 9px;
            font-weight: 700;
            min-width: 16px;
            height: 16px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0 3px;
        }

        /* User section in nav */
        .sh-user-section {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .sh-user-section img {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--sh-line);
        }
        .sh-user-name {
            font-size: 13px;
            font-weight: 500;
            color: var(--sh-ink) !important;
            text-decoration: none;
            max-width: 130px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .sh-user-name:hover { color: var(--sh-navy) !important; }
        .sh-sep { color: var(--sh-line); margin: 0 2px; }
        .sh-logout-btn {
            background: none;
            border: 1.5px solid var(--sh-navy);
            color: var(--sh-navy);
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            font-family: 'Roboto', sans-serif;
            transition: all .15s;
        }
        .sh-logout-btn:hover { background: var(--sh-navy); color: #fff; }
        .sh-login-btn {
            background: var(--sh-navy);
            color: #fff !important;
            padding: 8px 18px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            transition: background .15s;
        }
        .sh-login-btn:hover { background: var(--sh-navy-2); color: #fff !important; }

        /* ── FOOTER ── */
        footer.sh-footer {
            background: var(--sh-navy) !important;
            color: rgba(255,255,255,.75);
            text-align: center;
            padding: 20px;
            margin-top: 0 !important;
            font-size: 13px;
        }
        footer.sh-footer p { margin: 0; color: rgba(255,255,255,.75); }

        @media (max-width: 900px) {
            .sh-search { display: none; }
            .sh-navlinks { gap: 16px; }
            .sh-topbar { display: none; }
        }
        @media (max-width: 640px) {
            .sh-navlinks { display: none; }
            nav.sh-nav { padding: 12px 16px !important; }
        }
    </style>
</head>

<body class="@yield('body-class') d-flex flex-column min-vh-100">

    <!-- TOPBAR -->
    <div class="sh-topbar">
        <span class="tb-phone"><i class="bi bi-telephone-fill"></i>+62 812 3456 7890</span>
        <span class="tb-promo">Diskon hingga <b>50%</b> untuk produk pilihan &middot; <a href="{{ url('/') }}">Belanja Sekarang</a></span>
        <span class="tb-right">
            <span>ID <i class="bi bi-chevron-down" style="font-size:10px"></i></span>
        </span>
    </div>

    <!-- NAVBAR -->
    <nav class="sh-nav">
        <!-- Brand -->
        <a class="sh-brand" href="{{ url('/') }}">
            <span class="sh-mark">SH</span>
            <b>Spare<span>Hub</span></b>
        </a>

        <!-- Nav links -->
        <ul class="sh-navlinks">
            <li><a href="{{ url('/') }}">Beranda</a></li>
            <li><a href="{{ route('keranjang') }}">Keranjang</a></li>
            <li><a href="{{ route('profil_toko') }}">Toko Saya</a></li>
        </ul>

        <!-- Search bar -->
        <div class="sh-search">
            <i class="bi bi-search"></i>
            <input type="text" placeholder="Cari produk..." />
        </div>

        <!-- Right: account + cart -->
        <div class="sh-nav-right">
            <div class="sh-user-section">
                @auth
                    <img src="{{ auth()->user()->pfpPath }}" id="iconPengguna" alt="User Icon" />
                    <a href="{{ route('profile.edit') }}" class="sh-user-name">{{ auth()->user()->name }}</a>
                    <span class="sh-sep">|</span>
                    <form method="POST" action="{{ route('logout') }}" style="display:inline"
                          onsubmit="return confirm('Apakah Anda yakin ingin logout?')">
                        @csrf
                        <button type="submit" class="sh-logout-btn">Logout</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="sh-login-btn">Login</a>
                @endauth
            </div>

            <a class="sh-ico-link" href="{{ route('keranjang') }}">
                <i class="bi bi-cart3"></i>
                <span class="sh-cart-pip cart-count" style="display:none">0</span>
            </a>
        </div>
    </nav>

    <!-- MAIN CONTENT -->
    <main class="flex-fill">
        @yield('content')
    </main>

    <!-- FOOTER -->
    <footer class="sh-footer">
        <p>&copy; 2025 SpareHub. Semua hak dilindungi.</p>
    </footer>

    @stack('scripts')
</body>

</html>