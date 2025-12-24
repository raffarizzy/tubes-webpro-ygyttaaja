async function loadRatings() {
const [produkRes, pesananRes] = await Promise.all([
fetch("JSON/productdata.json"),
fetch("JSON/pesananData.json")
]);

const [produkList, pesananList] = await Promise.all([
    produkRes.json(),
    pesananRes.json()
]);

// Ambil rating dari localStorage, jika belum ada pakai JSON/ratingData.json
let ratingList = JSON.parse(localStorage.getItem("ratingList")) || await fetch("JSON/ratingData.json").then(res => res.json());

const container = document.getElementById("ratingContainer");
const selectProduk = document.getElementById("produkSelect");

// Isi dropdown produk: hanya produk yang pernah dipesan, tapi belum dirating
produkList.forEach(p => {
    const sudahDipesan = pesananList.some(ps => ps.produkId === p.id);
    const sudahDirating = ratingList.some(r => r.produkId === p.id);

    if (sudahDipesan && !sudahDirating) {
        const opt = document.createElement("option");
        opt.value = p.id;
        opt.textContent = p.nama;
        selectProduk.appendChild(opt);
    }
});

// Tampilkan rating
function displayRatings(list) {
    container.innerHTML = "";
    list.forEach(r => {
        const produk = produkList.find(p => p.id === r.produkId);
        const card = document.createElement("div");
        card.classList.add("rating-card");

        card.innerHTML = `
        <img src="${produk?.imagePath || 'assets/images/no-image.png'}" alt="${produk?.nama || 'Produk'}" />
        <div class="rating-info">
            <h3>${produk?.nama || 'Produk tidak ditemukan'}</h3>
            <div class="stars">${"⭐".repeat(r.rating)}</div>
            <p>${r.komentar}</p>
            <small>${r.tanggal}</small>
            <br><br>
            <button class="delete-btn" data-id="${r.id}">Hapus</button>
        </div>
        `;
        container.appendChild(card);
    });

    // Event listener untuk hapus
    const deleteButtons = document.querySelectorAll(".delete-btn");
    deleteButtons.forEach(btn => {
        btn.addEventListener("click", (e) => {
            const id = parseInt(e.target.getAttribute("data-id"));
            if (confirm("Yakin ingin menghapus rating ini?")) {
                const index = ratingList.findIndex(r => r.id === id);
                if (index !== -1) {
                    ratingList.splice(index, 1);
                    localStorage.setItem("ratingList", JSON.stringify(ratingList));
                    displayRatings(ratingList);
                    alert("Rating berhasil dihapus");
                }
            }
        });
    });
}

displayRatings(ratingList);

// Tambah rating baru
document.getElementById("ratingForm").addEventListener("submit", (e) => {
    e.preventDefault();

    const newRating = {
        id: ratingList.length + 1,
        produkId: parseInt(selectProduk.value),
        userId: 1, // contoh user login
        rating: parseInt(document.getElementById("ratingValue").value),
        komentar: document.getElementById("komentar").value,
        tanggal: new Date().toISOString().split("T")[0]
    };

    ratingList.push(newRating);
    localStorage.setItem("ratingList", JSON.stringify(ratingList));
    displayRatings(ratingList);

    alert("Rating berhasil ditambahkan");
    e.target.reset();
})
}

loadRatings();