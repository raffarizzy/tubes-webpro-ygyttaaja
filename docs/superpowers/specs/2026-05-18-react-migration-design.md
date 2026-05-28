# SpareHub Frontend Migration: Blade + Vanilla JS → React SPA

**Date:** 2026-05-18  
**Project:** SpareHub (e-commerce sparepart otomotif)  
**Scope:** Frontend only — backend (Laravel controllers, Node.js API) tidak diubah

---

## Ringkasan

Migrasi seluruh frontend SpareHub dari Blade views + vanilla JS ke React SPA (Single Page Application). Backend tetap utuh: Laravel controllers, Node.js/Express API, dan semua endpoint tidak disentuh.

---

## Arsitektur

### Stack

- **React 18** — UI library
- **React Router v6** — client-side routing
- **Vite** — bundler (sudah ada, tambah `@vitejs/plugin-react`)
- **Tailwind CSS v3** — styling (sudah ada, ganti Bootstrap CDN)
- **Axios** — HTTP client (sudah ada di devDependencies)
- **React Context API** — state management (tidak perlu Redux/Zustand)
- **JavaScript** (bukan TypeScript)

### Diagram Arsitektur

```
Browser (React SPA)
   │
   ├─ React Router v6  (semua routing di client)
   │     /               → <HomePage>
   │     /produk/:id     → <DetailProdukPage>
   │     /keranjang      → <KeranjangPage>
   │     /checkout       → <CheckoutPage>
   │     /login          → <LoginPage>
   │     /register       → <RegisterPage>
   │     /profil         → <ProfilPage>
   │     /toko           → <TokoPage>
   │     /riwayat        → <RiwayatPage>
   │     /rating         → <RatingPage>
   │     /kelola-produk  → <KelolaProdukPage>
   │
   ├─ Laravel (port 8000) — existing routes, tidak diubah
   │     POST /login
   │     POST /register
   │     POST /logout
   │     POST /checkout/pay
   │     GET/POST/PUT /toko
   │     POST/PUT/DELETE /product
   │     GET /riwayat (jika ada)
   │     GET /rating (jika ada)
   │
   └─ Node.js API (port 3000) — tidak diubah sama sekali
         GET /api/products
         GET /api/products/:id
         GET /api/profile
```

### Perubahan di Laravel `web.php`

Perubahan minimal di backend:

1. Tambah satu route JSON untuk auth state check:
```php
Route::get('/api/user', fn() => response()->json(auth()->user()));
```

2. Semua GET route yang return Blade view diganti catch-all yang return `index.blade.php`:
```php
// Sesudah — catch-all (di BAWAH semua POST/PUT/DELETE routes)
Route::get('/{any}', fn() => view('index'))->where('any', '.*');
```

`resources/views/index.blade.php` adalah shell minimal yang hanya load Vite assets React.

> **Catatan:** Sanctum tidak terinstall di project ini. Auth check pakai session cookie Laravel biasa. CSRF otomatis di-handle via `XSRF-TOKEN` cookie yang sudah di-set Laravel — Axios akan membacanya secara otomatis dengan `withCredentials: true`.

---

## Struktur Folder

```
resources/
  js/
    main.jsx                   ← ReactDOM.createRoot, mount App
    App.jsx                    ← React Router setup, provider wrapper
    
    pages/
      HomePage.jsx             ← ganti homepage.blade.php + homepage.js
      DetailProdukPage.jsx     ← ganti detail-produk.blade.php + detail-produk.js
      KeranjangPage.jsx        ← ganti keranjang.blade.php + keranjang.js
      CheckoutPage.jsx         ← ganti checkout.blade.php + checkout.js
      LoginPage.jsx            ← ganti auth/login.blade.php + login.js
      RegisterPage.jsx         ← ganti auth/register.blade.php + register.js
      ProfilPage.jsx           ← ganti profil.blade.php + pengguna.js
      TokoPage.jsx             ← ganti profil_toko.blade.php + profil_toko.js
      RiwayatPage.jsx          ← ganti riwayat_pesanan.blade.php + riwayat_pesanan.js
      RatingPage.jsx           ← ganti rating.blade.php + rating.js
      KelolaProdukPage.jsx     ← ganti mengelolaProdukCRUD.blade.php + mengelolaProdukCRUD.js
    
    components/
      Navbar.jsx               ← ganti layouts/navigation.blade.php + navbar-manager.js
      Footer.jsx               ← ganti footer di layouts/main.blade.php
      ProdukCard.jsx           ← card produk reusable
      ProtectedRoute.jsx       ← guard untuk halaman yang butuh login
    
    context/
      AuthContext.jsx          ← global auth state (user info, login/logout methods)
      CartContext.jsx          ← cart state dari localStorage, sync otomatis
    
    hooks/
      useAuth.js               ← shortcut hook untuk AuthContext
      useCart.js               ← shortcut hook untuk CartContext
```

---

## State Management

### AuthContext

- Mount → GET `/api/user` (Laravel, existing endpoint) → simpan user di state
- `login(credentials)` → POST `/login` dengan CSRF → update state
- `logout()` → POST `/logout` → clear state
- State: `{ user: null | {...}, loading: boolean, isAuthenticated: boolean }`

### CartContext

- Baca `keranjangData` dari localStorage saat mount
- Semua operasi cart (add, remove, update qty) tulis ke localStorage
- Badge di Navbar subscribe ke CartContext (otomatis update)
- State: `{ items: [], addItem, removeItem, clearCart, totalItems }`

### CSRF untuk POST ke Laravel

Axios instance dikonfigurasi dengan `withCredentials: true`. Laravel Sanctum SPA mode otomatis handle CSRF via cookie (`XSRF-TOKEN`) — tidak butuh perubahan backend.

```js
// services/api.js
const api = axios.create({
  baseURL: '/',
  withCredentials: true,
  headers: { 'X-Requested-With': 'XMLHttpRequest' }
});
```

---

## Data Flow

### Product data (dari Node.js API)

```
HomePage mount
  → fetch http://localhost:3000/api/products
  → render <ProdukCard> grid
  → search/filter/pagination di client-side state

DetailProdukPage mount (param: id dari URL)
  → fetch http://localhost:3000/api/products/:id
  → render detail + add to cart
```

### Checkout flow

```
CheckoutPage mount
  → baca checkoutData dari localStorage
  → render form
  → submit → POST /checkout/pay (Laravel)
  → redirect ke Xendit payment URL
```

### Auth flow

```
App mount
  → GET /api/user
  → jika 200: set user di AuthContext
  → jika 401: user = null (guest)

LoginPage submit
  → POST /login (form data)
  → jika success: redirect ke /

ProtectedRoute
  → cek AuthContext.isAuthenticated
  → jika false: <Navigate to="/login" />
```

---

## Protected Routes (halaman yang butuh login)

- `/checkout`
- `/profil`
- `/toko`
- `/riwayat`
- `/rating`
- `/kelola-produk`

---

## Migration Strategy

Migrasi dilakukan halaman per halaman (bukan big-bang rewrite):

| Step | Task | Output |
|------|------|--------|
| 1 | Setup: install React, Vite plugin, konfigurasi Tailwind, buat shell index.blade.php | Project bisa jalan |
| 2 | Buat AuthContext, CartContext, Navbar, Footer, App.jsx, main.jsx | Layout + routing dasar |
| 3 | HomePage | Produk grid dari Node.js API |
| 4 | DetailProdukPage | Routing param + cart |
| 5 | KeranjangPage | Cart read/write localStorage |
| 6 | CheckoutPage | POST ke Laravel |
| 7 | LoginPage + RegisterPage | Auth flow |
| 8 | ProfilPage + TokoPage | Auth-protected |
| 9 | RiwayatPage + RatingPage + KelolaProdukPage | Remaining pages |
| 10 | Cleanup | Hapus `public/js/*.js`, Blade views lama |

---

## Yang Dihapus Setelah Migrasi

- `public/js/*.js` — semua vanilla JS (homepage.js, detail-produk.js, dll.)
- `resources/views/*.blade.php` — semua Blade views yang sudah diganti React
- Bootstrap CDN di `layouts/main.blade.php` — diganti Tailwind
- `resources/views/layouts/main.blade.php` — diganti `index.blade.php` yang minimal

## Yang Tidak Diubah

- `node-api/` — seluruh Node.js Express API
- `app/Http/Controllers/` — semua Laravel controller
- `routes/auth.php` — Laravel Breeze auth routes
- POST/PUT/DELETE routes di `routes/web.php`
- Database (SQLite Laravel + MySQL Node.js)
- `.env` files

---

## Dependencies Baru

```json
{
  "dependencies": {
    "react": "^18.0.0",
    "react-dom": "^18.0.0",
    "react-router-dom": "^6.0.0"
  },
  "devDependencies": {
    "@vitejs/plugin-react": "^4.0.0"
  }
}
```

Axios dan Tailwind CSS sudah ada di `package.json`.
