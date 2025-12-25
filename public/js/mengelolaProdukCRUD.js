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

function openEditModal(id, nama, harga, stok, deskripsi, imagePath) {
    editId.value = id;
    editNama.value = nama;
    editHarga.value = harga;
    editStok.value = stok;
    editDeskripsi.value = deskripsi;

    previewEditGambar.src = '/storage/' + imagePath;
    previewEditGambar.style.display = 'block';

    new bootstrap.Modal(modalEdit).show();
}

function openHapusModal(id) {
    hapusId.value = id;
    new bootstrap.Modal(modalHapus).show();
}

function openEditTokoModal() {
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
    const fd = new FormData(formEdit);
    fd.append('_token', csrf);
    fd.append('_method', 'PUT');

    fetch(`/product/${editId.value}`, { method:'POST', body:fd })
        .then(res => res.json())
        .then(() => location.reload());
}

// DELETE
function hapusProduk() {
    fetch(`/product/${hapusId.value}`, {
        method:'DELETE',
        headers:{ 'X-CSRF-TOKEN': csrf }
    }).then(() => {
        document.getElementById(`produk-${hapusId.value}`).remove();
        modalHapus.classList.remove('show');
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
            location.reload();
        }
    })
    .catch(err => {
        alert('Gagal update toko. Cek console.');
        console.error(err);
    });
}


