document.addEventListener("DOMContentLoaded", function () {
    const items = document.querySelectorAll(".item-keranjang");

    // Tambahkan event listener ke tombol hapus dan input jumlah
    items.forEach(item => {
        const qtyInput = item.querySelector("input");
        const hapusBtn = item.querySelector(".btn-hapus");

        qtyInput.addEventListener("change", () => {
            if (qtyInput.value < 1) qtyInput.value = 1;
        });

        hapusBtn.addEventListener("click", () => {
            item.remove();
        });
    });
});
