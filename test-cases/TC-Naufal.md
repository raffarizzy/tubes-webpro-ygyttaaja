# Test Cases — Naufal Muhammad Dzulfikar (103022300021)

**Project:** SpareHub  
**PIC:** Naufal Muhammad Dzulfikar  
**Coverage Area:** Halaman Detail Produk & Beranda Pagination  

---

## TC-BB-N01 — Pencarian Keyword (Equivalence Partitioning)

| Field          | Value                                              |
|----------------|----------------------------------------------------|
| Test Case ID   | TC-BB-N01                                          |
| Description    | Pencarian Keyword — Equivalence Partitioning       |
| Feature        | FR3 — Pencarian Sparepart                          |
| Test Type      | Black-Box                                          |
| Created By     | Naufal Muhammad Dzulfikar                          |
| Reviewed By    | -                                                  |
| Version        | 1.0                                                |
| Tester         | Naufal Muhammad Dzulfikar                          |
| Date Tested    | -                                                  |
| Test Result    | Not Executed                                       |

### Prerequisites

| # | Prerequisite                                                          |
|---|-----------------------------------------------------------------------|
| 1 | Aplikasi berjalan dan homepage `/` dapat diakses                      |
| 2 | Database produk memiliki minimal 5 produk dengan nama beragam         |
| 3 | Terdapat produk bernama "Oli Mesin 10W-40" di database                |

### Test Data — Partisi Equivalence

| Kelas         | Input Keyword       | Keterangan                                      |
|---------------|---------------------|-------------------------------------------------|
| Valid — match | `Oli`               | Keyword valid, produk ditemukan                 |
| Valid — match | `oli mesin`         | Frasa dua kata, produk ditemukan                |
| Invalid — kosong | *(kosong)*       | Tidak ada keyword, semua produk tampil          |
| Invalid — tidak ada hasil | `zzz999xabc` | Keyword tidak cocok produk manapun         |
| Invalid — karakter khusus | `<script>` | Keyword berisi karakter HTML/JS             |

### Test Scenario

Verifikasi bahwa fitur pencarian mempartisi input menjadi hasil ditemukan, kosong, dan tidak ada hasil.

| Step | Step Details                                                                        | Expected Results                                                        | Actual Results | Pass / Fail / Not Executed |
|------|-------------------------------------------------------------------------------------|-------------------------------------------------------------------------|----------------|---------------------------|
| 1    | Buka homepage `/`, ketik `Oli` di search bar, tekan Enter                           | Produk yang mengandung kata "Oli" pada nama tampil di hasil pencarian   | -              | Not Executed              |
| 2    | Ketik `oli mesin` (lowercase dua kata), tekan Enter                                 | Produk "Oli Mesin 10W-40" tampil (case-insensitive, multi-word search)  | -              | Not Executed              |
| 3    | Kosongkan search bar, tekan Enter atau reload `/`                                   | Seluruh produk tampil tanpa filter                                      | -              | Not Executed              |
| 4    | Ketik keyword `zzz999xabc` yang tidak ada di database, tekan Enter                  | Halaman menampilkan pesan "Produk tidak ditemukan" atau grid kosong     | -              | Not Executed              |
| 5    | Ketik `<script>alert(1)</script>` di search bar, tekan Enter                        | Input di-escape dengan aman, tidak ada eksekusi JS, hasil kosong tampil | -              | Not Executed              |

---

## TC-BB-N02 — Batas Quantity Order (Boundary Value Analysis)

| Field          | Value                                               |
|----------------|-----------------------------------------------------|
| Test Case ID   | TC-BB-N02                                           |
| Description    | Batas Quantity Order — Boundary Value Analysis      |
| Feature        | FR7 — Melakukan Pemesanan                           |
| Test Type      | Black-Box                                           |
| Created By     | Naufal Muhammad Dzulfikar                           |
| Reviewed By    | -                                                   |
| Version        | 1.0                                                 |
| Tester         | Naufal Muhammad Dzulfikar                           |
| Date Tested    | -                                                   |
| Test Result    | Not Executed                                        |

### Prerequisites

| # | Prerequisite                                                               |
|---|----------------------------------------------------------------------------|
| 1 | User sudah login sebagai Pembeli                                           |
| 2 | Produk dengan `id=1` tersedia dengan `stok = 10` di database              |
| 3 | Node.js API berjalan, Xendit sandbox aktif                                 |
| 4 | User memiliki minimal 1 alamat tersimpan                                   |

### Test Data — Boundary pada field `jumlah` (min:1 per validasi `BarangKeranjangController`)

| # | `jumlah` | Keterangan                      |
|---|----------|---------------------------------|
| 1 | 0        | Di bawah batas minimum (invalid)|
| 2 | 1        | Tepat di batas minimum (valid)  |
| 3 | 5        | Di tengah rentang valid         |
| 4 | 10       | Tepat di batas stok (valid)     |
| 5 | 11       | Melampaui stok tersedia (invalid)|

### Test Scenario

Verifikasi bahwa sistem menolak `jumlah` di luar batas yang diizinkan dan memproses checkout dengan benar pada batas valid.

| Step | Step Details                                                                              | Expected Results                                                           | Actual Results | Pass / Fail / Not Executed |
|------|-------------------------------------------------------------------------------------------|----------------------------------------------------------------------------|----------------|---------------------------|
| 1    | Login, kirim `POST /keranjang/item` dengan `product_id=1, jumlah=0`                       | Response 422: "The jumlah field must be at least 1."                        | -              | Not Executed              |
| 2    | Kirim `POST /keranjang/item` dengan `product_id=1, jumlah=1`                              | Response 200 success, produk (qty=1) masuk keranjang                       | -              | Not Executed              |
| 3    | Buka `/keranjang`, update jumlah item menjadi `5`                                         | Jumlah berhasil diubah ke 5, subtotal ter-update                           | -              | Not Executed              |
| 4    | Update jumlah item menjadi `10` (setara stok)                                             | Jumlah berhasil diubah ke 10, subtotal ter-update                          | -              | Not Executed              |
| 5    | Update jumlah item menjadi `11` (melebihi stok)                                           | Node.js API mengembalikan error stok tidak cukup; pesan error tampil ke user | -            | Not Executed              |
| 6    | Set jumlah kembali ke `1`, buka `/checkout`, pilih alamat, klik "Bayar Sekarang"          | Redirect ke halaman invoice Xendit sandbox berhasil                        | -              | Not Executed              |

---

## TC-BB-N03 — Update Profil (Decision Table)

| Field          | Value                                      |
|----------------|--------------------------------------------|
| Test Case ID   | TC-BB-N03                                  |
| Description    | Update Profil — Decision Table             |
| Feature        | FR11 — Edit Profil                         |
| Test Type      | Black-Box                                  |
| Created By     | Naufal Muhammad Dzulfikar                  |
| Reviewed By    | -                                          |
| Version        | 1.0                                        |
| Tester         | Naufal Muhammad Dzulfikar                  |
| Date Tested    | -                                          |
| Test Result    | Not Executed                               |

### Prerequisites

| # | Prerequisite                                                                  |
|---|-------------------------------------------------------------------------------|
| 1 | User login dengan akun `tester@sparehub.com` / `Password123!`                |
| 2 | Halaman edit profil dapat diakses di `/edit_profil`                          |
| 3 | Node.js API berjalan untuk sinkronisasi data profil                           |

### Decision Table

| Kondisi                            | R1  | R2  | R3  | R4  |
|------------------------------------|-----|-----|-----|-----|
| Nama & Email valid & tidak duplikat | Ya  | Tidak | Ya | Ya |
| `current_password` benar           | -   | -   | Ya  | Tidak |
| `password` baru diisi              | Tidak | - | Ya | Ya |
| **Aksi: Profil berhasil diperbarui** | **Ya** | **Tidak** | **Ya** | **Tidak** |

### Test Scenario

Verifikasi bahwa `ProfileController::update()` memperbarui profil hanya ketika semua kondisi validasi terpenuhi.

| Step | Step Details                                                                                             | Expected Results                                                          | Actual Results | Pass / Fail / Not Executed |
|------|----------------------------------------------------------------------------------------------------------|---------------------------------------------------------------------------|----------------|---------------------------|
| 1    | **(R1)** Login, buka `/edit_profil`, ubah Nama menjadi "Naufal Updated", Email tetap, tidak ganti password, klik Simpan | Flash message "Profil berhasil diperbarui", nama ter-update di UI | -          | Not Executed              |
| 2    | **(R2)** Ubah Email menjadi email yang **sudah dipakai akun lain**, klik Simpan                         | Pesan error "Email sudah digunakan."                                      | -              | Not Executed              |
| 3    | **(R2)** Kosongkan field Nama, klik Simpan                                                              | Pesan error "Username wajib diisi."                                       | -              | Not Executed              |
| 4    | **(R3)** Isi `current_password` = "Password123!" (benar), `password` = "NewPass456!", `password_confirmation` = "NewPass456!", klik Simpan | Flash "Profil berhasil diperbarui", password berubah | -       | Not Executed              |
| 5    | **(R4)** Isi `current_password` = "WrongPass!" (salah), `password` = "NewPass456!", klik Simpan        | Pesan error "Password saat ini salah."                                    | -              | Not Executed              |
| 6    | Upload foto profil via form `update_pfp` dengan file `.jpg` valid                                       | Flash "Foto profil berhasil diperbarui", avatar baru tampil               | -              | Not Executed              |
| 7    | Upload foto profil dengan file `.pdf` (bukan gambar)                                                    | Pesan error "Format gambar harus JPG, JPEG, atau PNG."                    | -              | Not Executed              |

---

## TC-WB-N01 — ProductController::show() Fallback Node-Eloquent (Path Coverage)

| Field          | Value                                                              |
|----------------|--------------------------------------------------------------------|
| Test Case ID   | TC-WB-N01                                                          |
| Description    | ProductController::show() Fallback Node-Eloquent — Path Coverage   |
| Feature        | FR5 — Detail Produk                                                |
| Test Type      | White-Box                                                          |
| Created By     | Naufal Muhammad Dzulfikar                                          |
| Reviewed By    | -                                                                  |
| Version        | 1.0                                                                |
| Tester         | Naufal Muhammad Dzulfikar                                          |
| Date Tested    | -                                                                  |
| Test Result    | Not Executed                                                       |

### Prerequisites

| # | Prerequisite                                                     |
|---|------------------------------------------------------------------|
| 1 | Produk dengan `id=1` ada di database MySQL                       |
| 2 | Node.js API dapat dihidupkan dan dimatikan untuk simulasi        |
| 3 | Ratings untuk `product_id=1` ada di tabel `ratings`             |

### Path Map — `ProductController::show($id)`

```
show($id)
 try {
   [B1] $response->successful() == false (HTTP non-2xx)
        → fallback: Eloquent Product::find($id)
        ├─ [B2] $product == null → abort(404)
        └─ [B3] $product != null → return view('detail-produk')

   [B4] $response->successful() == true
        $productData = $response->json('data')
        ├─ [B5] $productData == null → abort(404)
        └─ [B6] $productData != null
               → reconstruct $product object
               → load ratings via Eloquent
               → return view('detail-produk')
 } catch (\Exception $e) {
   [B7] exception thrown (timeout/koneksi gagal)
        → fallback Eloquent Product::find($id)
        ├─ [B8] $product == null → abort(404)
        └─ [B9] $product != null → return view('detail-produk')
 }
```

### Test Scenario

| Step | Path Target | Step Details                                                               | Expected Results                                                       | Actual Results | Pass / Fail / Not Executed |
|------|-------------|----------------------------------------------------------------------------|------------------------------------------------------------------------|----------------|---------------------------|
| 1    | B4, B6      | Node.js API aktif, buka `/produk/1` (produk ada)                           | Halaman detail produk tampil lengkap dengan nama, harga, stok, rating  | -              | Not Executed              |
| 2    | B4, B5      | Node.js API aktif, buka `/produk/99999` (produk tidak ada di API)          | HTTP 404 "Product not found"                                           | -              | Not Executed              |
| 3    | B7, B9      | Matikan Node.js API (simulasi timeout), buka `/produk/1`                   | Fallback ke Eloquent berhasil, halaman detail produk tetap tampil      | -              | Not Executed              |
| 4    | B7, B8      | Matikan Node.js API, buka `/produk/99999` (tidak ada di DB juga)           | HTTP 404 "Product not found"                                           | -              | Not Executed              |
| 5    | B1, B3      | Node.js API mengembalikan status 500, buka `/produk/1`                     | Fallback ke Eloquent berhasil, detail produk tampil                    | -              | Not Executed              |
| 6    | B6 (ratings)| Buka `/produk/1`, produk memiliki beberapa rating                         | Daftar rating, rata-rata bintang, dan jumlah ulasan tampil dengan benar | -             | Not Executed              |

---

## TC-WB-N02 — ProductController::index() Pagination (Branch Coverage)

| Field          | Value                                                       |
|----------------|-------------------------------------------------------------|
| Test Case ID   | TC-WB-N02                                                   |
| Description    | ProductController::index() Pagination — Branch Coverage     |
| Feature        | FR4 — Katalog Produk                                        |
| Test Type      | White-Box                                                   |
| Created By     | Naufal Muhammad Dzulfikar                                   |
| Reviewed By    | -                                                           |
| Version        | 1.0                                                         |
| Tester         | Naufal Muhammad Dzulfikar                                   |
| Date Tested    | -                                                           |
| Test Result    | Not Executed                                                |

### Prerequisites

| # | Prerequisite                                                              |
|---|---------------------------------------------------------------------------|
| 1 | Database memiliki > 10 produk untuk memicu pagination                     |
| 2 | Node.js API (`localhost:3001`) dapat diakses                              |
| 3 | Homepage `/` dapat diakses tanpa login                                    |

### Branch Map — `HomeController::index()` & Node.js Pagination

```
HomeController::index()
 └─ Product::with(['category','toko'])->orderBy('created_at','desc')->get()
     (Eloquent — tidak ada branching di controller)
     → pass $products ke view 'homepage'

Node.js GET /api/products (dengan pagination)
 ├─ [B1] query param `page` ada → ambil halaman tertentu
 └─ [B2] query param `page` tidak ada → default halaman 1

 ├─ [B3] total produk > per_page → ada next page
 └─ [B4] total produk ≤ per_page → tidak ada next page (single page)

 ├─ [B5] `page` > total halaman → return data kosong / error
 └─ [B6] `page` valid → return data produk halaman tersebut
```

### Test Scenario

| Step | Branch Target | Step Details                                                                     | Expected Results                                                       | Actual Results | Pass / Fail / Not Executed |
|------|--------------|----------------------------------------------------------------------------------|------------------------------------------------------------------------|----------------|---------------------------|
| 1    | B2, B3       | Buka homepage `/` (default page 1, > 10 produk di DB)                            | Produk halaman pertama tampil, terdapat kontrol navigasi "Next"        | -              | Not Executed              |
| 2    | B1, B6       | Klik tombol "Next" atau akses `GET /api/products?page=2`                         | Produk halaman 2 tampil, kontrol "Previous" tersedia                   | -              | Not Executed              |
| 3    | B1, B5       | Akses `GET /api/products?page=9999` (halaman yang jauh melampaui total)          | Data kosong atau pesan "Halaman tidak ditemukan"                        | -              | Not Executed              |
| 4    | B2, B4       | Seed database dengan hanya 3 produk, buka homepage                               | Semua produk tampil tanpa pagination (single page, tidak ada next)     | -              | Not Executed              |
| 5    | B1, B6       | Klik tombol "Previous" dari halaman 2                                            | Kembali ke halaman 1, produk awal tampil                               | -              | Not Executed              |
