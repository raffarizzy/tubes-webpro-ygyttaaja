# PROMPT: Buatkan Dokumen Cuplikan Source Code — Medcom

> **Instruksi untuk Claude/AI:** Buatkan dokumen **Cuplikan Source Code** untuk aplikasi **Medcom** berdasarkan snippet kode di bawah ini. Dokumen berformat laporan akademis (laporan tugas akhir/proyek), bahasa Indonesia.
>
> **WAJIB DIPATUHI:**
> 1. **Mulai penomoran dari `BAB 1`** — gunakan judul **"BAB 1 — Cuplikan Source Code"** dan subbab **1.1, 1.2, 1.3, 1.4, 1.5**. JANGAN memakai BAB 4, BAB X, atau nomor lain.
> 2. **Maksimal 3 halaman A4.** Pilih hanya kode inti; jangan menambah snippet di luar yang disediakan. Jika perlu, persingkat baris kode yang kurang esensial dengan komentar `// ... (dipersingkat) ...` agar tetap muat 3 halaman.
> 3. Gunakan nama produk **Medcom** di seluruh narasi (jangan gunakan nama lama "Sparehub").
>
> _Catatan: seluruh snippet sudah diselaraskan dengan source code terbaru. Tampilkan kode apa adanya._

---

## Informasi Aplikasi

| Item | Detail |
|------|--------|
| **Nama Aplikasi** | Medcom |
| **Jenis** | Platform e-commerce / marketplace distribusi komponen elektronik, peralatan, tools, repair, dan multimedia |
| **Stack Teknologi** | Laravel (PHP) + Node.js (Express) + MySQL |
| **Arsitektur** | Laravel sebagai web layer, Node.js sebagai REST API backend, terintegrasi payment gateway (Duitku) & layanan ongkir (KlikResi) |

---

## Struktur Dokumen yang Diinginkan (maks 3 halaman)

```
BAB 1 — Cuplikan Source Code
  1.1  Registrasi Akun & Hashing Password (Laravel)
  1.2  Perhitungan Ongkos Kirim Dinamis (Laravel)
  1.3  Pembuatan Pesanan dengan Database Transaction (Node.js)
  1.4  Integrasi Payment Gateway Duitku (Laravel)
  1.5  Verifikasi Signature Callback Pembayaran (Laravel)
```

> Tiap subbab: tampilkan judul subbab + keterangan path file, tuliskan **1–2 kalimat konteks**, lalu blok kode, lalu **1 kalimat** penjelasan poin teknis penting. Lima cuplikan ini sengaja dipilih karena merepresentasikan logika bisnis paling inti & orisinal Medcom: alur transaksi end-to-end mulai dari registrasi, perhitungan ongkir, pembuatan pesanan secara atomik, pembayaran, hingga verifikasi keamanan callback.

---

## Snippet Kode yang Harus Dimasukkan

### 1.1 — Registrasi Akun & Hashing Password (Laravel)

**File:** `app/Http/Controllers/Auth/RegisteredUserController.php`
**Konteks:** Menangani pendaftaran akun pengguna baru. Password di-hash dengan `Hash::make()` (bcrypt) sebelum disimpan, dan foto profil default diberikan otomatis.

```php
public function store(Request $request): RedirectResponse
{
    $request->validate([
        'name'     => ['required', 'string', 'max:255'],
        'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
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
```

> Poin teknis: penggunaan `Hash::make()` memastikan password tidak pernah disimpan dalam bentuk teks asli.

---

### 1.2 — Perhitungan Ongkos Kirim Dinamis (Laravel)

**File:** `app/Http/Controllers/ShippingController.php`
**Konteks:** Menghitung ongkir via KlikResi API. Origin diambil dari `kode_wilayah` toko produk (bukan hardcoded), berat total dihitung dari berat aktual produk × quantity, dengan aturan pembulatan toleransi +300 gram.

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

    // Pembulatan: toleransi +300g (contoh: 1.31kg -> 2kg, 1.30kg -> 1kg)
    $weightKg    = $totalWeightGrams / 1000;
    $intPart     = floor($weightKg);
    $finalWeight = (($weightKg - $intPart) > 0.3) ? ($intPart + 1) : max(1, $intPart);

    // Panggil KlikResi API
    $response = Http::withHeaders(['x-api-key' => $this->apiKey])
        ->post('https://klikresi.com/api/rates', [
            'origin_id'      => $dynamicOriginId,
            'destination_id' => $request->destination_id,
            'weight'         => $finalWeight,
        ]);

    return response()->json(['success' => true, 'weight' => $finalWeight, 'data' => $response->json()]);
}
```

> Poin teknis: origin pengiriman bersifat dinamis per-toko dan berat dibulatkan dengan aturan bisnis khusus (+0,3 kg).

---

### 1.3 — Pembuatan Pesanan dengan Database Transaction (Node.js)

**File:** `node-api/src/services/checkout.service.js`
**Konteks:** Fungsi `createOrder` membuat pesanan secara **atomik**: validasi stok, pembuatan order & item, pengurangan stok, dan pengosongan keranjang dilakukan dalam satu transaksi. Bila ada satu langkah gagal, seluruh proses di-rollback.

```javascript
exports.createOrder = async (userId, alamatId, items,
    courierCode = null, courierName = null, serviceName = null, shippingCost = 0) => {
  const connection = await db.getConnection();
  try {
    await connection.query("SET time_zone = '+07:00'");
    await connection.beginTransaction();

    let totalHarga = 0;
    const orderItemsData = [];

    // Validasi stok & hitung total
    for (const item of items) {
      const [rows] = await connection.query(
        "SELECT id, nama, harga, stok FROM products WHERE id = ?", [item.product_id]);
      if (rows.length === 0) throw new Error(`Produk ID ${item.product_id} tidak ditemukan`);

      const product = rows[0];
      if (product.stok < item.jumlah)
        throw new Error(`Stok tidak mencukupi untuk ${product.nama}. Tersedia: ${product.stok}`);

      const subtotal = product.harga * item.jumlah;
      totalHarga += subtotal;
      orderItemsData.push({ product_id: product.id, nama_produk: product.nama,
        harga: product.harga, qty: item.jumlah, subtotal });
    }

    const grandTotal = totalHarga + shippingCost;
    const now = getWIBTimestamp();

    // Insert order
    const [orderResult] = await connection.query(
      `INSERT INTO orders (user_id, alamat_id, total_harga, courier_code, courier_name,
         service_name, shipping_cost, status, created_at, updated_at)
       VALUES (?, ?, ?, ?, ?, ?, ?, 'pending', ?, ?)`,
      [userId, alamatId, grandTotal, courierCode, courierName, serviceName, shippingCost, now, now]);
    const orderId = orderResult.insertId;

    // Insert item & kurangi stok
    for (const item of orderItemsData) {
      await connection.query(
        `INSERT INTO order_items (order_id, product_id, nama_produk, harga, qty, subtotal)
         VALUES (?, ?, ?, ?, ?, ?)`,
        [orderId, item.product_id, item.nama_produk, item.harga, item.qty, item.subtotal]);
      await connection.query("UPDATE products SET stok = stok - ? WHERE id = ?",
        [item.qty, item.product_id]);
    }

    // Kosongkan keranjang user
    const [cartRows] = await connection.query("SELECT id FROM keranjangs WHERE user_id = ?", [userId]);
    if (cartRows.length > 0)
      await connection.query("DELETE FROM barang_keranjangs WHERE keranjang_id = ?", [cartRows[0].id]);

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

> Poin teknis: pemakaian `beginTransaction` / `commit` / `rollback` menjamin konsistensi data (stok dan pesanan tidak akan tercatat sebagian).

---

### 1.4 — Integrasi Payment Gateway Duitku (Laravel)

**File:** `app/Http/Controllers/CheckoutController.php`
**Konteks:** Fungsi `pay()` membuat **signature SHA-256**, memanggil API Duitku untuk memperoleh `paymentUrl`, lalu menyinkronkan status pembayaran ke Node.js API.

```php
public function pay(Request $request)
{
    $request->validate(['order_id' => 'required|integer', 'total' => 'required|integer|min:1']);

    $user         = Auth::user();
    $merchantCode = config('services.duitku.merchant_code');
    $apiKey       = config('services.duitku.api_key');
    $mode         = config('services.duitku.mode');

    $merchantOrderId = 'ORDER-' . $request->order_id . '-' . time();
    $timestamp       = round(microtime(true) * 1000);

    // Signature: SHA256(merchantCode + timestamp + apiKey)
    $signature = hash('sha256', $merchantCode . $timestamp . $apiKey);

    $duitkuUrl = $mode === 'production'
        ? 'https://api.duitku.com/api/merchant/createInvoice'
        : 'https://api-sandbox.duitku.com/api/merchant/createInvoice';

    $response = Http::withHeaders([
        'x-duitku-signature'    => $signature,
        'x-duitku-timestamp'    => $timestamp,
        'x-duitku-merchantcode' => $merchantCode,
    ])->timeout(30)->post($duitkuUrl, [
        'merchantCode'    => $merchantCode,
        'paymentAmount'   => (int) $request->total,
        'merchantOrderId' => $merchantOrderId,
        'productDetails'  => 'Pembayaran Order #' . $request->order_id,
        'email'           => $user->email,
        'callbackUrl'     => route('duitku.callback'),
        'returnUrl'       => route('riwayat.pesanan'),
        'expiryPeriod'    => 1440,
    ]);

    $duitkuData = $response->json();
    if (($duitkuData['statusCode'] ?? '') !== '00')
        throw new \Exception('Duitku: ' . ($duitkuData['statusMessage'] ?? 'Unknown Error'));

    // Sinkronisasi status ke Node.js API
    Http::timeout(10)->put("{$this->nodeApiUrl}/orders/{$request->order_id}/status", [
        'status'            => 'pending',
        'payment_url'       => $duitkuData['paymentUrl'],
        'payment_reference' => $duitkuData['reference'],
    ]);

    return response()->json(['success' => true,
        'payment_url' => $duitkuData['paymentUrl'], 'reference' => $duitkuData['reference']]);
}
```

> Poin teknis: setiap transaksi diberi `merchantOrderId` unik (mengandung timestamp) dan signature SHA-256 sebagai pengaman permintaan ke payment gateway.

---

### 1.5 — Verifikasi Signature Callback Pembayaran (Laravel)

**File:** `app/Http/Controllers/DuitkuCallbackController.php`
**Konteks:** Endpoint callback yang dipanggil server Duitku. Keasliannya diverifikasi dengan menghitung ulang **signature MD5**; bila cocok dan pembayaran sukses (`resultCode = 00`), status pesanan diperbarui menjadi `paid`.

```php
public function handle(Request $request)
{
    $apiKey          = config('services.duitku.api_key');
    $merchantCode    = $request->merchantCode;
    $amount          = $request->amount;
    $merchantOrderId = $request->merchantOrderId;
    $signature       = $request->signature;
    $resultCode      = $request->resultCode; // 00 = success

    // Verifikasi signature: md5(merchantCode + amount + merchantOrderId + apiKey)
    $calcSignature = md5($merchantCode . $amount . $merchantOrderId . $apiKey);
    if ($signature !== $calcSignature)
        return response()->json(['message' => 'Invalid Signature'], 400);

    if ($resultCode == '00') {
        // Ekstrak Order ID dari format ORDER-123-xxxx
        $orderId = str_replace('ORDER-', '', explode('-', $merchantOrderId)[1]);

        $response = Http::timeout(10)->put(
            "{$this->nodeApiUrl}/orders/{$orderId}/status", ['status' => 'paid']);

        return $response->successful() ? response('OK', 200) : response('Internal Server Error', 500);
    }

    return response('OK', 200);
}
```

> Poin teknis: verifikasi signature MD5 menolak notifikasi palsu — status pesanan hanya menjadi `paid` jika request benar-benar berasal dari Duitku.

---

## Catatan Gaya Penulisan Dokumen

- **Penomoran wajib mulai dari BAB 1** (subbab 1.1–1.5). Jangan mulai dari BAB 4 atau BAB X.
- **Target panjang: maksimal 3 halaman A4.** Jaga agar tidak melebihi.
- Setiap subbab diawali **1–2 kalimat konteks** dan ditutup **1 kalimat** poin teknis.
- Blok kode pakai font monospace (Courier New 10pt); teks biasa Times New Roman 12pt; A4, margin 3-3-3-3 cm, spasi 1.5.
- Cantumkan path file di atas tiap blok kode sebagai caption (teks tebal/miring).
- Gunakan nama produk **Medcom** di seluruh narasi (jangan gunakan nama lama "Sparehub").
