# PROMPT: Buatkan Dokumen Cuplikan Source Code — Medcom

> **Instruksi untuk Claude:** Buatkan dokumen **Cuplikan Source Code** untuk aplikasi **Medcom** berdasarkan semua snippet kode di bawah ini. Dokumen harus berformat laporan akademis (seperti laporan tugas akhir/proyek), kurang lebih **2–3 halaman** isi (tidak termasuk halaman judul dan daftar isi). Sajikan setiap potongan kode dalam blok kode berformat rapi dengan keterangan penjelasan singkat di atas dan/atau di bawahnya. Gunakan bahasa Indonesia.

---

## Informasi Aplikasi

| Item | Detail |
|------|--------|
| **Nama Aplikasi** | Medcom |
| **Jenis** | Platform e-commerce jual beli sparepart motor |
| **Stack Teknologi** | Laravel (PHP) + Node.js (Express) + MySQL |
| **Arsitektur** | Laravel sebagai web layer, Node.js sebagai REST API backend |

---

## Struktur Dokumen yang Diinginkan

```
Halaman Judul
Daftar Isi

BAB X — Cuplikan Source Code
  X.1  Konfigurasi Database (Node.js)
  X.2  Entry Point Aplikasi Node.js
  X.3  Utility Response API
  X.4  Routes Produk (Node.js)
  X.5  Controller Produk (Node.js)
  X.6  Service Produk — Query Database
  X.7  Service Checkout — Buat Pesanan & Transaksi DB
  X.8  Controller Checkout (Node.js)
  X.9  Controller Registrasi (Laravel)
  X.10 Controller Checkout — Integrasi Duitku (Laravel)
```

> Nomor bab menyesuaikan urutan dokumen keseluruhan (bisa X = 4, 5, dst. — sesuaikan sendiri). Tiap subbab: tampilkan judul subbab, tuliskan 1–2 kalimat konteks/penjelasan, lalu blok kode, lalu 1–2 kalimat penjelasan singkat setelah kode jika diperlukan.

---

## Snippet Kode yang Harus Dimasukkan

### X.1 — Konfigurasi Koneksi Database (Node.js)

**File:** `node-api/src/config/db.js`
**Konteks:** Konfigurasi koneksi database MySQL menggunakan `mysql2/promise` dengan connection pool. Timezone diset ke WIB (+07:00).

```javascript
const mysql = require("mysql2/promise");

const db = mysql.createPool({
    host: process.env.DB_HOST,
    user: process.env.DB_USER,
    password: process.env.DB_PASS,
    database: process.env.DB_NAME,
    timezone: "+07:00",
});

db.on("connection", (connection) => {
    connection.query("SET time_zone = '+07:00'");
});

console.log("DB CONNECTED TO:", process.env.DB_NAME);

module.exports = db;
```

---

### X.2 — Entry Point Aplikasi Node.js

**File:** `node-api/src/app.js`
**Konteks:** File utama Express.js yang mendaftarkan seluruh middleware, routes, dan handler 404. Juga menangani prefix URL untuk deployment di cPanel.

```javascript
const express = require("express");
const cors = require("cors");
const db = require("./config/db");

const profileRoutes  = require("./routes/profile.routes");
const productRoutes  = require("./routes/product.routes");
const cartRoutes     = require("./routes/cart.routes");
const tokoRoutes     = require("./routes/toko.routes");
const checkoutRoutes = require("./routes/checkout.routes");
const historyRoutes  = require("./routes/history.routes");
const ratingRoutes   = require("./routes/rating.routes");
const alamatRoutes   = require("./routes/alamat.routes");

const app = express();

app.use(cors());
app.use(express.json());
app.use(express.urlencoded({ extended: true }));

// Fix prefix URL cPanel
app.use((req, res, next) => {
    const prefix = '/nodeApi';
    if (req.url.startsWith(prefix)) {
        req.url = req.url.replace(prefix, '');
        if (req.url === '') req.url = '/';
    }
    next();
});

// Mount Routes
app.use("/api/profile",  profileRoutes);
app.use("/api/products", productRoutes);
app.use("/api/cart",     cartRoutes);
app.use("/api/toko",     tokoRoutes);
app.use("/api/orders",   checkoutRoutes);
app.use("/api/history",  historyRoutes);
app.use("/api/ratings",  ratingRoutes);
app.use("/api/alamat",   alamatRoutes);

// 404 Handler
app.use((req, res) => {
    res.status(404).json({
        success: false,
        message: "Endpoint not found",
        requested_url: req.originalUrl,
    });
});

module.exports = app;
```

---

### X.3 — Utility Format Response API

**File:** `node-api/src/utils/response.js`
**Konteks:** Helper terpusat untuk memastikan seluruh response API Node.js memiliki format JSON yang konsisten.

```javascript
exports.success = (res, data, message = 'Success', code = 200) => {
    return res.status(code).json({ success: true, message, data });
};

exports.error = (res, message = 'Internal Server Error', code = 500, error = null) => {
    const response = { success: false, message };
    if (error) response.error = error;
    return res.status(code).json(response);
};

exports.notFound = (res, message = 'Resource not found') => {
    return res.status(404).json({ success: false, message });
};

exports.validationError = (res, message = 'Validation Error', errors = null) => {
    const response = { success: false, message };
    if (errors) response.errors = errors;
    return res.status(400).json(response);
};
```

---

### X.4 — Routes Produk (Node.js)

**File:** `node-api/src/routes/product.routes.js`
**Konteks:** Definisi endpoint RESTful untuk modul produk. Mendukung operasi CRUD penuh, termasuk filter berdasarkan toko dan kategori.

```javascript
const express    = require('express');
const router     = express.Router();
const controller = require('../controllers/product.controller');

router.get('/toko/:tokoId',         controller.getByToko);
router.get('/category/:categoryId', controller.getByCategory);
router.get('/',                     controller.index);
router.get('/:id',                  controller.show);
router.post('/',                    controller.create);
router.patch('/:id',                controller.update);
router.delete('/:id',               controller.delete);

module.exports = router;
```

---

### X.5 — Controller Produk (Node.js)

**File:** `node-api/src/controllers/product.controller.js`  
**Konteks:** Controller yang menangani request HTTP untuk produk. Memvalidasi input, memanggil service layer, dan mengembalikan response terstandar. Ditampilkan fungsi `create` dan `update` sebagai contoh representatif.

```javascript
const service  = require('../services/product.service');
const response = require('../utils/response');

// POST /api/products — Tambah produk baru
exports.create = async (req, res) => {
  try {
    const requiredFields = ['toko_id', 'category_id', 'nama',
                            'deskripsi', 'harga', 'stok', 'imagePath'];
    const missingFields  = requiredFields.filter(f => !req.body[f]);

    if (missingFields.length > 0) {
      return response.validationError(
        res, `Field berikut wajib diisi: ${missingFields.join(', ')}`
      );
    }

    const result = await service.create(req.body);
    return response.success(res, result, 'Produk berhasil dibuat', 201);
  } catch (error) {
    return response.error(res, 'Gagal membuat produk', 500, error.message);
  }
};

// PATCH /api/products/:id — Perbarui produk
exports.update = async (req, res) => {
  try {
    const result = await service.update(req.params.id, req.body);
    return response.success(res, result, 'Produk berhasil diperbarui');
  } catch (error) {
    if (error.message === 'Produk tidak ditemukan')
      return response.notFound(res, error.message);
    return response.error(res, 'Gagal memperbarui produk', 500, error.message);
  }
};
```

---

### X.6 — Service Produk — Query Database

**File:** `node-api/src/services/product.service.js`  
**Konteks:** Service layer yang berinteraksi langsung dengan database MySQL. Menggunakan raw SQL dengan JOIN untuk mengambil data produk beserta informasi toko dan kategorinya. Ditampilkan fungsi `getById`, `getAll`, dan `create`.

```javascript
const db = require('../config/db');

// Ambil detail produk by ID (JOIN toko & kategori)
exports.getById = async (id) => {
  const [rows] = await db.query(
    `SELECT p.id, p.nama, p.deskripsi, p.harga, p.diskon, p.stok, p.imagePath,
            p.toko_id, t.nama_toko, t.lokasi AS toko_lokasi, t.logo_path AS toko_logo,
            p.category_id, c.judulKategori AS category_nama, p.created_at
     FROM products p
     LEFT JOIN tokos      t ON p.toko_id      = t.id
     LEFT JOIN categories c ON p.category_id  = c.id
     WHERE p.id = ?`,
    [id]
  );
  return rows[0];
};

// Ambil semua produk dengan pagination
exports.getAll = async (limit = 20, offset = 0) => {
  const [rows] = await db.query(
    `SELECT p.id, p.nama, p.harga, p.diskon, p.stok, p.imagePath,
            p.toko_id, t.nama_toko, p.category_id, c.judulKategori AS category_nama
     FROM products p
     LEFT JOIN tokos      t ON p.toko_id     = t.id
     LEFT JOIN categories c ON p.category_id = c.id
     ORDER BY p.created_at DESC
     LIMIT ? OFFSET ?`,
    [parseInt(limit), parseInt(offset)]
  );
  return rows;
};

// Buat produk baru
exports.create = async (data) => {
  const [result] = await db.query(
    `INSERT INTO products
       (toko_id, category_id, nama, deskripsi, harga, diskon, stok, imagePath, created_at, updated_at)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())`,
    [
      parseInt(data.toko_id),
      parseInt(data.category_id),
      data.nama,
      data.deskripsi || '',
      parseInt(data.harga),
      data.diskon ? parseInt(data.diskon) : 0,
      parseInt(data.stok),
      data.imagePath,
    ]
  );
  return { id: result.insertId, message: 'Produk berhasil dibuat' };
};
```

---

### X.7 — Service Checkout — Membuat Pesanan dengan Database Transaction

**File:** `node-api/src/services/checkout.service.js`  
**Konteks:** Fungsi `createOrder` menggunakan database transaction untuk memastikan konsistensi data: pembuatan order, insert order items, pengurangan stok produk, dan pengosongan keranjang belanja dilakukan secara atomik. Jika salah satu langkah gagal, seluruh proses di-rollback.

```javascript
const db = require("../config/db");
const { getWIBTimestamp } = require("../utils/dateHelper");

exports.createOrder = async (
  userId, alamatId, items,
  courierCode = null, courierName = null,
  serviceName = null, shippingCost = 0
) => {
  const connection = await db.getConnection();
  try {
    await connection.query("SET time_zone = '+07:00'");
    await connection.beginTransaction();

    let totalHarga = 0;
    const orderItemsData = [];

    // Validasi stok & hitung total
    for (const item of items) {
      const [productRows] = await connection.query(
        "SELECT id, nama, harga, stok FROM products WHERE id = ?",
        [item.product_id]
      );
      if (productRows.length === 0)
        throw new Error(`Produk dengan ID ${item.product_id} tidak ditemukan`);

      const product = productRows[0];
      if (product.stok < item.jumlah)
        throw new Error(`Stok tidak mencukupi untuk produk ${product.nama}`);

      const subtotal = product.harga * item.jumlah;
      totalHarga += subtotal;
      orderItemsData.push({
        product_id: product.id, nama_produk: product.nama,
        harga: product.harga, qty: item.jumlah, subtotal,
      });
    }

    const grandTotal = totalHarga + shippingCost;
    const now = getWIBTimestamp();

    // Insert order
    const [orderResult] = await connection.query(
      `INSERT INTO orders
         (user_id, alamat_id, total_harga, courier_code, courier_name,
          service_name, shipping_cost, status, created_at, updated_at)
       VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', ?, ?)`,
      [userId, alamatId, grandTotal, courierCode,
       courierName, serviceName, shippingCost, now, now]
    );
    const orderId = orderResult.insertId;

    // Insert order items & kurangi stok
    for (const item of orderItemsData) {
      await connection.query(
        `INSERT INTO order_items
           (order_id, product_id, nama_produk, harga, qty, subtotal)
         VALUES (?, ?, ?, ?, ?, ?)`,
        [orderId, item.product_id, item.nama_produk,
         item.harga, item.qty, item.subtotal]
      );
      await connection.query(
        "UPDATE products SET stok = stok - ? WHERE id = ?",
        [item.qty, item.product_id]
      );
    }

    // Kosongkan keranjang user
    const [cartRows] = await connection.query(
      "SELECT id FROM keranjangs WHERE user_id = ?", [userId]
    );
    if (cartRows.length > 0) {
      await connection.query(
        "DELETE FROM barang_keranjangs WHERE keranjang_id = ?",
        [cartRows[0].id]
      );
    }

    await connection.commit();
    return await this.getOrderById(orderId);
  } catch (error) {
    await connection.rollback();
    throw error;
  } finally {
    connection.release();
  }
};
```

---

### X.8 — Controller Checkout (Node.js)

**File:** `node-api/src/controllers/checkout.controller.js`  
**Konteks:** Menangani endpoint pembuatan order dan pembaruan status. Validasi dilakukan di layer controller sebelum meneruskan ke service.

```javascript
const service  = require("../services/checkout.service");
const response = require("../utils/response");

// POST /api/orders — Buat order baru
exports.createOrder = async (req, res) => {
  try {
    const { user_id, alamat_id, items,
            courier_code, courier_name, service_name, shipping_cost } = req.body;

    if (!user_id)   return response.validationError(res, "Field user_id wajib diisi");
    if (!alamat_id) return response.validationError(res, "Field alamat_id wajib diisi");
    if (!items || !Array.isArray(items) || items.length === 0)
      return response.validationError(res, "Field items wajib diisi (min. 1 item)");

    const order = await service.createOrder(
      user_id, alamat_id, items,
      courier_code, courier_name, service_name, shipping_cost
    );
    return response.success(res, { order }, "Order berhasil dibuat", 201);
  } catch (error) {
    if (error.message.includes("tidak ditemukan"))
      return response.notFound(res, error.message);
    if (error.message.includes("Stok"))
      return response.error(res, error.message, 400);
    return response.error(res, "Gagal membuat order", 500, error.message);
  }
};

// PUT /api/orders/:orderId/status — Perbarui status order
exports.updateOrderStatus = async (req, res) => {
  try {
    const { orderId } = req.params;
    const { status, payment_url } = req.body;

    if (!status) return response.validationError(res, "Field status wajib diisi");

    const result = await service.updateOrderStatus(orderId, status, payment_url);
    return response.success(res, result, "Status order berhasil diperbarui");
  } catch (error) {
    if (error.message.includes("tidak ditemukan"))
      return response.notFound(res, error.message);
    if (error.message.includes("tidak valid"))
      return response.validationError(res, error.message);
    return response.error(res, "Gagal memperbarui status order", 500, error.message);
  }
};
```

---

### X.9 — Controller Registrasi Akun (Laravel)

**File:** `app/Http/Controllers/Auth/RegisteredUserController.php`  
**Konteks:** Menangani proses pendaftaran akun pengguna baru di sisi Laravel. Password di-hash menggunakan `Hash::make()` (bcrypt) sebelum disimpan ke database. Foto profil default diberikan otomatis saat akun pertama kali dibuat.

```php
<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'lowercase', 'email',
                           'max:255', 'unique:'.User::class],
            'phone'    => ['required', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'password' => Hash::make($request->password),
            'pfpPath'  => 'https://i.ibb.co.com/ZRkqGfJ3/default-avatar-medcomtize.png',
        ]);

        event(new Registered($user));
        Auth::login($user);

        return redirect('/');
    }
}
```

---

### X.10 — Controller Checkout — Integrasi Payment Gateway Duitku (Laravel)

**File:** `app/Http/Controllers/CheckoutController.php`  
**Konteks:** Fungsi `pay()` di Laravel berfungsi sebagai jembatan antara frontend dan payment gateway Duitku. Laravel membuat signature SHA-256, memanggil API Duitku untuk mendapatkan `paymentUrl`, lalu menyinkronkan status ke Node.js API.

```php
public function pay(Request $request)
{
    $request->validate([
        'order_id' => 'required|integer',
        'total'    => 'required|integer|min:1',
    ]);

    $user         = Auth::user();
    $merchantCode = config('services.duitku.merchant_code');
    $apiKey       = config('services.duitku.api_key');
    $mode         = config('services.duitku.mode');

    $merchantOrderId = 'ORDER-' . $request->order_id . '-' . time();
    $paymentAmount   = (int) $request->total;
    $timestamp       = round(microtime(true) * 1000);

    // Signature: SHA256(merchantCode + timestamp + apiKey)
    $signature = hash('sha256', $merchantCode . $timestamp . $apiKey);

    $duitkuUrl = $mode === 'production'
        ? 'https://api.duitku.com/api/merchant/createInvoice'
        : 'https://api-sandbox.duitku.com/api/merchant/createInvoice';

    $response = Http::withHeaders([
        'x-duitku-signature'   => $signature,
        'x-duitku-timestamp'   => $timestamp,
        'x-duitku-merchantcode'=> $merchantCode,
    ])->timeout(30)->post($duitkuUrl, [
        'merchantCode'    => $merchantCode,
        'paymentAmount'   => $paymentAmount,
        'merchantOrderId' => $merchantOrderId,
        'productDetails'  => 'Pembayaran Order #' . $request->order_id,
        'email'           => $user->email,
        'customerVaName'  => $user->name,
        'callbackUrl'     => route('duitku.callback'),
        'returnUrl'       => route('riwayat.pesanan'),
        'expiryPeriod'    => 1440,
    ]);

    $duitkuData = $response->json();
    $paymentUrl = $duitkuData['paymentUrl'];
    $reference  = $duitkuData['reference'];

    // Sinkronisasi status ke Node.js API
    Http::timeout(10)->put("{$this->nodeApiUrl}/orders/{$request->order_id}/status", [
        'status'      => 'pending',
        'payment_url' => $paymentUrl,
    ]);

    return response()->json([
        'success'     => true,
        'payment_url' => $paymentUrl,
        'reference'   => $reference,
    ]);
}
```

---

## Catatan Gaya Penulisan Dokumen

- Setiap subbab dimulai dengan **1–2 kalimat konteks** yang menjelaskan tujuan/fungsi kode tersebut dalam sistem secara keseluruhan
- Blok kode ditulis dengan **font monospace** (Courier New 10pt atau 11pt)
- Di bawah setiap blok kode, sertakan **1 kalimat penutup** yang menjelaskan poin teknis penting dari kode tersebut (misal: penggunaan transaction, pola validasi, dll.)
- Beri **nomor baris** pada kode jika memungkinkan di format dokumen
- Heading subbab menggunakan format: `X.Y Nama Fungsi/File (Bahasa Pemrograman)`
- Ukuran dokumen: A4, margin 3-3-3-3 cm, spasi 1.5, Times New Roman 12pt untuk teks biasa, Courier New 10pt untuk kode
- Tambahkan keterangan nama file (path) di atas setiap blok kode sebagai **caption miring** atau **teks tebal**
