<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SpareHub</title>
    <link rel="icon" href="https://i.ibb.co.com/VcGWcqFG/icon-Spare-Hub.png">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700;800&family=Roboto:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        :root {
            --navy: #122C4F;
            --navy-2: #1a3a66;
            --navy-3: #0D1F38;
            --blue: #0066CC;
            --gold: #FFC107;
            --cream: #F4E9DC;
            --cream-2: #EFE2D1;
            --ink: #202124;
            --body: #3C4043;
            --muted: #5F6368;
            --hint: #999;
            --line: #E5DFD3;
            --ok: #1E8E3E;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        html, body {
            height: 100%;
            font-family: 'Roboto', sans-serif;
            color: var(--body);
        }

        .split-layout {
            display: flex;
            min-height: 100vh;
        }

        /* ===== LEFT ART PANEL ===== */
        .art-panel {
            flex: 0 0 50%;
            background: linear-gradient(145deg, var(--navy-3) 0%, var(--navy) 50%, var(--navy-2) 100%);
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: flex-start;
            padding: 3rem 3.5rem;
        }

        /* Decorative radial glows */
        .art-panel::before {
            content: '';
            position: absolute;
            top: -120px;
            right: -120px;
            width: 420px;
            height: 420px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255,193,7,0.18) 0%, transparent 70%);
            pointer-events: none;
        }

        .art-panel::after {
            content: '';
            position: absolute;
            bottom: -100px;
            left: -80px;
            width: 360px;
            height: 360px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(0,102,204,0.22) 0%, transparent 70%);
            pointer-events: none;
        }

        .glow-mid {
            position: absolute;
            top: 50%;
            right: 60px;
            transform: translateY(-50%);
            width: 260px;
            height: 260px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255,193,7,0.10) 0%, transparent 65%);
            pointer-events: none;
        }

        /* Brand */
        .art-brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 2.5rem;
            position: relative;
            z-index: 1;
        }

        .art-brand img {
            width: 44px;
            height: 44px;
            border-radius: 10px;
            object-fit: contain;
            background: rgba(255,255,255,0.08);
            padding: 4px;
        }

        .art-brand-name {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            font-size: 1.4rem;
            color: #fff;
            letter-spacing: 0.02em;
        }

        /* Headline */
        .art-headline {
            font-family: 'Poppins', sans-serif;
            font-weight: 800;
            font-size: 2rem;
            line-height: 1.25;
            color: #fff;
            margin-bottom: 2rem;
            position: relative;
            z-index: 1;
            max-width: 380px;
        }

        .art-headline em {
            font-style: normal;
            color: var(--gold);
        }

        /* Feature bullets */
        .art-features {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 1rem;
            margin-bottom: 3rem;
            position: relative;
            z-index: 1;
        }

        .art-features li {
            display: flex;
            align-items: center;
            gap: 0.9rem;
            color: rgba(255,255,255,0.88);
            font-size: 0.9rem;
            font-weight: 500;
        }

        .feat-icon {
            flex-shrink: 0;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: var(--gold);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            color: var(--navy-3);
        }

        /* Circular art element */
        .art-circle-wrap {
            position: absolute;
            bottom: 2.5rem;
            right: 2.5rem;
            z-index: 1;
        }

        .art-circle {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 2px solid rgba(255,193,7,0.35);
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .art-circle::before {
            content: '';
            position: absolute;
            inset: 8px;
            border-radius: 50%;
            border: 1.5px solid rgba(255,193,7,0.20);
        }

        .art-circle-inner {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: rgba(255,193,7,0.12);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .art-circle-inner i {
            font-size: 2rem;
            color: var(--gold);
        }

        /* ===== RIGHT FORM PANEL ===== */
        .form-panel {
            flex: 0 0 50%;
            background: var(--cream);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 2.5rem 3rem;
            overflow-y: auto;
        }

        .form-inner {
            width: 100%;
            max-width: 420px;
        }

        .back-home {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.82rem;
            font-weight: 500;
            color: var(--muted);
            text-decoration: none;
            margin-bottom: 2rem;
            transition: color 0.2s;
        }

        .back-home:hover { color: var(--navy); }

        .form-title {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            font-size: 1.65rem;
            color: var(--ink);
            margin-bottom: 0.3rem;
        }

        .form-subtitle {
            font-size: 0.875rem;
            color: var(--muted);
            margin-bottom: 1.75rem;
        }

        /* Alert */
        .alert-session {
            background: #e8f5e9;
            border: 1px solid #a5d6a7;
            color: var(--ok);
            border-radius: 8px;
            padding: 0.65rem 1rem;
            font-size: 0.85rem;
            margin-bottom: 1.25rem;
        }

        /* Input groups */
        .field-group {
            margin-bottom: 1.1rem;
        }

        .field-group label {
            display: block;
            font-size: 0.82rem;
            font-weight: 600;
            color: var(--ink);
            margin-bottom: 0.4rem;
            letter-spacing: 0.01em;
        }

        .input-wrap {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-wrap .i-icon {
            position: absolute;
            left: 0.85rem;
            color: var(--hint);
            font-size: 1rem;
            pointer-events: none;
            z-index: 1;
        }

        .input-wrap input {
            width: 100%;
            padding: 0.65rem 0.85rem 0.65rem 2.5rem;
            background: #fff;
            border: 1.5px solid var(--line);
            border-radius: 10px;
            font-size: 0.9rem;
            color: var(--ink);
            font-family: 'Roboto', sans-serif;
            transition: border-color 0.2s, box-shadow 0.2s;
            outline: none;
        }

        .input-wrap input:focus {
            border-color: var(--navy);
            box-shadow: 0 0 0 3px rgba(18,44,79,0.10);
        }

        .input-wrap input.is-invalid {
            border-color: #dc3545;
        }

        .input-wrap .toggle-eye {
            position: absolute;
            right: 0.75rem;
            background: none;
            border: none;
            cursor: pointer;
            color: var(--hint);
            font-size: 1.05rem;
            padding: 0;
            display: flex;
            align-items: center;
        }

        .input-wrap .toggle-eye:hover { color: var(--navy); }

        .field-error {
            font-size: 0.78rem;
            color: #dc3545;
            margin-top: 0.3rem;
        }

        /* Remember + forgot */
        .row-remember {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.4rem;
        }

        .check-label {
            display: flex;
            align-items: center;
            gap: 0.45rem;
            font-size: 0.83rem;
            color: var(--body);
            cursor: pointer;
        }

        .check-label input[type="checkbox"] {
            width: 15px;
            height: 15px;
            accent-color: var(--navy);
            cursor: pointer;
        }

        .forgot-link {
            font-size: 0.83rem;
            color: var(--blue);
            text-decoration: none;
            font-weight: 500;
        }

        .forgot-link:hover { text-decoration: underline; }

        /* Login button */
        .btn-login {
            width: 100%;
            padding: 0.75rem;
            background: linear-gradient(135deg, var(--navy) 0%, var(--navy-2) 100%);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
            font-size: 0.97rem;
            letter-spacing: 0.03em;
            cursor: pointer;
            transition: opacity 0.2s, transform 0.15s;
            margin-bottom: 1.25rem;
        }

        .btn-login:hover { opacity: 0.9; transform: translateY(-1px); }
        .btn-login:active { transform: translateY(0); }

        /* Divider */
        .divider {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1.1rem;
        }

        .divider::before, .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--line);
        }

        .divider span {
            font-size: 0.72rem;
            font-weight: 600;
            color: var(--hint);
            letter-spacing: 0.08em;
            white-space: nowrap;
        }

        /* Social buttons */
        .social-row {
            display: flex;
            gap: 0.75rem;
            margin-bottom: 1.75rem;
        }

        .btn-social {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.6rem 0.5rem;
            background: #fff;
            border: 1.5px solid var(--line);
            border-radius: 10px;
            font-size: 0.83rem;
            font-weight: 500;
            color: var(--ink);
            cursor: pointer;
            transition: border-color 0.2s, box-shadow 0.2s;
            text-decoration: none;
        }

        .btn-social:hover {
            border-color: #bbb;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            color: var(--ink);
        }

        .btn-social img {
            width: 18px;
            height: 18px;
        }

        /* Footer */
        .form-footer {
            text-align: center;
            font-size: 0.85rem;
            color: var(--muted);
        }

        .form-footer a {
            color: var(--navy);
            font-weight: 600;
            text-decoration: none;
        }

        .form-footer a:hover { text-decoration: underline; }

        /* Responsive */
        @media (max-width: 768px) {
            .art-panel { display: none; }
            .form-panel { flex: 0 0 100%; padding: 2rem 1.5rem; }
        }
    </style>
</head>
<body>

<div class="split-layout">

    <!-- ===== LEFT ART PANEL ===== -->
    <div class="art-panel">
        <div class="glow-mid"></div>

        <div class="art-brand">
            <img src="https://i.ibb.co.com/VcGWcqFG/icon-Spare-Hub.png" alt="SpareHub Logo">
            <span class="art-brand-name">SpareHub</span>
        </div>

        <h1 class="art-headline">
            Belanja sparepart<br>
            tanpa <em>ribet.</em>
        </h1>

        <ul class="art-features">
            <li>
                <span class="feat-icon"><i class="bi bi-shield-check-fill"></i></span>
                Produk 100% original &amp; terverifikasi
            </li>
            <li>
                <span class="feat-icon"><i class="bi bi-truck"></i></span>
                Pengiriman cepat ke seluruh Indonesia
            </li>
            <li>
                <span class="feat-icon"><i class="bi bi-arrow-return-left"></i></span>
                Garansi retur mudah &amp; tanpa ribet
            </li>
        </ul>

        <!-- Circular art element -->
        <div class="art-circle-wrap">
            <div class="art-circle">
                <div class="art-circle-inner">
                    <i class="bi bi-gear-fill"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== RIGHT FORM PANEL ===== -->
    <div class="form-panel">
        <div class="form-inner">

            <a href="{{ route('home') }}" class="back-home">
                <i class="bi bi-arrow-left"></i> Kembali ke Beranda
            </a>

            <h2 class="form-title">Masuk ke akun</h2>
            <p class="form-subtitle">Selamat datang kembali di SpareHub</p>

            {{-- Session Status --}}
            @if (session('status'))
                <div class="alert-session">{{ session('status') }}</div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                {{-- EMAIL --}}
                <div class="field-group">
                    <label for="email">Alamat Email</label>
                    <div class="input-wrap">
                        <i class="bi bi-envelope i-icon"></i>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email') }}"
                            placeholder="contoh@email.com"
                            required
                            autofocus
                            class="{{ $errors->has('email') ? 'is-invalid' : '' }}"
                        >
                    </div>
                    @error('email')
                        <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>

                {{-- PASSWORD --}}
                <div class="field-group">
                    <label for="password">Password</label>
                    <div class="input-wrap">
                        <i class="bi bi-lock i-icon"></i>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Masukkan password"
                            required
                            class="{{ $errors->has('password') ? 'is-invalid' : '' }}"
                        >
                        <button type="button" class="toggle-eye" onclick="toggleEye('password', this)" aria-label="Tampilkan password">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                    @error('password')
                        <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>

                {{-- REMEMBER + FORGOT --}}
                <div class="row-remember">
                    <label class="check-label">
                        <input type="checkbox" name="remember" id="remember">
                        Ingat saya
                    </label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="forgot-link">Lupa password?</a>
                    @endif
                </div>

                <button type="submit" class="btn-login">Masuk Sekarang</button>
            </form>

            <div class="divider"><span>ATAU MASUK DENGAN</span></div>

            <div class="social-row">
                <a href="#" class="btn-social" onclick="return false;">
                    <img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google">
                    Google
                </a>
                <a href="#" class="btn-social" onclick="return false;">
                    <img src="https://www.svgrepo.com/show/448224/facebook.svg" alt="Facebook">
                    Facebook
                </a>
            </div>

            <p class="form-footer">
                Belum punya akun? <a href="{{ route('register') }}">Daftar gratis</a>
            </p>

        </div>
    </div>

</div>

<script>
function toggleEye(fieldId, btn) {
    const input = document.getElementById(fieldId);
    const icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('bi-eye', 'bi-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('bi-eye-slash', 'bi-eye');
    }
}
</script>

</body>
</html>