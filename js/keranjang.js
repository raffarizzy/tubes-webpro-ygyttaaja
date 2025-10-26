document.addEventListener("DOMContentLoaded", function () {
    const items = document.querySelectorAll(".item-keranjang");
    const totalItemEl = document.getElementById("total-item");
    const totalHargaEl = document.getElementById("total-harga");

    // Fungsi untuk format angka jadi "Rp xxx.xxx"
    function formatRupiah(angka) {
        return new Intl.NumberFormat("id-ID", {
            style: "currency",
            currency: "IDR",
            minimumFractionDigits: 0
        }).format(angka);
    }


    // Fungsi untuk update total item dan total harga
    function updateRingkasan() {
        var totalItem = 0;
        var totalHarga = 0;

        document.querySelectorAll(".item-keranjang").forEach(item => {
            const qty = parseInt(item.querySelector("input").value);
            const harga = parseInt(item.dataset.harga); // Ambil harga dari data attribute
            totalItem += qty;
            totalHarga += harga * qty;
        });

        totalItemEl.textContent = totalItem;
        totalHargaEl.textContent = formatRupiah(totalHarga);
    }

    // Tambahkan event listener ke setiap tombol
    items.forEach(item => {
        const minusBtn = item.querySelector(".btn-minus");
        const plusBtn = item.querySelector(".btn-plus");
        const qtyInput = item.querySelector("input");
        const hapusBtn = item.querySelector(".btn-hapus");

        minusBtn.addEventListener("click", () => {
            var val = parseInt(qtyInput.value);
            if (val > 1) {
                qtyInput.value = val - 1;
                updateRingkasan();
            }
        });

        plusBtn.addEventListener("click", () => {
            qtyInput.value = parseInt(qtyInput.value) + 1;
            updateRingkasan();
        });

        qtyInput.addEventListener("change", () => {
            if (qtyInput.value < 1) qtyInput.value = 1;
            updateRingkasan();
        });

        hapusBtn.addEventListener("click", () => {
            item.remove();
            updateRingkasan();
        });
    });

    // Update pertama kali saat halaman dibuka
    updateRingkasan();
});
