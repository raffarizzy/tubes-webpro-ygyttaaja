@extends('layouts.main')

@section('title', 'Profil Toko - SpareHub')

@push('bootstrap')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    .preview-image {
        max-width: 200px;
        max-height: 200px;
        margin-top: 10px;
        border-radius: 8px;
    }
</style>
@endpush

<meta name="csrf-token" content="{{ csrf_token() }}">

@section('content')
<div class="container py-5">

    {{-- ================= HEADER TOKO ================= --}}
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row align-items-center">

                <div class="col-md-2 text-center">
                    <img
                        src="{{ $toko->logo_path ? asset('storage/'.$toko->logo_path) : asset('img/iconPengguna.png') }}"
                        class="rounded-circle border"
                        style="width:120px;height:120px;object-fit:cover;"
                        id="header-logo">
                </div>

                <div class="col-md-8">
                    <h3 id="header-nama-toko">{{ $toko->nama_toko }}</h3>
                    <p id="header-deskripsi">{{ $toko->deskripsi_toko }}</p>
                    <p><strong>Lokasi:</strong> <span id="header-lokasi">{{ $toko->lokasi }}</span></p>
                </div>

                <div class="col-md-2 text-end">
                    <button class="btn btn-outline-primary" onclick="openEditTokoModal()">
                        ✏️ Edit Toko
                    </button>
                </div>

            </div>
        </div>
    </div>

    {{-- ================= PRODUK ================= --}}
    <div class="card shadow-sm">
        <div class="card-body">

            <div class="d-flex justify-content-between mb-3">
                <h4>Produk</h4>
                <button class="btn btn-primary" onclick="openTambahModal()">+ Tambah Produk</button>
            </div>

            @if($toko->products->count() == 0)
                <p class="text-muted">Belum ada produk.</p>
            @endif

            <div class="row" id="produk-list">
                @foreach($toko->products as $p)
                <div class="col-md-4 mb-3" id="produk-{{ $p->id }}">
                    <div class="card h-100">
                        <img src="{{ asset('storage/'.$p->imagePath) }}"
                             class="card-img-top"
                             style="height:200px;object-fit:cover;">
                        <div class="card-body">
                            <h5>{{ $p->nama }}</h5>
                            <p>Rp {{ number_format($p->harga,0,',','.') }}</p>
                            <p>Stok: {{ $p->stok }}</p>

                            <div class="d-flex gap-2">
                                <button class="btn btn-warning btn-sm"
                                    onclick="openEditModal(
                                        {{ $p->id }},
                                        '{{ $p->nama }}',
                                        {{ $p->harga }},
                                        {{ $p->stok }},
                                        '{{ $p->imagePath }}'
                                    )">
                                    Edit
                                </button>
                                <button class="btn btn-danger btn-sm"
                                    onclick="openHapusModal({{ $p->id }})">
                                    Hapus
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

        </div>
    </div>
</div>

{{-- ================= MODAL EDIT TOKO ================= --}}
{{-- ================= MODAL EDIT TOKO ================= --}}
<div class="modal fade" id="modalEditToko" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Edit Toko</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            {{-- 🔥 FORM EDIT TOKO (DI SINI TEMPATNYA) --}}
            <div class="modal-body">
                <form id="formEditToko" enctype="multipart/form-data">

                        <div class="mb-3">
                            <label class="form-label">Nama Toko</label>
                            <input
                                type="text"
                                name="nama_toko"
                                id="editTokoNama"
                                class="form-control"
                                value="{{ $toko->nama_toko }}"
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea
                                name="deskripsi_toko"
                                id="editTokoDeskripsi"
                                class="form-control"
                                required
                            >{{ $toko->deskripsi_toko }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Lokasi</label>
                            <input
                                type="text"
                                name="lokasi"
                                id="editTokoLokasi"
                                class="form-control"
                                value="{{ $toko->lokasi }}"
                                required
                            >
                        </div>

                        {{-- ✅ INI DIA INPUT FILE-NYA --}}
                        <div class="mb-3">
                            <label class="form-label">Logo Toko</label>
                            <input
                                type="file"
                                id="editTokoLogo"
                                name="logo"
                                class="form-control"
                                accept="image/*"
                            >
                        </div>

                    </form>
                </div>

                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal"
                    >
                        Batal
                    </button>

                    <button
                        type="button"
                        class="btn btn-primary"
                        onclick="updateToko()"
                    >
                        Simpan
                    </button>
                </div>

            </div>
        </div>
    </div>


{{-- ================= MODAL TAMBAH PRODUK ================= --}}
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Tambah Produk</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                {{-- 🔥 FORM TAMBAH PRODUK --}}
                <form id="formTambah" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-2">
                        <label>Nama Produk</label>
                        <input type="text" name="nama" class="form-control" required>
                    </div>

                    <div class="mb-2">
                        <label>Kategori</label>
                        <select name="category_id" class="form-control" required>
                            <option value="1">Sparepart Mesin</option>
                            <option value="2">Sparepart Body</option>
                        </select>
                    </div>

                    <div class="mb-2">
                        <label>Harga</label>
                        <input type="number" name="harga" class="form-control" required>
                    </div>

                    <div class="mb-2">
                        <label>Stok</label>
                        <input type="number" name="stok" class="form-control" required>
                    </div>

                    <div class="mb-2">
                        <label>Deskripsi</label>
                        <textarea name="deskripsi" class="form-control" required></textarea>
                    </div>

                    <div class="mb-2">
                        <label>Gambar Produk</label>
                        <input type="file" name="gambar" class="form-control" accept="image/*" required>
                    </div>

                </form>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button class="btn btn-primary" onclick="simpanProduk()">Simpan</button>
            </div>

        </div>
    </div>
</div>



{{-- ================= MODAL EDIT PRODUK ================= --}}
<div class="modal fade" id="modalEdit">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5>Edit Produk</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="editId">
                <input id="editNama" class="form-control mb-2">
                <input id="editHarga" class="form-control mb-2">
                <input id="editStok" class="form-control mb-2">
                <input type="file" id="editGambar" class="form-control">
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" onclick="updateProduk()">Update</button>
            </div>
        </div>
    </div>
</div>

{{-- ================= MODAL HAPUS ================= --}}
<div class="modal fade" id="modalHapus">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <input type="hidden" id="hapusId">
                <p>Yakin hapus produk?</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-danger" onclick="hapusProduk()">Hapus</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<meta name="csrf-token" content="{{ csrf_token() }}">
<script>
    const TOKO_ID = {{ $toko->id }};
    const STORE_PRODUCT_URL = "{{ route('product.store') }}";
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/mengelolaProdukCRUD.js') }}"></script>
@endpush
