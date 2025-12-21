document.addEventListener("DOMContentLoaded", function () {
  const checkoutData = JSON.parse(localStorage.getItem("checkoutData")) || [];

  const itemContainer = document.querySelector(".col-lg-8");
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
    const itemCard = document.querySelector(".col-lg-8 .card:nth-child(2)");
    const itemCardBody = itemCard.querySelector(".card-body");

    const label = itemCardBody.querySelector("h6");
    itemCardBody.innerHTML = "";
    itemCardBody.appendChild(label);

    items.forEach((product) => {
      const hargaAsli =
        parseFloat(product.hargaAsli) || parseFloat(product.harga);
      const harga = parseFloat(product.harga);
      const diskon = parseFloat(product.diskon) || 0;
      const jumlah = parseInt(product.jumlah) || 1;

      let hargaSetelahDiskon = harga;
      if (diskon > 0) {
        const persenDiskon = diskon < 1 ? diskon : diskon / 100;
        hargaSetelahDiskon = Math.round(hargaAsli - hargaAsli * persenDiskon);
      }

      const itemDiv = document.createElement("div");
      itemDiv.className = "card mb-3 item-detail-card overflow-hidden";
      itemDiv.innerHTML = `
        <div class="row g-0 align-items-center h-100">
          <div class="col-md-3 bg-light h-100 d-flex align-items-center justify-content-center">
            <img src="${product.imagePath || "img/placeholder.png"}" 
                 alt="${product.nama}" 
                 class="img-fluid" 
                 style="max-height: 140px; width: 100%; object-fit: contain;"
                 onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22100%22 height=%22100%22%3E%3Crect fill=%22%23ddd%22 width=%22100%22 height=%22100%22/%3E%3Ctext fill=%22%23999%22 x=%2250%25%22 y=%2250%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 font-size=%2214%22%3ENo Image%3C/text%3E%3C/svg%3E';" />
          </div>
          <div class="col-md-9 h-100">
            <div class="card-body py-2 px-3 h-100 d-flex flex-column justify-content-center">
              <h5 class="card-title mb-1">${product.nama}</h5>
              <p class="card-text text-muted small mb-2">${
                product.deskripsi || "Tidak ada deskripsi produk."
              }</p>
              <div class="mb-1">
                <span class="fw-bold fs-6">${formatRupiah(
                  hargaSetelahDiskon
                )}</span>
                ${
                  hargaAsli > hargaSetelahDiskon
                    ? `<span class="text-decoration-line-through text-danger ms-2 small">${formatRupiah(
                        hargaAsli
                      )}</span>`
                    : ""
                }
                ${
                  diskon > 0
                    ? `<span class="badge bg-success ms-2 small">${
                        diskon < 1 ? diskon * 100 : diskon
                      }% Off</span>`
                    : ""
                }
              </div>
              <p class="mb-0 small text-muted">Jumlah: ${jumlah} pcs</p>
            </div>
          </div>
        </div>
      `;

      itemCardBody.appendChild(itemDiv);
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
        hargaSetelahDiskon = Math.round(hargaAsli - hargaAsli * persenDiskon);
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
  let alamatList = JSON.parse(localStorage.getItem("alamatList")) || [];
  let editIndex = null;

  if (alamatList.length === 0) {
    fetch("JSON/alamatData.json")
      .then((res) => res.json())
      .then((data) => {
        alamatList = data;
        localStorage.setItem("alamatList", JSON.stringify(alamatList));
        renderSemuaAlamat();
      })
      .catch((err) => {
        console.error("Gagal memuat data alamat:", err);
        renderSemuaAlamat();
      });
  } else {
    renderSemuaAlamat();
  }

  function renderSemuaAlamat() {
    document.querySelectorAll(".address-card").forEach((c) => c.remove());

    alamatList.forEach((alamat, index) => {
      buatAddressCard(alamat.nama, alamat.alamat, alamat.nomor, index);
    });

    const addCardCol = addAddressCard.closest(".col-md-6");
    if (alamatList.length >= 3) {
      addCardCol.style.display = "none";
    } else {
      addCardCol.style.display = "block";
    }
  }

  function buatAddressCard(nama, alamat, nomor, index) {
    const col = document.createElement("div");
    col.className = "col-md-6 col-xl-4 address-card";

    col.innerHTML = `
      <div class="card border-2 h-100 card-selectable" data-index="${index}">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start mb-2">
            <h6 class="mb-0 fw-bold">${nama}</h6>
            <span class="text-primary small edit-link" data-index="${index}">
              <i class="bi bi-pencil"></i> Edit
            </span>
          </div>
          <p class="text-muted small mb-2" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">${alamat}</p>
          <p class="text-muted small mb-0"><i class="bi bi-telephone"></i> ${nomor}</p>
        </div>
      </div>
    `;

    const card = col.querySelector(".card");
    card.addEventListener("click", (e) => {
      if (e.target.closest(".edit-link")) return;

      document
        .querySelectorAll(".card-selectable")
        .forEach((c) => c.classList.remove("selected"));
      card.classList.add("selected");
      selectedAddress = card;
      updatePayButtonState();
    });

    const editBtn = col.querySelector(".edit-link");
    editBtn.addEventListener("click", (e) => {
      e.stopPropagation();
      editIndex = index;
      tampilkanFormEdit(alamatList[index]);
    });

    addressContainer.insertBefore(col, addAddressCard.closest(".col-md-6"));
  }

  addAddressCard.addEventListener("click", () => {
    editIndex = null;
    addAddressForm.classList.remove("d-none");
    document.getElementById("formTitle").textContent = "Tambah Alamat Baru";
    deleteAddressBtn.classList.add("d-none");
    resetForm();

    addAddressForm.scrollIntoView({ behavior: "smooth", block: "center" });
  });

  saveAddressBtn.addEventListener("click", () => {
    const nama = document.getElementById("namaInput").value.trim();
    const alamat = document.getElementById("alamatInput").value.trim();
    const nomor = document.getElementById("nomorInput").value.trim();

    if (!nama || !alamat || !nomor) {
      alert("Lengkapi semua data alamat!");
      return;
    }

    const dataBaru = { nama, alamat, nomor };

    if (editIndex !== null) {
      alamatList[editIndex] = dataBaru;
      editIndex = null;
    } else {
      if (alamatList.length >= 3) {
        alert("Maksimal 3 alamat!");
        return;
      }
      alamatList.push(dataBaru);
    }

    localStorage.setItem("alamatList", JSON.stringify(alamatList));
    renderSemuaAlamat();
    addAddressForm.classList.add("d-none");

    showNotification("Alamat berhasil disimpan!", "success");
  });

  cancelAddBtn.addEventListener("click", () => {
    addAddressForm.classList.add("d-none");
    editIndex = null;
  });

  deleteAddressBtn.addEventListener("click", () => {
    if (editIndex === null) return;
    if (confirm("Yakin ingin menghapus alamat ini?")) {
      alamatList.splice(editIndex, 1);
      localStorage.setItem("alamatList", JSON.stringify(alamatList));
      renderSemuaAlamat();
      addAddressForm.classList.add("d-none");
      editIndex = null;

      showNotification("Alamat berhasil dihapus!", "info");
    }
  });

  function tampilkanFormEdit(data) {
    addAddressForm.classList.remove("d-none");
    document.getElementById("formTitle").textContent = "Edit Alamat";
    deleteAddressBtn.classList.remove("d-none");
    document.getElementById("namaInput").value = data.nama;
    document.getElementById("alamatInput").value = data.alamat;
    document.getElementById("nomorInput").value = data.nomor;

    addAddressForm.scrollIntoView({ behavior: "smooth", block: "center" });
  }

  function resetForm() {
    document.getElementById("namaInput").value = "";
    document.getElementById("alamatInput").value = "";
    document.getElementById("nomorInput").value = "";
  }

  paymentCards.forEach((card, index) => {
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

  function updatePayButtonState() {
    if (selectedAddress && selectedPayment) {
      payButton.disabled = false;
      payButton.classList.remove("disabled");
    } else {
      payButton.disabled = true;
      payButton.classList.add("disabled");
    }
  }

  payButton.addEventListener("click", () => {
    if (!selectedAddress || !selectedPayment) {
      alert("Pilih alamat dan metode pembayaran terlebih dahulu!");
      return;
    }

    payButton.innerHTML =
      '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';
    payButton.disabled = true;

    setTimeout(() => {
      alert("Pembayaran berhasil! Terima kasih sudah berbelanja di SpareHub.");

      localStorage.removeItem("checkoutData");

      const userId = 1;
      const savedCart = localStorage.getItem("keranjangData");
      if (savedCart) {
        let allCartData = JSON.parse(savedCart);
        allCartData = allCartData.filter((item) => item.userId !== userId);
        localStorage.setItem("keranjangData", JSON.stringify(allCartData));
        console.log("Keranjang dikosongkan setelah checkout");
      }

      window.location.href = "homepage.html";
    }, 1500);
  });

  function showNotification(message, type = "success") {
    const alertDiv = document.createElement("div");
    alertDiv.className = `alert alert-${
      type === "success" ? "success" : "info"
    } alert-dismissible fade show position-fixed`;
    alertDiv.style.cssText =
      "top: 80px; right: 20px; z-index: 9999; min-width: 300px;";
    alertDiv.innerHTML = `
      ${message}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    document.body.appendChild(alertDiv);

    setTimeout(() => {
      alertDiv.remove();
    }, 3000);
  }

  updatePayButtonState();
});