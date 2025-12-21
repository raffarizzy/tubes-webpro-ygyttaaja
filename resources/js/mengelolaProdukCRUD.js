let products = [];
let tokoIdAktif = parseInt(localStorage.getItem('selectedTokoId')) || 1;

// Load produk dari JSON + LocalStorage
async function loadProducts() {
  try {
    const response = await fetch('JSON/productData.json');
    const data = await response.json();

    // Ambil tambahan dari LocalStorage
    const saved = JSON.parse(localStorage.getItem('productDataExtra')) || [];

    // Gabungkan dan filter berdasarkan toko aktif
    const combined = [...data, ...saved];
    products = combined.filter(p => parseInt(p.tokoId) === tokoIdAktif);

    renderProducts();
  } catch (err) {
    console.error('Error loading products:', err);
    document.getElementById('loadingState').innerHTML =
      '<p style="color:red;">Gagal memuat data produk</p>';
  }
}

// Render produk ke tabel
function renderProducts() {
  const tbody = document.getElementById('productTableBody');
  const table = document.getElementById('productTable');
  const empty = document.getElementById('emptyState');
  const loading = document.getElementById('loadingState');

  loading.style.display = 'none';

  if (!products || products.length === 0) {
    table.style.display = 'none';
    empty.style.display = 'block';
    return;
  }

  table.style.display = 'table';
  empty.style.display = 'none';

  tbody.innerHTML = products
    .map(
      (p, i) => `
      <tr>
        <td>${p.id}</td>
        <td>
          <img src="${
            p.imagePath && p.imagePath.trim() !== ''
              ? p.imagePath
              : ''
          }" 
          alt="${p.nama}" 
          width="60" height="60" 
          style="border-radius:6px;object-fit:cover;">
        </td>
        <td><strong>${p.nama}</strong></td>
        <td>Rp ${parseInt(p.harga).toLocaleString('id-ID')}</td>
        <td>${p.stok}</td>
        <td>${p.kategori}</td>
        <td>
          <button class="btn-icon delete" onclick="hapusProduk(${i})" title="Hapus">Hapus</button>
        </td>
      </tr>
    `
    )
    .join('');
}

// Tambah produk baru
function tambahProduk(event) {
  event.preventDefault();

  const nama = document.getElementById('namaProduk').value.trim();
  const harga = parseInt(document.getElementById('hargaProduk').value);
  const stok = parseInt(document.getElementById('stokProduk').value);
  const kategori = document.getElementById('kategoriProduk').value.trim();
  const deskripsi = document.getElementById('deskripsiProduk').value.trim();
  const imagePath = document.getElementById('imagePathProduk').value.trim();

  if (!nama || isNaN(harga) || isNaN(stok) || !kategori) {
    showNotification('Isi semua field yang wajib', 'error');
    return;
  }

  // 🔹 Cari ID terakhir biar urut
  const saved = JSON.parse(localStorage.getItem('productDataExtra')) || [];
  const allProducts = [...products, ...saved];
  const lastId = allProducts.length > 0 ? Math.max(...allProducts.map(p => p.id || 0)) : 0;

  const newProduct = {
    id: lastId + 1, // ID urut bukan timestamp
    nama, // ✅ disamain field-nya biar profil_toko bisa baca
    harga,
    stok,
    kategori,
    deskripsi,
    tokoId: tokoIdAktif,
    imagePath: imagePath || ''
  };

  // Simpan ke LocalStorage
  saved.push(newProduct);
  localStorage.setItem('productDataExtra', JSON.stringify(saved));

  // Update array & render ulang
  products.push(newProduct);
  renderProducts();

  document.getElementById('productForm').reset();
  showNotification('Produk berhasil ditambahkan!', 'success');
}

// Hapus produk
function hapusProduk(index) {
  const produk = products[index];
  const konfirmasi = confirm(`Yakin mau hapus produk "${produk.nama}"?`);
  if (!konfirmasi) return;

  const saved = JSON.parse(localStorage.getItem('productDataExtra')) || [];
  const filtered = saved.filter(p => p.id !== produk.id);
  localStorage.setItem('productDataExtra', JSON.stringify(filtered));

  products.splice(index, 1);
  renderProducts();
  showNotification('Produk berhasil dihapus', 'success');
}

// Notifikasi pojok
function showNotification(msg, type) {
  const notif = document.createElement('div');
  notif.textContent = msg;
  notif.style.cssText = `
    position: fixed;
    top: 20px;
    right: 20px;
    background: ${type === 'success' ? '#e61717ff' : '#dc3545'};
    color: white;
    padding: 14px 22px;
    border-radius: 8px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.2);
    font-weight: 600;
    z-index: 9999;
  `;
  document.body.appendChild(notif);
  setTimeout(() => notif.remove(), 3000);
}

document.addEventListener('DOMContentLoaded', loadProducts);
