<!DOCTYPE html>
<html lang="id">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Kelola Produk - SpareHub</title>
    <link rel="stylesheet" href="css/mengelolaProdukCRUD.css" />
  </head>
  <body>
    <div class="header">
      <h1>Kelola Produk</h1>
      <button class="btn-kembali" onclick="window.history.back()">
        ← Kembali
      </button>
    </div>

    <div class="container">
      <!-- FORM TAMBAH PRODUK -->
      <div class="form-produk">
        <h2>Tambah Produk</h2>
        <form id="productForm" onsubmit="tambahProduk(event)">
          <div class="form-group">
            <label>Nama Produk *</label>
            <input type="text" id="namaProduk" required />
          </div>
          <div class="form-group">
            <label>Harga (Rp) *</label>
            <input type="number" id="hargaProduk" required min="0" />
          </div>
          <div class="form-group">
            <label>Stok *</label>
            <input type="number" id="stokProduk" required min="0" value="10" />
          </div>
          <div class="form-group">
            <label>Kategori *</label>
            <input
              type="text"
              id="kategoriProduk"
              required
              placeholder="Contoh: Otomotif"
            />
          </div>
          <div class="form-group full">
            <label>Deskripsi</label>
            <textarea
              id="deskripsiProduk"
              placeholder="Deskripsi produk (opsional)"
            ></textarea>
          </div>
          <div class="form-group">
            <label>Upload Gambar *</label>
            <input
              type="file"
              id="imageProduk"
              accept="image/jpeg,image/jpg,image/png"
              required
            />
            <small style="color: #666;">Format: JPG, JPEG, PNG</small>
          </div>
          <div class="form-group" style="display: none;">
            <label>Toko ID</label>
            <input type="number" id="tokoIdProduk" value="1" min="1" />
          </div>
          <div class="form-actions">
            <button type="submit" class="btn btn-primary">Tambah Produk</button>
          </div>
        </form>
      </div>

      <hr style="margin: 30px 0; border: 1px solid #ddd" />

      <!-- DAFTAR PRODUK -->
      <div class="product-table">
        <div id="loadingState" class="loading">
          <p>Memuat data produk...</p>
        </div>

        <table id="productTable" style="display: none">
          <thead>
            <tr>
              <th>ID</th>
              <th>Gambar</th>
              <th>Nama Produk</th>
              <th>Harga</th>
              <th>Stok</th>
              <th>Kategori</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody id="productTableBody"></tbody>
        </table>

        <div id="emptyState" style="display: none" class="empty-state">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"
            />
          </svg>
          <h3>Belum ada produk</h3>
          <p>Klik tombol "Tambah Produk" di atas</p>
        </div>
      </div>
    </div>

    <!-- MODAL KONFIRMASI HAPUS -->
    <div id="confirmModal" class="modal">
      <div class="modal-content" style="max-width: 400px">
        <div class="modal-header">Konfirmasi Hapus</div>
        <p style="margin-bottom: 20px; color: #666">
          Apakah Anda yakin ingin menghapus produk ini?
        </p>
        <div
          id="deleteProductInfo"
          style="
            padding: 12px;
            background-color: #f8f9fa;
            border-radius: 8px;
            margin-bottom: 20px;
          "
        ></div>
        <div class="form-actions">
          <button class="btn btn-secondary" onclick="closeConfirmModal()">
            Batal
          </button>
          <button class="btn btn-danger" onclick="confirmDelete()">
            Hapus
          </button>
        </div>
      </div>
    </div>

    <script src="js/mengelolaProdukCRUD.js"></script>
  </body>
</html>