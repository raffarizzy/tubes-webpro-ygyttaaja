<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Lupa Password - Medcom</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="bg-light d-flex align-items-center justify-content-center min-vh-100">

<div class="card shadow-sm p-4" style="width: 400px;">
    <div class="text-center mb-4">
        <img src="https://i.ibb.co/VcGWcqFG/icon-Spare-Hub.png" alt="Logo" width="60" class="mb-2">
        <h4 class="fw-bold text-primary">Lupa Password?</h4>
        <p class="text-muted small">Masukkan email Anda untuk menerima link reset password.</p>
    </div>

    <!-- Session Status -->
    @if (session('status'))
        <div class="alert alert-success small mb-4">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <div class="input-group">
                <span class="input-group-text"><i class="fa-solid fa-envelope"></i></span>
                <input id="email" class="form-control @error('email') is-invalid @enderror" type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="nama@email.com">
            </div>
            @error('email')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-grid gap-2">
            <button type="submit" class="btn btn-primary">
                Kirim Link Reset
            </button>
            <a href="{{ route('login') }}" class="btn btn-link text-muted small">Kembali ke Login</a>
        </div>
    </form>
</div>

</body>
</html>
