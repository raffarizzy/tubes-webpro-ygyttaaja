// --- Tombol Aksi ---
const btnKeranjang = document.getElementById("btn-Keranjang");
const btnBeli = document.getElementById("btn-Beli");
const cartCount = document.getElementById("cart-count");

// Jumlah awal keranjang
let count = 0;
cartCount.textContent = count;

// Event: Tambah ke keranjang
btnKeranjang.addEventListener("click", () => {
  count++;
  cartCount.textContent = count;
  localStorage.setItem("cartCount", count);
  alert("Barang berhasil ditambahkan ke keranjang!");
});

// Event: Beli langsung
btnBeli.addEventListener("click", () => {
  alert("Pembelian berhasil! Terima kasih sudah berbelanja di SpareHub");
});
