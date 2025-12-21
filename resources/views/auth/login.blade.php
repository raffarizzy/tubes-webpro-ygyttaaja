<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - SpareHub</title>
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
        <p class="text-muted small">Login ke akun Anda</p>
    </div>

    <!-- SESSION STATUS -->
    @if (session('status'))
        <div class="alert alert-info">
            {{ session('status') }}
        </div>
    @endif

    <!-- FORM LOGIN -->
    <form method="POST" action="{{ route('login') }}">
        @csrf

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
                    autofocus
                >
            </div>
            @error('email')
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
                    onclick="togglePassword()">
                    <i class="fa-solid fa-eye"></i>
                </button>
            </div>
            @error('password')
                <div class="invalid-feedback d-block">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <!-- REMEMBER ME -->
        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" name="remember" id="remember">
            <label class="form-check-label" for="remember">
                Remember me
            </label>
        </div>

        <button class="btn btn-primary w-100 mb-3">
            Login
        </button>
    </form>

    <!-- LINKS -->
    <div class="text-center small">
        <p>
            Belum punya akun?
            <a href="{{ route('register') }}">Daftar</a>
        </p>

        @if (Route::has('password.request'))
            <p>
                <a href="{{ route('password.request') }}">
                    Lupa password?
                </a>
            </p>
        @endif
    </div>
</div>

<script>
function togglePassword() {
    const input = document.getElementById('password');
    input.type = input.type === 'password' ? 'text' : 'password';
}
</script>

</body>
</html>