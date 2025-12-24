@extends('layouts.main')

@section('title', 'Detail Produk - SpareHub')

@section('body-class', 'class="bg-light"')

@push('bootstrap')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
@endpush

@section('footer-class', 'class="text-center bg-primary text-white py-3"')
@section('footer-text-class', 'class="m-0"')

@section('content')
  <!-- KONTEN -->
  <div class="container py-5">
    <div class="row g-5 justify-content-center">
      <!-- GAMBAR PRODUK -->
      <div class="col-md-4 text-center">
        <img id="product-image" src="img/iconOli.png" alt="Produk" class="img-fluid rounded shadow"
          style="max-width: 360px" />
      </div>

      <!-- DETAIL PRODUK -->
      <div class="col-md-6">
        <h3 id="product-name" class="fw-bold text-primary">Loading...</h3>

        <!-- Harga -->
        <div class="d-flex align-items-center gap-2 my-3">
          <span id="product-price" class="fw-bold fs-4 text-primary">Rp0</span>
          <span id="product-original-price" class="text-muted text-decoration-line-through"></span>
          <span id="product-discount" class="badge bg-danger"></span>
        </div>

        <p><strong>Kondisi:</strong> <span id="product-kondisi">-</span></p>
        <p><strong>Stok:</strong> <span id="product-stok">-</span> Tersedia</p>

        <!-- Quantity -->
        <div class="p-3 bg-body-secondary rounded">
          <p class="fw-semibold text-primary mb-2">Jumlah:</p>

          <div class="d-flex align-items-center gap-3">
            <button id="btn-decrease" class="btn btn-outline-primary fw-bold px-3">
              -
            </button>

            <span id="quantity-display" class="fw-bold fs-5 px-3 py-1 bg-white rounded shadow-sm">
              1
            </span>

            <button id="btn-increase" class="btn btn-outline-primary fw-bold px-3">
              +
            </button>
          </div>

          <p class="mt-3">
            <strong>Total:</strong>
            <span id="total-price" class="fw-bold text-primary">Rp0</span>
          </p>
        </div>

        <!-- Info Toko -->
        <div class="d-flex align-items-center gap-3 p-3 bg-white rounded shadow-sm my-3">
          <div class="bg-primary text-white rounded p-3 fs-3 d-flex justify-content-center align-items-center">
            🏪
          </div>

          <div>
            <p id="toko-nama" class="fw-bold text-primary m-0">-</p>
            <p id="toko-lokasi" class="text-muted small m-0">-</p>
          </div>
        </div>

        <!-- Deskripsi -->
        <div class="mt-4">
          <h5 class="text-primary fw-semibold">Deskripsi</h5>
          <p id="product-description" class="text-muted">-</p>
        </div>

        <!-- Rating -->
        <div class="d-flex align-items-center gap-2 mt-3">
          <span class="text-warning fs-3">★</span>
          <span id="avg-rating" class="fw-bold text-warning">0.0</span>
          <span id="rating-count" class="text-muted">(0 ulasan)</span>
        </div>

        <!-- Aksi -->
        <div class="d-flex gap-3 mt-4">
          <button id="btn-Keranjang" class="btn btn-dark w-50">
            Tambah ke Keranjang
          </button>
          <button id="btn-Beli" class="btn btn-success w-50">
            Beli Sekarang
          </button>
        </div>
      </div>
    </div>

    <!-- Ulasan -->
    <div class="mt-5">
      <h4 class="text-primary fw-bold mb-3">Ulasan Pengguna</h4>
      <div id="reviews-list" class="row g-3"></div>
    </div>
  </div>
@endsection

@push('scripts')
  <script src="{{ asset('js/rating.js') }}"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    window.PRODUK_ID = {{ $id }};
  </script>
  <script src="{{ asset('js/detail-produk.js') }}"></script>
@endpush