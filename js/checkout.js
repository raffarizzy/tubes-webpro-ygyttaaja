document.addEventListener("DOMContentLoaded", function () {
  const addressCards = document.querySelectorAll(".address-card");
  const paymentCards = document.querySelectorAll(".payment-card");
  const payButton = document.querySelector(".payment-button-container button");

  let selectedAddress = null;
  let selectedPayment = null;

  // --- Fungsi untuk reset dan pilih address ---
  addressCards.forEach((card) => {
    card.addEventListener("click", () => {
      addressCards.forEach((c) => c.classList.remove("selected"));
      card.classList.add("selected");
      selectedAddress = card;
      updatePayButtonState();
    });
  });

  // --- Fungsi untuk reset dan pilih payment ---
  paymentCards.forEach((card) => {
    card.addEventListener("click", () => {
      paymentCards.forEach((c) => c.classList.remove("selected"));
      card.classList.add("selected");
      selectedPayment = card;
      updatePayButtonState();
    });
  });

  // --- Saat klik tombol bayar ---
  payButton.addEventListener("click", function () {
    if (!selectedAddress || !selectedPayment) {
      alert("Pilih alamat dan metode pembayaran terlebih dahulu!");
      return;
    }
    alert("Pembayaran berhasil!");
  });

  // Default: tombol dinonaktifkan dulu
  updatePayButtonState();
});
