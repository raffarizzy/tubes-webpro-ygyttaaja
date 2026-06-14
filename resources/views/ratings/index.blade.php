@extends('layouts.main')

@section('title', 'Riwayat Ulasan - Medcom')

@push('styles')
<style>
    .rating-card {
        border-radius: 15px;
        transition: transform 0.2s;
        border: 1px solid rgba(0,0,0,0.05);
    }
    .rating-card:hover {
        transform: translateY(-5px);
    }
    .rating-star {
        color: #ffc107;
        font-size: 1rem;
    }
    .product-img {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border-radius: 12px;
    }
    .review-date {
        font-size: 0.8rem;
        color: #999;
    }
    .badge-rating {
        background: rgba(255, 193, 7, 0.1);
        color: #ffc107;
        padding: 5px 12px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.85rem;
    }
    .italic {
        font-style: italic;
    }
</style>
@endpush

@section('content')
<div class="container my-5">

    {{-- FLASH MESSAGES --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm border-0">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm border-0">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="text-center mb-5">
        <h2 class="fw-bold">Ulasan Saya</h2>
        <p class="text-muted">Kelola ulasan produk yang telah kamu berikan</p>
    </div>

    @if ($ratings->count() > 0)
        <div class="row g-4">
            @foreach ($ratings as $rating)
                <div class="col-md-6">
                    <div class="card rating-card h-100 shadow-sm border-0">
                        <div class="card-body p-4">
                            <div class="d-flex gap-3 align-items-start mb-3">
                                <img
                                    src="{{ isset($rating['image_path']) && $rating['image_path']
                                        ? asset('storage/' . $rating['image_path'])
                                        : asset('img/no-image.png') }}"
                                    alt="{{ $rating['product_name'] ?? 'Produk' }}"
                                    class="product-img shadow-sm"
                                    onerror="this.src='{{ asset('img/no-image.png') }}'"
                                >
                                <div class="flex-grow-1">
                                    <h6 class="fw-bold mb-1">{{ $rating['product_name'] ?? 'Produk' }}</h6>
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <div class="rating-star">
                                            @for($i=1; $i<=5; $i++)
                                                <i class="bi bi-star{{ $i <= $rating['rating'] ? '-fill' : '' }}"></i>
                                            @endfor
                                        </div>
                                        <span class="badge-rating">{{ $rating['rating'] }}/5</span>
                                    </div>
                                    <p class="review-date mb-0">
                                        <i class="bi bi-calendar-check me-1"></i>
                                        {{ isset($rating['created_at']) 
                                            ? \Carbon\Carbon::parse($rating['created_at'])->translatedFormat('d F Y') 
                                            : '' }}
                                    </p>
                                </div>
                            </div>

                            <div class="bg-light p-3 rounded-3 mb-3">
                                <p class="mb-0 text-dark italic small">"{{ $rating['review'] }}"</p>
                            </div>

                            <div class="text-end">
                                <form
                                    action="{{ route('ratings.destroy', $rating['id']) }}"
                                    method="POST"
                                    onsubmit="return confirm('Yakin ingin menghapus ulasan ini?')"
                                    class="d-inline"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm text-danger btn-link p-0 text-decoration-none">
                                        <i class="bi bi-trash3 me-1"></i> Hapus Ulasan
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-5">
            <div class="mb-3">
                <i class="bi bi-chat-left-dots text-muted" style="font-size: 4rem;"></i>
            </div>
            <h5 class="text-muted">Belum ada ulasan yang kamu berikan</h5>
            <p class="text-muted small">Belanja produk sparepart berkualitas dan berikan ulasan pertamamu!</p>
            <a href="{{ url('/') }}" class="btn btn-primary mt-3 px-4 rounded-pill shadow-sm" style="background-color: #122c4f; border: none;">
                Mulai Belanja
            </a>
        </div>
    @endif

</div>
@endsection
