document.addEventListener("DOMContentLoaded", async () => {
    console.log("Load data keranjang dari:", window.location.href);

    // Get elements
    const keranjangContainer = document.getElementById("keranjang-container");
    const totalItemEl = document.getElementById("total-item");
    const totalHargaEl = document.getElementById("total-harga");
    const btnCheckout = document.querySelector(".btn-checkout");

    // Get cart data from Laravel (passed via window.keranjangItems)
    let cartItems = window.keranjangItems || [];
    console.log("Cart items from Laravel:", cartItems);

    // RENDER FUNCTIONS

    function renderKeranjang() {
        console.log(`Rendering ${cartItems.length} items`);

        // Clear container
        keranjangContainer.innerHTML = "";

        if (cartItems.length === 0) {
            keranjangContainer.innerHTML = `
                <div style="text-align: center; padding: 50px; color: #666;">
                    <p style="font-size: 18px;">Keranjang belanja Anda kosong</p>
                    <a href="/" style="color: #007bff; text-decoration: none;">
                        Kembali Belanja
                    </a>
                </div>
            `;
            updateSummary();
            return;
        }

        // Render each item
        cartItems.forEach((item) => {
            const itemElement = createItemElement(item);
            keranjangContainer.appendChild(itemElement);
        });

        updateSummary();
    }

    function createItemElement(item) {
        const div = document.createElement("div");
        div.className = "item-keranjang";
        div.dataset.itemId = item.id;

        const subtotal = item.harga * item.jumlah;

        div.innerHTML = `
            <!-- Product Image -->
            <img src="http://localhost:8000/storage/${item.product.imagePath || '/img/placeholder.png'}"
                 alt="${item.product.nama}"
                 onerror="this.onerror=null; this.src='/img/placeholder.png';">

            <!-- Product Info -->
            <div class="info-produk">
                <h3>${item.product.nama}</h3>
                <p class="deskripsi">${item.product.deskripsi || ''}</p>
                <p class="harga">${formatRupiah(item.harga)}</p>
            </div>

            <!-- Quantity Controls -->
            <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 8px;">
                <div class="kuantitas">
                    <button class="btn-minus btn-decrease" data-item-id="${item.id}">-</button>
                    <input type="text" value="${item.jumlah}" readonly>
                    <button class="btn-plus btn-increase" data-item-id="${item.id}" data-max-stock="${item.product.stok}">+</button>
                </div>
                <p style="margin: 0; font-size: 0.85em; color: #5f6368;">
                    Subtotal: <strong style="color: #1e8e3e;">${formatRupiah(subtotal)}</strong>
                </p>
            </div>

            <!-- Remove Button -->
            <button class="btn-hapus btn-remove" data-item-id="${item.id}">Hapus</button>
        `;

        // Add event listeners
        const btnDecrease = div.querySelector(".btn-decrease");
        const btnIncrease = div.querySelector(".btn-increase");
        const btnRemove = div.querySelector(".btn-remove");

        btnDecrease.addEventListener("click", () => handleDecreaseQuantity(item.id, item.jumlah));
        btnIncrease.addEventListener("click", () => handleIncreaseQuantity(item.id, item.jumlah, item.product.stok));
        btnRemove.addEventListener("click", () => handleRemoveItem(item.id, item.product.nama));

        return div;
    }

    function updateSummary() {
        const totalItems = cartItems.reduce((sum, item) => sum + item.jumlah, 0);
        const totalHarga = cartItems.reduce((sum, item) => sum + (item.harga * item.jumlah), 0);

        totalItemEl.textContent = totalItems;
        totalHargaEl.textContent = formatRupiah(totalHarga);

        console.log(`Summary: ${totalItems} items, ${formatRupiah(totalHarga)}`);
    }

    // EVENT HANDLERS

    async function handleDecreaseQuantity(itemId, currentQty) {
        if (currentQty <= 1) {
            showNotification("Jumlah minimal adalah 1. Gunakan tombol Hapus untuk menghapus item.", "warning");
            return;
        }

        await updateItemQuantity(itemId, currentQty - 1);
    }

    async function handleIncreaseQuantity(itemId, currentQty, maxStock) {
        if (currentQty >= maxStock) {
            showNotification(`Stok maksimal adalah ${maxStock} item`, 'warning');
            return;
        }

        await updateItemQuantity(itemId, currentQty + 1);
    }

    async function handleRemoveItem(itemId, productName) {
        if (!confirm(`Hapus ${productName} dari keranjang?`)) {
            return;
        }

        await removeItem(itemId);
    }

    // API CALLS (Laravel API)

    async function updateItemQuantity(itemId, newQty) {
        try {
            const response = await fetch(`/keranjang/item/${itemId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ jumlah: newQty })
            });

            const result = await response.json();

            if (result.success) {
                console.log('Quantity updated');
                showNotification('Jumlah berhasil diperbarui', 'success');
                // Reload page to get fresh data
                window.location.reload();
            } else {
                throw new Error(result.message);
            }
        } catch (error) {
            console.error('Error updating quantity:', error);
            showNotification('Gagal memperbarui jumlah', 'error');
        }
    }

    async function removeItem(itemId) {
        try {
            const response = await fetch(`/keranjang/item/${itemId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    'Accept': 'application/json'
                }
            });

            const result = await response.json();

            if (result.success) {
                console.log('Item removed');
                showNotification(result.message, 'success');
                // Reload page to get fresh data
                window.location.reload();
            } else {
                throw new Error(result.message);
            }
        } catch (error) {
            console.error('Error removing item:', error);
            showNotification('Gagal menghapus item', 'error');
        }
    }

    // UTILITY FUNCTIONS

    function formatRupiah(amount) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(amount);
    }

    function showNotification(message, type = 'success') {
        // Remove existing notification
        const existingNotif = document.querySelector('.notification-toast');
        if (existingNotif) {
            existingNotif.remove();
        }

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

        // Add animation styles if not exists
        if (!document.getElementById('notification-styles')) {
            const style = document.createElement('style');
            style.id = 'notification-styles';
            style.textContent = `
                @keyframes slideIn {
                    from { transform: translateX(400px); opacity: 0; }
                    to { transform: translateX(0); opacity: 1; }
                }
                @keyframes slideOut {
                    from { transform: translateX(0); opacity: 1; }
                    to { transform: translateX(400px); opacity: 0; }
                }
            `;
            document.head.appendChild(style);
        }

        document.body.appendChild(notification);

        setTimeout(() => {
            notification.style.animation = 'slideOut 0.3s ease-out';
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.remove();
                }
            }, 300);
        }, 3000);
    }

    // CHECKOUT BUTTON

    btnCheckout.addEventListener('click', () => {
        if (cartItems.length === 0) {
            showNotification('Keranjang Anda kosong!', 'warning');
            return;
        }

        // Prepare checkout data from cart items
        const checkoutData = cartItems.map(item => ({
            id: item.product_id,
            productId: item.product_id,
            nama: item.product.nama,
            harga: item.harga,
            hargaAsli: item.harga,
            diskon: 0,
            jumlah: item.jumlah,
            imagePath: item.product.imagePath,
            deskripsi: item.product.deskripsi
        }));

        // Save to localStorage for checkout page
        localStorage.setItem('checkoutData', JSON.stringify(checkoutData));

        // Redirect to checkout page
        window.location.href = '/checkout';
    });

    // INITIALIZE
    renderKeranjang();
    console.log("Keranjang Laravel initialized");
});