// login sementara
const userId = 1;

// ambil data toko dari JSON
async function getTokoData() {
  const res = await fetch('JSON/tokoData.json');
  const data = await res.json();
  return data;
}

// ambil data produk dari JSON
async function getProdukData() {
  const res = await fetch('JSON/productData.json');
  const data = await res.json();
  return data;
}

// Menampilkan data toko dan produk
(async () => {
  const tokoData = await getTokoData();
  const toko = tokoData.find(t => t.id == userId || t.pemilikId == userId);

  if (toko) {
    tampilkanDataToko(toko);

    const produkData = await getProdukData();
    const produkToko = produkData.filter(p => p.tokoId == toko.id);
    tampilkanProduk(produkToko);
  } else {
    console.warn('⚠️ Toko tidak ditemukan untuk userId:', userId);
    document.querySelector('.toko-detail h2').textContent = 'Toko tidak ditemukan';
  }
})();

// fungsi menampilkan toko
function tampilkanDataToko(toko) {
  const namaEl = document.querySelector('.toko-detail h2');
  const profilEl = document.querySelector('.profil');
  const detailEl = document.querySelector('.toko-detail');

  namaEl.textContent = toko.namaToko || 'Tanpa Nama';
  profilEl.src = toko.logoPath || 'img/iconPengguna.png';

  // Hapus lokasi lama biar gak dobel
  const existingLokasi = detailEl.querySelector('.lokasi');
  if (existingLokasi) existingLokasi.remove();

  // Tambahkan lokasi baru
  const lokasiEl = document.createElement('p');
  lokasiEl.classList.add('lokasi');
  lokasiEl.textContent = `${toko.lokasi || 'Lokasi tidak diketahui'}`;
  lokasiEl.style.marginTop = '4px';
  lokasiEl.style.color = '#666';
  detailEl.appendChild(lokasiEl);
}

// fngsi tampilkan produk
function tampilkanProduk(produkList) {
  const container = document.querySelector('.produk-list');
  container.innerHTML = '';

  if (!produkList || produkList.length === 0) {
    container.innerHTML = `<p style="text-align:center; color:#777;">Belum ada produk di toko ini.</p>`;
    document.getElementById('jumlah-produk').textContent = 0;
    return;
  }

  produkList.forEach(p => {
    const card = document.createElement('div');
    card.classList.add('produk-card');

    card.innerHTML = `
      <img src="${p.imagePath}" alt="${p.nama}">
      <h4>${p.nama}</h4>
      <p class="harga">Rp${p.harga.toLocaleString('id-ID')}</p>
      <p class="terjual">${p.deskripsi}</p>
    `;

    container.appendChild(card);
  });

  const jumlahProdukEl = document.getElementById('jumlah-produk');
  if (jumlahProdukEl) jumlahProdukEl.textContent = produkList.length;
}

// fungsi hapus toko
document.querySelector('.btn-hapus').addEventListener('click', async () => {
  const konfirmasi = confirm('Apakah Anda yakin ingin menghapus toko ini?');
  if (!konfirmasi) return;

  // Hapus tampilan toko
  document.querySelector('.toko-header').remove();
  document.querySelector('.produk-list').innerHTML =
    `<p style="text-align:center; color:#777;">Toko telah dihapus.</p>`;

  alert('Toko berhasil dihapus!');
});

// fungsi edit toko
const editBtn = document.querySelector('.btn-Edit');
const popupEdit = document.getElementById('editPopup');
const saveBtn = document.getElementById('saveEdit');
const cancelBtn = document.getElementById('cancelEdit');

editBtn.addEventListener('click', async () => {
  popupEdit.classList.add('show');

  const tokoData = await getTokoData();
  const toko = tokoData.find(t => t.id == userId || t.pemilikId == userId);

  if (!toko) return;

  document.getElementById('editNamaToko').value = toko.namaToko || '';
  document.getElementById('editLokasi').value = toko.lokasi || '';
  document.getElementById('editLogo').value = toko.logoPath || '';
});

cancelBtn.addEventListener('click', () => {
  popupEdit.classList.remove('show');
});

saveBtn.addEventListener('click', async () => {
  const nama = document.getElementById('editNamaToko').value.trim();
  const lokasi = document.getElementById('editLokasi').value.trim();
  const logo = document.getElementById('editLogo').value.trim();

  // Update tampilan langsung tanpa menyimpan ke file
  const toko = { namaToko: nama, lokasi: lokasi, logoPath: logo };
  tampilkanDataToko(toko);

  alert('Perubahan disimpan (sementara)');
  popupEdit.classList.remove('show');
});
