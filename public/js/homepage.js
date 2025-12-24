// ============================================
// DATA MANAGEMENT
// ============================================

let produkData = [];
let keranjangData = [];

// Load data dari JSON
async function loadData() {
    try {
        const response = await fetch("JSON/productData.json");
        produkData = await response.json();

        // Load keranjang dari localStorage
        const savedCart = localStorage.getItem("keranjangData");
        if (savedCart) {
            keranjangData = JSON.parse(savedCart);
        }

        // Render produk dan update cart
        renderProduk();
        updateCartCount();
    } catch (error) {
        console.error("Error loading data:", error);
        // Fallback ke data hardcoded
        useFallbackData();
        renderProduk();
        updateCartCount();
    }
}

// Fallback data jika JSON ga bisa dimuat
function useFallbackData() {
    produkData = [
        {
            id: 1,
            nama: "Oli Mobil",
            harga: 125000,
            deskripsi: "Oli mobil berkualitas tinggi, dijamin original.",
            imagePath: "img/iconOli.png",
            kategori: "Otomotif",
        },
        {
            id: 2,
            nama: "Oli Motor",
            harga: 100700,
            deskripsi: "Oli motor original dengan performa tinggi.",
            imagePath: "img/iconOli.png",
            kategori: "Otomotif",
        },
        {
            id: 3,
            nama: "Filter Udara Mobil",
            harga: 75000,
            deskripsi: "Filter udara mobil kualitas OEM.",
            imagePath: "img/iconOli.png",
            kategori: "Suku Cadang",
        },
    ];
}

// ============================================
// RENDER PRODUK
// ============================================

function renderProduk() {
    const produkSection = document.querySelector(".produk");

    // Hapus card hardcoded yang ada
    const existingCard = document.querySelector(".card-produk");
    if (existingCard) {
        existingCard.remove();
    }

    // Buat container untuk semua card
    const container = document.createElement("div");
    container.className = "produk-container";
    container.style.cssText = `
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 30px;
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
  `;

    // Render setiap produk
    produkData.forEach((produk) => {
        const card = createProductCard(produk);
        container.appendChild(card);
    });

    produkSection.appendChild(container);
}

function createProductCard(produk) {
    const card = document.createElement("div");
    card.className = "card-produk";

    card.innerHTML = `
        <img src="${produk.imagePath}" alt="${produk.nama}" />
        <h3>${produk.nama}</h3>
        <p class="harga">${formatRupiah(produk.harga)}</p>
        <p class="deskripsi">${produk.deskripsi}</p>
        <a href="/produk/${produk.id}">
            <button class="btn-beli">Lihat Detail</button>
        </a>
    `;

    return card;
}

// Format harga ke Rupiah
function formatRupiah(amount) {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
    }).format(amount);
}

// ============================================
// CART COUNTER
// ============================================

function updateCartCount() {
    const userId = 1; // Hardcode untuk sekarang
    const userCart = keranjangData.filter((item) => item.userId === userId);
    const totalItems = userCart.reduce((sum, item) => sum + item.jumlah, 0);

    const cartCountElement = document.getElementById("cart-count");
    if (cartCountElement) {
        cartCountElement.textContent = totalItems;
        if (totalItems > 0) {
            cartCountElement.style.display = "inline-block";
        } else {
            cartCountElement.style.display = "none";
        }
    }
}

// ============================================
// HERO BUTTON
// ============================================

function initHeroButton() {
    const heroButton = document.querySelector(".hero button");
    if (heroButton) {
        heroButton.addEventListener("click", () => {
            // Scroll ke section produk
            const produkSection = document.querySelector(".produk");
            if (produkSection) {
                produkSection.scrollIntoView({ behavior: "smooth" });
            }
        });
    }
}

// ============================================
// INITIALIZE
// ============================================

document.addEventListener("DOMContentLoaded", () => {
    loadData();
    initHeroButton();
});

// ============================================
// GLOBAL DEBUG FUNCTIONS
// ============================================

window.resetCart = function () {
    localStorage.removeItem("keranjangData");
    localStorage.removeItem("cartCount");
    keranjangData = [];
    updateCartCount();
    console.log("✅ Cart berhasil di-reset!");
    alert("Cart berhasil di-reset!");
    location.reload();
};

window.viewCart = function () {
    console.log("=== ISI CART ===");
    console.log("Total items:", keranjangData.length);
    console.log("Data:", keranjangData);
    return keranjangData;
};

console.log(
    "%c🏠 HOMEPAGE LOADED",
    "color: #0066cc; font-weight: bold; font-size: 14px;"
);
console.log("Commands: resetCart(), viewCart()");
