# Test Cases — Bagas Pratama (103022300035)

**Project:** SpareHub  
**PIC:** Bagas Pratama  
**Coverage Area:** Halaman Riwayat Pesanan & Checkout  

---

## TC-BB-B01 — Pagination Katalog (Equivalence Partitioning)

| Field          | Value                                               |
|----------------|-----------------------------------------------------|
| Test Case ID   | TC-BB-B01                                           |
| Description    | Pagination Katalog — Equivalence Partitioning       |
| Feature        | FR4 — Katalog Produk                                |
| Test Type      | Black-Box                                           |
| Created By     | Bagas Pratama                                       |
| Reviewed By    | -                                                   |
| Version        | 1.0                                                 |
| Tester         | Bagas Pratama                                       |
| Date Tested    | -                                                   |
| Test Result    | Not Executed                                        |

### Prerequisites

| # | Prerequisite                                                        |
|---|---------------------------------------------------------------------|
| 1 | Aplikasi berjalan dan homepage `/` dapat diakses                    |
| 2 | Database memiliki > 10 produk (untuk memicu pagination)             |
| 3 | Node.js API aktif                                                   |

### Test Data — Partisi Equivalence untuk parameter `page`

| Kelas         | Input `page` | Keterangan                                      |
|---------------|--------------|-------------------------------------------------|
| Valid — awal  | `1`          | Halaman pertama, selalu valid                   |
| Valid — tengah| `2`          | Halaman pertengahan, produk tersedia            |
| Invalid — 0   | `0`          | Di bawah batas minimum                         |
| Invalid — negatif | `-1`    | Nilai negatif                                   |
| Invalid — melampaui total | `9999` | Halaman tidak ada                        |
| Invalid — bukan angka | `abc` | Tipe data salah                             |

### Test Scenario

Verifikasi bahwa sistem menampilkan katalog produk dengan benar untuk berbagai nilai parameter halaman.

| Step | Step Details                                                                    | Expected Results                                                         | Actual Results | Pass / Fail / Not Executed |
|------|---------------------------------------------------------------------------------|--------------------------------------------------------------------------|----------------|---------------------------|
| 1    | Buka homepage `/` (halaman 1 default)                                           | Produk katalog tampil urut `created_at DESC`, pagination tersedia        | -              | Not Executed              |
| 2    | Navigasi ke halaman 2 via tombol "Next" / `GET /api/products?page=2`            | Produk halaman 2 tampil, tombol "Previous" aktif                         | -              | Not Executed              |
| 3    | Akses `GET /api/products?page=0`                                                | Error atau redirect ke halaman 1, tidak ada crash                        | -              | Not Executed              |
| 4    | Akses `GET /api/products?page=-1`                                               | Error atau redirect ke halaman 1, tidak ada crash                        | -              | Not Executed              |
| 5    | Akses `GET /api/products?page=9999`                                             | Data kosong atau pesan "Tidak ada produk", tidak ada crash               | -              | Not Executed              |
| 6    | Akses `GET /api/products?page=abc`                                              | Error validasi atau default ke halaman 1, tidak ada crash                | -              | Not Executed              |

---

## TC-BB-B02 — Deskripsi Produk (Boundary Value Analysis)

| Field          | Value                                              |
|----------------|----------------------------------------------------|
| Test Case ID   | TC-BB-B02                                          |
| Description    | Deskripsi Produk — Boundary Value Analysis         |
| Feature        | FR5 — Detail Produk                                |
| Test Type      | Black-Box                                          |
| Created By     | Bagas Pratama                                      |
| Reviewed By    | -                                                  |
| Version        | 1.0                                                |
| Tester         | Bagas Pratama                                      |
| Date Tested    | -                                                  |
| Test Result    | Not Executed                                       |

### Prerequisites

| # | Prerequisite                                                    |
|---|-----------------------------------------------------------------|
| 1 | User login sebagai Penjual dan memiliki toko                    |
| 2 | Halaman unggah produk dapat diakses                             |
| 3 | Field `deskripsi` di `ProductController::store()` required string |

### Test Data — Boundary pada panjang field `deskripsi`

| # | Panjang Deskripsi | Konten                          | Keterangan              |
|---|-------------------|---------------------------------|-------------------------|
| 1 | 0 karakter        | *(kosong)*                      | Di bawah minimum (invalid)|
| 2 | 1 karakter        | `A`                             | Tepat batas minimum valid|
| 3 | 255 karakter      | `A` × 255                      | Batas normal            |
| 4 | 1000 karakter     | `A` × 1000                     | Deskripsi panjang       |
| 5 | 5001 karakter     | `A` × 5001                     | Potensi batas maksimum  |

### Test Scenario

Verifikasi bahwa sistem memvalidasi dan menampilkan deskripsi produk dengan benar pada berbagai panjang input.

| Step | Step Details                                                                                | Expected Results                                                        | Actual Results | Pass / Fail / Not Executed |
|------|---------------------------------------------------------------------------------------------|-------------------------------------------------------------------------|----------------|---------------------------|
| 1    | Login sebagai penjual, buka form unggah produk, isi semua field kecuali `deskripsi` kosong, submit | Response error: "The deskripsi field is required."               | -              | Not Executed              |
| 2    | Isi `deskripsi` = 1 karakter `A`, field lain valid, submit                                  | Produk berhasil dibuat, redirect/response sukses                        | -              | Not Executed              |
| 3    | Isi `deskripsi` = 255 karakter, field lain valid, submit                                    | Produk berhasil dibuat                                                  | -              | Not Executed              |
| 4    | Isi `deskripsi` = 1000 karakter, field lain valid, submit                                   | Produk berhasil dibuat, deskripsi tampil penuh di halaman detail produk | -              | Not Executed              |
| 5    | Buka halaman detail produk (`/produk/{id}`) produk yang baru dibuat                         | Nama, harga, stok, foto, deskripsi, rating tampil dengan benar          | -              | Not Executed              |
| 6    | Buka halaman detail produk yang tidak ada (`/produk/99999`)                                 | HTTP 404 dengan pesan "Product not found"                               | -              | Not Executed              |

---

## TC-BB-B03 — Upload Produk (Decision Table)

| Field          | Value                                    |
|----------------|------------------------------------------|
| Test Case ID   | TC-BB-B03                                |
| Description    | Upload Produk — Decision Table           |
| Feature        | FR14 — Mengunggah Produk                 |
| Test Type      | Black-Box                                |
| Created By     | Bagas Pratama                            |
| Reviewed By    | -                                        |
| Version        | 1.0                                      |
| Tester         | Bagas Pratama                            |
| Date Tested    | -                                        |
| Test Result    | Not Executed                             |

### Prerequisites

| # | Prerequisite                                              |
|---|-----------------------------------------------------------|
| 1 | User login sebagai Penjual                                |
| 2 | Penjual sudah memiliki toko (`toko` terhubung ke user)    |
| 3 | Tersedia gambar `.jpg` dan `.pdf` untuk pengujian         |

### Decision Table

| Kondisi                          | R1  | R2  | R3  | R4  |
|----------------------------------|-----|-----|-----|-----|
| User punya toko                  | Ya  | Tidak | Ya | Ya |
| Semua field wajib diisi          | Ya  | -   | Tidak | Ya |
| File gambar format valid (jpg/png) | Ya | -   | -   | Tidak (.pdf) |
| **Aksi: Produk berhasil diunggah** | **Ya** | **Tidak** | **Tidak** | **Tidak** |

### Test Scenario

Verifikasi bahwa `ProductController::store()` mengunggah produk hanya ketika semua kondisi terpenuhi.

| Step | Step Details                                                                                             | Expected Results                                                          | Actual Results | Pass / Fail / Not Executed |
|------|----------------------------------------------------------------------------------------------------------|---------------------------------------------------------------------------|----------------|---------------------------|
| 1    | **(R1)** Login sebagai penjual bertoko, isi semua field dengan data valid + gambar `.jpg`, submit        | JSON `{ success: true }`, produk baru muncul di katalog toko              | -              | Not Executed              |
| 2    | **(R2)** Login sebagai user yang **belum punya toko**, coba akses `POST /product/store`                  | JSON `{ success: false, message: 'User belum memiliki toko' }` status 400 | -              | Not Executed              |
| 3    | **(R3)** Login penjual bertoko, submit form tanpa mengisi `nama` produk                                  | Response 422: "The nama field is required."                               | -              | Not Executed              |
| 4    | **(R3)** Submit form tanpa mengisi `harga`                                                               | Response 422: "The harga field is required."                              | -              | Not Executed              |
| 5    | **(R4)** Submit form dengan file `.pdf` sebagai gambar                                                   | Response 422: "The image must be a file of type: jpg, jpeg, png."         | -              | Not Executed              |
| 6    | **(R1)** Submit produk valid, kemudian buka `GET /api/products` via Postman                              | Produk baru muncul di daftar dengan semua field benar                     | -              | Not Executed              |

---

## TC-WB-B01 — OrderController::history(), riwayatPesanan() (Branch Coverage)

| Field          | Value                                                              |
|----------------|--------------------------------------------------------------------|
| Test Case ID   | TC-WB-B01                                                          |
| Description    | OrderController::history(), riwayatPesanan() — Branch Coverage     |
| Feature        | FR8 — Riwayat Pesanan                                              |
| Test Type      | White-Box                                                          |
| Created By     | Bagas Pratama                                                      |
| Reviewed By    | -                                                                  |
| Version        | 1.0                                                                |
| Tester         | Bagas Pratama                                                      |
| Date Tested    | -                                                                  |
| Test Result    | Not Executed                                                       |

### Prerequisites

| # | Prerequisite                                                    |
|---|-----------------------------------------------------------------|
| 1 | Akun pembeli dengan minimal 2 pesanan di database               |
| 2 | Node.js API (`localhost:3001`) dapat dihidupkan/dimatikan       |
| 3 | Akun pembeli lain (untuk uji ownership)                         |

### Branch Map

```
riwayatPesanan()
 ├─ [B1] Auth::check() == false → redirect('/login')
 └─ [B2] Auth::check() == true
     try {
       ├─ [B3] $response->successful() == true
       │       → map orders ke collection objects → view('riwayat_pesanan')
       └─ [B4] exception / API failure
               → view('riwayat_pesanan', ['orders' => collect([]), 'error' => '...'])
     }

history() [API endpoint]
 ├─ [B5] Auth::check() == false → JSON 401
 └─ [B6] Auth::check() == true
     ├─ [B7] $response->successful() == true → JSON { success: true, data }
     └─ [B8] exception thrown → JSON 500

show($id)
 ├─ [B9]  Auth::check() == false → redirect('/login')
 └─ [B10] Auth::check() == true
     ├─ [B11] $response->successful() == true
     │        ├─ [B12] $order->user_id != $user->id → redirect riwayat + error
     │        └─ [B13] $order->user_id == $user->id → view('order_detail')
     └─ [B14] exception → redirect riwayat + error

cancelForm($id)
 ├─ [B15] Auth::check() == false → redirect('/login')
 └─ [B16] Auth::check() == true
     ├─ [B17] $response->successful() == true → redirect riwayat + success
     └─ [B18] exception → redirect back + error
```

### Test Scenario

| Step | Branch Target | Step Details                                                                  | Expected Results                                                           | Actual Results | Pass / Fail / Not Executed |
|------|--------------|-------------------------------------------------------------------------------|----------------------------------------------------------------------------|----------------|---------------------------|
| 1    | B2, B3       | Login, akses `GET /riwayat-pesanan` saat API aktif                            | Halaman riwayat pesanan tampil dengan daftar pesanan user                  | -              | Not Executed              |
| 2    | B1           | Logout, akses `GET /riwayat-pesanan`                                          | Redirect ke `/login`                                                       | -              | Not Executed              |
| 3    | B2, B4       | Login, matikan Node.js API, akses `GET /riwayat-pesanan`                      | Halaman tampil dengan pesanan kosong dan pesan error "Gagal memuat..."     | -              | Not Executed              |
| 4    | B6, B7       | Login, akses `GET /api/orders/history`                                        | JSON `{ success: true, data: [...orders] }`                                | -              | Not Executed              |
| 5    | B5           | Logout, akses `GET /api/orders/history`                                       | JSON `{ success: false, message: 'Unauthorized' }` status 401              | -              | Not Executed              |
| 6    | B10, B13     | Login user A, akses `GET /orders/{id}` milik user A sendiri                   | Halaman detail pesanan tampil dengan benar                                 | -              | Not Executed              |
| 7    | B10, B12     | Login user A, akses `GET /orders/{id}` milik user B                           | Redirect ke `/riwayat-pesanan` dengan error "Anda tidak memiliki akses"    | -              | Not Executed              |
| 8    | B16, B17     | Login, akses `POST /orders/{id}/cancel` pada pesanan berstatus "pending"      | Redirect ke `/riwayat-pesanan` dengan flash "Pesanan berhasil dibatalkan!" | -              | Not Executed              |

---

## TC-WB-B02 — CheckoutController::index(), pay(), callback (Branch Coverage)

| Field          | Value                                                                |
|----------------|----------------------------------------------------------------------|
| Test Case ID   | TC-WB-B02                                                            |
| Description    | CheckoutController::index(), pay(), callback — Branch Coverage       |
| Feature        | FR7 — Melakukan Pemesanan                                            |
| Test Type      | White-Box                                                            |
| Created By     | Bagas Pratama                                                        |
| Reviewed By    | -                                                                    |
| Version        | 1.0                                                                  |
| Tester         | Bagas Pratama                                                        |
| Date Tested    | -                                                                    |
| Test Result    | Not Executed                                                         |

### Prerequisites

| # | Prerequisite                                                              |
|---|---------------------------------------------------------------------------|
| 1 | User login, keranjang berisi minimal 1 item                               |
| 2 | User memiliki alamat tersimpan                                            |
| 3 | Xendit sandbox API key dikonfigurasi di `.env`                            |
| 4 | Node.js API aktif                                                         |

### Branch Map

```
index()
 ├─ [B1] Auth::check() == false → redirect('/login')
 └─ [B2] Auth::check() == true
     ├─ [B3] alamatResponse->successful() == false → $alamats = collect([])
     ├─ [B4] alamatResponse->successful() == true  → $alamats dari API
     ├─ [B5] cartResponse->successful() == false   → return view + error
     └─ [B6] cartResponse->successful() == true    → hitung subtotal, return view

pay()
 ├─ [B7]  Auth::check() == false → JSON 401
 └─ [B8]  Auth::check() == true
     ├─ [B9]  validation fails → JSON 422
     └─ [B10] validation pass
          ├─ [B11] orderResponse not successful → exception "Order tidak ditemukan"
          └─ [B12] orderResponse successful
               ├─ [B13] order->user_id != user->id → JSON 403
               └─ [B14] order->user_id == user->id
                    ├─ [B15] order->status != 'pending' → exception
                    └─ [B16] order->status == 'pending'
                         ├─ [B17] Xendit ApiException → JSON 500
                         └─ [B18] Invoice berhasil dibuat → JSON { success: true, invoice_url }

paymentSuccess()
 ├─ [B19] session('last_order_id') ada → log, clear session
 └─ [B20] session tidak ada → langsung redirect riwayat
```

### Test Scenario

| Step | Branch Target | Step Details                                                                            | Expected Results                                                                | Actual Results | Pass / Fail / Not Executed |
|------|--------------|-----------------------------------------------------------------------------------------|---------------------------------------------------------------------------------|----------------|---------------------------|
| 1    | B2, B4, B6   | Login, isi keranjang, akses `GET /checkout` saat API aktif                              | Halaman checkout tampil dengan daftar item, alamat, dan total harga             | -              | Not Executed              |
| 2    | B1           | Logout, akses `GET /checkout`                                                           | Redirect ke `/login`                                                            | -              | Not Executed              |
| 3    | B2, B3, B6   | Login, matikan endpoint alamat di Node.js, akses `GET /checkout`                        | Halaman checkout tampil, bagian alamat kosong (graceful degradation)            | -              | Not Executed              |
| 4    | B2, B6, B5   | Login, matikan Node.js API sepenuhnya, akses `GET /checkout`                            | Halaman checkout tampil dengan pesan "Gagal memuat data keranjang"              | -              | Not Executed              |
| 5    | B8, B10, B16, B18 | Login, akses `POST /checkout/pay` dengan `order_id` valid, `total` valid         | JSON `{ success: true, invoice_url: '...' }`, redirect ke Xendit sandbox        | -              | Not Executed              |
| 6    | B7           | Logout, kirim `POST /checkout/pay`                                                      | JSON `{ success: false, message: 'Unauthorized' }` status 401                  | -              | Not Executed              |
| 7    | B8, B9       | Login, kirim `POST /checkout/pay` tanpa `order_id`                                     | JSON 422 Validation Error                                                       | -              | Not Executed              |
| 8    | B8, B10, B13 | Login sebagai user B, kirim `POST /checkout/pay` dengan `order_id` milik user A         | JSON `{ success: false, message: 'Anda tidak memiliki akses ke order ini' }` 403 | -            | Not Executed              |
| 9    | B8, B10, B15 | Login, kirim `POST /checkout/pay` dengan `order_id` yang statusnya sudah `paid`         | JSON 500 / error "Order sudah diproses sebelumnya"                              | -              | Not Executed              |
| 10   | B19          | Setelah bayar sukses di Xendit sandbox, akses `GET /payment/success`                    | Redirect ke `/riwayat-pesanan` dengan flash "Pembayaran berhasil!"              | -              | Not Executed              |
