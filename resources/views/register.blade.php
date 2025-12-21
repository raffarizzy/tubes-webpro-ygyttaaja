<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Register - SpareHub</title>
    <link rel="icon" href="img/iconSpareHub.png" />
    <link rel="stylesheet" href="css/register.css" />
  </head>
  <body>
    <div class="register-container">
      <div class="register-card">
        <img src="img/iconSpareHub.png" alt="Logo" class="logo" />
        <h1>SpareHub</h1>
        <p class="subtitle">Buat akun baru</p>

        <form class="register-form">
          <div class="input-group">
            <label for="nama">Nama Lengkap</label>
            <div class="input-wrapper">
              <i class="fa-solid fa-user"></i>
              <input type="text" id="nama" placeholder="Nama Lengkap" required />
            </div>
          </div>

          <div class="input-group">
            <label for="email">Email</label>
            <div class="input-wrapper">
              <i class="fa-solid fa-envelope"></i>
              <input type="email" id="email" placeholder="Email" required />
            </div>
          </div>

          <div class="input-group">
            <label for="alamat">Alamat</label>
            <div class="input-wrapper">
              <i class="fa-solid fa-location-dot"></i>
              <input type="text" id="alamat" placeholder="Alamat" required />
            </div>
          </div>

          <div class="input-group">
            <label for="password">Password</label>
            <div class="input-wrapper password-wrapper">
              <i class="fa-solid fa-lock"></i>
              <input
                type="password"
                id="password"
                placeholder="Password"
                required
              />
              <button type="button" class="toggle-password" data-target="password">
                <i class="fa-solid fa-eye"></i>
              </button>
            </div>
          </div>

          <div class="input-group">
            <label for="confirm-password">Konfirmasi Password</label>
            <div class="input-wrapper password-wrapper">
              <i class="fa-solid fa-lock"></i>
              <input
                type="password"
                id="confirm-password"
                placeholder="Konfirmasi Password"
                required
              />
              <button type="button" class="toggle-password" data-target="confirm-password">
                <i class="fa-solid fa-eye"></i>
              </button>
            </div>
          </div>

          <button type="submit" class="btn-register">Daftar</button>
        </form>

        <p class="login-text">
          Sudah punya akun? <a href="login.html">Login</a>
        </p>
      </div>
    </div>

    <script src="js/register.js"></script>
  </body>
</html>