# PROMPT: Buatkan Dokumen Cuplikan Source Code — Medcom

> **Instruksi untuk Claude:** Buatkan dokumen **Cuplikan Source Code** untuk aplikasi **Medcom** berdasarkan semua snippet kode di bawah ini. Dokumen harus berformat laporan akademis (seperti laporan tugas akhir/proyek). Sajikan setiap potongan kode dalam blok kode berformat rapi dengan keterangan penjelasan singkat di atas dan/atau di bawahnya. Gunakan bahasa Indonesia.

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

### X.11 — Service Keranjang Belanja (Node.js)

**File:** `node-api/src/services/cart.service.js`
**Konteks:** Mengelola logika keranjang belanja: mengambil isi keranjang (JOIN ke produk dan toko), menambah item (otomatis membuat keranjang baru jika belum ada, dan menggabungkan jika produk sudah ada), serta memperbarui/menghapus item. Validasi stok dilakukan sebelum setiap penambahan.

```javascript
const db = require('../config/db');

// Ambil isi keranjang user (JOIN produk & toko)
exports.getCartItems = async (userId) => {
  const [rows] = await db.query(`
    SELECT bk.id, bk.keranjang_id, bk.product_id, bk.jumlah, bk.harga,
           p.nama AS product_nama, p.imagePath AS product_imagePath,
           p.stok AS product_stok, p.harga AS product_harga_current,
           t.nama_toko, t.lokasi AS toko_lokasi
    FROM barang_keranjangs bk
    INNER JOIN keranjangs k  ON bk.keranjang_id = k.id
    INNER JOIN products   p  ON bk.product_id   = p.id
    INNER JOIN tokos      t  ON p.toko_id        = t.id
    WHERE k.user_id = ?
    ORDER BY bk.created_at DESC
  `, [userId]);

  return rows.map(row => ({
    id: row.id, product_id: row.product_id, jumlah: row.jumlah, harga: row.harga,
    product: {
      id: row.product_id, nama: row.product_nama,
      imagePath: row.product_imagePath, stok: row.product_stok,
      harga: row.product_harga_current, nama_toko: row.nama_toko,
    }
  }));
};

// Tambah item — buat keranjang jika belum ada, gabungkan jika sudah ada
exports.addItem = async (userId, productId, jumlah) => {
  const [productRows] = await db.query(
    'SELECT stok, harga FROM products WHERE id = ?', [productId]
  );
  if (productRows.length === 0) throw new Error('Produk tidak ditemukan');

  const product = productRows[0];

  let [cartRows] = await db.query('SELECT id FROM keranjangs WHERE user_id = ?', [userId]);
  let cartId;
  if (cartRows.length === 0) {
    const [res] = await db.query('INSERT INTO keranjangs (user_id) VALUES (?)', [userId]);
    cartId = res.insertId;
  } else {
    cartId = cartRows[0].id;
  }

  const [existing] = await db.query(
    'SELECT id, jumlah FROM barang_keranjangs WHERE keranjang_id = ? AND product_id = ?',
    [cartId, productId]
  );

  if (existing.length > 0) {
    const newJumlah = existing[0].jumlah + jumlah;
    if (newJumlah > product.stok)
      throw new Error(`Stok tidak mencukupi. Stok tersedia: ${product.stok}`);
    await db.query('UPDATE barang_keranjangs SET jumlah = ? WHERE id = ?',
      [newJumlah, existing[0].id]);
    return { id: existing[0].id, jumlah: newJumlah };
  } else {
    if (jumlah > product.stok)
      throw new Error(`Stok tidak mencukupi. Stok tersedia: ${product.stok}`);
    const [res] = await db.query(
      'INSERT INTO barang_keranjangs (keranjang_id, product_id, jumlah, harga) VALUES (?, ?, ?, ?)',
      [cartId, productId, jumlah, product.harga]
    );
    return { id: res.insertId, jumlah };
  }
};
```

---

### X.12 — Controller Keranjang (Node.js)

**File:** `node-api/src/controllers/cart.controller.js`
**Konteks:** Menangani seluruh endpoint HTTP untuk operasi keranjang — GET, POST, PUT, dan DELETE. Validasi input dilakukan di level controller sebelum memanggil service.

```javascript
const service  = require('../services/cart.service');
const response = require('../utils/response');

// POST /api/cart/item — Tambah produk ke keranjang
exports.addItem = async (req, res) => {
  try {
    const { user_id, product_id, jumlah } = req.body;
    if (!user_id || !product_id || !jumlah)
      return response.validationError(res, 'Field user_id, product_id, dan jumlah wajib diisi');

    const result = await service.addItem(user_id, product_id, jumlah);
    return response.success(res, result, 'Produk berhasil ditambahkan ke keranjang', 201);
  } catch (error) {
    if (error.message.includes('Stok'))
      return response.error(res, error.message, 400);
    return response.error(res, 'Gagal menambahkan ke keranjang', 500, error.message);
  }
};

// PUT /api/cart/item/:id — Ubah jumlah item
exports.updateItem = async (req, res) => {
  try {
    const { jumlah } = req.body;
    if (!jumlah || jumlah < 1)
      return response.validationError(res, 'Jumlah minimal 1');

    const result = await service.updateItem(req.params.id, jumlah);
    return response.success(res, result, 'Jumlah berhasil diperbarui');
  } catch (error) {
    if (error.message.includes('tidak ditemukan'))
      return response.notFound(res, error.message);
    if (error.message.includes('Stok'))
      return response.error(res, error.message, 400);
    return response.error(res, 'Gagal memperbarui jumlah', 500, error.message);
  }
};

// DELETE /api/cart/:userId/clear — Kosongkan keranjang
exports.clearCart = async (req, res) => {
  try {
    const result = await service.clearCart(req.params.userId);
    return response.success(res, result, 'Keranjang berhasil dikosongkan');
  } catch (error) {
    return response.error(res, 'Gagal mengosongkan keranjang', 500, error.message);
  }
};
```

---

### X.13 — Service Rating (Node.js)

**File:** `node-api/src/services/rating.service.js`
**Konteks:** Mengelola operasi ulasan produk. Fungsi `create` memvalidasi duplikasi rating sebelum menyimpan — satu user hanya boleh memberi satu ulasan per produk. Fungsi `remove` memastikan hanya pemilik ulasan yang dapat menghapusnya.

```javascript
const db = require('../config/db');

exports.getByUser = async (userId) => {
  const [rows] = await db.query(`
    SELECT r.id, r.product_id, r.rating, r.review, r.created_at,
           p.nama AS product_name, p.imagePath AS image_path
    FROM ratings r
    JOIN products p ON p.id = r.product_id
    WHERE r.user_id = ?
    ORDER BY r.created_at DESC
  `, [userId]);
  return rows;
};

exports.create = async ({ user_id, product_id, rating, review }) => {
  // Cegah rating duplikat
  const [exist] = await db.query(
    `SELECT id FROM ratings WHERE user_id = ? AND product_id = ?`,
    [user_id, product_id]
  );
  if (exist.length > 0)
    throw new Error('Kamu sudah pernah merating produk ini');

  const [result] = await db.query(
    `INSERT INTO ratings (user_id, product_id, rating, review, created_at)
     VALUES (?, ?, ?, ?, NOW())`,
    [user_id, product_id, rating, review]
  );
  return { id: result.insertId, user_id, product_id, rating, review };
};

exports.remove = async (id, userId) => {
  const [result] = await db.query(
    `DELETE FROM ratings WHERE id = ? AND user_id = ?`,
    [id, userId]
  );
  if (result.affectedRows === 0)
    throw new Error('Rating tidak ditemukan atau bukan milik Anda');
  return true;
};
```

---

### X.14 — Controller Rating (Node.js)

**File:** `node-api/src/controllers/rating.controller.js`
**Konteks:** Menangani tiga endpoint rating: GET (daftar ulasan user), POST (buat ulasan baru dengan validasi bintang 1–5), dan DELETE (hapus ulasan).

```javascript
const ratingService = require('../services/rating.service');

exports.store = async (req, res) => {
  try {
    const { user_id, product_id, rating, review } = req.body;

    if (!user_id || !product_id || !rating || !review)
      return res.status(400).json({ success: false, message: 'Data tidak lengkap' });

    if (rating < 1 || rating > 5)
      return res.status(400).json({ success: false, message: 'Rating harus antara 1-5' });

    const result = await ratingService.create({ user_id, product_id, rating, review });

    res.status(201).json({ success: true, message: 'Rating berhasil ditambahkan', data: result });
  } catch (err) {
    res.status(400).json({ success: false, message: err.message });
  }
};

exports.destroy = async (req, res) => {
  try {
    const { id } = req.params;
    const { user_id } = req.body;

    if (!user_id)
      return res.status(400).json({ success: false, message: 'user_id wajib diisi' });

    await ratingService.remove(id, user_id);
    res.json({ success: true, message: 'Rating berhasil dihapus' });
  } catch (err) {
    res.status(500).json({ success: false, message: err.message });
  }
};
```

---

### X.15 — Service Toko (Node.js)

**File:** `node-api/src/services/toko.service.js`
**Konteks:** Mengelola CRUD toko penjual. Fungsi `create` memvalidasi agar satu user hanya bisa memiliki satu toko. Fungsi `delete` menerapkan cascade manual — semua produk toko dihapus terlebih dahulu sebelum toko dihapus dari database.

```javascript
const db = require('../config/db');

// Buat toko baru (validasi satu user = satu toko)
exports.create = async (data) => {
  const existing = await exports.checkUserHasToko(data.user_id);
  if (existing) throw new Error('User sudah memiliki toko');

  const [result] = await db.query(
    `INSERT INTO tokos
       (user_id, nama_toko, deskripsi_toko, lokasi, provinsi, kota, kecamatan, kode_wilayah, logo_path, created_at, updated_at)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())`,
    [data.user_id, data.nama_toko, data.deskripsi_toko || null,
     data.lokasi || null, data.provinsi || null, data.kota || null,
     data.kecamatan || null, data.kode_wilayah || null, data.logo_path || null]
  );
  return { id: result.insertId, message: 'Toko berhasil dibuat' };
};

// Update toko (verifikasi ownership)
exports.update = async (tokoId, userId, data) => {
  const [rows] = await db.query(
    'SELECT * FROM tokos WHERE id = ? AND user_id = ?', [tokoId, userId]
  );
  if (!rows.length) throw new Error('Toko tidak ditemukan atau bukan milik Anda');

  const old = rows[0];
  await db.query(
    `UPDATE tokos SET nama_toko=?, deskripsi_toko=?, lokasi=?, logo_path=? WHERE id=? AND user_id=?`,
    [data.nama_toko ?? old.nama_toko, data.deskripsi_toko ?? old.deskripsi_toko,
     data.lokasi ?? old.lokasi, data.logo_path ?? old.logo_path, tokoId, userId]
  );
  return { message: 'Toko berhasil diperbarui' };
};

// Hapus toko beserta semua produknya (cascade manual)
exports.delete = async (tokoId, userId) => {
  const [rows] = await db.query(
    'SELECT id FROM tokos WHERE id = ? AND user_id = ?', [tokoId, userId]
  );
  if (!rows.length) throw new Error('Toko tidak ditemukan atau bukan milik Anda');

  await db.query('DELETE FROM products WHERE toko_id = ?', [tokoId]);
  await db.query('DELETE FROM tokos WHERE id = ?', [tokoId]);
  return { message: 'Toko dan semua produknya berhasil dihapus' };
};
```

---

### X.16 — Controller Toko (Node.js)

**File:** `node-api/src/controllers/toko.controller.js`
**Konteks:** Mengambil identitas penjual dari header `x-user-id` yang dikirim Laravel (menggantikan JWT sementara). Endpoint `checkHasToko` digunakan sebagai gerbang — jika user belum punya toko, frontend mengarahkan ke halaman pembuatan toko.

```javascript
const service  = require('../services/toko.service');
const response = require('../utils/response');

// GET /api/toko/check — Cek apakah user sudah punya toko
exports.checkHasToko = async (req, res) => {
  try {
    const userId = req.headers['x-user-id'];
    if (!userId) return response.validationError(res, 'User ID tidak ditemukan');

    const toko = await service.checkUserHasToko(userId);
    return response.success(res, { hasToko: !!toko, toko }, 'Berhasil cek status toko');
  } catch (error) {
    return response.error(res, 'Gagal cek status toko', 500, error.message);
  }
};

// POST /api/toko — Buat toko baru
exports.create = async (req, res) => {
  try {
    const userId = req.headers['x-user-id'];
    if (!userId) return response.validationError(res, 'User ID tidak ditemukan. Login terlebih dahulu.');

    const { nama_toko, deskripsi_toko, lokasi, provinsi, kota, kecamatan, kode_wilayah, logo_path } = req.body;
    if (!nama_toko) return response.validationError(res, 'Nama toko wajib diisi');

    const result = await service.create({
      user_id: userId, nama_toko, deskripsi_toko, lokasi,
      provinsi, kota, kecamatan, kode_wilayah, logo_path
    });
    return response.success(res, result, 'Toko berhasil dibuat', 201);
  } catch (error) {
    if (error.message === 'User sudah memiliki toko')
      return response.validationError(res, error.message);
    return response.error(res, 'Gagal membuat toko', 500, error.message);
  }
};
```

---

### X.17 — Service Alamat — CRUD dengan Default Handling (Node.js)

**File:** `node-api/src/services/alamat.service.js`
**Konteks:** Mengelola alamat pengiriman pengguna. Saat sebuah alamat ditandai sebagai default (`is_default = 1`), semua alamat lain milik user tersebut secara otomatis di-unset. Saat alamat default dihapus, sistem otomatis menetapkan alamat pertama yang tersisa sebagai default baru.

```javascript
const db = require("../config/db");
const { getWIBTimestamp } = require("../utils/dateHelper");

// Buat alamat baru — otomatis unset default lain jika ini ditandai default
exports.createAlamat = async (userId, data) => {
  const connection = await db.getConnection();
  try {
    await connection.beginTransaction();

    const { alamat, provinsi, kota, kecamatan, kode_wilayah,
            nama_penerima, nomor_penerima, is_default } = data;

    if (!alamat || !nama_penerima || !nomor_penerima)
      throw new Error("Field alamat, nama_penerima, dan nomor_penerima wajib diisi");

    const isDefaultValue = (is_default === true || is_default === 1 || is_default === "1") ? 1 : 0;

    // Unset default lain jika ini akan jadi default
    if (isDefaultValue === 1) {
      await connection.query("UPDATE alamats SET is_default = 0 WHERE user_id = ?", [userId]);
    }

    const now = getWIBTimestamp();
    const [result] = await connection.query(
      `INSERT INTO alamats
         (user_id, alamat, provinsi, kota, kecamatan, kode_wilayah, nama_penerima, nomor_penerima, is_default, created_at, updated_at)
       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)`,
      [userId, alamat, provinsi || null, kota || null, kecamatan || null,
       kode_wilayah || null, nama_penerima, nomor_penerima, isDefaultValue, now, now]
    );

    await connection.commit();
    return await exports.getAlamatById(result.insertId);
  } catch (error) {
    await connection.rollback();
    throw error;
  } finally {
    connection.release();
  }
};

// Hapus alamat — otomatis set alamat pertama sebagai default baru jika yang dihapus adalah default
exports.deleteAlamat = async (alamatId, userId) => {
  const connection = await db.getConnection();
  try {
    await connection.beginTransaction();

    const [existing] = await connection.query(
      "SELECT id, user_id, is_default FROM alamats WHERE id = ?", [alamatId]
    );
    if (existing.length === 0) throw new Error("Alamat tidak ditemukan");
    if (existing[0].user_id !== userId) throw new Error("Anda tidak memiliki akses ke alamat ini");

    const wasDefault = existing[0].is_default === 1;
    await connection.query("DELETE FROM alamats WHERE id = ?", [alamatId]);

    // Jika yang dihapus adalah default, set alamat berikutnya sebagai default
    if (wasDefault) {
      const [remaining] = await connection.query(
        "SELECT id FROM alamats WHERE user_id = ? ORDER BY created_at ASC LIMIT 1", [userId]
      );
      if (remaining.length > 0) {
        await connection.query("UPDATE alamats SET is_default = 1 WHERE id = ?", [remaining[0].id]);
      }
    }

    await connection.commit();
    return { id: alamatId, deleted: true };
  } catch (error) {
    await connection.rollback();
    throw error;
  } finally {
    connection.release();
  }
};
```

---

### X.18 — Controller Profil — Upload & Optimasi Foto (Laravel)

**File:** `app/Http/Controllers/ProfileController.php`
**Konteks:** Fungsi `update_pfp` menangani penggantian foto profil dengan tiga tahap: validasi file, optimasi gambar menggunakan library Intervention Image (crop square 300×300 dan konversi ke WebP), lalu sinkronisasi path ke Node.js API. Foto lama dihapus dari storage sebelum foto baru disimpan.

```php
public function update_pfp(Request $request)
{
    $request->validate([
        'pfpPath' => 'required|image|mimes:jpg,jpeg,png,webp'
    ]);

    $user = $request->user();

    // Hapus foto lama jika ada
    if ($user->pfpPath && str_contains($user->pfpPath, asset('storage'))) {
        $oldPath = str_replace(asset('storage') . '/', '', $user->pfpPath);
        Storage::disk('public')->delete($oldPath);
    }

    // Simpan & optimasi foto baru
    $image    = $request->file('pfpPath');
    $filename = hexdec(uniqid()) . '.webp';
    $path     = 'avatars/' . $filename;

    $manager = new \Intervention\Image\ImageManager(
        new \Intervention\Image\Drivers\Gd\Driver()
    );
    $img = $manager->decode($image);
    $img->cover(300, 300); // Crop persegi 300×300

    Storage::disk('public')->put(
        $path,
        (string) $img->encodeUsingFileExtension('webp', quality: 80)
    );

    $avatarUrl = asset('storage/' . $path);

    // Sinkronisasi ke Node.js API
    $response = Http::patch(
        config('services.node_api.url') . "/api/profile/{$user->id}",
        ['pfpPath' => $avatarUrl]
    );

    if ($response->failed())
        throw new \Exception('Gagal sinkronisasi ke server profile.');

    return back()->with('success', 'Foto profil berhasil diperbarui');
}
```

---

### X.19 — Controller Pengiriman — Integrasi KlikResi (Laravel)

**File:** `app/Http/Controllers/ShippingController.php`
**Konteks:** Menghitung ongkos kirim secara dinamis menggunakan KlikResi API. Origin pengiriman diambil dari `kode_wilayah` toko produk (bukan hardcoded), berat total dihitung dari database berdasarkan berat aktual produk dan quantity, dengan toleransi pembulatan +300g.

```php
public function getRates(Request $request)
{
    $request->validate([
        'destination_id' => 'required|string',
        'items'          => 'required|array|min:1',
    ]);

    $items = $request->items;

    // Origin ID dinamis dari toko produk pertama
    $firstProduct    = Product::with('toko')->find($items[0]['product_id'] ?? $items[0]['id']);
    $dynamicOriginId = ($firstProduct && $firstProduct->toko && $firstProduct->toko->kode_wilayah)
        ? $firstProduct->toko->kode_wilayah
        : $this->originId;

    // Hitung berat total dari database (gram)
    $totalWeightGrams = 0;
    foreach ($items as $item) {
        $product           = Product::find($item['product_id'] ?? $item['id']);
        $productWeight     = $product ? $product->berat : 1000;
        $qty               = $item['jumlah'] ?? $item['qty'] ?? 1;
        $totalWeightGrams += ($productWeight * $qty);
    }

    // Pembulatan: toleransi +300g (contoh: 1.31kg → 2kg, 1.30kg → 1kg)
    $weightKg    = $totalWeightGrams / 1000;
    $intPart     = floor($weightKg);
    $decPart     = $weightKg - $intPart;
    $finalWeight = ($decPart > 0.3) ? ($intPart + 1) : max(1, $intPart);

    // Panggil KlikResi API
    $response = Http::withHeaders(['x-api-key' => $this->apiKey])
        ->post('https://klikresi.com/api/rates', [
            'origin_id'      => $dynamicOriginId,
            'destination_id' => $request->destination_id,
            'weight'         => $finalWeight,
        ]);

    return response()->json([
        'success' => true,
        'weight'  => $finalWeight,
        'data'    => $response->json()
    ]);
}
```

---

### X.20 — Web Routes Laravel (Ringkasan Arsitektur Routing)

**File:** `routes/web.php`
**Konteks:** Mendefinisikan seluruh route web aplikasi Medcom. Route dikelompokkan berdasarkan akses: public (beranda, detail produk), dan protected dengan middleware `auth` (keranjang, checkout, pesanan, toko, profil, dll.). Ini mencerminkan arsitektur dual-role — satu pengguna bisa mengakses fitur pembeli dan penjual sekaligus.

```php
// PUBLIC
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/produk/{id}', [ProductController::class, 'show'])->name('produk.detail');

// PROTECTED (requires auth)
Route::middleware('auth')->group(function () {

    // Keranjang
    Route::get('/keranjang', [KeranjangController::class, 'index'])->name('keranjang');
    Route::post('/keranjang/item', [BarangKeranjangController::class, 'store']);
    Route::put('/keranjang/item/{id}', [BarangKeranjangController::class, 'update']);
    Route::delete('/keranjang/item/{id}', [BarangKeranjangController::class, 'destroy']);

    // Checkout & Pembayaran
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
    Route::post('/checkout/pay', [CheckoutController::class, 'pay'])->name('checkout.pay');

    // Riwayat & Pesanan
    Route::get('/riwayat-pesanan', [OrderController::class, 'riwayatPesanan'])->name('riwayat.pesanan');
    Route::post('/orders/{id}/cancel', [OrderController::class, 'cancelForm'])->name('orders.cancel');
    Route::post('/orders/{id}/finish', [OrderController::class, 'finishOrder'])->name('orders.finish');

    // Rating
    Route::get('/ratings', [RatingController::class, 'index'])->name('ratings.index');
    Route::get('/ratings/create/{productId}', [RatingController::class, 'createRating']);
    Route::post('/ratings', [RatingController::class, 'store'])->name('ratings.store');
    Route::delete('/ratings/{id}', [RatingController::class, 'destroy'])->name('ratings.destroy');

    // Toko (Penjual)
    Route::get('/toko', [TokoController::class, 'index'])->name('profil_toko');
    Route::post('/toko', [TokoController::class, 'store'])->name('toko.store');
    Route::put('/toko/{id}', [TokoController::class, 'update'])->name('toko.update');
    Route::post('/toko/orders/{id}/accept', [TokoController::class, 'acceptOrder']);
    Route::post('/toko/orders/{id}/ship', [TokoController::class, 'shipOrder']);

    // Produk (Penjual)
    Route::post('/product/store', [ProductController::class, 'store'])->name('product.store');
    Route::put('/product/{id}', [ProductController::class, 'update'])->name('product.update');
    Route::delete('/product/{id}', [ProductController::class, 'destroy'])->name('product.destroy');

    // Shipping & Wilayah
    Route::post('/api/shipping/rates', [ShippingController::class, 'getRates'])->name('shipping.rates');
    Route::get('/api/wilayah/provinsi', [WilayahController::class, 'getProvinces']);
    Route::get('/api/wilayah/kota/{provinsi_kode}', [WilayahController::class, 'getCities']);
    Route::get('/api/wilayah/kecamatan/{kota_kode}', [WilayahController::class, 'getDistricts']);
});
```

---

### X.21 — Frontend: Filter & Pencarian Real-time + Pagination (JavaScript)

**File:** `public/js/homepage.js`
**Konteks:** Logika filter produk di sisi client. Semua data produk dimuat sekali dari API, lalu fungsi `applyFilters()` menyaring array secara lokal tanpa request ulang ke server. Hasil filter dibagi halaman oleh `renderProduk()` menggunakan sistem pagination sederhana (12 item per halaman).

```javascript
let produkData         = [];
let filteredProdukData = [];
let currentPage        = 1;
const itemsPerPage     = 12;

let filterState = { search: "", category: "", priceMin: null, priceMax: null };

// Muat data produk dari Node.js API
async function loadData() {
    const API_URL = window.location.hostname === 'localhost'
        ? "http://localhost:3001/api"
        : "https://api.medcom.web.id/api";

    const response = await fetch(`${API_URL}/products`);
    const result   = await response.json();
    produkData     = result.success ? result.data : [];

    filteredProdukData = [...produkData];
    renderProduk();
}

// Filter lokal — tanpa request ulang ke server
function applyFilters() {
    filteredProdukData = produkData.filter((produk) => {
        const matchSearch   = filterState.search === "" ||
            produk.nama.toLowerCase().includes(filterState.search.toLowerCase());
        const matchCategory = filterState.category === "" ||
            produk.category_nama === filterState.category;
        const matchPriceMin = filterState.priceMin === null || produk.harga >= filterState.priceMin;
        const matchPriceMax = filterState.priceMax === null || produk.harga <= filterState.priceMax;
        return matchSearch && matchCategory && matchPriceMin && matchPriceMax;
    });
    currentPage = 1;
    renderProduk();
}

// Render produk dengan pagination
function renderProduk() {
    const container     = document.getElementById("produk-container");
    const totalPages    = Math.ceil(filteredProdukData.length / itemsPerPage);
    const startIndex    = (currentPage - 1) * itemsPerPage;
    const currentItems  = filteredProdukData.slice(startIndex, startIndex + itemsPerPage);

    container.innerHTML = "";
    if (currentItems.length === 0) {
        container.innerHTML = `<div style="grid-column:1/-1;text-align:center;padding:60px 20px;color:#999;">
            <h3>Tidak ada produk ditemukan</h3><p>Coba ubah filter pencarian Anda</p></div>`;
    } else {
        currentItems.forEach(produk => container.appendChild(createProductCard(produk)));
    }
    renderPagination(totalPages);
}
```

---

### X.22 — Frontend: Alur Checkout Client-side (JavaScript)

**File:** `public/js/checkout.js`
**Konteks:** Mengorkestrasi seluruh flow checkout di sisi browser: memuat tarif ongkir dari KlikResi via Laravel (`/api/shipping/rates`), menghitung total harga termasuk diskon, membuat order ke Node.js API (`/api/orders`), lalu mengirim request pembayaran ke Duitku (`/checkout/pay`) dan mengarahkan pengguna ke payment page.

```javascript
// Hitung tarif ongkir dari KlikResi (melalui Laravel proxy)
async function loadShippingRates(destinationId) {
    const currentCheckoutData = JSON.parse(localStorage.getItem("checkoutData")) || [];

    const response = await fetch("/api/shipping/rates", {
        method: "POST",
        headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": getCsrfToken() },
        body: JSON.stringify({ destination_id: destinationId, items: currentCheckoutData }),
    });

    const result = await response.json();
    if (!result.success) throw new Error(result.message);
    renderShippingOptions(result.data, result.weight);
}

// Handler tombol Bayar
payButton.addEventListener("click", async () => {
    const courierCode  = document.getElementById("courierCodeInput").value;
    const shippingCost = parseInt(document.getElementById("shippingCostInput").value);

    if (!selectedAddress || !courierCode) { alert("Lengkapi alamat dan pengiriman!"); return; }

    payButton.disabled = true;
    payButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Memproses...';

    try {
        const currentCheckoutData = JSON.parse(localStorage.getItem("checkoutData")) || [];
        const items = currentCheckoutData.map(item => ({
            product_id: item.productId || item.id, jumlah: item.jumlah
        }));
        const total = parseInt(document.getElementById("orderTotal").textContent.replace(/[^0-9]/g, ""));

        // Step 1: Buat order di Node.js API
        const orderResponse = await fetch("/api/orders", {
            method: "POST",
            headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": getCsrfToken() },
            body: JSON.stringify({
                alamat_id: selectedAddress.id, items,
                courier_code: courierCode,
                courier_name: document.getElementById("courierNameInput").value,
                service_name: document.getElementById("serviceNameInput").value,
                shipping_cost: shippingCost,
            }),
        });
        const orderResult = await orderResponse.json();
        const orderId     = orderResult.data.order.id;

        // Step 2: Proses pembayaran via Duitku (Laravel)
        const paymentResponse = await fetch("/checkout/pay", {
            method: "POST",
            headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": getCsrfToken() },
            body: JSON.stringify({ order_id: orderId, total }),
        });
        const paymentData = await paymentResponse.json();

        // Step 3: Redirect ke halaman pembayaran Duitku
        if (paymentData.payment_url) {
            localStorage.removeItem("checkoutData");
            setTimeout(() => window.location.href = paymentData.payment_url, 1000);
        }
    } catch (err) {
        payButton.disabled = false;
        payButton.innerHTML = '<i class="bi bi-credit-card"></i> Bayar Sekarang';
    }
});
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
