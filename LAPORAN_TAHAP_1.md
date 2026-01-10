# LAPORAN TUGAS BESAR TAHAP 1
## Pemrograman Web - SpareHub

---

## 1. Deskripsi Aplikasi

**SpareHub** adalah aplikasi web e-commerce B2C yang dirancang untuk jual-beli suku cadang kendaraan (spare parts). Platform ini memungkinkan pengguna untuk berbelanja berbagai produk suku cadang kendaraan dengan mudah dan nyaman.

### Fitur Utama:

#### Untuk Pembeli/Customer:
- **Browsing Produk**: Melihat katalog produk dengan fitur search dan pagination
- **Detail Produk**: Melihat informasi lengkap produk termasuk harga, diskon, dan rating
- **Keranjang Belanja**: Menambahkan produk ke keranjang dan mengatur jumlah
- **Checkout**: Proses pembelian dengan pemilihan alamat dan metode pembayaran
- **Rating & Review**: Memberikan penilaian dan komentar pada produk
- **Riwayat Pesanan**: Melihat daftar pesanan yang telah dilakukan
- **Manajemen Profil**: Mengubah informasi pribadi pengguna

#### Untuk Penjual/Seller:
- **Profil Toko**: Mengelola informasi toko (nama, lokasi, logo)
- **Tambah Toko**: Membuat toko baru untuk berjualan
- **Manajemen Produk**: Menambah, mengubah, dan menghapus produk (CRUD)
- **Statistik Toko**: Melihat total produk dan informasi toko

#### Fitur Umum:
- **Autentikasi Pengguna**: Login dan Register dengan validasi
- **Search Bar**: Pencarian produk berdasarkan nama
- **Pagination**: Navigasi halaman produk
- **Navigasi Konsisten**: Navbar yang unified di semua halaman
- **Responsive Design**: Tampilan yang menyesuaikan berbagai ukuran layar
- **Persistensi Data**: Penyimpanan data menggunakan localStorage

### Teknologi yang Digunakan:
- **HTML5**: Struktur halaman web
- **CSS3**: Styling dan responsive design
- **JavaScript (ES6+)**: Logika aplikasi dan interaksi
- **localStorage**: Penyimpanan data di browser
- **JSON**: Format data untuk produk, user, dan transaksi
- **Font Awesome**: Icon library untuk UI

### Arsitektur Aplikasi:
Aplikasi ini dibangun sebagai **frontend-only application** tanpa backend server. Data disimpan dalam file JSON dan localStorage browser untuk persistensi data runtime.

---

## 2. Link GitHub Repository

**Repository**: [https://github.com/raffarizzy/tubes-webpro-ygyttaaja](https://github.com/raffarizzy/tubes-webpro-ygyttaaja)

**Status**: Public Repository ✓

---

## 3. Daftar Anggota Kelompok & Kontribusi

| No | Nama Anggota | NIM | Halaman yang Dikembangkan |
|----|--------------|-----|---------------------------|
| 1 | **Raffa Rizky Febryan** | - | Register, Edit Profil, Rating |
| 2 | **Naufal Dzulfikar** | - | Detail Produk, Homepage (Pagination) |
| 3 | **Bagas Pratama** | - | Riwayat Pesanan, Checkout |
| 4 | **Frizam Maulana** | - | Keranjang, Homepage (Search Bar) |
| 5 | **Riziq Rizwan** | - | Login, Profil Toko, Tambah Toko |

---

## 4. Detail Kontribusi Per Anggota

### 4.1. Raffa Rizky Febryan

#### Halaman yang Dikembangkan:
1. **Register** (`register.html`)
2. **Edit Profil** (`edit_profil.html`)
3. **Rating** (`rating.html`)

---

#### A. Halaman Register (`register.html`)

**Screenshot**:
![Screenshot Register](img/screenshots/register.png)

**Deskripsi**:
Halaman pendaftaran akun baru untuk pengguna yang ingin bergabung dengan SpareHub. Halaman ini memiliki form validasi lengkap untuk memastikan data yang diinput valid.

**HTML**:
Struktur HTML menggunakan form dengan berbagai input field untuk pengumpulan data pengguna:
- Form container dengan class `register-container` untuk layout centering
- Input fields: Username, Email, Password, Confirm Password, Phone, Address
- Icon Font Awesome untuk visual enhancement pada setiap input
- Password visibility toggle button
- Link navigasi ke halaman login untuk user yang sudah punya akun

```html
<div class="register-container">
    <form id="registerForm" class="register-form">
        <h1>Daftar Akun SpareHub</h1>

        <div class="input-group">
            <i class="fas fa-user"></i>
            <input type="text" id="username" required placeholder="Username" minlength="3">
        </div>

        <div class="input-group">
            <i class="fas fa-envelope"></i>
            <input type="email" id="email" required placeholder="Email">
        </div>

        <div class="input-group">
            <i class="fas fa-lock"></i>
            <input type="password" id="password" required placeholder="Password" minlength="6">
            <button type="button" class="toggle-password" onclick="togglePassword('password')">
                <i class="fas fa-eye"></i>
            </button>
        </div>

        <div class="input-group">
            <i class="fas fa-lock"></i>
            <input type="password" id="confirmPassword" required placeholder="Konfirmasi Password">
        </div>

        <div class="input-group">
            <i class="fas fa-phone"></i>
            <input type="tel" id="phone" placeholder="Nomor Telepon (opsional)">
        </div>

        <div class="input-group">
            <i class="fas fa-map-marker-alt"></i>
            <textarea id="address" rows="2" placeholder="Alamat (opsional)"></textarea>
        </div>

        <button type="submit" class="btn-register">Daftar</button>
        <p class="form-footer">Sudah punya akun? <a href="login.html">Login di sini</a></p>
    </form>
</div>
```

**CSS** (`css/register.css`):
Styling fokus pada user experience dan visual appeal:
- **Layout**: Flexbox untuk centering form secara vertikal dan horizontal
- **Background**: Gradient background untuk visual menarik
- **Form Card**: White background dengan border-radius dan box-shadow untuk depth
- **Input Styling**: Icon positioning absolute di dalam input group, padding disesuaikan
- **Responsive Design**: Form menyesuaikan ukuran layar mobile
- **Interactive Elements**: Hover effects pada button dan focus states pada input
- **Color Scheme**: Primary color #122c4f untuk konsistensi brand

```css
.register-container {
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 2rem 1rem;
}

.register-form {
    background: white;
    padding: 2.5rem;
    border-radius: 12px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.2);
    width: 100%;
    max-width: 450px;
}

.register-form h1 {
    text-align: center;
    color: #122c4f;
    margin-bottom: 2rem;
}

.input-group {
    position: relative;
    margin-bottom: 1.5rem;
}

.input-group i {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #666;
}

.input-group input,
.input-group textarea {
    width: 100%;
    padding: 0.75rem 2.5rem;
    border: 1px solid #ddd;
    border-radius: 6px;
    transition: border-color 0.3s;
}

.input-group input:focus {
    outline: none;
    border-color: #122c4f;
}

.btn-register {
    width: 100%;
    padding: 0.75rem;
    background: #122c4f;
    color: white;
    border: none;
    border-radius: 6px;
    cursor: pointer;
}

.btn-register:hover {
    background: #0d1f38;
}
```

**JavaScript** (`js/register.js`):
Implementasi validasi form dan penyimpanan data user:
- **Form Validation**: Validasi client-side untuk username, email, password
- **Email Regex**: Pengecekan format email yang valid
- **Password Matching**: Memastikan password dan confirm password sama
- **Uniqueness Check**: Cek apakah email sudah terdaftar
- **Data Storage**: Simpan user baru ke localStorage
- **User Feedback**: Alert untuk error dan success messages
- **Redirect**: Navigasi ke login page setelah registrasi berhasil

```javascript
document.getElementById('registerForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const username = document.getElementById('username').value.trim();
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirmPassword').value;

    if (username.length < 3) {
        alert('Username minimal 3 karakter!');
        return;
    }

    if (password.length < 6) {
        alert('Password minimal 6 karakter!');
        return;
    }

    if (password !== confirmPassword) {
        alert('Password tidak cocok!');
        return;
    }

    const existingUsers = JSON.parse(localStorage.getItem('userData')) || [];

    if (existingUsers.some(user => user.email === email)) {
        alert('Email sudah terdaftar!');
        return;
    }

    const newUser = {
        id: Date.now(),
        username: username,
        email: email,
        password: password,
        phone: document.getElementById('phone').value.trim(),
        address: document.getElementById('address').value.trim(),
        createdAt: new Date().toISOString()
    };

    existingUsers.push(newUser);
    localStorage.setItem('userData', JSON.stringify(existingUsers));

    alert('Registrasi berhasil!');
    window.location.href = 'login.html';
});
```

**jQuery**: Tidak digunakan - menggunakan Vanilla JavaScript

---

#### B. Halaman Edit Profil (`edit_profil.html`)

**Screenshot**:
![Screenshot Edit Profil](img/screenshots/edit_profil.png)

**Deskripsi**:
Halaman untuk mengubah informasi profil pengguna yang sudah login.

**HTML**, **CSS**, **JavaScript**: (Detail implementasi mirip dengan contoh sebelumnya)

**jQuery**: Tidak digunakan

---

#### C. Halaman Rating (`rating.html`)

**Screenshot**:
![Screenshot Rating](img/screenshots/rating.png)

**Deskripsi**:
Halaman untuk memberikan rating bintang dan komentar pada produk.

**jQuery**: Tidak digunakan

---

### 4.2. Naufal Dzulfikar

#### Halaman yang Dikembangkan:
1. **Detail Produk** (`detail-produk.html`)
2. **Homepage - Pagination** (`homepage.html`)

**jQuery**: Tidak digunakan

---

### 4.3. Bagas Pratama

#### Halaman yang Dikembangkan:
1. **Riwayat Pesanan** (`riwayat_pesanan.html`)
2. **Checkout** (`checkout.html`)

**jQuery**: Tidak digunakan

---

### 4.4. Frizam Maulana

#### Halaman yang Dikembangkan:
1. **Keranjang** (`keranjang.html`)
2. **Homepage - Search Bar** (`homepage.html`)

**jQuery**: Tidak digunakan

---

### 4.5. Riziq Rizwan

#### Halaman yang Dikembangkan:
1. **Login** (`login.html`)
2. **Profil Toko** (`profil_toko.html`)
3. **Tambah Toko** (Fitur dalam `mengelolaProdukCRUD.html`)

**jQuery**: Tidak digunakan

---

## 5. Kesimpulan

**SpareHub** adalah aplikasi web e-commerce yang lengkap untuk jual-beli suku cadang kendaraan, dibangun dengan teknologi modern **HTML5**, **CSS3**, dan **JavaScript ES6+** tanpa menggunakan jQuery atau framework lainnya.

**Fitur Lengkap**: Autentikasi, Search, Pagination, Shopping Cart, Checkout, Rating, Order History, Store Management

**Teknologi**: Frontend-only application dengan localStorage untuk persistensi data, menggunakan Vanilla JavaScript murni

---

**Tanggal**: Januari 2026
**Kelompok**: YGYTTAAJA
**Status**: Tahap 1 ✓
