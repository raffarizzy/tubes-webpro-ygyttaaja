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
          <a href="/keranjang" class="btn btn-primary mt-3">
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

    window.updateOrderDetails = function (items) {
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

        orderPrice.textContent = window.formatRupiah(totalHargaAsli);

        const shippingInput = document.getElementById("shippingCostInput");
        const biayaPengiriman = shippingInput ? parseInt(shippingInput.value) : 0;

        orderDelivery.textContent =
            biayaPengiriman === 0 ? "Gratis" : window.formatRupiah(biayaPengiriman);
        orderDelivery.className =
            biayaPengiriman === 0
                ? "text-end text-success fw-bold"
                : "text-end fw-bold";

        orderDiscount.textContent =
            totalDiskon > 0 ? `- ${window.formatRupiah(totalDiskon)}` : "- Rp 0";

        const totalAkhir = totalHargaSetelahDiskon + biayaPengiriman;
        orderTotal.textContent = window.formatRupiah(totalAkhir);
    }

    window.formatRupiah = function (angka) {
        const num = typeof angka === "string" ? parseFloat(angka) : angka;
        return num.toLocaleString("id-ID", {
            style: "currency",
            currency: "IDR",
            minimumFractionDigits: 0,
        });
    }

    renderAllItems(checkoutData);
    window.updateOrderDetails(checkoutData);
});

// ============= PAYMENT METHODS MANAGEMENT =============
document.addEventListener("DOMContentLoaded", function () {
    const paymentMethodsContainer = document.getElementById(
        "paymentMethodsContainer"
    );

    let selectedPaymentMethod = null;

    // Available payment methods with official Duitku codes
    const paymentMethods = [
        {
            id: "SP", // ShopeePay / QRIS
            name: "QRIS",
            description: "Scan & Bayar dengan QRIS",
            image: "https://blogger.googleusercontent.com/img/b/R29vZ2xl/AVvXsEjhvTtjN1Bj37W3jTiire9jlqgP046Je6-JPvIVEMjW6avji3kH1eC5HyUDIY8q1l6z89kidy_XZz4cX7-d_rdSentSrY94naUFcRo-NhiEvMUWmevEbQz-xRdMLUFSr61dHVvbVDq58GmxM0UAIgwnfCak8KWr0wTa0UmmjdUQTTcm2pEd3YjuHtPj9Q/s2161/Logo%20QRIS.png",
            color: "#1a73e8",
        },
        {
            id: "BC", // BCA Virtual Account
            name: "BCA",
            description: "Transfer via Virtual Account BCA",
            image: "https://iconape.com/wp-content/png_logo_vector/bca-bank-central-asia.png",
            color: "#003d7a",
        },
        {
            id: "VC", // Credit Card (Visa/Mastercard)
            name: "VISA",
            description: "Bayar dengan Kartu Kredit/Debit VISA",
            image: "https://www.freepnglogos.com/uploads/visa-inc-png-18.png",
            color: "#ff6b00",
        },
    ];

    // Render payment methods
    function renderPaymentMethods() {
        paymentMethodsContainer.innerHTML = "";

        const row = document.createElement("div");
        row.className = "row g-3";

        paymentMethods.forEach((method) => {
            const col = document.createElement("div");
            col.className = "col-md-4";

            col.innerHTML = `
                <div class="card payment-method-card h-100" data-method="${method.id}">
                    <div class="card-body p-3 text-center">
                        <div class="payment-logo-container mb-3">
                            <img src="${method.image}" alt="${method.name}" class="payment-method-icon" 
                                 onerror="this.onerror=null; this.src='https://via.placeholder.com/80x80/cccccc/666666?text=${method.name}';">
                        </div>
                        <div>
                            <h6 class="mb-1 fw-semibold">${method.name}</h6>
                            <p class="mb-2 text-muted small">${method.description}</p>
                        </div>
                        <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input" type="radio" name="paymentMethod" id="payment-${method.id}" value="${method.id}">
                        </div>
                    </div>
                </div>
            `;

            row.appendChild(col);
        });

        paymentMethodsContainer.appendChild(row);

        // Add event listeners
        document.querySelectorAll(".payment-method-card").forEach((card) => {
            card.addEventListener("click", function () {
                const methodId = this.dataset.method;
                selectPaymentMethod(methodId);
            });
        });
    }

    // Select payment method
    function selectPaymentMethod(methodId) {
        // Unselect all
        document.querySelectorAll(".payment-method-card").forEach((card) => {
            card.classList.remove("selected");
        });

        document
            .querySelectorAll('input[name="paymentMethod"]')
            .forEach((radio) => {
                radio.checked = false;
            });

        // Select the clicked one
        const selectedCard = document.querySelector(
            `.payment-method-card[data-method="${methodId}"]`
        );
        if (selectedCard) {
            selectedCard.classList.add("selected");
            const radio = selectedCard.querySelector('input[type="radio"]');
            if (radio) radio.checked = true;
        }

        selectedPaymentMethod = paymentMethods.find((m) => m.id === methodId);

        console.log("Selected payment method:", selectedPaymentMethod);

        // Update pay button state
        updatePayButtonState();
    }

    // Initialize
    renderPaymentMethods();
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
    const paymentHint = document.getElementById("paymentHint");

    const provinsiSelect = document.getElementById("provinsiSelect");
    const kotaSelect = document.getElementById("kotaSelect");
    const kecamatanSelect = document.getElementById("kecamatanSelect");

    let selectedAddress = null;
    let selectedPayment = null;
    let alamatList = [];
    let editIndex = null;

    // --- Wilayah Cascading Logic ---
    async function fetchWilayah(url) {
        try {
            const response = await fetch(url);
            return await response.json();
        } catch (error) {
            console.error("Error fetching wilayah:", error);
            return [];
        }
    }

    async function loadProvinces() {
        const provinces = await fetchWilayah("/api/wilayah/provinsi");
        provinsiSelect.innerHTML = '<option value="">Pilih Provinsi</option>';
        provinces.forEach((p) => {
            const option = document.createElement("option");
            option.value = p.kode;
            option.textContent = p.nama;
            provinsiSelect.appendChild(option);
        });
    }

    provinsiSelect.addEventListener("change", async function () {
        const provinceCode = this.value;
        kotaSelect.innerHTML = '<option value="">Pilih Kota</option>';
        kecamatanSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';
        kecamatanSelect.disabled = true;

        if (provinceCode) {
            const cities = await fetchWilayah(`/api/wilayah/kota/${provinceCode}`);
            cities.forEach((c) => {
                const option = document.createElement("option");
                option.value = c.kode;
                option.textContent = c.nama;
                kotaSelect.appendChild(option);
            });
            kotaSelect.disabled = false;
        } else {
            kotaSelect.disabled = true;
        }
    });

    kotaSelect.addEventListener("change", async function () {
        const cityCode = this.value;
        kecamatanSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';

        if (cityCode) {
            const districts = await fetchWilayah(`/api/wilayah/kecamatan/${cityCode}`);
            districts.forEach((d) => {
                const option = document.createElement("option");
                option.value = d.kode;
                option.textContent = d.nama;
                kecamatanSelect.appendChild(option);
            });
            kecamatanSelect.disabled = false;
        } else {
            kecamatanSelect.disabled = true;
        }
    });

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

                setTimeout(() => {
                    window.location.href =
                        "/login?redirect=" +
                        encodeURIComponent(window.location.pathname);
                }, 2000);

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
                alamat.is_default,
                alamat.provinsi,
                alamat.kota,
                alamat.kecamatan
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
    function buatAddressCard(nama, alamat, nomor, id, isDefault = false, provinsi = '', kota = '', kecamatan = '') {
        const col = document.createElement("div");
        col.className = "col-md-6 col-xl-4 address-card";

        const isDefaultBool =
            isDefault === 1 || isDefault === true || isDefault === "1";

        const defaultBadge = isDefaultBool
            ? '<span class="badge bg-success ms-2"><i class="bi bi-star-fill"></i> Default</span>'
            : "";
        
        const regionText = [kecamatan, kota, provinsi].filter(Boolean).join(', ');

        col.innerHTML = `
      <div class="card border-2 h-100 card-selectable" data-id="${id}">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start mb-2">
            <h6 class="mb-0 fw-bold">${nama}${defaultBadge}</h6>
            <span class="text-primary small edit-link" data-id="${id}" style="cursor: pointer;">
              <i class="bi bi-pencil"></i> Edit
            </span>
          </div>
          <div class="text-muted small mb-2">
            <p class="mb-1 text-dark fw-medium">${alamat}</p>
            <p class="mb-0 italic"><i class="bi bi-geo-alt"></i> ${regionText || '-'}</p>
          </div>
          <p class="text-muted small mb-0"><i class="bi bi-telephone"></i> ${nomor}</p>
        </div>
      </div>
    `;

        const card = col.querySelector(".card");

        // Auto-select alamat default
        if (isDefaultBool) {
            card.classList.add("selected");
            selectedAddress = { id, nama, alamat, nomor };

            // Trigger shipping rates for default address
            const alamatData = alamatList.find(a => a.id == id);
            if (alamatData && alamatData.kode_wilayah) {
                loadShippingRates(alamatData.kode_wilayah);
            }

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

            // Trigger shipping rates calculation
            const alamatData = alamatList.find(a => a.id == id);
            if (alamatData && alamatData.kode_wilayah) {
                loadShippingRates(alamatData.kode_wilayah);
            }

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
        loadProvinces();

        addAddressForm.scrollIntoView({ behavior: "smooth", block: "center" });
    });

    // Simpan alamat (create/update)
    saveAddressBtn.addEventListener("click", async () => {
        const nama = document.getElementById("namaInput").value.trim();
        const alamat = document.getElementById("alamatInput").value.trim();
        const nomor = document.getElementById("nomorInput").value.trim();
        const isDefaultCheckbox = document.getElementById("defaultCheckbox");
        const isDefault = isDefaultCheckbox ? isDefaultCheckbox.checked : false;

        const getSelectedText = (selectEl) => {
            if (!selectEl || selectEl.selectedIndex === -1) return "";
            const option = selectEl.options[selectEl.selectedIndex];
            if (!option || option.value === "") return "";
            return option.text;
        };

        const provinsi = getSelectedText(provinsiSelect);
        const kota = getSelectedText(kotaSelect);
        const kecamatan = getSelectedText(kecamatanSelect);
        const kode_wilayah = kecamatanSelect.value || kotaSelect.value || provinsiSelect.value || "";

        console.log("Saving Alamat - Cleaned Data:", { provinsi, kota, kecamatan, kode_wilayah });

        if (!nama || !alamat || !nomor || !provinsi || !kota || !kecamatan) {
            alert("Lengkapi semua data alamat termasuk wilayah (Provinsi, Kota, dan Kecamatan)!");
            return;
        }

        const dataBaru = {
            nama_penerima: nama,
            alamat: alamat,
            nomor_penerima: nomor,
            provinsi: provinsi,
            kota: kota,
            kecamatan: kecamatan,
            kode_wilayah: kode_wilayah,
            is_default: isDefault ? 1 : 0,
        };

        try {
            saveAddressBtn.disabled = true;
            const originalText = saveAddressBtn.innerHTML;
            saveAddressBtn.innerHTML =
                '<span class="spinner-border spinner-border-sm me-2"></span>Menyimpan...';

            let response;
            let url;
            let method;

            if (editIndex !== null) {
                url = `/alamat/${editIndex}`;
                method = "PUT";
                console.log(`Updating alamat ID: ${editIndex}`);
            } else {
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
    async function tampilkanFormEdit(data) {
        console.log("Editing alamat:", data);

        addAddressForm.classList.remove("d-none");
        document.getElementById("formTitle").textContent = "Edit Alamat";
        deleteAddressBtn.classList.remove("d-none");
        document.getElementById("namaInput").value = data.nama_penerima;
        document.getElementById("alamatInput").value = data.alamat;
        document.getElementById("nomorInput").value = data.nomor_penerima;

        // Repopulate regions
        await loadProvinces();
        if (data.kode_wilayah) {
            const provinceCode = data.kode_wilayah.substring(0, 2);
            const cityCode = data.kode_wilayah.substring(0, 5);
            const districtCode = data.kode_wilayah;

            provinsiSelect.value = provinceCode;
            
            // Load cities
            const cities = await fetchWilayah(`/api/wilayah/kota/${provinceCode}`);
            kotaSelect.innerHTML = '<option value="">Pilih Kota</option>';
            cities.forEach((c) => {
                const option = document.createElement("option");
                option.value = c.kode;
                option.textContent = c.nama;
                kotaSelect.appendChild(option);
            });
            kotaSelect.value = cityCode;
            kotaSelect.disabled = false;

            // Load districts
            const districts = await fetchWilayah(`/api/wilayah/kecamatan/${cityCode}`);
            kecamatanSelect.innerHTML = '<option value="">Pilih Kecamatan</option>';
            districts.forEach((d) => {
                const option = document.createElement("option");
                option.value = d.kode;
                option.textContent = d.nama;
                kecamatanSelect.appendChild(option);
            });
            kecamatanSelect.value = districtCode;
            kecamatanSelect.disabled = false;
        }

        const defaultCheckbox = document.getElementById("defaultCheckbox");
        if (defaultCheckbox) {
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
        }
    }

    // Update pay button state
    window.updatePayButtonState = function () {
        const courierSelected = document.getElementById("courierCodeInput")?.value !== "";

        if (selectedAddress && courierSelected) {
            payButton.disabled = false;
            payButton.classList.remove("disabled");
            paymentHint.textContent = "Siap untuk melanjutkan pembayaran";
            paymentHint.classList.remove("text-muted");
            paymentHint.classList.add("text-success");
        } else {
            payButton.disabled = true;
            payButton.classList.add("disabled");

            if (!selectedAddress) {
                paymentHint.textContent = "Pilih alamat pengiriman";
            } else if (!courierSelected) {
                paymentHint.textContent = "Pilih metode pengiriman";
            }
            paymentHint.classList.remove("text-success");
            paymentHint.classList.add("text-muted");
        }
    };

    // --- Shipping (KlikResi) Logic ---
    async function loadShippingRates(destinationId) {
        const container = document.getElementById("shippingOptionsContainer");
        container.innerHTML = `
            <div class="text-center py-3">
                <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                <p class="text-muted small mt-2 mb-0">Menghitung ongkir...</p>
            </div>
        `;

        try {
            // Fetch checkoutData directly from localStorage to avoid scope issues
            const currentCheckoutData = JSON.parse(localStorage.getItem("checkoutData")) || [];
            
            if (currentCheckoutData.length === 0) {
                throw new Error("Data checkout tidak ditemukan");
            }

            const response = await fetch("/api/shipping/rates", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": getCsrfToken(),
                },
                body: JSON.stringify({ 
                    destination_id: destinationId,
                    items: currentCheckoutData 
                }),
            });

            const result = await response.json();
            if (!result.success) throw new Error(result.message);

            renderShippingOptions(result.data, result.weight);
        } catch (error) {
            console.error("Shipping rates error:", error);
            container.innerHTML = `
                <div class="alert alert-danger small py-2">
                    <i class="bi bi-exclamation-triangle"></i> Gagal memuat ongkir: ${error.message}
                </div>
            `;
        }
    }

    function renderShippingOptions(rawResponse, weight) {
        const container = document.getElementById("shippingOptionsContainer");
        container.innerHTML = "";

        // Based on user example, the data is in rawResponse.data.pricing
        const pricing = rawResponse.data?.pricing || [];

        if (pricing.length === 0) {
            container.innerHTML = '<p class="text-muted small text-center">Tidak ada kurir tersedia untuk wilayah ini.</p>';
            return;
        }

        const listGroup = document.createElement("div");
        listGroup.className = "list-group list-group-flush border rounded";

        pricing.forEach((rate) => {
            const item = document.createElement("button");
            item.type = "button";
            item.className = "list-group-item list-group-item-action p-3 d-flex justify-content-between align-items-center shipping-item";
            item.dataset.courierCode = rate.courier_code;
            item.dataset.courierName = rate.courier_name;
            item.dataset.serviceName = rate.service;
            item.dataset.cost = rate.price;

            item.innerHTML = `
                <div>
                    <div class="fw-bold small">${rate.courier_name} - ${rate.service}</div>
                    <div class="text-muted" style="font-size: 0.7rem;">
                        Estimasi: ${rate.duration || '-'} (${weight}kg)
                    </div>
                </div>
                <div class="fw-bold text-primary">Rp ${rate.price.toLocaleString("id-ID")}</div>
            `;

            item.addEventListener("click", function() {
                selectShipping(this);
            });

            listGroup.appendChild(item);
        });

        container.appendChild(listGroup);
    }

    function selectShipping(element) {
        // Unselect all
        document.querySelectorAll(".shipping-item").forEach(el => el.classList.remove("active", "bg-primary-subtle"));
        
        // Select this one
        element.classList.add("active", "bg-primary-subtle");

        // Set hidden inputs
        document.getElementById("courierCodeInput").value = element.dataset.courierCode;
        document.getElementById("courierNameInput").value = element.dataset.courierName;
        document.getElementById("serviceNameInput").value = element.dataset.serviceName;
        document.getElementById("shippingCostInput").value = element.dataset.cost;

        // Update UI Summary
        const cost = parseInt(element.dataset.cost);
        const orderDelivery = document.getElementById("orderDelivery");
        orderDelivery.textContent = formatRupiah(cost);
        orderDelivery.classList.remove("text-success");
        orderDelivery.classList.add("fw-bold");

        // Re-calculate Total
        const currentCheckoutData = JSON.parse(localStorage.getItem("checkoutData")) || [];
        updateOrderDetails(currentCheckoutData);
        updatePayButtonState();
    }

    // Pay button handler
    payButton.addEventListener("click", async () => {
        const courierCode = document.getElementById("courierCodeInput").value;
        const shippingCost = parseInt(document.getElementById("shippingCostInput").value);

        if (!selectedAddress || !courierCode) {
            alert("Lengkapi alamat dan pengiriman!");
            return;
        }

        payButton.disabled = true;
        payButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';

        try {
            const currentCheckoutData = JSON.parse(localStorage.getItem("checkoutData")) || [];
            
            if (currentCheckoutData.length === 0) {
                throw new Error("Tidak ada produk untuk checkout");
            }

            const items = currentCheckoutData.map((item) => ({
                product_id: item.productId || item.id,
                jumlah: item.jumlah,
            }));

            // Calculate total price with shipping
            const totalText = document.getElementById("orderTotal").textContent;
            const total = parseInt(totalText.replace(/[^0-9]/g, ""));

            // Kirim ke API untuk create order
            const orderResponse = await fetch("/api/orders", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": getCsrfToken(),
                },
                body: JSON.stringify({
                    alamat_id: selectedAddress.id,
                    items: items,
                    courier_code: courierCode,
                    courier_name: document.getElementById("courierNameInput").value,
                    service_name: document.getElementById("serviceNameInput").value,
                    shipping_cost: shippingCost,
                }),
            });

            if (!orderResponse.ok) throw new Error("Gagal membuat order");
            const orderResult = await orderResponse.json();
            const orderId = orderResult.data.order.id;

            // Proses pembayaran dengan Xendit
            const paymentResponse = await fetch("/checkout/pay", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": getCsrfToken(),
                },
                body: JSON.stringify({
                    order_id: orderId,
                    alamat_id: selectedAddress.id,
                    total: total,
                    payment_method: '', // Biarkan Duitku POP yang menangani pilihan metode
                }),
            });

            if (!paymentResponse.ok) throw new Error("Gagal memproses pembayaran");
            const paymentData = await paymentResponse.json();

            if (paymentData.reference) {
                // INTEGRASI DUITKU POP
                checkout.process(paymentData.reference, {
                    defaultLanguage: "id",
                    successEvent: function(result) {
                        console.log('Duitku Success:', result);
                        localStorage.removeItem("checkoutData");
                        showNotification("Pembayaran Berhasil!", "success");
                        setTimeout(() => window.location.href = "/riwayat-pesanan", 2000);
                    },
                    pendingEvent: function(result) {
                        console.log('Duitku Pending:', result);
                        localStorage.removeItem("checkoutData");
                        showNotification("Menunggu Pembayaran", "info");
                        setTimeout(() => window.location.href = "/riwayat-pesanan", 2000);
                    },
                    errorEvent: function(result) {
                        console.error('Duitku Error:', result);
                        showNotification("Gagal memproses pembayaran: " + (result.statusMessage || "Unknown error"), "danger");
                        payButton.disabled = false;
                        payButton.innerHTML = '<i class="bi bi-credit-card"></i> Bayar Sekarang';
                    },
                    closeEvent: function(result) {
                        console.log('Duitku Closed:', result);
                        payButton.disabled = false;
                        payButton.innerHTML = '<i class="bi bi-credit-card"></i> Bayar Sekarang';
                    }
                });
            }
        } catch (err) {
            console.error(err);
            showNotification(err.message, "danger");
            payButton.disabled = false;
            payButton.innerHTML = '<i class="bi bi-credit-card"></i> Bayar Sekarang';
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
