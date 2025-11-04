document.addEventListener("DOMContentLoaded", function () {
  // Ambil data checkout dari localStorage & tampilkan

  const checkoutData = JSON.parse(localStorage.getItem("checkoutData")) || [];

  // Container untuk multiple items
  const itemContainer = document.querySelector(".item-container");

  // Elemen detail harga di kanan
  const priceDetail = document.querySelector(".price-detail td.price");
  const deliveryCharges = document.querySelector(".delivery-charges");
  const discountDetail = document.querySelector(".discount-price");
  const totalDetail = document.querySelector(".price-detail-total td.price");

  if (checkoutData.length === 0) {
    itemContainer.innerHTML = `
      <p class="label">Item Detail</p>
      <div style="text-align: center; padding: 40px; color: #999;">
        <p style="font-size: 18px;">Tidak ada produk untuk checkout.</p>
        <a href="keranjang.html" style="color: #007bff; text-decoration: none;">
          ← Kembali ke Keranjang
        </a>
      </div>
    `;
    return;
  }

  // === RENDER SEMUA ITEM ===
  renderAllItems(checkoutData);

  // === HITUNG & UPDATE TOTAL ===
  updateOrderDetails(checkoutData);

  // === FUNCTION: RENDER ALL ITEMS ===
  function renderAllItems(items) {
    // Clear existing content
    itemContainer.innerHTML = '<p class="label">Item Detail</p>';

    items.forEach((product, index) => {
      // Hitung harga
      const hargaAsli =
        parseFloat(product.hargaAsli) || parseFloat(product.harga);
      const harga = parseFloat(product.harga);
      const diskon = parseFloat(product.diskon) || 0;
      const jumlah = parseInt(product.jumlah) || 1;

      // Hitung harga setelah diskon jika ada
      let hargaSetelahDiskon = harga;
      if (diskon > 0) {
        const persenDiskon = diskon < 1 ? diskon : diskon / 100;
        hargaSetelahDiskon = Math.round(hargaAsli - hargaAsli * persenDiskon);
      }

      const subtotal = hargaSetelahDiskon * jumlah;

      // Buat card untuk setiap item
      const itemCard = document.createElement("div");
      itemCard.className = "item-detail-card";
      itemCard.style.marginBottom = "15px";

      itemCard.innerHTML = `
        <div class="item-detail-image">
          <img src="${product.imagePath || "img/placeholder.png"}" 
               alt="${product.nama}"
               onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22100%22 height=%22100%22%3E%3Crect fill=%22%23ddd%22 width=%22100%22 height=%22100%22/%3E%3Ctext fill=%22%23999%22 x=%2250%25%22 y=%2250%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 font-size=%2214%22%3ENo Image%3C/text%3E%3C/svg%3E';" />
        </div>
        <div class="item-detail-desc">
          <h3>${product.nama}</h3>
          <p>${product.deskripsi || "Tidak ada deskripsi produk."}</p>
          <div class="price-wrapper">
            <span class="price">${formatRupiah(hargaSetelahDiskon)}</span>
            ${
              hargaAsli > hargaSetelahDiskon
                ? `<span class="from-price" style="text-decoration: line-through; color: red; font-size: 13px;">${formatRupiah(
                    hargaAsli
                  )}</span>`
                : ""
            }
            ${
              diskon > 0
                ? `<span class="discount" style="color: green;">(Lebih hemat ${
                    diskon < 1 ? diskon * 100 : diskon
                  }%)</span>`
                : ""
            }
          </div>
          <p style="margin-top: 10px;">Total: ${jumlah} pcs</p>
        </div>
      `;

      itemContainer.appendChild(itemCard);
    });
  }

  // === FUNCTION: UPDATE ORDER DETAILS ===
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

      // Hitung harga setelah diskon
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

    priceDetail.textContent = formatRupiah(totalHargaAsli);

    const biayaPengiriman = 0;
    deliveryCharges.textContent =
      biayaPengiriman === 0 ? "Gratis" : formatRupiah(biayaPengiriman);

    // Discount
    if (totalDiskon > 0) {
      discountDetail.textContent = `- ${formatRupiah(totalDiskon)}`;
      discountDetail.parentElement.style.display = "";
    } else {
      discountDetail.textContent = "- Rp 0";
      discountDetail.parentElement.style.display = "";
    }

    const totalAkhir = totalHargaSetelahDiskon + biayaPengiriman;
    totalDetail.textContent = formatRupiah(totalAkhir);
  }

  // Fungsi bantu format Rupiah
  function formatRupiah(angka) {
    const num = typeof angka === "string" ? parseFloat(angka) : angka;
    return num.toLocaleString("id-ID", {
      style: "currency",
      currency: "IDR",
      minimumFractionDigits: 0,
    });
  }
});

// Logika alamat & metode pembayaran

document.addEventListener("DOMContentLoaded", function () {
  const addressContainer = document.getElementById("addressContainer");
  const addAddressCard = document.getElementById("addAddressCard");
  const addAddressForm = document.getElementById("addAddressForm");
  const saveAddressBtn = document.getElementById("saveAddress");
  const cancelAddBtn = document.getElementById("cancelAdd");
  const deleteAddressBtn = document.getElementById("deleteAddress");

  const payButton = document.querySelector(".payment-button-container button");
  const paymentCards = document.querySelectorAll(".payment-card");

  let selectedAddress = null;
  let selectedPayment = null;
  let alamatList = JSON.parse(localStorage.getItem("alamatList")) || [];
  let editIndex = null;

  // Load alamat awal dari JSON jika localStorage kosong
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

  // Render semua alamat ke container
  function renderSemuaAlamat() {
    document.querySelectorAll(".address-card").forEach((c) => c.remove());
    alamatList.forEach((alamat, index) => {
      buatAddressCard(alamat.nama, alamat.alamat, alamat.nomor, index);
    });
    addAddressCard.style.display = alamatList.length >= 3 ? "none" : "flex";
  }

  // Buat kartu alamat
  function buatAddressCard(nama, alamat, nomor, index) {
    const card = document.createElement("div");
    card.classList.add("address-card");
    card.innerHTML = `
      <div class="address-header">
        <h3>${nama}</h3>
        <span class="edit-address-text" style="cursor:pointer; color:#007BFF;">Edit</span>
      </div>
      <p>${alamat}</p>
      <p class="mobile-no">No. Telepon : ${nomor}</p>
    `;

    // Klik kartu
    card.addEventListener("click", () => {
      document
        .querySelectorAll(".address-card")
        .forEach((c) => c.classList.remove("selected"));
      card.classList.add("selected");
      selectedAddress = card;
      updatePayButtonState();
    });

    // Edit
    const editBtn = card.querySelector(".edit-address-text");
    editBtn.addEventListener("click", (e) => {
      e.stopPropagation();
      editIndex = index;
      tampilkanFormEdit(alamatList[index]);
    });

    addressContainer.insertBefore(card, addAddressCard);
  }

  // Tampilkan form tambah alamat
  addAddressCard.addEventListener("click", () => {
    editIndex = null;
    addAddressCard.style.display = "none";
    addAddressForm.style.display = "flex";
    deleteAddressBtn.style.display = "none";
    resetForm();
  });

  // Simpan alamat baru atau hasil edit
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
      alamatList.push(dataBaru);
    }

    localStorage.setItem("alamatList", JSON.stringify(alamatList));
    renderSemuaAlamat();
    addAddressForm.style.display = "none";
  });

  // Batalkan tambah/edit alamat
  cancelAddBtn.addEventListener("click", () => {
    addAddressForm.style.display = "none";
    if (alamatList.length < 3) {
      addAddressCard.style.display = "flex";
    }
  });

  // Hapus alamat
  deleteAddressBtn.addEventListener("click", () => {
    if (editIndex === null) return;
    if (confirm("Yakin ingin menghapus alamat ini?")) {
      alamatList.splice(editIndex, 1);
      localStorage.setItem("alamatList", JSON.stringify(alamatList));
      renderSemuaAlamat();
      addAddressForm.style.display = "none";
    }
  });

  // Tampilkan form edit alamat
  function tampilkanFormEdit(data) {
    addAddressForm.style.display = "flex";
    addAddressCard.style.display = "none";
    deleteAddressBtn.style.display = "inline-block";
    document.getElementById("namaInput").value = data.nama;
    document.getElementById("alamatInput").value = data.alamat;
    document.getElementById("nomorInput").value = data.nomor;
  }

  function resetForm() {
    document.getElementById("namaInput").value = "";
    document.getElementById("alamatInput").value = "";
    document.getElementById("nomorInput").value = "";
  }

  // Pilih metode pembayaran
  paymentCards.forEach((card) => {
    card.addEventListener("click", () => {
      paymentCards.forEach((c) => c.classList.remove("selected"));
      card.classList.add("selected");
      selectedPayment = card;
      updatePayButtonState();
    });
  });

  // Update tombol Pay
  function updatePayButtonState() {
    if (selectedAddress && selectedPayment) {
      payButton.disabled = false;
      payButton.style.opacity = "1";
      payButton.style.cursor = "pointer";
    } else {
      payButton.disabled = true;
      payButton.style.opacity = "0.6";
      payButton.style.cursor = "not-allowed";
    }
  }

  // Klik Pay
  payButton.addEventListener("click", () => {
    if (!selectedAddress || !selectedPayment) {
      alert("Pilih alamat dan metode pembayaran terlebih dahulu!");
      return;
    }

    // Simulasi pembayaran berhasil
    alert("Pembayaran berhasil! Terima kasih sudah berbelanja di SpareHub.");

    // Hapus checkoutData dari localStorage
    localStorage.removeItem("checkoutData");

    // Kosongkan keranjang user setelah checkout berhasil
    const userId = 1;
    const savedCart = localStorage.getItem("keranjangData");
    if (savedCart) {
      let allCartData = JSON.parse(savedCart);
      // Hapus semua item user ini dari keranjang
      allCartData = allCartData.filter((item) => item.userId !== userId);
      localStorage.setItem("keranjangData", JSON.stringify(allCartData));
      console.log("Keranjang dikosongkan setelah checkout");
    }

    // Redirect ke homepage atau halaman sukses
    setTimeout(() => {
      window.location.href = "homepage.html";
    }, 500);
  });

  updatePayButtonState();
});
