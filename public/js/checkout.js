document.addEventListener("DOMContentLoaded", function () {
    const checkoutData = JSON.parse(localStorage.getItem("checkoutData")) || [];

    const itemContainer = document.getElementById("checkoutItems");

    const orderPrice = document.getElementById("orderPrice");
    const orderDelivery = document.getElementById("orderDelivery");
    const orderDiscount = document.getElementById("orderDiscount");
    const orderTotal = document.getElementById("orderTotal");

    if (checkoutData.length === 0) {
        const emptyCard = `
      <div class="card shadow-sm mb-4">
        <div class="card-body text-center py-5">
          <i class="bi bi-cart-x fs-1 text-muted mb-3"></i>
          <h5 class="text-muted">Tidak ada produk untuk checkout</h5>
          <p class="text-muted">Silakan tambahkan produk ke keranjang terlebih dahulu</p>
          <a href="keranjang.html" class="btn btn-primary mt-3">
            <i class="bi bi-arrow-left"></i> Kembali ke Keranjang
          </a>
        </div>
      </div>
    `;

        itemContainer.innerHTML = emptyCard;

        orderPrice.textContent = "Rp 0";
        orderDelivery.textContent = "Gratis";
        orderDiscount.textContent = "- Rp 0";
        orderTotal.textContent = "Rp 0";

        return;
    }

    renderAllItems(checkoutData);
    updateOrderDetails(checkoutData);

    function renderAllItems(items) {
        itemContainer.innerHTML = "";

        items.forEach((product) => {
            const hargaAsli = product.hargaAsli || product.harga;
            const diskon = product.diskon || 0;
            const jumlah = product.jumlah;

            let hargaFinal = hargaAsli;
            if (diskon > 0) {
                hargaFinal =
                    hargaAsli -
                    hargaAsli * (diskon < 1 ? diskon : diskon / 100);
            }

            const div = document.createElement("div");
            div.className = "card mb-2";

            div.innerHTML = `
          <div class="row g-0">
            <div class="col-md-3 bg-light p-1 text-center">
              <img src="${product.imagePath}" 
                   alt="${product.nama}"
                   class="img-fluid" 
                   style="max-height:100px"
                   onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22110%22 height=%22110%22 viewBox=%220 0 110 110%22%3E%3Crect width=%22110%22 height=%22110%22 fill=%22%23f1f3f4%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 font-family=%22Arial, sans-serif%22 font-size=%2214%22 fill=%22%235f6368%22%3ENo Image%3C/text%3E%3Cpath d=%22M35 40h40v5H35z M40 50h30v5H40z M45 60h20v5H45z%22 fill=%22%23dadce0%22/%3E%3C/svg%3E';" />
            </div>
            <div class="col-md-9">
              <div class="card-body p-2">
                <h6 class="mb-1 small fw-semibold">${product.nama}</h6>
                <p class="small text-muted mb-1">${product.deskripsi}</p>
                <p class="small fw-bold mb-0">
                  Rp ${hargaFinal.toLocaleString("id-ID")}
                </p>
                <p class="small text-muted mb-0">Jumlah: ${jumlah} pcs</p>
              </div>
            </div>
          </div>
        `;

            itemContainer.appendChild(div);
        });
    }

    function updateOrderDetails(items) {
        let totalHargaAsli = 0;
        let totalHargaSetelahDiskon = 0;
        let totalDiskon = 0;

        items.forEach((product) => {
            const hargaAsli =
                parseFloat(product.hargaAsli) || parseFloat(product.harga);
            const harga = parseFloat(product.harga);
            const diskon = parseFloat(product.diskon) || 0;
            const jumlah = parseInt(product.jumlah) || 1;

            let hargaSetelahDiskon = harga;
            if (diskon > 0) {
                const persenDiskon = diskon < 1 ? diskon : diskon / 100;
                hargaSetelahDiskon = Math.round(
                    hargaAsli - hargaAsli * persenDiskon
                );
            }

            const subtotalAsli = hargaAsli * jumlah;
            const subtotalSetelahDiskon = hargaSetelahDiskon * jumlah;

            totalHargaAsli += subtotalAsli;
            totalHargaSetelahDiskon += subtotalSetelahDiskon;
        });

        totalDiskon = totalHargaAsli - totalHargaSetelahDiskon;

        orderPrice.textContent = formatRupiah(totalHargaAsli);

        const biayaPengiriman = 0;
        orderDelivery.textContent =
            biayaPengiriman === 0 ? "Gratis" : formatRupiah(biayaPengiriman);
        orderDelivery.className =
            biayaPengiriman === 0
                ? "text-end text-success fw-bold"
                : "text-end fw-bold";

        orderDiscount.textContent =
            totalDiskon > 0 ? `- ${formatRupiah(totalDiskon)}` : "- Rp 0";

        const totalAkhir = totalHargaSetelahDiskon + biayaPengiriman;
        orderTotal.textContent = formatRupiah(totalAkhir);
    }

    function formatRupiah(angka) {
        const num = typeof angka === "string" ? parseFloat(angka) : angka;
        return num.toLocaleString("id-ID", {
            style: "currency",
            currency: "IDR",
            minimumFractionDigits: 0,
        });
    }
});

// ============= ALAMAT MANAGEMENT WITH MySQL =============
document.addEventListener("DOMContentLoaded", function () {
    const addressContainer = document.getElementById("addressContainer");
    const addAddressCard = document.getElementById("addAddressCard");
    const addAddressForm = document.getElementById("addAddressForm");
    const saveAddressBtn = document.getElementById("saveAddress");
    const cancelAddBtn = document.getElementById("cancelAdd");
    const deleteAddressBtn = document.getElementById("deleteAddress");
    const payButton = document.getElementById("payNowBtn");
    const paymentCards = document.querySelectorAll(".card[role='button']");

    let selectedAddress = null;
    let selectedPayment = null;
    let alamatList = [];
    let editIndex = null;

    // Helper function untuk mendapatkan CSRF token
    function getCsrfToken() {
        const token = document.querySelector('meta[name="csrf-token"]');
        if (!token) {
            console.error(
                'CSRF token not found! Pastikan ada <meta name="csrf-token"> di HTML'
            );
            return "";
        }
        return token.getAttribute("content");
    }

    // Load alamat dari database
    async function loadAlamatFromDB() {
        console.log("Loading alamat from database...");

        try {
            const response = await fetch("/alamat", {
                method: "GET",
                headers: {
                    "Content-Type": "application/json",
                    Accept: "application/json",
                    "X-CSRF-TOKEN": getCsrfToken(),
                },
                credentials: "same-origin",
            });

            console.log("Response status:", response.status);

            // Handle 401 Unauthorized - user belum login
            if (response.status === 401) {
                console.error(
                    "User not authenticated. Redirecting to login..."
                );
                showNotification("Anda harus login terlebih dahulu", "warning");

                // Redirect ke halaman login setelah 2 detik
                setTimeout(() => {
                    window.location.href =
                        "/login?redirect=" +
                        encodeURIComponent(window.location.pathname);
                }, 2000);

                // Tampilkan pesan di UI
                const loginCard = `
                    <div class="col-12">
                        <div class="alert alert-warning text-center">
                            <i class="bi bi-exclamation-triangle fs-1 mb-3"></i>
                            <h5>Anda Belum Login</h5>
                            <p>Silakan login terlebih dahulu untuk melanjutkan checkout</p>
                            <a href="/login" class="btn btn-primary mt-2">
                                <i class="bi bi-box-arrow-in-right"></i> Login Sekarang
                            </a>
                        </div>
                    </div>
                `;
                addressContainer.innerHTML = loginCard;
                return;
            }

            if (!response.ok) {
                const errorText = await response.text();
                console.error("Error response:", errorText);
                throw new Error(`HTTP ${response.status}: ${errorText}`);
            }

            const data = await response.json();
            console.log("Alamat loaded:", data);

            alamatList = data;
            renderSemuaAlamat();
        } catch (error) {
            console.error("Error loading alamat:", error);
            showNotification(`Gagal memuat alamat: ${error.message}`, "danger");

            // Tampilkan pesan error di UI
            const errorCard = `
                <div class="col-12">
                    <div class="alert alert-danger">
                        <strong>Error:</strong> ${error.message}<br>
                        <small>Cek console untuk detail lebih lanjut</small>
                    </div>
                </div>
            `;
            addressContainer.insertAdjacentHTML("afterbegin", errorCard);
        }
    }

    // Render semua alamat
    function renderSemuaAlamat() {
        document.querySelectorAll(".address-card").forEach((c) => c.remove());

        console.log("Rendering alamat:", alamatList);

        alamatList.forEach((alamat) => {
            buatAddressCard(
                alamat.nama_penerima,
                alamat.alamat,
                alamat.nomor_penerima,
                alamat.id,
                alamat.is_default
            );
        });

        const addCardCol = addAddressCard.closest(".col-md-6");
        if (alamatList.length >= 3) {
            addCardCol.style.display = "none";
        } else {
            addCardCol.style.display = "block";
        }
    }

    // Buat card alamat
    function buatAddressCard(nama, alamat, nomor, id, isDefault = false) {
        const col = document.createElement("div");
        col.className = "col-md-6 col-xl-4 address-card";

        // Konversi isDefault ke boolean yang benar
        const isDefaultBool =
            isDefault === 1 || isDefault === true || isDefault === "1";

        console.log(
            `Card ${id}: isDefault =`,
            isDefault,
            "converted to:",
            isDefaultBool
        );

        const defaultBadge = isDefaultBool
            ? '<span class="badge bg-success ms-2"><i class="bi bi-star-fill"></i> Default</span>'
            : "";

        col.innerHTML = `
      <div class="card border-2 h-100 card-selectable" data-id="${id}">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start mb-2">
            <h6 class="mb-0 fw-bold">${nama}${defaultBadge}</h6>
            <span class="text-primary small edit-link" data-id="${id}" style="cursor: pointer;">
              <i class="bi bi-pencil"></i> Edit
            </span>
          </div>
          <p class="text-muted small mb-2" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">${alamat}</p>
          <p class="text-muted small mb-0"><i class="bi bi-telephone"></i> ${nomor}</p>
        </div>
      </div>
    `;

        const card = col.querySelector(".card");

        // Auto-select alamat default
        if (isDefaultBool) {
            card.classList.add("selected");
            selectedAddress = { id, nama, alamat, nomor };
            updatePayButtonState();
            console.log(`Auto-selected default address: ${id}`);
        }

        card.addEventListener("click", (e) => {
            if (e.target.closest(".edit-link")) return;

            document
                .querySelectorAll(".card-selectable")
                .forEach((c) => c.classList.remove("selected"));
            card.classList.add("selected");
            selectedAddress = { id, nama, alamat, nomor };
            updatePayButtonState();
        });

        const editBtn = col.querySelector(".edit-link");
        editBtn.addEventListener("click", (e) => {
            e.stopPropagation();
            const alamatData = alamatList.find((a) => a.id == id);
            if (alamatData) {
                editIndex = id;
                tampilkanFormEdit(alamatData);
            }
        });

        addressContainer.insertBefore(col, addAddressCard.closest(".col-md-6"));
    }

    // Tambah alamat baru
    addAddressCard.addEventListener("click", () => {
        editIndex = null;
        addAddressForm.classList.remove("d-none");
        document.getElementById("formTitle").textContent = "Tambah Alamat Baru";
        deleteAddressBtn.classList.add("d-none");

        resetForm();

        addAddressForm.scrollIntoView({ behavior: "smooth", block: "center" });
    });

    // Simpan alamat (create/update)
    saveAddressBtn.addEventListener("click", async () => {
        const nama = document.getElementById("namaInput").value.trim();
        const alamat = document.getElementById("alamatInput").value.trim();
        const nomor = document.getElementById("nomorInput").value.trim();
        const isDefaultCheckbox = document.getElementById("defaultCheckbox");
        const isDefault = isDefaultCheckbox ? isDefaultCheckbox.checked : false;

        console.log("Form values:");
        console.log("- Nama:", nama);
        console.log("- Alamat:", alamat);
        console.log("- Nomor:", nomor);
        console.log("- Checkbox element:", isDefaultCheckbox);
        console.log(
            "- Checkbox checked:",
            isDefaultCheckbox ? isDefaultCheckbox.checked : "CHECKBOX NOT FOUND"
        );
        console.log("- isDefault final:", isDefault);

        if (!nama || !alamat || !nomor) {
            alert("Lengkapi semua data alamat!");
            return;
        }

        const dataBaru = {
            nama_penerima: nama,
            alamat: alamat,
            nomor_penerima: nomor,
            is_default: isDefault ? 1 : 0,
        };

        console.log(
            "Data yang akan dikirim:",
            JSON.stringify(dataBaru, null, 2)
        );

        try {
            saveAddressBtn.disabled = true;
            const originalText = saveAddressBtn.innerHTML;
            saveAddressBtn.innerHTML =
                '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';

            let response;
            let url;
            let method;

            if (editIndex !== null) {
                // Update alamat
                url = `/alamat/${editIndex}`;
                method = "PUT";
                console.log(`Updating alamat ID: ${editIndex}`);
            } else {
                // Create alamat baru
                if (alamatList.length >= 3) {
                    alert("Maksimal 3 alamat!");
                    saveAddressBtn.disabled = false;
                    saveAddressBtn.innerHTML = originalText;
                    return;
                }

                url = "/alamat";
                method = "POST";
                console.log("Creating new alamat");
            }

            console.log(`${method} request to:`, url);

            response = await fetch(url, {
                method: method,
                headers: {
                    "Content-Type": "application/json",
                    Accept: "application/json",
                    "X-CSRF-TOKEN": getCsrfToken(),
                    "X-Requested-With": "XMLHttpRequest",
                },
                credentials: "same-origin",
                body: JSON.stringify(dataBaru),
            });

            console.log("Save response status:", response.status);

            if (!response.ok) {
                const errorText = await response.text();
                console.error("Error response:", errorText);
                throw new Error(`HTTP ${response.status}: ${errorText}`);
            }

            const result = await response.json();
            console.log("Save result:", result);

            // Reset selected address agar re-render ulang
            selectedAddress = null;

            await loadAlamatFromDB();
            addAddressForm.classList.add("d-none");
            editIndex = null;

            showNotification("Alamat berhasil disimpan!", "success");
        } catch (error) {
            console.error("Error saving alamat:", error);
            showNotification(
                `Gagal menyimpan alamat: ${error.message}`,
                "danger"
            );
        } finally {
            saveAddressBtn.disabled = false;
            saveAddressBtn.innerHTML = '<i class="bi bi-check-lg"></i> Simpan';
        }
    });

    // Cancel form
    cancelAddBtn.addEventListener("click", () => {
        addAddressForm.classList.add("d-none");
        editIndex = null;
    });

    // Delete alamat
    deleteAddressBtn.addEventListener("click", async () => {
        if (editIndex === null) return;

        if (!confirm("Yakin ingin menghapus alamat ini?")) return;

        console.log("Deleting alamat:", editIndex);

        try {
            deleteAddressBtn.disabled = true;

            const response = await fetch(`/alamat/${editIndex}`, {
                method: "DELETE",
                headers: {
                    "Content-Type": "application/json",
                    Accept: "application/json",
                    "X-CSRF-TOKEN": getCsrfToken(),
                    "X-Requested-With": "XMLHttpRequest",
                },
                credentials: "same-origin",
            });

            console.log("Delete response status:", response.status);

            if (!response.ok) {
                const errorText = await response.text();
                console.error("Error response:", errorText);
                throw new Error(`HTTP ${response.status}: ${errorText}`);
            }

            await loadAlamatFromDB();
            addAddressForm.classList.add("d-none");
            editIndex = null;

            showNotification("Alamat berhasil dihapus!", "info");
        } catch (error) {
            console.error("Error deleting alamat:", error);
            showNotification(
                `Gagal menghapus alamat: ${error.message}`,
                "danger"
            );
        } finally {
            deleteAddressBtn.disabled = false;
        }
    });

    // Tampilkan form edit
    function tampilkanFormEdit(data) {
        console.log("Editing alamat:", data);

        addAddressForm.classList.remove("d-none");
        document.getElementById("formTitle").textContent = "Edit Alamat";
        deleteAddressBtn.classList.remove("d-none");
        document.getElementById("namaInput").value = data.nama_penerima;
        document.getElementById("alamatInput").value = data.alamat;
        document.getElementById("nomorInput").value = data.nomor_penerima;

        const defaultCheckbox = document.getElementById("defaultCheckbox");
        if (defaultCheckbox) {
            // Konversi dengan benar
            const isDefaultBool =
                data.is_default === 1 ||
                data.is_default === true ||
                data.is_default === "1";
            defaultCheckbox.checked = isDefaultBool;
            console.log(
                `Setting checkbox for edit: is_default = ${data.is_default}, checked = ${isDefaultBool}`
            );
        } else {
            console.error("Checkbox defaultCheckbox tidak ditemukan!");
        }

        addAddressForm.scrollIntoView({ behavior: "smooth", block: "center" });
    }

    // Reset form
    function resetForm() {
        document.getElementById("namaInput").value = "";
        document.getElementById("alamatInput").value = "";
        document.getElementById("nomorInput").value = "";
        const defaultCheckbox = document.getElementById("defaultCheckbox");
        if (defaultCheckbox) {
            defaultCheckbox.checked = false;
        } else {
            console.error(
                "Checkbox defaultCheckbox tidak ditemukan saat reset!"
            );
        }
    }

    // Payment selection
    paymentCards.forEach((card) => {
        if (card.querySelector(".bi-plus-circle")) return;

        card.addEventListener("click", () => {
            paymentCards.forEach((c) => {
                if (!c.querySelector(".bi-plus-circle")) {
                    c.classList.remove("selected", "border-success");
                }
            });
            card.classList.add("selected", "border-success");
            selectedPayment = card;
            updatePayButtonState();
        });
    });

    // Update pay button state
    function updatePayButtonState() {
        if (selectedAddress && selectedPayment) {
            payButton.disabled = false;
            payButton.classList.remove("disabled");
        } else {
            payButton.disabled = true;
            payButton.classList.add("disabled");
        }
    }

    // Pay button handler
    // Pay button handler
    // Pay button handler
    payButton.addEventListener("click", async () => {
        if (!selectedAddress || !selectedPayment) {
            alert("Pilih alamat dan metode pembayaran terlebih dahulu!");
            return;
        }

        payButton.disabled = true;
        payButton.innerHTML =
            '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';

        try {
            // 1️⃣ Ambil data checkout dari localStorage
            const checkoutData =
                JSON.parse(localStorage.getItem("checkoutData")) || [];

            if (checkoutData.length === 0) {
                throw new Error("Tidak ada produk untuk checkout");
            }

            console.log("📦 Checkout data:", checkoutData);

            // 2️⃣ Format data items untuk API
            const items = checkoutData.map((item) => ({
                product_id: item.productId || item.id,
                jumlah: item.jumlah,
            }));

            console.log("📤 Items to send:", items);

            // 3️⃣ Kirim ke Laravel proxy yang forward ke Node.js API
            const orderResponse = await fetch("/api/node/orders", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": getCsrfToken(),
                    "X-Requested-With": "XMLHttpRequest",
                },
                credentials: "same-origin",
                body: JSON.stringify({
                    user_id: window.APP_USER_ID,
                    alamat_id: selectedAddress.id,
                    items: items,
                }),
            });

            console.log("📡 Order response status:", orderResponse.status);

            if (!orderResponse.ok) {
                const errorData = await orderResponse.json();
                throw new Error(
                    errorData.message || `HTTP ${orderResponse.status}`
                );
            }

            const orderResult = await orderResponse.json();
            console.log("✅ Order created:", orderResult);

            // 4️⃣ Proses pembayaran dengan Xendit
            const totalText = document.getElementById("orderTotal").textContent;
            const total = parseInt(totalText.replace(/[^0-9]/g, ""));

            const paymentResponse = await fetch("/checkout/pay", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": getCsrfToken(),
                    "X-Requested-With": "XMLHttpRequest",
                },
                credentials: "same-origin",
                body: JSON.stringify({
                    order_id: orderResult.data.order.id,
                    alamat_id: selectedAddress.id,
                    total: total,
                }),
            });

            if (!paymentResponse.ok) {
                const errorText = await paymentResponse.text();
                console.error("Payment error response:", errorText);
                throw new Error(
                    `Payment failed: HTTP ${paymentResponse.status}`
                );
            }

            const paymentData = await paymentResponse.json();
            console.log("💳 Payment data:", paymentData);

            if (paymentData.invoice_url) {
                // 5️⃣ Clear checkout data dari localStorage
                localStorage.removeItem("checkoutData");

                // 6️⃣ Redirect ke payment gateway
                showNotification(
                    "Order berhasil dibuat! Mengarahkan ke pembayaran...",
                    "success"
                );

                setTimeout(() => {
                    window.location.href = paymentData.invoice_url;
                }, 1500);
            } else {
                throw new Error("Invoice URL tidak ditemukan");
            }
        } catch (err) {
            console.error("❌ Payment error:", err);
            showNotification(
                `Gagal memproses pembayaran: ${err.message}`,
                "danger"
            );

            payButton.disabled = false;
            payButton.innerHTML =
                '<i class="bi bi-credit-card"></i> Bayar Sekarang';
        }
    });
    // Show notification
    function showNotification(message, type = "success") {
        const alertDiv = document.createElement("div");
        alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
        alertDiv.style.cssText =
            "top: 80px; right: 20px; z-index: 9999; min-width: 300px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);";
        alertDiv.innerHTML = `
      ${message}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

        document.body.appendChild(alertDiv);

        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    }

    // Initialize
    console.log("Initializing checkout page...");
    console.log("CSRF Token:", getCsrfToken() ? "Found" : "NOT FOUND!");

    loadAlamatFromDB();
    updatePayButtonState();
});