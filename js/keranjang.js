document.addEventListener("DOMContentLoaded", async () => {
  console.log("Load data keranjang dari:", window.location.href);

  const keranjangContainer = document.getElementById("keranjang-container");
  const totalItemEl = document.getElementById("total-item");
  const totalHargaEl = document.getElementById("total-harga");

  let keranjangUser = [];
  let produkData = [];
  const userId = 1;

  // === FUNGSI LOAD DATA ===
  async function loadData() {
    try {
      // SELALU ambil dari JSON file sebagai sumber default
      const keranjangRes = await fetch("JSON/keranjangData.json");
      const keranjangData = await keranjangRes.json();
      keranjangUser = keranjangData.filter((item) => item.userId === userId);
      
      // Simpan ke localStorage
      saveToLocalStorage();
      console.log("Data dimuat dari JSON (fresh data setiap refresh)");

      // Load produk data
      const produkRes = await fetch("JSON/productData.json");
      produkData = await produkRes.json();

      renderKeranjang();
    } catch (err) {
      console.error("Gagal load data keranjang:", err);
      keranjangContainer.innerHTML = `<p>Gagal memuat data keranjang.</p>`;
    }
  }

  // === FUNGSI SIMPAN KE LOCALSTORAGE ===
  function saveToLocalStorage() {
    localStorage.setItem(`keranjang_user_${userId}`, JSON.stringify(keranjangUser));
    console.log("Data disimpan ke localStorage");
  }

  // === FUNGSI RENDER (READ) ===
  function renderKeranjang() {
    keranjangContainer.innerHTML = "";
    let totalHarga = 0;
    let totalItem = 0;

    if (keranjangUser.length === 0) {
      keranjangContainer.innerHTML = `<p>Keranjang kosong.</p>`;
      totalItemEl.textContent = 0;
      totalHargaEl.textContent = "Rp 0";
      return;
    }

    keranjangUser.forEach((item, index) => {
      const produk = produkData.find((p) => p.id === item.produkId);
      if (!produk) return;

      const subtotal = produk.harga * item.jumlah;
      totalHarga += subtotal;
      totalItem += item.jumlah;

      const itemDiv = document.createElement("div");
      itemDiv.classList.add("item-keranjang");
      itemDiv.setAttribute("data-index", index);

      itemDiv.innerHTML = `
        <img src="${produk.imagePath}" alt="${produk.nama}" />
        <div class="info-produk">
          <h3>${produk.nama}</h3>
          <p class="harga">Rp ${produk.harga.toLocaleString("id-ID")}</p>
          <p class="deskripsi">${produk.deskripsi}</p>
        </div>
        <div class="kuantitas">
          <button class="btn-minus" data-index="${index}">-</button>
          <input type="number" value="${item.jumlah}" min="1" data-index="${index}" />
          <button class="btn-plus" data-index="${index}">+</button>
        </div>
        <button class="btn-hapus" data-index="${index}">Hapus</button>
      `;

      keranjangContainer.appendChild(itemDiv);
    });

    totalItemEl.textContent = totalItem;
    totalHargaEl.textContent = `Rp ${totalHarga.toLocaleString("id-ID")}`;

    setupEventListeners();
  }

  // === FUNGSI UPDATE RINGKASAN ===
  function updateRingkasan() {
    let totalHarga = 0;
    let totalItem = 0;

    keranjangUser.forEach((item) => {
      const produk = produkData.find((p) => p.id === item.produkId);
      if (produk) {
        totalHarga += produk.harga * item.jumlah;
        totalItem += item.jumlah;
      }
    });

    totalItemEl.textContent = totalItem;
    totalHargaEl.textContent = `Rp ${totalHarga.toLocaleString("id-ID")}`;
  }

  // === SETUP EVENT LISTENER (UPDATE & DELETE) ===
  function setupEventListeners() {
    // Event delegation untuk performa lebih baik
    keranjangContainer.addEventListener("click", (e) => {
      const target = e.target;
      const index = parseInt(target.getAttribute("data-index"));

      // TOMBOL MINUS (UPDATE)
      if (target.classList.contains("btn-minus")) {
        if (keranjangUser[index].jumlah > 1) {
          keranjangUser[index].jumlah--;
          saveToLocalStorage();
          
          // Update UI langsung tanpa full re-render
          const input = target.nextElementSibling;
          input.value = keranjangUser[index].jumlah;
          updateRingkasan();
        }
      }

      // TOMBOL PLUS (UPDATE)
      if (target.classList.contains("btn-plus")) {
        keranjangUser[index].jumlah++;
        saveToLocalStorage();
        
        // Update UI langsung
        const input = target.previousElementSibling;
        input.value = keranjangUser[index].jumlah;
        updateRingkasan();
      }

      // TOMBOL HAPUS (DELETE)
      if (target.classList.contains("btn-hapus")) {
        const produk = produkData.find(p => p.id === keranjangUser[index].produkId);
        const konfirmasi = confirm(`Hapus "${produk?.nama}" dari keranjang?`);
        
        if (konfirmasi) {
          keranjangUser.splice(index, 1);
          saveToLocalStorage();
          renderKeranjang();
        }
      }
    });

    // INPUT MANUAL (UPDATE)
    keranjangContainer.addEventListener("change", (e) => {
      if (e.target.type === "number") {
        const index = parseInt(e.target.getAttribute("data-index"));
        const val = parseInt(e.target.value);
        
        if (val > 0) {
          keranjangUser[index].jumlah = val;
          saveToLocalStorage();
          updateRingkasan();
        } else {
          // Jika input tidak valid, kembalikan nilai sebelumnya
          e.target.value = keranjangUser[index].jumlah;
        }
      }
    });
  }

  // === MULAI APLIKASI ===
  await loadData();
});