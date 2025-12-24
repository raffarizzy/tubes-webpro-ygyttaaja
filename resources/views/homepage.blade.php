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
        <button>Jelajahi Produk</button>
    </section>

    <!-- Produk -->
    <section class="produk">
        <h2>Produk Unggulan</h2>
        <!-- Produk akan dimuat dari JS -->
    </section>
@endsection

@push('scripts')
    <script src="{{ asset('js/homepage.js') }}"></script>
@endpush