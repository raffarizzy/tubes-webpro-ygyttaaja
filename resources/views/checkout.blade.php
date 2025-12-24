<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Checkout - SpareHub</title>
    <link rel="icon" href="img/iconSpareHub.png" />
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css"
    />
    <link rel="stylesheet" href="{{ asset('css/navbar-unified.css') }}">
  </head>

  <body class="bg-light">
    <nav>
      <img src="img/iconSpareHub.png" id="logo" alt="Logo SpareHub" />
      <ul>
        <li><a href="{{ url('/') }}">Beranda</a></li>
        <a href="{{ route('keranjang') }}">Keranjang</a>
        <li><a href="profil_toko.html">Toko Saya</a></li>
        <li>
          <div id="profil"></div>
        </li>
      </ul>
    </nav>

    <main class="container my-3">
      <h2 class="mb-3 fw-bold">Checkout</h2>

      <div class="row g-3">
        <div class="col-lg-7">
          <!-- Pilih Alamat -->
          <div class="card shadow-sm mb-3">
            <div class="card-body p-3">
              <h6 class="text-secondary fw-bold mb-3">Pilih Alamat</h6>

              <div class="row g-2" id="addressContainer">
                <div class="col-md-6 col-xl-4">
                  <div
                    class="card border-2 border-dashed h-100"
                    id="addAddressCard"
                    role="button"
                  >
                    <div
                      class="card-body p-3 d-flex align-items-center justify-content-center text-center text-secondary"
                    >
                      <div>
                        <i class="bi bi-plus-circle fs-4"></i>
                        <p class="mt-2 mb-0 small">Tambah Alamat</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <div id="addAddressForm" class="d-none mt-3">
                <div class="card border-success">
                  <div class="card-body p-3">
                    <h6 class="card-title mb-3" id="formTitle">
                      Tambah Alamat Baru
                    </h6>
                    <form>
                      <div class="mb-2">
                        <label for="namaInput" class="form-label small mb-1">Nama</label>
                        <input
                          type="text"
                          class="form-control form-control-sm"
                          id="namaInput"
                          placeholder="Masukkan nama"
                          required
                        />
                      </div>
                      <div class="mb-2">
                        <label for="alamatInput" class="form-label small mb-1"
                          >Alamat Lengkap</label
                        >
                        <textarea
                          class="form-control form-control-sm"
                          id="alamatInput"
                          rows="2"
                          placeholder="Masukkan alamat lengkap"
                          required
                        ></textarea>
                      </div>
                      <div class="mb-3">
                        <label for="nomorInput" class="form-label small mb-1"
                          >Nomor HP</label
                        >
                        <input
                          type="tel"
                          class="form-control form-control-sm"
                          id="nomorInput"
                          placeholder="08xx xxxx xxxx"
                          required
                        />
                      </div>
                      <div class="d-flex gap-2 justify-content-end">
                        <button
                          type="button"
                          id="deleteAddress"
                          class="btn btn-danger btn-sm d-none"
                        >
                          <i class="bi bi-trash"></i> Hapus
                        </button>
                        <button
                          type="button"
                          id="cancelAdd"
                          class="btn btn-secondary btn-sm"
                        >
                          Batal
                        </button>
                        <button
                          type="button"
                          id="saveAddress"
                          class="btn btn-success btn-sm"
                        >
                          <i class="bi bi-check-lg"></i> Simpan
                        </button>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Detail Item -->
          <div class="card shadow-sm mb-3">
            <div class="card-body p-3">
              <h6 class="text-secondary fw-bold mb-3">Detail Item</h6>

              <div id="checkoutItems"></div>
            </div>
          </div>


          <!-- Metode Pembayaran -->
          <div class="card shadow-sm mb-3">
            <div class="card-body p-3">
              <h6 class="text-secondary fw-bold mb-3">
                Pilih Metode Pembayaran
              </h6>

              <div class="row g-2">
                <div class="col-md-6 col-xl-4">
                  <div class="card border-2 h-100" role="button">
                    <div class="card-body p-3">
                      <img
                        src="img/visa.png"
                        alt="Visa"
                        class="mb-2"
                        height="24"
                      />
                      <p class="mb-1 fw-semibold small">**** **** **** 0817</p>
                      <p class="text-muted mb-0" style="font-size: 0.75rem;">Expires 12-29</p>
                    </div>
                  </div>
                </div>

                <div class="col-md-6 col-xl-4">
                  <div class="card border-2 h-100" role="button">
                    <div class="card-body p-3">
                      <img
                        src="img/mastercard.png"
                        alt="Mastercard"
                        class="mb-2"
                        height="24"
                      />
                      <p class="mb-1 fw-semibold small">**** **** **** 3830</p>
                      <p class="text-muted mb-0" style="font-size: 0.75rem;">Expires 10-27</p>
                    </div>
                  </div>
                </div>

                <div class="col-md-6 col-xl-4">
                  <div class="card border-2 border-dashed h-100" role="button">
                    <div
                      class="card-body p-3 d-flex align-items-center justify-content-center text-center text-secondary"
                    >
                      <div>
                        <i class="bi bi-plus-circle fs-4"></i>
                        <p class="mt-2 mb-0 small">
                          Tambah Metode Pembayaran
                        </p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4 ms-lg-3">
          <div class="card shadow-sm">
            <div class="card-body p-3">
              <h6 class="card-title border-bottom pb-2 mb-3">Detail Pesanan</h6>

              <div class="small">
                <div class="d-flex justify-content-between mb-2">
                  <span class="text-muted">Harga</span>
                  <span class="fw-bold" id="orderPrice">Rp 0</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                  <span class="text-muted">Biaya Antar</span>
                  <span class="text-success fw-bold" id="orderDelivery">Gratis</span>
                </div>
                <div class="d-flex justify-content-between mb-2 pb-2 border-bottom">
                  <span class="text-muted">Diskon</span>
                  <span class="text-danger fw-bold" id="orderDiscount">- Rp 0</span>
                </div>
                <div class="d-flex justify-content-between pt-2 fs-6">
                  <span class="fw-bold">Total Harga</span>
                  <span class="fw-bold" id="orderTotal">Rp 0</span>
                </div>
              </div>
            </div>
          </div>

          <div class="d-grid gap-2 mt-3">
            <button class="btn btn-success" id="payNowBtn">
              <i class="bi bi-credit-card"></i> Bayar Sekarang
            </button>
          </div>
        </div>
      </div>
    </main>

    <footer class="bg-dark text-white text-center py-2 mt-4">
      <p class="mb-0 small">&copy; 2025 SpareHub</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/navbar-manager.js') }}"></script>
    <script src="{{ asset('js/checkout.js') }}"></script>

    <style>
      .card-selectable.selected {
        border-color: #198754 !important;
        border-width: 3px !important;
        box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.25) !important;
      }

      .card-selectable {
        cursor: pointer;
        transition: all 0.3s ease;
      }

      .card-selectable:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
      }
    </style>
  </body>
</html>