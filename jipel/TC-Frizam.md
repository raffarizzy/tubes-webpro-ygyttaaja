# Test Cases — Frizam Dafa Maulana (103022300011)

**Project:** SpareHub  
**PIC:** Frizam Dafa Maulana  
**Coverage Area:** Halaman Keranjang & Beranda Search  

---

## TC-BB-F01 — Registrasi Akun (Equivalence Partitioning)

| Field          | Value                                        |
|----------------|----------------------------------------------|
| Test Case ID   | TC-BB-F01                                    |
| Description    | Registrasi Akun — Equivalence Partitioning   |
| Feature        | FR1 — Registrasi Akun                        |
| Test Type      | Black-Box                                    |
| Created By     | Frizam Dafa Maulana                          |
| Reviewed By    | -                                            |
| Version        | 1.0                                          |
| Tester         | Frizam Dafa Maulana                          |
| Date Tested    | -                                            |
| Test Result    | Not Executed                                 |

### Prerequisites

| # | Prerequisite                                     |
|---|--------------------------------------------------|
| 1 | Aplikasi SpareHub dapat diakses di browser       |
| 2 | Halaman registrasi terbuka di `/register`        |
| 3 | Belum ada akun dengan email yang akan didaftarkan |

### Test Data

| Kelas | Nama     | Email                    | No. HP      | Password        | Confirm Password | Keterangan   |
|-------|----------|--------------------------|-------------|-----------------|------------------|--------------|
| Valid | BudiTest | budi.test@gmail.com      | 08123456789 | Password123!    | Password123!     | Data valid   |
| Invalid — email kosong | BudiTest | *(kosong)* | 08123456789 | Password123! | Password123! | Email wajib diisi |
| Invalid — email duplikat | BudiTest | budi.test@gmail.com | 08123456789 | Password123! | Password123! | Email sudah terdaftar |
| Invalid — password tidak cocok | BudiTest | budi2@gmail.com | 08123456789 | Password123! | Password456! | Konfirmasi tidak cocok |
| Invalid — nama kosong | *(kosong)* | budi3@gmail.com | 08123456789 | Password123! | Password123! | Nama wajib diisi |

### Test Scenario

Verifikasi bahwa sistem membatasi registrasi berdasarkan partisi valid dan invalid pada setiap field input.

| Step | Step Details                                                                 | Expected Results                                                         | Actual Results | Pass / Fail / Not Executed |
|------|------------------------------------------------------------------------------|--------------------------------------------------------------------------|----------------|---------------------------|
| 1    | Buka URL `/register` di browser                                              | Halaman registrasi tampil dengan form Name, Email, Phone, Password       | -              | Not Executed              |
| 2    | Isi semua field dengan **data valid** (kelas valid) lalu klik Register       | User berhasil terdaftar, redirect ke `/` (homepage), user ter-login otomatis | -          | Not Executed              |
| 3    | Isi form dengan **email kosong**, field lain valid, lalu klik Register       | Muncul pesan validasi "The email field is required."                     | -              | Not Executed              |
| 4    | Isi form dengan **email yang sudah terdaftar**, lalu klik Register           | Muncul pesan "The email has already been taken."                         | -              | Not Executed              |
| 5    | Isi form dengan **password tidak cocok** antara Password dan Confirm Password | Muncul pesan "The password field confirmation does not match."           | -              | Not Executed              |
| 6    | Isi form dengan **nama kosong**, field lain valid, lalu klik Register        | Muncul pesan validasi "The name field is required."                      | -              | Not Executed              |

---

## TC-BB-F02 — Login Password (Boundary Value Analysis)

| Field          | Value                                          |
|----------------|------------------------------------------------|
| Test Case ID   | TC-BB-F02                                      |
| Description    | Login Password — Boundary Value Analysis       |
| Feature        | FR2 — Login Sistem                             |
| Test Type      | Black-Box                                      |
| Created By     | Frizam Dafa Maulana                            |
| Reviewed By    | -                                              |
| Version        | 1.0                                            |
| Tester         | Frizam Dafa Maulana                            |
| Date Tested    | -                                              |
| Test Result    | Not Executed                                   |

### Prerequisites

| # | Prerequisite                                                |
|---|-------------------------------------------------------------|
| 1 | Aplikasi dapat diakses di browser                           |
| 2 | Akun terdaftar: `tester@sparehub.com` / `Password123!`      |
| 3 | Halaman login terbuka di `/login`                           |

### Test Data (Boundary pada panjang password — min 8 karakter per `Rules\Password::defaults()`)

| # | Email                 | Password          | Panjang | Keterangan              |
|---|-----------------------|-------------------|---------|-------------------------|
| 1 | tester@sparehub.com   | Pass123           | 7 char  | Di bawah batas minimum  |
| 2 | tester@sparehub.com   | Pass123!          | 8 char  | Tepat di batas minimum  |
| 3 | tester@sparehub.com   | Password123!      | 12 char | Di dalam rentang valid  |
| 4 | tester@sparehub.com   | *(kosong)*        | 0 char  | Di bawah batas absolut  |
| 5 | *(kosong)*            | Password123!      | -       | Email kosong            |

### Test Scenario

Verifikasi bahwa sistem menolak login dengan password yang tidak memenuhi batas panjang minimum dan menerima password yang valid.

| Step | Step Details                                                              | Expected Results                                                   | Actual Results | Pass / Fail / Not Executed |
|------|---------------------------------------------------------------------------|--------------------------------------------------------------------|----------------|---------------------------|
| 1    | Buka URL `/login`                                                         | Halaman login tampil dengan field Email dan Password               | -              | Not Executed              |
| 2    | Masukkan email valid + password **7 karakter** (`Pass123`), klik Login    | Autentikasi gagal — pesan "These credentials do not match our records." atau validasi password | - | Not Executed |
| 3    | Masukkan email valid + password **8 karakter** (`Pass123!`) sesuai akun terdaftar, klik Login | Login berhasil, redirect ke homepage `/`              | -              | Not Executed              |
| 4    | Masukkan email valid + password **12 karakter** (`Password123!`), klik Login | Login berhasil, redirect ke `/`                               | -              | Not Executed              |
| 5    | Biarkan field password **kosong**, isi email valid, klik Login            | Muncul validasi "The password field is required."                  | -              | Not Executed              |
| 6    | Biarkan field email **kosong**, isi password valid, klik Login            | Muncul validasi "The email field is required."                     | -              | Not Executed              |
| 7    | Login berhasil (step 3), kemudian klik Logout                             | Session dihapus, user di-redirect ke `/`                           | -              | Not Executed              |

---

## TC-BB-F03 — Tambah Keranjang (Decision Table)

| Field          | Value                                         |
|----------------|-----------------------------------------------|
| Test Case ID   | TC-BB-F03                                     |
| Description    | Tambah Keranjang — Decision Table             |
| Feature        | FR6 — Keranjang Belanja                       |
| Test Type      | Black-Box                                     |
| Created By     | Frizam Dafa Maulana                           |
| Reviewed By    | -                                             |
| Version        | 1.0                                           |
| Tester         | Frizam Dafa Maulana                           |
| Date Tested    | -                                             |
| Test Result    | Not Executed                                  |

### Prerequisites

| # | Prerequisite                                               |
|---|------------------------------------------------------------|
| 1 | User sudah login sebagai Pembeli                           |
| 2 | Terdapat produk dengan `id=1`, stok > 0 di database       |
| 3 | Node.js API (`localhost:3001`) berjalan                    |

### Decision Table

| Kondisi                    | R1  | R2  | R3  | R4  |
|----------------------------|-----|-----|-----|-----|
| User terautentikasi        | Ya  | Tidak | Ya | Ya |
| `product_id` valid (exists) | Ya  | -   | Tidak | Ya |
| `jumlah` ≥ 1              | Ya  | -   | -   | Tidak (0) |
| **Aksi: Produk ditambahkan ke keranjang** | **Ya** | **Tidak** | **Tidak** | **Tidak** |

### Test Scenario

Verifikasi bahwa `BarangKeranjangController::store()` menambahkan produk hanya ketika semua kondisi terpenuhi.

| Step | Step Details                                                                              | Expected Results                                                            | Actual Results | Pass / Fail / Not Executed |
|------|-------------------------------------------------------------------------------------------|-----------------------------------------------------------------------------|----------------|---------------------------|
| 1    | **(R1)** Login, buka halaman detail produk (`/produk/1`), klik "Tambah ke Keranjang" dengan jumlah = 1 | Response JSON `{ success: true }`, produk tampil di `/keranjang`     | -              | Not Executed              |
| 2    | **(R1)** Buka `/keranjang`, ubah jumlah item menjadi 3 via tombol update                 | Response JSON `{ success: true }`, jumlah item berubah menjadi 3            | -              | Not Executed              |
| 3    | **(R1)** Di halaman keranjang, klik tombol hapus pada item                               | Item dihapus dari keranjang, konfirmasi sukses tampil                        | -              | Not Executed              |
| 4    | **(R2)** Logout, lalu kirim POST `/keranjang/item` dengan `product_id=1, jumlah=1`       | Response 401 Unauthorized atau redirect ke `/login`                         | -              | Not Executed              |
| 5    | **(R3)** Login, kirim POST `/keranjang/item` dengan `product_id=99999` (tidak ada)       | Response 422 Validation Error: "The selected product id is invalid."         | -              | Not Executed              |
| 6    | **(R4)** Login, kirim POST `/keranjang/item` dengan `product_id=1, jumlah=0`             | Response 422 Validation Error: "The jumlah field must be at least 1."        | -              | Not Executed              |

---

## TC-WB-F01 — KeranjangController + BarangKeranjangCtrl (Branch Coverage)

| Field          | Value                                                             |
|----------------|-------------------------------------------------------------------|
| Test Case ID   | TC-WB-F01                                                         |
| Description    | KeranjangController + BarangKeranjangCtrl — Branch Coverage       |
| Feature        | FR6 — Keranjang Belanja                                           |
| Test Type      | White-Box                                                         |
| Created By     | Frizam Dafa Maulana                                               |
| Reviewed By    | -                                                                 |
| Version        | 1.0                                                               |
| Tester         | Frizam Dafa Maulana                                               |
| Date Tested    | -                                                                 |
| Test Result    | Not Executed                                                      |

### Prerequisites

| # | Prerequisite                                                           |
|---|------------------------------------------------------------------------|
| 1 | Node.js API (`localhost:3001`) berjalan normal                         |
| 2 | Akun pembeli terdaftar dan sudah login                                 |
| 3 | Minimal 1 produk tersedia di database                                  |

### Branch Map — `KeranjangController`

```
index()
 ├─ [B1] Auth::check() == false  → redirect('/login')
 └─ [B2] Auth::check() == true
     ├─ [B3] $response->successful() == true  → $cartItems = $response->json('data')
     └─ [B4] $response->successful() == false (exception) → $cartItems = []

getCartData()
 ├─ [B5] Auth::check() == false → JSON 401
 └─ [B6] Auth::check() == true
     ├─ [B7] $response->successful() == true → JSON data
     └─ [B8] exception thrown → JSON 500

clear()
 ├─ [B9]  Auth::check() == false → JSON 401
 └─ [B10] Auth::check() == true
     ├─ [B11] $response->successful() == true → JSON success
     └─ [B12] exception thrown → JSON 500
```

### Branch Map — `BarangKeranjangController`

```
store()
 ├─ [B13] validation fails → JSON 422
 └─ [B14] validation pass
     ├─ [B15] Node response successful → JSON { success: true }
     └─ [B16] Node response failed → JSON { success: false }

update()
 ├─ [B17] validation fails → JSON 422
 └─ [B18] validation pass
     ├─ [B19] Node response successful → JSON { success: true }
     └─ [B20] Node response failed → JSON { success: false }

destroy()
 ├─ [B21] Node response successful → JSON { success: true }
 └─ [B22] Node response failed → JSON { success: false }
```

### Test Scenario

| Step | Branch Target | Step Details                                                                    | Expected Results                                                | Actual Results | Pass / Fail / Not Executed |
|------|--------------|---------------------------------------------------------------------------------|-----------------------------------------------------------------|----------------|---------------------------|
| 1    | B2, B3       | Login, akses `GET /keranjang` saat Node.js API aktif                            | Halaman keranjang tampil, data item termuat                     | -              | Not Executed              |
| 2    | B2, B4       | Login, matikan Node.js API sementara, akses `GET /keranjang`                   | Halaman keranjang tampil dengan keranjang kosong (graceful fallback) | -         | Not Executed              |
| 3    | B1           | Logout, akses `GET /keranjang`                                                  | Redirect ke `/login`                                            | -              | Not Executed              |
| 4    | B6, B7       | Login, akses `GET /keranjang/data`                                              | JSON `{ success: true, data: { items, total_items, total_price } }` | -          | Not Executed              |
| 5    | B5           | Logout, akses `GET /keranjang/data`                                             | JSON `{ success: false, message: 'Unauthorized' }` status 401   | -              | Not Executed              |
| 6    | B10, B11     | Login, kirim `DELETE /keranjang/clear`                                          | JSON `{ success: true, message: 'Keranjang berhasil dikosongkan' }` | -          | Not Executed              |
| 7    | B14, B15     | Login, kirim `POST /keranjang/item` dengan `product_id=1, jumlah=2`             | JSON `{ success: true, message: 'Produk berhasil ditambahkan ke keranjang' }` | - | Not Executed  |
| 8    | B13          | Login, kirim `POST /keranjang/item` dengan `jumlah=-1`                          | JSON 422 Validation Error                                       | -              | Not Executed              |
| 9    | B18, B19     | Login, kirim `PUT /keranjang/item/{id}` dengan `jumlah=5`                       | JSON `{ success: true, message: 'Jumlah berhasil diperbarui' }` | -              | Not Executed              |
| 10   | B21          | Login, kirim `DELETE /keranjang/item/{id}` dengan id item valid                 | JSON `{ success: true, message: 'Item berhasil dihapus dari keranjang' }` | - | Not Executed  |

---

## TC-WB-F02 — ProductController::index() Search Bar (Branch Coverage)

| Field          | Value                                                        |
|----------------|--------------------------------------------------------------|
| Test Case ID   | TC-WB-F02                                                    |
| Description    | ProductController::index() Search Bar — Branch Coverage      |
| Feature        | FR3 — Pencarian Sparepart                                    |
| Test Type      | White-Box                                                    |
| Created By     | Frizam Dafa Maulana                                          |
| Reviewed By    | -                                                            |
| Version        | 1.0                                                          |
| Tester         | Frizam Dafa Maulana                                          |
| Date Tested    | -                                                            |
| Test Result    | Not Executed                                                 |

### Prerequisites

| # | Prerequisite                                                      |
|---|-------------------------------------------------------------------|
| 1 | Node.js API (`localhost:3001`) dapat diakses                      |
| 2 | Database produk memiliki data, misal produk bernama "Kampas Rem"  |
| 3 | Fitur pencarian tersedia di homepage (`/`)                        |

### Branch Map — `HomeController::index()` & Node.js API Search

```
HomeController::index()
 └─ Panggil Product::with(['category','toko'])->orderBy()->get()
     (tidak ada branching langsung di controller ini,
      branching ada di layer Node.js API /api/products)

Node.js GET /api/products
 ├─ [B1] query param `search` ada & tidak kosong → filter WHERE nama LIKE '%search%'
 └─ [B2] query param `search` tidak ada / kosong → return semua produk

 ├─ [B3] hasil pencarian ditemukan (rows > 0) → return data array
 └─ [B4] hasil pencarian kosong (rows = 0) → return array kosong []
```

### Test Scenario

| Step | Branch Target | Step Details                                                                   | Expected Results                                                     | Actual Results | Pass / Fail / Not Executed |
|------|--------------|--------------------------------------------------------------------------------|----------------------------------------------------------------------|----------------|---------------------------|
| 1    | B2, B3       | Buka homepage `/`, tidak masukkan keyword apapun                               | Semua produk tampil di halaman                                       | -              | Not Executed              |
| 2    | B1, B3       | Di homepage, ketikkan "Kampas Rem" di search bar, tekan Enter                  | Hanya produk yang nama-nya mengandung "Kampas Rem" yang tampil       | -              | Not Executed              |
| 3    | B1, B4       | Di homepage, ketikkan keyword yang pasti tidak ada misal "xyzxyzxyz123"        | Tidak ada produk tampil, halaman menampilkan pesan kosong / empty state | -            | Not Executed              |
| 4    | B1, B3       | Ketikkan keyword huruf kecil "kampas rem" (case-insensitive test)              | Produk "Kampas Rem" tetap tampil (pencarian case-insensitive)        | -              | Not Executed              |
| 5    | B1           | Kirim request `GET /api/products?search=Rem` langsung via Postman              | JSON `{ success: true, data: [...] }` berisi produk yang mengandung "Rem" | -          | Not Executed              |
