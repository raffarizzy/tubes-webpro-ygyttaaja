@extends('layouts.main')

@section('title', 'Keranjang - SpareHub')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/keranjang.css') }}" />
@endpush

@section('content')
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
@endsection

@push('scripts')
    <script>
        // Pass cart data from Laravel to JavaScript
        window.keranjangItems = @json($keranjangItems ?? []);
    </script>
    <script src="{{ asset('js/keranjang.js') }}"></script>
@endpush