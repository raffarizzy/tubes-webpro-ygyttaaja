// SVG Placeholder inline - sama seperti di checkout.js
const NO_IMAGE_SVG =
    "data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22110%22 height=%22110%22 viewBox=%220 0 110 110%22%3E%3Crect width=%22110%22 height=%22110%22 fill=%22%23f1f3f4%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 font-family=%22Arial, sans-serif%22 font-size=%2214%22 fill=%22%235f6368%22%3ENo Image%3C/text%3E%3Cpath d=%22M35 40h40v5H35z M40 50h30v5H40z M45 60h20v5H45z%22 fill=%22%23dadce0%22/%3E%3C/svg%3E";

async function loadRiwayat() {
    try {
        const container = document.getElementById("pesananContainer");

        // Tampilkan loading state
        container.innerHTML = `
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="text-muted mt-3">Memuat riwayat pesanan...</p>
            </div>
        `;

        // Ambil data pesanan dari API Laravel
        const response = await fetch("/api/orders/history", {
            method: "GET",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN":
                    document.querySelector('meta[name="csrf-token"]')
                        ?.content || "",
            },
            credentials: "same-origin",
        });

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }

        const pesananList = await response.json();
        console.log("Orders loaded:", pesananList);

        // Clear container
        container.innerHTML = "";

        // Jika tidak ada pesanan
        if (pesananList.length === 0) {
            container.innerHTML = `
                <div class="card shadow-sm">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-inbox fs-1 text-muted mb-3"></i>
                        <h5 class="text-muted mt-3">Belum Ada Pesanan</h5>
                        <p class="text-muted">Anda belum pernah melakukan pemesanan</p>
                        <a href="/" class="btn btn-primary mt-3">
                            <i class="bi bi-shop"></i> Mulai Belanja
                        </a>
                    </div>
                </div>
            `;
            return;
        }

        // Render setiap pesanan
        pesananList.forEach((pesanan) => {
            // Total items
            const totalItems = pesanan.items.reduce(
                (sum, item) => sum + item.qty,
                0
            );

            // Status badge dengan Bootstrap
            let statusBadge = "";
            if (pesanan.status === "pending") {
                statusBadge =
                    '<span class="badge bg-warning text-dark fs-6"><i class="bi bi-hourglass-split"></i> Menunggu Pembayaran</span>';
            } else if (pesanan.status === "paid") {
                statusBadge =
                    '<span class="badge bg-success fs-6"><i class="bi bi-check-circle"></i> Lunas</span>';
            } else if (pesanan.status === "cancelled") {
                statusBadge =
                    '<span class="badge bg-danger fs-6"><i class="bi bi-x-circle"></i> Dibatalkan</span>';
            }

            // Render items dengan Bootstrap - GUNAKAN SVG PLACEHOLDER
            const itemsHTML = pesanan.items
                .map(
                    (item) => `
                <div class="d-flex align-items-start mb-3 pb-3 border-bottom">
                    <img src="${item.product?.image_path || NO_IMAGE_SVG}" 
                         alt="${item.nama_produk}"
                         class="rounded me-3"
                         style="width: 80px; height: 80px; object-fit: cover;"
                         onerror="this.onerror=null; this.src='${NO_IMAGE_SVG}'">
                    <div class="flex-grow-1">
                        <h6 class="mb-1 fw-semibold">${item.nama_produk}</h6>
                        <p class="text-muted small mb-1">
                            ${
                                item.product?.deskripsi ||
                                "Produk berkualitas tinggi"
                            }
                        </p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted small">
                                ${item.qty} pcs × Rp ${formatRupiah(item.harga)}
                            </span>
                            <span class="fw-bold text-primary">
                                Rp ${formatRupiah(item.subtotal)}
                            </span>
                        </div>
                    </div>
                </div>
            `
                )
                .join("");

            // Tombol review
            let reviewBtn = "";
            if (pesanan.status === "paid") {
                reviewBtn =
                    '<a href="rating.html" class="btn btn-success btn-sm me-2"><i class="bi bi-star"></i> Review</a>';
            }

            // Buat card dengan Bootstrap
            const card = document.createElement("div");
            card.className = "card shadow-sm mb-3";

            card.innerHTML = `
                <div class="card-header bg-light">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h6 class="mb-0 fw-bold">
                                <i class="bi bi-receipt text-primary"></i>
                                Order #${pesanan.id}
                            </h6>
                            <small class="text-muted">
                                <i class="bi bi-calendar3"></i>
                                ${formatDate(pesanan.created_at)}
                            </small>
                        </div>
                        <div class="col-md-6 text-md-end mt-2 mt-md-0">
                            ${statusBadge}
                        </div>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Order Items -->
                    <div class="mb-3">
                        ${itemsHTML}
                    </div>

                    <!-- Order Summary -->
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="border rounded p-3 bg-light h-100">
                                <h6 class="mb-3 fw-semibold">
                                    <i class="bi bi-geo-alt text-danger"></i>
                                    Alamat Pengiriman
                                </h6>
                                <p class="mb-1 fw-semibold">${
                                    pesanan.alamat?.nama_penerima ||
                                    "Tidak ada nama"
                                }</p>
                                <p class="small mb-1 text-muted">${
                                    pesanan.alamat?.alamat ||
                                    "Alamat tidak tersedia"
                                }</p>
                                <p class="small mb-0 text-muted">
                                    <i class="bi bi-telephone"></i>
                                    ${pesanan.alamat?.nomor_penerima || "-"}
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border rounded p-3 bg-light h-100">
                                <h6 class="mb-3 fw-semibold">
                                    <i class="bi bi-receipt-cutoff text-success"></i>
                                    Ringkasan Pembayaran
                                </h6>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Total Item:</span>
                                    <span class="fw-semibold">${totalItems} pcs</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted">Ongkir:</span>
                                    <span class="text-success fw-semibold">Gratis</span>
                                </div>
                                <hr class="my-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-bold">Total:</span>
                                    <span class="fw-bold text-primary fs-5">
                                        Rp ${formatRupiah(pesanan.total_harga)}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="mt-3 d-flex justify-content-end gap-2">
                        ${reviewBtn}
                        ${
                            pesanan.status === "pending"
                                ? `
                            <button class="btn btn-outline-danger btn-sm" onclick="cancelOrder(${pesanan.id})">
                                <i class="bi bi-x-circle"></i> Batalkan
                            </button>
                        `
                                : ""
                        }
                        <button class="btn btn-outline-primary btn-sm" onclick="viewOrderDetail(${
                            pesanan.id
                        })">
                            <i class="bi bi-eye"></i> Detail
                        </button>
                    </div>
                </div>
            `;

            container.appendChild(card);
        });
    } catch (err) {
        console.error("Gagal memuat data:", err);
        const container = document.getElementById("pesananContainer");
        container.innerHTML = `
            <div class="alert alert-danger shadow-sm" role="alert">
                <h5 class="alert-heading">
                    <i class="bi bi-exclamation-triangle-fill"></i> Error
                </h5>
                <p class="mb-0">Gagal memuat riwayat pesanan. ${err.message}</p>
            </div>
        `;
    }
}

// Helper function untuk format tanggal
function formatDate(dateString) {
    const date = new Date(dateString);
    const options = {
        year: "numeric",
        month: "long",
        day: "numeric",
        hour: "2-digit",
        minute: "2-digit",
    };
    return date.toLocaleDateString("id-ID", options);
}

// Helper function untuk format rupiah
function formatRupiah(number) {
    return new Intl.NumberFormat("id-ID").format(number);
}

// Function untuk cancel order
window.cancelOrder = function (orderId) {
    if (confirm("Apakah Anda yakin ingin membatalkan pesanan ini?")) {
        fetch(`/api/orders/${orderId}/cancel`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN":
                    document.querySelector('meta[name="csrf-token"]')
                        ?.content || "",
            },
            credentials: "same-origin",
        })
            .then((response) => response.json())
            .then((data) => {
                alert("Pesanan berhasil dibatalkan!");
                loadRiwayat(); // Reload data
            })
            .catch((error) => {
                console.error("Error:", error);
                alert("Gagal membatalkan pesanan!");
            });
    }
};

// Function untuk view detail
window.viewOrderDetail = function (orderId) {
    window.location.href = `/api/orders/${orderId}`;
};

// Load saat halaman dimuat
loadRiwayat();
