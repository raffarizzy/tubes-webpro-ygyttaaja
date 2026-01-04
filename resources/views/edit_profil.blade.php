<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Pengguna - SpareHub</title>
    <link rel="icon" href="https://i.ibb.co.com/VcGWcqFG/icon-Spare-Hub.png">

    <!-- BOOTSTRAP -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="css/navbar-unified.css" />
    <!-- <link rel="stylesheet" href="css/pengguna.css"> -->
</head>
<body class="bg-light">

    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-dark" style="background-color:#122C4F;">
        <div class="container">
            <a href="login.html" class="navbar-brand">
                <img src="https://i.ibb.co.com/VcGWcqFG/icon-Spare-Hub.png" id="logo" alt="Logo SpareHub" width="40">
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMenu">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarMenu">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a href="{{route('home.index')}}" class="nav-link">Beranda</a></li>
                    <li class="nav-item"><a href="keranjang.blade.php" class="nav-link">Keranjang</a></li>
                    <li class="nav-item"><a href="profil_toko.html" class="nav-link">Toko Saya</a></li>
                    <li class="nav-item d-flex align-items-center ms-3">
                        <img src="https://i.ibb.co.com/CpnrrVtb/icon-Pengguna.png" width="28" class="me-2">
                        <a href="edit_profil.html" class="nav-link">Pengguna</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- CONTENT -->
    <div class="container my-5">

        <!-- Riwayat Pesanan -->
        <div class="card shadow-sm mb-4">
            <div class="card-body text-center">
                <h3 class="fw-bold text-primary">Riwayat Pesanan</h3>
                <p class="text-muted">Lihat daftar pesanan yang pernah Anda lakukan</p>
                <a href="riwayat_pesanan.html" class="btn btn-primary px-4">Lihat Riwayat</a>
            </div>
        </div>

        <!-- Form Profil -->
        <div class="card shadow-sm">
            <div class="card-body">
                <p>testing
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <h3 class="fw-bold">Profil Saya</h3>
                <p class="text-muted">Kelola informasi profil Anda untuk mengontrol akun</p>
                <hr>

                <form>
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input id="username" name="username" type="text" class="form-control" placeholder="Masukan username">
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input id="email" name="email" type="email" class="form-control" placeholder="Masukan email">
                    </div>

                    <div class="mb-3">
                        <label for="nomorTelp" class="form-label">Nomor Telepon</label>
                        <input id="nomorTelp" name="nomorTelp" type="number" class="form-control" placeholder="Masukan nomor">
                    </div>

                    <div class="mb-3">
                        <label for="alamat" class="form-label">Alamat</label>
                        <input id="alamat" name="alamat" type="text" class="form-control" placeholder="Masukan alamat">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Jenis Kelamin</label>
                        <div class="form-check">
                            <input type="radio" name="kelamin" value="Laki-Laki" class="form-check-input" id="lk">
                            <label for="lk" class="form-check-label">Laki - Laki</label>
                        </div>

                        <div class="form-check">
                            <input type="radio" name="kelamin" value="Perempuan" class="form-check-input" id="pr">
                            <label for="pr" class="form-check-label">Perempuan</label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </form>
            </div>
        </div>

    </div>

    <footer class="text-white text-center py-3" style="background-color:#122C4F;">
        <p class="m-0">&copy; 2025 SpareHub. Semua hak dilindungi.</p>
    </footer>

    <script src="../js/pengguna.js"></script>
    <script src="../js/navbar-manager.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>