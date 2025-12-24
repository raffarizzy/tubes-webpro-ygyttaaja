@extends('layouts.main')

@section('title', 'Home Page - SpareHub')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/homepage.css') }}">
@endpush

@section('content')
    <!-- Hero -->
    <section class="hero">
        <h1>Selamat Datang di <span>SpareHub</span></h1>
        <p>Tempat terbaik untuk mencari suku cadang kendaraan Anda!</p>
        <button id="scroll-produk">Jelajahi Produk</button>
    </section>

    <!-- Search & Filter Section -->
    <section class="search-filter-section" style="max-width: 1200px; margin: 40px auto; padding: 0 20px;">
        <div style="display: flex; gap: 20px; flex-wrap: wrap; align-items: end;">
            <!-- Search Bar -->
            <div style="flex: 1; min-width: 250px;">
                <label for="search-input" style="display: block; margin-bottom: 8px; font-weight: 500;">Cari Produk</label>
                <input
                    type="text"
                    id="search-input"
                    placeholder="Cari nama produk..."
                    style="width: 100%; padding: 10px 15px; border: 2px solid #ddd; border-radius: 8px; font-size: 14px;"
                />
            </div>

            <!-- Filter Harga Min -->
            <div style="min-width: 150px;">
                <label for="price-min" style="display: block; margin-bottom: 8px; font-weight: 500;">Harga Min</label>
                <input
                    type="number"
                    id="price-min"
                    placeholder="Rp 0"
                    style="width: 100%; padding: 10px 15px; border: 2px solid #ddd; border-radius: 8px; font-size: 14px;"
                />
            </div>

            <!-- Filter Harga Max -->
            <div style="min-width: 150px;">
                <label for="price-max" style="display: block; margin-bottom: 8px; font-weight: 500;">Harga Max</label>
                <input
                    type="number"
                    id="price-max"
                    placeholder="Rp 999999999"
                    style="width: 100%; padding: 10px 15px; border: 2px solid #ddd; border-radius: 8px; font-size: 14px;"
                />
            </div>

            <!-- Reset Button -->
            <div>
                <button
                    id="reset-filter"
                    style="padding: 10px 20px; background-color: #6c757d; color: white; border: none; border-radius: 8px; font-weight: 500; cursor: pointer;"
                >
                    Reset
                </button>
            </div>
        </div>

        <!-- Results Info -->
        <div id="results-info" style="margin-top: 15px; color: #666; font-size: 14px;">
            Menampilkan semua produk
        </div>
    </section>

    <!-- Produk -->
    <section class="produk">
        <h2>Produk Unggulan</h2>
        <!-- Produk akan dimuat dari JS -->
        <div id="produk-container"></div>

        <!-- Pagination -->
        <div id="pagination" style="display: flex; justify-content: center; gap: 10px; margin-top: 30px; flex-wrap: wrap;">
            <!-- Pagination buttons akan dimuat dari JS -->
        </div>
    </section>
@endsection

@push('scripts')
    <script src="{{ asset('js/homepage.js') }}"></script>
@endpush