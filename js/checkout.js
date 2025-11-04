document.addEventListener("DOMContentLoaded", function () {
  // Ambil data checkout dari localStorage & tampilkan

  const checkoutData = JSON.parse(localStorage.getItem("checkoutData")) || [];

  // Elemen-elemen produk
  const itemImage = document.querySelector(".item-detail-image img");
  const itemName = document.querySelector(".item-detail-desc h3");
  const itemDesc = document.querySelector(".item-detail-desc p");
  const priceEl = document.querySelector(".price");
  const fromPriceEl = document.querySelector(".from-price");
  const discountEl = document.querySelector(".discount");
  const totalEl = document.querySelector(".item-detail-desc p:last-child");

  // Elemen detail harga di kanan
  const priceDetail = document.querySelector(".price-detail td.price");
  const discountDetail = document.querySelector(".discount-price");
  const totalDetail = document.querySelector(".price-detail-total td.price");

  if (checkoutData.length === 0) {
    itemName.textContent = "Tidak ada produk untuk di-checkout.";
  } else {
    const product = checkoutData[0];

    // Logika gambar & placeholder

    const imageContainer = document.querySelector(".item-detail-image");

    if (product.imagePath && product.imagePath.trim() !== "") {
      itemImage.src = product.imagePath;
      itemImage.onerror = function () {
        imageContainer.innerHTML = `
          <div style="
            width: 100%;
            height: 100%;
            background-color: #e0e0e0;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #777;
            font-size: 14px;
            border-radius: 8px;
          ">
            Tidak ada gambar
          </div>
        `;
      };
    } else {
      imageContainer.innerHTML = `
        <div style="
          width: 100%;
          height: 100%;
          background-color: #e0e0e0;
          display: flex;
          align-items: center;
          justify-content: center;
          color: #777;
          font-size: 14px;
          border-radius: 8px;
        ">
          Tidak ada gambar
        </div>
      `;
    }

    // Detail produk & harga

    itemName.textContent = product.nama;
    itemDesc.textContent = product.deskripsi || "Tidak ada deskripsi produk.";

    const hargaAsli =
      parseFloat(product.hargaAsli) || parseFloat(product.harga);
    const harga = parseFloat(product.harga);
    let diskon = parseFloat(product.diskon) || 0;
    const jumlah = parseInt(product.jumlah) || 1;

    // Jika diskon berbentuk desimal (misal 0.1), ubah ke persen
    if (diskon < 1) diskon = diskon * 100;

    const hargaSetelahDiskon = hargaAsli - (hargaAsli * diskon) / 100;
    const totalHarga = hargaSetelahDiskon * jumlah;

    // Tampilkan
    priceEl.textContent = formatRupiah(hargaSetelahDiskon);
    fromPriceEl.textContent =
      hargaAsli > hargaSetelahDiskon ? formatRupiah(hargaAsli) : "";
    discountEl.textContent = diskon > 0 ? `(${diskon}% Offer)` : "";
    totalEl.textContent = `Total : ${jumlah} pcs`;

    fromPriceEl.style.textDecoration = "line-through";
    fromPriceEl.style.color = "red";
    discountEl.style.color = "green";
    discountEl.style.marginLeft = "8px";

    // Update kanan
    priceDetail.textContent = formatRupiah(hargaSetelahDiskon);
    discountDetail.textContent =
      diskon > 0
        ? `- ${formatRupiah(hargaAsli - hargaSetelahDiskon)}`
        : "- Rp0";
    totalDetail.textContent = formatRupiah(totalHarga);
  }

  // Logika alamat & metode pembayaran

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

  // Load alamat awal dari JSON jika kosong
  if (alamatList.length === 0) {
    fetch("JSON/alamatData.json")
      .then((res) => res.json())
      .then((data) => {
        alamatList = data;
        localStorage.setItem("alamatList", JSON.stringify(alamatList));
        renderSemuaAlamat();
      })
      .catch((err) => console.error("Gagal memuat data alamat:", err));
  } else {
    renderSemuaAlamat();
  }

  // Render semua alamat
  function renderSemuaAlamat() {
    document.querySelectorAll(".address-card").forEach((c) => c.remove());
    alamatList.forEach((alamat, index) => {
      buatAddressCard(alamat.nama, alamat.alamat, alamat.nomor, index);
    });
    addAddressCard.style.display = alamatList.length > 2 ? "none" : "flex";
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
      <p class="mobile-no">Mobile No : ${nomor}</p>
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

  // Tambah alamat
  addAddressCard.addEventListener("click", () => {
    editIndex = null;
    addAddressCard.style.display = "none";
    addAddressForm.style.display = "flex";
    deleteAddressBtn.style.display = "none";
    resetForm();
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
      alamatList.push(dataBaru);
    }

    localStorage.setItem("alamatList", JSON.stringify(alamatList));
    renderSemuaAlamat();
    addAddressForm.style.display = "none";
  });

  cancelAddBtn.addEventListener("click", () => {
    addAddressForm.style.display = "none";
    if (alamatList.length < 3) {
      addAddressCard.style.display = "flex";
    }
  });

  deleteAddressBtn.addEventListener("click", () => {
    if (editIndex === null) return;
    if (confirm("Yakin ingin menghapus alamat ini?")) {
      alamatList.splice(editIndex, 1);
      localStorage.setItem("alamatList", JSON.stringify(alamatList));
      renderSemuaAlamat();
      addAddressForm.style.display = "none";
    }
  });

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
    } else {
      payButton.disabled = true;
      payButton.style.opacity = "0.6";
    }
  }

  payButton.addEventListener("click", () => {
    if (!selectedAddress || !selectedPayment) {
      alert("Pilih alamat dan metode pembayaran terlebih dahulu!");
      return;
    }

    alert("Pembayaran berhasil!");
    localStorage.removeItem("checkoutData");
    window.location.href = "homepage.html";
  });

  updatePayButtonState();
});

// 🔹 Fungsi bantu format Rupiah

function formatRupiah(angka) {
  const num = typeof angka === "string" ? parseFloat(angka) : angka;
  return num.toLocaleString("id-ID", {
    style: "currency",
    currency: "IDR",
    minimumFractionDigits: 0,
  });
}
