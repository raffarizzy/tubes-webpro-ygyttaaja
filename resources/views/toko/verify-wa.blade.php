@extends('layouts.main')

@section('title', 'Verifikasi Penjual - Medcom')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7 text-center">
            <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5">
                <div class="mb-4">
                    <div class="bg-primary-subtle rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                        <img src="https://i.ibb.co.com/spW2xV2t/medcom-logo-Copy.png" style="width: 125px; height: 125px;"></img>
                    </div>
                </div>
                
                <h2 class="fw-bold mb-3">Verifikasi Akun Penjual</h2>
                <p class="text-muted fs-5 mb-4">
                    Untuk menjamin keamanan dan kualitas produk di Medcom, semua calon penjual wajib melakukan verifikasi akun melalui admin kami.
                </p>
                
                <div class="bg-light rounded-3 p-4 mb-4 text-start">
                    <h5 class="fw-bold small text-uppercase text-primary mb-3">Langkah Verifikasi:</h5>
                    <ol class="mb-0">
                        <li class="mb-2">Klik tombol <strong>"Hubungi Admin via WA"</strong> di bawah.</li>
                        <li class="mb-2">Kirim pesan otomatis yang sudah tersedia.</li>
                        <li class="mb-2">Tunggu admin memproses permintaan Anda (Estimasi: 1x24 jam).</li>
                        <li>Setelah diverifikasi, Anda bisa langsung membuka toko Anda!</li>
                    </ol>
                </div>

                <div class="d-grid gap-3">
                    @php
                        $adminWa = "6285974936105"; // Ganti dengan nomor admin Anda
                        $message = "Halo Admin Medcom, saya ingin verifikasi akun penjual saya. Nama: " . auth()->user()->name . " (ID: " . auth()->id() . ")";
                        $waUrl = "https://wa.me/" . $adminWa . "?text=" . urlencode($message);
                    @endphp
                    
                    <a href="{{ $waUrl }}" target="_blank" class="btn btn-primary btn-lg rounded-pill fw-bold py-3">
                        <i class="bi bi-whatsapp me-2"></i> Hubungi Admin via WA
                    </a>
                    
                    <a href="{{ route('home') }}" class="btn btn-link text-decoration-none text-muted">
                        Kembali ke Beranda
                    </a>
                </div>
            </div>
            
            <p class="mt-4 text-muted small">
                Sudah menghubungi admin dan belum diverifikasi? <br>
                Pastikan Anda mengirimkan data yang benar untuk mempercepat proses.
            </p>
        </div>
    </div>
</div>
@endsection
