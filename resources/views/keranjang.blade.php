@extends('layouts.main')

@section('title', 'Keranjang - SpareHub')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/keranjang.css') }}" />
@endpush

@section('content')
    <section class="keranjang">
        <h2>Keranjang Belanja Anda</h2>
        
        <!-- Container akan di-render oleh JavaScript -->
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
    {{-- Pass data keranjang dari backend ke JavaScript --}}
    <script>
        // Data keranjang dari backend
        window.keranjangItems = @json($keranjangItems);
    </script>
    
    <script src="{{ asset('js/keranjang-laravel.js') }}"></script>
@endpush