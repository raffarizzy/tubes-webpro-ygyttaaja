@extends('layouts.main')

@section('title', 'Rating & Ulasan Produk - SpareHub')

@push('styles')
<style>
    .rating-card {
        border-radius: 12px;
    }
    .rating-star {
        color: #ffc107;
        font-size: 1.1rem;
    }
    .product-img {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid #eee;
    }
</style>
@endpush

@section('content')
<div class="container my-5">

    {{-- FLASH MESSAGES --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <h2 class="text-center mb-4 fw-bold">Rating & Ulasan Produk</h2>

    {{-- ========== BAGIAN 1: LIST RATING YANG SUDAH ADA ========== --}}
    @if ($ratings->count() > 0)
        <div class="mb-5">
            <h5 class="mb-3 fw-semibold">Rating Kamu</h5>
            @foreach ($ratings as $rating)
                <div class="card rating-card mb-3 shadow-sm">
                    <div class="card-body d-flex gap-3">

                        {{-- PRODUCT IMAGE --}}
                        <img
                            src="{{ isset($rating['image_path']) && $rating['image_path']
                                ? 'http://localhost:8000/storage/' . $rating['image_path']
                                : asset('img/no-image.png') }}"
                            alt="{{ $rating['product_name'] ?? 'Produk' }}"
                            class="product-img"
                        >

                        <div class="flex-grow-1">
                            {{-- PRODUCT NAME --}}
                            <h5 class="mb-1">{{ $rating['product_name'] ?? 'Nama Produk' }}</h5>

                            {{-- RATING STARS --}}
                            <div class="rating-star mb-1">
                                {{ str_repeat('⭐', $rating['rating'] ?? 0) }}
                                <span class="text-muted small ms-1">({{ $rating['rating'] ?? 0 }}/5)</span>
                            </div>

                            {{-- REVIEW TEXT --}}
                            <p class="mb-2 text-muted">{{ $rating['review'] ?? '' }}</p>

                            {{-- DATE --}}
                            <small class="text-muted d-block mb-2">
                                <i class="bi bi-clock me-1"></i>
                                {{ isset($rating['created_at']) 
                                    ? \Carbon\Carbon::parse($rating['created_at'])->format('d M Y, H:i') 
                                    : '' }}
                            </small>

                            {{-- DELETE BUTTON --}}
                            <form
                                action="{{ route('ratings.destroy', $rating['id']) }}"
                                method="POST"
                                onsubmit="return confirm('Yakin ingin menghapus rating ini?')"
                            >
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash me-1"></i> Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="alert alert-secondary text-center mb-5">
            <i class="bi bi-info-circle me-2"></i>
            Kamu belum memberikan rating produk.
        </div>
    @endif

    {{-- ========== BAGIAN 2: FORM TAMBAH RATING ========== --}}
    @if ($products->count() > 0)
        <div class="card rating-card shadow">
            <div class="card-body">
                <h4 class="card-title mb-4 fw-semibold">
                    <i class="bi bi-plus-circle me-2"></i>
                    Tambah Rating Produk
                </h4>

                <form method="POST" action="{{ route('ratings.store') }}">
                    @csrf

                    {{-- PRODUK --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Produk</label>
                        <select
                            name="product_id"
                            class="form-select @error('product_id') is-invalid @enderror"
                            required
                        >
                            <option value="">Pilih Produk</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}" 
                                    {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                    {{ $product->nama }}
                                </option>
                            @endforeach
                        </select>
                        @error('product_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- RATING --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Rating</label>
                        <select
                            name="rating"
                            class="form-select @error('rating') is-invalid @enderror"
                            required
                        >
                            <option value="">Pilih Rating</option>
                            @for ($i = 1; $i <= 5; $i++)
                                <option value="{{ $i }}" 
                                    {{ old('rating') == $i ? 'selected' : '' }}>
                                    {{ str_repeat('⭐', $i) }} - {{ $i }} Bintang
                                </option>
                            @endfor
                        </select>
                        @error('rating')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- REVIEW --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Ulasan</label>
                        <textarea
                            name="review"
                            rows="3"
                            class="form-control @error('review') is-invalid @enderror"
                            placeholder="Tulis pengalaman kamu dengan produk ini..."
                            required
                        >{{ old('review') }}</textarea>
                        @error('review')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- SUBMIT --}}
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-send me-2"></i>
                            Kirim Rating
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @else
        <div class="alert alert-success text-center">
            <i class="bi bi-check-circle me-2"></i>
            Keren! Kamu sudah memberikan rating untuk semua produk.
        </div>
    @endif

</div>
@endsection