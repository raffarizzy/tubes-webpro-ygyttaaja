@extends('layouts.main')

@section('title', 'Checkout - SpareHub')

@section('body-class', 'class="checkout-page"')

@push('bootstrap')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet" />
@endpush

@section('footer-class', 'class="bg-dark text-white text-center py-2 mt-4"')
@section('footer-text-class', 'class="mb-0 small"')

@section('content')
    <main class="checkout-main py-4">
      <div class="checkout-frame mx-auto px-3">

        {{-- ── 4-step progress stepper ── --}}
        <div class="stepper mb-4">
          <div class="stepper-inner">
            {{-- Step 1: Keranjang (completed) --}}
            <div class="step step--done">
              <div class="step__circle">
                <i class="bi bi-check-lg"></i>
              </div>
              <span class="step__label">Keranjang</span>
            </div>

            <div class="step__line step__line--done"></div>

            {{-- Step 2: Pengiriman (active) --}}
            <div class="step step--active">
              <div class="step__circle">2</div>
              <span class="step__label">Pengiriman</span>
            </div>

            <div class="step__line"></div>

            {{-- Step 3: Pembayaran --}}
            <div class="step">
              <div class="step__circle">3</div>
              <span class="step__label">Pembayaran</span>
            </div>

            <div class="step__line"></div>

            {{-- Step 4: Selesai --}}
            <div class="step">
              <div class="step__circle">4</div>
              <span class="step__label">Selesai</span>
            </div>
          </div>
        </div>

        {{-- ── Two-column layout ── --}}
        <div class="row g-3 align-items-start">

          {{-- ── LEFT COLUMN ── --}}
          <div class="col-lg-7">

            {{-- Pilih Alamat --}}
            <div class="co-card mb-3">
              <div class="co-card__header">
                <i class="bi bi-geo-alt-fill me-2 text-gold"></i>
                <span>Pilih Alamat</span>
              </div>
              <div class="co-card__body">

                <div class="row g-2" id="addressContainer">
                  <div class="col-md-6 col-xl-4">
                    <div
                      class="add-address-card h-100"
                      id="addAddressCard"
                      role="button"
                    >
                      <i class="bi bi-plus-circle fs-4"></i>
                      <p class="mt-2 mb-0 small">Tambah Alamat</p>
                    </div>
                  </div>
                </div>

                <div id="addAddressForm" class="d-none mt-3">
                  <div class="address-form-card">
                    <h6 class="fw-semibold mb-3" id="formTitle">Tambah Alamat Baru</h6>
                    <form>
                      <div class="mb-2">
                        <label for="namaInput" class="form-label small fw-medium mb-1">Nama</label>
                        <input
                          type="text"
                          class="form-control form-control-sm co-input"
                          id="namaInput"
                          placeholder="Masukkan nama"
                          required
                        />
                      </div>
                      <div class="mb-2">
                        <label for="alamatInput" class="form-label small fw-medium mb-1">Alamat Lengkap</label>
                        <textarea
                          class="form-control form-control-sm co-input"
                          id="alamatInput"
                          rows="2"
                          placeholder="Masukkan alamat lengkap"
                          required
                        ></textarea>
                      </div>
                      <div class="mb-2">
                        <label for="nomorInput" class="form-label small fw-medium mb-1">Nomor HP</label>
                        <input
                          type="tel"
                          class="form-control form-control-sm co-input"
                          id="nomorInput"
                          placeholder="08xx xxxx xxxx"
                          required
                        />
                      </div>

                      <div class="mb-3">
                        <div class="form-check">
                          <input
                            class="form-check-input"
                            type="checkbox"
                            id="defaultCheckbox"
                          />
                          <label class="form-check-label small" for="defaultCheckbox">
                            <i class="bi bi-star-fill text-warning"></i> Jadikan sebagai alamat utama
                          </label>
                        </div>
                        <small class="text-muted d-block mt-1">
                          Alamat utama akan otomatis terpilih saat checkout
                        </small>
                      </div>

                      <div class="d-flex gap-2 justify-content-end">
                        <button type="button" id="deleteAddress" class="btn btn-danger btn-sm d-none">
                          <i class="bi bi-trash"></i> Hapus
                        </button>
                        <button type="button" id="cancelAdd" class="btn co-btn-outline btn-sm">
                          Batal
                        </button>
                        <button type="button" id="saveAddress" class="btn co-btn-primary btn-sm">
                          <i class="bi bi-check-lg"></i> Simpan
                        </button>
                      </div>
                    </form>
                  </div>
                </div>

              </div>
            </div>

            {{-- Detail Item --}}
            <div class="co-card mb-3">
              <div class="co-card__header">
                <i class="bi bi-box-seam-fill me-2 text-gold"></i>
                <span>Detail Item</span>
              </div>
              <div class="co-card__body">
                <div id="checkoutItems"></div>
              </div>
            </div>

            {{-- Metode Pembayaran --}}
            <div class="co-card mb-3">
              <div class="co-card__header">
                <i class="bi bi-wallet2 me-2 text-gold"></i>
                <span>Pilih Metode Pembayaran</span>
              </div>
              <div class="co-card__body">
                <div id="paymentMethodsContainer">
                  <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                      <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="text-muted mt-2 mb-0 small">Memuat metode pembayaran...</p>
                  </div>
                </div>
              </div>
            </div>

          </div>{{-- /col-lg-7 --}}

          {{-- ── RIGHT SIDEBAR ── --}}
          <div class="col-lg-4 ms-lg-2">

            {{-- Detail Pesanan --}}
            <div class="co-card mb-3">
              <div class="co-card__header">
                <i class="bi bi-receipt me-2 text-gold"></i>
                <span>Detail Pesanan</span>
              </div>
              <div class="co-card__body">
                <div class="order-row">
                  <span class="order-row__label">Harga</span>
                  <span class="order-row__value fw-semibold" id="orderPrice">Rp 0</span>
                </div>
                <div class="order-row">
                  <span class="order-row__label">Biaya Antar</span>
                  <span class="order-row__value fw-semibold text-success" id="orderDelivery">Gratis</span>
                </div>
                <div class="order-row order-row--sep">
                  <span class="order-row__label">Diskon</span>
                  <span class="order-row__value fw-semibold text-danger" id="orderDiscount">- Rp 0</span>
                </div>
                <div class="order-row order-row--total mt-2">
                  <span class="fw-bold">Total Harga</span>
                  <span class="fw-bold fs-6" id="orderTotal">Rp 0</span>
                </div>
              </div>
            </div>

            {{-- Pay button + hint --}}
            <button class="btn pay-now-btn w-100 disabled" id="payNowBtn" disabled>
              <i class="bi bi-credit-card me-2"></i>Bayar Sekarang
            </button>

            <small class="text-muted text-center d-block mt-2" id="paymentHint">
              Pilih alamat dan metode pembayaran untuk melanjutkan
            </small>

            <div class="security-note mt-3">
              <i class="bi bi-shield-check text-navy me-1"></i>
              <small>Transaksi kamu dilindungi enkripsi SSL 256-bit</small>
            </div>

          </div>{{-- /col-lg-4 --}}

        </div>{{-- /row --}}
      </div>{{-- /checkout-frame --}}
    </main>
@endsection

@push('scripts')
    <script>
        window.APP_USER_ID = {{ auth()->id() }};
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/checkout.js') }}"></script>
@endpush

@push('styles')
<style>
  /* ─── Design tokens ─────────────────────────────── */
  :root {
    --navy:   #122C4F;
    --navy-2: #1a3a66;
    --blue:   #0066CC;
    --gold:   #FFC107;
    --cream:  #F4E9DC;
    --ink:    #202124;
    --muted:  #5F6368;
    --line:   #E5DFD3;
    --ok:     #1E8E3E;
  }

  /* ─── Page shell ────────────────────────────────── */
  .checkout-page { background: var(--cream); font-family: 'Roboto', sans-serif; }

  .checkout-main { min-height: 80vh; }

  .checkout-frame { max-width: 1024px; }

  .text-gold { color: var(--gold) !important; }
  .text-navy { color: var(--navy) !important; }

  /* ─── Stepper ───────────────────────────────────── */
  .stepper {
    background: #fff;
    border-radius: 12px;
    padding: 20px 24px;
    box-shadow: 0 2px 8px rgba(18,44,79,.08);
  }

  .stepper-inner {
    display: flex;
    align-items: center;
    justify-content: center;
  }

  .step {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
    flex-shrink: 0;
  }

  .step__circle {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: 'Poppins', sans-serif;
    font-size: .85rem;
    font-weight: 600;
    background: var(--line);
    color: var(--muted);
    border: 2px solid var(--line);
    transition: all .3s;
  }

  .step__label {
    font-size: .72rem;
    font-family: 'Poppins', sans-serif;
    font-weight: 500;
    color: var(--muted);
    white-space: nowrap;
  }

  .step--done .step__circle {
    background: var(--ok);
    border-color: var(--ok);
    color: #fff;
  }
  .step--done .step__label { color: var(--ok); }

  .step--active .step__circle {
    background: var(--navy);
    border-color: var(--navy);
    color: #fff;
    box-shadow: 0 0 0 4px rgba(18,44,79,.18);
  }
  .step--active .step__label {
    color: var(--navy);
    font-weight: 700;
  }

  .step__line {
    flex: 1;
    height: 2px;
    background: var(--line);
    margin: 0 6px;
    margin-bottom: 22px; /* align with circle centre */
  }
  .step__line--done { background: var(--ok); }

  /* ─── Shared card shell ─────────────────────────── */
  .co-card {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(18,44,79,.07);
    overflow: hidden;
  }

  .co-card__header {
    display: flex;
    align-items: center;
    padding: 14px 18px 12px;
    border-bottom: 1px solid var(--line);
    font-family: 'Poppins', sans-serif;
    font-size: .9rem;
    font-weight: 600;
    color: var(--ink);
    background: linear-gradient(90deg, rgba(18,44,79,.03) 0%, transparent 100%);
  }

  .co-card__body { padding: 16px 18px; }

  /* ─── Add-address card (dashed tile) ────────────── */
  .add-address-card {
    border: 2px dashed var(--line);
    border-radius: 10px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 20px 12px;
    text-align: center;
    color: var(--muted);
    cursor: pointer;
    transition: all .25s;
  }
  .add-address-card:hover {
    border-color: var(--blue);
    color: var(--blue);
    background: rgba(0,102,204,.04);
  }

  /* ─── Address form card ─────────────────────────── */
  .address-form-card {
    border: 1.5px solid var(--ok);
    border-radius: 10px;
    padding: 16px;
    background: rgba(30,142,62,.03);
  }

  .co-input {
    border-radius: 8px !important;
    border-color: var(--line) !important;
    font-size: .875rem;
  }
  .co-input:focus {
    border-color: var(--blue) !important;
    box-shadow: 0 0 0 3px rgba(0,102,204,.12) !important;
  }

  /* ─── Buttons ───────────────────────────────────── */
  .co-btn-primary {
    background: var(--navy);
    border-color: var(--navy);
    color: #fff;
    border-radius: 8px;
  }
  .co-btn-primary:hover {
    background: var(--navy-2);
    border-color: var(--navy-2);
    color: #fff;
  }

  .co-btn-outline {
    background: transparent;
    border-color: var(--line);
    color: var(--muted);
    border-radius: 8px;
  }
  .co-btn-outline:hover {
    background: var(--line);
    color: var(--ink);
  }

  /* ─── Pay Now button ────────────────────────────── */
  .pay-now-btn {
    background: linear-gradient(135deg, var(--navy) 0%, var(--navy-2) 100%);
    color: #fff !important;
    border: none;
    border-radius: 50px;
    padding: 13px 24px;
    font-family: 'Poppins', sans-serif;
    font-weight: 600;
    font-size: .95rem;
    letter-spacing: .3px;
    transition: all .3s;
    box-shadow: 0 4px 14px rgba(18,44,79,.35);
  }
  .pay-now-btn:not(:disabled):not(.disabled):hover {
    background: linear-gradient(135deg, var(--navy-2) 0%, #213f72 100%);
    box-shadow: 0 6px 18px rgba(18,44,79,.45);
    transform: translateY(-1px);
  }
  .pay-now-btn.disabled,
  .pay-now-btn:disabled {
    opacity: .55;
    cursor: not-allowed;
    box-shadow: none;
  }

  /* ─── Order summary rows ────────────────────────── */
  .order-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 6px 0;
    font-size: .875rem;
  }
  .order-row__label { color: var(--muted); }
  .order-row--sep {
    border-bottom: 1px dashed var(--line);
    padding-bottom: 10px;
  }
  .order-row--total { padding-top: 10px; }

  /* ─── Security note ─────────────────────────────── */
  .security-note {
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--muted);
    font-size: .78rem;
    gap: 4px;
  }

  /* ─── Existing card-selectable styles (kept) ────── */
  .card-selectable {
    cursor: pointer;
    transition: all 0.3s ease;
  }
  .card-selectable:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,.15);
  }
  .card-selectable.selected {
    border-color: #198754 !important;
    border-width: 2px !important;
  }

  /* ─── Existing payment-method-card styles (kept) ── */
  .payment-method-card {
    cursor: pointer;
    transition: all 0.3s ease;
    border: 2px solid #dee2e6;
    border-radius: 10px;
  }
  .payment-method-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,.15);
  }
  .payment-method-card.selected {
    border-color: #198754 !important;
  }

  .payment-method-icon {
    width: 80px;
    height: 80px;
    object-fit: contain;
    display: block;
    margin: 0 auto;
  }
  .payment-logo-container {
    min-height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
  }

  /* ─── Responsive tweaks ─────────────────────────── */
  @media (max-width: 576px) {
    .step__label { font-size: .65rem; }
    .step__circle { width: 30px; height: 30px; font-size: .78rem; }
    .stepper { padding: 14px 10px; }
  }
</style>
@endpush