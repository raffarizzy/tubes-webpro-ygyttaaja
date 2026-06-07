@extends('layouts.main')

@section('title', 'Profil Toko - SpareHub')

@push('bootstrap')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
@endpush

@section('content')
<div class="container py-4">
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-2 text-center">
                    <img src="{{ $toko['logo_path'] ? asset('storage/'.$toko['logo_path']) : asset('images/default-store.png') }}" 
                         class="rounded-circle border" style="width: 100px; height: 100px; object-fit: cover;">
                </div>
                <div class="col-md-8">
                    <h3>{{ $toko['nama_toko'] }}</h3>
                    <p>{{ $toko['deskripsi_toko'] }}</p>
                    <p><strong>Lokasi:</strong> {{ $toko['lokasi'] }}</p>
                </div>
                <div class="col-md-2 text-end">
                    <button class="btn btn-outline-primary" onclick="alert('Edit logic')">Edit Toko</button>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between mb-3">
                <h4>Produk</h4>
                <button class="btn btn-primary">+ Tambah Produk</button>
            </div>

            @if(!isset($toko['products']) || $toko['products']->count() == 0)
                <p class="text-muted">Belum ada produk.</p>
            @else
                <div class="row">
                    @foreach($toko['products'] as $p)
                        <div class="col-md-3 mb-3">
                            <div class="card h-100">
                                <img src="{{ asset('storage/'.$p->imagePath) }}" class="card-img-top" style="height:150px;object-fit:cover;">
                                <div class="card-body">
                                    <h6 class="card-title">{{ $p->nama }}</h6>
                                    <p class="text-primary fw-bold">Rp {{ number_format($p->harga, 0, ',', '.') }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const TOKO_ID = {{ $toko['id'] }};
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@endpush
