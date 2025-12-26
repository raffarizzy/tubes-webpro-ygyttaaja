// ========================================
// DETAIL PRODUK - SIMPLIFIED VERSION
// All data is loaded from Laravel, JS only handles UI interactions
// ========================================

let currentQuantity = 1;
let currentProduct = null;

// ========================================
// INITIALIZATION
// ========================================

document.addEventListener("DOMContentLoaded", () => {
    // Get product data from window (set by Laravel)
    currentProduct = window.PRODUCT_DATA;

    if (!currentProduct) {
        console.error("❌ Product data not found!");
        return;
    }

    console.log("✅ Product loaded:", currentProduct);

    // Initialize UI controls
    initializeQuantityControls();
    initializeActionButtons();
});

// ========================================
// QUANTITY CONTROLS
// ========================================

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
    const totalPriceEl = document.getElementById("total-price");
    totalPriceEl.textContent = formatRupiah(totalPrice);
}

// ========================================
// ACTION BUTTONS
// ========================================

function initializeActionButtons() {
    const btnKeranjang = document.getElementById("btn-Keranjang");
    const btnBeli = document.getElementById("btn-Beli");

    // Tombol Tambah ke Keranjang
    btnKeranjang.addEventListener("click", async () => {
        const userId = window.USER_ID;

        // Check if user is logged in
        if (!userId || userId === null) {
            showNotification('Silakan login terlebih dahulu', 'warning');
            setTimeout(() => {
                window.location.href = '/login';
            }, 1500);
            return;
        }

        const success = await tambahKeKeranjang(
            userId,
            currentProduct.id,
            currentQuantity
        );

        if (success) {
            showNotification(
                `${currentQuantity} ${currentProduct.nama} berhasil ditambahkan ke keranjang!`,
                "success"
            );

            // Reset quantity
            currentQuantity = 1;
            document.getElementById("quantity-display").textContent = currentQuantity;
            updateTotalPrice();
        }
    });

    // Tombol Beli Sekarang
    btnBeli.addEventListener("click", () => {
        if (currentQuantity > currentProduct.stok) {
            showNotification(
                `Stok ${currentProduct.nama} hanya tersedia ${currentProduct.stok} item`,
                "warning"
            );
            return;
        }

        // Simpan data checkout ke localStorage
        const checkoutData = [
            {
                productId: currentProduct.id,
                id: currentProduct.id,
                nama: currentProduct.nama,
                harga: currentProduct.harga,
                jumlah: currentQuantity,
                imagePath: currentProduct.imagePath,
                deskripsi: currentProduct.deskripsi,
            },
        ];

        localStorage.setItem("checkoutData", JSON.stringify(checkoutData));
        window.location.href = "/checkout";
    });
}

// ========================================
// CART MANAGEMENT
// ========================================

async function tambahKeKeranjang(userId, produkId, jumlahTambahan) {
    console.log("🛒 Adding to cart:", { userId, produkId, jumlahTambahan });

    try {
        // Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        if (!csrfToken) {
            showNotification('Session expired. Silakan refresh halaman', 'error');
            return false;
        }

        // Call backend API to add to cart
        const response = await fetch('/keranjang/item', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                product_id: produkId,
                jumlah: jumlahTambahan
            })
        });

        const result = await response.json();

        if (response.status === 401) {
            showNotification('Silakan login terlebih dahulu', 'warning');
            setTimeout(() => {
                window.location.href = '/login';
            }, 1500);
            return false;
        }

        if (result.success) {
            console.log("✅ Added to cart successfully!");
            return true;
        } else {
            console.error("❌ Failed to add to cart:", result.message);
            showNotification(result.message || 'Gagal menambahkan ke keranjang', 'error');
            return false;
        }
    } catch (error) {
        console.error('❌ Error adding to cart:', error);
        showNotification('Terjadi kesalahan. Pastikan Anda sudah login.', 'error');
        return false;
    }
}

// ========================================
// UTILITY FUNCTIONS
// ========================================

// Format angka menjadi format mata uang Rupiah
function formatRupiah(amount) {
    return new Intl.NumberFormat("id-ID", {
        style: "currency",
        currency: "IDR",
        minimumFractionDigits: 0,
    }).format(amount);
}

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