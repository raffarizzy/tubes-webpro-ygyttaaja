# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

**SpareHub** — an Indonesian e-commerce platform for automotive spare parts (sparepart otomotif). UI language is Bahasa Indonesia.

## Commands

### First-time setup
```bash
composer run setup
```
This runs: `composer install`, copies `.env`, generates app key, migrates DB, `npm install`, `npm run build`.

### Development (run all services concurrently)
```bash
composer run dev
```
Starts: Laravel dev server, queue worker, Pail log viewer, and Vite — all in one terminal.

### Node.js API (separate terminal)
```bash
cd node-api
node server.js
```
The Node API must also be running; frontend JS hardcodes `http://localhost:3000/api` as the base URL. Copy `node-api/.env.example` to `node-api/.env` and fill in MySQL credentials (`DB_HOST`, `DB_USER`, `DB_PASS`, `DB_NAME=tubes_webpro`).

### Testing
```bash
composer run test          # run all Pest tests
php artisan test --filter TestName  # run a single test
```

### Code style
```bash
./vendor/bin/pint          # Laravel Pint (PHP formatter)
```

### Database
```bash
php artisan migrate
php artisan migrate:fresh --seed
```

## Architecture

This is a **dual-server** application:

### 1. Laravel (PHP) — `app/`, `routes/`, `resources/`
- Serves Blade views and handles authentication (Laravel Breeze), profile management, toko management, and payment (Xendit).
- Default DB is **SQLite** (see `.env.example`). Laravel manages: `users`, `tokos`, `categories`, `keranjangs`, `barang_keranjangs`, `orders`, `ratings`, `alamats` via Eloquent.
- Routes are in `routes/web.php`. Auth routes are in `routes/auth.php` (required at bottom of `web.php`).
- The product detail page (`/produk/{id}`) passes the ID from Laravel to Blade as `window.PRODUK_ID = {{ $id }}`, then the page JS fetches product data from the Node API.

### 2. Node.js/Express API — `node-api/`
- Connects directly to **MySQL** (`tubes_webpro` database) — separate from Laravel's SQLite.
- Exposes REST endpoints at `/api/products` and `/api/profile`.
- Service layer in `node-api/src/services/` does raw SQL with `mysql2/promise`; controllers in `node-api/src/controllers/` handle HTTP logic.
- The `products` table in MySQL has: `id`, `toko_id`, `category_id`, `nama`, `deskripsi`, `harga`, `diskon`, `stok`, `imagePath`.

### Frontend JS — `public/js/`
Each page has a corresponding vanilla JS file. These files fetch from the Node API (`http://localhost:3000/api`) and manage state in `localStorage`:
- **`homepage.js`** — fetches product list, handles search/filter/pagination, cart count badge.
- **`detail-produk.js`** — fetches single product detail using `window.PRODUK_ID`, handles quantity, add-to-cart (persisted to `localStorage`).
- **`checkout.js`** — reads `checkoutData` from `localStorage`, calls Laravel's `/checkout/pay` which invokes Xendit.
- Cart data is stored entirely in `localStorage` under `keranjangData` — it is not persisted to a database.

### Key Data Flow
```
Browser → Laravel (Blade view rendered, $id injected)
        → public/js/*.js fetches → Node.js API → MySQL
        → Add to cart → localStorage
        → Checkout → Laravel /checkout/pay → Xendit → redirect
```

### Eloquent Model Relationships
- `User` has one `Toko`; `Toko` has many `Product`s.
- `Keranjang` belongs to `User`; has many `BarangKeranjang`.
- `Rating` belongs to a product (via `produk_id`) and user.

## Environment Variables

Laravel `.env` key entries (beyond standard Laravel):
```
XENDIT_SECRET_KEY=   # required for checkout
```

Node API `node-api/.env`:
```
PORT=3001            # note: frontend JS hardcodes port 3000 — keep in sync
DB_HOST=localhost
DB_USER=root
DB_PASS=
DB_NAME=tubes_webpro
```

> The frontend JS files hardcode `http://localhost:3000/api`. If the Node API port changes, update all `API_BASE_URL` constants in `public/js/`.
