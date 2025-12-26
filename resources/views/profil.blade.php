@extends('layouts.main')

@section('title', 'Profil Pengguna - SpareHub')
@section('body-class', 'class="bg-light"')

@push('bootstrap')
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" />
@endpush

@section('footer-class', 'class="bg-dark text-white text-center py-2 mt-4"')
@section('footer-text-class', 'class="mb-0 small"')

@section('content')
<main class="container my-4">
    <h2 class="mb-3 fw-bold">Profil Pengguna</h2>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-3">
                <div class="card-body d-flex gap-4">
                    <img id="userImage" src="{{ asset('img/iconPengguna.png') }}" alt="Foto Pengguna" class="rounded-circle" width="140" height="140" style="object-fit: cover;">

                    <div class="flex-grow-1">
                        <h4 id="userName" class="fw-bold mb-1">{{ auth()->user()->name }}</h4>
                        <p id="userEmail" class="text-muted mb-1">{{ auth()->user()->email }}</p>
                        <p id="userAlamat" class="text-muted mb-3">Alamat pengguna akan ditampilkan di sini</p>

                        <div class="d-flex gap-2">
                            <a href="{{ route('profile.edit') }}" class="btn btn-primary btn-sm">
                                <i class="bi bi-pencil"></i> Edit Profil
                            </a>
                            <a href="{{ route('riwayat.pesanan') }}" class="btn btn-success btn-sm">
                                <i class="bi bi-clock-history"></i> Riwayat Pesanan
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection

@push('scripts')
    <script>
        // Optional: Load additional user data from JSON if needed
        // But prioritize Laravel auth data
        const userName = "{{ auth()->user()->name }}";
        const userEmail = "{{ auth()->user()->email }}";
        
        console.log('Current user:', userName, userEmail);
    </script>
@endpush

@push('styles')
<style>
    .card img {
        border-radius: 50%;
    }
</style>
@endpush