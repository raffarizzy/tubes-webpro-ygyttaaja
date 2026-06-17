# Prompt — Dokumen Cuplikan Source Code (untuk Pengajuan HKI)

> File ini **bukan** dokumen HKI-nya, melainkan **prompt** yang dipakai untuk
> meng-generate dokumen "Cuplikan Source Code" (kurang lebih 2–3 halaman) yang
> akan dilampirkan pada pengajuan **Hak Cipta / HKI Program Komputer** ke DJKI.
>
> Prompt ini **sudah berisi cuplikan source code aslinya**, jadi AI assistant
> mana pun (Claude/ChatGPT/Gemini) bisa langsung memprosesnya **tanpa perlu
> akses ke repository**. Tinggal salin seluruh bagian "PROMPT" lalu jalankan.

---

## Cara Pakai

1. Salin **seluruh isi** bagian "PROMPT" di bawah ini (dari `BEGIN PROMPT`
   sampai `END PROMPT`).
2. Tempel ke AI assistant, jalankan.
3. Output diharapkan berupa dokumen siap-cetak (Markdown/PDF) sepanjang ±2–3
   halaman berisi cuplikan kode dari modul-modul inti yang sudah disediakan.

---

## PROMPT

`BEGIN PROMPT`

````
Kamu adalah technical writer yang membantu menyusun dokumen lampiran untuk
pengajuan Hak Cipta Program Komputer (HKI) ke Direktorat Jenderal Kekayaan
Intelektual (DJKI) Indonesia.

Buatkan dokumen berjudul "CUPLIKAN SOURCE CODE" sepanjang kurang lebih
2–3 halaman, MENGGUNAKAN cuplikan kode yang sudah saya sediakan di bawah ini
(bagian "BAHAN CUPLIKAN KODE"). Jangan mengarang kode lain; rapikan, beri
penjelasan singkat per modul, lalu susun jadi dokumen yang rapi dan formal.

=== IDENTITAS CIPTAAN ===
- Nama Aplikasi    : Medcom Indonesia — Platform Marketplace Komponen Elektronik
- Jenis Ciptaan    : Program Komputer (Aplikasi Web)
- Deskripsi Singkat:
  Medcom Indonesia adalah platform transaksi (marketplace) untuk distribusi
  komponen elektronik, peralatan, tools, repair, dan multimedia. Aplikasi
  menghubungkan supplier/manufacturer dengan pembeli melalui katalog produk,
  keranjang belanja, perhitungan ongkos kirim berbasis berat & wilayah,
  pembayaran online (payment gateway Duitku), pelacakan pesanan (nomor resi),
  serta sistem rating/ulasan toko dan produk.
- Pemilik/Pencipta : Tim Pengembang Medcom Indonesia
- Tahun            : 2026

PENTING — Branding:
- Gunakan nama "Medcom" / "Medcom Indonesia" di seluruh NARASI dokumen.
- JANGAN gunakan nama lama "Sparehub" di mana pun pada narasi.
- Tampilkan kode apa adanya (jangan mengubah isi kode), tetapi pada
  penjelasan selalu sebut produk sebagai "Medcom".

=== STACK TEKNOLOGI (tulis ringkas di bagian pembuka dokumen) ===
- Backend Utama : PHP 8.2, Laravel 12 (MVC, Eloquent ORM, Blade)
- API Service   : Node.js + Express (layer service terpisah di folder node-api/)
- Frontend      : Blade Template, Tailwind CSS, Alpine.js, JavaScript vanilla
- Database      : MySQL (migration & Eloquent models)
- Integrasi     : Payment Gateway Duitku, perhitungan ongkir KlikResi (berbasis
                  berat & kode wilayah)
- Pengujian     : Cypress (End-to-End Testing)

=== FORMAT & GAYA DOKUMEN ===
- Bahasa: Indonesia, formal-teknis.
- Struktur dokumen:
  1. Judul + tabel Identitas Ciptaan singkat.
  2. Paragraf "Gambaran Umum" (3–5 kalimat) menjelaskan fungsi aplikasi Medcom.
  3. Ringkasan Arsitektur & Stack Teknologi (bullet).
  4. Bagian "Cuplikan Source Code", dikelompokkan per modul (Modul 1–6 di
     bawah). Untuk setiap cuplikan:
       a. Sub-judul modul + keterangan path berkas.
       b. 1–2 kalimat penjelasan fungsi/peran kode tersebut.
       c. Blok kode dengan syntax highlighting yang sesuai (php / js).
  5. Penutup: 1 paragraf pernyataan bahwa cuplikan di atas merupakan bagian
     inti dan orisinal dari program, dan keseluruhan source code tersimpan pada
     pemegang hak cipta.
- Panjang total: jaga di kisaran 2–3 halaman A4. Bila kepanjangan, potong kode
  yang kurang esensial dan ganti dengan komentar "// ... (kode dipersingkat) ...".
- Tonjolkan LOGIKA BISNIS orisinal: alur registrasi, perhitungan ongkir
  berbasis berat, alur checkout & pembuatan order (transaksi DB + pengurangan
  stok), integrasi pembayaran Duitku beserta verifikasi signature, dan
  agregasi rating.

============================================================
BAHAN CUPLIKAN KODE (sumber: source code asli aplikasi Medcom)
============================================================

----- MODUL 1: Registrasi & Autentikasi Pengguna -----
Berkas: app/Http/Controllers/Auth/RegisteredUserController.php
Peran : Validasi input pendaftaran, hashing password, pembuatan akun, dan
        auto-login pengguna baru.

```php
public function store(Request $request): RedirectResponse
{
    $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
        'phone' => ['required', 'string', 'max:20'],
        'password' => ['required', 'confirmed', Rules\Password::defaults()],
    ]);

    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'phone' => $request->phone,
        'password' => Hash::make($request->password),
        'pfpPath' => 'https://i.ibb.co.com/ZRkqGfJ3/default-avatar-medcomtize.png'
    ]);

    event(new Registered($user));
    Auth::login($user);

    return redirect('/');
}
```

----- MODUL 2: Perhitungan Ongkos Kirim (Berbasis Berat & Wilayah) -----
Berkas: app/Http/Controllers/ShippingController.php
Peran : Menghitung berat total pesanan, menerapkan aturan pembulatan berat
        (toleransi +0,3 kg), menentukan origin dinamis dari wilayah toko, lalu
        meminta tarif ke layanan KlikResi.

```php
public function getRates(Request $request)
{
    $request->validate([
        'destination_id' => 'required|string',
        'items' => 'required|array|min:1',
    ]);

    $items = $request->items;

    // Origin dinamis dari kode wilayah toko produk pertama
    $firstItem = $items[0];
    $productId = $firstItem['product_id'] ?? $firstItem['id'];
    $firstProduct = Product::with('toko')->find($productId);
    $dynamicOriginId = ($firstProduct && $firstProduct->toko && $firstProduct->toko->kode_wilayah)
        ? $firstProduct->toko->kode_wilayah
        : $this->originId;

    // Hitung berat total (gram); default 1kg/item bila berat tidak diset
    $totalWeightGrams = 0;
    foreach ($items as $item) {
        $pId = $item['product_id'] ?? $item['id'];
        $product = Product::find($pId);
        $productWeight = $product ? $product->berat : 1000;
        $qty = $item['jumlah'] ?? $item['qty'] ?? 1;
        $totalWeightGrams += ($productWeight * $qty);
    }

    // Pembulatan berat: 1.30 -> 1kg, 1.31 -> 2kg (toleransi 0,3 kg)
    $weightKg = $totalWeightGrams / 1000;
    $integerPart = floor($weightKg);
    $decimalPart = $weightKg - $integerPart;
    $finalWeight = ($decimalPart > 0.3) ? $integerPart + 1 : max(1, $integerPart);

    // Minta tarif ke KlikResi
    $response = Http::withHeaders(['x-api-key' => $this->apiKey])
        ->post('https://klikresi.com/api/rates', [
            'origin_id' => $dynamicOriginId,
            'destination_id' => $request->destination_id,
            'weight' => $finalWeight,
        ]);

    if ($response->successful()) {
        return response()->json([
            'success' => true,
            'weight' => $finalWeight,
            'data' => $response->json(),
        ]);
    }

    throw new \Exception($response->json('message') ?? 'Gagal mengambil tarif pengiriman');
}
```

----- MODUL 3: Inisiasi Pembayaran (Integrasi Payment Gateway Duitku) -----
Berkas: app/Http/Controllers/CheckoutController.php
Peran : Membentuk signature transaksi (SHA256), membuat invoice di Duitku,
        lalu menyinkronkan URL & referensi pembayaran ke layanan Node.js.

```php
public function pay(Request $request)
{
    $request->validate([
        'order_id' => 'required|integer',
        'total' => 'required|integer|min:1',
    ]);

    $user = Auth::user();
    $merchantCode = config('services.duitku.merchant_code');
    $apiKey = config('services.duitku.api_key');
    $mode = config('services.duitku.mode');

    $merchantOrderId = 'ORDER-' . $request->order_id . '-' . time();
    $paymentAmount = (int) $request->total;
    $timestamp = round(microtime(true) * 1000);

    // Signature = SHA256(merchantCode + timestamp + apiKey)
    $signature = hash('sha256', $merchantCode . $timestamp . $apiKey);

    $duitkuUrl = $mode === 'production'
        ? 'https://api.duitku.com/api/merchant/createInvoice'
        : 'https://api-sandbox.duitku.com/api/merchant/createInvoice';

    $response = Http::withHeaders([
        'x-duitku-signature' => $signature,
        'x-duitku-timestamp' => $timestamp,
        'x-duitku-merchantcode' => $merchantCode,
    ])->timeout(30)->post($duitkuUrl, [
        'merchantCode' => $merchantCode,
        'paymentAmount' => $paymentAmount,
        'merchantOrderId' => $merchantOrderId,
        'productDetails' => 'Pembayaran Order #' . $request->order_id,
        'email' => $user->email,
        'callbackUrl' => route('duitku.callback'),
        'returnUrl' => route('riwayat.pesanan'),
        'expiryPeriod' => 1440,
    ]);

    $duitkuData = $response->json();
    if (($duitkuData['statusCode'] ?? '') !== '00') {
        throw new \Exception('Duitku: ' . ($duitkuData['statusMessage'] ?? 'Unknown Error'));
    }

    // Sinkronisasi status & URL pembayaran ke Node.js API
    Http::timeout(10)->put("{$this->nodeApiUrl}/orders/{$request->order_id}/status", [
        'status' => 'pending',
        'payment_url' => $duitkuData['paymentUrl'],
        'payment_reference' => $duitkuData['reference'],
    ]);

    return response()->json([
        'success' => true,
        'payment_url' => $duitkuData['paymentUrl'],
        'reference' => $duitkuData['reference'],
    ]);
}
```

----- MODUL 4: Verifikasi Callback Pembayaran (Keamanan Transaksi) -----
Berkas: app/Http/Controllers/DuitkuCallbackController.php
Peran : Memverifikasi keaslian notifikasi pembayaran via signature MD5,
        kemudian memperbarui status order menjadi "paid".

```php
public function handle(Request $request)
{
    $apiKey = config('services.duitku.api_key');
    $merchantCode = $request->merchantCode;
    $amount = $request->amount;
    $merchantOrderId = $request->merchantOrderId;
    $signature = $request->signature;
    $resultCode = $request->resultCode; // 00 = success

    // Verifikasi signature: md5(merchantCode + amount + merchantOrderId + apiKey)
    $calcSignature = md5($merchantCode . $amount . $merchantOrderId . $apiKey);
    if ($signature !== $calcSignature) {
        return response()->json(['message' => 'Invalid Signature'], 400);
    }

    if ($resultCode == '00') {
        // Ekstrak Order ID dari format ORDER-123-xxxx
        $orderId = str_replace('ORDER-', '', explode('-', $merchantOrderId)[1]);

        $response = Http::timeout(10)->put(
            "{$this->nodeApiUrl}/orders/{$orderId}/status",
            ['status' => 'paid']
        );

        return $response->successful()
            ? response('OK', 200)
            : response('Internal Server Error', 500);
    }

    return response('OK', 200);
}
```

----- MODUL 5: Pembuatan Pesanan (Transaksi Atomik di Layer Node.js) -----
Berkas: node-api/src/services/checkout.service.js
Peran : Membuat order dalam satu transaksi database — validasi stok, hitung
        total + ongkir, simpan order & item, kurangi stok produk, dan kosongkan
        keranjang. Bila gagal di tahap mana pun, seluruh proses di-rollback.

```javascript
exports.createOrder = async (userId, alamatId, items, courierCode, courierName, serviceName, shippingCost = 0) => {
    const connection = await db.getConnection();
    try {
        await connection.query("SET time_zone = '+07:00'");
        await connection.beginTransaction();

        let totalHarga = 0;
        const orderItemsData = [];

        // Validasi stok & hitung subtotal tiap item
        for (const item of items) {
            const [productRows] = await connection.query(
                "SELECT id, nama, harga, stok FROM products WHERE id = ?",
                [item.product_id]
            );
            if (productRows.length === 0)
                throw new Error(`Produk dengan ID ${item.product_id} tidak ditemukan`);

            const product = productRows[0];
            if (product.stok < item.jumlah)
                throw new Error(`Stok tidak mencukupi untuk produk ${product.nama}. Stok tersedia: ${product.stok}`);

            const subtotal = product.harga * item.jumlah;
            totalHarga += subtotal;
            orderItemsData.push({ product_id: product.id, nama_produk: product.nama, harga: product.harga, qty: item.jumlah, subtotal });
        }

        const grandTotal = totalHarga + shippingCost;
        const now = getWIBTimestamp();

        // Simpan order
        const [orderResult] = await connection.query(
            "INSERT INTO orders (user_id, alamat_id, total_harga, courier_code, courier_name, service_name, shipping_cost, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [userId, alamatId, grandTotal, courierCode, courierName, serviceName, shippingCost, "pending", now, now]
        );
        const orderId = orderResult.insertId;

        // Simpan item & kurangi stok
        for (const itemData of orderItemsData) {
            await connection.query(
                "INSERT INTO order_items (order_id, product_id, nama_produk, harga, qty, subtotal) VALUES (?, ?, ?, ?, ?, ?)",
                [orderId, itemData.product_id, itemData.nama_produk, itemData.harga, itemData.qty, itemData.subtotal]
            );
            await connection.query("UPDATE products SET stok = stok - ? WHERE id = ?", [itemData.qty, itemData.product_id]);
        }

        // Kosongkan keranjang user
        const [cartRows] = await connection.query("SELECT id FROM keranjangs WHERE user_id = ?", [userId]);
        if (cartRows.length > 0) {
            await connection.query("DELETE FROM barang_keranjangs WHERE keranjang_id = ?", [cartRows[0].id]);
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

----- MODUL 6: Detail Produk & Agregasi Rating -----
Berkas: app/Http/Controllers/ProductController.php
Peran : Mengambil detail produk dari Node.js API (dengan fallback ke Eloquent
        bila API gagal), lalu menghitung rata-rata & jumlah ulasan produk.

```php
public function show($id)
{
    // Ambil produk dari Node.js API, fallback ke Eloquent bila gagal
    $response = Http::timeout(5)->get(config('services.node_api.url') . "/api/products/{$id}");
    if (!$response->successful()) {
        $product = \App\Models\Product::with(['toko', 'category'])->find($id);
    } else {
        $product = (object) $response->json('data');
        // ... (rekonstruksi objek toko & kategori dipersingkat) ...
    }

    if (!$product) {
        abort(404, 'Product not found');
    }

    // Agregasi rating dengan Eloquent
    $ratings = \App\Models\Rating::with('user')
        ->where('product_id', $id)
        ->orderBy('created_at', 'desc')
        ->get();

    $avgRating = $ratings->avg('rating') ?? 0;
    $ratingCount = $ratings->count();

    return view('detail-produk', compact('product', 'ratings', 'avgRating', 'ratingCount'));
}
```

============================================================

Sekarang susun dokumen "CUPLIKAN SOURCE CODE" final berdasarkan seluruh bahan
dan instruksi di atas.
````

`END PROMPT`

---

## Catatan untuk Tim

- Enam modul di atas dipilih karena memuat **logika bisnis inti & orisinal**
  Medcom: registrasi, perhitungan ongkir berbasis berat, inisiasi pembayaran
  Duitku, verifikasi signature callback, pembuatan order transaksional, dan
  agregasi rating. Kode boilerplate Laravel/Breeze sengaja tidak dimasukkan
  karena bukan karya orisinal yang relevan untuk klaim HKI.
- Cuplikan sudah dipersingkat agar pas di 2–3 halaman. Bila masih kepanjangan,
  buang Modul 6 atau Modul 1 lebih dulu (paling generik). Bila kependekan,
  tambahkan cuplikan dari `OrderController`, `RatingController`, atau model
  `Product`/`Order`.
- Tidak ada kredensial/API key/isi `.env` pada cuplikan — semuanya diambil
  lewat `config(...)`, aman untuk dilampirkan.
- Sebelum mengirim, pastikan tidak ada string "Sparehub" tersisa pada narasi
  dokumen final — branding resmi adalah **Medcom Indonesia**.
