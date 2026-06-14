# Test Cases — Raffa Rizky Febryan (103022330138)

**Project:** Medcom  
**PIC:** Raffa Rizky Febryan  
**Coverage Area:** Halaman Register, Toko, & Rating  

---

## TC-BB-RA01 — Rating 1-5 (Equivalence Partitioning)

| Field          | Value                                              |
|----------------|----------------------------------------------------|
| Test Case ID   | TC-BB-RA01                                         |
| Description    | Rating 1-5 — Equivalence Partitioning              |
| Feature        | FR10 — Memberikan Ulasan                           |
| Test Type      | Black-Box                                          |
| Created By     | Raffa Rizky Febryan                                |
| Reviewed By    | -                                                  |
| Version        | 1.0                                                |
| Tester         | Raffa Rizky Febryan                                |
| Date Tested    | -                                                  |
| Test Result    | Not Executed                                       |

### Prerequisites

| # | Prerequisite                                                              |
|---|---------------------------------------------------------------------------|
| 1 | User login sebagai Pembeli                                                |
| 2 | User memiliki pesanan berstatus `paid` / `completed` untuk produk tertentu|
| 3 | Halaman rating dapat diakses di `/ratings`                                |

### Test Data — Partisi Equivalence untuk field `rating` (min:1|max:5 per validasi)

| Kelas           | Input `rating` | `review`            | Keterangan                          |
|-----------------|---------------|---------------------|-------------------------------------|
| Valid           | `3`           | "Produk oke"        | Di dalam rentang 1-5, valid         |
| Valid — min     | `1`           | "Kurang memuaskan"  | Tepat batas bawah valid             |
| Valid — max     | `5`           | "Sangat bagus!"     | Tepat batas atas valid              |
| Invalid — bawah | `0`           | "Test"              | Di bawah batas minimum              |
| Invalid — atas  | `6`           | "Test"              | Di atas batas maksimum              |
| Invalid — review kosong | `4`   | *(kosong)*          | Review wajib diisi                  |

### Test Scenario

Verifikasi bahwa `RatingController::store()` memvalidasi partisi nilai rating dan menerima/menolak sesuai aturan.

| Step | Step Details                                                                                 | Expected Results                                                          | Actual Results | Pass / Fail / Not Executed |
|------|----------------------------------------------------------------------------------------------|---------------------------------------------------------------------------|----------------|---------------------------|
| 1    | Login, buka `/ratings`, pilih produk, masukkan `rating=3`, isi review "Produk oke", submit   | Redirect ke `/ratings` dengan flash "Rating berhasil ditambahkan"         | -              | Not Executed              |
| 2    | Pilih produk lain, masukkan `rating=1`, review "Kurang memuaskan", submit                    | Rating berhasil disimpan, bintang 1 tampil di halaman detail produk       | -              | Not Executed              |
| 3    | Masukkan `rating=5`, review "Sangat bagus!", submit                                          | Rating berhasil disimpan, bintang 5 tampil                                | -              | Not Executed              |
| 4    | Masukkan `rating=0`, review "Test", submit                                                   | Pesan error validasi "The rating must be at least 1."                     | -              | Not Executed              |
| 5    | Masukkan `rating=6`, review "Test", submit                                                   | Pesan error validasi "The rating must not be greater than 5."             | -              | Not Executed              |
| 6    | Masukkan `rating=4`, review **kosong**, submit                                               | Pesan error "The review field is required."                               | -              | Not Executed              |
| 7    | Klik tombol hapus pada rating yang sudah dibuat                                              | Rating dihapus via Node.js API, halaman `/ratings` di-refresh             | -              | Not Executed              |

---

## TC-BB-RA02 — Nama Toko (Boundary Value Analysis)

| Field          | Value                                         |
|----------------|-----------------------------------------------|
| Test Case ID   | TC-BB-RA02                                    |
| Description    | Nama Toko — Boundary Value Analysis           |
| Feature        | FR12 — Membuat Toko                           |
| Test Type      | Black-Box                                     |
| Created By     | Raffa Rizky Febryan                           |
| Reviewed By    | -                                             |
| Version        | 1.0                                           |
| Tester         | Raffa Rizky Febryan                           |
| Date Tested    | -                                             |
| Test Result    | Not Executed                                  |

### Prerequisites

| # | Prerequisite                                                                    |
|---|---------------------------------------------------------------------------------|
| 1 | User login sebagai pengguna yang **belum punya toko**                           |
| 2 | Halaman buat toko dapat diakses di `/toko/create`                               |
| 3 | File gambar `.jpg` tersedia untuk logo toko                                     |

### Test Data — Boundary pada panjang `nama_toko` (max:255 per validasi `TokoController`)

| # | `nama_toko`       | Panjang  | Keterangan               |
|---|-------------------|----------|--------------------------|
| 1 | *(kosong)*        | 0        | Di bawah minimum (invalid)|
| 2 | `A`               | 1        | Tepat batas minimum valid |
| 3 | `Toko Raffa`      | 11       | Nama normal              |
| 4 | `A` × 255         | 255      | Tepat batas maksimum valid|
| 5 | `A` × 256         | 256      | Melampaui batas (invalid) |

### Test Scenario

Verifikasi bahwa `TokoController::store()` memvalidasi panjang nama toko sesuai batas yang ditentukan.

| Step | Step Details                                                                                                 | Expected Results                                                           | Actual Results | Pass / Fail / Not Executed |
|------|--------------------------------------------------------------------------------------------------------------|----------------------------------------------------------------------------|----------------|---------------------------|
| 1    | Login user tanpa toko, buka `/toko/create`, submit form dengan `nama_toko` **kosong**, field lain valid      | Pesan error "The nama toko field is required."                             | -              | Not Executed              |
| 2    | Submit form dengan `nama_toko` = 1 karakter `A`, field lain valid + logo `.jpg`                              | Toko berhasil dibuat, redirect ke `/toko`                                  | -              | Not Executed              |
| 3    | Hapus toko tersebut, buat toko baru dengan `nama_toko` = 255 karakter                                        | Toko berhasil dibuat                                                       | -              | Not Executed              |
| 4    | Hapus toko, buat toko baru dengan `nama_toko` = 256 karakter                                                 | Pesan error validasi "The nama toko must not be greater than 255 characters." | -           | Not Executed              |
| 5    | Coba buat toko **kedua** dengan user yang sudah punya toko                                                    | Redirect dengan error "Anda sudah memiliki toko"                           | -              | Not Executed              |
| 6    | Submit form pembuatan toko dengan logo format `.gif` (valid per validasi)                                    | Toko berhasil dibuat, logo `.gif` tersimpan di storage                     | -              | Not Executed              |

---

## TC-BB-RA03 — Kelola Produk (Decision Table)

| Field          | Value                                      |
|----------------|--------------------------------------------|
| Test Case ID   | TC-BB-RA03                                 |
| Description    | Kelola Produk — Decision Table             |
| Feature        | FR15 — Mengelola Produk                    |
| Test Type      | Black-Box                                  |
| Created By     | Raffa Rizky Febryan                        |
| Reviewed By    | -                                          |
| Version        | 1.0                                        |
| Tester         | Raffa Rizky Febryan                        |
| Date Tested    | -                                          |
| Test Result    | Not Executed                               |

### Prerequisites

| # | Prerequisite                                                     |
|---|------------------------------------------------------------------|
| 1 | User A login sebagai Penjual dan memiliki toko + produk          |
| 2 | Produk dengan `id=1` dimiliki oleh toko User A                   |
| 3 | Node.js API aktif                                                |

### Decision Table

| Kondisi                             | R1  | R2  | R3  | R4  |
|-------------------------------------|-----|-----|-----|-----|
| Field data produk valid             | Ya  | Tidak | Ya | Ya |
| Gambar baru di-upload (opsional)    | Tidak | -  | Ya  | Tidak |
| Node.js API berhasil diproses       | Ya  | -   | Ya  | Tidak |
| **Aksi: Produk berhasil diperbarui/dihapus** | **Ya** | **Tidak** | **Ya** | **Tidak** |

### Test Scenario

Verifikasi bahwa `ProductController::update()` dan `destroy()` mengelola produk sesuai kondisi yang diberikan.

| Step | Step Details                                                                                           | Expected Results                                                        | Actual Results | Pass / Fail / Not Executed |
|------|--------------------------------------------------------------------------------------------------------|-------------------------------------------------------------------------|----------------|---------------------------|
| 1    | **(R1 — update)** Login penjual, kirim `PUT /product/1` dengan nama, harga, stok valid tanpa gambar baru | JSON `{ success: true }`, produk ter-update di katalog               | -              | Not Executed              |
| 2    | **(R3 — update + gambar)** Kirim `PUT /product/1` dengan semua field valid + gambar `.jpg` baru         | JSON `{ success: true }`, gambar produk diperbarui                    | -              | Not Executed              |
| 3    | **(R2 — update)** Kirim `PUT /product/1` dengan `harga` = string `abc` (bukan numerik)                 | Response 422: "The harga field must be a number."                     | -              | Not Executed              |
| 4    | **(R2 — update)** Kirim `PUT /product/1` dengan `stok` = string `abc`                                  | Response 422: "The stok field must be an integer."                    | -              | Not Executed              |
| 5    | **(R4 — delete)** Matikan Node.js API, kirim `DELETE /product/1`                                       | JSON `{ success: false }` status 500                                  | -              | Not Executed              |
| 6    | **(R1 — delete)** Hidupkan Node.js API, kirim `DELETE /product/1`                                      | JSON `{ success: true }`, produk tidak lagi tampil di katalog         | -              | Not Executed              |

---

## TC-WB-RA01 — RegisteredUserController::store() (Branch Coverage)

| Field          | Value                                                         |
|----------------|---------------------------------------------------------------|
| Test Case ID   | TC-WB-RA01                                                    |
| Description    | RegisteredUserController::store() — Branch Coverage           |
| Feature        | FR1 — Registrasi Akun                                         |
| Test Type      | White-Box                                                     |
| Created By     | Raffa Rizky Febryan                                           |
| Reviewed By    | -                                                             |
| Version        | 1.0                                                           |
| Tester         | Raffa Rizky Febryan                                           |
| Date Tested    | -                                                             |
| Test Result    | Not Executed                                                  |

### Prerequisites

| # | Prerequisite                                                      |
|---|-------------------------------------------------------------------|
| 1 | Halaman registrasi dapat diakses di `/register`                   |
| 2 | Email `raffa.test@medcom.com` belum terdaftar                   |
| 3 | Email `existing@medcom.com` sudah terdaftar di database         |

### Branch Map — `RegisteredUserController::store()`

```
store(Request $request)
 ├─ [B1] validation fails
 │       ├─ [B1a] 'name' kosong atau > 255 char
 │       ├─ [B1b] 'email' bukan format email
 │       ├─ [B1c] 'email' sudah digunakan (unique)
 │       ├─ [B1d] 'phone' kosong atau > 20 char
 │       ├─ [B1e] 'password' tidak terpenuhi Rules::defaults()
 │       └─ [B1f] 'password' ≠ 'password_confirmation'
 │       → throw ValidationException (kembali ke form)
 │
 └─ [B2] validation pass
         → User::create([...])
         → event(new Registered($user))
         → Auth::login($user)
         → redirect('/')
```

### Test Scenario

| Step | Branch Target | Step Details                                                                           | Expected Results                                                        | Actual Results | Pass / Fail / Not Executed |
|------|--------------|----------------------------------------------------------------------------------------|-------------------------------------------------------------------------|----------------|---------------------------|
| 1    | B2           | Buka `/register`, isi semua field valid (nama, email baru, phone, password cocok), klik Register | Redirect ke `/`, user ter-login otomatis, profil muncul di navbar | -          | Not Executed              |
| 2    | B1a          | Isi form dengan `name` kosong, klik Register                                           | Pesan error "The name field is required."                               | -              | Not Executed              |
| 3    | B1b          | Isi `email` = `bukan-email`, klik Register                                             | Pesan error "The email field must be a valid email address."            | -              | Not Executed              |
| 4    | B1c          | Isi `email` = `existing@medcom.com` (sudah ada), klik Register                      | Pesan error "The email has already been taken."                         | -              | Not Executed              |
| 5    | B1d          | Isi `phone` kosong, klik Register                                                      | Pesan error "The phone field is required."                              | -              | Not Executed              |
| 6    | B1f          | Isi `password` = "Password123!", `password_confirmation` = "Password456!", klik Register | Pesan error "The password field confirmation does not match."         | -              | Not Executed              |
| 7    | B1e          | Isi `password` = `short` (< 8 karakter, tidak memenuhi defaults), klik Register       | Pesan error validasi password (panjang minimum tidak terpenuhi)         | -              | Not Executed              |
| 8    | B2           | Setelah registrasi sukses (step 1), verifikasi `pfpPath` default ter-set               | `pfpPath` user di database adalah URL avatar default Medcom           | -              | Not Executed              |

---

## TC-WB-RA02 — TokoController (index, store, update, destroy) (Branch Coverage)

| Field          | Value                                                              |
|----------------|--------------------------------------------------------------------|
| Test Case ID   | TC-WB-RA02                                                         |
| Description    | TokoController (index, store, update, destroy) — Branch Coverage   |
| Feature        | FR12 — Membuat Toko                                                |
| Test Type      | White-Box                                                          |
| Created By     | Raffa Rizky Febryan                                                |
| Reviewed By    | -                                                                  |
| Version        | 1.0                                                                |
| Tester         | Raffa Rizky Febryan                                                |
| Date Tested    | -                                                                  |
| Test Result    | Not Executed                                                       |

### Prerequisites

| # | Prerequisite                                                             |
|---|--------------------------------------------------------------------------|
| 1 | User A login — belum punya toko                                          |
| 2 | User B login — sudah punya toko dengan `toko.id = 2`                     |
| 3 | Node.js API dapat dihidupkan dan dimatikan untuk simulasi                |
| 4 | File gambar `.jpg` tersedia                                              |

### Branch Map

```
index()
 try {
   [B1] API check successful → result['data']['hasToko'] == false → redirect('toko.create')
   [B2] API check successful → result['data']['hasToko'] == true
        ├─ [B3] tokoResponse successful → view('profil_toko')
        └─ [B4] tokoResponse failed → exception
   [B5] API exception → fallback Eloquent
        ├─ [B6] Toko::where()->first() == null → redirect('toko.create')
        └─ [B7] Toko::where()->first() != null → view('profil_toko')
 }

store()
 [B8]  validation fails → back()->withErrors()
 [B9]  validation pass
       ├─ [B10] User sudah punya toko (check API) → back() + error 'sudah punya toko'
       └─ [B11] User belum punya toko
                [B12] Upload logo, kirim ke Node API
                ├─ [B13] Node API status !2xx → hapus file, throw Exception → back() + error
                └─ [B14] Node API status 2xx  → redirect('profil_toko') + success

update()
 [B15] validation fails → JSON 422
 [B16] validation pass
       ├─ [B17] $toko->user_id != auth()->id() → JSON 403
       └─ [B18] $toko->user_id == auth()->id()
                ├─ [B19] $request->hasFile('logo') == true → hapus logo lama, upload baru
                └─ [B20] $request->hasFile('logo') == false → pertahankan logo lama
                → update DB, sync Node API → JSON { success: true }

destroy()
 ├─ [B21] $toko->user_id != auth()->id() → JSON 403
 └─ [B22] $toko->user_id == auth()->id()
           → delete Node API, hapus logo, delete DB → JSON { success: true }
```

### Test Scenario

| Step | Branch Target | Step Details                                                                          | Expected Results                                                            | Actual Results | Pass / Fail / Not Executed |
|------|--------------|-----------------------------------------------------------------------------------------|-----------------------------------------------------------------------------|----------------|---------------------------|
| 1    | B1           | Login User A (tanpa toko), akses `GET /toko`                                           | Redirect ke `/toko/create`                                                  | -              | Not Executed              |
| 2    | B2, B3       | Login User B (sudah punya toko), akses `GET /toko`                                     | Halaman profil toko tampil dengan data toko User B                          | -              | Not Executed              |
| 3    | B5, B7       | Matikan Node.js API, login User B, akses `GET /toko`                                   | Fallback ke Eloquent, halaman profil toko tetap tampil                      | -              | Not Executed              |
| 4    | B5, B6       | Matikan Node.js API, login User A (tidak ada di DB juga), akses `GET /toko`            | Redirect ke `/toko/create`                                                  | -              | Not Executed              |
| 5    | B9, B11, B14 | Login User A, buka `/toko/create`, isi semua field valid + logo `.jpg`, submit          | Toko dibuat, redirect ke `/toko` dengan flash "Toko berhasil dibuat!"       | -              | Not Executed              |
| 6    | B10          | Login User B (sudah punya toko), kirim `POST /toko` lagi                               | Redirect dengan error "Anda sudah memiliki toko"                            | -              | Not Executed              |
| 7    | B8           | Login User A, submit form toko dengan `nama_toko` kosong                               | Redirect back dengan error validasi                                         | -              | Not Executed              |
| 8    | B9, B11, B13 | Login User A, matikan Node.js API, submit form toko valid                              | File logo dihapus, redirect back dengan error "Gagal menyimpan toko ke API" | -              | Not Executed              |
| 9    | B16, B18, B20| Login User B, kirim `PUT /toko/2` dengan data valid tanpa logo baru                    | JSON `{ success: true }`, data toko diperbarui                              | -              | Not Executed              |
| 10   | B16, B18, B19| Login User B, kirim `PUT /toko/2` dengan data valid + logo baru `.jpg`                 | JSON `{ success: true, logo_url: '...' }`, logo toko diperbarui             | -              | Not Executed              |
| 11   | B17          | Login User A, kirim `PUT /toko/2` (milik User B)                                       | JSON `{ success: false, message: 'Anda tidak memiliki akses...' }` 403      | -              | Not Executed              |
| 12   | B22          | Login User B, kirim `DELETE /toko/2`                                                   | JSON `{ success: true, message: 'Toko berhasil dihapus' }`, toko terhapus   | -              | Not Executed              |
| 13   | B21          | Login User A, kirim `DELETE /toko/2` (milik User B)                                   | JSON `{ success: false, message: 'Anda tidak memiliki akses...' }` 403      | -              | Not Executed              |
