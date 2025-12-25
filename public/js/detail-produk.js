const API_BASE_URL = "http://localhost:8000/api";

// Data Management - Variabel untuk menyimpan data aplikasi
let produkData = [];
let tokoData = [];
let ratingData = [];
let keranjangData = [];

// Load semua data dari API Laravel
async function loadData() {
    try {
        // 🔥 Load produk dari Laravel API
        const timestamp = new Date().getTime();
        const produkResponse = await fetch(
            `${API_BASE_URL}/products?_=${timestamp}`,
            {
                method: "GET",
                headers: {
                    "Cache-Control": "no-cache",
                    Pragma: "no-cache",
                },
            }
        );
        produkData = await produkResponse.json();

        console.log("✅ Products loaded:", produkData.length, "items");

        // Load toko data (sementara masih dari JSON)
        try {
            const tokoResponse = await fetch("JSON/tokoData.json");
            tokoData = await tokoResponse.json();
        } catch (e) {
            console.log("⚠️ Toko data not found, using fallback");
            tokoData = [
                {
                    id: 1,
                    namaToko: "Toko Default",
                    lokasi: "Jakarta",
                },
            ];
        }

        // Load rating data
        ratingData = JSON.parse(localStorage.getItem("ratingList")) || [];

        // Load keranjang dari localStorage
        const savedCart = localStorage.getItem("keranjangData");
        if (savedCart) {
            keranjangData = JSON.parse(savedCart);
            console.log("Keranjang dimuat dari localStorage");
        } else {
            keranjangData = [];
        }

        // Inisialisasi halaman setelah semua data berhasil dimuat
        initializePage();
    } catch (error) {
        console.error("❌ Error loading data:", error);
        alert("Gagal memuat data produk!");
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
    // Konversi id ke number kalau string
    const numericId = typeof id === "string" ? parseInt(id) : id;

    const product = produkData.find((produk) => produk.id === numericId);

    console.log("🔍 Looking for product ID:", numericId);
    console.log("📦 Found product:", product);

    return product;
} // Di detail-produk.js, bagian btnBeli

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
    // 🔥 WAJIB: sync dari localStorage
    let keranjangData = JSON.parse(localStorage.getItem("keranjangData")) || [];

    // Validasi produk
    const produk = getProdukById(produkId);
    if (!produk) {
        console.error("Produk tidak ditemukan");
        return false;
    }

    // Cari produk di keranjang
    const existingItemIndex = keranjangData.findIndex(
        (item) => item.userId === userId && item.produkId === produkId
    );

    if (existingItemIndex !== -1) {
        const newTotal =
            keranjangData[existingItemIndex].jumlah + jumlahTambahan;

        if (newTotal > produk.stok) {
            showNotification(
                `Stok ${produk.nama} hanya tersedia ${produk.stok} item`,
                "warning"
            );
            return false;
        }

        keranjangData[existingItemIndex].jumlah = newTotal;
    } else {
        if (jumlahTambahan > produk.stok) {
            showNotification(
                `Stok ${produk.nama} hanya tersedia ${produk.stok} item`,
                "warning"
            );
            return false;
        }

        keranjangData.push({
            userId: userId,
            produkId: produk.id,
            nama: produk.nama,
            harga: produk.harga,
            hargaAsli: produk.hargaAsli || produk.harga,
            diskon: produk.diskon || 0,
            jumlah: jumlahTambahan,
            imagePath: produk.imagePath,
            deskripsi: produk.deskripsi,
        });
    }

    // 💾 Simpan kembali (merge, bukan replace)
    localStorage.setItem("keranjangData", JSON.stringify(keranjangData));

    console.log("Keranjang sekarang:", keranjangData);

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
        cartCountElement.style.display =
            totalItems > 0 ? "inline-block" : "none";
    }
}

// Page Initialization

let currentProduct = null;
let currentQuantity = 1;

// Inisialisasi halaman product detail
function initializePage() {
    const productId = window.PRODUK_ID;

    console.log("🔍 Product ID from URL:", productId);
    console.log("📦 All products:", produkData);

    // Ambil data produk berdasarkan ID
    currentProduct = getProdukById(productId);

    console.log("✅ Current product found:", currentProduct);

    if (!currentProduct) {
        console.error("❌ Product not found!");
        document.body.innerHTML = `
        <div style="text-align: center; padding: 100px;">
          <h1>Produk tidak ditemukan</h1>
          <p>Produk yang Anda cari tidak tersedia.</p>
          <p>Product ID: ${productId}</p>
          <p>Available products: ${produkData.map((p) => p.id).join(", ")}</p>
          <a href="/" style="color: #007bff; text-decoration: none;">← Kembali ke Beranda</a>
        </div>
      `;
        return;
    }

    // Render detail produk ke halaman
    renderProductDetails(currentProduct);

    // 🔥 FIX: Render informasi toko - pakai toko_id atau ambil dari relasi
    if (currentProduct.toko) {
        // Kalau API return relasi toko (sudah di-include)
        renderTokoInfo({
            id: currentProduct.toko.id,
            namaToko: currentProduct.toko.nama_toko,
            lokasi: currentProduct.toko.lokasi,
        });
    } else if (currentProduct.toko_id) {
        // Fallback: cari manual by toko_id
        const toko = getTokoById(currentProduct.toko_id);
        if (toko) {
            renderTokoInfo(toko);
        }
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
    console.log("🎨 Rendering product:", product);

    // Set gambar produk
    const imgElement = document.getElementById("product-image");
    if (imgElement) {
        let imagePath = product.imagePath || "img/iconOli.png";

        if (imagePath.startsWith("images/")) {
            imagePath = `http://localhost:8000/storage/${imagePath}`;
        }

        imgElement.src = imagePath;
        imgElement.alt = product.nama;

        imgElement.onerror = function () {
            console.error("Failed to load image:", imagePath);
            this.src = "img/iconOli.png";
        };

        imgElement.onload = function () {
            console.log("✅ Image loaded successfully!");
        };
    }

    // Set nama produk
    const nameEl = document.getElementById("product-name");
    if (nameEl) {
        nameEl.textContent = product.nama;
        console.log("✅ Name set:", product.nama);
    }

    // Set harga produk
    const priceEl = document.getElementById("product-price");
    if (priceEl) {
        priceEl.textContent = formatRupiah(product.harga);
        console.log("✅ Price set:", product.harga);
    }

    // Handle harga asli dan diskon
    const originalPriceEl = document.getElementById("product-original-price");
    const discountEl = document.getElementById("product-discount");

    if (product.diskon && product.diskon > 0) {
        // Hitung harga asli dari harga dan diskon
        const hargaAsli = Math.round(
            product.harga / (1 - product.diskon / 100)
        );

        if (originalPriceEl) {
            originalPriceEl.textContent = formatRupiah(hargaAsli);
            originalPriceEl.style.display = "block";
        }

        if (discountEl) {
            discountEl.textContent = `-${product.diskon}%`;
            discountEl.style.display = "inline-block";
        }

        console.log("✅ Discount displayed:", product.diskon + "%");
    } else {
        if (originalPriceEl) originalPriceEl.style.display = "none";
        if (discountEl) discountEl.style.display = "none";
    }

    // Set stok
    const stokEl = document.getElementById("product-stok");
    if (stokEl) {
        stokEl.textContent = product.stok;
        console.log("✅ Stock set:", product.stok);
    }

    // Set deskripsi
    const descEl = document.getElementById("product-description");
    if (descEl) {
        descEl.textContent = product.deskripsi;
        console.log("✅ Description set");
    }

    // Update total harga
    updateTotalPrice();

    console.log("✅ Product render completed");
}

// Render informasi toko
function renderTokoInfo(toko) {
    console.log("🏪 Rendering toko:", toko);

    const namaEl = document.getElementById("toko-nama");
    if (namaEl) {
        namaEl.textContent = toko.namaToko || toko.nama_toko || "Toko Default";
        console.log("✅ Toko name set:", namaEl.textContent);
    }

    const lokasiEl = document.getElementById("toko-lokasi");
    if (lokasiEl) {
        lokasiEl.textContent = toko.lokasi || "Indonesia";
        console.log("✅ Toko location set:", lokasiEl.textContent);
    }
}

// Render rating dan ulasan produk
function renderRatings(ratings) {
    // Hitung rata-rata rating
    const avgRating = calculateAvgRating(ratings);

    // Update ringkasan rating
    document.getElementById("avg-rating").textContent = avgRating.toFixed(1);
    document.getElementById(
        "rating-count"
    ).textContent = `(${ratings.length} ulasan)`;

    // Render list ulasan
    const reviewsList = document.getElementById("reviews-list");
    reviewsList.innerHTML = "";

    if (ratings.length === 0) {
        reviewsList.innerHTML =
            '<p style="color: #999; text-align: center;">Belum ada ulasan</p>';
        return;
    }

    // Loop setiap rating dan buat elemen HTML
    ratings.forEach((rating) => {
        const reviewItem = document.createElement("div");
        reviewItem.className = "review-item";

        // Generate bintang rating
        const stars = "★".repeat(rating.rating) + "☆".repeat(5 - rating.rating);

        reviewItem.innerHTML = `
       <div class="col-12 col-md-6 col-lg-4">
  <div class="card shadow-sm h-100">

    <div class="card-body d-flex">

      <!-- Icon -->
      <div class="me-3 d-flex align-items-start">
        <i class="bi bi-person-circle fs-3 text-primary"></i>
      </div>

      <!-- Content -->
      <div>
        <div class="text-warning fw-bold" style="font-size: 14px;">
          ${"★".repeat(rating.rating)}${"☆".repeat(5 - rating.rating)}
        </div>

        <p class="mb-1">${rating.komentar}</p>

        <small class="text-muted">${rating.tanggal}</small>
      </div>

    </div>

  </div>
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
    btnDecrease.addEventListener("click", () => {
        if (currentQuantity > 1) {
            currentQuantity--;
            quantityDisplay.textContent = currentQuantity;
            updateTotalPrice();
        }
    });

    // Tombol tambah quantity
    btnIncrease.addEventListener("click", () => {
        if (currentQuantity < currentProduct.stok) {
            currentQuantity++;
            quantityDisplay.textContent = currentQuantity;
            updateTotalPrice();
        } else {
            showNotification(
                `Stok ${currentProduct.nama} hanya tersedia ${currentProduct.stok} item`,
                "warning"
            );
        }
    });
}

// Update total harga berdasarkan quantity
function updateTotalPrice() {
    const totalPrice = currentProduct.harga * currentQuantity;
    document.getElementById("total-price").textContent =
        formatRupiah(totalPrice);
}

// Action Button Functions

// Setup event listener untuk tombol aksi
// Action Button Functions
function initializeActionButtons() {
    const btnKeranjang = document.getElementById("btn-Keranjang");
    const btnBeli = document.getElementById("btn-Beli");

    // Tombol Tambah ke Keranjang
    btnKeranjang.addEventListener("click", () => {
        const userId = 1;

        const success = tambahKeKeranjang(
            userId,
            currentProduct.id,
            currentQuantity
        );

        if (success) {
            showNotification(
                `${currentQuantity} ${currentProduct.nama} berhasil ditambahkan ke keranjang!`,
                "success"
            );

            currentQuantity = 1;
            document.getElementById("quantity-display").textContent =
                currentQuantity;
            updateTotalPrice();
        }
    });

    // Event handler tombol beli sekarang ✅ INI YANG BENAR
    btnBeli.addEventListener("click", () => {
        if (currentQuantity > currentProduct.stok) {
            showNotification(
                `Stok ${currentProduct.nama} hanya tersedia ${currentProduct.stok} item`,
                "warning"
            );
            return;
        }

        const checkoutData = [
            {
                productId: currentProduct.id,
                id: currentProduct.id,
                nama: currentProduct.nama,
                harga: currentProduct.harga,
                hargaAsli: currentProduct.hargaAsli || currentProduct.harga,
                diskon: currentProduct.diskon || 0,
                jumlah: currentQuantity,
                imagePath: currentProduct.imagePath,
                deskripsi: currentProduct.deskripsi,
            },
        ];

        localStorage.setItem("checkoutData", JSON.stringify(checkoutData));
        window.location.href = "/checkout";
    });
}

// Notification System

// Menampilkan notifikasi popup
function showNotification(message, type = "success") {
    // Hapus notifikasi lama jika ada
    const existingNotif = document.querySelector(".notification-toast");
    if (existingNotif) {
        existingNotif.remove();
    }

    // Buat elemen notifikasi
    const notification = document.createElement("div");
    notification.className = "notification-toast";

    const bgColor =
        type === "success"
            ? "#28a745"
            : type === "warning"
            ? "#ffc107"
            : type === "error"
            ? "#dc3545"
            : "#17a2b8";

    const textColor = type === "warning" ? "#000" : "#fff";

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
    if (!document.getElementById("notification-styles")) {
        const style = document.createElement("style");
        style.id = "notification-styles";
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
document.addEventListener("DOMContentLoaded", () => {
    loadData();
});
