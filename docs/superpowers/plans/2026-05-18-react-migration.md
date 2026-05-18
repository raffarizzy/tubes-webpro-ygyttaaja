# SpareHub Frontend → React SPA Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Migrate all SpareHub frontend pages from Blade views + vanilla JS to a React 18 SPA using React Router v6 and Tailwind CSS, keeping all backend (Laravel controllers, Node.js API) intact.

**Architecture:** React SPA bundled via Vite, served by Laravel's catch-all route. AuthContext checks `/api/user` (new JSON route) for session state. CartContext syncs with localStorage. All existing POST/PUT/DELETE routes are called as-is with Axios + CSRF cookie.

**Tech Stack:** React 18, React Router v6, Axios, Tailwind CSS v3, Vite + @vitejs/plugin-react, JavaScript (no TypeScript)

---

## File Map

### Created
- `resources/css/app.css` — Tailwind directives entry
- `resources/js/main.jsx` — React entry point
- `resources/js/App.jsx` — Router + providers
- `resources/js/services/api.js` — Axios for Laravel (CSRF)
- `resources/js/services/nodeApi.js` — Axios for Node.js API port 3000
- `resources/js/utils/format.js` — formatRupiah helper
- `resources/js/context/AuthContext.jsx` — global auth state
- `resources/js/context/CartContext.jsx` — localStorage cart state
- `resources/js/hooks/useAuth.js` — context shortcut
- `resources/js/hooks/useCart.js` — context shortcut
- `resources/js/components/Navbar.jsx` — top navigation
- `resources/js/components/Footer.jsx` — page footer
- `resources/js/components/ProtectedRoute.jsx` — auth guard
- `resources/js/components/ProdukCard.jsx` — product card
- `resources/js/components/Toast.jsx` — notification toast
- `resources/js/pages/HomePage.jsx`
- `resources/js/pages/DetailProdukPage.jsx`
- `resources/js/pages/KeranjangPage.jsx`
- `resources/js/pages/CheckoutPage.jsx`
- `resources/js/pages/LoginPage.jsx`
- `resources/js/pages/RegisterPage.jsx`
- `resources/js/pages/ProfilPage.jsx`
- `resources/js/pages/TokoPage.jsx`
- `resources/js/pages/RiwayatPage.jsx`
- `resources/js/pages/RatingPage.jsx`
- `resources/js/pages/KelolaProdukPage.jsx`
- `resources/views/index.blade.php` — React SPA shell

### Modified
- `vite.config.js` — add @vitejs/plugin-react, change entry to main.jsx
- `tailwind.config.js` — add JSX files to content
- `routes/web.php` — add GET /api/user, GET /api/toko, catch-all route

### Deleted (Task 11)
- `public/js/*.js` — semua vanilla JS
- `resources/views/homepage.blade.php`, `keranjang.blade.php`, `checkout.blade.php`, `detail-produk.blade.php`, `profil.blade.php`, `profil_toko.blade.php`, `riwayat_pesanan.blade.php`, `rating.blade.php`, `mengelolaProdukCRUD.blade.php`, `login.blade.php`, `register.blade.php`, `dashboard.blade.php`
- `resources/views/layouts/main.blade.php`, `navigation.blade.php`

---

## Task 1: Setup — Install dependencies dan konfigurasi build

**Files:**
- Modify: `package.json`
- Modify: `vite.config.js`
- Modify: `tailwind.config.js`
- Create: `resources/css/app.css`
- Create: `resources/js/main.jsx`
- Create: `resources/views/index.blade.php`
- Modify: `routes/web.php`

- [ ] **Step 1: Install React dependencies**

```bash
npm install react react-dom react-router-dom
npm install -D @vitejs/plugin-react
```

Expected output: packages installed without errors.

- [ ] **Step 2: Update vite.config.js**

Replace entire `vite.config.js`:

```js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/main.jsx'],
            refresh: true,
        }),
        react(),
    ],
});
```

- [ ] **Step 3: Update tailwind.config.js — tambah JSX ke content scan**

Replace `content` array in `tailwind.config.js`:

```js
content: [
    './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
    './storage/framework/views/*.php',
    './resources/views/**/*.blade.php',
    './resources/js/**/*.{js,jsx}',
],
```

- [ ] **Step 4: Buat resources/css/app.css**

```css
@tailwind base;
@tailwind components;
@tailwind utilities;
```

- [ ] **Step 5: Buat resources/js/main.jsx**

```jsx
import React from 'react';
import ReactDOM from 'react-dom/client';
import App from './App';
import '../css/app.css';

ReactDOM.createRoot(document.getElementById('app')).render(
  <React.StrictMode>
    <App />
  </React.StrictMode>
);
```

- [ ] **Step 6: Buat resources/views/index.blade.php — React SPA shell**

```blade
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SpareHub</title>
    <link rel="icon" href="https://i.ibb.co.com/VcGWcqFG/icon-Spare-Hub.png" />
    @viteReactRefresh
    @vite(['resources/css/app.css', 'resources/js/main.jsx'])
</head>
<body>
    <div id="app"></div>
</body>
</html>
```

- [ ] **Step 7: Update routes/web.php — tambah JSON routes dan catch-all**

Tambahkan di paling ATAS routes (sebelum existing GET routes) dan hapus semua GET routes yang return view. Isi `routes/web.php` final:

```php
<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TokoController;

// === JSON API routes untuk React SPA ===
Route::get('/api/user', fn() => response()->json(auth()->user()));

Route::middleware('auth')->get('/api/toko', function () {
    $toko = \App\Models\Toko::where('user_id', auth()->id())->first();
    return response()->json($toko);
});

// === Form action routes (tetap, tidak diubah) ===
Route::post('/checkout/pay', [CheckoutController::class, 'pay'])->name('checkout.pay');

Route::get('/payment/success', function () {
    return redirect('/')->with('success', 'Pembayaran berhasil');
})->name('payment.success');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/toko', [TokoController::class, 'store'])->name('toko.store');
    Route::put('/toko/{id}', [TokoController::class, 'update'])->name('toko.update');

    Route::post('/product/store', [ProductController::class, 'store'])->name('product.store');
    Route::put('/product/{id}', [ProductController::class, 'update']);
    Route::delete('/product/{id}', [ProductController::class, 'destroy']);
});

// === Catch-all: semua GET route dilayani React SPA ===
Route::get('/{any}', fn() => view('index'))->where('any', '.*');

require __DIR__.'/auth.php';
```

- [ ] **Step 8: Verifikasi build berjalan**

```bash
npm run dev
```

Buka `http://localhost:8000`. Seharusnya tampil halaman kosong (blank, tapi tidak error). Jika ada error "Cannot find module", jalankan `npm install` ulang.

---

## Task 2: Services, Contexts, Hooks, Utils

**Files:**
- Create: `resources/js/services/api.js`
- Create: `resources/js/services/nodeApi.js`
- Create: `resources/js/utils/format.js`
- Create: `resources/js/context/AuthContext.jsx`
- Create: `resources/js/context/CartContext.jsx`
- Create: `resources/js/hooks/useAuth.js`
- Create: `resources/js/hooks/useCart.js`

- [ ] **Step 1: Buat resources/js/services/api.js**

```js
import axios from 'axios';

const api = axios.create({
  baseURL: '/',
  withCredentials: true,
  headers: {
    'X-Requested-With': 'XMLHttpRequest',
    'Accept': 'application/json',
  },
});

api.interceptors.request.use((config) => {
  const token = document.cookie
    .split('; ')
    .find((row) => row.startsWith('XSRF-TOKEN='))
    ?.split('=')[1];
  if (token) {
    config.headers['X-XSRF-TOKEN'] = decodeURIComponent(token);
  }
  return config;
});

export default api;
```

- [ ] **Step 2: Buat resources/js/services/nodeApi.js**

```js
import axios from 'axios';

const nodeApi = axios.create({
  baseURL: 'http://localhost:3000/api',
  headers: {
    'Accept': 'application/json',
  },
});

export default nodeApi;
```

- [ ] **Step 3: Buat resources/js/utils/format.js**

```js
export function formatRupiah(amount) {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
  }).format(amount);
}
```

- [ ] **Step 4: Buat resources/js/context/AuthContext.jsx**

```jsx
import { createContext, useState, useEffect } from 'react';
import api from '../services/api';

export const AuthContext = createContext(null);

export function AuthProvider({ children }) {
  const [user, setUser] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    api.get('/api/user')
      .then((res) => setUser(res.data))
      .catch(() => setUser(null))
      .finally(() => setLoading(false));
  }, []);

  async function login(email, password) {
    await api.post('/login', { email, password });
    const res = await api.get('/api/user');
    setUser(res.data);
  }

  async function logout() {
    await api.post('/logout');
    setUser(null);
  }

  return (
    <AuthContext.Provider value={{ user, loading, isAuthenticated: !!user, login, logout }}>
      {children}
    </AuthContext.Provider>
  );
}
```

- [ ] **Step 5: Buat resources/js/context/CartContext.jsx**

```jsx
import { createContext, useState, useEffect } from 'react';

export const CartContext = createContext(null);

export function CartProvider({ children }) {
  const [items, setItems] = useState(() => {
    try {
      return JSON.parse(localStorage.getItem('keranjangData')) || [];
    } catch {
      return [];
    }
  });

  useEffect(() => {
    localStorage.setItem('keranjangData', JSON.stringify(items));
  }, [items]);

  function addItem(produk, jumlah, userId = 1) {
    const existing = items.find((i) => i.produkId === produk.id && i.userId === userId);
    if (existing && existing.jumlah + jumlah > produk.stok) return false;
    if (!existing && jumlah > produk.stok) return false;

    setItems((prev) => {
      const idx = prev.findIndex(
        (i) => i.produkId === produk.id && i.userId === userId
      );
      if (idx !== -1) {
        const updated = [...prev];
        updated[idx] = { ...updated[idx], jumlah: updated[idx].jumlah + jumlah };
        return updated;
      }
      return [
        ...prev,
        {
          userId,
          produkId: produk.id,
          nama: produk.nama,
          harga: produk.harga,
          hargaAsli: produk.hargaAsli || produk.harga,
          diskon: produk.diskon || 0,
          jumlah,
          imagePath: produk.imagePath,
          deskripsi: produk.deskripsi,
          stok: produk.stok,
        },
      ];
    });
    return true;
  }

  function updateItem(produkId, jumlah, userId = 1) {
    setItems((prev) =>
      prev.map((i) =>
        i.produkId === produkId && i.userId === userId
          ? { ...i, jumlah }
          : i
      )
    );
  }

  function removeItem(produkId, userId = 1) {
    setItems((prev) =>
      prev.filter((i) => !(i.produkId === produkId && i.userId === userId))
    );
  }

  function clearCart(userId = 1) {
    setItems((prev) => prev.filter((i) => i.userId !== userId));
  }

  const totalItems = items
    .filter((i) => i.userId === 1)
    .reduce((sum, i) => sum + i.jumlah, 0);

  return (
    <CartContext.Provider value={{ items, addItem, updateItem, removeItem, clearCart, totalItems }}>
      {children}
    </CartContext.Provider>
  );
}
```

- [ ] **Step 6: Buat resources/js/hooks/useAuth.js**

```js
import { useContext } from 'react';
import { AuthContext } from '../context/AuthContext';

export function useAuth() {
  return useContext(AuthContext);
}
```

- [ ] **Step 7: Buat resources/js/hooks/useCart.js**

```js
import { useContext } from 'react';
import { CartContext } from '../context/CartContext';

export function useCart() {
  return useContext(CartContext);
}
```

---

## Task 3: Layout — Navbar, Footer, ProtectedRoute, Toast, App

**Files:**
- Create: `resources/js/components/Navbar.jsx`
- Create: `resources/js/components/Footer.jsx`
- Create: `resources/js/components/ProtectedRoute.jsx`
- Create: `resources/js/components/Toast.jsx`
- Create: `resources/js/App.jsx`

- [ ] **Step 1: Buat resources/js/components/Navbar.jsx**

```jsx
import { Link, useNavigate } from 'react-router-dom';
import { useAuth } from '../hooks/useAuth';
import { useCart } from '../hooks/useCart';

export default function Navbar() {
  const { user, isAuthenticated, logout } = useAuth();
  const { totalItems } = useCart();
  const navigate = useNavigate();

  async function handleLogout(e) {
    e.preventDefault();
    if (!confirm('Apakah Anda yakin ingin logout?')) return;
    await logout();
    navigate('/');
  }

  return (
    <nav className="bg-white shadow-md sticky top-0 z-50">
      <div className="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between">
        <Link to="/">
          <img
            src="https://i.ibb.co.com/VcGWcqFG/icon-Spare-Hub.png"
            alt="SpareHub"
            className="h-10 cursor-pointer"
          />
        </Link>

        <ul className="flex items-center gap-6 list-none m-0">
          <li>
            <Link to="/" className="text-gray-700 hover:text-blue-600 font-medium">
              Beranda
            </Link>
          </li>

          <li className="relative">
            <Link to="/keranjang" className="text-gray-700 hover:text-blue-600 font-medium">
              Keranjang
              {totalItems > 0 && (
                <span className="absolute -top-2 -right-3 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                  {totalItems}
                </span>
              )}
            </Link>
          </li>

          <li>
            <Link to="/toko" className="text-gray-700 hover:text-blue-600 font-medium">
              Toko Saya
            </Link>
          </li>

          <li className="flex items-center gap-2">
            <img
              src={user?.pfpPath || 'https://i.ibb.co.com/RkZ105G9/default-avatar.png'}
              alt="User"
              className="w-8 h-8 rounded-full object-cover"
            />
            {isAuthenticated ? (
              <>
                <Link to="/profil" className="text-gray-700 hover:text-blue-600 font-medium">
                  {user?.name}
                </Link>
                <span className="text-gray-300">|</span>
                <button
                  onClick={handleLogout}
                  className="text-red-500 hover:text-red-700 font-medium bg-transparent border-none cursor-pointer"
                >
                  Logout
                </button>
              </>
            ) : (
              <Link to="/login" className="text-blue-600 hover:text-blue-800 font-medium">
                Login
              </Link>
            )}
          </li>
        </ul>
      </div>
    </nav>
  );
}
```

- [ ] **Step 2: Buat resources/js/components/Footer.jsx**

```jsx
export default function Footer() {
  return (
    <footer className="bg-gray-800 text-white text-center py-4 mt-auto">
      <p className="m-0 text-sm">&copy; 2025 SpareHub. Semua hak dilindungi.</p>
    </footer>
  );
}
```

- [ ] **Step 3: Buat resources/js/components/ProtectedRoute.jsx**

```jsx
import { Navigate } from 'react-router-dom';
import { useAuth } from '../hooks/useAuth';

export default function ProtectedRoute({ children }) {
  const { isAuthenticated, loading } = useAuth();

  if (loading) {
    return (
      <div className="flex items-center justify-center min-h-screen">
        <div className="text-gray-500 text-lg">Memuat...</div>
      </div>
    );
  }

  if (!isAuthenticated) {
    return <Navigate to="/login" replace />;
  }

  return children;
}
```

- [ ] **Step 4: Buat resources/js/components/Toast.jsx**

```jsx
import { useState, useEffect } from 'react';

export function useToast() {
  const [toast, setToast] = useState(null);

  function showToast(message, type = 'success') {
    setToast({ message, type });
    setTimeout(() => setToast(null), 3000);
  }

  return { toast, showToast };
}

export function Toast({ toast }) {
  if (!toast) return null;

  const colors = {
    success: 'bg-green-500 text-white',
    warning: 'bg-yellow-400 text-black',
    error: 'bg-red-500 text-white',
    info: 'bg-blue-500 text-white',
  };

  return (
    <div
      className={`fixed top-20 right-5 z-[10000] px-5 py-4 rounded-lg shadow-lg font-medium max-w-xs animate-slide-in ${colors[toast.type] || colors.success}`}
    >
      {toast.message}
    </div>
  );
}
```

- [ ] **Step 5: Buat resources/js/App.jsx**

```jsx
import { BrowserRouter, Routes, Route } from 'react-router-dom';
import { AuthProvider } from './context/AuthContext';
import { CartProvider } from './context/CartContext';
import Navbar from './components/Navbar';
import Footer from './components/Footer';
import ProtectedRoute from './components/ProtectedRoute';
import HomePage from './pages/HomePage';
import DetailProdukPage from './pages/DetailProdukPage';
import KeranjangPage from './pages/KeranjangPage';
import CheckoutPage from './pages/CheckoutPage';
import LoginPage from './pages/LoginPage';
import RegisterPage from './pages/RegisterPage';
import ProfilPage from './pages/ProfilPage';
import TokoPage from './pages/TokoPage';
import RiwayatPage from './pages/RiwayatPage';
import RatingPage from './pages/RatingPage';
import KelolaProdukPage from './pages/KelolaProdukPage';

export default function App() {
  return (
    <BrowserRouter>
      <AuthProvider>
        <CartProvider>
          <div className="min-h-screen flex flex-col">
            <Navbar />
            <main className="flex-1">
              <Routes>
                <Route path="/" element={<HomePage />} />
                <Route path="/produk/:id" element={<DetailProdukPage />} />
                <Route path="/login" element={<LoginPage />} />
                <Route path="/register" element={<RegisterPage />} />
                <Route path="/keranjang" element={<KeranjangPage />} />
                <Route path="/checkout" element={
                  <ProtectedRoute><CheckoutPage /></ProtectedRoute>
                } />
                <Route path="/profil" element={
                  <ProtectedRoute><ProfilPage /></ProtectedRoute>
                } />
                <Route path="/toko" element={
                  <ProtectedRoute><TokoPage /></ProtectedRoute>
                } />
                <Route path="/riwayat" element={
                  <ProtectedRoute><RiwayatPage /></ProtectedRoute>
                } />
                <Route path="/rating" element={
                  <ProtectedRoute><RatingPage /></ProtectedRoute>
                } />
                <Route path="/kelola-produk" element={
                  <ProtectedRoute><KelolaProdukPage /></ProtectedRoute>
                } />
              </Routes>
            </main>
            <Footer />
          </div>
        </CartProvider>
      </AuthProvider>
    </BrowserRouter>
  );
}
```

- [ ] **Step 6: Tambah slide-in animation di tailwind.config.js**

Dalam `theme.extend` di `tailwind.config.js`, tambahkan:

```js
keyframes: {
  'slide-in': {
    from: { transform: 'translateX(400px)', opacity: '0' },
    to: { transform: 'translateX(0)', opacity: '1' },
  },
},
animation: {
  'slide-in': 'slide-in 0.3s ease-out',
},
```

- [ ] **Step 7: Verifikasi App terbuka tanpa error**

```bash
npm run dev
```

Buka `http://localhost:8000`. Harusnya tampil Navbar kosong + Footer. Buka browser console — tidak boleh ada error.

---

## Task 4: ProdukCard + HomePage

**Files:**
- Create: `resources/js/components/ProdukCard.jsx`
- Create: `resources/js/pages/HomePage.jsx`

- [ ] **Step 1: Buat resources/js/components/ProdukCard.jsx**

```jsx
import { Link } from 'react-router-dom';
import { formatRupiah } from '../utils/format';

export default function ProdukCard({ produk }) {
  return (
    <Link to={`/produk/${produk.id}`} className="no-underline">
      <div className="bg-white rounded-xl shadow hover:shadow-lg transition-shadow cursor-pointer overflow-hidden h-full flex flex-col">
        <img
          src={produk.imagePath}
          alt={produk.nama}
          className="w-full h-48 object-cover"
          onError={(e) => {
            e.target.src =
              'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="200" height="200"%3E%3Crect fill="%23ddd" width="200" height="200"/%3E%3Ctext fill="%23999" x="50%25" y="50%25" dominant-baseline="middle" text-anchor="middle" font-size="16"%3ENo Image%3C/text%3E%3C/svg%3E';
          }}
        />
        <div className="p-4 flex flex-col flex-1">
          <h3 className="font-semibold text-gray-800 text-sm mb-1 line-clamp-2">{produk.nama}</h3>
          <p className="text-blue-600 font-bold text-base mt-auto">{formatRupiah(produk.harga)}</p>
          {produk.diskon > 0 && (
            <span className="text-xs text-red-500 font-medium">Diskon {produk.diskon}%</span>
          )}
          <p className="text-xs text-gray-400 mt-1">Stok: {produk.stok}</p>
        </div>
      </div>
    </Link>
  );
}
```

- [ ] **Step 2: Buat resources/js/pages/HomePage.jsx**

```jsx
import { useState, useEffect, useMemo } from 'react';
import nodeApi from '../services/nodeApi';
import ProdukCard from '../components/ProdukCard';

const ITEMS_PER_PAGE = 9;

export default function HomePage() {
  const [produkList, setProdukList] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [search, setSearch] = useState('');
  const [priceMin, setPriceMin] = useState('');
  const [priceMax, setPriceMax] = useState('');
  const [currentPage, setCurrentPage] = useState(1);

  useEffect(() => {
    nodeApi.get('/products')
      .then((res) => setProdukList(res.data.success ? res.data.data : []))
      .catch(() => setError('Gagal memuat produk. Pastikan Node.js API berjalan.'))
      .finally(() => setLoading(false));
  }, []);

  const filtered = useMemo(() => {
    return produkList.filter((p) => {
      const matchSearch = p.nama.toLowerCase().includes(search.toLowerCase());
      const matchMin = priceMin === '' || p.harga >= Number(priceMin);
      const matchMax = priceMax === '' || p.harga <= Number(priceMax);
      return matchSearch && matchMin && matchMax;
    });
  }, [produkList, search, priceMin, priceMax]);

  const totalPages = Math.ceil(filtered.length / ITEMS_PER_PAGE);
  const paginated = filtered.slice(
    (currentPage - 1) * ITEMS_PER_PAGE,
    currentPage * ITEMS_PER_PAGE
  );

  function handleFilter(e) {
    e.preventDefault();
    setCurrentPage(1);
  }

  function handleReset() {
    setSearch('');
    setPriceMin('');
    setPriceMax('');
    setCurrentPage(1);
  }

  return (
    <div>
      {/* Hero */}
      <section className="bg-blue-600 text-white text-center py-20 px-4">
        <h1 className="text-4xl font-bold mb-3">
          Selamat Datang di <span className="text-yellow-300">SpareHub</span>
        </h1>
        <p className="text-lg mb-6">Tempat terbaik untuk mencari suku cadang kendaraan Anda!</p>
        <button
          onClick={() => document.getElementById('produk-section').scrollIntoView({ behavior: 'smooth' })}
          className="bg-white text-blue-600 font-semibold px-6 py-3 rounded-full hover:bg-blue-50 transition"
        >
          Jelajahi Produk
        </button>
      </section>

      {/* Search & Filter */}
      <section className="max-w-6xl mx-auto px-4 mt-10">
        <form onSubmit={handleFilter} className="flex flex-wrap gap-4 items-end">
          <div className="flex-1 min-w-[200px]">
            <label className="block text-sm font-medium text-gray-700 mb-1">Cari Produk</label>
            <input
              type="text"
              value={search}
              onChange={(e) => { setSearch(e.target.value); setCurrentPage(1); }}
              placeholder="Cari nama produk..."
              className="w-full px-4 py-2 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:outline-none"
            />
          </div>
          <div className="min-w-[130px]">
            <label className="block text-sm font-medium text-gray-700 mb-1">Harga Min</label>
            <input
              type="number"
              value={priceMin}
              onChange={(e) => setPriceMin(e.target.value)}
              placeholder="Rp 0"
              className="w-full px-4 py-2 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:outline-none"
            />
          </div>
          <div className="min-w-[130px]">
            <label className="block text-sm font-medium text-gray-700 mb-1">Harga Max</label>
            <input
              type="number"
              value={priceMax}
              onChange={(e) => setPriceMax(e.target.value)}
              placeholder="Rp ∞"
              className="w-full px-4 py-2 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:outline-none"
            />
          </div>
          <button
            type="submit"
            className="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium"
          >
            Filter
          </button>
          <button
            type="button"
            onClick={handleReset}
            className="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium"
          >
            Reset
          </button>
        </form>
      </section>

      {/* Produk Grid */}
      <section id="produk-section" className="max-w-6xl mx-auto px-4 py-10">
        <h2 className="text-2xl font-bold text-gray-800 mb-6">Produk Tersedia</h2>

        {loading && (
          <div className="text-center py-20 text-gray-500">Memuat produk...</div>
        )}

        {error && (
          <div className="text-center py-10 text-red-500">{error}</div>
        )}

        {!loading && !error && paginated.length === 0 && (
          <div className="text-center py-20 text-gray-400">
            <p className="text-lg">Tidak ada produk ditemukan.</p>
          </div>
        )}

        {!loading && !error && paginated.length > 0 && (
          <>
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
              {paginated.map((produk) => (
                <ProdukCard key={produk.id} produk={produk} />
              ))}
            </div>

            {/* Pagination */}
            {totalPages > 1 && (
              <div className="flex justify-center gap-2 mt-8">
                {Array.from({ length: totalPages }, (_, i) => i + 1).map((page) => (
                  <button
                    key={page}
                    onClick={() => setCurrentPage(page)}
                    className={`w-9 h-9 rounded-full font-medium transition ${
                      page === currentPage
                        ? 'bg-blue-600 text-white'
                        : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                    }`}
                  >
                    {page}
                  </button>
                ))}
              </div>
            )}
          </>
        )}
      </section>
    </div>
  );
}
```

- [ ] **Step 3: Verifikasi HomePage**

Buka `http://localhost:8000`. Pastikan:
- Hero section muncul
- Grid produk muncul (jika Node.js API jalan di port 3000)
- Search dan filter bekerja
- Pagination muncul jika produk > 9

---

## Task 5: DetailProdukPage

**Files:**
- Create: `resources/js/pages/DetailProdukPage.jsx`

- [ ] **Step 1: Buat resources/js/pages/DetailProdukPage.jsx**

```jsx
import { useState, useEffect } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import nodeApi from '../services/nodeApi';
import { useCart } from '../hooks/useCart';
import { useAuth } from '../hooks/useAuth';
import { useToast, Toast } from '../components/Toast';
import { formatRupiah } from '../utils/format';

export default function DetailProdukPage() {
  const { id } = useParams();
  const navigate = useNavigate();
  const { addItem } = useCart();
  const { user } = useAuth();
  const { toast, showToast } = useToast();

  const [produk, setProduk] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState(null);
  const [quantity, setQuantity] = useState(1);

  useEffect(() => {
    nodeApi.get(`/products/${id}`)
      .then((res) => {
        if (res.data.success) setProduk(res.data.data);
        else setError('Produk tidak ditemukan');
      })
      .catch(() => setError('Gagal memuat produk'))
      .finally(() => setLoading(false));
  }, [id]);

  function decreaseQty() {
    setQuantity((q) => Math.max(1, q - 1));
  }

  function increaseQty() {
    if (quantity >= produk.stok) {
      showToast(`Stok hanya tersedia ${produk.stok} item`, 'warning');
      return;
    }
    setQuantity((q) => q + 1);
  }

  function handleAddToCart() {
    const success = addItem(produk, quantity, user?.id || 1);
    if (success !== false) {
      showToast(`${quantity} ${produk.nama} berhasil ditambahkan ke keranjang!`, 'success');
      setQuantity(1);
    }
  }

  function handleBeliSekarang() {
    const checkoutData = [{
      nama: produk.nama,
      harga: produk.harga,
      hargaAsli: produk.hargaAsli || produk.harga,
      diskon: produk.diskon || 0,
      jumlah: quantity,
      imagePath: produk.imagePath,
      deskripsi: produk.deskripsi,
    }];
    localStorage.setItem('checkoutData', JSON.stringify(checkoutData));
    navigate('/checkout');
  }

  if (loading) {
    return <div className="text-center py-20 text-gray-500">Memuat produk...</div>;
  }

  if (error || !produk) {
    return (
      <div className="text-center py-20">
        <h2 className="text-xl font-semibold text-gray-700 mb-4">{error || 'Produk tidak ditemukan'}</h2>
        <button onClick={() => navigate('/')} className="text-blue-600 hover:underline">
          ← Kembali ke Beranda
        </button>
      </div>
    );
  }

  const hargaDiskon = produk.diskon > 0
    ? produk.harga - (produk.harga * (produk.diskon < 1 ? produk.diskon : produk.diskon / 100))
    : produk.harga;

  return (
    <div className="max-w-5xl mx-auto px-4 py-10">
      <Toast toast={toast} />

      <div className="grid grid-cols-1 md:grid-cols-2 gap-10">
        {/* Gambar */}
        <div className="flex justify-center">
          <img
            src={produk.imagePath}
            alt={produk.nama}
            className="w-full max-w-sm rounded-2xl shadow-lg object-cover"
            onError={(e) => {
              e.target.src = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="300" height="300"%3E%3Crect fill="%23ddd" width="300" height="300"/%3E%3Ctext fill="%23999" x="50%25" y="50%25" dominant-baseline="middle" text-anchor="middle" font-size="20"%3ENo Image%3C/text%3E%3C/svg%3E';
            }}
          />
        </div>

        {/* Detail */}
        <div>
          <h1 className="text-2xl font-bold text-blue-700 mb-3">{produk.nama}</h1>

          <div className="flex items-center gap-3 mb-4">
            <span className="text-2xl font-bold text-blue-600">{formatRupiah(hargaDiskon)}</span>
            {produk.diskon > 0 && (
              <>
                <span className="text-gray-400 line-through text-base">{formatRupiah(produk.harga)}</span>
                <span className="bg-red-100 text-red-600 text-sm font-semibold px-2 py-0.5 rounded">
                  -{produk.diskon}%
                </span>
              </>
            )}
          </div>

          <div className="bg-gray-50 rounded-xl p-4 mb-4 space-y-2 text-sm">
            <p><span className="font-medium text-gray-600">Stok:</span> <span className="text-gray-800">{produk.stok} item</span></p>
            {produk.nama_toko && (
              <p><span className="font-medium text-gray-600">Toko:</span> <span className="text-gray-800">{produk.nama_toko}</span></p>
            )}
            {produk.lokasi && (
              <p><span className="font-medium text-gray-600">Lokasi:</span> <span className="text-gray-800">{produk.lokasi}</span></p>
            )}
          </div>

          <p className="text-gray-600 mb-6 leading-relaxed">{produk.deskripsi}</p>

          {/* Quantity */}
          <div className="flex items-center gap-4 mb-4">
            <span className="text-sm font-medium text-gray-600">Jumlah:</span>
            <div className="flex items-center border-2 border-gray-200 rounded-lg overflow-hidden">
              <button
                onClick={decreaseQty}
                className="px-4 py-2 text-lg font-bold text-gray-600 hover:bg-gray-100 transition"
              >
                -
              </button>
              <span className="px-4 py-2 font-semibold text-gray-800 min-w-[40px] text-center">
                {quantity}
              </span>
              <button
                onClick={increaseQty}
                className="px-4 py-2 text-lg font-bold text-gray-600 hover:bg-gray-100 transition"
              >
                +
              </button>
            </div>
          </div>

          <p className="text-sm text-gray-500 mb-6">
            Total: <span className="font-bold text-gray-800 text-base">{formatRupiah(hargaDiskon * quantity)}</span>
          </p>

          <div className="flex gap-3">
            <button
              onClick={handleAddToCart}
              className="flex-1 py-3 border-2 border-blue-600 text-blue-600 font-semibold rounded-xl hover:bg-blue-50 transition"
            >
              + Keranjang
            </button>
            <button
              onClick={handleBeliSekarang}
              className="flex-1 py-3 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 transition"
            >
              Beli Sekarang
            </button>
          </div>
        </div>
      </div>

      {/* Rating section placeholder */}
      <div className="mt-12">
        <h2 className="text-xl font-bold text-gray-800 mb-4">Ulasan Pembeli</h2>
        <p className="text-gray-400 text-center py-8">Belum ada ulasan untuk produk ini.</p>
      </div>
    </div>
  );
}
```

- [ ] **Step 2: Verifikasi DetailProdukPage**

Klik salah satu produk dari HomePage. Pastikan:
- Detail produk muncul (gambar, nama, harga)
- Tombol +/- quantity bekerja
- Tombol "Tambah ke Keranjang" menambah item dan badge navbar terupdate

---

## Task 6: KeranjangPage

**Files:**
- Create: `resources/js/pages/KeranjangPage.jsx`

- [ ] **Step 1: Buat resources/js/pages/KeranjangPage.jsx**

```jsx
import { useNavigate } from 'react-router-dom';
import { useCart } from '../hooks/useCart';
import { useAuth } from '../hooks/useAuth';
import { useToast, Toast } from '../components/Toast';
import { formatRupiah } from '../utils/format';

export default function KeranjangPage() {
  const { items, updateItem, removeItem } = useCart();
  const { user } = useAuth();
  const { toast, showToast } = useToast();
  const navigate = useNavigate();

  const userId = user?.id || 1;
  const keranjangUser = items.filter((i) => i.userId === userId);

  const totalHarga = keranjangUser.reduce((sum, i) => sum + i.harga * i.jumlah, 0);
  const totalItem = keranjangUser.reduce((sum, i) => sum + i.jumlah, 0);

  function handleMinus(item) {
    if (item.jumlah <= 1) return;
    updateItem(item.produkId, item.jumlah - 1, userId);
  }

  function handlePlus(item) {
    if (item.jumlah >= item.stok) {
      showToast(`Stok hanya ${item.stok} item`, 'warning');
      return;
    }
    updateItem(item.produkId, item.jumlah + 1, userId);
  }

  function handleHapus(item) {
    if (!confirm(`Hapus "${item.nama}" dari keranjang?`)) return;
    removeItem(item.produkId, userId);
    showToast(`"${item.nama}" dihapus dari keranjang`, 'info');
  }

  function handleCheckout() {
    if (keranjangUser.length === 0) {
      showToast('Keranjang kosong!', 'warning');
      return;
    }
    const checkoutData = keranjangUser.map((item) => ({
      nama: item.nama,
      harga: item.harga,
      hargaAsli: item.hargaAsli || item.harga,
      diskon: item.diskon || 0,
      jumlah: item.jumlah,
      imagePath: item.imagePath,
      deskripsi: item.deskripsi,
    }));
    localStorage.setItem('checkoutData', JSON.stringify(checkoutData));
    navigate('/checkout');
  }

  if (keranjangUser.length === 0) {
    return (
      <div className="max-w-3xl mx-auto px-4 py-20 text-center">
        <Toast toast={toast} />
        <p className="text-xl text-gray-400 mb-4">Keranjang kosong.</p>
        <p className="text-gray-400 mb-6">Yuk belanja dulu!</p>
        <button onClick={() => navigate('/')} className="text-blue-600 hover:underline font-medium">
          ← Kembali ke Beranda
        </button>
      </div>
    );
  }

  return (
    <div className="max-w-4xl mx-auto px-4 py-10">
      <Toast toast={toast} />
      <h1 className="text-2xl font-bold text-gray-800 mb-6">Keranjang Belanja</h1>

      <div className="space-y-4 mb-8">
        {keranjangUser.map((item) => (
          <div key={item.produkId} className="flex gap-4 bg-white rounded-xl shadow p-4 items-center">
            <img
              src={item.imagePath}
              alt={item.nama}
              className="w-24 h-24 object-cover rounded-lg flex-shrink-0"
              onError={(e) => { e.target.src = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="96" height="96"%3E%3Crect fill="%23ddd" width="96" height="96"/%3E%3C/svg%3E'; }}
            />
            <div className="flex-1">
              <h3 className="font-semibold text-gray-800">{item.nama}</h3>
              <p className="text-blue-600 font-bold">{formatRupiah(item.harga)}</p>
              <p className="text-sm text-gray-500">
                Subtotal: <strong>{formatRupiah(item.harga * item.jumlah)}</strong>
              </p>
            </div>
            <div className="flex items-center gap-2">
              <button
                onClick={() => handleMinus(item)}
                className="w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-200 font-bold text-gray-700 flex items-center justify-center"
              >
                -
              </button>
              <span className="w-8 text-center font-semibold">{item.jumlah}</span>
              <button
                onClick={() => handlePlus(item)}
                className="w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-200 font-bold text-gray-700 flex items-center justify-center"
              >
                +
              </button>
            </div>
            <button
              onClick={() => handleHapus(item)}
              className="ml-2 text-red-400 hover:text-red-600 font-medium text-sm"
            >
              Hapus
            </button>
          </div>
        ))}
      </div>

      {/* Ringkasan */}
      <div className="bg-white rounded-xl shadow p-6">
        <div className="flex justify-between text-gray-600 mb-2">
          <span>Total Item</span>
          <span className="font-semibold">{totalItem} pcs</span>
        </div>
        <div className="flex justify-between text-gray-800 text-lg font-bold border-t pt-3">
          <span>Total Harga</span>
          <span>{formatRupiah(totalHarga)}</span>
        </div>
        <button
          onClick={handleCheckout}
          className="w-full mt-4 py-3 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 transition"
        >
          Checkout
        </button>
      </div>
    </div>
  );
}
```

- [ ] **Step 2: Verifikasi KeranjangPage**

Tambah produk dari DetailProdukPage, lalu buka `/keranjang`. Pastikan:
- Item tampil dengan gambar, nama, harga
- +/- quantity bekerja
- Hapus bekerja
- Tombol Checkout navigasi ke `/checkout`

---

## Task 7: CheckoutPage

**Files:**
- Create: `resources/js/pages/CheckoutPage.jsx`

- [ ] **Step 1: Buat resources/js/pages/CheckoutPage.jsx**

```jsx
import { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import api from '../services/api';
import { useCart } from '../hooks/useCart';
import { useAuth } from '../hooks/useAuth';
import { useToast, Toast } from '../components/Toast';
import { formatRupiah } from '../utils/format';

export default function CheckoutPage() {
  const { clearCart } = useCart();
  const { user } = useAuth();
  const { toast, showToast } = useToast();
  const navigate = useNavigate();

  const [checkoutData, setCheckoutData] = useState([]);
  const [alamatList, setAlamatList] = useState([]);
  const [selectedAlamat, setSelectedAlamat] = useState(null);
  const [selectedPayment, setSelectedPayment] = useState(null);
  const [showForm, setShowForm] = useState(false);
  const [editIndex, setEditIndex] = useState(null);
  const [formData, setFormData] = useState({ nama: '', alamat: '', nomor: '' });
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    const data = JSON.parse(localStorage.getItem('checkoutData')) || [];
    setCheckoutData(data);
    const saved = JSON.parse(localStorage.getItem('alamatList')) || [];
    setAlamatList(saved);
  }, []);

  const totals = checkoutData.reduce((acc, item) => {
    const hargaAsli = item.hargaAsli || item.harga;
    const diskon = item.diskon || 0;
    const persen = diskon < 1 ? diskon : diskon / 100;
    const hargaFinal = diskon > 0 ? Math.round(hargaAsli - hargaAsli * persen) : item.harga;
    acc.asli += hargaAsli * item.jumlah;
    acc.final += hargaFinal * item.jumlah;
    return acc;
  }, { asli: 0, final: 0 });

  const totalDiskon = totals.asli - totals.final;

  function saveAlamat() {
    if (!formData.nama || !formData.alamat || !formData.nomor) {
      showToast('Lengkapi semua data alamat!', 'warning');
      return;
    }
    const updated = [...alamatList];
    if (editIndex !== null) {
      updated[editIndex] = formData;
    } else {
      if (updated.length >= 3) { showToast('Maksimal 3 alamat!', 'warning'); return; }
      updated.push(formData);
    }
    setAlamatList(updated);
    localStorage.setItem('alamatList', JSON.stringify(updated));
    setShowForm(false);
    setEditIndex(null);
    setFormData({ nama: '', alamat: '', nomor: '' });
    showToast('Alamat berhasil disimpan!', 'success');
  }

  function deleteAlamat() {
    if (editIndex === null || !confirm('Yakin hapus alamat ini?')) return;
    const updated = alamatList.filter((_, i) => i !== editIndex);
    setAlamatList(updated);
    localStorage.setItem('alamatList', JSON.stringify(updated));
    setShowForm(false);
    setEditIndex(null);
    if (selectedAlamat === editIndex) setSelectedAlamat(null);
    showToast('Alamat dihapus', 'info');
  }

  async function handlePay() {
    if (selectedAlamat === null || !selectedPayment) {
      showToast('Pilih alamat dan metode pembayaran!', 'warning');
      return;
    }
    setLoading(true);
    try {
      const res = await api.post('/checkout/pay', { total: totals.final });
      clearCart(user?.id || 1);
      localStorage.removeItem('checkoutData');
      window.location.href = res.data.invoice_url;
    } catch {
      showToast('Gagal memproses pembayaran', 'error');
      setLoading(false);
    }
  }

  if (checkoutData.length === 0) {
    return (
      <div className="max-w-3xl mx-auto px-4 py-20 text-center">
        <p className="text-xl text-gray-400 mb-4">Tidak ada produk untuk checkout.</p>
        <button onClick={() => navigate('/keranjang')} className="text-blue-600 hover:underline">
          ← Kembali ke Keranjang
        </button>
      </div>
    );
  }

  const paymentOptions = ['Transfer Bank', 'QRIS', 'OVO', 'GoPay'];

  return (
    <div className="max-w-5xl mx-auto px-4 py-10">
      <Toast toast={toast} />
      <h1 className="text-2xl font-bold text-gray-800 mb-8">Checkout</h1>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div className="lg:col-span-2 space-y-6">
          {/* Produk */}
          <div className="bg-white rounded-xl shadow p-5">
            <h2 className="font-semibold text-gray-800 mb-4">Produk</h2>
            {checkoutData.map((item, i) => (
              <div key={i} className="flex gap-3 mb-3">
                <img src={item.imagePath} alt={item.nama} className="w-16 h-16 object-cover rounded-lg" />
                <div>
                  <p className="font-medium text-gray-800">{item.nama}</p>
                  <p className="text-sm text-gray-500">{item.deskripsi}</p>
                  <p className="text-blue-600 font-bold">{formatRupiah(item.harga)} × {item.jumlah}</p>
                </div>
              </div>
            ))}
          </div>

          {/* Alamat */}
          <div className="bg-white rounded-xl shadow p-5">
            <h2 className="font-semibold text-gray-800 mb-4">Alamat Pengiriman</h2>
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
              {alamatList.map((a, i) => (
                <div
                  key={i}
                  onClick={() => setSelectedAlamat(i)}
                  className={`border-2 rounded-lg p-3 cursor-pointer transition ${
                    selectedAlamat === i ? 'border-blue-500 bg-blue-50' : 'border-gray-200 hover:border-gray-300'
                  }`}
                >
                  <div className="flex justify-between">
                    <p className="font-semibold text-sm">{a.nama}</p>
                    <button
                      onClick={(e) => { e.stopPropagation(); setEditIndex(i); setFormData(a); setShowForm(true); }}
                      className="text-blue-500 text-xs hover:underline"
                    >
                      Edit
                    </button>
                  </div>
                  <p className="text-xs text-gray-500 mt-1">{a.alamat}</p>
                  <p className="text-xs text-gray-500">{a.nomor}</p>
                </div>
              ))}
              {alamatList.length < 3 && (
                <button
                  onClick={() => { setEditIndex(null); setFormData({ nama: '', alamat: '', nomor: '' }); setShowForm(true); }}
                  className="border-2 border-dashed border-gray-300 rounded-lg p-3 text-gray-400 hover:border-gray-400 hover:text-gray-600 transition text-sm"
                >
                  + Tambah Alamat
                </button>
              )}
            </div>

            {showForm && (
              <div className="border rounded-lg p-4 bg-gray-50 mt-3 space-y-3">
                <h3 className="font-medium text-sm">{editIndex !== null ? 'Edit Alamat' : 'Alamat Baru'}</h3>
                {['nama', 'alamat', 'nomor'].map((field) => (
                  <input
                    key={field}
                    type="text"
                    placeholder={field.charAt(0).toUpperCase() + field.slice(1)}
                    value={formData[field]}
                    onChange={(e) => setFormData({ ...formData, [field]: e.target.value })}
                    className="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:border-blue-500"
                  />
                ))}
                <div className="flex gap-2">
                  <button onClick={saveAlamat} className="flex-1 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700">
                    Simpan
                  </button>
                  {editIndex !== null && (
                    <button onClick={deleteAlamat} className="px-4 py-2 bg-red-500 text-white text-sm rounded-lg hover:bg-red-600">
                      Hapus
                    </button>
                  )}
                  <button onClick={() => setShowForm(false)} className="px-4 py-2 bg-gray-200 text-gray-700 text-sm rounded-lg hover:bg-gray-300">
                    Batal
                  </button>
                </div>
              </div>
            )}
          </div>

          {/* Pembayaran */}
          <div className="bg-white rounded-xl shadow p-5">
            <h2 className="font-semibold text-gray-800 mb-4">Metode Pembayaran</h2>
            <div className="grid grid-cols-2 gap-3">
              {paymentOptions.map((opt) => (
                <div
                  key={opt}
                  onClick={() => setSelectedPayment(opt)}
                  className={`border-2 rounded-lg p-3 cursor-pointer text-sm font-medium text-center transition ${
                    selectedPayment === opt ? 'border-green-500 bg-green-50 text-green-700' : 'border-gray-200 hover:border-gray-300'
                  }`}
                >
                  {opt}
                </div>
              ))}
            </div>
          </div>
        </div>

        {/* Ringkasan */}
        <div className="bg-white rounded-xl shadow p-5 h-fit sticky top-24">
          <h2 className="font-semibold text-gray-800 mb-4">Ringkasan Pesanan</h2>
          <div className="space-y-2 text-sm">
            <div className="flex justify-between text-gray-600">
              <span>Subtotal</span><span>{formatRupiah(totals.asli)}</span>
            </div>
            <div className="flex justify-between text-gray-600">
              <span>Pengiriman</span><span className="text-green-600 font-medium">Gratis</span>
            </div>
            {totalDiskon > 0 && (
              <div className="flex justify-between text-red-500">
                <span>Diskon</span><span>- {formatRupiah(totalDiskon)}</span>
              </div>
            )}
            <div className="flex justify-between font-bold text-gray-800 text-base border-t pt-3">
              <span>Total</span><span>{formatRupiah(totals.final)}</span>
            </div>
          </div>
          <button
            onClick={handlePay}
            disabled={loading || selectedAlamat === null || !selectedPayment}
            className="w-full mt-5 py-3 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 transition disabled:opacity-50 disabled:cursor-not-allowed"
          >
            {loading ? 'Memproses...' : 'Bayar Sekarang'}
          </button>
        </div>
      </div>
    </div>
  );
}
```

- [ ] **Step 2: Verifikasi CheckoutPage**

Masuk sebagai user, tambah produk ke cart, checkout. Pastikan:
- List produk tampil
- Tambah/edit/hapus alamat bekerja
- Pilih metode pembayaran bekerja
- Tombol "Bayar Sekarang" ter-disable jika belum pilih alamat & payment

---

## Task 8: LoginPage + RegisterPage

**Files:**
- Create: `resources/js/pages/LoginPage.jsx`
- Create: `resources/js/pages/RegisterPage.jsx`

- [ ] **Step 1: Buat resources/js/pages/LoginPage.jsx**

```jsx
import { useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import { useAuth } from '../hooks/useAuth';

export default function LoginPage() {
  const { login, isAuthenticated } = useAuth();
  const navigate = useNavigate();
  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');
  const [showPass, setShowPass] = useState(false);
  const [error, setError] = useState('');
  const [loading, setLoading] = useState(false);

  if (isAuthenticated) {
    navigate('/');
    return null;
  }

  async function handleSubmit(e) {
    e.preventDefault();
    if (!email || !password) { setError('Isi email dan password!'); return; }
    setError('');
    setLoading(true);
    try {
      await login(email, password);
      navigate('/');
    } catch (err) {
      const msg = err.response?.data?.message || 'Email atau password salah!';
      setError(msg);
    } finally {
      setLoading(false);
    }
  }

  return (
    <div className="min-h-[80vh] flex items-center justify-center bg-gray-50 px-4">
      <div className="bg-white rounded-2xl shadow-lg p-8 w-full max-w-md">
        <div className="text-center mb-6">
          <img src="https://i.ibb.co.com/VcGWcqFG/icon-Spare-Hub.png" alt="SpareHub" className="h-12 mx-auto mb-3" />
          <h1 className="text-2xl font-bold text-gray-800">Masuk ke SpareHub</h1>
        </div>

        {error && (
          <div className="bg-red-50 border border-red-200 text-red-600 text-sm px-4 py-3 rounded-lg mb-4">
            {error}
          </div>
        )}

        <form onSubmit={handleSubmit} className="space-y-4">
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input
              type="email"
              value={email}
              onChange={(e) => setEmail(e.target.value)}
              placeholder="email@example.com"
              className="w-full px-4 py-2.5 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:outline-none"
            />
          </div>
          <div>
            <label className="block text-sm font-medium text-gray-700 mb-1">Password</label>
            <div className="relative">
              <input
                type={showPass ? 'text' : 'password'}
                value={password}
                onChange={(e) => setPassword(e.target.value)}
                placeholder="••••••••"
                className="w-full px-4 py-2.5 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:outline-none pr-12"
              />
              <button
                type="button"
                onClick={() => setShowPass(!showPass)}
                className="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600"
              >
                {showPass ? '🙈' : '👁️'}
              </button>
            </div>
          </div>
          <button
            type="submit"
            disabled={loading}
            className="w-full py-3 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 transition disabled:opacity-50"
          >
            {loading ? 'Masuk...' : 'Masuk'}
          </button>
        </form>

        <p className="text-center text-sm text-gray-500 mt-6">
          Belum punya akun?{' '}
          <Link to="/register" className="text-blue-600 font-medium hover:underline">
            Daftar sekarang
          </Link>
        </p>
      </div>
    </div>
  );
}
```

- [ ] **Step 2: Buat resources/js/pages/RegisterPage.jsx**

```jsx
import { useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';
import api from '../services/api';

export default function RegisterPage() {
  const navigate = useNavigate();
  const [form, setForm] = useState({ name: '', email: '', password: '', password_confirmation: '' });
  const [errors, setErrors] = useState({});
  const [loading, setLoading] = useState(false);

  function handleChange(e) {
    setForm({ ...form, [e.target.name]: e.target.value });
  }

  async function handleSubmit(e) {
    e.preventDefault();
    setErrors({});
    if (form.password !== form.password_confirmation) {
      setErrors({ password_confirmation: ['Password tidak cocok!'] });
      return;
    }
    setLoading(true);
    try {
      await api.post('/register', form);
      navigate('/');
    } catch (err) {
      if (err.response?.status === 422) {
        setErrors(err.response.data.errors || {});
      } else {
        setErrors({ general: ['Terjadi kesalahan, coba lagi.'] });
      }
    } finally {
      setLoading(false);
    }
  }

  const fields = [
    { name: 'name', label: 'Nama Lengkap', type: 'text', placeholder: 'Nama Anda' },
    { name: 'email', label: 'Email', type: 'email', placeholder: 'email@example.com' },
    { name: 'password', label: 'Password', type: 'password', placeholder: '••••••••' },
    { name: 'password_confirmation', label: 'Konfirmasi Password', type: 'password', placeholder: '••••••••' },
  ];

  return (
    <div className="min-h-[80vh] flex items-center justify-center bg-gray-50 px-4 py-8">
      <div className="bg-white rounded-2xl shadow-lg p-8 w-full max-w-md">
        <div className="text-center mb-6">
          <img src="https://i.ibb.co.com/VcGWcqFG/icon-Spare-Hub.png" alt="SpareHub" className="h-12 mx-auto mb-3" />
          <h1 className="text-2xl font-bold text-gray-800">Daftar ke SpareHub</h1>
        </div>

        {errors.general && (
          <div className="bg-red-50 border border-red-200 text-red-600 text-sm px-4 py-3 rounded-lg mb-4">
            {errors.general[0]}
          </div>
        )}

        <form onSubmit={handleSubmit} className="space-y-4">
          {fields.map(({ name, label, type, placeholder }) => (
            <div key={name}>
              <label className="block text-sm font-medium text-gray-700 mb-1">{label}</label>
              <input
                type={type}
                name={name}
                value={form[name]}
                onChange={handleChange}
                placeholder={placeholder}
                className="w-full px-4 py-2.5 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:outline-none"
              />
              {errors[name] && (
                <p className="text-red-500 text-xs mt-1">{errors[name][0]}</p>
              )}
            </div>
          ))}
          <button
            type="submit"
            disabled={loading}
            className="w-full py-3 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 transition disabled:opacity-50"
          >
            {loading ? 'Mendaftar...' : 'Daftar'}
          </button>
        </form>

        <p className="text-center text-sm text-gray-500 mt-6">
          Sudah punya akun?{' '}
          <Link to="/login" className="text-blue-600 font-medium hover:underline">
            Masuk di sini
          </Link>
        </p>
      </div>
    </div>
  );
}
```

- [ ] **Step 3: Verifikasi auth pages**

Buka `/login`. Masuk dengan akun valid → redirect ke `/`. Buka `/register`, isi form → redirect ke `/`.

---

## Task 9: ProfilPage + TokoPage

**Files:**
- Create: `resources/js/pages/ProfilPage.jsx`
- Create: `resources/js/pages/TokoPage.jsx`

- [ ] **Step 1: Buat resources/js/pages/ProfilPage.jsx**

```jsx
import { useState, useEffect } from 'react';
import { useAuth } from '../hooks/useAuth';
import api from '../services/api';
import { useToast, Toast } from '../components/Toast';

export default function ProfilPage() {
  const { user } = useAuth();
  const { toast, showToast } = useToast();
  const [form, setForm] = useState({ name: '', email: '' });
  const [loading, setLoading] = useState(false);
  const [errors, setErrors] = useState({});

  useEffect(() => {
    if (user) setForm({ name: user.name || '', email: user.email || '' });
  }, [user]);

  async function handleSubmit(e) {
    e.preventDefault();
    setErrors({});
    setLoading(true);
    try {
      await api.patch('/profile', { name: form.name, email: form.email });
      showToast('Profil berhasil diperbarui!', 'success');
    } catch (err) {
      if (err.response?.status === 422) {
        setErrors(err.response.data.errors || {});
      } else {
        showToast('Gagal memperbarui profil', 'error');
      }
    } finally {
      setLoading(false);
    }
  }

  return (
    <div className="max-w-2xl mx-auto px-4 py-10">
      <Toast toast={toast} />
      <h1 className="text-2xl font-bold text-gray-800 mb-6">Profil Saya</h1>

      <div className="bg-white rounded-2xl shadow p-6">
        <div className="flex items-center gap-4 mb-6">
          <img
            src={user?.pfpPath || 'https://i.ibb.co.com/RkZ105G9/default-avatar.png'}
            alt="Avatar"
            className="w-16 h-16 rounded-full object-cover border-2 border-gray-200"
          />
          <div>
            <p className="font-bold text-lg text-gray-800">{user?.name}</p>
            <p className="text-gray-500 text-sm">{user?.email}</p>
          </div>
        </div>

        <form onSubmit={handleSubmit} className="space-y-4">
          {[
            { name: 'name', label: 'Nama', type: 'text' },
            { name: 'email', label: 'Email', type: 'email' },
          ].map(({ name, label, type }) => (
            <div key={name}>
              <label className="block text-sm font-medium text-gray-700 mb-1">{label}</label>
              <input
                type={type}
                value={form[name]}
                onChange={(e) => setForm({ ...form, [name]: e.target.value })}
                className="w-full px-4 py-2.5 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:outline-none"
              />
              {errors[name] && <p className="text-red-500 text-xs mt-1">{errors[name][0]}</p>}
            </div>
          ))}

          <button
            type="submit"
            disabled={loading}
            className="w-full py-3 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 transition disabled:opacity-50"
          >
            {loading ? 'Menyimpan...' : 'Simpan Perubahan'}
          </button>
        </form>
      </div>
    </div>
  );
}
```

- [ ] **Step 2: Buat resources/js/pages/TokoPage.jsx**

```jsx
import { useState, useEffect } from 'react';
import { useNavigate } from 'react-router-dom';
import api from '../services/api';
import nodeApi from '../services/nodeApi';
import { useAuth } from '../hooks/useAuth';
import { useToast, Toast } from '../components/Toast';
import { formatRupiah } from '../utils/format';

export default function TokoPage() {
  const { user } = useAuth();
  const { toast, showToast } = useToast();
  const navigate = useNavigate();
  const [toko, setToko] = useState(null);
  const [produkList, setProdukList] = useState([]);
  const [loading, setLoading] = useState(true);
  const [showCreateForm, setShowCreateForm] = useState(false);
  const [showEditForm, setShowEditForm] = useState(false);
  const [createForm, setCreateForm] = useState({ nama_toko: '', deskripsi_toko: '', lokasi: '', logo: null });
  const [editForm, setEditForm] = useState({ nama_toko: '', deskripsi_toko: '', lokasi: '', logo: null });

  useEffect(() => {
    api.get('/api/toko')
      .then((res) => {
        setToko(res.data);
        if (res.data) loadProduk();
        else setShowCreateForm(true);
      })
      .catch(() => setShowCreateForm(true))
      .finally(() => setLoading(false));
  }, []);

  async function loadProduk() {
    try {
      const res = await nodeApi.get('/products');
      const all = res.data.success ? res.data.data : [];
      setProdukList(all.filter((p) => p.toko_id === user?.toko?.id));
    } catch {}
  }

  async function handleCreate(e) {
    e.preventDefault();
    const fd = new FormData();
    fd.append('nama_toko', createForm.nama_toko);
    fd.append('deskripsi_toko', createForm.deskripsi_toko);
    fd.append('lokasi', createForm.lokasi);
    if (createForm.logo) fd.append('logo', createForm.logo);
    try {
      await api.post('/toko', fd, { headers: { 'Content-Type': 'multipart/form-data' } });
      showToast('Toko berhasil dibuat!', 'success');
      const res = await api.get('/api/toko');
      setToko(res.data);
      setShowCreateForm(false);
    } catch (err) {
      showToast(err.response?.data?.message || 'Gagal membuat toko', 'error');
    }
  }

  async function handleEdit(e) {
    e.preventDefault();
    const fd = new FormData();
    fd.append('nama_toko', editForm.nama_toko);
    fd.append('deskripsi_toko', editForm.deskripsi_toko);
    fd.append('lokasi', editForm.lokasi);
    if (editForm.logo) fd.append('logo', editForm.logo);
    fd.append('_method', 'PUT');
    try {
      await api.post(`/toko/${toko.id}`, fd, { headers: { 'Content-Type': 'multipart/form-data' } });
      showToast('Toko berhasil diperbarui!', 'success');
      const res = await api.get('/api/toko');
      setToko(res.data);
      setShowEditForm(false);
    } catch (err) {
      showToast(err.response?.data?.message || 'Gagal memperbarui toko', 'error');
    }
  }

  if (loading) return <div className="text-center py-20 text-gray-500">Memuat...</div>;

  if (showCreateForm) {
    return (
      <div className="max-w-2xl mx-auto px-4 py-10">
        <Toast toast={toast} />
        <h1 className="text-2xl font-bold text-gray-800 mb-6">Buat Toko Anda</h1>
        <div className="bg-white rounded-2xl shadow p-6">
          <form onSubmit={handleCreate} className="space-y-4">
            {[
              { name: 'nama_toko', label: 'Nama Toko', type: 'text' },
              { name: 'deskripsi_toko', label: 'Deskripsi', type: 'text' },
              { name: 'lokasi', label: 'Lokasi', type: 'text' },
            ].map(({ name, label, type }) => (
              <div key={name}>
                <label className="block text-sm font-medium text-gray-700 mb-1">{label}</label>
                <input
                  type={type}
                  value={createForm[name]}
                  onChange={(e) => setCreateForm({ ...createForm, [name]: e.target.value })}
                  className="w-full px-4 py-2.5 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:outline-none"
                  required
                />
              </div>
            ))}
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Logo Toko</label>
              <input
                type="file"
                accept="image/*"
                onChange={(e) => setCreateForm({ ...createForm, logo: e.target.files[0] })}
                className="w-full text-sm text-gray-500"
                required
              />
            </div>
            <button type="submit" className="w-full py-3 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700">
              Buat Toko
            </button>
          </form>
        </div>
      </div>
    );
  }

  return (
    <div className="max-w-5xl mx-auto px-4 py-10">
      <Toast toast={toast} />

      {/* Header Toko */}
      <div className="bg-white rounded-2xl shadow p-6 mb-6 flex items-center gap-5">
        <img
          src={toko?.logo_path ? `/storage/${toko.logo_path}` : 'https://i.ibb.co.com/RkZ105G9/default-avatar.png'}
          alt="Logo Toko"
          className="w-20 h-20 rounded-full object-cover border-2 border-gray-200"
        />
        <div className="flex-1">
          <h1 className="text-xl font-bold text-gray-800">{toko?.nama_toko}</h1>
          <p className="text-gray-500 text-sm">{toko?.deskripsi_toko}</p>
          <p className="text-gray-400 text-sm mt-1">📍 {toko?.lokasi}</p>
        </div>
        <div className="flex gap-2">
          <button
            onClick={() => { setEditForm({ nama_toko: toko.nama_toko, deskripsi_toko: toko.deskripsi_toko, lokasi: toko.lokasi, logo: null }); setShowEditForm(true); }}
            className="px-4 py-2 border-2 border-blue-600 text-blue-600 rounded-lg text-sm font-medium hover:bg-blue-50"
          >
            Edit Toko
          </button>
          <button
            onClick={() => navigate('/kelola-produk')}
            className="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700"
          >
            Kelola Produk
          </button>
        </div>
      </div>

      {/* Edit Form */}
      {showEditForm && (
        <div className="bg-white rounded-2xl shadow p-6 mb-6">
          <h2 className="font-semibold text-gray-800 mb-4">Edit Toko</h2>
          <form onSubmit={handleEdit} className="space-y-3">
            {[
              { name: 'nama_toko', label: 'Nama Toko' },
              { name: 'deskripsi_toko', label: 'Deskripsi' },
              { name: 'lokasi', label: 'Lokasi' },
            ].map(({ name, label }) => (
              <div key={name}>
                <label className="block text-sm font-medium text-gray-700 mb-1">{label}</label>
                <input
                  type="text"
                  value={editForm[name]}
                  onChange={(e) => setEditForm({ ...editForm, [name]: e.target.value })}
                  className="w-full px-4 py-2 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:outline-none"
                />
              </div>
            ))}
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Logo Baru (opsional)</label>
              <input type="file" accept="image/*" onChange={(e) => setEditForm({ ...editForm, logo: e.target.files[0] })} />
            </div>
            <div className="flex gap-2">
              <button type="submit" className="flex-1 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700">Simpan</button>
              <button type="button" onClick={() => setShowEditForm(false)} className="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg text-sm hover:bg-gray-300">Batal</button>
            </div>
          </form>
        </div>
      )}

      {/* Produk Toko */}
      <h2 className="text-lg font-bold text-gray-800 mb-4">Produk Toko ({produkList.length})</h2>
      {produkList.length === 0 ? (
        <p className="text-gray-400 text-center py-10">Belum ada produk. Tambah di halaman Kelola Produk.</p>
      ) : (
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
          {produkList.map((p) => (
            <div key={p.id} className="bg-white rounded-xl shadow p-4">
              <img src={p.imagePath} alt={p.nama} className="w-full h-36 object-cover rounded-lg mb-3" />
              <h3 className="font-medium text-gray-800 text-sm">{p.nama}</h3>
              <p className="text-blue-600 font-bold text-sm">{formatRupiah(p.harga)}</p>
              <p className="text-gray-400 text-xs">Stok: {p.stok}</p>
            </div>
          ))}
        </div>
      )}
    </div>
  );
}
```

- [ ] **Step 3: Verifikasi TokoPage**

Login → buka `/toko`. Jika belum punya toko: form pembuatan toko muncul. Jika sudah: info toko dan produk tampil.

---

## Task 10: RiwayatPage + RatingPage + KelolaProdukPage

**Files:**
- Create: `resources/js/pages/RiwayatPage.jsx`
- Create: `resources/js/pages/RatingPage.jsx`
- Create: `resources/js/pages/KelolaProdukPage.jsx`

- [ ] **Step 1: Buat resources/js/pages/RiwayatPage.jsx**

```jsx
import { useState, useEffect } from 'react';
import { Link } from 'react-router-dom';
import { formatRupiah } from '../utils/format';

export default function RiwayatPage() {
  const [pesananList, setPesananList] = useState([]);

  useEffect(() => {
    const data = JSON.parse(localStorage.getItem('pesananList')) || [];
    setPesananList(data);
  }, []);

  if (pesananList.length === 0) {
    return (
      <div className="max-w-3xl mx-auto px-4 py-20 text-center">
        <p className="text-xl text-gray-400 mb-4">Belum ada riwayat pesanan.</p>
        <Link to="/" className="text-blue-600 hover:underline">← Belanja Sekarang</Link>
      </div>
    );
  }

  const statusColors = {
    selesai: 'bg-green-100 text-green-700',
    proses: 'bg-yellow-100 text-yellow-700',
    dibatalkan: 'bg-red-100 text-red-700',
  };

  return (
    <div className="max-w-3xl mx-auto px-4 py-10">
      <h1 className="text-2xl font-bold text-gray-800 mb-6">Riwayat Pesanan</h1>
      <div className="space-y-4">
        {pesananList.map((pesanan, i) => (
          <div key={i} className="bg-white rounded-xl shadow p-4 flex gap-4">
            <img
              src={pesanan.imagePath || 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="80" height="80"%3E%3Crect fill="%23ddd" width="80" height="80"/%3E%3C/svg%3E'}
              alt={pesanan.nama}
              className="w-20 h-20 object-cover rounded-lg flex-shrink-0"
            />
            <div className="flex-1">
              <h3 className="font-semibold text-gray-800">{pesanan.nama || 'Produk'}</h3>
              <p className="text-sm text-gray-500">Jumlah: {pesanan.jumlah}</p>
              <p className="text-sm text-gray-500">Total: {formatRupiah(pesanan.totalHarga || 0)}</p>
              <p className="text-sm text-gray-500">Tanggal: {pesanan.tanggal}</p>
              <p className="text-xs text-gray-400">{pesanan.alamatPengiriman}</p>
            </div>
            <div className="flex flex-col items-end gap-2">
              <span className={`text-xs font-medium px-2 py-1 rounded-full ${statusColors[pesanan.status] || 'bg-gray-100 text-gray-600'}`}>
                {pesanan.status}
              </span>
              {pesanan.status === 'selesai' && (
                <Link to="/rating" className="text-blue-600 text-xs hover:underline">Review</Link>
              )}
            </div>
          </div>
        ))}
      </div>
    </div>
  );
}
```

- [ ] **Step 2: Buat resources/js/pages/RatingPage.jsx**

```jsx
import { useState, useEffect } from 'react';
import { useToast, Toast } from '../components/Toast';

export default function RatingPage() {
  const { toast, showToast } = useToast();
  const [ratingList, setRatingList] = useState([]);
  const [produkList, setProdukList] = useState([]);
  const [selectedProduk, setSelectedProduk] = useState('');
  const [rating, setRating] = useState(5);
  const [komentar, setKomentar] = useState('');

  useEffect(() => {
    const saved = JSON.parse(localStorage.getItem('ratingList')) || [];
    setRatingList(saved);
    const pesanan = JSON.parse(localStorage.getItem('pesananList')) || [];
    const unik = [...new Map(pesanan.map((p) => [p.produkId, p])).values()];
    setProdukList(unik.filter((p) => !saved.some((r) => r.produkId === p.produkId)));
  }, []);

  function handleSubmit(e) {
    e.preventDefault();
    if (!selectedProduk) { showToast('Pilih produk!', 'warning'); return; }
    const newRating = {
      id: Date.now(),
      produkId: Number(selectedProduk),
      rating,
      komentar,
      tanggal: new Date().toISOString().split('T')[0],
    };
    const updated = [...ratingList, newRating];
    setRatingList(updated);
    localStorage.setItem('ratingList', JSON.stringify(updated));
    setSelectedProduk('');
    setRating(5);
    setKomentar('');
    showToast('Rating berhasil ditambahkan!', 'success');
  }

  function handleDelete(id) {
    if (!confirm('Hapus rating ini?')) return;
    const updated = ratingList.filter((r) => r.id !== id);
    setRatingList(updated);
    localStorage.setItem('ratingList', JSON.stringify(updated));
    showToast('Rating dihapus', 'info');
  }

  return (
    <div className="max-w-3xl mx-auto px-4 py-10">
      <Toast toast={toast} />
      <h1 className="text-2xl font-bold text-gray-800 mb-6">Rating & Ulasan</h1>

      {/* Form tambah rating */}
      {produkList.length > 0 && (
        <div className="bg-white rounded-2xl shadow p-6 mb-6">
          <h2 className="font-semibold text-gray-800 mb-4">Tambah Ulasan</h2>
          <form onSubmit={handleSubmit} className="space-y-4">
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Produk</label>
              <select
                value={selectedProduk}
                onChange={(e) => setSelectedProduk(e.target.value)}
                className="w-full px-4 py-2.5 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:outline-none"
              >
                <option value="">-- Pilih Produk --</option>
                {produkList.map((p) => (
                  <option key={p.produkId} value={p.produkId}>{p.nama}</option>
                ))}
              </select>
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Rating</label>
              <div className="flex gap-2">
                {[1, 2, 3, 4, 5].map((n) => (
                  <button
                    key={n}
                    type="button"
                    onClick={() => setRating(n)}
                    className={`text-2xl transition ${n <= rating ? 'text-yellow-400' : 'text-gray-300'}`}
                  >
                    ★
                  </button>
                ))}
              </div>
            </div>
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Komentar</label>
              <textarea
                value={komentar}
                onChange={(e) => setKomentar(e.target.value)}
                rows={3}
                placeholder="Tulis ulasan Anda..."
                className="w-full px-4 py-2.5 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:outline-none resize-none"
              />
            </div>
            <button type="submit" className="w-full py-3 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700">
              Kirim Ulasan
            </button>
          </form>
        </div>
      )}

      {/* List rating */}
      <div className="space-y-4">
        {ratingList.map((r) => (
          <div key={r.id} className="bg-white rounded-xl shadow p-4 flex gap-4">
            <div className="flex-1">
              <div className="flex items-center gap-2 mb-1">
                <span className="text-yellow-400 text-lg">{'★'.repeat(r.rating)}{'☆'.repeat(5 - r.rating)}</span>
              </div>
              <p className="text-gray-700">{r.komentar}</p>
              <p className="text-xs text-gray-400 mt-1">{r.tanggal}</p>
            </div>
            <button
              onClick={() => handleDelete(r.id)}
              className="text-red-400 hover:text-red-600 text-sm self-start"
            >
              Hapus
            </button>
          </div>
        ))}
        {ratingList.length === 0 && (
          <p className="text-gray-400 text-center py-8">Belum ada ulasan.</p>
        )}
      </div>
    </div>
  );
}
```

- [ ] **Step 3: Buat resources/js/pages/KelolaProdukPage.jsx**

```jsx
import { useState, useEffect } from 'react';
import api from '../services/api';
import nodeApi from '../services/nodeApi';
import { useAuth } from '../hooks/useAuth';
import { useToast, Toast } from '../components/Toast';
import { formatRupiah } from '../utils/format';

export default function KelolaProdukPage() {
  const { user } = useAuth();
  const { toast, showToast } = useToast();
  const [produkList, setProdukList] = useState([]);
  const [showForm, setShowForm] = useState(false);
  const [editProduk, setEditProduk] = useState(null);
  const [form, setForm] = useState({ nama: '', harga: '', stok: '', deskripsi: '', category_id: 1, image: null });
  const [tokoId, setTokoId] = useState(null);

  useEffect(() => {
    api.get('/api/toko').then((res) => {
      if (res.data) setTokoId(res.data.id);
    });
    loadProduk();
  }, []);

  async function loadProduk() {
    try {
      const res = await nodeApi.get('/products');
      setProdukList(res.data.success ? res.data.data : []);
    } catch {}
  }

  async function handleSubmit(e) {
    e.preventDefault();
    const fd = new FormData();
    fd.append('nama', form.nama);
    fd.append('harga', form.harga);
    fd.append('stok', form.stok);
    fd.append('deskripsi', form.deskripsi);
    fd.append('category_id', form.category_id);
    if (form.image) fd.append('image', form.image);

    try {
      if (editProduk) {
        fd.append('_method', 'PUT');
        await api.post(`/product/${editProduk.id}`, fd, { headers: { 'Content-Type': 'multipart/form-data' } });
        showToast('Produk berhasil diperbarui!', 'success');
      } else {
        await api.post('/product/store', fd, { headers: { 'Content-Type': 'multipart/form-data' } });
        showToast('Produk berhasil ditambahkan!', 'success');
      }
      setShowForm(false);
      setEditProduk(null);
      setForm({ nama: '', harga: '', stok: '', deskripsi: '', category_id: 1, image: null });
      loadProduk();
    } catch (err) {
      showToast(err.response?.data?.message || 'Gagal menyimpan produk', 'error');
    }
  }

  async function handleDelete(id, nama) {
    if (!confirm(`Hapus produk "${nama}"?`)) return;
    try {
      await api.delete(`/product/${id}`);
      showToast('Produk dihapus', 'info');
      loadProduk();
    } catch {
      showToast('Gagal menghapus produk', 'error');
    }
  }

  function openEdit(produk) {
    setEditProduk(produk);
    setForm({ nama: produk.nama, harga: produk.harga, stok: produk.stok, deskripsi: produk.deskripsi, category_id: produk.category_id || 1, image: null });
    setShowForm(true);
  }

  const myProduk = tokoId ? produkList.filter((p) => p.toko_id === tokoId) : [];

  return (
    <div className="max-w-5xl mx-auto px-4 py-10">
      <Toast toast={toast} />
      <div className="flex items-center justify-between mb-6">
        <h1 className="text-2xl font-bold text-gray-800">Kelola Produk</h1>
        <button
          onClick={() => { setEditProduk(null); setForm({ nama: '', harga: '', stok: '', deskripsi: '', category_id: 1, image: null }); setShowForm(true); }}
          className="px-4 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700"
        >
          + Tambah Produk
        </button>
      </div>

      {showForm && (
        <div className="bg-white rounded-2xl shadow p-6 mb-6">
          <h2 className="font-semibold text-gray-800 mb-4">{editProduk ? 'Edit Produk' : 'Tambah Produk'}</h2>
          <form onSubmit={handleSubmit} className="space-y-3">
            {[
              { name: 'nama', label: 'Nama Produk', type: 'text' },
              { name: 'harga', label: 'Harga (Rp)', type: 'number' },
              { name: 'stok', label: 'Stok', type: 'number' },
              { name: 'deskripsi', label: 'Deskripsi', type: 'text' },
            ].map(({ name, label, type }) => (
              <div key={name}>
                <label className="block text-sm font-medium text-gray-700 mb-1">{label}</label>
                <input
                  type={type}
                  value={form[name]}
                  onChange={(e) => setForm({ ...form, [name]: e.target.value })}
                  className="w-full px-4 py-2 border-2 border-gray-200 rounded-lg focus:border-blue-500 focus:outline-none"
                  required
                />
              </div>
            ))}
            <div>
              <label className="block text-sm font-medium text-gray-700 mb-1">Gambar {editProduk && '(opsional)'}</label>
              <input
                type="file"
                accept="image/*"
                onChange={(e) => setForm({ ...form, image: e.target.files[0] })}
                required={!editProduk}
              />
            </div>
            <div className="flex gap-2 mt-2">
              <button type="submit" className="flex-1 py-2 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700">
                {editProduk ? 'Simpan Perubahan' : 'Tambah Produk'}
              </button>
              <button type="button" onClick={() => setShowForm(false)} className="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                Batal
              </button>
            </div>
          </form>
        </div>
      )}

      {myProduk.length === 0 ? (
        <p className="text-gray-400 text-center py-16">Belum ada produk. Tambah produk pertama Anda!</p>
      ) : (
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
          {myProduk.map((p) => (
            <div key={p.id} className="bg-white rounded-xl shadow p-4">
              <img src={p.imagePath} alt={p.nama} className="w-full h-36 object-cover rounded-lg mb-3"
                onError={(e) => { e.target.src = 'data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" width="200" height="150"%3E%3Crect fill="%23ddd" width="200" height="150"/%3E%3C/svg%3E'; }}
              />
              <h3 className="font-medium text-gray-800 text-sm mb-1">{p.nama}</h3>
              <p className="text-blue-600 font-bold text-sm">{formatRupiah(p.harga)}</p>
              <p className="text-gray-400 text-xs mb-3">Stok: {p.stok}</p>
              <div className="flex gap-2">
                <button onClick={() => openEdit(p)} className="flex-1 py-1.5 text-sm border-2 border-blue-600 text-blue-600 rounded-lg hover:bg-blue-50">
                  Edit
                </button>
                <button onClick={() => handleDelete(p.id, p.nama)} className="flex-1 py-1.5 text-sm bg-red-500 text-white rounded-lg hover:bg-red-600">
                  Hapus
                </button>
              </div>
            </div>
          ))}
        </div>
      )}
    </div>
  );
}
```

- [ ] **Step 4: Verifikasi semua halaman**

Buka `/riwayat`, `/rating`, `/kelola-produk`. Pastikan tidak ada console error dan layout tampil dengan benar.

---

## Task 11: Cleanup — Hapus file lama

**Files:**
- Delete: `public/js/*.js` (semua vanilla JS)
- Delete: blade views yang sudah diganti React

- [ ] **Step 1: Hapus semua vanilla JS**

```bash
del public\js\homepage.js
del public\js\detail-produk.js
del public\js\keranjang.js
del public\js\checkout.js
del public\js\login.js
del public\js\register.js
del public\js\pengguna.js
del public\js\profil_toko.js
del public\js\riwayat_pesanan.js
del public\js\rating.js
del public\js\mengelolaProdukCRUD.js
del public\js\navbar-manager.js
```

- [ ] **Step 2: Hapus Blade views yang sudah diganti React**

```bash
del resources\views\homepage.blade.php
del resources\views\detail-produk.blade.php
del resources\views\keranjang.blade.php
del resources\views\checkout.blade.php
del resources\views\profil.blade.php
del resources\views\profil_toko.blade.php
del resources\views\riwayat_pesanan.blade.php
del resources\views\rating.blade.php
del resources\views\mengelolaProdukCRUD.blade.php
del resources\views\login.blade.php
del resources\views\register.blade.php
del resources\views\dashboard.blade.php
del resources\views\edit_profil.blade.php
del resources\views\layouts\main.blade.php
del resources\views\layouts\navigation.blade.php
```

- [ ] **Step 3: Verifikasi final**

```bash
npm run dev
```

Buka `http://localhost:8000`. Navigasi ke semua halaman:
- `/` — produk grid
- `/produk/1` — detail produk
- `/keranjang` — keranjang
- `/login` — form login
- `/register` — form register
- `/toko` — profil toko (butuh login)
- `/kelola-produk` — CRUD produk (butuh login)

Pastikan tidak ada error di browser console dan semua fitur berfungsi.

---

## Catatan Penting

1. **CSRF**: Axios membaca cookie `XSRF-TOKEN` yang di-set Laravel secara otomatis. Tidak perlu setup tambahan.
2. **Node.js API**: Harus jalan di port 3000. Jalankan `cd node-api && node server.js` di terminal terpisah.
3. **Laravel server**: Jalankan `composer run dev` di terminal utama.
4. **Auth routes** (`routes/auth.php`) tidak diubah — Breeze handle POST /login, /register, /logout.
5. **`/api/user`**: Returns `null` jika user belum login (bukan 401) karena tidak pakai `auth` middleware — React cek `user === null`.
6. **File upload**: Form yang upload gambar pakai `multipart/form-data` dan Laravel method-spoofing `_method=PUT` untuk update.
