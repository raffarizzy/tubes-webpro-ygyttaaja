<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Medcom')</title>

    <!-- Warna Utama Medcom -->
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">

    <!-- Boostrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Favicon -->
    <link rel="icon" href="https://i.ibb.co/qMV58T6P/medcom-Navbar.png" />

    <!-- Navbar CSS -->
    <link rel="stylesheet" href="{{ asset('css/navbar-unified.css') }}" />

    <style>
        @media (max-width: 768px) {
            html, body {
                overflow-x: hidden;
                width: 100%;
                position: relative;
            }
        }
    </style>

    @stack('styles')
</head>

<body class="@yield('body-class') d-flex flex-column min-vh-100">
    <!-- NAVBAR -->
    <nav>
        <a href="{{ url('/') }}" class="navbar-brand">
            <img src="https://i.ibb.co/qMV58T6P/medcom-Navbar.png" id="logo" alt="Logo Medcom" />
        </a>

        <ul class="nav-links">
            <li><a href="{{ url('/') }}">Beranda</a></li>
            <li class="nav-keranjang-wrapper">
                <a href="{{ route('keranjang') }}">
                    Keranjang
                </a>
            </li>
            <li><a href="{{ route('profil_toko') }}">Toko Saya</a></li>
        </ul>

        <!-- PROFIL -->
        <div id="profil" class="d-flex align-items-center">
            @auth
                <a href="{{ route('profile.edit') }}" class="user-name-link d-flex align-items-center gap-2 text-decoration-none">
                    <img src="{{ auth()->user()->pfpPath }}" id="iconPengguna" alt="User Icon" class="rounded-circle border border-white" style="width: 32px; height: 32px; object-fit: cover;"/>
                    <span class="user-name">
                        {{ auth()->user()->name }}
                    </span>
                </a>

                <span class="nav-separator">|</span>

                <form method="POST" action="{{ route('logout') }}" style="display:inline"
                    onsubmit="return confirm('Apakah Anda yakin ingin logout?')">
                    @csrf
                    <button type="submit" class="btn-logout">
                        Logout
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="login-link">
                    Login
                </a>
            @endauth
        </div>
    </nav>

    <!-- MAIN CONTENT -->
    <main class="flex-fill">
        @yield('content')
    </main>

    <!-- FOOTER -->
    <footer>
        <div class="container-fluid px-4 px-md-5">
            <div class="row align-items-center">
                <!-- Spacer Kolom Kiri -->
                <div class="col-md-4 d-none d-md-block"></div>

                <!-- Teks Hak Cipta di Tengah -->
                <div class="col-12 col-md-4 text-center">
                    <p class="mb-0">&copy; 2025 Medcom. Semua hak dilindungi.</p>
                </div>

                <!-- Socials di Kanan Banget -->
                <div class="col-12 col-md-4 mt-3 mt-md-0 d-flex justify-content-center justify-content-md-end">
                    <div name="medcomLogos" class="medcomLogos d-flex flex-column gap-2 align-items-center align-items-md-end">
                        <p class="mb-0 text-md-end w-100" style="font-size: 1rem; font-weight: 600;">Socials</p>
                        <ul class="d-flex gap-3 mb-0" style="list-style: none; padding: 0;">
                            <li class="bg-dark p-2 rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                                <a href="https://www.tokopedia.com/medcomindonesia" target="_blank" title="Tokopedia">
                                    <img src="{{ asset('storage/icons/tokopedia.image') }}" style="width: 20px;" />
                                </a>
                            </li>

                            <li class="bg-dark p-2 rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                                <a href="https://www.facebook.com/people/Medcom-Indonesia/100084631722551/" target="_blank" title="Facebook">
                                    <img src="{{ asset('storage/icons/facebook.svg') }}" style="width: 18px;" />
                                </a>
                            </li>

                            <li class="bg-dark p-2 rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                                <a href="https://www.tiktok.com/@medcomindonesia1" target="_blank" title="TikTok">
                                    <img src="{{ asset('storage/icons/tiktok.svg') }}" style="width: 18px;" />
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>

</html>
