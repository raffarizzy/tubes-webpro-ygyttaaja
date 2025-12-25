document.addEventListener("DOMContentLoaded", async () => {
    console.log("Load data keranjang dari API");

    const keranjangContainer = document.getElementById("keranjang-container");
    const totalItemEl = document.getElementById("total-item");
    const totalHargaEl = document.getElementById("total-harga");
    const btnCheckout = document.querySelector(".btn-checkout");

    let cartData = {
        items: [],
        summary: { total_items: 0, total_quantity: 0, total_price: 0 }
    };
    const userId = 1; // TODO: Get from session/auth
    const API_BASE_URL = 'http://localhost:3000/api';

    // FUNGSI LOAD DATA FROM API
    async function loadData() {
        try {
            // Load cart from Node.js API
            const cartResponse = await fetch(`${API_BASE_URL}/cart/${userId}`);
            const cartResult = await cartResponse.json();

            if (cartResult.success) {
                cartData.items = cartResult.data.items || [];
                cartData.summary = cartResult.data.summary || { total_items: 0, total_quantity: 0, total_price: 0 };
                console.log(`✅ Loaded ${cartData.items.length} items from API`);
            } else {
                console.warn('⚠️ Failed to load cart from API, using empty cart');
                cartData.items = [];
            }

            renderKeranjang();
        } catch (err) {
            console.error("Error loading cart:", err);
            console.warn("⚠️ Falling back to localStorage");

            // Fallback to localStorage
            const savedCart = localStorage.getItem("keranjangData");
            if (savedCart) {
                const allCartData = JSON.parse(savedCart);
                const userCart = allCartData.filter(item => item.userId === userId);

                // Convert localStorage format to API format
                cartData.items = userCart.map(item => ({
                    id: item.id,
                    product_id: item.produkId,
                    jumlah: item.jumlah,
                    harga: item.harga || 0,
                    product_nama: item.nama || '',
                    imagePath: item.imagePath || ''
                }));
            }

            renderKeranjang();
        }
    }

    // FUNGSI UPDATE ITEM QUANTITY
    async function updateItemQuantity(itemId, newJumlah) {
        try {
            const response = await fetch(`${API_BASE_URL}/cart/items/${itemId}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ jumlah: newJumlah })
            });

            const result = await response.json();

            if (result.success) {
                console.log('✅ Item quantity updated');
                // Reload cart to get updated summary
                await loadData();
                return true;
            } else {
                throw new Error(result.message);
            }
        } catch (err) {
            console.error('Error updating item:', err);
            alert('Gagal update jumlah barang');
            return false;
        }
    }

    // FUNGSI REMOVE ITEM
    async function removeItem(itemId) {
        try {
            const response = await fetch(`${API_BASE_URL}/cart/items/${itemId}`, {
                method: 'DELETE'
            });

            const result = await response.json();

            if (result.success) {
                console.log('✅ Item removed');
                // Reload cart
                await loadData();
                return true;
            } else {
                throw new Error(result.message);
            }
        } catch (err) {
            console.error('Error removing item:', err);
            alert('Gagal menghapus barang');
            return false;
        }
    }

    // FUNGSI UPDATE CART COUNT
    function updateCartCount() {
        const totalItems = cartData.summary.total_quantity || 0;
        const cartCountElement = document.getElementById("cart-count");
        if (cartCountElement) {
            cartCountElement.textContent = totalItems;
            cartCountElement.style.display = totalItems > 0 ? "inline-block" : "none";
        }
    }

    // FUNGSI RENDER KERANJANG
    function renderKeranjang() {
        keranjangContainer.innerHTML = "";

        const totalHarga = cartData.summary.total_price || 0;
        const totalItem = cartData.summary.total_quantity || 0;

        if (cartData.items.length === 0) {
            keranjangContainer.innerHTML = `
                <div style="text-align: center; padding: 40px; color: #999;">
                    <p style="font-size: 18px;">Keranjang kosong.</p>
                    <p>Yuk belanja dulu!</p>
                    <a href="/" style="color: #007bff; text-decoration: none;">
                        Kembali ke Beranda
                    </a>
                </div>
            `;
            totalItemEl.textContent = 0;
            totalHargaEl.textContent = "Rp 0";
            updateCartCount();

            // Disable tombol checkout jika keranjang kosong
            if (btnCheckout) {
                btnCheckout.disabled = true;
                btnCheckout.style.opacity = "0.5";
                btnCheckout.style.cursor = "not-allowed";
            }
            return;
        }

        cartData.items.forEach((item) => {
            const subtotal = item.harga * item.jumlah;
            const imageSrc = item.imagePath || item.image_path || 'img/iconOli.png';

            const itemDiv = document.createElement("div");
            itemDiv.classList.add("item-keranjang");
            itemDiv.setAttribute("data-item-id", item.id);

            itemDiv.innerHTML = `
                <img src="${imageSrc}"
                     alt="${item.product_nama}"
                     onerror="this.src='img/iconOli.png'" />
                <div class="info-produk">
                    <h3>${item.product_nama}</h3>
                    <p class="harga">Rp ${item.harga.toLocaleString("id-ID")}</p>
                    <p class="deskripsi">${item.deskripsi || ''}</p>
                    <p class="subtotal">Subtotal: <strong>Rp ${subtotal.toLocaleString("id-ID")}</strong></p>
                </div>
                <div class="kuantitas">
                    <button class="btn-minus" data-item-id="${item.id}">-</button>
                    <input type="number" value="${item.jumlah}" min="1" data-item-id="${item.id}" />
                    <button class="btn-plus" data-item-id="${item.id}">+</button>
                </div>
                <button class="btn-hapus" data-item-id="${item.id}">Hapus</button>
            `;

            keranjangContainer.appendChild(itemDiv);
        });

        totalItemEl.textContent = totalItem;
        totalHargaEl.textContent = `Rp ${totalHarga.toLocaleString("id-ID")}`;

        // Enable tombol checkout jika ada item
        if (btnCheckout) {
            btnCheckout.disabled = false;
            btnCheckout.style.opacity = "1";
            btnCheckout.style.cursor = "pointer";
        }

        updateCartCount();
        setupEventListeners();
    }

    // SETUP EVENT LISTENER
    function setupEventListeners() {
        // Event delegation
        keranjangContainer.addEventListener("click", async (e) => {
            const target = e.target;
            const itemId = parseInt(target.getAttribute("data-item-id"));

            if (isNaN(itemId)) return;

            const item = cartData.items.find(i => i.id === itemId);
            if (!item) return;

            // TOMBOL MINUS
            if (target.classList.contains("btn-minus")) {
                if (item.jumlah > 1) {
                    await updateItemQuantity(itemId, item.jumlah - 1);
                }
            }

            // TOMBOL PLUS
            if (target.classList.contains("btn-plus")) {
                await updateItemQuantity(itemId, item.jumlah + 1);
            }

            // TOMBOL HAPUS
            if (target.classList.contains("btn-hapus")) {
                const konfirmasi = confirm(`Hapus "${item.product_nama}" dari keranjang?`);
                if (konfirmasi) {
                    await removeItem(itemId);
                    showNotification(`"${item.product_nama}" berhasil dihapus dari keranjang`, "info");
                }
            }
        });

        // INPUT MANUAL
        keranjangContainer.addEventListener("change", async (e) => {
            if (e.target.type === "number") {
                const itemId = parseInt(e.target.getAttribute("data-item-id"));
                const val = parseInt(e.target.value);

                if (val > 0) {
                    await updateItemQuantity(itemId, val);
                } else {
                    // Reset to current value if invalid
                    const item = cartData.items.find(i => i.id === itemId);
                    if (item) e.target.value = item.jumlah;
                }
            }
        });
    }

    // TOMBOL CHECKOUT
    if (btnCheckout) {
        btnCheckout.addEventListener("click", (e) => {
            e.preventDefault();
            if (cartData.items.length === 0) {
                alert("Keranjang Anda kosong!");
                return;
            }

            // Convert to checkout format and save to localStorage
            const checkoutData = cartData.items.map(item => ({
                nama: item.product_nama,
                harga: item.harga,
                jumlah: item.jumlah,
                imagePath: item.imagePath || item.image_path,
                deskripsi: item.deskripsi || ''
            }));

            localStorage.setItem("checkoutData", JSON.stringify(checkoutData));
            console.log("Data checkout disimpan:", checkoutData);

            window.location.href = "/checkout";
        });
    }

    // NOTIFICATION SYSTEM 
    function showNotification(message, type = "success") {
        const notification = document.createElement("div");
        notification.style.cssText = `
            position: fixed;
            top: 80px;
            right: 20px;
            background-color: ${type === "success" ? "#28a745" : type === "info" ? "#17a2b8" : "#dc3545"};
            color: white;
            padding: 15px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            z-index: 10000;
            animation: slideIn 0.3s ease-out;
        `;
        notification.textContent = message;

        document.body.appendChild(notification);

        setTimeout(() => {
            notification.style.animation = "slideOut 0.3s ease-out";
            setTimeout(() => {
                if (notification.parentNode) {
                    document.body.removeChild(notification);
                }
            }, 300);
        }, 3000);
    }

    // Initial load
    await loadData();
});