// Data Management - Variabel untuk menyimpan data aplikasi
let produkData = [];
let tokoData = [];
let ratingData = [];
let keranjangData = [];

// Load semua data dari JSON atau localStorage
async function loadData() {
  try {
    // Load produk data
    const produkResponse = await fetch("JSON/productData.json");
    produkData = await produkResponse.json();

    // Load toko data
    const tokoResponse = await fetch("JSON/tokoData.json");
    tokoData = await tokoResponse.json();

    // Load rating data
    const ratingResponse = await fetch("JSON/ratingData.json");
    ratingData = await ratingResponse.json();

    // Load keranjang dari localStorage (PENTING: Key yang sama dengan keranjang.js)
    const savedCart = localStorage.getItem('keranjangData');
    if (savedCart) {
      keranjangData = JSON.parse(savedCart);
      console.log('Keranjang dimuat dari localStorage');
    } else {
      // Jika tidak ada, coba load dari JSON sebagai fallback
      try {
        const cartResponse = await fetch('JSON/keranjangData.json');
        keranjangData = await cartResponse.json();
        // Simpan ke localStorage
        localStorage.setItem('keranjangData', JSON.stringify(keranjangData));
        console.log('Keranjang dimuat dari JSON (fallback)');
      } catch (err) {
        keranjangData = [];
        console.log('Keranjang kosong');
      }
    }

    // Inisialisasi halaman setelah semua data berhasil dimuat
    initializePage();
  } catch (error) {
    console.error("Error loading data:", error);
    // Fallback ke data hardcoded jika file tidak ada
    useFallbackData();
    initializePage();
  }
}

// Data backup jika file JSON tidak bisa dimuat
function useFallbackData() {
  // Data produk default
  produkData = [
    {
      id: 1,
      nama: "Oli Mobil",
      harga: 125000,
      deskripsi: "Oli mobil berkualitas tinggi, dijamin original.",
      imagePath: "img/oliMobil.png",
      tokoId: 1,
      stok: 10,
      kategori: "Otomotif",
    },
    {
      id: 2,
      nama: "Oli Motor",
      harga: 100700,
      deskripsi: "Oli motor original dengan performa tinggi.",
      imagePath: "img/oliMotor.png",
      tokoId: 1,
      stok: 20,
      kategori: "Otomotif",
    },
    {
      id: 3,
      nama: "Filter Udara Mobil",
      harga: 75000,
      deskripsi: "Filter udara mobil kualitas OEM.",
      imagePath: "img/filterUdara.png",
      tokoId: 2,
      stok: 15,
      kategori: "Suku Cadang",
    },
  ];

  // Data toko default
  tokoData = [
    {
      id: 1,
      namaToko: "Bengkel Jaya Motor",
      pemilikId: 1,
      deskripsi: "Spesialis oli dan sparepart kendaraan.",
      logoPath: "img/logoToko1.png",
      lokasi: "Jakarta Barat",
    },
    {
      id: 2,
      namaToko: "Otomax Shop",
      pemilikId: 2,
      deskripsi: "Toko perlengkapan otomotif lengkap dan terpercaya.",
      logoPath: "img/logoToko2.png",
      lokasi: "Bandung",
    },
  ];

  // Data rating default
  ratingData = [
    {
      id: 1,
      produkId: 1,
      userId: 1,
      rating: 5,
      komentar: "Barang ori dan bagus banget, pengiriman cepat!",
      tanggal: "2025-11-03",
    },
    {
      id: 2,
      produkId: 2,
      userId: 1,
      rating: 4,
      komentar: "Oli motor sesuai deskripsi, recommended.",
      tanggal: "2025-11-03",
    },
  ];
}

// Helper Functions

// Mendapatkan data produk berdasarkan ID
function getProdukById(id) {
  return produkData.find((produk) => produk.id === id);
}

// Mendapatkan data toko berdasarkan ID
function getTokoById(id) {
  return tokoData.find((toko) => toko.id === id);
}

// Mendapatkan semua rating untuk produk tertentu
function getRatingByProdukId(produkId) {
  return ratingData.filter((rating) => rating.produkId === produkId);
}

// Menghitung rata-rata rating dari array rating
function calculateAvgRating(ratings) {
  if (ratings.length === 0) return 0;
  const sum = ratings.reduce((acc, rating) => acc + rating.rating, 0);
  return sum / ratings.length;
}

// Format angka menjadi format mata uang Rupiah
function formatRupiah(amount) {
  return new Intl.NumberFormat("id-ID", {
    style: "currency",
    currency: "IDR",
    minimumFractionDigits: 0,
  }).format(amount);
}

// Cart Management Functions

// Menambahkan produk ke keranjang belanja
function tambahKeKeranjang(userId, produkId, jumlahTambahan) {
  // Validasi stok
  const produk = getProdukById(produkId);
  if (!produk) {
    console.error('Produk tidak ditemukan');
    return false;
  }

  // Cari apakah produk sudah ada di keranjang
  const existingItemIndex = keranjangData.findIndex(
    (item) => item.userId === userId && item.produkId === produkId
  );

  if (existingItemIndex !== -1) {
    // Produk sudah ada, cek total jumlah tidak melebihi stok
    const newTotal = keranjangData[existingItemIndex].jumlah + jumlahTambahan;
    
    if (newTotal > produk.stok) {
      showNotification(
        `Stok ${produk.nama} hanya tersedia ${produk.stok} item`,
        'warning'
      );
      return false;
    }
    
    keranjangData[existingItemIndex].jumlah = newTotal;
  } else {
    // Produk belum ada, validasi jumlah tidak melebihi stok
    if (jumlahTambahan > produk.stok) {
      showNotification(
        `Stok ${produk.nama} hanya tersedia ${produk.stok} item`,
        'warning'
      );
      return false;
    }
    
    // Tambah item baru
    keranjangData.push({
      userId: userId,
      produkId: produkId,
      jumlah: jumlahTambahan,
    });
  }

  // Simpan ke localStorage (PENTING: Key yang sama dengan keranjang.js)
  localStorage.setItem('keranjangData', JSON.stringify(keranjangData));
  console.log('Keranjang disimpan ke localStorage');
  
  // Update tampilan badge jumlah item di keranjang
  updateCartCount();
  
  return true;
}

// Update badge jumlah item di keranjang pada navbar
function updateCartCount() {
  const userId = 1; // Hardcode untuk sekarang, nanti bisa pakai user login
  const userCart = keranjangData.filter((item) => item.userId === userId);
  const totalItems = userCart.reduce((sum, item) => sum + item.jumlah, 0);

  const cartCountElement = document.getElementById("cart-count");
  if (cartCountElement) {
    cartCountElement.textContent = totalItems;
    cartCountElement.style.display = totalItems > 0 ? "inline-block" : "none";
  }
}

// Page Initialization

let currentProduct = null;
let currentQuantity = 1;

// Inisialisasi halaman product detail
function initializePage() {
  // Ambil ID produk dari parameter URL, default ke ID 1
  const urlParams = new URLSearchParams(window.location.search);
  const productId = parseInt(urlParams.get("id")) || 1;

  // Ambil data produk berdasarkan ID
  currentProduct = getProdukById(productId);

  if (!currentProduct) {
    console.error('Product not found!');
    document.body.innerHTML = `
      <div style="text-align: center; padding: 100px;">
        <h1>Produk tidak ditemukan</h1>
        <p>Produk yang Anda cari tidak tersedia.</p>
        <a href="homepage.html" style="color: #007bff; text-decoration: none;">← Kembali ke Beranda</a>
      </div>
    `;
    return;
  }

  // Render detail produk ke halaman
  renderProductDetails(currentProduct);

  // Render informasi toko
  const toko = getTokoById(currentProduct.tokoId);
  if (toko) {
    renderTokoInfo(toko);
  }

  // Render rating dan ulasan
  const ratings = getRatingByProdukId(currentProduct.id);
  renderRatings(ratings);

  // Setup kontrol quantity
  initializeQuantityControls();

  // Setup tombol aksi (keranjang & beli)
  initializeActionButtons();

  // Update badge jumlah keranjang
  updateCartCount();
}

// Rendering Functions

// Render detail produk ke elemen HTML
function renderProductDetails(product) {
  // Set gambar produk dengan error handling
  const imgElement = document.getElementById('product-image');
  imgElement.src = product.imagePath;
  imgElement.alt = product.nama;
  
  // Log path gambar untuk debugging
  console.log('Loading image from:', product.imagePath);
  
  // Handler jika gambar gagal dimuat
  imgElement.onerror = function() {
    console.error('Failed to load image:', product.imagePath);
    // Tampilkan placeholder jika gambar tidak ditemukan
    this.src = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="300" height="300"%3E%3Crect fill="%23ddd" width="300" height="300"/%3E%3Ctext fill="%23999" x="50%25" y="50%25" dominant-baseline="middle" text-anchor="middle" font-size="20"%3ENo Image%3C/text%3E%3C/svg%3E';
    this.style.backgroundColor = '#f0f0f0';
  };
  
  // Handler jika gambar berhasil dimuat
  imgElement.onload = function() {
    console.log('Image loaded successfully!');
  };

  // Set nama produk
  document.getElementById('product-name').textContent = product.nama;

  // Set harga produk
  document.getElementById('product-price').textContent = formatRupiah(product.harga);
  
  // Handle harga asli dan diskon jika ada
  const originalPriceEl = document.getElementById('product-original-price');
  const discountEl = document.getElementById('product-discount');
  
  if (product.hargaAsli && product.diskon) {
    originalPriceEl.textContent = formatRupiah(product.hargaAsli);
    originalPriceEl.style.display = "block";

    discountEl.textContent = `-${product.diskon}%`;
    discountEl.style.display = "inline-block";
  } else {
    originalPriceEl.style.display = "none";
    discountEl.style.display = "none";
  }

  // Kondisi (jika ada)
  const kondisiEl = document.getElementById("product-kondisi");
  const kondisiRow = document.querySelector(".kondisi-row");
  if (kondisiRow) {
    if (product.kondisi) {
      kondisiEl.textContent = product.kondisi;
      kondisiRow.style.display = "block";
    } else {
      kondisiRow.style.display = "none";
    }
  }

  // Set stok produk
  document.getElementById('product-stok').textContent = product.stok;

  // Set deskripsi produk
  document.getElementById('product-description').textContent = product.deskripsi;

  // Update total harga berdasarkan quantity
  updateTotalPrice();
}

// Render informasi toko
function renderTokoInfo(toko) {
  document.getElementById("toko-nama").textContent = toko.namaToko;
  document.getElementById("toko-lokasi").textContent = toko.lokasi;
}

// Render rating dan ulasan produk
function renderRatings(ratings) {
  // Hitung rata-rata rating
  const avgRating = calculateAvgRating(ratings);
  
  // Update ringkasan rating
  document.getElementById('avg-rating').textContent = avgRating.toFixed(1);
  document.getElementById('rating-count').textContent = `(${ratings.length} ulasan)`;

  // Render list ulasan
  const reviewsList = document.getElementById('reviews-list');
  reviewsList.innerHTML = '';

  if (ratings.length === 0) {
    reviewsList.innerHTML =
      '<p style="color: #999; text-align: center;">Belum ada ulasan</p>';
    return;
  }

  // Loop setiap rating dan buat elemen HTML
  ratings.forEach(rating => {
    const reviewItem = document.createElement('div');
    reviewItem.className = 'review-item';
    
    // Generate bintang rating
    const stars = '★'.repeat(rating.rating) + '☆'.repeat(5 - rating.rating);
    
    reviewItem.innerHTML = `
      <div class="review-icon">
        👤
      </div>
      <div class="review-content">
        <div class="review-rating">${stars}</div>
        <div class="review-text">${rating.komentar}</div>
        <div class="review-date">${rating.tanggal}</div>
      </div>
    `;

    reviewsList.appendChild(reviewItem);
  });
}

// Quantity Control Functions

// Setup event listener untuk kontrol quantity
function initializeQuantityControls() {
  const btnDecrease = document.getElementById("btn-decrease");
  const btnIncrease = document.getElementById("btn-increase");
  const quantityDisplay = document.getElementById("quantity-display");

  // Tombol kurang quantity
  btnDecrease.addEventListener('click', () => {
    if (currentQuantity > 1) {
      currentQuantity--;
      quantityDisplay.textContent = currentQuantity;
      updateTotalPrice();
    }
  });

  // Tombol tambah quantity
  btnIncrease.addEventListener('click', () => {
    if (currentQuantity < currentProduct.stok) {
      currentQuantity++;
      quantityDisplay.textContent = currentQuantity;
      updateTotalPrice();
    } else {
      showNotification(`Stok ${currentProduct.nama} hanya tersedia ${currentProduct.stok} item`, 'warning');
    }
  });
}

// Update total harga berdasarkan quantity
function updateTotalPrice() {
  const totalPrice = currentProduct.harga * currentQuantity;
  document.getElementById("total-price").textContent = formatRupiah(totalPrice);
}

// Action Button Functions

// Setup event listener untuk tombol aksi
function initializeActionButtons() {
  const btnKeranjang = document.getElementById("btn-Keranjang");
  const btnBeli = document.getElementById("btn-Beli");

  // Tombol Tambah ke Keranjang
  btnKeranjang.addEventListener("click", () => {
    const userId = 1; // Hardcode, nanti pakai user login
    
    const success = tambahKeKeranjang(userId, currentProduct.id, currentQuantity);
    
    if (success) {
      // Show notification
      showNotification(
        `${currentQuantity} ${currentProduct.nama} berhasil ditambahkan ke keranjang!`,
        'success'
      );

      // Reset quantity
      currentQuantity = 1;
      document.getElementById('quantity-display').textContent = currentQuantity;
      updateTotalPrice();
    }
  });

  // Event handler tombol beli sekarang
  btnBeli.addEventListener('click', () => {
    // Validasi stok
    if (currentQuantity > currentProduct.stok) {
      showNotification(
        `Stok ${currentProduct.nama} hanya tersedia ${currentProduct.stok} item`,
        'warning'
      );
      return;
    }

    // Simpan data checkout ke localStorage
    const checkoutData = [
      {
        nama: currentProduct.nama,
        harga: currentProduct.harga,
        hargaAsli: currentProduct.hargaAsli || currentProduct.harga,
        diskon: currentProduct.diskon || 0,
        jumlah: currentQuantity,
        imagePath: currentProduct.imagePath,
        deskripsi: currentProduct.deskripsi,
      },
    ];
    
    // Simpan data checkout ke localStorage
    localStorage.setItem('checkoutData', JSON.stringify(checkoutData));
    
    // Redirect ke halaman checkout
    window.location.href = "checkout.html";
  });
}

// Notification System

// Menampilkan notifikasi popup
function showNotification(message, type = 'success') {
  // Hapus notifikasi lama jika ada
  const existingNotif = document.querySelector('.notification-toast');
  if (existingNotif) {
    existingNotif.remove();
  }

  // Buat elemen notifikasi
  const notification = document.createElement('div');
  notification.className = 'notification-toast';
  
  const bgColor = type === 'success' ? '#28a745' : 
                  type === 'warning' ? '#ffc107' : 
                  type === 'error' ? '#dc3545' : '#17a2b8';
  
  const textColor = type === 'warning' ? '#000' : '#fff';
  
  notification.style.cssText = `
    position: fixed;
    top: 80px;
    right: 20px;
    background-color: ${bgColor};
    color: ${textColor};
    padding: 15px 20px;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    z-index: 10000;
    animation: slideIn 0.3s ease-out;
    font-weight: 500;
    max-width: 350px;
  `;
  notification.textContent = message;

  // Tambahkan CSS animation jika belum ada
  if (!document.getElementById('notification-styles')) {
    const style = document.createElement('style');
    style.id = 'notification-styles';
    style.textContent = `
      @keyframes slideIn {
        from {
          transform: translateX(400px);
          opacity: 0;
        }
        to {
          transform: translateX(0);
          opacity: 1;
        }
      }
      @keyframes slideOut {
        from {
          transform: translateX(0);
          opacity: 1;
        }
        to {
          transform: translateX(400px);
          opacity: 0;
        }
      }
    `;
    document.head.appendChild(style);
  }

  // Tambahkan notifikasi ke body
  document.body.appendChild(notification);

  // Auto hapus notifikasi setelah 3 detik
  setTimeout(() => {
    notification.style.animation = "slideOut 0.3s ease-out";
    setTimeout(() => {
      if (notification.parentNode) {
        document.body.removeChild(notification);
      }
    }, 300);
  }, 3000);
}

// Initialize Application

// Event listener saat halaman selesai dimuat
document.addEventListener('DOMContentLoaded', () => {
  loadData();
});
