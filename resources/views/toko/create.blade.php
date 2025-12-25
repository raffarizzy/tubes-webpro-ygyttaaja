@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Buat Toko</h3>

    <form action="/toko" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label>Nama Toko</label>
            <input type="text" name="nama_toko" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Deskripsi</label>
            <textarea name="deskripsi_toko" class="form-control" required></textarea>
        </div>

        <div class="mb-3">
            <label>Lokasi</label>
            <input type="text" name="lokasi" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Logo Toko</label>
            <input type="file" name="logo" class="form-control" required>
        </div>

        <button class="btn btn-primary">Buat Toko</button>
    </form>
</div>
@endsection
