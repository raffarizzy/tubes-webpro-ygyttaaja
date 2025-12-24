<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Register - SpareHub</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 -->
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
      rel="stylesheet"
    >

    <!-- Font Awesome -->
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
    >
</head>
<body class="bg-light d-flex align-items-center justify-content-center min-vh-100">

<div class="card shadow-sm p-4" style="width: 380px;">
    <div class="text-center mb-4">
        <img src="https://i.ibb.co.com/VcGWcqFG/icon-Spare-Hub.png"
             alt="Logo"
             width="70"
             class="mb-2">
        <h4 class="fw-bold text-primary">SpareHub</h4>
        <p class="text-muted small">Daftar akun baru</p>
    </div>

    <!-- FORM REGISTER -->
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- NAME -->
        <div class="mb-3">
            <label class="form-label">Nama</label>
            <div class="input-group">
                <span class="input-group-text">
                    <i class="fa-solid fa-user"></i>
                </span>
                <input
                    type="text"
                    name="name"
                    value="{{ old('name') }}"
                    class="form-control @error('name') is-invalid @enderror"
                    placeholder="Nama lengkap"
                    required
                    autofocus
                >
            </div>
            @error('name')
                <div class="invalid-feedback d-block">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <!-- EMAIL -->
        <div class="mb-3">
            <label class="form-label">Email</label>
            <div class="input-group">
                <span class="input-group-text">
                    <i class="fa-solid fa-envelope"></i>
                </span>
                <input
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    class="form-control @error('email') is-invalid @enderror"
                    placeholder="Email"
                    required
                >
            </div>
            @error('email')
                <div class="invalid-feedback d-block">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <!-- PHONE -->
        <div class="mb-3">
            <label class="form-label">Nomor HP</label>
            <div class="input-group">
                <span class="input-group-text">
                    <i class="fa-solid fa-phone"></i>
                </span>
                <input
                    type="tel"
                    name="phone"
                    value="{{ old('phone') }}"
                    class="form-control @error('phone') is-invalid @enderror"
                    placeholder="08xxxxxxxxxx"
                    required
                >
            </div>
            @error('phone')
                <div class="invalid-feedback d-block">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <!-- PASSWORD -->
        <div class="mb-3">
            <label class="form-label">Password</label>
            <div class="input-group">
                <span class="input-group-text">
                    <i class="fa-solid fa-lock"></i>
                </span>
                <input
                    type="password"
                    name="password"
                    id="password"
                    class="form-control @error('password') is-invalid @enderror"
                    placeholder="Password"
                    required
                >
                <button
                    type="button"
                    class="btn btn-outline-secondary"
                    onclick="togglePassword('password')">
                    <i class="fa-solid fa-eye" id="password-icon"></i>
                </button>
            </div>
            @error('password')
                <div class="invalid-feedback d-block">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <!-- CONFIRM PASSWORD -->
        <div class="mb-3">
            <label class="form-label">Konfirmasi Password</label>
            <div class="input-group">
                <span class="input-group-text">
                    <i class="fa-solid fa-lock"></i>
                </span>
                <input
                    type="password"
                    name="password_confirmation"
                    id="password_confirmation"
                    class="form-control @error('password_confirmation') is-invalid @enderror"
                    placeholder="Konfirmasi password"
                    required
                >
                <button
                    type="button"
                    class="btn btn-outline-secondary"
                    onclick="togglePassword('password_confirmation')">
                    <i class="fa-solid fa-eye" id="password_confirmation-icon"></i>
                </button>
            </div>
            @error('password_confirmation')
                <div class="invalid-feedback d-block">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <button class="btn btn-primary w-100 mb-3">
            Daftar
        </button>
    </form>

    <!-- LINKS -->
    <div class="text-center small">
        <p>
            Sudah punya akun?
            <a href="{{ route('login') }}">Login</a>
        </p>
    </div>
</div>

<script>
function togglePassword(fieldId) {
    const input = document.getElementById(fieldId);
    const icon = document.getElementById(fieldId + '-icon');

    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>

</body>
</html>