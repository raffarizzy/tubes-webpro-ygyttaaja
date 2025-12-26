@extends('layouts.main')

@section('title', 'Riwayat Pesanan - SpareHub')
@section('body-class', 'class="bg-light"')

@push('bootstrap')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" />
@endpush

@section('footer-class', 'class="bg-dark text-white text-center py-2 mt-4"')
@section('footer-text-class', 'class="mb-0 small"')

@section('content')
<main class="container my-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1 fw-bold">
                <i class="bi bi-clock-history text-primary"></i> Riwayat Pesanan
            </h2>
            <p class="text-muted mb-0">Lihat semua pesanan yang pernah Anda buat</p>
        </div>
        <a href="{{ url('/') }}" class="btn btn-outline-primary">
            <i class="bi bi-house"></i> Kembali ke Beranda
        </a>
    </div>

    <!-- Orders Container -->
    <div id="pesananContainer">
        <!-- Loading State (will be populated by JS) -->
    </div>
</main>
@endsection

@push('scripts')
<script src="{{ asset('js/riwayat_pesanan.js') }}"></script>
@endpush