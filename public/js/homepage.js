// ============================================
// CONFIGURATION
// ============================================

const API_BASE_URL = "http://localhost:8000/api"; // Sesuaikan dengan URL Laravel Anda

// ============================================
// DATA MANAGEMENT
// ============================================

let produkData = [];
let filteredProdukData = [];
let keranjangData = [];
let currentPage = 1;
const itemsPerPage = 9;

// Filter state
let filterState = {
    search: "",
    priceMin: null,
    priceMax: null,
};

// Load data from Laravel
async function loadData() {
    try {
        // Fetch dari Node.js API
        const API_BASE_URL = "http://localhost:3001/api";
        const response = await fetch(`${API_BASE_URL}/products`);
        const result = await response.json();

        // Handle API response
        produkData = result.success ? result.data : [];

        console.log("✅ Products loaded:", produkData.length, "items");

        // Load keranjang dari localStorage
        const savedCart = localStorage.getItem("keranjangData");
        if (savedCart) {
            keranjangData = JSON.parse(savedCart);
        }

        // Initialize
        filteredProdukData = [...produkData];
        renderProduk();
        updateCartCount();
    } catch (error) {
        console.error("❌ Error loading data:", error);
        alert("Gagal memuat data produk dari server!");
    }
}

// ============================================
// FILTER & SEARCH
// ============================================

function applyFilters() {
    filteredProdukData = produkData.filter((produk) => {
        // Search filter
        const matchSearch =
            filterState.search === "" ||
            produk.nama
                .toLowerCase()
                .includes(filterState.search.toLowerCase());

        // Price filter
        const matchPriceMin =
            filterState.priceMin === null ||
            produk.harga >= filterState.priceMin;
        const matchPriceMax =
            filterState.priceMax === null ||
            produk.harga <= filterState.priceMax;

        return matchSearch && matchPriceMin && matchPriceMax;
    });

    // Reset to page 1 when filter changes
    currentPage = 1;

    // Update results info
    updateResultsInfo();

    // Render produk
    renderProduk();
}

function updateResultsInfo() {
    const resultsInfoEl = document.getElementById("results-info");
    if (!resultsInfoEl) return;

    const total = filteredProdukData.length;
    const originalTotal = produkData.length;

    if (total === originalTotal) {
        resultsInfoEl.textContent = `Menampilkan semua produk (${total} produk)`;
    } else {
        resultsInfoEl.textContent = `Ditemukan ${total} produk dari ${originalTotal} produk`;
    }
}

// ============================================
// RENDER PRODUK WITH PAGINATION
// ============================================

function renderProduk() {
    const container = document.getElementById("produk-container");
    if (!container) return;

    // Clear container
    container.innerHTML = "";

    // Calculate pagination
    const totalPages = Math.ceil(filteredProdukData.length / itemsPerPage);
    const startIndex = (currentPage - 1) * itemsPerPage;
    const endIndex = startIndex + itemsPerPage;
    const currentProducts = filteredProdukData.slice(startIndex, endIndex);

    // Set container style
    container.style.cssText = `
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 30px;
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 20px;
    `;

    // Render products for current page
    if (currentProducts.length === 0) {
        container.innerHTML = `
            <div style="grid-column: 1 / -1; text-align: center; padding: 60px 20px; color: #999;">
                <h3 style="margin-bottom: 10px;">Tidak ada produk ditemukan</h3>
                <p>Coba ubah filter pencarian Anda</p>
            </div>
        `;
    } else {
        currentProducts.forEach((produk) => {
            const card = createProductCard(produk);
            container.appendChild(card);
        });
    }

    // Render pagination
    renderPagination(totalPages);
}

function createProductCard(produk) {
    const card = document.createElement("div");
    card.className = "card-produk";

    // Fix path gambar
    let imagePath;
    if (produk.imagePath.startsWith("http")) {
        // Kalau sudah full URL
        imagePath = produk.imagePath;
    } else if (produk.imagePath.startsWith("products/")) {
        // Kalau dari Laravel storage
        imagePath = `http://localhost:8000/storage/${produk.imagePath}`;
    } else {
        // Kalau path relatif (img/iconOli.png)
        imagePath = produk.imagePath;
    }

    card.innerHTML = `
        <img src="${imagePath}" 
             alt="${produk.nama}" 
             onerror="if(this.src!=='${
                 window.location.origin
             }/img/iconOli.png'){this.src='${
        window.location.origin
    }/img/iconOli.png'}" />
        <h3>${produk.nama}</h3>
        <p class="harga">${formatRupiah(produk.harga)}</p>
        <p class="deskripsi">${produk.deskripsi}</p>
        <a href="/produk/${produk.id}">
            <button class="btn-beli">Lihat Detail</button>
        </a>
    `;

    return card;
}

// ============================================
// PAGINATION
// ============================================

function renderPagination(totalPages) {
    const paginationEl = document.getElementById("pagination");
    if (!paginationEl) return;

    paginationEl.innerHTML = "";

    // Hide pagination if only 1 page or no results
    if (totalPages <= 1) {
        paginationEl.style.display = "none";
        return;
    }

    paginationEl.style.display = "flex";

    // Previous button
    if (currentPage > 1) {
        const prevBtn = createPaginationButton("« Prev", currentPage - 1);
        paginationEl.appendChild(prevBtn);
    }

    // Page numbers
    const maxVisiblePages = 5;
    let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
    let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);

    // Adjust start if we're near the end
    if (endPage - startPage < maxVisiblePages - 1) {
        startPage = Math.max(1, endPage - maxVisiblePages + 1);
    }

    // First page
    if (startPage > 1) {
        paginationEl.appendChild(createPaginationButton(1, 1));
        if (startPage > 2) {
            const dots = document.createElement("span");
            dots.textContent = "...";
            dots.style.cssText = "padding: 8px 12px; color: #666;";
            paginationEl.appendChild(dots);
        }
    }

    // Page numbers
    for (let i = startPage; i <= endPage; i++) {
        paginationEl.appendChild(createPaginationButton(i, i));
    }

    // Last page
    if (endPage < totalPages) {
        if (endPage < totalPages - 1) {
            const dots = document.createElement("span");
            dots.textContent = "...";
            dots.style.cssText = "padding: 8px 12px; color: #666;";
            paginationEl.appendChild(dots);
        }
        paginationEl.appendChild(
            createPaginationButton(totalPages, totalPages)
        );
    }

    // Next button
    if (currentPage < totalPages) {
        const nextBtn = createPaginationButton("Next »", currentPage + 1);
        paginationEl.appendChild(nextBtn);
    }
}

function createPaginationButton(label, page) {
    const btn = document.createElement("button");
    btn.textContent = label;
    btn.className = currentPage === page ? "active" : "";
    btn.style.cssText = `
        padding: 8px 16px;
        border: 2px solid ${currentPage === page ? "#007bff" : "#ddd"};
        background-color: ${currentPage === page ? "#007bff" : "white"};
        color: ${currentPage === page ? "white" : "#333"};
        border-radius: 6px;
        cursor: pointer;
        font-weight: ${currentPage === page ? "600" : "400"};
        transition: all 0.2s;
    `;

    btn.addEventListener("click", () => {
        currentPage = page;
        renderProduk();
        // Scroll to top of products
        document
            .querySelector(".produk")
            .scrollIntoView({ behavior: "smooth" });
    });

    btn.addEventListener("mouseenter", () => {
        if (currentPage !== page) {
            btn.style.backgroundColor = "#f0f0f0";
        }
    });

    btn.addEventListener("mouseleave", () => {
        if (currentPage !== page) {
            btn.style.backgroundColor = "white";
        }
    });

    return btn;
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

// Update cart count from Laravel API
async function updateCartCount() {
    try {
        const response = await fetch("/keranjang/data", {
            headers: {
                Accept: "application/json",
            },
        });

        if (response.ok) {
            const result = await response.json();
            if (result.success) {
                const totalItems = result.data.total_items || 0;

                const cartCountElement = document.getElementById("cart-count");
                if (cartCountElement) {
                    cartCountElement.textContent = totalItems;
                    cartCountElement.style.display =
                        totalItems > 0 ? "inline-block" : "none";
                }
                return;
            }
        }
    } catch (error) {
        console.log("Cart count not available (user may not be logged in)");
    }

    // Fallback: hide cart count if not logged in
    const cartCountElement = document.getElementById("cart-count");
    if (cartCountElement) {
        cartCountElement.style.display = "none";
    }
}

// Legacy function kept for compatibility - now calls Laravel API version
function updateCartCountLegacy() {
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
// EVENT LISTENERS
// ============================================

function initEventListeners() {
    // Hero button
    const heroButton = document.getElementById("scroll-produk");
    if (heroButton) {
        heroButton.addEventListener("click", () => {
            const produkSection = document.querySelector(".produk");
            if (produkSection) {
                produkSection.scrollIntoView({ behavior: "smooth" });
            }
        });
    }

    // Search input
    const searchInput = document.getElementById("search-input");
    if (searchInput) {
        searchInput.addEventListener("input", (e) => {
            filterState.search = e.target.value;
            applyFilters();
        });
    }

    // Price min filter
    const priceMinInput = document.getElementById("price-min");
    if (priceMinInput) {
        priceMinInput.addEventListener("input", (e) => {
            filterState.priceMin = e.target.value
                ? parseInt(e.target.value)
                : null;
            applyFilters();
        });
    }

    // Price max filter
    const priceMaxInput = document.getElementById("price-max");
    if (priceMaxInput) {
        priceMaxInput.addEventListener("input", (e) => {
            filterState.priceMax = e.target.value
                ? parseInt(e.target.value)
                : null;
            applyFilters();
        });
    }

    // Reset filter button
    const resetBtn = document.getElementById("reset-filter");
    if (resetBtn) {
        resetBtn.addEventListener("click", () => {
            // Clear filter state
            filterState = {
                search: "",
                priceMin: null,
                priceMax: null,
            };

            // Clear inputs
            if (searchInput) searchInput.value = "";
            if (priceMinInput) priceMinInput.value = "";
            if (priceMaxInput) priceMaxInput.value = "";

            // Reapply filters
            applyFilters();
        });
    }
}

// ============================================
// INITIALIZE
// ============================================

document.addEventListener("DOMContentLoaded", () => {
    loadData();
    initEventListeners();
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
