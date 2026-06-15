# 📖 User Manual — Medcom Marketplace

Panduan lengkap penggunaan aplikasi web **Medcom**, sebuah platform marketplace/e-commerce tempat pengguna dapat berbelanja produk sekaligus membuka toko dan berjualan.

---

## Daftar Isi

1. [Pengenalan](#1-pengenalan)
2. [Memulai (Registrasi & Login)](#2-memulai-registrasi--login)
3. [Menjelajah & Mencari Produk](#3-menjelajah--mencari-produk)
4. [Melihat Detail Produk](#4-melihat-detail-produk)
5. [Keranjang Belanja](#5-keranjang-belanja)
6. [Checkout & Pembayaran](#6-checkout--pembayaran)
7. [Riwayat Pesanan](#7-riwayat-pesanan)
8. [Memberi Rating & Ulasan](#8-memberi-rating--ulasan)
9. [Mengelola Profil Akun](#9-mengelola-profil-akun)
10. [Mengelola Alamat](#10-mengelola-alamat)
11. [Membuka & Mengelola Toko (Penjual)](#11-membuka--mengelola-toko-penjual)
12. [Mengelola Produk Toko (Penjual)](#12-mengelola-produk-toko-penjual)
13. [Mengelola Pesanan Masuk (Penjual)](#13-mengelola-pesanan-masuk-penjual)
14. [Status Pesanan](#14-status-pesanan)
15. [FAQ & Troubleshooting](#15-faq--troubleshooting)

---

## 1. Pengenalan

**Medcom** adalah aplikasi marketplace di mana setiap pengguna dapat berperan ganda:

- **Sebagai Pembeli** — menjelajah produk, menambahkan ke keranjang, melakukan checkout, membayar, melacak pesanan, dan memberi ulasan.
- **Sebagai Penjual** — membuka toko, mengunggah produk, serta mengelola pesanan yang masuk.

### Teknologi yang Digunakan

| Komponen | Teknologi |
|----------|-----------|
| Frontend & Web | Laravel (Blade), Tailwind CSS, JavaScript |
| Backend API | Node.js (Express) |
| Database | MySQL / SQLite |
| Pembayaran | Duitku (Payment Gateway) |
| Ongkos Kirim & Pelacakan | KlikResi |
| Wilayah (Provinsi/Kota/Kecamatan) | API Wilayah Indonesia |

> **Catatan:** Anda hanya perlu menggunakan aplikasi melalui browser. Seluruh proses teknis (API, pembayaran, ongkir) berjalan otomatis di latar belakang.

---

## 2. Memulai (Registrasi & Login)

### 2.1 Mendaftar Akun Baru

1. Buka halaman utama, klik menu **Daftar / Register**.
2. Isi formulir pendaftaran:
   - **Nama / Username**
   - **Email** (harus valid dan belum terdaftar)
   - **Password** (minimal 8 karakter)
   - **Konfirmasi Password** (harus sama dengan password)
3. Klik tombol **Daftar**.
4. Sistem akan mengirimkan email verifikasi ke alamat email Anda.

### 2.2 Verifikasi Email

- Buka email yang dikirim oleh Medcom, lalu klik tautan verifikasi.
- Beberapa fitur (seperti dashboard) memerlukan email yang sudah terverifikasi.
- Jika belum menerima email, Anda dapat meminta pengiriman ulang dari halaman verifikasi.

### 2.3 Login

1. Klik menu **Masuk / Login**.
2. Masukkan **Email** dan **Password**.
3. (Opsional) Centang **Remember Me** agar tetap login.
4. Klik **Masuk**.

### 2.4 Lupa Password

1. Pada halaman login, klik **Lupa Password?**.
2. Masukkan email akun Anda, lalu klik kirim.
3. Buka email dan klik tautan reset password.
4. Masukkan password baru beserta konfirmasinya.

### 2.5 Logout

Klik nama/menu profil Anda di pojok kanan atas, lalu pilih **Logout**.

---

## 3. Menjelajah & Mencari Produk

Halaman **Beranda (Homepage)** menampilkan seluruh produk yang tersedia, diurutkan dari yang terbaru.

### Fitur Pencarian & Filter

Pada bagian **Search & Filter** Anda dapat menyaring produk berdasarkan:

| Filter | Fungsi |
|--------|--------|
| 🔍 **Cari Produk** | Mengetik nama produk untuk pencarian cepat |
| 🏷️ **Kategori** | Memilih kategori produk (atau "Semua Kategori") |
| 💰 **Harga Minimum** | Menampilkan produk di atas harga tertentu |
| 💰 **Harga Maksimum** | Menampilkan produk di bawah harga tertentu |
| ♻️ **Reset Filter** | Mengembalikan tampilan ke semua produk |

Filter bekerja secara langsung (real-time) tanpa perlu memuat ulang halaman. Anda dapat menggabungkan beberapa filter sekaligus.

---

## 4. Melihat Detail Produk

Klik salah satu kartu produk untuk membuka halaman **Detail Produk**, yang menampilkan:

- Gambar produk
- Nama, harga, dan stok produk
- Deskripsi lengkap
- Informasi toko penjual (nama toko, lokasi, logo)
- Kategori produk
- **Rating rata-rata** dan jumlah ulasan
- Daftar **ulasan** dari pembeli lain

Dari halaman ini Anda dapat **menambahkan produk ke keranjang** (memerlukan login).

---

## 5. Keranjang Belanja

Akses keranjang melalui menu **Keranjang** (ikon keranjang di navbar). Halaman ini menampilkan seluruh produk yang Anda tambahkan.

### Yang Dapat Dilakukan di Keranjang

- **Mengubah jumlah (quantity)** produk — total harga otomatis menyesuaikan.
- **Menghapus item** tertentu dari keranjang.
- **Mengosongkan seluruh keranjang** sekaligus.
- Melihat **subtotal** dan **total harga**.
- Melanjutkan ke **Checkout**.

> ⚠️ Anda harus login terlebih dahulu untuk mengakses keranjang.

---

## 6. Checkout & Pembayaran

Setelah menekan **Checkout** dari keranjang, Anda masuk ke halaman ringkasan pesanan.

### Langkah Checkout

1. **Pilih Alamat Pengiriman**
   - Pilih salah satu alamat yang sudah tersimpan.
   - Jika belum ada, tambahkan alamat baru terlebih dahulu (lihat [Mengelola Alamat](#10-mengelola-alamat)).

2. **Pilih Layanan Pengiriman (Ongkir)**
   - Sistem menghitung ongkos kirim otomatis melalui **KlikResi** berdasarkan:
     - Lokasi toko (asal)
     - Alamat tujuan Anda
     - Total berat produk
   - Pilih kurir dan layanan yang diinginkan dari daftar tarif yang muncul.

3. **Periksa Ringkasan Biaya**
   - **Subtotal** (harga produk)
   - **Ongkos Kirim**
   - **Total** keseluruhan yang harus dibayar.

4. **Lakukan Pembayaran**
   - Klik tombol **Bayar**.
   - Anda akan diarahkan ke halaman pembayaran **Duitku**.
   - Pilih metode pembayaran (Virtual Account, e-wallet, dll.) dan selesaikan pembayaran.
   - Batas waktu pembayaran adalah **24 jam** (1440 menit).

5. **Setelah Pembayaran**
   - Jika berhasil, Anda diarahkan kembali ke halaman **Riwayat Pesanan** dengan notifikasi sukses.
   - Jika gagal/dibatalkan, Anda dapat mencoba lagi dari halaman checkout.

---

## 7. Riwayat Pesanan

Akses melalui menu **Riwayat Pesanan**. Halaman ini menampilkan seluruh pesanan Anda beserta statusnya.

### Yang Dapat Dilakukan

- **Melihat detail pesanan** — produk yang dibeli, alamat, total, dan status.
- **Melacak pengiriman** — bila pesanan sudah dikirim (status `shipped`), nomor resi tersedia dan dapat dilacak melalui KlikResi.
- **Membatalkan pesanan** — selama pesanan belum diproses/dikirim.
- **Menyelesaikan pesanan (Selesai)** — setelah barang diterima, klik **Selesaikan Pesanan** untuk mengubah status menjadi `finished`.
- **Memberi rating** — setelah pesanan selesai, Anda dapat memberi ulasan produk.

---

## 8. Memberi Rating & Ulasan

### 8.1 Membuat Ulasan

1. Dari **Riwayat Pesanan** (pesanan yang sudah selesai) atau halaman **Ulasan**, pilih produk yang ingin diulas.
2. Isi formulir:
   - **Rating** — pilih bintang 1 sampai 5.
   - **Ulasan** — tulis komentar (maksimal 1000 karakter).
3. Klik **Kirim**.

> ⚠️ Setiap produk hanya dapat Anda ulas **satu kali**. Sistem akan menolak ulasan ganda untuk produk yang sama.

### 8.2 Melihat & Menghapus Ulasan

- Buka menu **Ulasan / Ratings** untuk melihat seluruh ulasan yang pernah Anda buat (Riwayat Ulasan).
- Anda dapat **menghapus** ulasan yang sudah Anda buat.

---

## 9. Mengelola Profil Akun

Akses melalui menu **Profil** → **Edit Profil**.

### Yang Dapat Diubah

| Data | Keterangan |
|------|------------|
| **Username / Nama** | Wajib diisi, maksimal 50 karakter |
| **Email** | Harus unik; jika diubah, verifikasi email diperlukan ulang |
| **Nomor Telepon** | Opsional |
| **Tanggal Lahir** | Opsional |
| **Jenis Kelamin** | Pria / Wanita (opsional) |
| **Foto Profil** | Format JPG, JPEG, PNG, atau WebP (otomatis dipotong 300×300) |
| **Password** | Untuk mengganti, masukkan password lama + password baru (min. 8 karakter) + konfirmasi |

### Menghapus Akun

- Pada bagian bawah halaman edit profil, terdapat opsi **Hapus Akun**.
- Anda harus memasukkan **password** untuk konfirmasi.
- ⚠️ Tindakan ini **permanen** dan tidak dapat dibatalkan.

---

## 10. Mengelola Alamat

Alamat digunakan sebagai tujuan pengiriman saat checkout.

### Menambah / Mengubah Alamat

1. Buka menu **Alamat** (atau dari halaman checkout).
2. Klik **Tambah Alamat**.
3. Isi data alamat lengkap, termasuk:
   - **Provinsi** → **Kota/Kabupaten** → **Kecamatan** (dipilih bertingkat)
   - Detail alamat (jalan, nomor rumah, dll.)
4. Simpan.

Anda dapat memiliki beberapa alamat, serta **mengubah** atau **menghapus** alamat yang sudah ada.

> 💡 Data wilayah (provinsi, kota, kecamatan) penting untuk perhitungan ongkos kirim yang akurat.

---

## 11. Membuka & Mengelola Toko (Penjual)

Untuk mulai berjualan, Anda harus membuka toko terlebih dahulu. **Satu akun hanya dapat memiliki satu toko.**

### 11.1 Membuat Toko

1. Buka menu **Toko / Profil Toko**.
2. Jika belum punya toko, Anda otomatis diarahkan ke halaman **Buat Toko**.
3. Isi formulir:
   - **Nama Toko** (wajib, maksimal 255 karakter)
   - **Deskripsi Toko** (wajib)
   - **Lokasi** (wajib)
   - **Provinsi, Kota, Kecamatan** (wajib — untuk perhitungan ongkir asal)
   - **Logo Toko** (wajib — JPG, PNG, JPG, GIF, atau WebP; otomatis dioptimasi ke 500px WebP)
4. Klik **Simpan** untuk membuat toko.

### 11.2 Halaman Profil Toko

Setelah toko dibuat, halaman **Profil Toko** menampilkan:

- Informasi & logo toko
- **Statistik**: jumlah pesanan sukses dan **rating rata-rata** toko
- Daftar **produk** toko
- Daftar **pesanan masuk**

### 11.3 Mengedit Toko

Dari halaman profil toko, Anda dapat memperbarui nama, deskripsi, lokasi, wilayah, dan logo toko.

---

## 12. Mengelola Produk Toko (Penjual)

Pengelolaan produk dilakukan dari halaman toko (CRUD Produk).

### Menambah Produk

1. Klik **Tambah Produk**.
2. Isi data:
   - **Nama Produk** (wajib)
   - **Kategori** (wajib)
   - **Harga** (wajib, angka)
   - **Stok** (wajib, angka)
   - **Deskripsi** (wajib)
   - **Gambar Produk** (wajib — JPG, JPEG, PNG, WebP; otomatis dioptimasi ke 1000px WebP)
   - **Diskon** (opsional)
3. Simpan.

### Mengubah & Menghapus Produk

- **Edit** — perbarui nama, harga, stok, deskripsi, kategori, diskon, atau gambar.
- **Hapus** — menghapus produk dari toko Anda.

> 💡 Gambar produk dan logo toko otomatis dikompresi ke format WebP untuk mempercepat waktu muat halaman.

---

## 13. Mengelola Pesanan Masuk (Penjual)

Pada halaman **Profil Toko**, bagian **Pesanan Masuk** menampilkan order yang berisi produk dari toko Anda. Alur penanganan pesanan:

| Aksi | Kondisi | Hasil |
|------|---------|-------|
| **Terima Pesanan** | Status pesanan `paid` (sudah dibayar) | Status berubah menjadi `processing` |
| **Tolak / Batalkan** | Status `paid` atau `processing` | Status berubah menjadi `cancelled` |
| **Kirim Pesanan** | Status `processing` | Wajib mengisi **Nomor Resi**, status berubah menjadi `shipped` |

Setelah dikirim, pembeli dapat melacak pesanan menggunakan nomor resi tersebut, lalu menyelesaikan pesanan saat barang diterima.

---

## 14. Status Pesanan

Pesanan melewati beberapa tahap status berikut:

| Status | Arti |
|--------|------|
| `pending` | Pesanan dibuat, menunggu pembayaran |
| `paid` | Pembayaran berhasil, menunggu diproses penjual |
| `processing` | Pesanan diterima & sedang disiapkan penjual |
| `shipped` | Pesanan dikirim (nomor resi tersedia) |
| `finished` | Pesanan selesai (dikonfirmasi pembeli) |
| `cancelled` | Pesanan dibatalkan |

### Alur Lengkap (Happy Path)

```
Pembeli                          Penjual
   │                                │
   ├─ Checkout & Bayar ──► pending  │
   │         │                      │
   │    [Duitku] ────────► paid     │
   │                                ├─ Terima ──► processing
   │                                ├─ Input Resi & Kirim ──► shipped
   ├─ Lacak resi                    │
   ├─ Terima barang                 │
   ├─ Selesaikan ──────► finished   │
   └─ Beri Rating ⭐                 │
```

---

## 15. FAQ & Troubleshooting

**T: Saya tidak bisa checkout, muncul pesan harus login.**
J: Pastikan Anda sudah masuk (login). Fitur keranjang, checkout, dan pesanan memerlukan akun yang aktif.

**T: Ongkos kirim tidak muncul saat checkout.**
J: Pastikan alamat pengiriman sudah lengkap dengan **provinsi, kota, dan kecamatan**. Toko penjual juga harus memiliki data wilayah yang valid.

**T: Mengapa saya tidak bisa memberi ulasan dua kali untuk produk yang sama?**
J: Setiap pengguna hanya boleh memberi satu ulasan per produk. Hapus ulasan lama jika ingin menulis ulang.

**T: Saya sudah membayar tetapi status masih `pending`.**
J: Status diperbarui secara otomatis setelah Duitku mengonfirmasi pembayaran (callback). Tunggu beberapa saat, lalu segarkan halaman Riwayat Pesanan.

**T: Saya tidak menemukan menu untuk berjualan.**
J: Buka menu **Toko**. Jika belum punya toko, Anda akan diarahkan ke halaman pembuatan toko. Satu akun = satu toko.

**T: Bagaimana cara membatalkan pesanan?**
J: Buka **Riwayat Pesanan**, pilih pesanan terkait, lalu klik **Batalkan**. Pesanan yang sudah dikirim tidak dapat dibatalkan oleh pembeli.

---

*Dokumen ini merupakan panduan pengguna aplikasi Medcom Marketplace. Untuk pertanyaan lebih lanjut, hubungi pengelola aplikasi.*
