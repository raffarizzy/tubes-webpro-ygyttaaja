# PROMPT: Buatkan Dokumen User Manual Medcom

> **Instruksi untuk Claude:** Buatkan dokumen **User Manual** lengkap untuk aplikasi **Medcom** berdasarkan semua informasi di bawah ini. Dokumen harus rapi, profesional, dan mudah dipahami oleh pengguna awam. Format dokumen menggunakan Microsoft Word style (heading bertingkat, nomor halaman, daftar isi, gambar/screenshot bernomor). Di setiap bagian yang butuh screenshot, saya sudah tandai dengan blok `[SCREENSHOT: ...]` — tolong tetap sertakan placeholder tersebut dalam dokumen dengan format *Figure X.X — Keterangan*, sisanya saya yang isi dengan gambar asli.

---

## Informasi Aplikasi

| Item | Detail |
|------|--------|
| **Nama Aplikasi** | Medcom |
| **Jenis** | Platform e-commerce khusus jual beli sparepart motor |
| **Platform** | Web Browser (Desktop & Mobile), Android, iOS |
| **Pengguna** | Pembeli dan Penjual (satu akun bisa berperan keduanya) |
| **Bahasa** | Indonesia |
| **Warna Utama** | Biru Gelap (#0012AD), Putih (#FFFFFF), Hitam (#000000) |

---

## Struktur Dokumen yang Diinginkan

Buat user manual dengan struktur berikut (gunakan heading bertingkat H1–H3):

```
Halaman Judul
Daftar Isi
Daftar Gambar

BAB 1 — Pendahuluan
  1.1 Tentang Medcom
  1.2 Tujuan Dokumen
  1.3 Sasaran Pembaca
  1.4 Cara Membaca Dokumen Ini

BAB 2 — Memulai Aplikasi
  2.1 Persyaratan Perangkat
  2.2 Mengakses Medcom
  2.3 Mendaftar Akun Baru (Registrasi)
  2.4 Masuk ke Aplikasi (Login)
  2.5 Lupa Password

BAB 3 — Fitur Pembeli
  3.1 Menjelajah & Mencari Produk
  3.2 Melihat Detail Produk
  3.3 Keranjang Belanja
  3.4 Proses Pemesanan & Pembayaran (Checkout)
  3.5 Riwayat Pesanan
  3.6 Melacak Status Pesanan
  3.7 Menyelesaikan Pesanan
  3.8 Memberikan Ulasan & Rating

BAB 4 — Fitur Penjual
  4.1 Membuat Toko
  4.2 Mengelola Profil Toko
  4.3 Menambahkan Produk
  4.4 Mengelola Produk (Edit & Hapus)
  4.5 Memantau Pesanan Masuk
  4.6 Memproses Pesanan (Terima, Kirim, Batalkan)

BAB 5 — Pengaturan Akun
  5.1 Mengedit Profil
  5.2 Mengganti Password
  5.3 Mengelola Alamat Pengiriman
  5.4 Menghapus Akun

BAB 6 — Status Pesanan & Alur Transaksi
  6.1 Penjelasan Setiap Status
  6.2 Alur Transaksi Lengkap (diagram teks/tabel)

BAB 7 — Pertanyaan Umum (FAQ)

Daftar Istilah (Glosarium)
```

---

## Detail Setiap Fitur (Gunakan Sebagai Bahan Penulisan)

### BAB 2 — Memulai Aplikasi

#### 2.1 Persyaratan Perangkat

Tulis tabel persyaratan minimum untuk tiga platform:

| Platform | Spesifikasi |
|----------|-------------|
| **Android** | Prosesor Quad Core+, RAM 3 GB+, Android 5.0+, Storage 2 GB bebas |
| **iOS** | Apple A9+, RAM 2 GB+, iOS 7.0+, Storage 2 GB bebas |
| **Desktop** | Dual Core+, RAM 4 GB+, Windows 10 / macOS High Sierra+, Browser Chrome/Firefox/Safari terbaru |

#### 2.2 Mengakses Medcom

Pengguna membuka browser dan mengetikkan URL Medcom. Halaman beranda akan tampil menampilkan daftar produk, search bar, dan navigasi.

`[SCREENSHOT: Screenshot halaman beranda Medcom — tampilkan navbar, search bar, dan grid produk]`
*Figure 2.1 — Halaman Beranda Medcom*

#### 2.3 Mendaftar Akun Baru

Langkah-langkah:
1. Klik tombol **Daftar / Register** di pojok kanan atas navbar.
2. Isi formulir: **Nama**, **Email**, **Password** (min. 8 karakter), **Konfirmasi Password**.
3. Klik **Daftar**.
4. Sistem mengirim email verifikasi — buka email dan klik tautan verifikasi.
5. Setelah terverifikasi, akun siap digunakan.

**Error yang mungkin muncul:**
- *"Email sudah terdaftar"* → gunakan email lain atau login dengan akun yang ada.
- *"Field tidak boleh kosong"* → pastikan semua kolom terisi.
- *"Password tidak cocok"* → ulangi isi konfirmasi password dengan benar.

`[SCREENSHOT: Screenshot halaman registrasi — form dengan field nama, email, password, konfirmasi]`
*Figure 2.2 — Halaman Registrasi*

#### 2.4 Masuk ke Aplikasi (Login)

1. Klik **Masuk / Login** di navbar.
2. Isi **Email** dan **Password**.
3. (Opsional) Centang **Remember Me**.
4. Klik **Masuk**.

**Error yang mungkin muncul:**
- *"Email atau password salah"* → periksa kembali kredensial.
- *"Field tidak boleh kosong"* → isi semua kolom.

`[SCREENSHOT: Screenshot halaman login — form email dan password]`
*Figure 2.3 — Halaman Login*

#### 2.5 Lupa Password

1. Di halaman login, klik **Lupa Password?**
2. Masukkan email terdaftar, klik **Kirim**.
3. Buka email, klik tautan reset password.
4. Masukkan password baru + konfirmasi, klik **Simpan**.

---

### BAB 3 — Fitur Pembeli

#### 3.1 Menjelajah & Mencari Produk

Halaman beranda menampilkan semua produk secara otomatis. Tersedia fitur filter & pencarian:

| Filter | Fungsi |
|--------|--------|
| **Search Bar** | Ketik nama/kata kunci produk — hasil tampil real-time |
| **Kategori** | Pilih kategori untuk menyaring produk |
| **Harga Minimum** | Tampilkan produk di atas harga tertentu |
| **Harga Maksimum** | Tampilkan produk di bawah harga tertentu |
| **Reset Filter** | Kembalikan tampilan ke semua produk |

Produk ditampilkan dengan sistem **pagination** — pengguna bisa klik tombol halaman berikutnya atau scroll.

`[SCREENSHOT: Screenshot halaman beranda dengan search bar aktif dan produk hasil pencarian]`
*Figure 3.1 — Fitur Pencarian dan Filter Produk*

#### 3.2 Melihat Detail Produk

Klik produk mana saja untuk membuka halaman detail. Informasi yang ditampilkan:
- Gambar produk
- Nama, harga, stok tersedia
- Deskripsi lengkap
- Nama toko, lokasi, dan logo toko
- Kategori produk
- Rating rata-rata bintang dan jumlah ulasan
- Daftar ulasan dari pembeli lain

`[SCREENSHOT: Screenshot halaman detail produk — gambar produk, info toko, rating, dan tombol tambah ke keranjang]`
*Figure 3.2 — Halaman Detail Produk*

#### 3.3 Keranjang Belanja

Klik ikon keranjang di navbar untuk membuka keranjang. Fitur:
- Lihat semua produk yang ditambahkan
- Ubah **jumlah (quantity)** — harga otomatis menyesuaikan
- Hapus item tertentu
- Kosongkan seluruh keranjang
- Lihat **subtotal** dan **total**
- Lanjut ke **Checkout**

> ⚠️ Akses keranjang membutuhkan login.

`[SCREENSHOT: Screenshot halaman keranjang belanja — daftar produk, kolom quantity, subtotal, dan tombol checkout]`
*Figure 3.3 — Halaman Keranjang Belanja*

#### 3.4 Proses Pemesanan & Pembayaran (Checkout)

**Langkah checkout:**
1. Dari keranjang, klik **Checkout**.
2. **Pilih atau tambahkan Alamat Pengiriman** (provinsi → kota → kecamatan → detail alamat).
3. **Pilih Layanan Pengiriman** — ongkos kirim dihitung otomatis berdasarkan berat produk dan wilayah tujuan (via KlikResi). Pilih kurir dan layanan yang diinginkan.
4. Periksa **Ringkasan Biaya**: subtotal produk + ongkos kirim = total bayar.
5. Klik **Bayar**.
6. Anda diarahkan ke halaman pembayaran **Duitku** — pilih metode (Virtual Account, e-wallet, dll.) dan selesaikan pembayaran.
7. Batas waktu pembayaran **24 jam**. Jika lewat, pesanan otomatis dibatalkan.
8. Setelah berhasil bayar → otomatis diarahkan ke **Riwayat Pesanan**.

**Error yang mungkin muncul:**
- Pembayaran gagal → coba ulang dari halaman checkout.
- Alamat belum diisi → tambahkan alamat dulu.

`[SCREENSHOT: Screenshot halaman checkout — pilihan alamat, daftar layanan pengiriman, dan ringkasan biaya]`
*Figure 3.4 — Halaman Checkout*

`[SCREENSHOT: Screenshot halaman pembayaran Duitku — pilihan metode pembayaran]`
*Figure 3.5 — Halaman Pembayaran Duitku*

#### 3.5 Riwayat Pesanan

Buka menu **Riwayat Pesanan** dari navbar. Halaman menampilkan semua pesanan beserta statusnya.

Yang bisa dilakukan:
- Klik pesanan untuk melihat **detail** (produk, alamat, total, status)
- **Batalkan pesanan** (selama belum diproses/dikirim)
- **Selesaikan pesanan** setelah barang diterima
- Akses form **ulasan** setelah pesanan selesai

`[SCREENSHOT: Screenshot halaman riwayat pesanan — daftar pesanan dengan badge status dan tombol aksi]`
*Figure 3.6 — Halaman Riwayat Pesanan*

#### 3.6 Melacak Status Pesanan

Dari riwayat pesanan, klik detail pesanan. Bila status sudah **Dikirim (shipped)**, nomor resi ekspedisi akan ditampilkan. Klik nomor resi untuk melacak paket secara langsung via KlikResi.

`[SCREENSHOT: Screenshot detail pesanan yang sudah dikirim — tampilkan nomor resi dan timeline status]`
*Figure 3.7 — Detail Pesanan dengan Nomor Resi*

#### 3.7 Menyelesaikan Pesanan

Setelah barang diterima:
1. Buka **Riwayat Pesanan**.
2. Pilih pesanan dengan status **Dikirim**.
3. Klik **Selesaikan Pesanan**.
4. Status berubah menjadi **Selesai (finished)**.
5. Tombol **Beri Ulasan** muncul.

#### 3.8 Memberikan Ulasan & Rating

1. Dari riwayat pesanan yang selesai, klik **Beri Ulasan** pada produk yang ingin diulas.
2. Pilih **rating bintang** (1–5).
3. Tulis **komentar/ulasan** (wajib, maks. 1000 karakter).
4. Klik **Kirim**.

> ⚠️ Setiap produk hanya bisa diulas **satu kali** per akun.

Ulasan dapat dilihat dan dihapus di menu **Ulasan Saya (Ratings)**.

`[SCREENSHOT: Screenshot form pemberian ulasan — pilih bintang dan kolom tulis ulasan]`
*Figure 3.8 — Form Ulasan Produk*

`[SCREENSHOT: Screenshot halaman riwayat ulasan (ratings/index) — daftar ulasan yang pernah diberikan]`
*Figure 3.9 — Halaman Riwayat Ulasan*

---

### BAB 4 — Fitur Penjual

> Satu akun hanya bisa memiliki **satu toko**. Akun yang belum punya toko dapat membuka toko kapan saja.

#### 4.1 Membuat Toko

1. Buka menu **Toko** dari navbar.
2. Sistem otomatis mengarahkan ke halaman **Buat Toko** (jika belum punya toko).
3. Isi formulir:
   - **Nama Toko** (wajib, maks. 255 karakter)
   - **Deskripsi Toko** (wajib)
   - **Lokasi / Alamat Toko** (wajib)
   - **Provinsi, Kota/Kabupaten, Kecamatan** (wajib — dropdown bertingkat, untuk perhitungan ongkir asal)
   - **Logo Toko** (wajib — JPG, PNG, GIF, atau WebP; sistem otomatis resize & kompresi)
4. Klik **Simpan**.

**Error yang mungkin muncul:**
- *"Field tidak boleh kosong"* → lengkapi semua kolom wajib.
- *"Anda sudah memiliki toko"* → tidak bisa membuat toko kedua.

`[SCREENSHOT: Screenshot halaman buat toko — form nama toko, deskripsi, lokasi, dropdown wilayah, dan upload logo]`
*Figure 4.1 — Halaman Pembuatan Toko*

#### 4.2 Mengelola Profil Toko

Halaman **Profil Toko** menampilkan:
- Logo, nama, dan deskripsi toko
- Statistik: jumlah pesanan sukses & rating rata-rata
- Daftar produk toko
- Daftar pesanan masuk

Untuk mengedit, klik **Edit Toko** dan perbarui informasi yang diinginkan.

`[SCREENSHOT: Screenshot halaman profil toko — logo toko, statistik, dan tab produk/pesanan]`
*Figure 4.2 — Halaman Profil Toko*

#### 4.3 Menambahkan Produk

1. Dari halaman profil toko, klik **Tambah Produk**.
2. Isi formulir:
   - **Nama Produk** (wajib)
   - **Kategori** (wajib — pilih dari dropdown)
   - **Harga** (wajib, angka positif)
   - **Stok** (wajib, angka bulat)
   - **Berat** (untuk kalkulasi ongkir)
   - **Deskripsi** (wajib)
   - **Gambar Produk** (wajib — JPG, JPEG, PNG, atau WebP; otomatis dioptimasi ke 1000px WebP)
   - **Diskon** (opsional, dalam persen)
3. Klik **Unggah / Simpan**.

**Error yang mungkin muncul:**
- *"Harga produk tidak valid"* → isi harga dengan angka positif.
- *"Data produk tidak lengkap"* → lengkapi semua kolom wajib.

`[SCREENSHOT: Screenshot form tambah produk — semua kolom terisi, termasuk preview gambar]`
*Figure 4.3 — Form Tambah Produk*

#### 4.4 Mengelola Produk (Edit & Hapus)

Dari daftar produk di profil toko:
- **Edit** → klik ikon edit pada produk, ubah data, klik **Simpan**.
- **Hapus** → klik ikon hapus, konfirmasi penghapusan.

`[SCREENSHOT: Screenshot daftar produk di profil toko — kartu produk dengan tombol edit dan hapus]`
*Figure 4.4 — Daftar Produk dengan Opsi Kelola*

#### 4.5 Memantau Pesanan Masuk

Bagian **Pesanan Masuk** di profil toko menampilkan semua order yang berisi produk dari toko Anda, diurutkan dari yang terbaru.

Informasi yang ditampilkan per pesanan: nama pembeli, produk yang dipesan, jumlah, total harga, dan status saat ini.

`[SCREENSHOT: Screenshot bagian pesanan masuk di profil toko — daftar pesanan dengan status dan tombol aksi]`
*Figure 4.5 — Pesanan Masuk di Profil Toko*

#### 4.6 Memproses Pesanan (Terima, Kirim, Batalkan)

| Aksi | Kondisi | Cara |
|------|---------|------|
| **Terima** | Status `paid` (sudah dibayar pembeli) | Klik **Terima Pesanan** → status jadi `processing` |
| **Kirim** | Status `processing` | Klik **Kirim**, isi **Nomor Resi** ekspedisi → status jadi `shipped` |
| **Batalkan** | Status `paid` atau `processing` | Klik **Batalkan** → status jadi `cancelled` |

> ⚠️ Pesanan yang sudah dikirim tidak dapat dibatalkan.

`[SCREENSHOT: Screenshot detail pesanan masuk — tombol Terima, Kirim (dengan kolom nomor resi), dan Batalkan]`
*Figure 4.6 — Aksi Pemrosesan Pesanan oleh Penjual*

---

### BAB 5 — Pengaturan Akun

#### 5.1 Mengedit Profil

Buka **Profil → Edit Profil** dari navbar.

| Data | Keterangan |
|------|------------|
| **Nama / Username** | Wajib, maks. 50 karakter |
| **Email** | Harus unik; jika diubah, verifikasi ulang diperlukan |
| **Nomor Telepon** | Opsional |
| **Tanggal Lahir** | Opsional |
| **Jenis Kelamin** | Opsional (Pria/Wanita) |

Klik **Simpan** setelah selesai mengubah data.

`[SCREENSHOT: Screenshot halaman edit profil — form dengan semua field profil]`
*Figure 5.1 — Halaman Edit Profil*

#### 5.2 Mengganti Foto Profil

Masih di halaman edit profil, terdapat bagian **Foto Profil**:
1. Klik area foto atau tombol **Ganti Foto**.
2. Pilih file gambar (JPG, JPEG, PNG, atau WebP).
3. Sistem otomatis memotong gambar menjadi 300×300 piksel.
4. Simpan.

`[SCREENSHOT: Screenshot bagian upload foto profil di halaman edit profil]`
*Figure 5.2 — Ganti Foto Profil*

#### 5.3 Mengganti Password

Di halaman edit profil, scroll ke bagian **Ganti Password**:
1. Isi **Password Saat Ini**.
2. Isi **Password Baru** (min. 8 karakter).
3. Isi **Konfirmasi Password Baru**.
4. Klik **Simpan**.

**Error yang mungkin muncul:**
- *"Password saat ini salah"* → cek dan isi ulang password lama.
- *"Konfirmasi password tidak cocok"* → isi ulang konfirmasi.

#### 5.4 Mengelola Alamat Pengiriman

Buka menu **Alamat** (dapat diakses dari halaman checkout atau menu akun).
- **Tambah Alamat** → isi provinsi, kota, kecamatan (dropdown bertingkat), dan detail alamat.
- **Edit Alamat** → perbarui data alamat yang ada.
- **Hapus Alamat** → hapus alamat yang tidak digunakan.

> 💡 Data wilayah lengkap (provinsi/kota/kecamatan) diperlukan agar ongkir dapat dihitung dengan akurat.

`[SCREENSHOT: Screenshot halaman daftar alamat — kartu alamat tersimpan dengan tombol edit dan hapus, serta tombol tambah alamat]`
*Figure 5.3 — Halaman Kelola Alamat*

`[SCREENSHOT: Screenshot form tambah/edit alamat — dropdown provinsi, kota, kecamatan, dan kolom detail alamat]`
*Figure 5.4 — Form Tambah Alamat*

#### 5.5 Menghapus Akun

> ⚠️ Tindakan ini **permanen** dan tidak dapat dibatalkan.

1. Buka **Edit Profil**.
2. Scroll ke bawah, klik **Hapus Akun**.
3. Masukkan **password** untuk konfirmasi.
4. Konfirmasi penghapusan.

---

### BAB 6 — Status Pesanan & Alur Transaksi

#### 6.1 Penjelasan Setiap Status

| Status | Ikon/Badge | Artinya |
|--------|------------|---------|
| `pending` | 🕐 Menunggu | Pesanan dibuat, menunggu pembayaran dari pembeli |
| `paid` | ✅ Dibayar | Pembayaran berhasil, menunggu konfirmasi penjual |
| `processing` | 🔧 Diproses | Penjual menerima & sedang menyiapkan pesanan |
| `shipped` | 🚚 Dikirim | Pesanan dalam perjalanan (nomor resi tersedia) |
| `finished` | 🎉 Selesai | Pembeli mengkonfirmasi penerimaan barang |
| `cancelled` | ❌ Dibatalkan | Pesanan dibatalkan oleh pembeli atau penjual |

#### 6.2 Alur Transaksi Lengkap

```
[Pembeli]                            [Sistem]                      [Penjual]
    │                                    │                              │
    ├── Checkout & Klik Bayar ──────► pending                          │
    │                                    │                              │
    ├── Bayar via Duitku ───────────► paid ────────────────────────────┤
    │                                    │                              ├── Terima Pesanan
    │                                    │                         processing
    │                                    │                              ├── Input Resi & Kirim
    │                                    │                          shipped
    ├── Lacak resi                        │                              │
    ├── Terima Barang                     │                              │
    ├── Klik Selesaikan ────────────► finished                          │
    └── Beri Rating ⭐                    │                              │
```

---

### BAB 7 — Pertanyaan Umum (FAQ)

Sertakan minimal 8 FAQ berikut (kembangkan jawabannya dengan bahasa yang ramah dan jelas):

1. **Apakah saya harus login untuk berbelanja?**
   Ya, keranjang belanja, checkout, dan riwayat pesanan hanya bisa diakses setelah login.

2. **Bagaimana jika ongkir tidak muncul saat checkout?**
   Pastikan alamat pengiriman sudah diisi lengkap sampai tingkat kecamatan. Toko penjual juga harus memiliki data wilayah yang valid.

3. **Mengapa saya tidak bisa memberi ulasan dua kali untuk produk yang sama?**
   Sistem membatasi satu ulasan per produk per akun untuk menjaga kejujuran ulasan. Hapus ulasan lama jika ingin menulis ulang.

4. **Status pesanan saya masih `pending` padahal sudah bayar. Kenapa?**
   Status diperbarui otomatis setelah konfirmasi dari payment gateway. Tunggu beberapa saat (maks. beberapa menit), lalu segarkan halaman.

5. **Bisakah saya berjualan dan berbelanja dengan akun yang sama?**
   Ya. Satu akun dapat membuka toko sekaligus berbelanja sebagai pembeli.

6. **Berapa lama batas waktu pembayaran?**
   24 jam sejak pesanan dibuat. Lewat dari itu, pesanan otomatis dibatalkan.

7. **Bisakah saya membatalkan pesanan yang sudah dikirim?**
   Tidak. Pesanan yang sudah berstatus `shipped` tidak dapat dibatalkan.

8. **Format gambar apa yang didukung untuk produk dan logo toko?**
   JPG, JPEG, PNG, dan WebP. Gambar otomatis dikompresi dan dioptimasi oleh sistem.

---

### Daftar Istilah (Glosarium)

Sertakan glosarium berisi minimal istilah-istilah berikut:

| Istilah | Definisi |
|---------|----------|
| Sparepart | Komponen pengganti untuk perbaikan kendaraan bermotor |
| Checkout | Proses konfirmasi dan pembayaran pesanan |
| Keranjang | Tempat penyimpanan sementara produk yang akan dibeli |
| Resi / Nomor Resi | Kode unik pengiriman dari ekspedisi untuk melacak paket |
| Rating | Penilaian bintang (1–5) terhadap produk |
| Ulasan | Komentar/teks dari pembeli tentang produk yang sudah dibeli |
| Ongkir | Ongkos Kirim — biaya pengiriman dari toko ke alamat pembeli |
| Payment Gateway | Layanan pembayaran online (Medcom menggunakan Duitku) |
| Pagination | Sistem tampilan produk yang dibagi menjadi beberapa halaman |
| Dashboard Toko | Halaman kelola toko penjual |

---

## Catatan Gaya Penulisan

- Gunakan **kalimat aktif** dan **bahasa Indonesia baku** namun tetap mudah dipahami
- Setiap langkah prosedur harus **bernomor** dan dimulai dengan kata kerja
- Pesan error ditulis dalam format *miring*
- Nama tombol ditulis **tebal**
- Nama halaman/menu ditulis **Kapital**
- Tambahkan **kotak peringatan** (⚠️) untuk hal-hal yang bersifat permanen atau penting
- Tambahkan **tips** (💡) untuk saran penggunaan
- Sertakan **Daftar Isi** dengan nomor halaman di awal dokumen
- Sertakan **Daftar Gambar** (Figure) di bawah Daftar Isi
- Ukuran dokumen: A4, margin 3-3-3-3 cm, spasi 1.5, Times New Roman 12pt (atau sesuaikan dengan standar laporan kampus)

---

## Daftar Screenshot yang Dibutuhkan (Ringkasan)

Berikut daftar semua screenshot yang perlu diambil dari aplikasi Medcom dan dimasukkan ke dalam dokumen:

| No | Figure | Halaman yang Di-screenshot |
|----|--------|---------------------------|
| 1 | Figure 2.1 | Halaman beranda (homepage) — tampilkan navbar + grid produk |
| 2 | Figure 2.2 | Halaman registrasi (daftar akun) |
| 3 | Figure 2.3 | Halaman login |
| 4 | Figure 3.1 | Halaman beranda dengan filter aktif / hasil pencarian |
| 5 | Figure 3.2 | Halaman detail produk |
| 6 | Figure 3.3 | Halaman keranjang belanja (ada produk di dalamnya) |
| 7 | Figure 3.4 | Halaman checkout (pilihan alamat + ongkir + ringkasan) |
| 8 | Figure 3.5 | Halaman pembayaran Duitku |
| 9 | Figure 3.6 | Halaman riwayat pesanan (daftar pesanan dengan berbagai status) |
| 10 | Figure 3.7 | Detail pesanan status `shipped` (ada nomor resi) |
| 11 | Figure 3.8 | Form pemberian ulasan/rating produk |
| 12 | Figure 3.9 | Halaman riwayat ulasan (ratings/index) |
| 13 | Figure 4.1 | Halaman buat toko (form) |
| 14 | Figure 4.2 | Halaman profil toko (setelah toko dibuat) |
| 15 | Figure 4.3 | Form tambah produk |
| 16 | Figure 4.4 | Daftar produk di profil toko (dengan tombol edit/hapus) |
| 17 | Figure 4.5 | Bagian pesanan masuk di profil toko |
| 18 | Figure 4.6 | Detail pesanan masuk (tombol terima/kirim/batalkan + kolom resi) |
| 19 | Figure 5.1 | Halaman edit profil |
| 20 | Figure 5.2 | Bagian upload foto profil |
| 21 | Figure 5.3 | Halaman daftar alamat |
| 22 | Figure 5.4 | Form tambah/edit alamat (dropdown wilayah) |
