<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'SpareHub')</title>

    <!-- Favicon -->
    <link rel="icon" href="{{ asset('img/iconSpareHub.png') }}" />

    <!-- Navbar CSS -->
    <link rel="stylesheet" href="{{ asset('css/navbar-unified.css') }}" />

    <!-- Bootstrap CSS (if needed on page) -->
    @stack('bootstrap')

    <!-- Page-specific CSS -->
    @stack('styles')
</head>

<body class="@yield('body-class')">
    <!-- NAVBAR -->
    <nav>
        <img src="{{ asset('img/iconSpareHub.png') }}" id="logo" alt="Logo SpareHub" />
        <ul>
            <li><a href="/">Beranda</a></li>
            <li class="nav-keranjang-wrapper">
                <a href="{{ route('keranjang') }}">
                    Keranjang
                    <span id="cart-count" class="cart-count">0</span>
                </a>
            </li>
            <li><a href="{{ route('profil_toko') }}">Toko Saya</a></li>
            <li>
                <div id="profil">
                    <!-- User info will be loaded by navbar-manager.js -->
                </div>
            </li>
        </ul>
    </nav>

    <!-- MAIN CONTENT -->
    @yield('content')

    <!-- FOOTER -->
    <footer @yield('footer-class')>
        <p @yield('footer-text-class')>&copy; 2025 SpareHub. Semua hak dilindungi.</p>
    </footer>

    <!-- Laravel Auth Injection -->
    <script>
        // Inject Laravel auth user to JavaScript
        @auth
            window.laravelAuthUser = {
                id: {{ auth()->user()->id }},
                nama: "{{ auth()->user()->name }}",
                email: "{{ auth()->user()->email }}"
            };
            // Sync with localStorage for navbar-manager
            localStorage.setItem('loggedInUser', JSON.stringify(window.laravelAuthUser));
        @else
            window.laravelAuthUser = null;
            localStorage.removeItem('loggedInUser');
        @endauth
    </script>

    <!-- Common Scripts -->
    <script src="{{ asset('js/navbar-manager.js') }}"></script>

    <!-- Page-specific Scripts -->
    @stack('scripts')
</body>
</html>