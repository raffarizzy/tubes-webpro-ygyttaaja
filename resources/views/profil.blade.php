<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Akun Pengguna - SpareHub</title>
  <link rel="icon" href="img/iconSpareHub.png">
  <link rel="stylesheet" href="css/pengguna.css">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      margin: 0;
      background-color: #f9f9f9;
    }

    nav {
      background-color: white;
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 10px 50px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
      position: sticky;
      top: 0;
      z-index: 10;
    }

    nav ul {
      list-style: none;
      display: flex;
      gap: 20px;
      margin: 0;
      padding: 0;
      align-items: center;
    }

    nav a {
      text-decoration: none;
      color: #333;
      font-weight: 500;
      transition: color 0.2s;
    }

    nav a:hover {
      color: #007bff;
    }

    #profil {
      display: flex;
      align-items: center;
      gap: 8px;
    }

    #iconPengguna {
      width: 35px;
      height: 35px;
      border-radius: 50%;
    }

    /* ===== Konten Profil ===== */
    .akunContainer {
      max-width: 800px;
      margin: 80px auto;
      background: white;
      padding: 30px;
      border-radius: 15px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      display: flex;
      gap: 30px;
      align-items: center;
    }

    .akunContainer img {
      width: 140px;
      height: 140px;
      border-radius: 50%;
      object-fit: cover;
    }

    .infoUser {
      flex: 1;
    }

    .infoUser h2 {
      margin: 0;
      font-size: 1.6em;
      color: #333;
    }

    .infoUser p {
      margin: 6px 0;
      color: #666;
    }

    .btnGroup {
      display: flex;
      gap: 15px;
      margin-top: 20px;
    }

    .btn {
      background-color: #007bff;
      color: white;
      border: none;
      padding: 10px 18px;
      border-radius: 10px;
      cursor: pointer;
      font-weight: 500;
      transition: background-color 0.2s ease;
    }

    .btn:hover {
      background-color: #0056b3;
    }

    footer {
      text-align: center;
      padding: 15px;
      background: #f0f0f0;
      margin-top: 50px;
      color: #555;
    }
  </style>
</head>
<body>
  <nav>
    <a href="homepage.html" class="wrapperLogo">
      <img src="img/iconSpareHub.png" id="logo" alt="Logo SpareHub" width="45">
    </a>
    <ul>
      <li><a href="homepage.html">Beranda</a></li>
      <li><a href="keranjang.html">Keranjang</a></li>
      <li><a href="profil_toko.html">Toko Saya</a></li>
      <li>
        <div id="profil">
          <img src="img/iconPengguna.png" id="iconPengguna" alt="Icon Pengguna">
          <a href="pengguna.html" id="namaPengguna">Pengguna</a>
        </div>
      </li>
    </ul>
  </nav>

  <div class="akunContainer">
    <img id="userImage" src="img/iconPengguna.png" alt="Foto Pengguna">
    <div class="infoUser">
      <h2 id="userName">Nama Pengguna</h2>
      <p id="userEmail">pengguna@email.com</p>
      <p id="userAlamat">Alamat pengguna akan ditampilkan di sini</p>
      <div class="btnGroup">
        <button class="btn" onclick="goToEdit()">Edit Profil</button>
        <button class="btn" onclick="goToOrders()">Riwayat Pesanan</button>
      </div>
    </div>
  </div>

  <footer>
    <p>&copy; 2025 SpareHub. Semua hak dilindungi.</p>
  </footer>

  <script>
    // Ambil data user dari JSON
    fetch('./data/UserData.json')
      .then(res => res.json())
      .then(users => {
        const currentUserId = localStorage.getItem('currentUserId') || 1;
        const user = users.find(u => u.id == currentUserId);

        if (user) {
          document.getElementById('userImage').src = user.imagePath;
          document.getElementById('userName').textContent = user.nama;
          document.getElementById('userEmail').textContent = user.email;
          document.getElementById('userAlamat').textContent = user.alamat;
          document.getElementById('namaPengguna').textContent = user.nama;
        }
      });

    function goToEdit() {
      window.location.href = 'edit_profil.html';
    }

    function goToOrders() {
      window.location.href = 'riwayat_pesanan.html';
    }
  </script>
</body>
</html>