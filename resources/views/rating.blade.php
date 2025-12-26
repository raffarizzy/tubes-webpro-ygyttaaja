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

    {{-- FLASH MESSAGE --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <h1 class="text-center mb-4 fw-bold">⭐ Rating & Ulasan Produk</h1>

    {{-- LIST RATING USER --}}
    @if ($ratings->count())
        <div class="mb-5">
            @foreach ($ratings as $rating)
                <div class="card rating-card mb-3 shadow-sm">
                    <div class="card-body d-flex gap-3">

                        <img
                            src="{{ $rating->product->image_path ?? asset('img/no-image.png') }}"
                            alt="{{ $rating->product->nama }}"
                            class="product-img"
                        >

                        <div class="flex-grow-1">
                            <h5 class="mb-1">{{ $rating->product->nama }}</h5>

                            <div class="rating-star mb-1">
                                {{ str_repeat('⭐', $rating->rating) }}
                            </div>

                            <p class="mb-1">{{ $rating->review }}</p>

                            <small class="text-muted">
                                {{ $rating->created_at->format('d M Y') }}
                            </small>

                            <form
                                action="{{ route('ratings.destroy', $rating->id) }}"
                                method="POST"
                                class="mt-2"
                                onsubmit="return confirm('Yakin ingin menghapus rating ini?')"
                            >
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">
                                    Hapus
                                </button>
                            </form>
                        </div>

                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="alert alert-secondary text-center mb-5">
            Kamu belum memberikan rating produk.
        </div>
    @endif

    {{-- FORM TAMBAH RATING --}}
    @if ($products->count())
        <div class="card rating-card shadow">
            <div class="card-body">
                <h4 class="card-title mb-4 fw-semibold">Tambah Rating Produk</h4>

                <form method="POST" action="{{ route('ratings.store') }}">
                    @csrf

                    {{-- PRODUK --}}
                    <div class="mb-3">
                        <label class="form-label">Produk</label>
                        <select
                            name="product_id"
                            class="form-select @error('product_id') is-invalid @enderror"
                            required
                        >
                            <option value="">Pilih Produk</option>
                            @foreach ($products as $product)
                                <option value="{{ $product->id }}">
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
                        <label class="form-label">Rating</label>
                        <select
                            name="rating"
                            class="form-select @error('rating') is-invalid @enderror"
                            required
                        >
                            <option value="">Pilih Rating</option>
                            <option value="1">⭐ 1</option>
                            <option value="2">⭐⭐ 2</option>
                            <option value="3">⭐⭐⭐ 3</option>
                            <option value="4">⭐⭐⭐⭐ 4</option>
                            <option value="5">⭐⭐⭐⭐⭐ 5</option>
                        </select>
                        @error('rating')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- REVIEW --}}
                    <div class="mb-3">
                        <label class="form-label">Ulasan</label>
                        <textarea
                            name="review"
                            rows="3"
                            class="form-control @error('review') is-invalid @enderror"
                            required
                        >{{ old('review') }}</textarea>
                        @error('review')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- SUBMIT --}}
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary px-4">
                            Kirim Rating
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @else
        <div class="alert alert-info text-center">
            Semua produk sudah kamu beri rating.
        </div>
    @endif

</div>
@endsection