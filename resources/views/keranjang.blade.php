<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Keranjang - SpareHub</title>
    <link rel="icon" href="{{ asset('img/iconSpareHub.png') }}" />
    <link rel="stylesheet" href="{{ asset('css/navbar-unified.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/keranjang.css') }}" />
  </head>
  <body>
    <!-- Nav -->
    <nav>
      <img src="{{ asset('https://i.ibb.co.com/VcGWcqFG/icon-Spare-Hub.png') }}" id="logo" alt="Logo SpareHub" />
      <ul>
        <li><a href="/">Beranda</a></li>
        <li><a href="{{ route('keranjang') }}">Keranjang</a></li>
        <li><a href="profil_toko.html">Toko Saya</a></li>
        <li>
          <div id="profil">
            <!-- User info will be loaded by navbar-manager.js -->
          </div>
        </li>
      </ul>
    </nav>

    <section class="keranjang">
      <h2>Keranjang Belanja Anda</h2>
      <!-- Keranjang akan di-render otomatis -->
      <div class="container-keranjang" id="keranjang-container"></div>

      <div class="ringkasan">
        <h3>Ringkasan Belanja</h3>
        <p>Total Item: <span id="total-item">0</span></p>
        <p>Total Harga: <span id="total-harga">Rp 0</span></p>
        <button class="btn-checkout">Lanjut ke Pembayaran</button>
      </div>
    </section>

    <footer>
      <p>&copy; 2025 SpareHub. Semua hak dilindungi.</p>
    </footer>

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
    <script src="{{ asset('js/navbar-manager.js') }}"></script>
    <script src="{{ asset('js/keranjang.js') }}"></script>
  </body>
</html>