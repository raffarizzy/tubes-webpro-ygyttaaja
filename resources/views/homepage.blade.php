<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Home Page - SpareHub</title>
    <link rel="icon" href="img/iconSpareHub.png" />
    <link rel="stylesheet" href="{{ asset('css/navbar-unified.css') }}">
    <link rel="stylesheet" href="{{ asset('css/homepage.css') }}">

  </head>
  <body>
    <!-- Navbar -->
    <nav>
      <img src="img/iconSpareHub.png" id="logo" alt="Logo SpareHub" />
      <ul>
        <li><a href="homepage.html">Beranda</a></li>
        <li class="nav-keranjang-wrapper">
          <a href="{{ route('keranjang') }}">
            Keranjang
            <span id="cart-count" class="cart-count">0</span>
          </a>
        </li>
        <li><a href="profil_toko.html">Toko Saya</a></li>
        <li>
          <div id="profil">
            <!-- User info will be loaded by navbar-manager.js -->
          </div>
        </li>
      </ul>
    </nav>

    <!-- Hero -->
    <section class="hero">
      <h1>Selamat Datang di <span>SpareHub</span></h1>
      <p>Tempat terbaik untuk mencari suku cadang kendaraan Anda!</p>
      <button>Jelajahi Produk</button>
    </section>

    <!-- Produk -->
    <section class="produk">
      <h2>Produk Unggulan</h2>
      <!-- Produk akan dimuat dari JS -->
    </section>

    <!-- Footer -->
    <footer>
      <p>&copy; 2025 SpareHub. Semua hak dilindungi.</p>
    </footer>

    <!-- JS -->
    <script src="{{ asset('js/navbar-manager.js') }}"></script>
    <script src="{{ asset('js/homepage.js') }}"></script>

  </body>
</html>