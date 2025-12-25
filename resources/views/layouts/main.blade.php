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

    <!-- Navbar CSS -->
    <link rel="stylesheet" href="{{ asset('css/navbar-unified.css') }}" />

    @stack('styles')
</head>

<body>
    <!-- NAVBAR -->
    <nav>
        <img src="https://i.ibb.co.com/VcGWcqFG/icon-Spare-Hub.png"
             id="logo"
             alt="Logo SpareHub"
             style="cursor:pointer"
             onclick="window.location.href='/'" />

        <ul>
            <li><a href="/">Beranda</a></li>

            <li class="nav-keranjang-wrapper">
                <a href="{{ route('keranjang') }}">
                    Keranjang
                </a>
            </li>

            <li><a href="/toko">Toko Saya</a></li>

            <!-- PROFIL -->
            <li>
                <div id="profil">
                    @auth
                        <img src="{{ auth()->user()->pfpPath }}"
                             id="iconPengguna"
                             alt="User Icon" />

                        <a href="{{ route('profile.edit') }}" class="user-name-link">
                            <span class="user-name">
                                {{ auth()->user()->name }}
                            </span>
                        </a>

                        <span class="nav-separator">|</span>

                        <form method="POST"
                            action="{{ route('logout') }}"
                            style="display:inline"
                            onsubmit="return confirm('Apakah Anda yakin ingin logout?')">
                            @csrf
                            <button type="submit" class="btn-logout">
                                Logout
                            </button>
                        </form>
                    @else
                        <img src="https://i.ibb.co.com/RkZ105G9/default-avatar.png"
                             id="iconPengguna"
                             alt="User Icon" />

                        <a href="{{ route('login') }}" class="login-link">
                            Login
                        </a>
                    @endauth
                </div>
            </li>
        </ul>
    </nav>

    <!-- MAIN CONTENT -->
    <main>
        @yield('content')
    </main>

    <!-- FOOTER -->
    <footer>
        <p>&copy; 2025 SpareHub. Semua hak dilindungi.</p>
    </footer>

    @stack('scripts')
</body>
</html>