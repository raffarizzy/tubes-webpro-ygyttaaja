document.addEventListener("DOMContentLoaded", async () => {
  console.log("Load data keranjang dari:", window.location.href);

  const keranjangContainer = document.getElementById("keranjang-container");
  const totalItemEl = document.getElementById("total-item");
  const totalHargaEl = document.getElementById("total-harga");
  const btnCheckout = document.querySelector(".btn-checkout");

  let keranjangUser = [];
  let produkData = [];
  const userId = 1;

  // === FUNGSI LOAD DATA ===
  async function loadData() {
    try {
      // PRIORITAS 1: Cek localStorage dulu (data dari detail-produk.js)
      const savedCart = localStorage.getItem('keranjangData');
      
      if (savedCart) {
        // Ambil dari localStorage
        const allCartData = JSON.parse(savedCart);
        keranjangUser = allCartData.filter((item) => item.userId === userId);
        console.log("Data dimuat dari localStorage (sinkron dengan detail-produk)");
      } else {
        // FALLBACK: Jika localStorage kosong, load dari JSON
        try {
          const keranjangRes = await fetch("JSON/keranjangData.json");
          const keranjangData = await keranjangRes.json();
          keranjangUser = keranjangData.filter((item) => item.userId === userId);
          
          // Simpan ke localStorage untuk konsistensi
          localStorage.setItem('keranjangData', JSON.stringify(keranjangData));
          console.log("Data dimuat dari JSON (fallback)");
        } catch (err) {
          console.log("Keranjang kosong (tidak ada data)");
          keranjangUser = [];
        }
      }

      // Load produk data
      const produkRes = await fetch("JSON/productData.json");
      produkData = await produkRes.json();

      renderKeranjang();
    } catch (err) {
      console.error("Gagal load data:", err);
      keranjangContainer.innerHTML = `<p>Gagal memuat data keranjang.</p>`;
    }
  }

  // === FUNGSI SIMPAN KE LOCALSTORAGE ===
  function saveToLocalStorage() {
    // PENTING: Simpan dengan key 'keranjangData' (sama dengan detail-produk.js)
    // Ambil semua data keranjang (termasuk user lain jika ada)
    const savedCart = localStorage.getItem('keranjangData');
    let allCartData = savedCart ? JSON.parse(savedCart) : [];
    
    // Hapus data user saat ini dari array
    allCartData = allCartData.filter(item => item.userId !== userId);
    
    // Tambahkan data user saat ini yang sudah diupdate
    allCartData.push(...keranjangUser);
    
    // Simpan kembali ke localStorage
    localStorage.setItem('keranjangData', JSON.stringify(allCartData));
    console.log("Data disimpan ke localStorage");
    
    // Update cart count di navbar (jika ada)
    updateCartCount();
  }

  // === FUNGSI UPDATE CART COUNT ===
  function updateCartCount() {
    const totalItems = keranjangUser.reduce((sum, item) => sum + item.jumlah, 0);
    const cartCountElement = document.getElementById('cart-count');
    if (cartCountElement) {
      cartCountElement.textContent = totalItems;
      cartCountElement.style.display = totalItems > 0 ? 'inline-block' : 'none';
    }
  }

  // === FUNGSI RENDER (READ) ===
  function renderKeranjang() {
    keranjangContainer.innerHTML = "";
    let totalHarga = 0;
    let totalItem = 0;

    if (keranjangUser.length === 0) {
      keranjangContainer.innerHTML = `
        <div style="text-align: center; padding: 40px; color: #999;">
          <p style="font-size: 18px;">Keranjang kosong.</p>
          <p>Yuk belanja dulu!</p>
          <a href="homepage.html" style="color: #007bff; text-decoration: none;">
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
        btnCheckout.style.opacity = '0.5';
        btnCheckout.style.cursor = 'not-allowed';
      }
      return;
    }

    keranjangUser.forEach((item, index) => {
      const produk = produkData.find((p) => p.id === item.produkId);
      if (!produk) {
        console.warn(`Produk dengan ID ${item.produkId} tidak ditemukan`);
        return;
      }

      const subtotal = produk.harga * item.jumlah;
      totalHarga += subtotal;
      totalItem += item.jumlah;

      const itemDiv = document.createElement("div");
      itemDiv.classList.add("item-keranjang");
      itemDiv.setAttribute("data-index", index);

      itemDiv.innerHTML = `
        <img src="${produk.imagePath}" 
             alt="${produk.nama}"
             onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22110%22 height=%22110%22 viewBox=%220 0 110 110%22%3E%3Crect width=%22110%22 height=%22110%22 fill=%22%23f1f3f4%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 font-family=%22Arial, sans-serif%22 font-size=%2214%22 fill=%22%235f6368%22%3ENo Image%3C/text%3E%3Cpath d=%22M35 40h40v5H35z M40 50h30v5H40z M45 60h20v5H45z%22 fill=%22%23dadce0%22/%3E%3C/svg%3E';" />
        <div class="info-produk">
          <h3>${produk.nama}</h3>
          <p class="harga">Rp ${produk.harga.toLocaleString("id-ID")}</p>
          <p class="deskripsi">${produk.deskripsi}</p>
          <p class="subtotal">Subtotal: <strong>Rp ${subtotal.toLocaleString("id-ID")}</strong></p>
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
    
    // Enable tombol checkout jika ada item
    if (btnCheckout) {
      btnCheckout.disabled = false;
      btnCheckout.style.opacity = '1';
      btnCheckout.style.cursor = 'pointer';
    }
    
    updateCartCount();
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

      if (isNaN(index)) return;

      // TOMBOL MINUS (UPDATE)
      if (target.classList.contains("btn-minus")) {
        if (keranjangUser[index].jumlah > 1) {
          keranjangUser[index].jumlah--;
          saveToLocalStorage();
          
          // Update UI langsung tanpa full re-render
          const itemDiv = target.closest('.item-keranjang');
          const input = itemDiv.querySelector('input[type="number"]');
          input.value = keranjangUser[index].jumlah;
          
          // Update subtotal
          const produk = produkData.find(p => p.id === keranjangUser[index].produkId);
          const subtotal = produk.harga * keranjangUser[index].jumlah;
          const subtotalEl = itemDiv.querySelector('.subtotal strong');
          subtotalEl.textContent = `Rp ${subtotal.toLocaleString("id-ID")}`;
          
          updateRingkasan();
        }
      }

      // TOMBOL PLUS (UPDATE)
      if (target.classList.contains("btn-plus")) {
        const produk = produkData.find(p => p.id === keranjangUser[index].produkId);
        
        // Cek stok
        if (keranjangUser[index].jumlah >= produk.stok) {
          alert(`Stok ${produk.nama} hanya tersedia ${produk.stok} item`);
          return;
        }
        
        keranjangUser[index].jumlah++;
        saveToLocalStorage();
        
        // Update UI langsung
        const itemDiv = target.closest('.item-keranjang');
        const input = itemDiv.querySelector('input[type="number"]');
        input.value = keranjangUser[index].jumlah;
        
        // Update subtotal
        const subtotal = produk.harga * keranjangUser[index].jumlah;
        const subtotalEl = itemDiv.querySelector('.subtotal strong');
        subtotalEl.textContent = `Rp ${subtotal.toLocaleString("id-ID")}`;
        
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
          
          // Show notification
          showNotification(`"${produk?.nama}" berhasil dihapus dari keranjang`, 'info');
        }
      }
    });

    // INPUT MANUAL (UPDATE)
    keranjangContainer.addEventListener("change", (e) => {
      if (e.target.type === "number") {
        const index = parseInt(e.target.getAttribute("data-index"));
        const val = parseInt(e.target.value);
        const produk = produkData.find(p => p.id === keranjangUser[index].produkId);
        
        if (val > 0 && val <= produk.stok) {
          keranjangUser[index].jumlah = val;
          saveToLocalStorage();
          
          // Update subtotal
          const itemDiv = e.target.closest('.item-keranjang');
          const subtotal = produk.harga * val;
          const subtotalEl = itemDiv.querySelector('.subtotal strong');
          subtotalEl.textContent = `Rp ${subtotal.toLocaleString("id-ID")}`;
          
          updateRingkasan();
        } else if (val > produk.stok) {
          alert(`Stok ${produk.nama} hanya tersedia ${produk.stok} item`);
          e.target.value = keranjangUser[index].jumlah;
        } else {
          // Jika input tidak valid, kembalikan nilai sebelumnya
          e.target.value = keranjangUser[index].jumlah;
        }
      }
    });
  }

  // === TOMBOL CHECKOUT - KIRIM KE CHECKOUT.HTML ===
  if (btnCheckout) {
    btnCheckout.addEventListener('click', (e) => {
      // Cek apakah keranjang kosong
      if (keranjangUser.length === 0) {
        e.preventDefault();
        alert('Keranjang Anda kosong! Silakan tambahkan produk terlebih dahulu.');
        return;
      }

      // Convert keranjang user ke format checkoutData
      const checkoutData = keranjangUser.map(item => {
        const produk = produkData.find(p => p.id === item.produkId);
        if (!produk) return null;

        return {
          nama: produk.nama,
          harga: produk.harga,
          hargaAsli: produk.hargaAsli || produk.harga,
          diskon: produk.diskon || 0,
          jumlah: item.jumlah,
          imagePath: produk.imagePath,
          deskripsi: produk.deskripsi
        };
      }).filter(item => item !== null); // Remove null items

      // Simpan ke localStorage untuk dibaca di checkout.html
      localStorage.setItem('checkoutData', JSON.stringify(checkoutData));
      console.log('Data checkout disimpan:', checkoutData);

      // Redirect ke checkout.html (link dari <a> tag akan handle ini)
    });
  }

  // === NOTIFICATION SYSTEM ===
  function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.style.cssText = `
      position: fixed;
      top: 80px;
      right: 20px;
      background-color: ${type === 'success' ? '#28a745' : type === 'info' ? '#17a2b8' : '#dc3545'};
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
      notification.style.animation = 'slideOut 0.3s ease-out';
      setTimeout(() => {
        if (notification.parentNode) {
          document.body.removeChild(notification);
        }
      }, 300);
    }, 3000);
  }

  // === MULAI APLIKASI ===
  await loadData();
});