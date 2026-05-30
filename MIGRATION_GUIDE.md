# Panduan Migrasi Fitur: Laravel ke Node.js + React

Dokumen ini menjelaskan langkah-langkah untuk memindahkan fitur dari sistem Laravel (Monolith/Integrated) ke arsitektur **Node.js (Backend API)** dan **React (Frontend)**.

---

## Alur Kerja Migrasi

### 1. Analisis Fitur di Laravel
Sebelum mulai coding di Node.js, identifikasi komponen berikut di folder Laravel:
- **Route:** Cek `routes/web.php` atau `routes/api.php`.
- **Controller:** Lihat logika bisnis di `app/Http/Controllers/`.
- **Model:** Cek struktur tabel dan relasi di `app/Models/`.
- **Middleware:** Cek proteksi rute (auth, role, dll) di `app/Http/Middleware/`.

### 2. Implementasi di Node.js (`node-api`)
Pindahkan logika tersebut ke struktur folder Node.js:

1.  **Model/Database:** Gunakan `mysql2` untuk menjalankan query secara langsung (Raw Query) ke database yang sama dengan Laravel. Pastikan koneksi di `src/config/db.js` sudah sesuai dengan `.env`.
2.  **Controller:** Buat file baru di `src/controllers/` (misal: `ProductController.js`). Gunakan `db.execute()` atau `db.query()` untuk mengambil data.
3.  **Route:** Daftarkan endpoint di `src/routes/` dan hubungkan ke `app.js`.
4.  **Middleware:** Gunakan middleware JWT atau session di `src/middleware/auth.js` untuk menggantikan `auth` Laravel.

### 3. Update Frontend (React di Laravel)
Saat ini React masih berada di `resources/js`. Ubah cara React mengambil data:

1.  **Update API Base URL:**
    Pastikan konfigurasi Axios atau Fetch mengarah ke port Node.js.
    ```javascript
    // resources/js/services/api.js
    import axios from 'axios';

    const api = axios.create({
        baseURL: 'http://localhost:3001/api', // Port Node.js
        withCredentials: true
    });
    ```
2.  **Ubah Endpoint:** Ganti panggil ke endpoint Laravel menjadi endpoint Node.js yang baru.

### 4. Validasi & Testing
- Pastikan CORS di Node.js sudah mengizinkan request dari domain Laravel (biasanya `localhost:8000`).
- Tes fungsi CRUD dan autentikasi secara menyeluruh.

---

## Contoh Kasus: Migrasi Fitur Produk

| Komponen | Laravel (Asal) | Node.js (Tujuan) |
| :--- | :--- | :--- |
| **Route** | `Route::get('/products', [ProductController::class, 'index'])` | `router.get('/', productController.getAll)` |
| **Logic** | `Product::all()` | `db.query('SELECT * FROM products')` |
| **Auth** | `middleware('auth')` | `authMiddleware.verifyToken` |
| **Frontend** | `axios.get('/api/products')` | `axios.get('http://localhost:3000/api/products')` |

---

## Tips Keamanan
- **Environment Variables:** Pindahkan semua key dari `.env` Laravel ke `.env` Node-API.
- **Password Hashing:** Pastikan algoritma hashing password sama (Laravel menggunakan `bcrypt`) agar user lama tetap bisa login.