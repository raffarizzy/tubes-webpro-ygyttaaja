<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - SpareHub</title>
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

        .art-features {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 1rem;
            margin-bottom: 2.5rem;
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

        /* Testimonial quote card */
        .testimonial-card {
            position: relative;
            z-index: 1;
            background: rgba(255,255,255,0.07);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 14px;
            padding: 1.1rem 1.3rem;
            max-width: 360px;
        }

        .testimonial-quote {
            font-size: 0.88rem;
            color: rgba(255,255,255,0.85);
            line-height: 1.55;
            margin-bottom: 0.65rem;
            font-style: italic;
        }

        .testimonial-author {
            display: flex;
            align-items: center;
            gap: 0.6rem;
        }

        .testimonial-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--gold);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.95rem;
            color: var(--navy-3);
            font-weight: 700;
        }

        .testimonial-name {
            font-size: 0.8rem;
            font-weight: 600;
            color: #fff;
        }

        .testimonial-city {
            font-size: 0.75rem;
            color: rgba(255,255,255,0.55);
        }

        .stars {
            color: var(--gold);
            font-size: 0.75rem;
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
            max-width: 440px;
        }

        .back-home {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.82rem;
            font-weight: 500;
            color: var(--muted);
            text-decoration: none;
            margin-bottom: 1.5rem;
            transition: color 0.2s;
        }

        .back-home:hover { color: var(--navy); }

        .form-title {
            font-family: 'Poppins', sans-serif;
            font-weight: 700;
            font-size: 1.55rem;
            color: var(--ink);
            margin-bottom: 0.3rem;
        }

        .form-subtitle {
            font-size: 0.875rem;
            color: var(--muted);
            margin-bottom: 1.5rem;
        }

        /* Input groups */
        .field-group {
            margin-bottom: 1rem;
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

        /* Name row split */
        .name-row {
            display: flex;
            gap: 0.75rem;
        }

        .name-row .field-group {
            flex: 1;
        }

        /* Password strength bar */
        .strength-bar-wrap {
            margin-top: 0.4rem;
            display: flex;
            gap: 4px;
        }

        .strength-seg {
            flex: 1;
            height: 4px;
            border-radius: 4px;
            background: var(--line);
            transition: background 0.3s;
        }

        .strength-seg.weak   { background: #dc3545; }
        .strength-seg.medium { background: #fd7e14; }
        .strength-seg.strong { background: var(--ok); }

        .strength-label {
            font-size: 0.72rem;
            color: var(--hint);
            margin-top: 0.25rem;
        }

        /* Terms checkbox */
        .terms-row {
            display: flex;
            align-items: flex-start;
            gap: 0.5rem;
            margin-bottom: 1.25rem;
        }

        .terms-row input[type="checkbox"] {
            width: 15px;
            height: 15px;
            accent-color: var(--navy);
            cursor: pointer;
            flex-shrink: 0;
            margin-top: 2px;
        }

        .terms-row label {
            font-size: 0.8rem;
            color: var(--body);
            cursor: pointer;
            line-height: 1.45;
        }

        .terms-row a {
            color: var(--blue);
            text-decoration: none;
            font-weight: 500;
        }

        .terms-row a:hover { text-decoration: underline; }

        /* Register button */
        .btn-register {
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

        .btn-register:hover { opacity: 0.9; transform: translateY(-1px); }
        .btn-register:active { transform: translateY(0); }

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
            margin-bottom: 1.5rem;
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
            .name-row { flex-direction: column; gap: 0; }
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
            Gabung &amp; mulai<br>
            belanja <em>hemat.</em>
        </h1>

        <ul class="art-features">
            <li>
                <span class="feat-icon"><i class="bi bi-ticket-perforated-fill"></i></span>
                Voucher eksklusif untuk member baru
            </li>
            <li>
                <span class="feat-icon"><i class="bi bi-heart-fill"></i></span>
                Simpan wishlist &amp; pantau harga favoritmu
            </li>
            <li>
                <span class="feat-icon"><i class="bi bi-bell-fill"></i></span>
                Notifikasi stok &amp; promo langsung ke kamu
            </li>
        </ul>

        <!-- Testimonial quote card -->
        <div class="testimonial-card">
            <p class="testimonial-quote">
                "Nyari kampas rem motor udah gak perlu keliling bengkel. SpareHub kirim cepet dan harganya jelas!"
            </p>
            <div class="testimonial-author">
                <div class="testimonial-avatar">R</div>
                <div>
                    <div class="testimonial-name">Rizky A.</div>
                    <div class="testimonial-city">Bandung &nbsp;<span class="stars">&#9733;&#9733;&#9733;&#9733;&#9733;</span></div>
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

            <h2 class="form-title">Buat akun baru</h2>
            <p class="form-subtitle">Gratis &amp; selesai dalam 1 menit</p>

            <form method="POST" action="{{ route('register') }}">
                @csrf

                {{-- SPLIT NAME ROW --}}
                <div class="name-row">
                    <div class="field-group">
                        <label for="first_name">Nama Depan</label>
                        <div class="input-wrap">
                            <i class="bi bi-person i-icon"></i>
                            <input
                                type="text"
                                id="first_name"
                                name="first_name"
                                value="{{ old('first_name') }}"
                                placeholder="Budi"
                                required
                                autofocus
                                class="{{ $errors->has('name') ? 'is-invalid' : '' }}"
                            >
                        </div>
                    </div>
                    <div class="field-group">
                        <label for="last_name">Nama Belakang</label>
                        <div class="input-wrap">
                            <i class="bi bi-person i-icon"></i>
                            <input
                                type="text"
                                id="last_name"
                                name="last_name"
                                value="{{ old('last_name') }}"
                                placeholder="Santoso"
                            >
                        </div>
                    </div>
                </div>
                {{-- hidden combined name field --}}
                <input type="hidden" name="name" id="name_combined">
                @error('name')
                    <div class="field-error" style="margin-top:-0.6rem; margin-bottom:0.75rem;">{{ $message }}</div>
                @enderror

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
                            class="{{ $errors->has('email') ? 'is-invalid' : '' }}"
                        >
                    </div>
                    @error('email')
                        <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>

                {{-- PHONE (alamat field kept as phone per backend) --}}
                <div class="field-group">
                    <label for="phone">Nomor HP</label>
                    <div class="input-wrap">
                        <i class="bi bi-telephone i-icon"></i>
                        <input
                            type="tel"
                            id="phone"
                            name="phone"
                            value="{{ old('phone') }}"
                            placeholder="08xxxxxxxxxx"
                            required
                            class="{{ $errors->has('phone') ? 'is-invalid' : '' }}"
                        >
                    </div>
                    @error('phone')
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
                            placeholder="Min. 8 karakter"
                            required
                            oninput="checkStrength(this.value)"
                            class="{{ $errors->has('password') ? 'is-invalid' : '' }}"
                        >
                        <button type="button" class="toggle-eye" onclick="toggleEye('password', this)" aria-label="Tampilkan password">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                    <div class="strength-bar-wrap">
                        <div class="strength-seg" id="seg1"></div>
                        <div class="strength-seg" id="seg2"></div>
                        <div class="strength-seg" id="seg3"></div>
                        <div class="strength-seg" id="seg4"></div>
                    </div>
                    <div class="strength-label" id="strength-label"></div>
                    @error('password')
                        <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>

                {{-- CONFIRM PASSWORD --}}
                <div class="field-group">
                    <label for="password_confirmation">Konfirmasi Password</label>
                    <div class="input-wrap">
                        <i class="bi bi-lock-fill i-icon"></i>
                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            placeholder="Ulangi password"
                            required
                            class="{{ $errors->has('password_confirmation') ? 'is-invalid' : '' }}"
                        >
                        <button type="button" class="toggle-eye" onclick="toggleEye('password_confirmation', this)" aria-label="Tampilkan password konfirmasi">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                    @error('password_confirmation')
                        <div class="field-error">{{ $message }}</div>
                    @enderror
                </div>

                {{-- TERMS --}}
                <div class="terms-row">
                    <input type="checkbox" id="terms" name="terms" required>
                    <label for="terms">
                        Saya menyetujui <a href="#">Syarat &amp; Ketentuan</a> serta <a href="#">Kebijakan Privasi</a> SpareHub.
                    </label>
                </div>

                <button type="submit" class="btn-register">Daftar Sekarang</button>
            </form>

            <div class="divider"><span>ATAU DAFTAR DENGAN</span></div>

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
                Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a>
            </p>

        </div>
    </div>

</div>

<script>
// Combine first + last name into hidden 'name' field on submit
document.querySelector('form').addEventListener('submit', function() {
    const first = document.getElementById('first_name').value.trim();
    const last  = document.getElementById('last_name').value.trim();
    document.getElementById('name_combined').value = last ? first + ' ' + last : first;
});

// Pre-fill split fields if old('name') has value (on validation error)
@if(old('name'))
(function() {
    const full = @json(old('name'));
    const parts = full.split(' ');
    document.getElementById('first_name').value = parts[0] || '';
    document.getElementById('last_name').value  = parts.slice(1).join(' ') || '';
})();
@endif

function toggleEye(fieldId, btn) {
    const input = document.getElementById(fieldId);
    const icon  = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('bi-eye', 'bi-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('bi-eye-slash', 'bi-eye');
    }
}

function checkStrength(val) {
    const segs   = [document.getElementById('seg1'), document.getElementById('seg2'),
                    document.getElementById('seg3'), document.getElementById('seg4')];
    const label  = document.getElementById('strength-label');
    let score = 0;
    if (val.length >= 8)              score++;
    if (/[A-Z]/.test(val))            score++;
    if (/[0-9]/.test(val))            score++;
    if (/[^A-Za-z0-9]/.test(val))     score++;

    const cls   = score <= 1 ? 'weak' : score <= 2 ? 'medium' : 'strong';
    const txt   = score <= 1 ? 'Lemah' : score <= 2 ? 'Sedang' : 'Kuat';
    const color = score <= 1 ? '#dc3545' : score <= 2 ? '#fd7e14' : '#1E8E3E';

    segs.forEach((s, i) => {
        s.className = 'strength-seg';
        if (i < score) s.classList.add(cls);
    });
    label.textContent = val.length ? 'Kekuatan password: ' + txt : '';
    label.style.color = color;
}
</script>

</body>
</html>
