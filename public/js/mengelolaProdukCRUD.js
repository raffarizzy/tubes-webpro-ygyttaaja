const csrf = document.querySelector('meta[name="csrf-token"]').content;

// PREVIEW IMAGE
function previewImage(input, id) {
    const img = document.getElementById(id);
    const reader = new FileReader();
    reader.onload = e => {
        img.src = e.target.result;
        img.style.display = 'block';
    };
    reader.readAsDataURL(input.files[0]);
}

// OPEN MODALS
function openTambahModal() {
    const form = document.getElementById('formTambah');
    if (form) form.reset();

    new bootstrap.Modal(
        document.getElementById('modalTambah')
    ).show();
}

function openEditModal(id, nama, harga, stok, imagePath) {
    // Deklarasi variabel dengan getElementById
    const editId = document.getElementById('editId');
    const editNama = document.getElementById('editNama');
    const editHarga = document.getElementById('editHarga');
    const editStok = document.getElementById('editStok');
    const previewEditGambar = document.getElementById('previewEditGambar');
    const modalEdit = document.getElementById('modalEdit');
    
    // Set nilai
    editId.value = id;
    editNama.value = nama;
    editHarga.value = harga;
    editStok.value = stok;

    // Set preview gambar
    if (imagePath) {
        previewEditGambar.src = '/storage/' + imagePath;
        previewEditGambar.style.display = 'block';
    }

    // Show modal
    new bootstrap.Modal(modalEdit).show();
}

function openHapusModal(id) {
    const hapusId = document.getElementById('hapusId');
    const modalHapus = document.getElementById('modalHapus');
    
    hapusId.value = id;
    new bootstrap.Modal(modalHapus).show();
}

function openEditTokoModal() {
    const modalEditToko = document.getElementById('modalEditToko');
    new bootstrap.Modal(modalEditToko).show();
}

// CREATE
function simpanProduk() {
    const form = document.getElementById('formTambah');
    const formData = new FormData(form);

    fetch(STORE_PRODUCT_URL, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json' 
        },
        body: formData
    })
    .then(res => res.json())
    .then(res => {
        if (res.success) {
            alert('Produk berhasil ditambahkan');
            location.reload();
        } else {
            alert(res.message ?? 'Gagal menyimpan produk');
        }
    })
    .catch(err => {
        console.error(err);
        alert('Server error');
    });
}


// UPDATE
function updateProduk() {
    const editId = document.getElementById('editId');
    const editNama = document.getElementById('editNama');
    const editKategori = document.getElementById('editKategori');
    const editHarga = document.getElementById('editHarga');
    const editStok = document.getElementById('editStok');
    const editDeskripsi = document.getElementById('editDeskripsi');
    const editGambar = document.getElementById('editGambar');
    
    const fd = new FormData();
    fd.append('_token', csrf);
    fd.append('_method', 'PUT');
    fd.append('nama', editNama.value);
    fd.append('category_id', editKategori.value);
    fd.append('harga', editHarga.value);
    fd.append('stok', editStok.value);
    fd.append('deskripsi', editDeskripsi.value);
    
    // Tambahkan gambar jika ada file baru yang dipilih
    if (editGambar.files.length > 0) {
        fd.append('image', editGambar.files[0]);
    }

    fetch(`/product/${editId.value}`, { 
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrf
        },
        body: fd 
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert('Produk berhasil diupdate');
            location.reload();
        } else {
            alert('Gagal update produk');
        }
    })
    .catch(err => {
        console.error(err);
        alert('Gagal update produk');
    });
}

// DELETE
function hapusProduk() {
    const hapusId = document.getElementById('hapusId');
    const modalHapus = document.getElementById('modalHapus');
    
    fetch(`/product/${hapusId.value}`, {
        method:'DELETE',
        headers:{ 'X-CSRF-TOKEN': csrf }
    }).then(() => {
        document.getElementById(`produk-${hapusId.value}`).remove();
        bootstrap.Modal.getInstance(modalHapus).hide();
        alert('Produk berhasil dihapus');
    })
    .catch(err => {
        console.error(err);
        alert('Gagal hapus produk');
    });
}

function updateToko() {
    const form = document.getElementById('formEditToko');
    const formData = new FormData(form);

    fetch(`/toko/${TOKO_ID}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document
                .querySelector('meta[name="csrf-token"]').content,
            'X-HTTP-Method-Override': 'PUT'
        },
        body: formData
    })
    .then(async res => {
        if (!res.ok) {
            const text = await res.text();
            console.error('SERVER ERROR:', text);
            throw new Error('Server error');
        }
        return res.json();
    })
    .then(data => {
        if (data.success) {
            alert('Toko berhasil diupdate');
            location.reload();
        }
    })
    .catch(err => {
        alert('Gagal update toko. Cek console.');
        console.error(err);
    });
}

// Event listener untuk preview gambar saat edit
document.addEventListener('DOMContentLoaded', function() {
    const editGambar = document.getElementById('editGambar');
    if (editGambar) {
        editGambar.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                previewImage(this, 'previewEditGambar');
            }
        });
    }
});