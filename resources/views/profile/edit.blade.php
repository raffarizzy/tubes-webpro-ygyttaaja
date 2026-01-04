@extends('layouts.main')

@section('title', 'Profil Pengguna - SpareHub')

@section('content')
<div class="container my-5">

{{-- RIWAYAT PESANAN --}}
<div class="card shadow-sm mb-4 text-center">
    <div class="card-body">
        <h3 class="fw-bold text-sparehub">Riwayat Pesanan</h3>
        <p class="text-muted mb-3">Lihat daftar pesanan yang pernah Anda lakukan</p>
        <a href="{{ route('riwayat.pesanan') }}" class="btn bg-sparehub text-white px-4">
            Lihat Riwayat
        </a>
    </div>
</div>

{{-- FORM EDIT PROFIL --}}
<div class="card shadow-sm">
<div class="card-body p-4">

<h4 class="fw-bold mb-1">Profil Saya</h4>
<p class="text-muted mb-4">Kelola informasi profil Anda</p>

<div class="row">

    {{-- PROFILE PICTURE --}}
    <div class="col-12">
        <form
            method="POST"
            enctype="multipart/form-data"
            action="{{ route('profile.update.pfpPath') }}"
        >
            @csrf
            @method('PATCH')

            <div class="text-center mb-4">
                <div class="avatar-wrapper">
                    <img
                        style="width:150px"
                        src="{{ auth()->user()->pfpPath ?? 'https://i.ibb.co.com/ZRkqGfJ3/default-avatar-sparehubtize.png' }}"
                        alt="Avatar"
                        class="bg-secondary rounded-circle mb-4"
                    >
                </div>

                <input
                    type="file"
                    name="pfpPath"
                    id="pfpPathInput"
                    class="d-none"
                    accept="image/*"
                    onchange="this.form.submit()"
                >

                <button
                    type="button"
                    class="btn bg-sparehub text-white px-4"
                    onclick="document.getElementById('pfpPathInput').click()"
                >
                    Ubah Foto Profil
                </button>

                <hr>
            </div>
        </form>
    </div>

    {{-- FORM EDIT PROFIL --}}
    <form id="profileForm" method="POST" action="{{ route('profile.update') }}" class="row">
        @csrf
        @method('PATCH')

        {{-- USERNAME --}}
        <div class="col-md-6 mb-3">
            <label class="form-label">Username</label>
            <input
                type="text"
                name="name"
                class="form-control"
                value="{{ old('name', auth()->user()->name) }}"
            >
        </div>

        {{-- EMAIL --}}
        <div class="col-md-6 mb-3">
            <label class="form-label">Email</label>
            <input
                type="email"
                name="email"
                class="form-control"
                value="{{ old('email', auth()->user()->email) }}"
            >
        </div>

        {{-- PHONE --}}
        <div class="col-md-6 mb-3">
            <label class="form-label">Nomor Telepon</label>
            <input
                type="text"
                name="phone"
                class="form-control"
                value="{{ old('phone', $profile['phone'] ?? '') }}"
            >
        </div>

        {{-- TANGGAL LAHIR --}}
        <div class="col-md-6 mb-3">
            <label class="form-label">Tanggal Lahir</label>
            <input
                type="date"
                name="birthDate"
                class="form-control"
                value="{{ old('birthDate', $profile['birthDate'] ?? '') }}"
            >
        </div>

        {{-- GENDER --}}
        <div class="col-12 mb-3">
            <label class="form-label d-block">Jenis Kelamin</label>

            <div class="form-check form-check-inline">
                <input
                    class="form-check-input"
                    type="radio"
                    name="gender"
                    value="male"
                    {{ ($profile['gender'] ?? '') == 'male' ? 'checked' : '' }}
                >
                <label class="form-check-label">Laki-laki</label>
            </div>

            <div class="form-check form-check-inline">
                <input
                    class="form-check-input"
                    type="radio"
                    name="gender"
                    value="female"
                    {{ ($profile['gender'] ?? '') == 'female' ? 'checked' : '' }}
                >
                <label class="form-check-label">Perempuan</label>
            </div>
        </div>

        <hr class="my-4">

        {{-- UBAH PASSWORD --}}
        <h5 class="fw-bold mb-3">Ubah Password</h5>

        {{-- PASSWORD BARU --}}
        <div class="col-md-6 mb-3">
            <label class="form-label">Password Baru</label>
            <input type="password" name="password" class="form-control">
        </div>

        {{-- KONFIRMASI PASSWORD --}}
        <div class="col-md-6 mb-3">
            <label class="form-label">Konfirmasi Password</label>
            <input type="password" name="password_confirmation" class="form-control">
        </div>

        {{-- SUBMIT --}}
        <div class="col-12 text-end mt-4">
            <button class="btn text-white bg-sparehub px-4">
                Simpan Perubahan
            </button>
        </div>

    </form>

</div>


<hr class="my-4">

<h5 class="fw-bold mb-3">Ubah Password</h5>

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Password Baru</label>
        <input type="password" name="password" class="form-control">
    </div>

    <div class="col-md-6 mb-3">
        <label class="form-label">Konfirmasi Password</label>
        <input type="password" name="password_confirmation" class="form-control">
    </div>
</div>

<div class="text-end mt-4">
    <button class="btn text-white bg-sparehub px-4">
        Simpan Perubahan
    </button>
</div>

<button type="button" class="btn btn-outline-secondary me-2" onclick="testData()" hidden>
    Test Data (Console)
</button>

</form>
</div>
</div>

</div>

<script>
function testData() {
    const form = document.getElementById('profileForm');
    const formData = new FormData(form);

    for (const [key, value] of formData.entries()) {
        console.log(key, '=>', value);
    }
}
</script>
@endsection