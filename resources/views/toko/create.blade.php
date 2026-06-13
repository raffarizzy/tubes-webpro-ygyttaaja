@extends('layouts.main')

@section('title', 'Buka Toko - SpareHub')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
    :root {
        --primary-dark: #122c4f;
        --bg-light: #f8fafc;
    }

    body {
        background-color: var(--bg-light);
    }

    .setup-container {
        max-width: 600px;
        margin: 60px auto;
    }

    .setup-card {
        background: white;
        border-radius: 24px;
        padding: 40px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.05);
        border: 1px solid #f1f5f9;
    }

    .icon-box {
        width: 64px;
        height: 64px;
        background: rgba(18, 44, 79, 0.05);
        color: var(--primary-dark);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        margin-bottom: 24px;
    }

    .form-label {
        font-weight: 600;
        color: #475569;
        margin-bottom: 8px;
    }

    .form-control {
        border-radius: 12px;
        padding: 12px 16px;
        border: 1px solid #e2e8f0;
        transition: all 0.2s;
    }

    .form-control:focus {
        border-color: var(--primary-dark);
        box-shadow: 0 0 0 4px rgba(18, 44, 79, 0.1);
    }

    .btn-create {
        background: var(--primary-dark);
        color: white;
        padding: 14px;
        border-radius: 12px;
        font-weight: 700;
        width: 100%;
        border: none;
        margin-top: 20px;
        transition: all 0.3s;
    }

    .btn-create:hover {
        background: #1e4b8a;
        transform: translateY(-2px);
        box-shadow: 0 8px 15px rgba(18, 44, 79, 0.2);
    }

    .upload-area {
        border: 2px dashed #e2e8f0;
        border-radius: 16px;
        padding: 30px;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s;
        position: relative;
    }

    .upload-area:hover {
        border-color: var(--primary-dark);
        background: #f8fafc;
    }

    #logo-preview {
        max-width: 100px;
        max-height: 100px;
        border-radius: 12px;
        margin-top: 15px;
        display: none;
    }

    .step-indicator {
        font-size: 0.85rem;
        color: #94a3b8;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 8px;
        display: block;
    }
</style>
@endpush

@section('content')
<div class="container setup-container">
    <div class="setup-card">
        <span class="step-indicator">Langkah 1 dari 1</span>
        <div class="icon-box">
            <i class="bi bi-shop"></i>
        </div>
        
        <h2 class="fw-bold mb-2" style="color: var(--primary-dark)">Mulai Berjualan</h2>
        <p class="text-muted mb-4">Lengkapi data di bawah ini untuk membuka toko Anda di SpareHub.</p>

        @if(session('error'))
            <div class="alert alert-danger rounded-3 border-0">
                {{ session('error') }}
            </div>
        @endif

        <form action="{{ route('toko.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-3">
                <label class="form-label">Nama Toko</label>
                <input type="text" name="nama_toko" class="form-control" placeholder="Contoh: Bengkel Sparepart Jaya" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Deskripsi Singkat</label>
                <textarea name="deskripsi_toko" class="form-control" rows="3" placeholder="Ceritakan apa yang Anda jual..." required></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Lokasi / Kota</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 rounded-start-3">
                        <i class="bi bi-geo-alt text-muted"></i>
                    </span>
                    <input type="text" name="lokasi" class="form-control border-start-0 rounded-end-3" placeholder="Contoh: Jakarta Pusat" required>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">Logo Toko</label>
                <div class="upload-area" onclick="document.getElementById('logo-input').click()">
                    <i class="bi bi-cloud-arrow-up fs-2 text-muted"></i>
                    <p class="mb-0 text-muted mt-2">Klik untuk upload logo toko</p>
                    <input type="file" name="logo" id="logo-input" class="d-none" accept="image/*" required onchange="handlePreview(this)">
                    <img id="logo-preview" alt="Preview Logo">
                </div>
            </div>

            <button type="submit" class="btn-create" name="buatTokoBtn">
                Buka Toko Sekarang
            </button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function handlePreview(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('logo-preview');
                preview.src = e.target.result;
                preview.style.display = 'inline-block';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endpush
