@extends('layouts.main')

@section('title', 'Buka Toko - Medcom')

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
        <p class="text-muted mb-4">Lengkapi data di bawah ini untuk membuka toko Anda di Medcom.</p>

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

            {{-- Lokasi Toko dengan Wilayah --}}
            <div class="row g-2 mb-3">
                <div class="col-md-4">
                    <label class="form-label small mb-1">Provinsi</label>
                    <select class="form-select" id="tokoProvinsi" required>
                        <option value="">Pilih Provinsi</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small mb-1">Kota/Kabupaten</label>
                    <select class="form-select" id="tokoKota" required disabled>
                        <option value="">Pilih Kota</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small mb-1">Kecamatan</label>
                    <select class="form-select" id="tokoKecamatan" required disabled>
                        <option value="">Pilih Kecamatan</option>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Alamat Lengkap (Jalan/No)</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 rounded-start-3">
                        <i class="bi bi-geo-alt text-muted"></i>
                    </span>
                    <input type="text" name="lokasi" class="form-control border-start-0 rounded-end-3" placeholder="Contoh: Jl. Sudirman No. 10" required>
                </div>
            </div>
            
            <input type="hidden" name="kode_wilayah" id="tokoKodeWilayah">
            <input type="hidden" name="provinsi" id="hiddenProvinsi">
            <input type="hidden" name="kota" id="hiddenKota">
            <input type="hidden" name="kecamatan" id="hiddenKecamatan">

            <div class="mb-4">
                <label class="form-label">Logo Toko</label>
                <div class="upload-area" id="upload-container" onclick="document.getElementById('logo-input').click()">
                    <div id="upload-placeholder">
                        <i class="bi bi-cloud-arrow-up fs-2 text-muted"></i>
                        <p class="mb-0 text-muted mt-2">Klik untuk upload logo toko</p>
                    </div>
                    <div id="preview-wrapper" class="position-relative d-none">
                        <img id="logo-preview" alt="Preview Logo" class="img-thumbnail" style="max-width: 150px; border-radius: 16px;">
                        <button type="button" class="btn btn-danger btn-sm position-absolute top-0 start-100 translate-middle rounded-circle" 
                                onclick="removePreview(event)" title="Hapus Gambar">
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                    <input type="file" name="logo" id="logo-input" class="d-none" accept="image/*" required onchange="handlePreview(this)">
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
    document.addEventListener('DOMContentLoaded', function() {
        const tokoProv = document.getElementById('tokoProvinsi');
        const tokoKota = document.getElementById('tokoKota');
        const tokoKec = document.getElementById('tokoKecamatan');
        const tokoKode = document.getElementById('tokoKodeWilayah');

        async function fetchWilayah(url) {
            const res = await fetch(url);
            return await res.json();
        }

        async function loadProvinces() {
            const data = await fetchWilayah('/api/wilayah/provinsi');
            tokoProv.innerHTML = '<option value="">Pilih Provinsi</option>';
            data.forEach(p => {
                const opt = document.createElement('option');
                opt.value = p.kode;
                opt.textContent = p.nama;
                tokoProv.appendChild(opt);
            });
        }

        tokoProv.addEventListener('change', async function() {
            tokoKota.innerHTML = '<option value="">Pilih Kota</option>';
            tokoKec.innerHTML = '<option value="">Pilih Kecamatan</option>';
            tokoKec.disabled = true;
            if (this.value) {
                const data = await fetchWilayah(`/api/wilayah/kota/${this.value}`);
                data.forEach(c => {
                    const opt = document.createElement('option');
                    opt.value = c.kode;
                    opt.textContent = c.nama;
                    tokoKota.appendChild(opt);
                });
                tokoKota.disabled = false;
            } else {
                tokoKota.disabled = true;
            }
        });

        tokoKota.addEventListener('change', async function() {
            tokoKec.innerHTML = '<option value="">Pilih Kecamatan</option>';
            if (this.value) {
                const data = await fetchWilayah(`/api/wilayah/kecamatan/${this.value}`);
                data.forEach(d => {
                    const opt = document.createElement('option');
                    opt.value = d.kode;
                    opt.textContent = d.nama;
                    tokoKec.appendChild(opt);
                });
                tokoKec.disabled = false;
            } else {
                tokoKec.disabled = true;
            }
        });

        // Update hidden fields on selection change
        [tokoProv, tokoKota, tokoKec].forEach(el => {
            el.addEventListener('change', () => {
                tokoKode.value = tokoKec.value || tokoKota.value || tokoProv.value || "";
                
                // Set the text values for Laravel to store
                document.getElementById('hiddenProvinsi').value = tokoProv.options[tokoProv.selectedIndex]?.text || '';
                document.getElementById('hiddenKota').value = tokoKota.options[tokoKota.selectedIndex]?.text || '';
                document.getElementById('hiddenKecamatan').value = tokoKec.options[tokoKec.selectedIndex]?.text || '';
            });
        });

        loadProvinces();
    });

    function handlePreview(input) {
        const placeholder = document.getElementById('upload-placeholder');
        const wrapper = document.getElementById('preview-wrapper');
        const preview = document.getElementById('logo-preview');

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                placeholder.classList.add('d-none');
                wrapper.classList.remove('d-none');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function removePreview(event) {
        event.stopPropagation(); // Mencegah klik menembus ke container (input click)
        const input = document.getElementById('logo-input');
        const placeholder = document.getElementById('upload-placeholder');
        const wrapper = document.getElementById('preview-wrapper');
        const preview = document.getElementById('logo-preview');

        input.value = ""; // Reset file input
        preview.src = "";
        wrapper.classList.add('d-none');
        placeholder.classList.remove('d-none');
    }
</script>
@endpush
