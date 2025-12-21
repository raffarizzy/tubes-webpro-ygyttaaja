<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rating & Ulasan Produk</title>
    <link rel="stylesheet" href="css/navbar.css">
    <link rel="stylesheet" href="css/rating.css">
</head>

<body>
    <nav>
        <img src="img/iconSpareHub.png" id="logo" alt="Logo SpareHub" />
        <ul>
            <li><a href="homepage.html">Beranda</a></li>
            <li><a href="keranjang.html" class="active">Keranjang</a></li>
            <li><a href="profil_toko.html">Toko Saya</a></li>
            <li>
                <div id="profil">
                    <img src="img/iconPengguna.png" id="iconPengguna" alt="Ikon Pengguna" />
                    <a href="pengguna.html">Pengguna</a>
                </div>
            </li>
        </ul>
    </nav>
    <h1>Rating & Ulasan Produk</h1>

    <div id="ratingContainer" class="rating-container"></div>

    <form id="ratingForm">
        <h2>Tambah Rating Baru</h2>
        <label>Pilih Produk:</label>
        <select id="produkSelect" required></select>

        <label>Rating (1–5):</label>
        <select id="ratingValue" required>
            <option value="">Pilih Rating</option>
            <option value="1">⭐ 1</option>
            <option value="2">⭐⭐ 2</option>
            <option value="3">⭐⭐⭐ 3</option>
            <option value="4">⭐⭐⭐⭐ 4</option>
            <option value="5">⭐⭐⭐⭐⭐ 5</option>
        </select>

        <label>Komentar:</label>
        <textarea id="komentar" rows="3" required></textarea>

        <button type="submit">Kirim Rating</button>
    </form>

    <script src="js/rating.js"></script>
</body>

</html>