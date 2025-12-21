async function loadRiwayat() {
    try {
    // Ambil data produk dan pesanan
    const [produkRes, pesananRes] = await Promise.all([
        fetch("JSON/productData.json"),
        fetch("JSON/pesananData.json"),
    ]);

    const [produkList, pesananList] = await Promise.all([
        produkRes.json(),
        pesananRes.json(),
    ]);

    const ratingList = JSON.parse(localStorage.getItem("ratingList")) || await fetch("JSON/ratingData.json").then(res => res.json());

    const container = document.getElementById("pesananContainer");

    pesananList.forEach(pesanan => {
        const produk = produkList.find(p => p.id === pesanan.produkId);
        const sudahDirating = ratingList.some(r => r.produkId === pesanan.id);
        let reviewBtn = '';

        if (pesanan.status === 'selesai' && sudahDirating) {
            reviewBtn += '<a href="rating.html"><button class="review-btn">Tampilkan Review</button></a>';
        } else if (pesanan.status === 'selesai') {
            reviewBtn += '<a href="rating.html"><button class="review-btn">Review</button></a>';
        }

        const card = document.createElement("div");
        card.classList.add("pesanan-card");

        card.innerHTML = `
        <img src="${produk?.imagePath || 'assets/images/no-image.png'}" alt="${produk?.nama || 'Produk'}" />
        <div class="pesanan-info">
            <h3>${produk?.nama || 'Produk tidak ditemukan'}</h3>
            <p>${produk?.deskripsi || ''}</p>
            <p>Jumlah: ${pesanan.jumlah}</p>
            <p>Total Harga: Rp ${pesanan.totalHarga.toLocaleString("id-ID")}</p>
            <p>Tanggal: ${pesanan.tanggal}</p>
            <p>Alamat: ${pesanan.alamatPengiriman}</p>
            <p class="status ${pesanan.status}">Status: ${pesanan.status}</p>
            ${reviewBtn}
        </div>
        `;
        container.appendChild(card);
    });
    } catch (err) {
    console.error("Gagal memuat data:", err);
    }
}

loadRiwayat();