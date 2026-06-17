# Prompt — Dokumen Cuplikan Source Code (untuk Pengajuan HKI)

> File ini **bukan** dokumen HKI-nya, melainkan **prompt** yang dipakai untuk
> meng-generate dokumen "Cuplikan Source Code" (kurang lebih 2–3 halaman) yang
> akan dilampirkan pada pengajuan **Hak Cipta / HKI Program Komputer** ke DJKI.
> Salin seluruh isi blok prompt di bawah ke AI assistant (Claude/ChatGPT) yang
> sudah diberi akses ke repository ini.

---

## Cara Pakai

1. Pastikan AI assistant punya akses ke source code repository ini.
2. Salin **seluruh isi** bagian "PROMPT" di bawah ini.
3. Tempel ke AI assistant, jalankan.
4. Output diharapkan berupa dokumen siap-cetak (Markdown/PDF) sepanjang ±2–3
   halaman berisi cuplikan kode dari modul-modul inti.

---

## PROMPT

````
Kamu adalah technical writer yang membantu menyusun dokumen lampiran untuk
pengajuan Hak Cipta Program Komputer (HKI) ke Direktorat Jenderal Kekayaan
Intelektual (DJKI) Indonesia.

Buatkan dokumen berjudul "CUPLIKAN SOURCE CODE" sepanjang kurang lebih
2–3 halaman. Dokumen ini memuat potongan-potongan kode (excerpt) dari bagian
program yang paling inti dan orisinal, BUKAN seluruh source code, dan BUKAN
kode boilerplate framework.

=== IDENTITAS CIPTAAN ===
- Nama Aplikasi   : Medcom Indonesia — Platform Marketplace Komponen Elektronik
- Jenis Ciptaan   : Program Komputer (Aplikasi Web)
- Deskripsi Singkat:
  Medcom Indonesia adalah platform transaksi (marketplace) untuk distribusi
  komponen elektronik, peralatan, tools, repair, dan multimedia. Aplikasi
  menghubungkan supplier/manufacturer dengan pembeli melalui katalog produk,
  keranjang belanja, perhitungan ongkos kirim berbasis wilayah, pembayaran
  online (payment gateway), pelacakan pesanan (nomor resi), serta sistem
  rating/ulasan toko dan produk.
- Pemilik/Pencipta : Tim Pengembang Medcom Indonesia
- Tahun            : 2026

PENTING — Branding:
- Gunakan nama "Medcom" / "Medcom Indonesia" di seluruh dokumen.
- JANGAN gunakan nama lama "Sparehub" di mana pun. Jika menemukan string
  "Sparehub" di kode, tetap tampilkan apa adanya di cuplikan kode (jangan ubah
  kode), tetapi pada narasi/penjelasan selalu sebut produk sebagai "Medcom".

=== STACK TEKNOLOGI (sebutkan ringkas di bagian pembuka dokumen) ===
- Backend Utama : PHP 8.2, Laravel 12 (MVC, Eloquent ORM, Blade)
- API Service   : Node.js + Express (layer service terpisah di folder node-api/)
- Frontend      : Blade Template, Tailwind CSS, Alpine.js, JavaScript vanilla
- Database      : MySQL (migration & Eloquent models)
- Integrasi     : Payment Gateway (Duitku / Xendit), perhitungan ongkir wilayah
- Pengujian     : Cypress (End-to-End Testing)

=== MODUL INTI YANG WAJIB DICUPLIK (pilih bagian fungsi paling representatif,
bukan seluruh file; cukup 15–40 baris per cuplikan yang benar-benar menunjukkan
logika bisnis orisinal) ===

1. Autentikasi & Registrasi Pengguna
   - app/Http/Controllers/Auth/RegisteredUserController.php
   - app/Http/Controllers/Auth/AuthenticatedSessionController.php

2. Manajemen Produk (CRUD katalog komponen elektronik)
   - app/Http/Controllers/ProductController.php
   - app/Models/Product.php

3. Manajemen Toko / Seller (termasuk verifikasi seller & wilayah)
   - app/Http/Controllers/TokoController.php
   - app/Models/Toko.php

4. Keranjang Belanja
   - app/Http/Controllers/KeranjangController.php
   - app/Http/Controllers/BarangKeranjangController.php
   - app/Models/Keranjang.php

5. Checkout, Ongkos Kirim & Pesanan
   - app/Http/Controllers/CheckoutController.php
   - app/Http/Controllers/ShippingController.php
   - app/Http/Controllers/OrderController.php
   - app/Models/Order.php

6. Integrasi Pembayaran (Payment Gateway / Callback)
   - app/Http/Controllers/DuitkuCallbackController.php

7. Sistem Rating & Ulasan
   - app/Http/Controllers/RatingController.php
   - app/Models/Rating.php

8. Layer API Node.js (logika service terpisah — tunjukkan keaslian arsitektur)
   - node-api/src/services/checkout.service.js
   - node-api/src/services/product.service.js
   - node-api/src/controllers/cart.controller.js

9. Routing Aplikasi (struktur endpoint)
   - routes/web.php (cuplik sebagian definisi route inti)

=== FORMAT & GAYA DOKUMEN ===
- Bahasa: Indonesia, formal-teknis.
- Struktur dokumen:
  1. Judul + tabel Identitas Ciptaan singkat.
  2. Paragraf "Gambaran Umum" (3–5 kalimat) menjelaskan fungsi aplikasi Medcom.
  3. Ringkasan Arsitektur & Stack Teknologi (bullet).
  4. Bagian "Cuplikan Source Code", dikelompokkan per modul (1–9 di atas).
     Untuk setiap cuplikan:
       a. Sub-judul modul + path file (sebagai keterangan, mis. "Berkas:
          app/Http/Controllers/ProductController.php").
       b. 1–2 kalimat penjelasan fungsi/peran kode tersebut.
       c. Blok kode dengan syntax highlighting yang sesuai (php / js / blade).
  5. Penutup: 1 paragraf pernyataan bahwa cuplikan di atas merupakan bagian
     inti dan orisinal dari program, dan keseluruhan source code tersimpan pada
     pemegang hak cipta.
- Panjang total: jaga di kisaran 2–3 halaman A4 (ringkas; potong kode panjang,
  gunakan komentar "// ... (kode dipersingkat) ..." bila perlu).
- JANGAN sertakan kredensial, API key, password, atau isi file .env.
- Pilih cuplikan yang menonjolkan LOGIKA BISNIS (perhitungan ongkir, alur
  checkout, validasi pesanan, callback pembayaran, agregasi rating), hindari
  getter/setter dan kode hasil generate framework yang generik.

Sekarang baca file-file di atas dari repository, lalu hasilkan dokumen
"CUPLIKAN SOURCE CODE" sesuai instruksi ini.
````

---

## Catatan untuk Tim

- Daftar file di atas sudah dipilih berdasarkan modul **inti & orisinal** dari
  aplikasi (autentikasi, produk, toko, keranjang, checkout, ongkir, pembayaran,
  rating, layer API Node.js). Kode boilerplate Laravel/Breeze sengaja
  tidak dimasukkan karena bukan karya orisinal yang relevan untuk klaim HKI.
- Bila dokumen masih terlalu panjang dari 3 halaman, kurangi jumlah cuplikan
  (prioritaskan modul: Produk, Checkout/Ongkir, Pembayaran, Rating).
- Bila terlalu pendek, tambahkan cuplikan dari model & migration terkait.
- Sebelum mengirim, pastikan tidak ada string "Sparehub" yang tersisa pada
  bagian narasi dokumen — branding final adalah **Medcom Indonesia**.
