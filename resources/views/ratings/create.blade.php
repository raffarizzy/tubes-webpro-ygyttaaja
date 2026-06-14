@extends('layouts.main')

@section('title', 'Beri Ulasan Produk - Medcom')

@push('styles')
<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
    .card-rating {
        border-radius: 20px;
        overflow: hidden;
    }
    .rating-header {
        background: linear-gradient(135deg, #122c4f 0%, #1a457a 100%);
        color: white;
        padding: 40px 20px;
        text-align: center;
    }
    /* Star Rating Logic */
    .star-rating {
        display: flex;
        flex-direction: row-reverse;
        justify-content: center;
        gap: 15px;
    }
    .star-rating input {
        display: none;
    }
    .star-rating label {
        font-size: 3rem;
        color: #e9ecef;
        cursor: pointer;
        transition: all 0.2s ease-in-out;
        margin: 0;
    }
    /* Effects */
    .star-rating label:hover,
    .star-rating label:hover ~ label,
    .star-rating input:checked ~ label {
        color: #ffc107;
    }
    .star-rating label:active {
        transform: scale(0.9);
    }
    .product-preview {
        background: #f8f9fa;
        border-radius: 15px;
        padding: 15px;
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 30px;
        border: 1px solid rgba(0,0,0,0.05);
    }
    .product-preview img {
        width: 70px;
        height: 70px;
        object-fit: cover;
        border-radius: 10px;
    }
    .btn-submit-rating {
        background-color: #122c4f;
        color: white;
        border-radius: 12px;
        padding: 14px 30px;
        font-weight: 600;
        transition: all 0.3s;
        border: none;
    }
    .btn-submit-rating:hover {
        background-color: #0d2033;
        transform: translateY(-2px);
        color: white;
        box-shadow: 0 5px 15px rgba(18, 44, 79, 0.2);
    }
</style>
@endpush

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card card-rating border-0 shadow-lg">
                <div class="rating-header">
                    <h3 class="fw-bold mb-2">{{ isset($existingRating) ? 'Ulasan Anda' : 'Beri Ulasan' }}</h3>
                    <p class="mb-0 opacity-75">{{ isset($existingRating) ? 'Terima kasih telah berbagi pengalamanmu' : 'Bagikan pengalamanmu menggunakan produk ini' }}</p>
                </div>
                
                <div class="card-body p-4 p-md-5">
                    {{-- Product Info --}}
                    <div class="product-preview shadow-sm">
                        <img src="{{ $product->imagePath ? asset('storage/' . $product->imagePath) : asset('img/no-image.png') }}" alt="{{ $product->nama }}" onerror="this.src='{{ asset('img/no-image.png') }}'">
                        <div>
                            <h6 class="mb-1 fw-bold">{{ $product->nama }}</h6>
                            <p class="text-muted small mb-0 text-truncate" style="max-width: 200px;">{{ $product->deskripsi }}</p>
                        </div>
                    </div>

                    @if(isset($existingRating))
                        {{-- Read Only View --}}
                        <div class="mb-4 text-center">
                            <label class="form-label d-block fw-bold mb-3">Kualitas Produk</label>
                            <div class="d-flex justify-content-center gap-2 text-warning fs-1">
                                @for($i=1; $i<=5; $i++)
                                    <i class="bi bi-star{{ $i <= $existingRating['rating'] ? '-fill' : '' }}"></i>
                                @endfor
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Ulasan Anda</label>
                            <div class="p-3 bg-light rounded-3 italic">
                                "{{ $existingRating['review'] }}"
                            </div>
                            <small class="text-muted d-block mt-2">
                                <i class="bi bi-calendar-check me-1"></i>
                                Direview pada {{ \Carbon\Carbon::parse($existingRating['created_at'])->translatedFormat('d F Y') }}
                            </small>
                        </div>

                        <div class="d-grid gap-2">
                            <a href="{{ route('riwayat.pesanan') }}" class="btn btn-submit-rating">Kembali ke Riwayat</a>
                            <form action="{{ route('ratings.destroy', $existingRating['id']) }}" method="POST" onsubmit="return confirm('Hapus ulasan ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-link text-danger w-100">Hapus Ulasan</button>
                            </form>
                        </div>
                    @else
                        {{-- Create Form --}}
                        <form action="{{ route('ratings.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">

                            {{-- Star Selection --}}
                            <div class="mb-4 text-center">
                                <label class="form-label d-block fw-bold mb-3">Kualitas Produk</label>
                                <div class="star-rating">
                                    <input type="radio" name="rating" id="star5" value="5" required>
                                    <label for="star5" title="Sangat Puas"><i class="bi bi-star-fill"></i></label>
                                    
                                    <input type="radio" name="rating" id="star4" value="4">
                                    <label for="star4" title="Puas"><i class="bi bi-star-fill"></i></label>
                                    
                                    <input type="radio" name="rating" id="star3" value="3">
                                    <label for="star3" title="Cukup"><i class="bi bi-star-fill"></i></label>
                                    
                                    <input type="radio" name="rating" id="star2" value="2">
                                    <label for="star2" title="Tidak Puas"><i class="bi bi-star-fill"></i></label>
                                    
                                    <input type="radio" name="rating" id="star1" value="1">
                                    <label for="star1" title="Sangat Tidak Puas"><i class="bi bi-star-fill"></i></label>
                                </div>
                                @error('rating')
                                    <small class="text-danger mt-2 d-block">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- Review Text --}}
                            <div class="mb-4">
                                <label for="review" class="form-label fw-bold">Ulasan Anda</label>
                                <textarea 
                                    name="review" 
                                    id="review" 
                                    class="form-control @error('review') is-invalid @enderror" 
                                    rows="4" 
                                    placeholder="Apa yang membuatmu suka/tidak suka dengan produk ini?"
                                    required
                                >{{ old('review') }}</textarea>
                                @error('review')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-submit-rating">
                                    Kirim Ulasan
                                </button>
                                <a href="{{ route('riwayat.pesanan') }}" class="btn btn-link text-muted">Batal</a>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
